<template>
   <v-layout justify-center wrap>
      <v-card class="mar2" absolute>
         <v-layout justify-center wrap>
            <v-flex md10 class="mar">
               <v-toolbar color="green" dark tabs>
                  <v-flex v-if="ocualtarTab">
                     <v-text-field
                        class="mx-3"
                        flat
                        label="Buscar Curso"
                        prepend-inner-icon="search"
                        solo-inverted
                        v-model="search"
                     ></v-text-field>
                  </v-flex>
                  <v-layout v-if="!ocualtarTab" xs12 justify-center align-center wrap>
                     <v-card-title class="title">Seleccione las Lecciones del Curso</v-card-title>
                  </v-layout>
                  <template v-slot:extension>
                     <v-tabs
                        v-model="tabs"
                        centered
                        color="transparent"
                        slider-color="white"
                        change
                     >
                        <v-tab
                           v-for="a in accions"
                           :key="a.id"
                           :disabled="a.id==2 ? noSelected : false"
                        >{{ a.text }}</v-tab>
                     </v-tabs>
                  </template>
               </v-toolbar>

               <v-tabs-items v-model="tabs">
                  <v-tab-item :key="1">
                     <selectcoursecomponent
                        :search="search"
                        v-on:speak="SelectCourseMethod($event)"
                     ></selectcoursecomponent>
                  </v-tab-item>
                  <v-tab-item :key="2">
                     <selectlessonscomponent
                        :seleccionados="selected"
                        :lessonstock="lessonstock"
                        v-on:speak="SalvarMethod($event)"
                     ></selectlessonscomponent>
                  </v-tab-item>
               </v-tabs-items>
            </v-flex>
         </v-layout>
      </v-card>
   </v-layout>
</template>

<script>
export default {
   data() {
      return {
         tabs: 0,
         search: "",
         noSelected: true,
         selected: [],
         mensaje: [],
         course: null,
         sections: [],
         lessonstock: [],
         accions: [
            {
               id: 1,
               text: "Cursos"
            },
            {
               id: 2,
               text: "Lecciones"
            }
         ]
      };
   },
   methods: {
      SelectCourseMethod: function(msg) {
         this.noSelected = msg.noSelected;
         if (!this.noSelected) this.course = msg.course;
         else this.course = null;
      },
      SalvarMethod: function(msg) {
         let self = this;
         axios
            .post("addlessonsforcourse/" + this.course.id, {
               lessons: msg.Lessons
            })
            .then(function(res) {
               self.mensaje = res.data;
               axios
                  .get("/lessonsforcourses/" + self.course.id)
                  .then(res => (self.selected = res.data));
               axios
                  .get("/lessonsstock/" + self.course.id)
                  .then(res => (self.lessonstock = res.data));
            });
         //.then(res => (this.mensaje = res.data));
      }
   },
   computed: {
      ocualtarTab: function() {
         return this.tabs == 0;
      }
   },
   watch: {
      tabs(val) {
         if (val == 1) {
            this.selected = [];
            axios
               .get("/lessonsforcourses/" + this.course.id)
               .then(res => (this.selected = res.data));
            axios
               .get("/lessonsstock/" + this.course.id)
               .then(res => (this.lessonstock = res.data));
         }
      },
      mensaje(val) {
         if (val.type === "success")
            this.$toast.success(val.message, {
               y: "top",
               timeout: 6000
            });
         else
            this.$toast.error(val.message, {
               y: "top",
               timeout: 6000
            });
      }
   },
   mounted() {}
};
</script>

<style scoped>
.mar {
   margin-top: -40px;
}
.mar2 {
   margin-top: 70px;
   width: 600px;
}
</style>