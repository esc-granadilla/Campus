<template>
   <v-container grid-list-md class="cont">
      <showlessons :dialogOpen="Opendialog" :course="curso" v-on:speak="Opendialog = $event"></showlessons>
      <v-form id="nativeForm" method="post" action="/screenstudent" v-show="false">
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
      <v-flex class="footer" height="auto">
         <v-card-text style="height: 100px;">
            <v-btn absolute dark fab top right color="indigo darken-4" @click="shownews">
               {{actual}}
               <v-icon class="unido">{{notificacion}}</v-icon>
            </v-btn>
         </v-card-text>
      </v-flex>
   </v-container>
</template>

<script>
export default {
   props: ["estudiante_id"],
   data: () => ({
      rowsPerPageItems: [4, 8],
      pagination: {
         rowsPerPage: 4
      },
      total: "",
      actual: "",
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
      shownews() {
         if (this.total != "0") this.$router.push("/enoticias");
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
      axios.get("/getcoursesstudent/" + this.estudiante_id).then(function(res) {
         self.cursos = res.data;
      });
      axios.get("/statusnotifications/").then(function(res) {
         self.total = res.data.totales;
         self.actual = res.data.actuales == "0" ? "" : res.data.actuales;
      });
   },
   computed: {
      notificacion() {
         if (this.total == 0) return "notifications_none";
         if (this.actual != "") return "notifications_active";
         return "notifications";
      }
   }
};
</script>

<style scoped>
.cont {
   height: -webkit-fill-available;
}
.footer {
   bottom: 4px;
   right: 68px;
   position: absolute;
}
.unido {
   padding: 0px;
   margin: 0px;
   width: auto;
}
.img {
   background-image: url("../../../img/textura.png");
   height: 100%;
   background-repeat: repeat;
   padding: 0;
}
</style>
