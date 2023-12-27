<template>
  <div id="app">
    <v-app>
      <router-view></router-view>
    </v-app>
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: left;
  color: #2c3e50;
  margin: 0;
  background-color: #ECEFF1;
}
.txtprimario{
  color: #2873a5 !important;
}
.txtsec{
  color: #112d40 !important;
}
.txtter{
  color: #e74c3c !important;
}
.primario{
  background-color: #266b99 !important;
}
.secundario{
  background-color: #112d40 !important;
}
.terciario{
  background-color: #e74c3c !important;
}
</style>
