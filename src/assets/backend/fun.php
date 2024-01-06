<?php

$hoy=date("d-m-Y");

function bitacora($id,$tabla,$idafecto,$accion,$comentario){

  global $idusuario, $db, $resultado;
  $query="INSERT INTO bitacora.bitacora VALUES (null,".$id.",now(), '".$tabla."', '".$idafecto."', '".$accion."', '".$comentario."')";
  //echo $query;
  $db->setQuery($query);
  $resultado = $db->query();
  echo $db->getLastErrorMessage();

}

function convertirNumeroAMes($numeroMes)
{
  $meses = array(
    '01' => 'Enero',
    '02' => 'Febrero',
    '03' => 'Marzo',
    '04' => 'Abril',
    '05' => 'Mayo',
    '06' => 'Junio',
    '07' => 'Julio',
    '08' => 'Agosto',
    '09' => 'Septiembre',
    '10' => 'Octubre',
    '11' => 'Noviembre',
    '12' => 'Diciembre'
  );

  // Verificar si el número de mes es válido
  if (array_key_exists($numeroMes, $meses)) {
    return $meses[$numeroMes];
  } else {
    return 'Mes no válido';
  }
}

function convertirNumeroADia($numeroDia) {
  $dias = array(
    '1' => 'Lunes',
    '2' => 'Martes',
    '3' => 'Miércoles',
    '4' => 'Jueves',
    '5' => 'Viernes',
    '6' => 'Sábado',
    '7' => 'Domingo'
  );

  // Verificar si el número de día es válido
  if (array_key_exists($numeroDia, $dias)) {
    return $dias[$numeroDia];
  } else {
    return 'Día no válido';
  }
}

function calcularDiasHabiles($fechaInicio, $fechaFin) {

  $diasHabiles = 0;

  $fechaInicio = new DateTime($fechaInicio);
  $fechaFin = new DateTime($fechaFin);

  $intervalo = new DateInterval('P1D'); // intervalo de 1 día

  $periodo = new DatePeriod($fechaInicio, $intervalo, $fechaFin->modify('+1 day')); // se suma 1 día a la fecha fin para incluirlo, se toma dia completo desde la multa

  foreach ($periodo as $dia) {

    if ($dia->format('N') < 6) { // 6 y 7 son sábado y domingo
      $diasHabiles++;
      //echo $diasHabiles." dia ".$dia->format('Y-m-d')." -> ".$dia->format('N')."<br>";
    }
  }

  return $diasHabiles;
}

function esFechaValida($fecha) {
  $formato = 'Y-m-d'; // Puedes ajustar el formato según tus necesidades

  $dateTime = DateTime::createFromFormat($formato, $fecha);

  return $dateTime && $dateTime->format($formato) === $fecha;
}

function generateRandomString($length = 5) {
  return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}


?>
