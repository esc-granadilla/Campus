<template>
   <div class="footer">
      <div class="contenedor">
         <h2 class="titulo-footer">Contactanos</h2>
         <h3 class="subtitulo-footer">Lo apreciariamos mucho</h3>
         <div class="footers">
            <form action="/email" method="GET">
               <input type="text" name="name" placeholder="Nombre" required />
               <input type="email" name="email" placeholder="Email" required />
               <textarea
                  name="mensaje"
                  cols="30"
                  rows="10"
                  placeholder="Ingrese su mensaje..."
                  required
               ></textarea>
               <input type="submit" value="Enviar" />
            </form>
         </div>
      </div>
   </div>
</template>

<script>
export default {
   name: "footercomponent"
};
</script>

<style scoped lang="css">
.contenedor {
   width: 90%;
   max-width: 1000px;
   margin: auto;
   overflow: hidden;
   padding: 52px 0;
}

.footer {
   background: #2c3e50;
   height: auto;
}

.titulo-footer {
   color: #fff;
   text-align: center;
   font-weight: 400;
   font-size: 56px;
}

.subtitulo-footer {
   color: #fff;
   text-align: center;
   font-weight: 400;
   font-size: 56px;
   font-size: 16px;
   margin-bottom: 64px;
}

form {
   display: flex;
   width: 100%;
   justify-content: space-between;
   flex-wrap: wrap;
   margin: auto;
}

input[type="text"],
input[type="email"] {
   display: inline-block;
   width: 48%;
   padding: 13px;
   border: none;
   color: #fff;
   font-family: "open sans";
   background: rgba(37, 37, 33, 0.7);
   margin-bottom: 16px;
   border-top: 5px solid rgba(37, 37, 33, 0.7);
}

input[type="text"]:focus,
input[type="email"]:focus {
   border-top: 5px solid rgba(255, 255, 255, 0.7);
}

textarea {
   display: block;
   width: 100%;
   max-width: 100%;
   min-width: 100%;
   max-height: 200px;
   min-height: 200px;
   background: rgba(37, 37, 33, 0.7);
   padding: 13px;
   border: none;
   color: #fff;
   font-family: "open sans";
   margin-bottom: 16px;
}

textarea:focus {
   border-top: 5px solid rgba(255, 255, 255, 0.7);
}

input[type="submit"] {
   display: inline-block;
   padding: 13px;
   border: none;
   color: #fff;
   background: rgba(37, 37, 33, 0.7);
   width: 96px;
}
</style>

