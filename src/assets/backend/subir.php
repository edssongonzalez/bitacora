<?php
$request_headers=apache_request_headers();
$http_origin=$request_headers['Origin'];
$allowed_http_origins=array(
  "http://localhost:8080",
  "https://pmt.munisumpango.gob.gt"
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

function generateRandomString($length = 5) {
  return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

$anio=date('Y');
session_name('muni1');
session_start();
if (!empty($_SESSION['idusuario'])) {

  require ("fun.php");
  $idremision=$_POST['idremision'];
  $idusuario=$_SESSION['idusuario'];

  if($_POST['accion']==1){

    if((!empty($_FILES["file"])) && ($_FILES['file']['error'][0] == 0)) {

      $filename = basename($_FILES['file']['name'][0]);
      $info = new SplFileInfo($filename);
      $ext=$info->getExtension();

      if($ext=='jpg' or $ext=='JPG' or $ext=='png' or $ext=='PNG' or $ext=='jpeg'){

        if ($_FILES["file"]["size"][0] < 5242880) {

          $peso=$_FILES["file"]["size"][0];
          $random=generateRandomString();
          $filename=$idremision.'_'.$random.".".$ext;
          $newname = dirname(__FILE__).'/fotomulta/'.$filename;
          $ruta=$filename;

          try {
            move_uploaded_file($_FILES['file']['tmp_name'][0],$newname);
            $query = "INSERT INTO pmt1.documento VALUES (null,'1', '".$idremision."', 'Fotomulta', '".$filename."', NULL)";
            $db->setQuery($query);
            $resultado = $db->query();
            $afectadas = $db->getAffectedRows();
            $lastid = $db->getLastId();

            if($db->getAffectedRows()==1){

              $query="UPDATE pmt1.documento SET estado=0 WHERE idtipodocumento=1 AND estado IS NULL AND idremision=".$idremision." AND iddocumento!=".$lastid;
              //echo $query;
              $db->setQuery($query);
              $resultado = $db->query();
              echo $db->getLastErrorMessage();

            }

            echo $afectadas;

            $bit=bitacora($idusuario,'documento',$lastid,'ingreso',$filename);

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
