<template>
  <div>
    <v-row justify="center">
      <v-col cols="12" xs="12" sm="12" md="12" lg="12" xl="12" class="ml-5 txtsec body-1 text-left mb-5">
        <v-icon color="blue darken-2" left>fa-location</v-icon>Registro de Areas de Cobertura:
      </v-col>
      <v-col cols="12" xs="12" sm="12" md="7" lg="7" xl="7" class="pa-0">
        <v-toolbar dark color="secundario" dense>
          <v-toolbar-title>
            <span class="subtitle-1">Registros encontrados</span>
          </v-toolbar-title>
          <v-spacer></v-spacer>
          <v-text-field v-model="search" append-icon="search" label="Filtro" single-line hide-details @keyup.esc="dialog = false" ></v-text-field>
          <v-icon class="mr-2" @click="lstregistros()">refresh</v-icon>
        </v-toolbar>
        <template>
          <v-data-table
            :headers="headers"
            :items="registros"
            item-key="idopcion"
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
            <template v-slot:item.color="{ item }">
              <v-icon large class="mr-2" :color="item.color">fa-location-dot</v-icon>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-icon class="mr-2" @click="ver_editar(item)" color="orange darken-3">edit</v-icon>
              <v-icon class="mr-2" @click="estado(item.idarea,item.estado)" color="orange darken-3">fa-shuffle</v-icon>
            </template>
            <v-alert slot="no-results" :value="true" color="secundario" icon="warning">
              <span class="body-2 white--text">Su busqueda de "{{ search }}" no dio resultados.</span>
            </v-alert>
          </v-data-table>
        </template>
      </v-col>
      <v-col cols="12" xs="12" sm="12" md="1" lg="1" xl="1" class="pa-0"></v-col>
      <v-col cols="12" xs="12" sm="12" md="4" lg="4" xl="4" class="pa-0 elevation-10">
        <template v-if="ven_ingresar==true">
          <v-card rounded>
            <v-card-text small class="primario white--text">
              <span class="subtitle-1">Nuevo Registro</span>
            </v-card-text>
            <v-card-text>
              <v-container>
                <v-form v-model="valid" ref="form" lazy-validation @keyup.native.enter="ingresar()">
                  <v-row align="center" justify="center">
                    <v-col cols="12" class="pa-0">
                      <v-autocomplete dense outlined append-icon="fa-list" :items="colores" v-model="color" label="Seleccione un color identificador" :rules="selRules" required></v-autocomplete>
                    </v-col>
                    <v-col cols="12" class="pa-0">
                      <v-text-field dense outlined v-model="area" label="Escriba el nombre del area" type="text" :rules="minRules" required>></v-text-field>
                    </v-col>
                  </v-row>
                </v-form>
              </v-container>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn small outlined color="blue darken-1" text @click="limpiar()"><v-icon left>fa-eraser</v-icon>Limpiar</v-btn>
              <v-btn small outlined color="blue darken-1" text @click="ingresar()"><v-icon left>fa-add</v-icon>Ingresar</v-btn>
            </v-card-actions>
          </v-card>
        </template>
        <template v-if="ven_editar==true">
          <v-card>
            <v-card-text small class="primario white--text">
              <span class="text-h6">Editar Registro</span>
            </v-card-text>
            <v-card-text>
              <v-container>
                <v-form v-model="valid" ref="form2" lazy-validation @keyup.native.enter="ingresar()">
                  <v-row align="center" justify="center">
                    <v-col cols="12" class="pa-0">
                      <v-autocomplete dense outlined append-icon="fa-list" :items="colores" v-model="registro.color" label="Seleccione un color identificador" :rules="selRules" required></v-autocomplete>
                    </v-col>
                    <v-col cols="12" class="pa-0">
                      <v-text-field dense outlined v-model="registro.area" label="Edite el nombre del area" type="text" :rules="minRules" required>></v-text-field>
                    </v-col>
                  </v-row>
                </v-form>
              </v-container>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn small outlined color="red darken-1" text @click="cerrar_editar()"><v-icon left>fa-circle-xmark</v-icon>Cerrar</v-btn>
              <v-btn small outlined color="red darken-1" text @click="editar()"><v-icon left>fa-pen-to-square</v-icon>actualizar</v-btn>
            </v-card-actions>
          </v-card>
        </template>
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
    valid:true,
    registros:[],
    registro:[],
    search: '',
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
      { text: 'Codigo', value: 'idarea' },
      { text: 'Nombre', value: 'area' },
      { text: 'Color', value: 'color' },
      { text: 'Estado', value: 'estado' },
      { text: 'Editar', value: 'actions', sortable: false }
    ],
    ven_ingresar:true,
    ven_editar:false,
    colores:[],
    color:null,
    area:null,
  }),
  mounted () {
    this.parametro = 'go';
  },
  watch:{
    parametro () {
      this.permiso();
      this.lstregistros();
      this.lstcolores();
    },
  },
  methods : {
    permiso(){
      axios.post(process.env.RUTA+'/assets/backend/data.php', {
        accion: 2,
        idopcion:1,
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
        accion: 3,
      })
        .then((response) => {
          this.registros = response.data;
        })
        .catch(err => {
          console.log(err)
        })
    },
    lstcolores(){
      axios.post(process.env.RUTA +'/assets/backend/data.php', {
        accion: 4,
      })
        .then((response) => {
          this.colores = response.data;
        })
        .catch(err => {
          console.log(err)
        })
    },
    ingresar(){
      if (this.$refs.form.validate()) {
        this.$toastr.confirm('Ingreso de un registro','¿Confirmar?')
          .then(() => {
            axios.post(process.env.RUTA +'/assets/backend/ingresa.php', {
              accion: 1,
              color:this.color,
              area:this.area
            })
              .then((response)=>{
                if(response.data=='1'){
                  iziToast.success({
                    title: 'Registro:',
                    message: 'ingresado',
                    position: 'topRight',
                    timeout: 2000,
                  });
                  this.$refs.form.reset();
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
    estado(idcambia,estado){
      this.$toastr.confirm('Cambiar de estado','¿Confirmar?')
        .then(() => {
          axios.post(process.env.RUTA +'/assets/backend/actualiza.php', {
            accion: 2,
            tabla:'area',
            campo:'idarea',
            estado:estado,
            idcambia:idcambia,
          })
            .then((response)=>{
              if(response.data=='1'){
                iziToast.success({
                  title: 'Estado:',
                  message: 'actualizado',
                  position: 'topRight',
                  timeout: 2000,
                });
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
  }
}
</script>
