<template>
   <v-container grid-list-md>
      <v-layout justify-end>
         <createtaskcomponent></createtaskcomponent>
         <edittaskdialog></edittaskdialog>
         <showlessons :dialogOpen="Opendialog" :course="curso" v-on:speak="Opendialog = $event"></showlessons>
      </v-layout>
      <v-form id="nativeForm" method="post" action="/screenteacher">
         <v-text-field id="ids" required hidden name="id"></v-text-field>
      </v-form>
      <v-data-iterator
         :items="cursos"
         :rows-per-page-items="rowsPerPageItems"
         :pagination.sync="pagination"
         rowsPerPageText="Elementos por página:"
         rowsPerPageAll="Todos"
         pageText="{0}-{1} de {2}"
         noResultsText="Ningún resultado a mostrar"
         nextPage="Página siguiente"
         prevPage="Página anterior"
         noDataText="Ningún dato disponible"
         content-tag="v-layout"
         row
         wrap
      >
         <template v-slot:item="props">
            <v-flex xs12 sm6 md4 lg3>
               <v-card>
                  <div :style="estilo()">
                     <div class="img">
                        <v-card-title text-sm-center>
                           <h4 style="color: white;" class="headline">{{ props.item.curso.nombre }}</h4>
                        </v-card-title>
                     </div>
                  </div>
                  <v-divider></v-divider>
                  <v-list dense>
                     <v-list-tile>
                        <v-list-tile-content>Sección:</v-list-tile-content>
                        <v-list-tile-content class="align-end">{{ props.item.seccion.seccion }}</v-list-tile-content>
                     </v-list-tile>
                     <v-list-tile>
                        <v-list-tile-content class="align-center">
                           <v-btn
                              flat
                              dark
                              color="success"
                              block
                              @click="curso = props.item.id, Opendialog = true"
                           >Ver Lecciones</v-btn>
                        </v-list-tile-content>
                        <v-list-tile-content class="align-center">
                           <v-btn
                              round
                              dark
                              color="primary"
                              block
                              @click="seleccionarCurso(props.item.id)"
                           >Seleccionar</v-btn>
                        </v-list-tile-content>
                     </v-list-tile>
                  </v-list>
               </v-card>
            </v-flex>
         </template>
      </v-data-iterator>
   </v-container>
</template>

<script>
import edittaskdialog from "../components/Modals/Task/TaskDialog.vue";
export default {
   props: ["profesor_id"],
   components: {
      edittaskdialog
   },
   data: () => ({
      rowsPerPageItems: [4, 8, 12],
      pagination: {
         rowsPerPage: 4
      },
      cursos: [],
      selectedCurso: {
         id: 0,
         course_id: 0,
         section_id: 0
      },
      curso: 0,
      Opendialog: false
   }),
   methods: {
      seleccionarCurso(id) {
         document.getElementById("ids").value = "" + id;
         nativeForm.submit();
      },
      randomColor() {
         let rc = "#";
         for (let i = 0; i < 6; i++) {
            rc += Math.floor(Math.random() * 9).toString(16);
         }
         return rc;
      },
      estilo() {
         let color1 = this.randomColor();
         let color2 = this.randomColor();
         let salida = `background: #fc466b;
                  background: -webkit-linear-gradient(
                     to right,
                     ${color1},
                     ${color2}
                  );
                  background: linear-gradient(to right, ${color1}, ${color2});
                  height: 120px;`;
         return salida;
      }
   },
   mounted() {
      var self = this;
      axios.get("/getcoursesteacher/" + this.profesor_id).then(function(res) {
         self.cursos = res.data;
      });
   }
};
</script>
<style scoped>
.img {
   background-image: url("../../img/textura.png");
   height: 100%;
   background-repeat: repeat;
   padding: 0;
}
</style>
