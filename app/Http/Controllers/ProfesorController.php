<?php

namespace Campus\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Campus\Course;
use Campus\Teacher;
use Campus\Student;
use Maatwebsite\Excel\Facades\Excel;
use Campus\Section;
use Campus\Task;
use Campus\Taskhistory;
use Campus\Qualification;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Session;
use Campus\Lesson;

class ProfesorController extends Controller
{
   /*
    |--------------------------------------------------------------------------
    | Profesor Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */
   public function __construct()
   {
      $this->middleware('auth');
      $this->middleware('profesor', ['except' => ['store', 'mystudentsexport', 'studentsexport']]);
   }

   public function index()
   {
      return view('panelprofesores');
   }

   public function screenteacher(Request $request)
   {
      $request->session()->put('course', $request->input('id'));
      return view('panelprofesores');
   }


   public function getcoursesteacher(Teacher $teacher, Request $request)
   {
      if ($request->ajax()) {
         $asignaciones = $teacher->courses()->get();
         $cursos = [];
         foreach ($asignaciones as $asig) {
            if (Lesson::where('course_id', $asig->id)->count() > 0)
               array_push($cursos, [
                  'id' => $asig->id,
                  'nombre' => $asig->nombre,
                  'curso' => $asig->subject()->first(),
                  'seccion' => $asig->section()->first()
               ]);
         }
         return response()->json($cursos, 200);
      }
   }

   public function studentsforcourse(Request $request)
   {
      if ($request->ajax()) {
         $course_id = $request->session()->get('course');
         $course = Course::where('id', (int) $course_id)->first();
         $students = $course->section()->first()->students()->where('estado', 1)->get();
         return response()->json($students, 200);
      }
   }

   public function qualificationsforstudent(Student $student, Request $request)
   {
      if ($request->ajax()) {
         $qualifications = $student->qualifications()->where([
            'estado' => 1,
            'course_id' => (int) $request->session()->get('course')
         ])->get();
         return response()->json($qualifications, 200);
      }
   }

   private function matrizqualifications(int $id, Request $request)
   {
      $course = Course::where('id', (int) $request->session()->get('course'))->first();
      $section = $course->section()->first();
      $students = $section->students()->where('estado', 1)->get();
      $calificaciones = [];
      foreach ($students as $student) {
         $examenes = $student->qualifications()->where(['trimestre' => $id, 'tipo' => 'Examen', 'course_id' => $course->id])->get();
         $tareas = $student->qualifications()->where(['trimestre' => $id, 'tipo' => 'Tarea', 'course_id' => $course->id])->get();
         $trabajos = $student->qualifications()->where(['trimestre' => $id, 'tipo' => 'Trabajo o investigación', 'course_id' => $course->id])->get();
         $otros = $student->qualifications()->where(['trimestre' => $id, 'tipo' => 'Otra', 'course_id' => $course->id])->get();
         $calificacion = ['estudiante' => $student, 'examenes' => $examenes, 'tareas' => $tareas, 'trabajos' => $trabajos, 'otros' => $otros];
         array_push($calificaciones, $calificacion);
      }
      return $calificaciones;
   }

   public function qualificationsfortrimester($id, Request $request)
   {
      if ($request->ajax()) {
         $qualifications = $this->matrizqualifications($id, $request);
         return response()->json($qualifications, 200);
      }
   }

   private function gettitles($array)
   {
      if (count($array) > 0) {
         $collection = collect($array);
         $titulos = $collection->unique()->sort();
         return $titulos->toArray();
      } else
         return $array;
   }

   private function Cabeceras($calificaciones)
   {
      $cabeceras = ['Cedula', 'Nombre'];
      $examenes = [];
      $tareas = [];
      $trabajos = [];
      $otros = [];
      $final = 0;
      if (count($calificaciones) > 0) {
         foreach ($calificaciones as $calificacion) {
            foreach ($calificacion['examenes'] as $array) {
               array_push($examenes, $array->titulo);
            }
            foreach ($calificacion['tareas'] as $array) {
               array_push($tareas, $array->titulo);
            }
            foreach ($calificacion['trabajos'] as $array) {
               array_push($trabajos, $array->titulo);
            }
            foreach ($calificacion['otros'] as $array) {
               array_push($otros, $array->titulo);
            }
         }
         $examenes = $this->gettitles($examenes);
         $tareas = $this->gettitles($tareas);
         $trabajos = $this->gettitles($trabajos);
         $otros = $this->gettitles($otros);
         $calificacion = $calificaciones[0];
         $arrays = [$examenes, $tareas, $trabajos, $otros];
         $final = 2;
         foreach ($arrays as $array) {
            $final += count($array);
            foreach ($array as $cali) {
               array_push($cabeceras, $cali);
            }
         }
         array_push($cabeceras, 'Nota');
      }
      return [$final, $cabeceras, [$examenes, $tareas, $trabajos, $otros]];
   }

