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
                        label="Buscar Tarea"
                        prepend-inner-icon="search"
                        solo-inverted
                        v-model="search"
                     ></v-text-field>
                  </v-flex>
                  <v-layout v-if="!ocualtarTab" xs12 justify-center align-center wrap>
                     <v-card-title class="title">Agrege el lapso de realización de la tarea.</v-card-title>
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
                     <selecttaskcomponent :search="search" v-on:speak="SelectTareaMethod($event)"></selecttaskcomponent>
                  </v-tab-item>
                  <v-tab-item :key="2">
                     <taskschedulecomponent
                        :data="data"
                        v-on:speak="SalvarMethod($event)"
                        :deshabilitar="deshabilitar"
                     ></taskschedulecomponent>
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
         data: null,
         noSelected: true,
         mensaje: "",
         tarea: null,
         tareahistorial: null,
         deshabilitar: false,
         accions: [
            {
               id: 1,
               text: "Tareas"
            },
            {
               id: 2,
               text: "Lapso"
            }
         ]
      };
   },
   methods: {
      SelectTareaMethod: function(msg) {
         this.noSelected = msg.noSelected;
         if (!this.noSelected) this.tarea = msg.task;
         else this.tarea = null;
      },
      SalvarMethod: function(msg) {
         let self = this;
         this.deshabilitar = true;
         if (msg.Accion === "save") {
            let History = msg.Data;
            History.task_id = this.tarea.id;
            this.tarea.history = History;
            axios.post("addtaskforstudents", this.tarea).then(function(res) {
               self.mensaje = res.data;
               self.deshabilitar = false;
               if (res.data.type === "success") self.tabs = 0;
            });
         } else {
            axios
               .post("removetaskforstudents/" + this.tarea.id)
               .then(function(res) {
                  self.mensaje = res.data;
                  self.deshabilitar = false;
                  if (res.data.type === "success") {
                     self.tabs = 0;
                     self.data = null;
                  }
               });
         }
      }
   },
   computed: {
      ocualtarTab: function() {
         return this.tabs == 0;
      }
   },
   watch: {
      tabs(val) {
         let self = this;
         if (val == 1) {
            axios.get("/taskforcourse/" + this.tarea.id).then(function(res) {
               if (
                  res.data != null &&
                  Object.keys(res.data).length === 0 &&
                  res.data.constructor === Object
               ) {
                  res.data = null;
               }
               self.data = res.data;
            });
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