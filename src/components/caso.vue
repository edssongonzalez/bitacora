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
          <v-btn @click="lstfinalizados()">
            <span class="txtprimario">Historia</span>
            <v-icon color="blue darken-3">fa-history</v-icon>
          </v-btn>
          <v-btn @click="lstpendientes()">
            <span class="txtprimario">Mis Pendientes</span>
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
          <v-text-field v-model="search" append-icon="search" label="Buscar por coincidencia" single-line hide-details @keyup.esc="dialog = false" ></v-text-field>
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
              <span class="body-2 red--text">{{item.dias}}d ({{item.horas}}h)</span>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-btn icon outlined class="orange darken-3" @click="ver_editar(item)" :disabled="item.editar==0"><v-icon color="white">edit</v-icon></v-btn>
              <v-btn icon outlined class="orange darken-3" @click="ver_tiempo(item)"><v-icon color="white">fa-timeline</v-icon></v-btn>
              <v-btn icon outlined class="orange darken-3" @click="ver_foto(item)"><v-icon color="white">fa-camera</v-icon></v-btn>
              <v-btn icon outlined class="orange darken-3" @click="ver_asigna(item)" :disabled="item.editar==0"><v-icon color="white">fa-shuffle</v-icon></v-btn>
              <v-btn icon outlined class="red darken-3" @click="ver_fin(item)" :disabled="item.editar==0"><v-icon color="white">fa-stopwatch</v-icon></v-btn>
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
    <v-dialog v-model="ven_editar" persistent max-width="800px">
      <v-card>
        <v-card-title>
          <span class="headline">Editar el Caso # {{this.registro.idcaso}}</span>
          <v-spacer></v-spacer>
          <v-btn icon color="blue darken-1" text @click="ven_editar = false"><v-icon>close</v-icon></v-btn>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form v-model="valid" ref="form2" lazy-validation @keyup.native.enter="ingresar()">
              <v-row align="center" justify="center">
                <v-col cols="12" class="pa-0">
                  <v-autocomplete dense outlined append-icon="fa-list" :items="origen" v-model="registro.idorigen" label="Origen del caso" :rules="selRules"></v-autocomplete>
                </v-col>
                <v-col cols="12" class="pa-0">
                  <v-autocomplete dense outlined append-icon="fa-list" :items="area" v-model="idarea2" label="Area del incidente" :rules="selRules"></v-autocomplete>
                </v-col>
                <v-col cols="12" class="pa-0">
                  <v-autocomplete dense outlined append-icon="fa-list" :items="ubicacion" v-model="registro.idubicacion" label="Ubicacion del incidente" :rules="selRules"></v-autocomplete>
                </v-col>
                <v-col cols="12" class="pa-0">
                  <v-autocomplete dense outlined append-icon="fa-list" :items="tipo" v-model="registro.idtipo" label="Clasificacion del caso" :rules="selRules"></v-autocomplete>
                </v-col>
                <v-col cols="12" class="pa-0">
                  <v-autocomplete dense outlined append-icon="fa-list" :items="severidad" v-model="registro.idseveridad" label="Severidad del caso" :rules="selRules"></v-autocomplete>
                </v-col>
                <v-col cols="12" class="pa-0">
                  <v-textarea dense outlined v-model="registro.descripcion" label="Descripcion del incidente o caso ingresado" type="text" :rules="minRules" required>></v-textarea>
                </v-col>
              </v-row>
            </v-form>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn outlined color="blue darken-1" text @click="editar()">editar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="ven_tiempo" persistent max-width="600px">
      <v-card>
        <v-card-title>
          <span class="headline">Linea de tiempo Caso # {{this.registro.idcaso}}</span>
          <v-spacer></v-spacer>
          <v-btn icon color="blue darken-1" text @click="ven_tiempo = false"><v-icon>close</v-icon></v-btn>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row align="center" justify="center" class="pa-2">
              <v-col v-for="n in tiempos" :key="n.idbitacora" cols="12" xs="12" sm="12" md="12" lg="12" xl="12"  class="text-left mt-3">
                <v-icon color="blue">fa-square-check</v-icon>
                <span class="body-1 txtprimario">{{n.estado}}<br></span>
                <span class="body-1 txtsec">{{n.fecha}} por {{n.nombre}}<br></span>
                <span class="body-1 txtsec">{{n.comentario}}</span>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="ven_foto" persistent max-width="800px">
      <template v-if="this.ven_foto==true">
        <Fotocaso @ventana="ven_foto=false" :id="this.id"></Fotocaso>
      </template>
    </v-dialog>
    <v-dialog v-model="ven_asigna" persistent max-width="600px">
      <v-card>
        <v-card-title>
          <span class="headline">Reasignar el Caso # {{this.registro.idcaso}}</span>
          <v-spacer></v-spacer>
          <v-btn icon color="blue darken-1" text @click="ven_asigna = false"><v-icon>close</v-icon></v-btn>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form v-model="valid" ref="form3" lazy-validation @keyup.native.enter="asignar()">
              <v-row align="center" justify="center">
                <v-col cols="12" class="pa-0">
                  <v-text-field dense outlined v-model="registro.nombre" label="Responsable actual" disabled></v-text-field>
                </v-col>
                <v-col cols="12" class="pa-0">
                  <v-autocomplete dense outlined append-icon="fa-list" :items="responsables" v-model="responsable" label="Nuevo responsable" :rules="selRules"></v-autocomplete>
                </v-col>
              </v-row>
            </v-form>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn outlined color="blue darken-1" text @click="asignar()">asignar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="ven_fin" persistent max-width="600px">
      <v-card>
        <v-card-title>
          <span class="headline">Finalizar el Caso # {{this.registro.idcaso}}</span>
          <v-spacer></v-spacer>
          <v-btn icon color="blue darken-1" text @click="ven_fin = false"><v-icon>close</v-icon></v-btn>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form v-model="valid" ref="form4" lazy-validation @keyup.native.enter="finalizar()">
              <v-row align="center" justify="center">
                <v-col cols="12" class="pa-0">
                  <v-textarea dense outlined v-model="cierre" label="Cierre del caso" :rules="minRules"></v-textarea>
                </v-col>
              </v-row>
            </v-form>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn outlined color="blue darken-1" text @click="finalizar()">Proceder al cierre</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import axios from 'axios';
