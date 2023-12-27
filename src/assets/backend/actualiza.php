<?php
$request_headers=apache_request_headers();
$http_origin=$request_headers['Origin'];
$allowed_http_origins=array(
  "http://localhost:8080",
  //"https://pmt.munisumpango.gob.gt"
);
if (in_array($http_origin, $allowed_http_origins)){
  @header("Access-Control-Allow-Origin: " . $http_origin);
}
header('Access-Control-Allow-Methods: GET, PUT, POST');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
header('Access-Control-Allow-Credentials: true');

session_name('bicmo');
session_start();

if (!empty($_SESSION['idusuario'])) {

  require ("fun.php");
  $idusuario=$_SESSION['idusuario'];
  $data = json_decode(file_get_contents("php://input"),true);
  $datos=[];
  require ("conector.php");
  $db = new dbhelper($db_name,$db_host,$db_user,$db_pass);

  //Actualiza area
  if($data['accion']==1){

    $query="SELECT * FROM bitacora.area WHERE area='".$data['area']."' AND color='".$data['color']."' AND idarea!=".$data['idarea'];
    //echo $query;
    $db->setQuery($query);
    $resultado = $db->query();
    $db->getLastErrorMessage();
    if($db->getAffectedRows()>=1){
      echo 'Ya existe un registro con ese nombre';
    }else{

      $query="UPDATE bitacora.area SET area='".$data['area']."', color='".$data['color']."' WHERE idarea=".$data['idarea'];

      $db->setQuery($query);
      $resultado = $db->query();
      $afectadas = $db->getAffectedRows();
      echo $db->getLastErrorMessage();

      $bit=bitacora($idusuario,'area',$data['idarea'],'actualizo',$data['area']);
      echo $afectadas;

    }
  }

  //Actualiza estado
  if($data['accion']==2){

    $tabla=$data['tabla'];
    $campo=$data['campo'];

    if($data['estado']=='activo'){
      $estado="0";
    }else{
      $estado="NULL";
    }

    $query="UPDATE bitacora.".$tabla." SET estado=".$estado." WHERE ".$campo."=".$data['idcambia'];
    $db->setQuery($query);
    $resultado = $db->query();
    $afectadas = $db->getAffectedRows();
    echo $db->getLastErrorMessage();

    $bit=bitacora($idusuario,$tabla,$data['idcambia'],'actualizo','estado '.$estado);
    echo $afectadas;
  }

  //Actualiza ubicacion
  if($data['accion']==3){

    $query="SELECT * FROM bitacora.ubicacion WHERE idarea='".$data['idarea']."' AND ubicacion='".$data['ubicacion']."' AND idubicacion!=".$data['idubicacion'];
    //echo $query;
    $db->setQuery($query);
    $resultado = $db->query();
    $db->getLastErrorMessage();
    if($db->getAffectedRows()>=1){
      echo 'Ya existe un registro con ese nombre';
    }else{

      $query="UPDATE bitacora.ubicacion SET idarea='".$data['idarea']."', ubicacion='".$data['ubicacion']."', latitud='".$data['latitud']."', longitud='".$data['longitud']."' WHERE idubicacion=".$data['idubicacion'];

      $db->setQuery($query);
      $resultado = $db->query();
      $afectadas = $db->getAffectedRows();
      echo $db->getLastErrorMessage();

      $bit=bitacora($idusuario,'ubicacion',$data['idubicacion'],'actualizo',$data['ubicacion']);
      echo $afectadas;

    }
  }

  //Actualiza tipo
  if($data['accion']==4){

    $query="SELECT * FROM bitacora.tipo WHERE tipo='".$data['tipo']."' AND idtipo!=".$data['idtipo'];
    //echo $query;
    $db->setQuery($query);
    $resultado = $db->query();
    $db->getLastErrorMessage();
    if($db->getAffectedRows()>=1){
      echo 'Ya existe un registro con ese nombre';
    }else{

      $query="UPDATE bitacora.tipo SET tipo='".$data['tipo']."', icono='".$data['icono']."' WHERE idtipo=".$data['idtipo'];

      $db->setQuery($query);
      $resultado = $db->query();
      $afectadas = $db->getAffectedRows();
      echo $db->getLastErrorMessage();

      $bit=bitacora($idusuario,'tipo',$data['idtipo'],'actualizo',$data['tipo']);
      echo $afectadas;

    }
  }

  //Actualiza tipo relacion
  if($data['accion']==5){

    $query="SELECT * FROM bitacora.tiporel WHERE tiporel='".$data['tiporel']."' AND idtiporel!=".$data['idtiporel'];
    //echo $query;
    $db->setQuery($query);
    $resultado = $db->query();
    $db->getLastErrorMessage();
    if($db->getAffectedRows()>=1){
      echo 'Ya existe un registro con ese nombre';
    }else{

      $query="UPDATE bitacora.tiporel SET tiporel='".$data['tiporel']."' WHERE idtiporel=".$data['idtiporel'];

      $db->setQuery($query);
      $resultado = $db->query();
      $afectadas = $db->getAffectedRows();
      echo $db->getLastErrorMessage();

      $bit=bitacora($idusuario,'tiporel',$data['idtiporel'],'actualizo',$data['tiporel']);
      echo $afectadas;

    }
  }

  //Actualiza tipo de origen
  if($data['accion']==6){

    $query="SELECT * FROM bitacora.origen WHERE origen='".$data['origen']."' AND idorigen!=".$data['idorigen'];
    //echo $query;
    $db->setQuery($query);
    $resultado = $db->query();
    $db->getLastErrorMessage();
    if($db->getAffectedRows()>=1){
      echo 'Ya existe un registro con ese nombre';
    }else{

      $query="UPDATE bitacora.origen SET origen='".$data['origen']."' WHERE idorigen=".$data['idorigen'];

      $db->setQuery($query);
      $resultado = $db->query();
      $afectadas = $db->getAffectedRows();
      echo $db->getLastErrorMessage();

      $bit=bitacora($idusuario,'origen',$data['idorigen'],'actualizo',$data['origen']);
      echo $afectadas;

    }
  }


}else{
  echo 'Sesión caducada';
}