   private function Cuerpo($calificaciones, $titulos)
   {
      $rows = [];
      foreach ($calificaciones as $calificacion) {
         $row = [
            $calificacion['estudiante']->cedula,
            $calificacion['estudiante']->nombre . ' ' .
               $calificacion['estudiante']->primer_apellido . ' ' .
               $calificacion['estudiante']->segundo_apellido
         ];
         $arrays = [$calificacion['examenes'], $calificacion['tareas'], $calificacion['trabajos'], $calificacion['otros']];
         $nota = 0;
         for ($i = 0; $i < count($titulos); $i++) {
            foreach ($titulos[$i] as $titulo) {
               $cali = $arrays[$i]->where('titulo', $titulo)->first();
               if ($cali != null) {
                  array_push($row, $cali->porcentaje_obtenido);
                  $nota += $cali->porcentaje_obtenido;
               } else {
                  array_push($row, 0);
               }
            }
         }
         array_push($row, $nota);
         array_push($rows, $row);
      }
      return $rows;
   }

   private function CuerpoLlenado($calificaciones, $text, &$array, &$nota)
   {
      if ($calificaciones->count() > 0) {
         $total = 0;
         foreach ($calificaciones as $cali) {
            $total += $cali->porcentaje_obtenido;
         }
         $nota += $total;
         $row = [$text, "", "", $total];
         array_push($array, $row);
         $row = [];
         foreach ($calificaciones as $cali) {
            $row = [$cali->titulo, $cali->valor_porcentual, $cali->porcentaje_obtenido, ""];
            array_push($array, $row);
         }
      }
   }

   private function CuerpoEstudiante($calificaciones)
   {
      $rows = [];
      if (
         $calificaciones['examenes']->count() > 0 ||
         $calificaciones['tareas']->count() > 0 ||
         $calificaciones['trabajos']->count() > 0 ||
         $calificaciones['otros']
      ) {
         $nota = 0;
         $this->CuerpoLlenado($calificaciones['examenes'], "Exámenes", $rows, $nota);
         $this->CuerpoLlenado($calificaciones['tareas'], "Tareas", $rows, $nota);
         $this->CuerpoLlenado($calificaciones['trabajos'], "Trabajos", $rows, $nota);
         $this->CuerpoLlenado($calificaciones['otros'], "Otros", $rows, $nota);
         array_push($rows, ["", "", "Nota", $nota]);
      }
      return $rows;
   }

