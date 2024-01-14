<template>
  <div class="white">
    <v-card>
      <v-card-title>
        <span class="headline">Imagenes relacionadas al caso # ({{this.id}})</span>
        <v-spacer></v-spacer>
        <v-btn icon color="blue darken-1" text @click="ventanacerrar"><v-icon>close</v-icon></v-btn>
      </v-card-title>
      <v-card-text>
        <v-row align="center" justify="center" class="px-3">
          <v-col cols="12" class="text-center">
            <input type="file" ref="fileInput" @change="onFileSelected"
                   accept="image/*, application/pdf" :disabled="disabled"
                   multiple>
            <v-btn color="primario" dark v-if="icon" icon flat @change="onFileSelected" @click.native="onFocus"></v-btn>
            <v-btn v-else color="blue darken-1" text dark @change="onFileSelected" @click.native="onFocus"><v-icon>cloud_upload</v-icon><span>Seleccionar archivo</span></v-btn>
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-text>
        <v-row align="start" justify="center" class="mt-0 pa-2">
          <v-col v-for="n in registros" :key="n.id" cols="12" xs="11" sm="10" md="9" lg="8" xl="7" class="pa-0 my-1">
            <v-card>
              <v-card-text>
                <v-img contain max-height="300" :src="rutafoto+n.ruta"></v-img>
              </v-card-text>
              <v-card-actions>
                {{n.fecha}} - ({{n.idusuario}})
                <v-spacer></v-spacer>
                <v-btn icon @click="abrir(rutafoto+n.ruta)" ><v-icon color="blue">fas fa-download</v-icon></v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
    <v-spacer></v-spacer>
    <v-dialog v-model="dialog" scrollable persistent @keydown.esc="cancelar" max-width="70%">
      <v-card>
        <v-card-title class="headline">Archivos selecionados </v-card-title>
        <v-data-table
          :headers="headers"
          :items="filesInfo"
          hide-default-footer
          class="elevation-1"
        >
          <template v-slot:item.eliminar="{ item }">
            <v-btn icon slot="activator" @click="deleteItem(item)">
              <v-icon color="red">delete</v-icon>
            </v-btn>
          </template>
        </v-data-table>
        <v-card-actions>
          <v-progress-circular :size="70" :width="10" :rotate="-90" :value="porcentajeDeSubida" color="primary">
            {{ porcentajeDeSubida }}
          </v-progress-circular>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click.native="cancelar" :disabled="uploading">Cancelar</v-btn>
          <v-btn color="blue darken-1" text slot="activator" @click.native="guardar" :disabled="uploading">Subir</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import axios from 'axios';
import iziToast from 'izitoast';
import Foto from './foto';

export default {
  props: ['id'],
  props: {
    id:null,
    privado:null,
    bien:'',
    icon: false,
    label: {
      type: String,
      default: 'Seleccionar'
    },
    disabled: {
      type: Boolean,
      default: false
    },
    multiple: {
      type: Boolean,
      default: false
    },
    info: {
      type: Object,
      default: () => {
        return {
          id: '', modulo: '', tipo_id: ''
        }
      }
    }
  },
  data() {
    return {
      parametro: null,
      rutafoto:process.env.RUTA +'/assets/backend/fotocaso/',
      dialog: false,
      dfotos:[],
      filesInfo: [],
      theFiles: [],
      headers: [
        {text: 'ID', value: 'id'},
        {text: 'NOMBRE', value: 'nombre'},
        {text: 'TIPO', value: 'tipo'},
        {text: 'TAMAÑO', value: 'tamanio'},
        {text: 'ELIMINAR', value: 'eliminar'},
      ],
      porcentajeDeSubida: 0,
      uploading: false,
      documentos:[],
      iddocumento:null,
      registros:[],
      headers2: [
        { text: 'Documento', value: 'archivo' },
        { text: 'Subido por', value: 'usuario' },
        { text: 'El', value: 'fecha' },
        { text: 'Ver', value: 'ver' },
      ],
    }
  },
  mounted () {
    this.parametro = 'go';
  },
  watch:{
    parametro () {
      this.lstregistros();
    }
  },
  methods: {
    lstregistros(){
      axios.post(process.env.RUTA +'/assets/backend/data.php', {
        accion: 20,
        idcaso:this.id,
      })
        .then((response) => {
          this.registros = response.data;
        })
        .catch(err => {
          console.log(err)
        })
    },
    ventanacerrar() {
      this.$emit('ventana');
    },
    actualizar(){
      this.$emit('actualiza');
    },
    bytesToSize(bytes) {
      let sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
      if (bytes == 0) return '0 Byte';
      let i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
      return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
    },
    onFocus() {
      if (!this.disabled) {
        //debugger;
        this.$refs.fileInput.click();
      }
    },
    onFileSelected(e) {
      this.filesInfo = [];
      this.theFiles = [];
      if (e.target.files.length > 0) {
        this.dialog = true;
        let files = e.target.files;
        this.theFiles = Array.from(files);
        this.getFilesInfo(files);
      }
    },
    getFilesInfo(files) {
      for (var i = 0, f; f = files[i]; i++) {
        this.filesInfo.push(
          {
            id: i,
            nombre: f.name,
            tipo: f.type.split('/', 2)[1].toUpperCase(),
            tamanio: f.size
          });
      }
      //this.fileInfo = [...this.files].map(file => file.name).join(', ');
    },
    cancelar() {
      this.dialog = false;
      this.filesInfo = [];
      this.theFiles = [];
      this.porcentajeDeSubida = 0;
      this.uploading = false;
    },
    guardar() {
      this.uploading = true;
      let fd = new FormData();
      for (var i = 0; i < this.theFiles.length; i++) {
        let file = this.theFiles[i];
        fd.append('file[' + i + ']', file);
      }
      fd.append('lleva',this.theFiles.length);
      fd.append('accion', '2');
      fd.append('idcaso', this.id);
      axios.post(process.env.RUTA +'/assets/backend/subir.php', fd, {
        onUploadProgress: uploadEvent => {
          this.porcentajeDeSubida = Math.round(uploadEvent.loaded / uploadEvent.total * 100)
        }
      })
        .then(response => {
          if(response.data=='1'){
            iziToast.success({
              title: 'Fotografia:',
              message: 'relacionada al caso',
              position: 'topRight',
              timeout: 2000,
            });
            this.cancelar();
            this.iddocumento=null;
            this.lstregistros();
          }else{
            iziToast.error({
              title: 'Alerta',
              message: response.data,
              position: 'topRight',
              timeout: 3000,
            });
            this.porcentajeDeSubida = 0;
            this.uploading = false;
          }
        })
        .catch(err => {
          console.log(err);
        })

    },
    deleteItem(item) {
      const index = this.filesInfo.indexOf(item)
      //confirm('Eliminar ' + item.nombre + '?') && this.theFiles.splice(index, 1) && this.filesInfo.splice(index, 1)
      this.theFiles.splice(index, 1) && this.filesInfo.splice(index, 1)
      if (this.filesInfo.length == 0)
        this.cancelar();
    },
    open(ruta) {
      window.open(process.env.RUTA+ruta, "_blank");
    },
    abrir(id){
      window.open(id, "_blank");
    },
  }
}
</script>

<style scoped>
input[type=file] {
  position: absolute;
  left: -99999px;
}
</style>
