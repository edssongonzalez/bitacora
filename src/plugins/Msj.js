import iziToast from 'izitoast'

const Msj = {
  install(Vue){
    Vue.prototype.$toastr = {
      confirm : function(mensaje, titulo = 'Confirmar'){
        return new Promise((resolve) => {
          iziToast.question({
            close : true,
            overlay : true,
            toastOnce : true,
            zindex: 999,
            color:'red',
            title : titulo,
            message : mensaje,
            position : "center",
            buttons : [
              ['<button>CONFIRMAR</button>',(instance,toast) => {
                resolve();
                instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
              },true],
              ['<button>CANCELAR</button>',(instance,toast) => {
                instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
              },true]
            ]
          })
        })
      }
    }
  }
};

export default Msj;
