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

  //Actualiza datos persona
  if($data['accion']==7){

    $query="SELECT * FROM bitacora.persona WHERE nombre='".$data['nombre']."' AND alias='".$data['alias']."' AND idpersona!=".$data['idpersona'];
    //echo $query;
    $db->setQuery($query);
    $resultado = $db->query();
    $db->getLastErrorMessage();
    if($db->getAffectedRows()>=1){
      echo 'Ya existe un registro con ese nombre y alias';
    }else{

      $direccion = isset($data['direccion']) ? "'".$data['direccion']."'" : 'NULL';
      $correo = isset($data['correo']) ? "'".$data['correo']."'" : 'NULL';
      $cui = isset($data['cui']) ? "'".$data['cui']."'" : 'NULL';


      $query="UPDATE bitacora.persona SET nombre='".$data['nombre']."', alias='".$data['alias']."', celular='".$data['celular']."', idtipoper='".$data['idtipoper']."', direccion=".$direccion.", correo=".$correo.", cui=".$cui."
      WHERE idpersona=".$data['idpersona'];

      $db->setQuery($query);
      $resultado = $db->query();
      $afectadas = $db->getAffectedRows();
      echo $db->getLastErrorMessage();

      $bit=bitacora($idusuario,'persona',$data['idpersona'],'actualizo',$data['nombre']);
      echo $afectadas;

    }
  }

  //Actualiza usuario no clave
  if($data['accion']==8){

    $query="SELECT * FROM bitacora.usuario WHERE usuario='".$data['usuario']."' AND idusuario!=".$data['idusuario'];
    //echo $query;
    $db->setQuery($query);
    $resultado = $db->query();
    $db->getLastErrorMessage();
    if($db->getAffectedRows()>=1){
      echo 'Ya existe un usuario con ese nombre';
    }else{

      $query="UPDATE bitacora.usuario SET nombre='".$data['nombre']."', puesto='".$data['puesto']."', usuario='".$data['usuario']."' WHERE idusuario=".$data['idusuario'];
      $db->setQuery($query);
      $resultado = $db->query();
      $afectadas = $db->getAffectedRows();
      echo $db->getLastErrorMessage();

      $bit=bitacora($idusuario,'usuario',$data['idusuario'],'actualizo',$data['usuario']);
      echo $afectadas;

    }
  }

  //cambia clave de usuario
  if($data['accion']==9){

    $query="UPDATE bitacora.usuario SET clave=md5('".$data['clave']."')  WHERE idusuario=".$data['idusuario'];
    $db->setQuery($query);
    $resultado = $db->query();
    $afectadas = $db->getAffectedRows();
    echo $db->getLastErrorMessage();

    $bit=bitacora($idusuario,'usuario',$data['idusuario'],'actualizo','Cambio de contraseña');
    echo $afectadas;
  }

  //Ingresa opcion a usuario
  if($data['accion']==10){

    $query="DELETE FROM bitacora.permiso WHERE idusuario='".$data['idusuario']."' AND idopcion='".$data['idopcion']."'";

    $db->setQuery($query);
    $resultado = $db->query();
    $afectadas = $db->getAffectedRows();
    $lastid = $db->getLastId();
    echo $db->getLastErrorMessage();

    $bit=bitacora($idusuario,'permiso',$data['idusuario'],'elimino',$data['idopcion']);
    echo $afectadas;
  }

  //edita datos de un caso
  if($data['accion']==11){

    $query="UPDATE bitacora.caso SET descripcion='".$data['descripcion']."', idarea='".$data['idarea']."', idorigen='".$data['idorigen']."', idtipo='".$data['idtipo']."', idubicacion='".$data['idubicacion']."'  WHERE idcaso=".$data['idcaso'];
    $db->setQuery($query);
    $resultado = $db->query();
    $afectadas = $db->getAffectedRows();
    echo $db->getLastErrorMessage();

    $bit=bitacora($idusuario,'caso',$data['idcaso'],'actualizo','Edito campos');
    echo $afectadas;
  }

  //reasigna un caso
  if($data['accion']==12){

    $query="UPDATE bitacora.caso SET responsable='".$data['responsable']."'  WHERE idcaso=".$data['idcaso'];
    $db->setQuery($query);
    $resultado = $db->query();
    $afectadas = $db->getAffectedRows();
    echo $db->getLastErrorMessage();

    if($afectadas>=1){

      $comentario="Reasignacion de caso, se quita a ".$data['nombre'];
      $query="INSERT INTO bitacora.historial VALUES (NULL,'".$data['idcaso']."','4','".$idusuario."',now(),'".$comentario."')";
      $db->setQuery($query);
      $resultado = $db->query();
      $afectadas = $db->getAffectedRows();
      echo $db->getLastErrorMessage();

      $bit=bitacora($idusuario,'caso',$data['idcaso'],'actualizo','Cambio responsable');

    }


    echo $afectadas;
  }

  //finaliza un caso
  if($data['accion']==13){

    $query="UPDATE bitacora.caso SET fin=now(), cierre='".$data['cierre']."'  WHERE idcaso=".$data['idcaso'];
    $db->setQuery($query);
    $resultado = $db->query();
    $afectadas = $db->getAffectedRows();
    echo $db->getLastErrorMessage();

    if($afectadas>=1){

      $comentario="El caso fue finalizado";
      $query="INSERT INTO bitacora.historial VALUES (NULL,'".$data['idcaso']."','2','".$idusuario."',now(),'".$comentario."')";
      $db->setQuery($query);
      $resultado = $db->query();
      $afectadas = $db->getAffectedRows();
      echo $db->getLastErrorMessage();

      $bit=bitacora($idusuario,'caso',$data['idcaso'],'actualizo','cierre');

    }


    echo $afectadas;
  }


}else{
  echo 'Sesión caducada';
}
