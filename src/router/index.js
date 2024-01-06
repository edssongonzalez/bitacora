import Vue from 'vue'
import Router from 'vue-router'
import Index from '@/components/index'
import Menu from '@/components/menu'
import Opciones from '@/components/opciones'
import Area from '@/components/area'
import Ubic from '@/components/ubic'
import Tipo from '@/components/tipo'
import Relacion from '@/components/relacion'
import Origen from '@/components/origen'
import Persona from '@/components/persona'
import Usuario from '@/components/usuario'
import Caso from '@/components/caso'
import Dash from '@/components/dash'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'Index',
      component: Index
    },
    {
      path: '/menu/:id',
      name: 'Menu',
      component: Menu,
      children: [
        {
          path: 'opciones',
          component: Opciones,
        },
        {
          path: 'area',
          component: Area,
        },
        {
          path: 'ubic',
          component: Ubic,
        },
        {
          path: 'tipo',
          component: Tipo,
        },
        {
          path: 'relacion',
          component: Relacion,
        },
        {
          path: 'origen',
          component: Origen,
        },
        {
          path: 'persona',
          component: Persona,
        },
        {
          path: 'usuario',
          component: Usuario,
        },
        {
          path: 'caso',
          component: Caso,
        },
        {
          path: 'dash',
          component: Dash,
        }
      ]
    }
  ]
})
