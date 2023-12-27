<?php
$request_headers=apache_request_headers();
if (!isset($request_headers['Origin'])) {
  $request_headers['Origin']='';
}
$http_origin=$request_headers['Origin'];
$allowed_http_origins=array(
  "http://localhost:8080",
  //"https://pmt.munisumpango.gob.gt"
);
if (in_array($http_origin, $allowed_http_origins)){
  @header("Access-Control-Allow-Origin: " . $http_origin);
}


header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
header('Access-Control-Allow-Credentials: true');

require ("conector.php");
require ("fun.php");

$db = new dbhelper($db_name,$db_host,$db_user,$db_pass);

$data = json_decode(file_get_contents("php://input"),true);

//login de usuario
if($data['accion']==1){

  $user=$data['usuario'];
  $pass=md5($data['password']);

  $query="SELECT idusuario, usuario, nombre, IFNULL(color, 'FFF') AS color FROM bitacora.usuario WHERE estado is null and usuario='".$user."'";
  $db->setQuery($query);
  $error=$db->getLastErrorMessage();

  $resultado = $db->query();

  if($db->getAffectedRows()==1){

    $res = mysqli_fetch_array($resultado,MYSQLI_ASSOC);
    $query="SELECT idusuario FROM bitacora.usuario WHERE idusuario='".$res['idusuario']."' AND clave='".$pass."'";
    $db->setQuery($query);
    $resultado = $db->query();

    if($db->getAffectedRows()==1){

      $query="SELECT * FROM bitacora.permiso WHERE idusuario='".$res['idusuario']."'";
      $db->setQuery($query);
      $resultado = $db->query();
      if($db->getAffectedRows()){

        $bit=bitacora($res['idusuario'],'usuario',$res['idusuario'],'login','Ingreso al sistema');

        session_name('bicmo');
        session_cache_limiter('nocache,private');
        session_start();

        $_SESSION['idusuario']=$res['idusuario'];
        $_SESSION['usuario']=$res['nombre'];
        $_SESSION['color']=$res['color'];

        echo 1;

      }else{
        echo 'No tiene permisos para este sistema';
      }
    }else{
      echo 'Verifique sus credenciales (2)';
    }
  }else{
    echo 'Verifique sus credenciales (1)';
  }

}
