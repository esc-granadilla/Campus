<?php

namespace Campus\Http\Controllers;

use Illuminate\Http\Request;
use Campus\Http\Middleware\Profesor;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
   /**
    * Create a new controller instance.
    *
    * @return void
    */
   public function __construct()
   {
      $this->middleware('auth', ['except' => ['email']]);
   }

   /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Http\Response
    */
   public function index(Request $request)
   {
      try {
         if ($request->user()->hasRole('Usuario')) {
            $estudiante = $request->user()->student()->first();
            $request->session()->push('student', $estudiante);
            return view('home', compact('estudiante'));
         } elseif ($request->user()->hasAnyRole(['Profesor', 'Administrador'])) {
            $profesor = $request->user()->teacher()->first();
            $request->session()->push('teacher', $profesor);
            return view('homeprofesores', compact('profesor'));
         }
      } catch (\Throwable $th) {
         return view('welcome');
      }
   }

   public function email(Request $request)
   {
      try {
         $data = array(
            'mensaje' => $request->input('mensaje'),
            'email' => $request->input('email'),
            'name' => $request->input('name')
         );
         Mail::send(
            'emails.home',
            $data,
            function ($message) {
               $message->from('escgranadilla@gmail.com', 'Campus Granadilla Norte');

               $message->to('escgranadilla@gmail.com', 'Campus Granadilla Norte');

               $message->subject('Info Campus Virtual');

               $message->priority(3);
            }
         );
      } catch (\Throwable $th) { }
      return view('welcome');
   }
}
