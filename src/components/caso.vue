<template>
  <div>
    <v-row justify="center">
      <v-col cols="11" xs="11" sm="11" md="6" lg="5" xl="4" class="pa-0 ma-5 elevation-5">
        <v-bottom-navigation v-model="value">
          <v-btn @click="lstregistros()">
            <span class="txtprimario">Refrescar</span>
            <v-icon color="blue darken-3">fa-arrows-rotate</v-icon>
          </v-btn>
          <v-btn @click="ven_ingresar=true">
            <span class="txtprimario">Nuevo Caso</span>
            <v-icon color="blue darken-3">fa-circle-plus</v-icon>
          </v-btn>
          <v-btn>
            <span class="txtprimario">Historia</span>
            <v-icon color="blue darken-3">fa-history</v-icon>
          </v-btn>
          <v-btn>
            <span class="txtprimario">Mis Casos</span>
            <v-icon color="blue darken-3">fa-circle-user</v-icon>
          </v-btn>
        </v-bottom-navigation>
      </v-col>
      <v-col cols="12" xs="12" sm="12" md="12" lg="12" xl="12" class="pa-0">
        <v-toolbar dark color="blue-grey darken-4" dense>
          <v-toolbar-title>
            <span class="subtitle-1">Registros encontrados</span>
          </v-toolbar-title>
          <v-spacer></v-spacer>
          <v-text-field v-model="search" append-icon="search" label="Filtro" single-line hide-details @keyup.esc="dialog = false" ></v-text-field>
          <v-icon class="mr-2" >refresh</v-icon>
        </v-toolbar>
        <template>
          <v-data-table
            :headers="headers"
            :items="registros"
            item-key="idcaso"
            :search="search"
            :items-per-page="25"
            :footer-props="{
                showFirstLastPage: true,
                prevIcon: 'keyboard_arrow_left',
                nextIcon: 'keyboard_arrow_right'
              }"
            class="elevation-10"
            :show-no-data="registros.length === 0"
            no-data-text="No hay datos disponibles"
          >
            <template v-slot:item.idcaso="{ item }">
              <v-chip :color="item.sevcolor" dark>{{item.idcaso}}</v-chip> <span class="caption grey--text">#{{item.idseveridad}}</span>
            </template>
            <template v-slot:item.ubica="{ item }">
              <span class="body-2 blue--text">{{item.ubicacion}}, {{item.area}}</span> <v-icon class="mr-2" color="blue" @click="ver_mapa(item.mapa)">fa-location-dot</v-icon>
            </template>
            <template v-slot:item.tiempo="{ item }">
              <span class="body-2 red--text">{{item.dias}}d - {{item.horas}}h</span>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-icon class="mr-2" @click="ver_editar(item)" color="orange darken-3">fa-paperclip</v-icon>
              <v-icon class="mr-2" @click="ver_editar(item)" color="orange darken-3">fa-people-arrows</v-icon>
              <v-icon class="mr-2" @click="ver_editar(item)" color="orange darken-3">edit</v-icon>
              <v-icon class="mr-2" @click="estado(item.idarea,item.estado)" color="orange darken-3">fa-shuffle</v-icon>
            </template>
            <v-alert slot="no-results" :value="true" color="secundario" icon="warning">
              <span class="body-2 white--text">Su busqueda de "{{ search }}" no dio resultados.</span>
            </v-alert>
          </v-data-table>
        </template>
      </v-col>
    </v-row>
    <v-dialog v-model="ven_ingresar" persistent max-width="800px">
      <v-card>
        <v-card-title>
          <span class="headline">Nuevo Caso</span>
          <v-spacer></v-spacer>
          <v-btn icon color="blue darken-1" text @click="ven_ingresar = false"><v-icon>close</v-icon></v-btn>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form v-model="valid" ref="form" lazy-validation @keyup.native.enter="ingresar()">
              <v-row align="center" justify="center">
                <v-col cols="12" class="pa-0">
                  <v-autocomplete dense outlined append-icon="fa-list" :items="origen" v-model="idorigen" label="Origen del caso" :rules="selRules"></v-autocomplete>
                </v-col>
                <v-col cols="12" class="pa-0">
                  <v-autocomplete dense outlined append-icon="fa-list" :items="area" v-model="idarea" label="Area del incidente" :rules="selRules"></v-autocomplete>
                </v-col>
                <v-col cols="12" class="pa-0">
                  <v-autocomplete dense outlined append-icon="fa-list" :items="ubicacion" v-model="idubicacion" label="Ubicacion del incidente" :rules="selRules"></v-autocomplete>
                </v-col>
                <v-col cols="12" class="pa-0">
                  <v-autocomplete dense outlined append-icon="fa-list" :items="tipo" v-model="idtipo" label="Clasificacion del caso" :rules="selRules"></v-autocomplete>
                </v-col>
                <v-col cols="12" class="pa-0">
                  <v-autocomplete dense outlined append-icon="fa-list" :items="severidad" v-model="idserveridad" label="Severidad del caso" :rules="selRules"></v-autocomplete>
                </v-col>
                <v-col cols="12" class="pa-0">
                  <v-textarea dense outlined v-model="descripcion" label="Descripcion del incidente o caso ingresado" type="text" :rules="minRules" required>></v-textarea>
                </v-col>
              </v-row>
            </v-form>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn outlined color="blue darken-1" text @click="ingresar()">ingresar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import axios from 'axios';
import iziToast from 'izitoast';

