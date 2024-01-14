<?php
$request_headers=apache_request_headers();
$http_origin=$request_headers['Origin'];
$allowed_http_origins=array(
  "http://localhost:8080",
  "https://bit.muniantigua.gob.gt/"
);
if (in_array($http_origin, $allowed_http_origins)){
  @header("Access-Control-Allow-Origin: " . $http_origin);
}
header('Access-Control-Allow-Credentials: true');

if($_GET['accion']==1){

  session_name('bicmo');
  session_start();

  if (!isset($_SESSION['idusuario'])){
    echo 2;
    session_destroy();
  }else{

    $datos=[];
    $datos['idusuario']=$_SESSION['idusuario'];
    $datos['usuario']=$_SESSION['usuario'];
    $datos['sistema']="BICMO v1.0";
    $datos['color']=$_SESSION['color'];

    echo json_encode($datos);

  }
}

?>
