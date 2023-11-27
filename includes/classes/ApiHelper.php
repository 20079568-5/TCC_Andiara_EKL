<?php

  Class ApiHelper {

    public static function get_public_actions(){
      return [ 
        "auth" => ["login"],
      ];
    }

    public static function null_empty_fields(){
      foreach ( $_REQUEST as $key => $data ){
        if ( $data == "" )
          $_REQUEST[$key] = null;
      }
      foreach ( $_POST as $key => $data ){
        if ( $data == "" )
          $_POST[$key] = null;
      }
    }
    
    public static function create_error_return($error, $api, $status_code = 500){
      if ( !headers_sent() ) {              
        header('Content-Type: application/json');
      }
  
      $retorno = [
        "error_type" => get_class($error),
        "error_code" => $error->getCode() ? $error->getCode() : "",
        "error_message" => $error->getMessage() ? $error->getMessage() : "",
        "api" => $api,
        "popup" => ["title" => get_class($error), "text" =>  $error->getMessage() ? $error->getMessage() : "", ],
        "post" => @$_POST,
        "get" => @$_GET,
      ];
  
      switch ( $retorno["error_type"] ){
        case "FormException": 
          $retorno['field'] = $error->get_field();
          break;

        case "PermissionException": 
          $retorno['permission_required'] = $error->get_permission_required();
          break;
      }
  
      http_response_code($status_code);
  
      echo json_encode($retorno, true);
    }

    public static function set_headers(){
      if ( isset($_REQUEST['header']) && !empty($_REQUEST['header']) ){
        switch ( $_REQUEST['header'] ){
          case "html":
            header('Content-Type: text/html; charset=utf-8');
            break;

          default:
            break;
        }

      }else {
        header('Content-Type: application/json; charset=utf-8');

      }
    }

    public static function json_return($data = []){
      echo json_encode($data, true);
    }

    public static function is_allowed($api, $action, $api_token){
      $public_actions = self::get_public_actions();
      if ( isset($public_actions[$api]) ){
        if ( in_array($action, $public_actions[$api]) )
          return true;
      }

      if ( !empty($api_token) ) // force new session when using token 
        Auth::logout();

      // not logged in, try login with api token
      if ( !isset($_SESSION['auth']['authenticated']) || isset($_SESSION['auth']['authenticated']) != true )
        Auth::token_login($api_token);
        
      return true;
    }

  }