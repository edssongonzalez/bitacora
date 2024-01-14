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

$data = json_decode(file_get_contents("php://input"),true);
$datos=[];

require ("conector.php");
$db = new dbhelper($db_name,$db_host,$db_user,$db_pass);



session_name('bicmo');
session_start();
if (!empty($_SESSION['idusuario'])) {

  require ("fun.php");
  $anio=date('Y');
  $idusuario=$_SESSION['idusuario'];

  //fotografia de una persona
  if($_POST['accion']==1){

    $idpersona=$_POST['idpersona'];

    if((!empty($_FILES["file"])) && ($_FILES['file']['error'][0] == 0)) {

      $filename = basename($_FILES['file']['name'][0]);
      $info = new SplFileInfo($filename);
      $ext=$info->getExtension();

      if($ext=='jpg' or $ext=='JPG' or $ext=='png' or $ext=='PNG' or $ext=='jpeg'){

        if ($_FILES["file"]["size"][0] < 5242880) {

          $peso=$_FILES["file"]["size"][0];
          $random=generateRandomString();
          $filename=$idpersona.'_'.$random.".".$ext;
          $newname = dirname(__FILE__).'/foto/'.$filename;
          $ruta=$filename;

          try {
            move_uploaded_file($_FILES['file']['tmp_name'][0],$newname);
            $query = "INSERT INTO bitacora.perfoto VALUES (NULL,'".$idpersona."', '".$filename."', now(), ".$idusuario.", NULL)";
            $db->setQuery($query);
            $resultado = $db->query();
            echo $db->getLastErrorMessage();
            $afectadas = $db->getAffectedRows();
            $lastid = $db->getLastId();

            echo $afectadas;

            $bit=bitacora($idusuario,'perfoto',$lastid,'ingreso',$filename);

          } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
          }

        } else {
          $mensaje_file="No se adjunto la fotografia, la cual excede el tamaño permitido 5Mb";
        }
      }else{
        echo 'El archivo tipo imagen debe estar en jpg o png';
      }

    }else{
      echo 'Ocurrio un error al recibir el archivo';
    }
  }

  //fotografia de un caso
  if($_POST['accion']==2){

    $idcaso=$_POST['idcaso'];

    if((!empty($_FILES["file"])) && ($_FILES['file']['error'][0] == 0)) {

      $filename = basename($_FILES['file']['name'][0]);
      $info = new SplFileInfo($filename);
      $ext=$info->getExtension();

      if($ext=='jpg' or $ext=='JPG' or $ext=='png' or $ext=='PNG' or $ext=='jpeg'){

        if ($_FILES["file"]["size"][0] < 5242880) {

          $peso=$_FILES["file"]["size"][0];
          $random=generateRandomString();
          $filename=$idcaso.'_'.$random.".".$ext;
          $newname = dirname(__FILE__).'/fotocaso/'.$filename;
          $ruta=$filename;

          try {
            move_uploaded_file($_FILES['file']['tmp_name'][0],$newname);
            $query = "INSERT INTO bitacora.adjunto VALUES (NULL,'".$idcaso."',1 ,'".$filename."', now(), NULL, ".$idusuario.", NULL)";
            $db->setQuery($query);
            $resultado = $db->query();
            echo $db->getLastErrorMessage();
            $afectadas = $db->getAffectedRows();
            $lastid = $db->getLastId();

            echo $afectadas;

            $bit=bitacora($idusuario,'adjunto',$lastid,'ingreso',$filename);

          } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
          }

        } else {
          $mensaje_file="No se adjunto la fotografia, la cual excede el tamaño permitido 5Mb";
        }
      }else{
        echo 'El archivo tipo imagen debe estar en jpg o png';
      }

    }else{
      echo 'Ocurrio un error al recibir el archivo';
    }
  }


}else{
  echo 'Sesión expirada, ingrese de nuevo';
}

?>
