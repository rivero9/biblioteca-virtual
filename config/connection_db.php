<?php

// database

define("host","localhost");
define("db","databases");
define("user","root");
define("pass","");

class Database {

  private $charset = "utf8";

  function connect()
  {
    try {
      $conection = "mysql:host=" . host . "; dbname=" . db . "; charset:" . 
      $this->charset;
      $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
      ];
    
      $pdo = new PDO($conection, user, pass, $options);
    
      return $pdo;
    } catch (PDOException $e){
      echo 'Error de conexión: ' . $e->getMessage();
      exit;
    }
  }
}

?>