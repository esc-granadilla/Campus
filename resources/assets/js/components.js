window.Vue = require('vue');
Vue.component('login', require('./pages/Login.vue').default);
Vue.component('App', require('./pages/App.vue').default);
Vue.component('Appstudents', require('./pages/AppStudents.vue').default);
Vue.component('Homeview', require('./pages/Home.vue').default);
Vue.component('registerstudents', require('./pages/RegisterStudents.vue').default);
Vue.component('registerprofesor', require('./pages/RegisterProfesor.vue').default);
Vue.component('remembercomponent', require('./components/Login/RememberComponent.vue').default);
Vue.component('administrador', require('./pages/Administrador.vue').default);
Vue.component('profesor', require('./pages/Profesor.vue').default);
Vue.component('estudiante', require('./pages/Estudiante.vue').default);
Vue.component('panelprofesor', require('./pages/PanelProfesor.vue').default);
Vue.component('panelestudiante', require('./pages/PanelEstudiante.vue').default);
Vue.component('selectsectioncomponent', require('./components/Partials/SelectSectionComponent.vue').default);
Vue.component('selectstudentscomponent', require('./components/Partials/SelectStudentsComponent.vue').default);
Vue.component('selectcoursecomponent', require('./components/Partials/SelectCourseComponent.vue').default);
Vue.component('selectlessonscomponent', require('./components/Partials/SelectLessonsComponent.vue').default);
Vue.component('createtaskcomponent', require('./components/Partials/Task/CreateTaskComponent.vue').default);
Vue.component('edittaskcomponent', require('./components/Partials/Task/EditTaskComponent.vue').default);
Vue.component('tasksteppercomponent', require('./components/Partials/Task/TaskStepperComponent.vue').default);
Vue.component('taskcomponent', require('./components/Partials/Task/TaskComponent.vue').default);
Vue.component('selecttaskcomponent', require('./components/Partials/Task/SelectTaskComponent.vue').default);
Vue.component('taskschedulecomponent', require('./components/Partials/Task/TaskScheduleComponent.vue').default);
Vue.component('selectnewscomponent', require('./components/Partials/News/SelectNewsComponent.vue').default);


/* Vue.component('ncrearcomponent', require('./components/Noticias/DialogCreate.vue').default);
Vue.component('neditarcomponent', require('./components/Noticias/DialogEdit.vue').default);
Vue.component('neliminarcomponent', require('./components/Noticias/DialogDelete.vue').default); */