   public function qualificationsexport($id, Request $request)
   {
      try {
         $course = $request->session()->get('course');
         $course = Course::where('id', $course)->first();
         $profesor = $course->teacher()->first();
         $section = $course->section()->first();
         $course = $course->subject()->first();
         $calificaciones = $this->matrizqualifications($id, $request);
         $Heads = $this->Cabeceras($calificaciones);
         $titulos = $Heads[2];
         $cabeceras = $Heads[1];
         $final = $Heads[0];
         $rows = $this->Cuerpo($calificaciones, $titulos);
         Excel::create('Notas trimestrales', function ($excel) use ($profesor, $course, $id, $section, $cabeceras, $final, $rows) {
            $excel->sheet('Notas Estudiantes', function ($sheet) use ($profesor, $course, $id, $section, $cabeceras, $final, $rows) {
               if ($final > 0) {
                  $Listado = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
                  $sheet->mergeCells('A1:' . $Listado[$final] . '1');
                  $sheet->row(1, ['Lista de Notas del ' . $id . ' Trimestre.']);
                  $sheet->mergeCells('A2:' . $Listado[$final] . '2');
                  $sheet->row(2, ['Curso: ' . $course->nombre . '        Sección: ' . $section->seccion]);
                  $sheet->mergeCells('A3:' . $Listado[$final] . '3');
                  $sheet->row(3, ['Profesor: ' . $profesor->nombre . ' ' . $profesor->primer_apellido . ' ' . $profesor->segundo_apellido]);
                  $sheet->row(4, $cabeceras);
                  foreach ($rows as $row) {
                     $sheet->appendRow($row);
                  }
                  for ($i = 0; $i < 3; $i++) {
                     $sheet->cells('A' . ($i + 1) . ':' . $Listado[$final] . ($i + 1), function ($cells) {
                        $cells->setBackground('#022450');
                        $cells->setFontColor('#ffffff');
                        $cells->setFont(array(
                           'family'     => 'Calibri',
                           'size'       => '16',
                           'bold'       =>  true
                        ));
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                     });
                  }
                  $sheet->cells('A4:' . $Listado[$final] . '4', function ($cells) {
                     $cells->setBackground('#91bdee');
                     $cells->setFontColor('#000000');
                     $cells->setFont(array(
                        'family'     => 'Calibri',
                        'size'       => '14',
                        'bold'       =>  true
                     ));
                  });
                  $sheet->setHeight(array(
                     1     =>  25,
                     2     =>  25,
                     3     =>  25,
                     4     =>  25,
                  ));
                  $sheet->setAutoSize(true);
               }
            });
         })->export('xlsx');
      } catch (\Exception $e) {
         $output = new \Symfony\Component\Console\Output\ConsoleOutput();
         $output->writeln($e->getMessage());
      }
   }

   public function studentsexport($id, Student $student, Request $request)
   {
      try {
         $qualifications = $this->matrizqualifications($id, $request);
         $calificaciones = [];
         foreach ($qualifications as $qualification) {
            if ($qualification['estudiante']->id == $student->id) {
               $calificaciones = $qualification;
               break;
            }
         }
         $course = $request->session()->get('course');
         $course = Course::where('id', $course)->first();
         $profesor = $course->teacher()->first();
         $section = $course->section()->first();
         $course = $course->subject()->first();
         $rows = $this->CuerpoEstudiante($calificaciones);
         Excel::create('Nota trimestral', function ($excel) use ($profesor, $course, $id, $section, $student, $rows) {
            $excel->sheet('Nota Estudiante', function ($sheet) use ($profesor, $course, $id, $section, $student, $rows) {
               if (count($rows) > 0) {
                  $sheet->mergeCells('A1:D1');
                  $sheet->row(1, ['Lista de Notas del ' . $id . ' Trimestre.']);
                  $sheet->mergeCells('A2:D2');
                  $sheet->row(2, ['Curso: ' . $course->nombre . '        Sección: ' . $section->seccion]);
                  $sheet->mergeCells('A3:D3');
                  $sheet->row(3, ['Profesor: ' . $profesor->nombre . ' ' . $profesor->primer_apellido . ' ' . $profesor->segundo_apellido]);
                  $sheet->mergeCells('A4:D4');
                  $sheet->row(4, ['Alumno=     Cedula: ' . $student->cedula . '     Nombre: ' . $student->nombre . ' ' . $student->primer_apellido . ' ' . $student->segundo_apellido]);
                  $sheet->row(5, ['Rubro', 'Valor', 'Obtenido', 'Totales']);
                  foreach ($rows as $row) {
                     $sheet->appendRow($row);
                  }
                  $sheet->cells('A1:A4', function ($cells) {
                     $cells->setBackground('#022450');
                     $cells->setFontColor('#ffffff');
                     $cells->setFont(array(
                        'family'     => 'Calibri',
                        'size'       => '16',
                        'bold'       =>  true
                     ));
                     $cells->setAlignment('center');
                     $cells->setValignment('center');
                  });
                  $sheet->cells('A5:D5', function ($cells) {
                     $cells->setBackground('#91bdee');
                     $cells->setFontColor('#000000');
                     $cells->setFont(array(
                        'family'     => 'Calibri',
                        'size'       => '14',
                        'bold'       =>  true
                     ));
                  });
                  $sheet->setHeight(array(
                     1     =>  25,
                     2     =>  25,
                     3     =>  25,
                     4     =>  25,
                     5     =>  25,
                  ));
                  $sheet->setAutoSize(true);
                  $sheet->setWidth('A', 75);
               }
            });
         })->export('xlsx');
      } catch (\Exception $e) {
         $output = new \Symfony\Component\Console\Output\ConsoleOutput();
         $output->writeln($e->getMessage());
      }
   }

