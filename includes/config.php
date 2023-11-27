<?php
    spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
  });

  define('PW_SALT', 'soh*eu*sei!');
  define('API_TOKEN_SALT', 'T0k3n*4p1!');
  $db_config = [ 
    "SERVER" => "MYSQL",
    "DB" => "tcc_andiara",
    "HOST" => "localhost",
    "PORT" => 1366,
    "USER" => "portal",
    "PASSWORD" => "0666",
    "CHARSET" => "utf8",
  ];
  define("DB_CONFIG", $db_config);
  define("TEMPLATES_DIR", "/var/www/html/projeto.andiara.local/tcc_andiara/includes/html/templates/");
  define("UPLOAD_FILES_DIR", "/var/www/html/projeto.andiara.local/tcc_andiara/public/assets/uploads/");
  session_start();  
  include_once("CustomExceptions.php");
  $Crud = new Crud();