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

//header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Credentials: true');


session_name('bicmo');
session_start();
session_destroy();
echo 1;

?>