export default {
  data: () => ({
    parametro: null,
    valid:true,
    registros:[],
    registro:[],
    search: '',
    value: 'recent',
    minRules: [
      v => !!v || 'Este campo es necesario',
      v => (v && v.length >= 2) || 'Utiliza al menos 2 caracteres',
    ],
    unaRules: [
      v => !!v || 'Este campo es necesario',
      v => (v && v.length == 1) || 'Utiliza 1 caracter',
    ],
    selRules:[(v) => !!v || 'Selecciona uno'],
    numRules: [
      v => !!v || 'Cantidad necesaria',
      v => /^[0-9]*$/.test(v) || 'Incorrecto',
    ],
    rutaimg:process.env.RUTA +'/assets/img/',
    headers: [
      { text: 'Codigo', value: 'idcaso' },
      { text: 'Origen', value: 'origen' },
      { text: 'Tipo', value: 'tipo' },
      { text: 'Ubicacion', value: 'ubica' },
      { text: 'Descripcion', value: 'descripcion' },
      { text: 'Inicio', value: 'inicio' },
      { text: 'Fin', value: 'fin' },
      { text: 'Tiempo', value: 'tiempo' },
      { text: 'Responsable', value: 'usuario' },
      { text: 'Opciones', value: 'actions', sortable: false }
    ],
    ven_ingresar:false,
    ven_editar:false,
    tipo:[],
    idtipo:null,
    origen:[],
    idorigen:null,
    severidad:[],
    idserveridad:null,
    area:[],
    idarea:null,
    ubicacion:[],
    idubicacion:null,
    inicio:null,
    descripcion:null,
  }),
  mounted () {
    this.parametro = 'go';
  },
  watch:{
    parametro () {
      this.permiso();
      this.lstregistros();
      this.lstori();
      this.lstsev();
      this.lsttipo();
      this.lstarea();
    },
    idarea(){
      this.lstubi();
    }
  },
  methods : {
    permiso(){
      axios.post(process.env.RUTA+'/assets/backend/data.php', {
        accion: 2,
        idopcion:7,
      })
        .then((response) => {
          if(response.data==1){
            iziToast.success({
              title: 'Validando acceso',
              message: 'correcto',
              position: 'topRight',
              timeout: 1000,
            });
          }else{
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
    lstregistros(){
      axios.post(process.env.RUTA +'/assets/backend/data.php', {
        accion: 17,
      })
        .then((response) => {
          this.registros = response.data;
        })
        .catch(err => {
          console.log(err)
        })
    },
    lstori(){
      axios.post(process.env.RUTA +'/assets/backend/data.php', {
        accion: 18,
        tabla:'origen',
        id:'idorigen',
        campo:'origen',
      })
        .then((response) => {
          this.origen = response.data;
        })
        .catch(err => {
          console.log(err)
        })
    },
    lstsev(){
      axios.post(process.env.RUTA +'/assets/backend/data.php', {
        accion: 18,
        tabla:'severidad',
        id:'idseveridad',
        campo:'severidad',
      })
        .then((response) => {
          this.severidad = response.data;
        })
        .catch(err => {
          console.log(err)
        })
    },
    lsttipo(){
      axios.post(process.env.RUTA +'/assets/backend/data.php', {
        accion: 18,
        tabla:'tipo',
        id:'idtipo',
        campo:'tipo',
      })
        .then((response) => {
          this.tipo = response.data;
        })
        .catch(err => {
          console.log(err)
        })
    },
    lstarea(){
      axios.post(process.env.RUTA +'/assets/backend/data.php', {
        accion: 18,
        tabla:'area',
        id:'idarea',
        campo:'area',
      })
        .then((response) => {
          this.area = response.data;
        })
        .catch(err => {
          console.log(err)
        })
    },
    lstubi(){
      axios.post(process.env.RUTA +'/assets/backend/data.php', {
        accion: 18,
        tabla:'ubicacion',
        id:'idubicacion',
        campo:'ubicacion',
        and:' AND idarea='+this.idarea,
      })
        .then((response) => {
          this.ubicacion = response.data;
        })
        .catch(err => {
          console.log(err)
        })
    },
    ingresar(){
      if (this.$refs.form.validate()) {
        this.$toastr.confirm('Ingreso de un Caso','¿Confirmar?')
          .then(() => {
            axios.post(process.env.RUTA +'/assets/backend/ingresa.php', {
              accion: 9,
              idtipo:this.idtipo,
              idarea:this.idarea,
              idorigen:this.idorigen,
              idseveridad:this.idserveridad,
              idubicacion:this.idubicacion,
              descripcion:this.descripcion
            })
              .then((response)=>{
                if(response.data=='1'){
                  iziToast.success({
                    title: 'Caso:',
                    message: 'ingresado',
                    position: 'topRight',
                    timeout: 2000,
                  });
                  this.$refs.form.reset();
                  this.ven_ingresar=false;
                  this.lstregistros();
                }
                else{
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
          })
      }
    },
    limpiar(){
      this.$refs.form.reset();
    },
    ver_mapa(ruta){
      window.open(ruta, "_blank");
    },
    ver_editar(id){
      this.registro=id;
      this.ven_ingresar=false;
      this.ven_editar=true;
    },
    cerrar_editar(){
      this.ven_ingresar=true;
      this.ven_editar=false;
    },
    editar(){
      if (this.$refs.form2.validate()) {
        this.$toastr.confirm('Edición de un registro','¿Confirmar?')
          .then(() => {
            axios.post(process.env.RUTA +'/assets/backend/actualiza.php', {
              accion: 1,
              color:this.registro.color,
              area:this.registro.area,
              idarea:this.registro.idarea,
            })
              .then((response)=>{
                if(response.data=='1'){
                  iziToast.success({
                    title: 'Registro:',
                    message: 'actualizado',
                    position: 'topRight',
                    timeout: 2000,
                  });
                  this.ven_editar=false;
                  this.ven_ingresar=true;
                  this.lstregistros();
                }
                else{
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
          })
      }
    },
  }
}
</script>
