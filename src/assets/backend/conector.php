<?php
//conector a base de datos


$db_name="bitacora";
$db_host="localhost";
$db_user="antiguaudb";
$db_pass="augitna20";
//$db_pass="20@Ug1tn@";

class dbhelper{

  public $dblink;
  private $query;

  function __construct($db_name, $db_host, $db_user, $db_pass){
    $this->dblink = mysqli_connect($db_host, $db_user, $db_pass) or die(mysqli_error());
    if(!$this->dblink){
      die("Error al conectarse a la base de datos: ".mysqli_error());
    }

    if(!mysqli_select_db($this->dblink, $db_name)){
      die("Error al seleccionar la base de datos: ".mysqli_error());
    }

    if(!mysqli_query($this->dblink, "SET NAMES utf8")){
      die("Error al configurar el juego de caracteres: ".mysqli_error());
    }

    return $this->dblink;
  }

  public function setQuery($query){
    $this->query = $query;
  }

  public function query(){
    $result = mysqli_query($this->dblink,$this->query);
    return $result;
  }

  public function sanitize($var){
    return mysqli_real_escape_string($var);
  }

  public function getLastErrorMessage(){
    return mysqli_error($this->dblink);
  }

  public function getLastNumErrorMessage(){
    return mysqli_error($this->dblink);
  }

  public function getAffectedRows(){
    return mysqli_affected_rows($this->dblink);
  }

  public function getReturnedRows($result){
    return mysqli_num_rows($result);
  }

  public function getLastId(){
    return mysqli_insert_id($this->dblink);
  }

}

?>
