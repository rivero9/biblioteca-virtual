<?php

// database config
define('host', getenv('DB_SERVER'));
define('user', getenv('DB_USERNAME'));
define('pass', getenv('DB_PASSWORD'));
define('db', getenv('DB_NAME'));

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