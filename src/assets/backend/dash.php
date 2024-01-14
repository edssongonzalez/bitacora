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

session_name('muni1');
session_start();

if (!empty($_SESSION['idusuario'])) {

  $idusuario=$_SESSION['idusuario'];
  $data = json_decode(file_get_contents("php://input"),true);
  $datos=[];
  require ("conector.php");
  require ("fun.php");
  $db = new dbhelper($db_name,$db_host,$db_user,$db_pass);


    //numeros generales remisiones
  if($data['accion']==1){

    $query="SELECT count(*) as numero FROM pmt1.remision WHERE montopagado IS NULL";
    $db->setQuery($query);
    $resultado = $db->query();
    echo $db->getLastErrorMessage();
    $res = mysqli_fetch_array($resultado,MYSQLI_ASSOC);
    $datos[] = [
      'numero'=>number_format($res['numero'],0) ,
      'color'=>'secundario',
      'texto'=>'Pendientes de pago'
    ];

    $query="SELECT count(*) as numero FROM pmt1.remision WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
    $db->setQuery($query);
    $resultado = $db->query();
    echo $db->getLastErrorMessage();
    $res = mysqli_fetch_array($resultado,MYSQLI_ASSOC);
    $datos[] = [
      'numero'=>number_format($res['numero'],0),
      'color'=>'secundario',
      'texto'=>'Impuestas ultimos 30 dias'
    ];

    $query="SELECT count(*) as numero FROM pmt1.remision WHERE montopagado>=1 AND fecha >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
    $db->setQuery($query);
    $resultado = $db->query();
    echo $db->getLastErrorMessage();
    $res = mysqli_fetch_array($resultado,MYSQLI_ASSOC);
    $datos[] = [
      'numero'=>number_format($res['numero'],0),
      'color'=>'secundario',
      'texto'=>'Pagadas ultimos 30 dias'
    ];

    $query="SELECT count(*) as numero FROM pmt1.remision WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 2 DAY)";
    $db->setQuery($query);
    $resultado = $db->query();
    echo $db->getLastErrorMessage();
    $res = mysqli_fetch_array($resultado,MYSQLI_ASSOC);
    $datos[] = [
      'numero'=>number_format($res['numero'],0),
      'color'=>'orange darken-2',
      'texto'=>'Impuestas ultimas 48 horas'
    ];

    $query="SELECT count(*) as numero FROM pmt1.remision WHERE montopagado>=1 AND fecha >= DATE_SUB(CURDATE(), INTERVAL 2 DAY)";
    $db->setQuery($query);
    $resultado = $db->query();
    echo $db->getLastErrorMessage();
    $res = mysqli_fetch_array($resultado,MYSQLI_ASSOC);
    $datos[] = [
      'numero'=>number_format($res['numero'],0),
      'color'=>'orange darken-2',
      'texto'=>'Pagadas ultimas 48 horas'
    ];

    echo json_encode($datos);
  }

  //multas por mes ultimos dos años
  if($data['accion']==2){

    $query="SELECT date_format(fecha,'%m') as mes, count(*) as numero FROM pmt1.remision WHERE montopagar>0 AND fecha>=DATE_SUB(now(), INTERVAL 2 YEAR) GROUP BY date_format(fecha,'%m') ORDER BY date_format(fecha,'%m')";
    $db->setQuery($query);
    $resultado = $db->query();
    echo $db->getLastErrorMessage();
    while($res = mysqli_fetch_array($resultado,MYSQLI_ASSOC)){

      $nombreMes = convertirNumeroAMes($res['mes']);

      $datos[] = [
        'mes' => $nombreMes,
        'numero' => $res['numero'],
      ];

    }
    echo json_encode($datos);
  }

  //multas por dia ultimos dos años
  if($data['accion']==3){

    $query="SELECT DAYOFWEEK(fecha) AS dia, count(*) as numero FROM pmt1.remision WHERE montopagar>0 AND fecha>=DATE_SUB(now(), INTERVAL 2 YEAR) GROUP BY DAYNAME(fecha) ORDER BY DAYNAME(fecha)";
    $db->setQuery($query);
    $resultado = $db->query();
    echo $db->getLastErrorMessage();
    while($res = mysqli_fetch_array($resultado,MYSQLI_ASSOC)){

      $dia = convertirNumeroADia($res['dia']);

      $datos[] = [
        'dia' => $dia,
        'numero' => $res['numero'],
      ];

    }
    echo json_encode($datos);
  }

  //multas por tipo de placa
  if($data['accion']==4){

    $query="SELECT b.nombre, count(*) as numero FROM pmt1.remision a JOIN pmt1.tipoplaca b ON a.idtipoplaca=b.idtipoplaca GROUP BY a.idtipoplaca";
    $db->setQuery($query);
    $resultado = $db->query();
    echo $db->getLastErrorMessage();
    while($res = mysqli_fetch_array($resultado,MYSQLI_ASSOC)){

      $datos[] = [
        'key' => $res['nombre'],
        'valor' => $res['numero'],
      ];

    }
    echo json_encode($datos);
  }

  //multas por tipo de vehiculo
  if($data['accion']==5){

    $query="SELECT b.nombre, count(*) as numero FROM pmt1.remision a JOIN pmt1.tipovehiculo b ON a.idtipovehiculo=b.idtipovehiculo GROUP BY a.idtipovehiculo ORDER BY numero DESC LIMIT 7";
    $db->setQuery($query);
    $resultado = $db->query();
    echo $db->getLastErrorMessage();
    while($res = mysqli_fetch_array($resultado,MYSQLI_ASSOC)){

      $datos[] = [
        'key' => $res['nombre'],
        'valor' => $res['numero'],
      ];

    }
    echo json_encode($datos);
  }

  //multas por marca de vehiculo
  if($data['accion']==6){

    $query="SELECT b.nombre, count(*) as numero FROM pmt1.remision a JOIN pmt1.tipomarca b ON a.idtipomarca=b.idtipomarca GROUP BY a.idtipomarca ORDER BY numero DESC LIMIT 7";
    $db->setQuery($query);
    $resultado = $db->query();
    echo $db->getLastErrorMessage();
    while($res = mysqli_fetch_array($resultado,MYSQLI_ASSOC)){

      $datos[] = [
        'key' => $res['nombre'],
        'valor' => $res['numero'],
      ];

    }
    echo json_encode($datos);
  }

  //multas por articulo de vehiculo
  if($data['accion']==7){

    $query="SELECT b.articulo as nombre, count(*) as numero FROM pmt1.remision a JOIN pmt1.articulo b ON a.idarticulo=b.idarticulo GROUP BY a.idarticulo ORDER BY numero DESC LIMIT 7";
    $db->setQuery($query);
    $resultado = $db->query();
    echo $db->getLastErrorMessage();
    while($res = mysqli_fetch_array($resultado,MYSQLI_ASSOC)){

      $datos[] = [
        'key' => $res['nombre'],
        'valor' => $res['numero'],
      ];

    }
    echo json_encode($datos);
  }

  //numeros generales remisiones
  if($data['accion']==8){

    $query="SELECT SUM(montopagar+interespagar) as numero FROM pmt1.remision WHERE montopagado IS NULL";
    $db->setQuery($query);
    $resultado = $db->query();
    echo $db->getLastErrorMessage();
    $res = mysqli_fetch_array($resultado,MYSQLI_ASSOC);
    $datos[] = [
      'numero'=>number_format($res['numero'],2) ,
      'color'=>'red darken-3',
      'texto'=>'Pendiente de pago'
    ];

    $query="SELECT SUM(montopagar+interespagar) as numero FROM pmt1.remision WHERE montopagado IS NULL AND fecha >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
    $db->setQuery($query);
    $resultado = $db->query();
    echo $db->getLastErrorMessage();
    $res = mysqli_fetch_array($resultado,MYSQLI_ASSOC);
    $datos[] = [
      'numero'=>number_format($res['numero'],2) ,
      'color'=>'red darken-3',
      'texto'=>'Impuesto ultimos 30 dias'
    ];

    $query="SELECT SUM(monto) AS numero FROM pmt1.recibo WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
    $db->setQuery($query);
    $resultado = $db->query();
    echo $db->getLastErrorMessage();
    $res = mysqli_fetch_array($resultado,MYSQLI_ASSOC);
    $datos[] = [
      'numero'=>number_format($res['numero'],2) ,
      'color'=>'green darken-3',
      'texto'=>'Pagado ultimos 30 dias'
    ];


    echo json_encode($datos);
  }

  //ingresos por mes ambos anios
  if($data['accion']==9){

    $query="SELECT date_format(fecha,'%m') as mes, truncate(sum(monto),0) as monto  FROM pmt1.recibo WHERE date_format(fecha,'%Y')=date_format(DATE_SUB(now(), INTERVAL 1 YEAR),'%Y')  GROUP BY date_format(fecha,'%m') ";
    $db->setQuery($query);
    $resultado = $db->query();
    echo $db->getLastErrorMessage();
    while($res = mysqli_fetch_array($resultado,MYSQLI_ASSOC)){

      $nombreMes = convertirNumeroAMes($res['mes']);

      $datos[] = [
        'mes' => $nombreMes,
        'ingreso' => $res['monto'],
      ];

    }

    $query="SELECT date_format(fecha,'%m') as mes, truncate(sum(monto),0) as monto  FROM pmt1.recibo WHERE date_format(fecha,'%Y')=date_format(now(),'%Y')  GROUP BY date_format(fecha,'%m')";
    $db->setQuery($query);
    $resultado = $db->query();
    echo $db->getLastErrorMessage();
    while($res = mysqli_fetch_array($resultado,MYSQLI_ASSOC)){

      $nombreMes = convertirNumeroAMes($res['mes']);

      $datos[] = [
        'mes' => $nombreMes,
        'ingreso2' => $res['monto'],
      ];

    }


    echo json_encode($datos);
  }

  //top 25 deudores
  if($data['accion']==10) {

    $query="SELECT c.nombre as tipo, CONCAT(b.inicial,'-',a.placa) AS placa, count(*) as multas, SUM(montopagar+interespagar) as monto
FROM pmt1.remision a
JOIN pmt1.tipoplaca b ON a.idtipoplaca=b.idtipoplaca
JOIN pmt1.tipovehiculo c ON a.idtipovehiculo=c.idtipovehiculo
WHERE a.montopagado IS NULL
GROUP BY a.placa ORDER BY monto DESC LIMIT 25";
    $db->setQuery($query);
    $resultado = $db->query();
    echo $db->getLastErrorMessage();
    while ($res = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {

      $datos[] = [
        'tipo' => $res['tipo'],
        'placa' => $res['placa'],
        'multas' => $res['multas'],
        'monto' => "Q".number_format($res['monto'],2),
      ];

    }
    echo json_encode($datos);
  }

}else{
  echo 'Sesión caducada';
}
