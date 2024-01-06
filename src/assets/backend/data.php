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

  $idusuario=$_SESSION['idusuario'];
  $data = json_decode(file_get_contents("php://input"),true);
  $datos=[];
  require ("conector.php");
  $db = new dbhelper($db_name,$db_host,$db_user,$db_pass);

  //Opciones del Menu
  if($data['accion']==1){

    $query="SELECT b.* FROM bitacora.permiso a JOIN bitacora.opcion b ON a.idopcion=b.idopcion WHERE idusuario=".$idusuario." AND estado IS NULL ORDER BY b.orden";
    $db->setQuery($query);
    $resultado = $db->query();
    //echo $db->getLastErrorMessage();
    while($res = mysqli_fetch_array($resultado,MYSQLI_ASSOC)){

      $datos[] = [
        'idopcion'=>$res['idopcion'],
        'nombre'=>$res['nombre'],
        'icono'=>$res['icono'],
        'ruta'=>$res['ruta'],
      ];

    }
    echo json_encode($datos);
  }

  //Confirma que tenga permiso a esa opcion
  if($data['accion']==2){

    $query="SELECT * FROM bitacora.permiso WHERE idopcion=".$data['idopcion']." AND idusuario=".$idusuario;
    $db->setQuery($query);
    $resultado = $db->query();
    echo $db->getLastErrorMessage();
    echo $db->getAffectedRows();
  }

  //Areas de cobertura
  if($data['accion']==3){

    $query="SELECT *, CASE WHEN estado IS NULL THEN 'activo' WHEN estado = 0 THEN 'inactivo' END AS estado FROM bitacora.area";
    $db->setQuery($query);
    $resultado = $db->query();
    //echo $db->getLastErrorMessage();
    while($res = mysqli_fetch_array($resultado,MYSQLI_ASSOC)){

      $datos[] = [
        'idarea'=>$res['idarea'],
        'area'=>$res['area'],
        'color'=>$res['color'],
        'estado'=>$res['estado'],
      ];

    }
    echo json_encode($datos);
  }

  //combo de colores
  if($data['accion']==4){

    $datos[] = [
      'value'=>'green accent-4',
      'text'=>'Verde',
    ];
    $datos[] = [
      'value'=>'yellow accent-4',
      'text'=>'Amarillo',
    ];
    $datos[] = [
      'value'=>'red accent-4',
      'text'=>'Rojo',
    ];

    echo json_encode($datos);
  }

  //Ubicaciones de cobertura
  if($data['accion']==5){

    $query="SELECT a.idubicacion, a.idarea, a.ubicacion, a.latitud, a.longitud, CASE WHEN a.estado IS NULL THEN 'activo' WHEN a.estado = 0 THEN 'inactivo' END AS estado, b.area FROM bitacora.ubicacion a JOIN bitacora.area b ON a.idarea=b.idarea;";
    $db->setQuery($query);
    $resultado = $db->query();
    //echo $db->getLastErrorMessage();
    while($res = mysqli_fetch_array($resultado,MYSQLI_ASSOC)){

      $url_maps = "https://www.google.com/maps?q=".$res['latitud'].",".$res['longitud'];

      $datos[] = [
        'idubicacion'=>$res['idubicacion'],
        'idarea'=>$res['idarea'],
        'area'=>$res['area'],
        'ubicacion'=>$res['ubicacion'],
        'latitud'=>$res['latitud'],
        'longitud'=>$res['longitud'],
        'estado'=>$res['estado'],
        'mapa'=>$url_maps,
      ];

    }
    echo json_encode($datos);
  }

  //combo de areas
  if($data['accion']==6){

    $query="SELECT * FROM bitacora.area WHERE estado IS NULL";
    $db->setQuery($query);
    $resultado = $db->query();
    //echo $db->getLastErrorMessage();
    while($res = mysqli_fetch_array($resultado,MYSQLI_ASSOC)){

      $datos[] = [
        'value'=>$res['idarea'],
        'text'=>$res['area'],
      ];

    }

    echo json_encode($datos);
  }

  //Areas de cobertura
  if($data['accion']==7){

    $query="SELECT idtipo, tipo, icono, CASE WHEN estado IS NULL THEN 'activo' WHEN estado = 0 THEN 'inactivo' END AS estado FROM bitacora.tipo";
    $db->setQuery($query);
    $resultado = $db->query();
    //echo $db->getLastErrorMessage();
    while($res = mysqli_fetch_array($resultado,MYSQLI_ASSOC)){

      $datos[] = [
        'idtipo'=>$res['idtipo'],
        'tipo'=>$res['tipo'],
        'icono'=>$res['icono'],
        'estado'=>$res['estado'],
      ];

    }
    echo json_encode($datos);
  }

  //combo de iconos
  if($data['accion']==8){

    $datos[] = [
      'value'=>'fa-person',
      'text'=>'Persona',
    ];
    $datos[] = [
      'value'=>'fa-people-group',
      'text'=>'Personas',
    ];
    $datos[] = [
      'value'=>'fa-house-user',
      'text'=>'Casa',
    ];
    $datos[] = [
      'value'=>'fa-fire',
      'text'=>'Incencio',
    ];
    $datos[] = [
      'value'=>'fa-truck-medical',
      'text'=>'Ambulancia',
    ];
    $datos[] = [
      'value'=>'fa-car-burst',
      'text'=>'Accidente auto',
    ];
    $datos[] = [
      'value'=>'fa-car',
      'text'=>'Automovil',
    ];
    $datos[] = [
      'value'=>'fa-person-falling-burst',
      'text'=>'Accidente de Persona',
    ];
    $datos[] = [
      'value'=>'fa-motorcycle',
      'text'=>'Motocicleta',
    ];
    $datos[] = [
      'value'=>'fa-truck',
      'text'=>'Camion',
    ];
    $datos[] = [
      'value'=>'fa-pickup',
      'text'=>'Pickup',
    ];
    $datos[] = [
      'value'=>'fa-camera',
      'text'=>'Camara',
    ];
    $datos[] = [
      'value'=>'fa-video',
      'text'=>'Video',
    ];
    $datos[] = [
      'value'=>'fa-champagne-glasses',
      'text'=>'Fiesta',
    ];
    $datos[] = [
      'value'=>'fa-cloud-rain',
      'text'=>'Lluvia',
    ];
    $datos[] = [
      'value'=>'fa-fingerprint',
      'text'=>'Huella dactilar',
    ];
    $datos[] = [
      'value'=>'fa-fingerprint',
      'text'=>'Huella dactilar',
    ];
    $datos[] = [
      'value'=>'fa-phone',
      'text'=>'Telefono',
    ];
    $datos[] = [
      'value'=>'fa-mobile-screen',
      'text'=>'Celular',
    ];
    $datos[] = [
      'value'=>'fa-trash',
      'text'=>'Basura',
    ];

    echo json_encode($datos);
  }

  //Tipos de relaciones
  if($data['accion']==9){

    $query="SELECT idtiporel, tiporel, CASE WHEN estado IS NULL THEN 'activo' WHEN estado = 0 THEN 'inactivo' END AS estado FROM bitacora.tiporel";
    $db->setQuery($query);
    $resultado = $db->query();
    //echo $db->getLastErrorMessage();
    while($res = mysqli_fetch_array($resultado,MYSQLI_ASSOC)){

      $datos[] = [
        'idtiporel'=>$res['idtiporel'],
        'tiporel'=>$res['tiporel'],
        'estado'=>$res['estado'],
      ];

    }
    echo json_encode($datos);
  }

  //Tipos de origen
  if($data['accion']==10){

    $query="SELECT idorigen, origen, CASE WHEN estado IS NULL THEN 'activo' WHEN estado = 0 THEN 'inactivo' END AS estado FROM bitacora.origen";
    $db->setQuery($query);
    $resultado = $db->query();
    //echo $db->getLastErrorMessage();
    while($res = mysqli_fetch_array($resultado,MYSQLI_ASSOC)){

      $datos[] = [
        'idorigen'=>$res['idorigen'],
        'origen'=>$res['origen'],
        'estado'=>$res['estado'],
      ];

    }
    echo json_encode($datos);
  }

  //Peronas
  if($data['accion']==11){

    $query="SELECT *, CASE WHEN estado IS NULL THEN 'activo' WHEN estado = 0 THEN 'inactivo' END AS estado FROM bitacora.persona";
    $db->setQuery($query);
    $resultado = $db->query();
    //echo $db->getLastErrorMessage();
    while($res = mysqli_fetch_array($resultado,MYSQLI_ASSOC)){

      $datos[] = [
        'idpersona'=>$res['idpersona'],
        'idtipoper'=>$res['idtipoper'],
        'nombre'=>$res['nombre'],
        'alias'=>$res['alias'],
        'celular'=>$res['celular'],
        'direccion'=>$res['direccion'],
        'correo'=>$res['correo'],
        'cui'=>$res['cui'],
        'estado'=>$res['estado'],
      ];

    }
    echo json_encode($datos);
  }

  //combo de areas
  if($data['accion']==12){

    $query="SELECT idtipoper, tipoper FROM bitacora.tipoper WHERE estado is null";
    $db->setQuery($query);
    $resultado = $db->query();
    //echo $db->getLastErrorMessage();
    while($res = mysqli_fetch_array($resultado,MYSQLI_ASSOC)){

      $datos[] = [
        'value'=>$res['idtipoper'],
        'text'=>$res['tipoper'],
      ];

    }

    echo json_encode($datos);
  }

  //fotos de la persona
  if($data['accion']==13){

    $query="SELECT * FROM bitacora.perfoto WHERE estado IS NULL AND idpersona=".$data['idpersona'];
    $db->setQuery($query);
    $resultado = $db->query();
    //echo $db->getLastErrorMessage();
    while($res = mysqli_fetch_array($resultado,MYSQLI_ASSOC)){

      $datos[] = [
        'idperfoto'=>$res['idperfoto'],
        'fecha'=>$res['fecha'],
        'idusuario'=>$res['idusuario'],
        'ruta'=>$res['ruta'],
      ];

    }
    echo json_encode($datos);
  }

  //datos del usuario
  if($data['accion']==14){

    $query="SELECT *, CASE WHEN estado IS NULL THEN 'activo' WHEN estado = 0 THEN 'inactivo' END AS estado FROM bitacora.usuario";
    $db->setQuery($query);
    $resultado = $db->query();
    //echo $db->getLastErrorMessage();
    while($res = mysqli_fetch_array($resultado,MYSQLI_ASSOC)){

      $query2="SELECT date_format(fecha,'%d-%m-%Y %H:%i:%s') as ingreso FROM bitacora.bitacora WHERE tabla='usuario' AND accion='login' AND idafecto=".$res['idusuario']." ORDER BY idbitacora DESC LIMIT 1";
      $db->setQuery($query2);
      $resultado2 = $db->query();

      if($db->getAffectedRows()>=1){

        $res2 = mysqli_fetch_array($resultado2,MYSQLI_ASSOC);
        $ingreso=$res2['ingreso'];
      }else{
        $ingreso="Sin ingresos";
      }

      $datos[] = [
        'idusuario'=>$res['idusuario'],
        'usuario'=>$res['usuario'],
        'nombre'=>$res['nombre'],
        'puesto'=>$res['puesto'],
        'color'=>$res['color'],
        'estado'=>$res['estado'],
        'ingreso'=>$ingreso,
      ];

    }
    echo json_encode($datos);
  }

  //combo de opciones disponibles para usuarios
  if($data['accion']==15){

    $query="SELECT idopcion, nombre FROM bitacora.opcion WHERE estado IS NULL";
    $db->setQuery($query);
    $resultado = $db->query();
    echo $db->getLastErrorMessage();
    while($res = mysqli_fetch_array($resultado,MYSQLI_ASSOC)){

      $datos[] = [
        'value'=>$res['idopcion'],
        'text'=>$res['nombre'],
      ];

    }
    echo json_encode($datos);
  }

  //Opciones que tiene un usuario
  if($data['accion']==16){

    $query="SELECT a.idusuario, b.idopcion, b.nombre FROM bitacora.permiso a JOIN bitacora.opcion b ON a.idopcion=b.idopcion WHERE a.idusuario=".$data['idusuario'];
    $db->setQuery($query);
    $resultado = $db->query();
    echo $db->getLastErrorMessage();
    while($res = mysqli_fetch_array($resultado,MYSQLI_ASSOC)){

      $datos[] = [
        'idopcion'=>$res['idopcion'],
        'idusuario'=>$res['idusuario'],
        'nombre'=>$res['nombre'],
      ];

    }
    echo json_encode($datos);
  }


}else{
  echo 'Sesión caducada';
}
