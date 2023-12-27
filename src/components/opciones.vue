<template>
  <div>
    <v-row align="center" justify="center">
      <v-col cols="12" xs="12" sm="12" md="12" lg="12" xl="12" class="my-3 pa-2 txtsec title text-center">
        Opciones Asignadas
      </v-col>
      <v-col v-for="n in registros" :key="n.idopcion" cols="12" xs="5" sm="4" md="3" lg="2" xl="2" class="elevation-6 pa-0 ma-8">
        <v-card class="blue-grey lighten-5">
          <v-card-title>
            <v-spacer></v-spacer>
            <span class="body-1 txtsec font-weight-bold">{{n.nombre}}</span>
            <v-spacer></v-spacer>
          </v-card-title>
          <v-card-text class="text-center">
            <v-avatar size="100">
              <v-img :src="rutaimg+n.icono"></v-img>
            </v-avatar>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn small color="terciario" @click="cambia(n.ruta)"><span class="white--text">ingresar</span></v-btn>
            <v-spacer></v-spacer>
          </v-card-actions>
        </v-card>
      </v-col>

    </v-row>
  </div>
</template>
<script>
import axios from 'axios';
import iziToast from 'izitoast';

export default {
  data: () => ({
    parametro: null,
    registros:[],
    registro:[],
    rutaimg:process.env.RUTA +'/assets/img/',
  }),
  mounted () {
    this.parametro = 'go';
  },
  watch:{
    parametro () {
      this.lstregistros();
    },
  },
  methods : {
    cambia(ruta){
      this.drawer = !this.drawer;
      this.$router.push( '/menu/1/'+ruta).catch(err => {});
      window.scrollTo(0,0);
    },
    lstregistros(){
      axios.post(process.env.RUTA +'/assets/backend/data.php', {
        accion: 1,
      })
        .then((response) => {
          this.registros = response.data;
        })
        .catch(err => {
          console.log(err)
        })
    },
  }
}
</script>
