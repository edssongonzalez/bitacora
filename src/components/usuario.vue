<template>
  <div>
    <v-row justify="center">
      <v-col cols="12" xs="12" sm="12" md="12" lg="12" xl="12" class="ml-5 txtsec body-1 text-left mb-5">
        <v-icon color="blue darken-2" left>fa-users-line</v-icon>Usuarios con acceso:
      </v-col>
      <v-col cols="12" xs="12" sm="12" md="8" lg="8" xl="8" class="px-2 py-0">
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
            item-key="idusuario"
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
            <template v-slot:item.idusuario="{ item }">
                <v-chip color="blue darken-2" dark>{{item.idusuario}}</v-chip>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-icon class="mr-2" @click="ver_editar(item)" color="orange darken-3">edit</v-icon>
              <v-icon class="mr-2" @click="estado(item.idusuario,item.estado)" color="orange darken-3">fa-shuffle</v-icon>
              <v-icon class="mr-2" @click="ver_clave(item)" color="red darken-3">lock</v-icon>
              <v-icon class="mr-2" @click="ver_roles(item)" color="red darken-3">fa-user-shield</v-icon>
            </template>
            <v-alert slot="no-results" :value="true" color="secundario" icon="warning">
              <span class="body-2 white--text">Su busqueda de "{{ search }}" no dio resultados.</span>
            </v-alert>
          </v-data-table>
        </template>
      </v-col>
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
                      <v-text-field dense outlined v-model="nombre" label="Nombre completo del usuario" type="text" :rules="minRules" required>></v-text-field>
                    </v-col>
                    <v-col cols="12" class="pa-0">
                      <v-text-field dense outlined v-model="usuario" label="Usuario identificador" type="text" :rules="minRules" required>></v-text-field>
                    </v-col>
                    <v-col cols="12" class="pa-0">
                      <v-text-field dense outlined v-model="puesto" label="Puesto dentro de la Municipalidad" type="text" :rules="minRules" required>></v-text-field>
                    </v-col>
                    <v-col cols="12" class="pa-0">
                      <v-text-field dense outlined v-model="clave" label="Clave de ingreso" type="password" :rules="passRules"></v-text-field>
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
                      <v-text-field dense outlined v-model="registro.nombre" label="Nombre completo del usuario" type="text" :rules="minRules" required>></v-text-field>
                    </v-col>
                    <v-col cols="12" class="pa-0">
                      <v-text-field dense outlined v-model="registro.usuario" label="Usuario identificador" type="text" :rules="minRules" required>></v-text-field>
                    </v-col>
                    <v-col cols="12" class="pa-0">
                      <v-text-field dense outlined v-model="registro.puesto" label="Puesto dentro de la Municipalidad" type="text" :rules="minRules" required>></v-text-field>
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
    <v-dialog v-model="ven_clave" persistent max-width="600px">
      <v-card>
        <v-card-title>
          <span class="headline">Cambiar contraseña</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12" class="pa-0">
                <v-text-field v-model="pass1" label="Escriba una nueva contraseña" type="password" :rules="passRules" required>></v-text-field>
              </v-col>
              <v-col cols="12" class="pa-0">
                <v-text-field v-model="pass2" label="Repita la contraseña" type="password" :rules="passRules" required>></v-text-field>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn outlined color="blue darken-1" text @click="ven_clave = false">Cerrar</v-btn>
          <v-btn outlined color="blue darken-1" text @click="editarclave()">Cambiar contraseña</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="ven_roles" persistent max-width="800px">
      <v-card>
        <v-card-title>
          <span class="headline">Opciones asignadas al usuario: {{this.registro.usuario}}</span>
          <v-spacer></v-spacer>
          <v-icon class="mr-2" @click="ven_roles=false" color="blue darken-2">close</v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form v-model="valid" ref="form4" lazy-validation @keyup.native.enter="agregarol()">
              <v-row align="center" justify="center">
                <v-col cols="6" class="pa-0">
                  <v-autocomplete dense outlined append-icon="fa-info" :items="roles" v-model="idrol" label="Seleccione una opcion" type="text" :rules="selRules" required>></v-autocomplete>
                </v-col>
                <v-col cols="6" class="pa-0 text-center">
                  <v-spacer></v-spacer>
                  <v-btn outlined color="blue darken-1" text @click="agregarol()">Agregar</v-btn>
                </v-col>
              </v-row>
            </v-form>
          </v-container>
        </v-card-text>
        <v-card-text>
          <template>
            <v-data-table
              :headers="headers2"
              :items="rolesactivos"
              item-key="idrol"
              :items-per-page="25"
              :footer-props="{
                showFirstLastPage: true,
                prevIcon: 'keyboard_arrow_left',
                nextIcon: 'keyboard_arrow_right'
              }"
            >
              <template v-slot:item.actions="{ item }">
                <v-icon class="mr-2" @click="quitarrol(item)" color="red darken-2">delete</v-icon>
              </template>
            </v-data-table>
          </template>
        </v-card-text>
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
    minRules: [
      v => !!v || 'Este campo es necesario',
      v => (v && v.length >= 2) || 'Utiliza al menos 2 caracteres',
    ],
    passRules: [
      v => !!v || 'Este campo es necesario',
      v => (v && v.length >= 8) || 'Utiliza al menos 8 caracteres',
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
      { text: 'Codigo', value: 'idusuario' },
      { text: 'Nombre', value: 'nombre' },
      { text: 'Usuario', value: 'usuario' },
      { text: 'Puesto', value: 'puesto' },
      { text: 'Ultimo ingreso', value: 'ingreso' },
      { text: 'Estado', value: 'estado' },
      { text: 'Editar', value: 'actions', sortable: false }
    ],
    ven_ingresar:true,
    ven_editar:false,
    ven_clave:false,
    ven_roles:false,
    nombre:null,
    usuario:null,
    clave:null,
    puesto:null,
    pass1:null,
    pass2:null,
    roles:[],
    idrol:null,
    rolesactivos:[],
    headers2: [
      { text: 'Codigo', value: 'idopcion' },
      { text: 'Opciones del usuario', value: 'nombre' },
      { text: 'Acciones', value: 'actions', sortable: false }
    ],
  }),
  mounted () {
    this.parametro = 'go';
  },
  watch:{
    parametro () {
      this.permiso();
      this.lstregistros();
    },
  },
  methods : {
    permiso(){
      axios.post(process.env.RUTA+'/assets/backend/data.php', {
        accion: 2,
        idopcion:8,
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
        accion: 14,
      })
        .then((response) => {
          this.registros = response.data;
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
              accion: 7,
              nombre:this.nombre,
              usuario:this.usuario,
              clave:this.clave,
              puesto:this.puesto,
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
              accion: 8,
              idusuario:this.registro.idusuario,
              nombre:this.registro.nombre,
              usuario:this.registro.usuario,
              puesto:this.registro.puesto
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
            tabla:'usuario',
            campo:'idusuario',
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

    },
    ver_clave(id){
      this.registro=id;
      this.ven_clave=true;
    },
    editarclave(){
      if(this.pass1!=this.pass2){
        iziToast.error({
          title: 'Alerta',
          message: 'las contraseñas no coinciden',
          position: 'topRight',
          timeout: 2000,
        });
      }else{
        axios.post(process.env.RUTA +'/assets/backend/actualiza.php', {
          accion: 9,
          idusuario:this.registro.idusuario,
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
              this.ven_clave=false;
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
    ver_roles(id){
      this.registro=id;
      axios.post(process.env.RUTA +'/assets/backend/data.php', {
        accion: 15,
      })
        .then((response) => {
          this.roles = response.data;
        })
        .catch(err => {
          console.log(err)
        })
      axios.post(process.env.RUTA +'/assets/backend/data.php', {
        accion: 16,
        idusuario:this.registro.idusuario,
      })
        .then((response) => {
          this.rolesactivos = response.data;
        })
        .catch(err => {
          console.log(err)
        })
      this.ven_roles=true;
    },
    agregarol(){
      axios.post(process.env.RUTA +'/assets/backend/ingresa.php', {
        accion: 8,
        idusuario:this.registro.idusuario,
        idopcion:this.idrol,
      })
        .then((response)=>{
          if(response.data=='1'){
            iziToast.success({
              title: 'Opcion:',
              message: 'asignada',
              position: 'topRight',
              timeout: 2000,
            });
            this.ven_roles=false;
            this.lstregistros();
            this.$refs.form4.reset();
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

    },
    quitarrol(id){
      this.registro=id;
      axios.post(process.env.RUTA +'/assets/backend/actualiza.php', {
        accion: 10,
        idusuario:this.registro.idusuario,
        idopcion:this.registro.idopcion,
      })
        .then((response)=>{
          if(response.data=='1'){
            iziToast.success({
              title: 'Opcion:',
              message: 'eliminada',
              position: 'topRight',
              timeout: 2000,
            });
            this.ven_roles=false;
            this.lstregistros();
            this.$refs.form4.reset();
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

    }
  }
}
</script>