import iziToast from 'izitoast';
import Fotocaso from './fotocaso';

export default {
  components:{
    Fotocaso
  },
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
    ven_tiempo:false,
    ven_foto:false,
    ven_asigna:false,
    ven_persona:false,
    ven_fin:false,
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
    tiempos:[],
    idarea2:null,
    responsables:[],
    responsable:null,
    cierre:null,
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
    },
    idarea2(){
      this.lstubi2();
    },

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
    lstfinalizados(){
      axios.post(process.env.RUTA +'/assets/backend/data.php', {
        accion: 22,
      })
        .then((response) => {
          this.registros = response.data;
        })
        .catch(err => {
          console.log(err)
        })
    },
    lstpendientes(){
      axios.post(process.env.RUTA +'/assets/backend/data.php', {
        accion: 23,
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
    lstubi2(){
      axios.post(process.env.RUTA +'/assets/backend/data.php', {
        accion: 18,
        tabla:'ubicacion',
        id:'idubicacion',
        campo:'ubicacion',
        and:' AND idarea='+this.idarea2,
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
      this.ven_editar=true;
      this.idarea2=this.registro.idarea;
    },
    editar(){
      if (this.$refs.form2.validate()) {
        this.$toastr.confirm('Edición de un registro','¿Confirmar?')
          .then(() => {
            axios.post(process.env.RUTA +'/assets/backend/actualiza.php', {
              accion: 11,
              idcaso:this.registro.idcaso,
              idtipo:this.registro.idtipo,
              idarea:this.idarea2,
              idorigen:this.registro.idorigen,
              idseveridad:this.registro.idserveridad,
              idubicacion:this.registro.idubicacion,
              descripcion:this.registro.descripcion
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
    ver_tiempo(id){
      this.registro=id;
      this.ven_tiempo=true;
      axios.post(process.env.RUTA +'/assets/backend/data.php', {
        accion: 19,
        idcaso:this.registro.idcaso,
      })
        .then((response) => {
          this.tiempos = response.data;
        })
        .catch(err => {
          console.log(err)
        })
    },
    ver_foto(id){
      this.registro=id;
      this.id=this.registro.idcaso;
      this.ven_foto=true;
    },
    ver_asigna(id){
      this.registro=id;
      this.ven_asigna=true;
      axios.post(process.env.RUTA +'/assets/backend/data.php', {
        accion: 21,
      })
        .then((response) => {
          this.responsables = response.data;
        })
        .catch(err => {
          console.log(err)
        })
    },
    asignar(){
      if (this.$refs.form3.validate()) {
        this.$toastr.confirm('Reasignar un caso','¿Confirmar?')
          .then(() => {
            axios.post(process.env.RUTA +'/assets/backend/actualiza.php', {
              accion: 12,
              idcaso:this.registro.idcaso,
              responsable:this.responsable,
              nombre:this.registro.nombre,
            })
              .then((response)=>{
                if(response.data=='1'){
                  iziToast.success({
                    title: 'Registro:',
                    message: 'actualizado',
                    position: 'topRight',
                    timeout: 2000,
                  });
                  this.ven_asigna=false;
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
    ver_fin(id){
      this.registro=id;
      this.ven_fin=true;
    },
    finalizar(){
      if (this.$refs.form4.validate()) {
        this.$toastr.confirm('Finalizar el caso','¿Confirmar?')
          .then(() => {
            axios.post(process.env.RUTA +'/assets/backend/actualiza.php', {
              accion: 13,
              idcaso:this.registro.idcaso,
              cierre:this.cierre,
            })
              .then((response)=>{
                if(response.data=='1'){
                  iziToast.success({
                    title: 'Caso:',
                    message: 'finalizado',
                    position: 'topRight',
                    timeout: 2000,
                  });
                  this.ven_fin=false;
                  this.lstregistros();
                  this.cierre=null;
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
