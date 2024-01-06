<template>
  <div>
    <v-toolbar dense :src="this.logo">
      <v-btn class="mx-2 transparent-btn" fab dark small color="primario" @click="ven_clave=true" ><v-icon class="mr-2 mx-2" color="white">fa-lock</v-icon></v-btn>
      <v-btn class="mx-2 transparent-btn" fab dark small color="primario" @click="salir()" ><v-icon class="mr-2 mx-2" color="white">fa-door-open</v-icon></v-btn>
      <v-spacer></v-spacer>
      <v-btn class="mx-2 transparent-btn" fab dark small color="primario" @click="cambia('opciones')" ><v-icon class="mr-2 mx-2" color="white">fa-grip</v-icon></v-btn>
      <v-spacer></v-spacer>
      <div class="mx-2 white--text body-1 font-weight-bold">{{this.sesion.sistema}} - {{this.sesion.usuario}}</div>
    </v-toolbar>
    <v-dialog v-model="ven_clave" persistent max-width="600px">
      <v-card>
        <v-card-title>
          <span class="headline">Cambiar contraseña</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12" class="pa-0">
                <v-text-field v-model="pass1" label="Escriba su nueva contraseña" type="password" :rules="minRules" required>></v-text-field>
              </v-col>
              <v-col cols="12" class="pa-0">
                <v-text-field v-model="pass2" label="Repita su nueva contraseña" type="password" :rules="minRules" required>></v-text-field>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="cerrarclave()">Cerrar</v-btn>
          <v-btn color="blue darken-1" text @click="clave()">Cambiar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-main>
      <router-view class="mt-3 py-2 px-6"></router-view>
    </v-main>
  </div>
</template>
<script>
import axios from 'axios';
import iziToast from 'izitoast';

export default {
  data: () => ({
    drawer: false,
    logo:process.env.RUTA+'/assets/img/bar.jpg',
    parametro: null,
    sesion:[],
    opciones:[],
    ven_clave:false,
    minRules: [
      v => !!v || 'Este campo es necesario',
      v => (v && v.length >= 8) || 'Utiliza al menos 8 caracteres',
    ],
    pass1:null,
    pass2:null,
    valid:true,
    opcatalogo:[],
  }),
  mounted () {
    this.parametro = 'go';
  },
  watch:{
    parametro () {
      this.verifica();
    },
  },
  methods : {
    verifica (){
      axios(process.env.RUTA +'/assets/backend/verifica.php',{
        method:'get',
        params: {accion: '1'},
        withCredentials:true
      })
        .then((response)=>{
          if(response.data==2){
            this.$router.push( '/');
          }
          else{
            this.sesion=response.data;
            this.lstcatalogo();
            window.scrollTo(0,0);
          }
        })
        .catch(err => {
          console.log(err)
        })
    },
    salir(){
      axios(process.env.RUTA +'/assets/backend/salir.php',{
        method:'get',
        params: {accion: '1'},
        withCredentials:true
      })
        .then((response)=>{
          if(response.data==1){
            this.$router.push( '/');
          }
        })
        .catch(err => {
          console.log(err)
        })

    },
    cambia(ruta){
      this.drawer = !this.drawer;
      this.$router.push( '/menu/1/'+ruta).catch(err => {});
      window.scrollTo(0,0);
    },
    clave(){
      if(this.pass1!=this.pass2){
        iziToast.error({
          title: 'Alerta',
          message: 'las contraseñas no coinciden',
          position: 'topRight',
          timeout: 2000,
        });
      }else{
        axios.post(process.env.RUTA +'/assets/backend/actualiza.php', {
          accion: 24,
          idusuario:this.sesion.idusuario,
          clave:this.pass1,
        })
          .then((response)=>{
            if(response.data=='1'){
              iziToast.success({
                title: 'Cambiando:',
                message: 'contraseña del usuario',
                position: 'topRight',
                timeout: 2000,
              });
              this.cerrarclave();
            }
            if(response.data!='1'){
              iziToast.error({
                title: 'Alerta',
                message: response.data,
                position: 'topRight',
                timeout: 2000,
              });
            }
          })
          .catch(err => {
            console.log(err)
          })
      }
    },
    cerrarclave(){
      this.ven_clave=false;
      this.pass1=null;
      this.pass2=null;
    },
    lstcatalogo(){
      axios.post(process.env.RUTA +'/assets/backend/data.php', {
        accion: 1,
      })
        .then((response) => {
          this.opcatalogo = response.data;
        })
        .catch(err => {
          console.log(err)
        })
    },
  }
}
</script>
