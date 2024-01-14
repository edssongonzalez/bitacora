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

  //Ingresa_area
  if($data['accion']==1){

    $query="SELECT * FROM bitacora.area WHERE area='".$data['area']."'";
    $db->setQuery($query);
    $resultado = $db->query();
    //echo $db->getLastErrorMessage();
    if($db->getAffectedRows()>=1){
      echo 'Este registro ya existe';
    }else{

      $query="INSERT INTO bitacora.area (area, color) VALUES ('".$data['area']."','".$data['color']."')";

      $db->setQuery($query);
      $resultado = $db->query();
      $afectadas = $db->getAffectedRows();
      $lastid = $db->getLastId();
      echo $db->getLastErrorMessage();

      $bit=bitacora($idusuario,'area',$lastid,'ingreso',$data['area']);
      echo $afectadas;

    }
  }

  //Ingresa_ubicacion
  if($data['accion']==2){

    $query="SELECT * FROM bitacora.ubicacion WHERE idarea='".$data['idarea']."' AND ubicacion='".$data['ubicacion']."'";
    $db->setQuery($query);
    $resultado = $db->query();
    //echo $db->getLastErrorMessage();
    if($db->getAffectedRows()>=1){
      echo 'Este registro ya existe';
    }else{

      $query="INSERT INTO bitacora.ubicacion (idarea, ubicacion, latitud, longitud) VALUES ('".$data['idarea']."','".$data['ubicacion']."','".$data['latitud']."','".$data['longitud']."')";

      $db->setQuery($query);
      $resultado = $db->query();
      $afectadas = $db->getAffectedRows();
      $lastid = $db->getLastId();
      echo $db->getLastErrorMessage();

      $bit=bitacora($idusuario,'ubicacion',$lastid,'ingreso',$data['ubicacion']);
      echo $afectadas;

    }
  }

  //Ingresa un tipo
  if($data['accion']==3){

    $query="SELECT * FROM bitacora.tipo WHERE tipo='".$data['tipo']."'";
    $db->setQuery($query);
    $resultado = $db->query();
    //echo $db->getLastErrorMessage();
    if($db->getAffectedRows()>=1){
      echo 'Este registro ya existe';
    }else{

      $query="INSERT INTO bitacora.tipo (tipo, icono) VALUES ('".$data['tipo']."','".$data['icono']."')";

      $db->setQuery($query);
      $resultado = $db->query();
      $afectadas = $db->getAffectedRows();
      $lastid = $db->getLastId();
      echo $db->getLastErrorMessage();

      $bit=bitacora($idusuario,'tipo',$lastid,'ingreso',$data['tipo']);
      echo $afectadas;

    }
  }

  //Ingresa un tipo de relacion
  if($data['accion']==4){

    $query="SELECT * FROM bitacora.tiporel WHERE tiporel='".$data['tiporel']."'";
    $db->setQuery($query);
    $resultado = $db->query();
    //echo $db->getLastErrorMessage();
    if($db->getAffectedRows()>=1){
      echo 'Este registro ya existe';
    }else{

      $query="INSERT INTO bitacora.tiporel (tiporel) VALUES ('".$data['tiporel']."')";

      $db->setQuery($query);
      $resultado = $db->query();
      $afectadas = $db->getAffectedRows();
      $lastid = $db->getLastId();
      echo $db->getLastErrorMessage();

      $bit=bitacora($idusuario,'tiporel',$lastid,'ingreso',$data['tiporel']);
      echo $afectadas;

    }
  }

  //Ingresa un tipo de origen
  if($data['accion']==5){

    $query="SELECT * FROM bitacora.origen WHERE origen='".$data['origen']."'";
    $db->setQuery($query);
    $resultado = $db->query();
    //echo $db->getLastErrorMessage();
    if($db->getAffectedRows()>=1){
      echo 'Este registro ya existe';
    }else{

      $query="INSERT INTO bitacora.origen (origen) VALUES ('".$data['origen']."')";

      $db->setQuery($query);
      $resultado = $db->query();
      $afectadas = $db->getAffectedRows();
      $lastid = $db->getLastId();
      echo $db->getLastErrorMessage();

      $bit=bitacora($idusuario,'origen',$lastid,'ingreso',$data['origen']);
      echo $afectadas;

    }
  }

  //Ingresa una persona
  if($data['accion']==6){

    $query="SELECT * FROM bitacora.persona WHERE nombre='".$data['nombre']."' AND alias='".$data['alias']."'";
    $db->setQuery($query);
    $resultado = $db->query();
    //echo $db->getLastErrorMessage();
    if($db->getAffectedRows()>=1){
      echo 'Este registro ya existe';
    }else{

      $direccion = isset($data['direccion']) ? "'".$data['direccion']."'" : 'NULL';
      $correo = isset($data['correo']) ? "'".$data['correo']."'" : 'NULL';
      $cui = isset($data['cui']) ? "'".$data['cui']."'" : 'NULL';

      $query="INSERT INTO bitacora.persona (idtipoper, nombre, alias, celular, direccion, correo, cui)
                VALUES ('".$data['idtipoper']."','".$data['nombre']."', '".$data['alias']."', '".$data['celular']."',".$direccion.",".$correo.",".$cui.")";

      $db->setQuery($query);
      $resultado = $db->query();
      $afectadas = $db->getAffectedRows();
      $lastid = $db->getLastId();
      echo $db->getLastErrorMessage();

      $bit=bitacora($idusuario,'persona',$lastid,'ingreso',$data['nombre']);
      echo $afectadas;

    }
  }

  //Ingresa un usuario
  if($data['accion']==7){

    $query="SELECT * FROM bitacora.usuario WHERE usuario='".$data['usuario']."'";
    $db->setQuery($query);
    $resultado = $db->query();
    //echo $db->getLastErrorMessage();
    if($db->getAffectedRows()>=1){
      echo 'Este registro ya existe';
    }else{

      $query="INSERT INTO bitacora.usuario VALUES (null,'".$data['usuario']."', md5('".$data['clave']."'), '".$data['nombre']."', '".$data['puesto']."', null, null)";

      $db->setQuery($query);
      $resultado = $db->query();
      $afectadas = $db->getAffectedRows();
      $lastid = $db->getLastId();
      echo $db->getLastErrorMessage();

      $bit=bitacora($idusuario,'usuario',$lastid,'ingreso',$data['usuario']);
      echo $afectadas;

    }
  }

  //Ingresa opcion a usuario
  if($data['accion']==8){

    $query="INSERT IGNORE INTO bitacora.permiso VALUES ('".$data['idusuario']."','".$data['idopcion']."')";

    $db->setQuery($query);
    $resultado = $db->query();
    $afectadas = $db->getAffectedRows();
    $lastid = $db->getLastId();
    echo $db->getLastErrorMessage();

    $bit=bitacora($idusuario,'permiso',$data['idusuario'],'ingreso',$data['idopcion']);
    echo $afectadas;
  }

  //Ingresa opcion a usuario
  if($data['accion']==9){

    $query="INSERT INTO bitacora.caso VALUES (NULL,'".$data['idtipo']."','".$data['idorigen']."','".$data['idseveridad']."','".$data['idubicacion']."','".$data['idarea']."', now(), null, '".$data['descripcion']."',null,".$idusuario.")";
    $db->setQuery($query);
    $resultado = $db->query();
    $afectadas = $db->getAffectedRows();
    $lastid = $db->getLastId();
    echo $db->getLastErrorMessage();

    if($lastid>=1){

      $query="INSERT INTO bitacora.historial VALUES (NULL,'".$lastid."','1','".$idusuario."',now(),'')";
      $db->setQuery($query);
      $resultado = $db->query();
      $afectadas = $db->getAffectedRows();

      $bit=bitacora($idusuario,'caso',$lastid,'ingreso',$data['descripcion']);
      echo $afectadas;
    }


  }

}else{
  echo 'Sesión caducada';
}