   public function questionsfortask(Task $task, Request $request)
   {
      if ($request->ajax()) {
         $questions = $task->questions()->get();
         return response()->json($questions, 200);
      }
   }

   public function addtaskforstudents(Request $request)
   {
      if ($request->ajax()) {
         $tarea = $request->all();
         $history = $request->all()['history'];
         $course = Course::all()->find((int) $request->session()->get('course'));
         $history['course_id'] = $course->id;
         $students = $course->section()->first()->students()->get();
         if ($students == null || $students->count() == 0) {
            return response()->json(['type' => 'error', 'message' => 'No alumnos en este curso imposible asignar tareas.'], 200);
         } else {
            $taskhistory = Taskhistory::where([
               'course_id' => $course->id,
               'task_id' => $history['task_id'],
               'student_id' => $students->first()->id,
            ])->first();
            if ($taskhistory != null) {
               return response()->json(['type' => 'error', 'message' => 'Ya se ha asignado esta tarea al curso.'], 200);
            } else {
               foreach ($students as $student) {
                  $history['student_id'] = $student->id;
                  $taskhistory = new Taskhistory($history);
                  $taskhistory->save();
                  $info = [
                     'titulo' => $history['nombre'], 'valor_porcentual' => (float) $tarea['valor'],
                     'porcentaje_obtenido' => (float) 0.0, 'tipo' => 'Tarea', 'condicion' => 'No realisada',
                     'descripcion' => $tarea['titulo'], 'trimestre' => $taskhistory->trimestre,
                     'fecha' => $taskhistory->inicio, 'student_id' => $student->id, 'course_id' => $course->id,
                  ];
                  $qualification = new Qualification($info);
                  $qualification->save();
               }
               return response()->json(['type' => 'success', 'message' => 'Se asigno la tarea correctamente.'], 200);
            }
         }
      }
   }

   public function removetaskforstudents(Request $request, Task $task)
   {
      if ($request->ajax()) {
         $taskhistories = $task->taskhistories()->where(
            'course_id',
            (int) $request->session()->get('course')
         )->get();
         foreach ($taskhistories as $taskhistory) {
            $student = $taskhistory->student()->first();
            $qualification = $student->qualifications()->where([
               'tipo' => 'Tarea',
               'course_id' => $taskhistory->course_id, 'trimestre' => $taskhistory->trimestre,
               'descripcion' => $task->titulo, 'valor_porcentual' => $task->valor
            ])->first();
            $taskhistory->delete();
            $qualification->delete();
         }
         return response()->json(['type' => 'success', 'message' => 'Se elimino la asignacion la tarea correctamente.'], 200);
      }
   }

   public function taskforcourse(Request $request, Task $task)
   {
      if ($request->ajax()) {
         $taskhistories = $task->taskhistories()->where(
            'course_id',
            (int) $request->session()->get('course')
         )->get();
         $taskhistory = null;
         if ($taskhistories != null && $taskhistories->count() > 0) {
            $taskhistory = $taskhistories->first();
         }
         return response()->json($taskhistory, 200);
      }
   }

   public function store(Request $request)
   {
      if ($request->ajax() && $request->hasFile('imagen')) {
         $file = $request->file('imagen');
         $name = $file->store('public/imagen');
         return response()->json(['name' => $name], 200);
      }
      return response()->json(['name' => 'error'], 200);
   }

   public function show(Request $request)
   {
      if ($request->session()->has('teacher')) {
         $teacher = $request->session()->get('teacher');
         $teacher = Teacher::where('cedula', $teacher[0]['cedula'])->first();
         return view('profileteacher', compact('teacher'));
      }
      return view('home');
   }

   public function mystudentsexport($id, Request $request)
   {
      try {
         $cedula = $request->session()->get('student')[0]->cedula;
         $student = Student::where('cedula', $cedula)->first();
         $this->studentsexport($id, $student, $request);
      } catch (\Exception $e) {
         $output = new \Symfony\Component\Console\Output\ConsoleOutput();
         $output->writeln($e->getMessage());
      }
   }
}
