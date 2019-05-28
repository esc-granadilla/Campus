window.Vue = require('vue');
Vue.component('login', require('./pages/Login.vue').default);
Vue.component('App', require('./pages/App.vue').default);
Vue.component('Homeview', require('./pages/Home.vue').default);
Vue.component('registerstudents', require('./pages/RegisterStudents.vue').default);
Vue.component('registerprofesor', require('./pages/RegisterProfesor.vue').default);
Vue.component('remembercomponent', require('./components/Login/RememberComponent.vue').default);
Vue.component('administrador', require('./pages/Administrador.vue').default);
Vue.component('profesor', require('./pages/Profesor.vue').default);
Vue.component('panelprofesor', require('./pages/PanelProfesor.vue').default);
Vue.component('selectsectioncomponent', require('./components/Partials/SelectSectionComponent.vue').default);
Vue.component('estudiantecomponent', require('./components/Estudiantes/EstudianteComponent.vue').default);
Vue.component('horariocomponent', require('./components/Horario/HorarioComponent.vue').default);
Vue.component('asignaturacomponent', require('./components/Asignatura/AsignaturaComponent.vue').default);
Vue.component('credencial', require('./components/Asignaciones/Credencial.vue').default);
Vue.component('acursohorario', require('./components/Asignaciones/AsignacionCursoHorario.vue').default);
Vue.component('acursoprofesor', require('./components/Asignaciones/AsignacionCursoProfesor.vue').default);
Vue.component('acursoalumno', require('./components/Asignaciones/AsignacionCursoAlumno.vue').default);
Vue.component('nmostrarcomponent', require('./components/Noticias/index.vue').default);
/* Vue.component('ncrearcomponent', require('./components/Noticias/DialogCreate.vue').default);
Vue.component('neditarcomponent', require('./components/Noticias/DialogEdit.vue').default);
Vue.component('neliminarcomponent', require('./components/Noticias/DialogDelete.vue').default); */