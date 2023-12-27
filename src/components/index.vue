<template>
  <div class="fondo">
    <div class="centrado">
      <v-row align="center" justify="center" class="mt-0 pa-0">
        <v-col cols="12" xs="12" sm="12" md="12" lg="12" xl="12" class="txtprimario text-center">
          <v-avatar size="150" class="pa-1"><v-img :src='this.logo'></v-img></v-avatar>
        </v-col>
        <v-col cols="12" xs="12" sm="12" md="12" lg="12" xl="12" class="ma-0 text-center">
          <v-btn class="mx-2 transparent-btn" fab dark small color="primario" @click="ven_login=true"><v-icon color="white">lock</v-icon> </v-btn>
        </v-col>
        <v-col cols="12" xs="12" sm="12" md="12" lg="12" xl="12" class="txtprimario text-center">
          Bitacora Centro de Monitoreo V1.0  &copy; 2023
        </v-col>
      </v-row>
    </div>
    <v-dialog v-model="ven_login" persistent max-width="600px">
      <v-card class="primario pa-4 text-right">
        <v-col cols="12" class="text-right ma-0 pa-0 white--text"><v-btn class="mx-2" fab dark small color="primario" @click="ven_login=false"><v-icon color="white">close</v-icon> </v-btn></v-col>
        <v-form v-model="valid" ref="form" lazy-validation @keyup.native.enter="login()">
          <v-text-field dark v-model="usuario" prepend-icon="person" label="Usuario" type="text" :rules="minRules"></v-text-field>
          <v-text-field dark v-model="password" prepend-icon="lock" label="Contraseña" type="password" :rules="minRules" required></v-text-field>
          <v-btn small color="primario" @click.native="login()"><span class="white--text">Ingresar al Sistema</span> </v-btn>

        </v-form>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>

import axios from 'axios';
import iziToast from 'izitoast';

export default {
  name: 'index',
  data: () => ({
    valid: true,
    parametro: null,
    logo:process.env.RUTA+'/assets/img/muni.png',
    minRules: [
      v => !!v || 'Este campo es necesario',
      v => (v && v.length>= 2) || 'Utiliza al menos 3 caracteres',
    ],
    usuario:null,
    password:null,
    ven_login:false,
  }),
  mounted () {
    this.parametro = 'go';
  },
  watch:{
    parametro () {

    },
  },
  methods : {
    login(){
      if (this.$refs.form.validate()) {
        axios.post(process.env.RUTA +'/assets/backend/login.php', {
          usuario: this.usuario,
          password: this.password,
          accion: 1,
        })
          .then((response) => {
            if (response.data == 1) {
              iziToast.success({
                title: 'Ingresando:',
                message: 'credenciales correctas',
                position: 'topRight',
                timeout: 2000,
              });
              this.$refs.form.reset();
              this.$router.push('/menu/1/opciones');
            } else {
              iziToast.error({
                title: 'Alerta',
                message: 'Error: '+response.data,
                position: 'topRight',
                timeout: 3000,
              });
            }
          })
          .catch(err => {
            console.log(err)
          })
      }
    },
    cambia(destino){
      if(destino!=''){
        this.$router.push( '/'+destino);
        window.scrollTo(0,0);
      }
    },
  },
}
</script>
<style>
.fondo {
  background-image: url('http://localhost/externos/bitacora/bitacora/src/assets/img/fondo1.png'); /* Ajusta la ruta según tu estructura de carpetas */
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  height: 100vh; /* 100% de la altura de la ventana */
}
.centrado {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh; /* Ajusta la altura según sea necesario */
}
</style>
