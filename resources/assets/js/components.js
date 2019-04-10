Vue.component('login', require('./views/Login.vue').default);
Vue.component('App', require('./views/App.vue').default);
Vue.component('Homeview', require('./views/Home.vue').default);
Vue.component('registerstudents', require('./views/RegisterStudents.vue').default);
Vue.component('registerprofesor', require('./views/RegisterProfesor.vue').default);
Vue.component('remembercomponent', require('./components/Login/RememberComponent.vue').default);
Vue.component('administrador', require('./views/Administrador.vue').default);
Vue.component('pmostrarcomponent', require('./components/Profesores/MostrarComponent.vue').default);
Vue.component('peditarcomponent', require('./components/Profesores/EditarComponent.vue').default);
Vue.component('pborrarcomponent', require('./components/Profesores/BorrarComponent.vue').default);
Vue.component('emostrarcomponent', require('./components/Estudiantes/MostrarComponent.vue').default);
Vue.component('eeditarcomponent', require('./components/Estudiantes/EditarComponent.vue').default);
Vue.component('eborrarcomponent', require('./components/Estudiantes/BorrarComponent.vue').default);
Vue.component('horariocomponent', require('./components/Horario/HorarioComponent.vue').default);
Vue.component('cursocomponent', require('./components/Curso/CursoComponent.vue').default);
Vue.component('credencial', require('./components/Asignaciones/Credencial.vue').default);
Vue.component('acursohorario', require('./components/Asignaciones/AsignacionCursoHorario.vue').default);
Vue.component('acursoprofesor', require('./components/Asignaciones/AsignacionCursoProfesor.vue').default);
Vue.component('acursoalumno', require('./components/Asignaciones/AsignacionCursoAlumno.vue').default);
Vue.component('nmostrarcomponent',require('./components/Noticias/index.vue').default);
Vue.component('ncrearcomponent',require('./components/Noticias/DialogCreate.vue').default);
Vue.component('neditarcomponent',require('./components/Noticias/DialogEdit.vue').default);
Vue.component('neliminarcomponent',require('./components/Noticias/DialogDelete.vue').default);