<?php // Auth Api file

  switch ( $_SERVER['REQUEST_METHOD'] ){
    case "POST":
      switch ( $_POST['action'] ){
        case "login":            
          if ( !isset($_POST['login']) || empty($_POST['login']) )
            throw new Exception("Login não informado");

          if ( !isset($_POST['password']) || empty($_POST['password']) )
            throw new Exception("Senha não informada");

          $auth = Auth::login($_POST['login'], $_POST['password']);
          if ( !$auth )            
            throw new AuthException("Falha ao realizar login. Usuário ou senha inválidos");

          ApiHelper::json_return($auth);       
          break;
        
        default:
          throw new ApiException("Ação inválida");
          break;
      }
      break;

    case "GET":
      switch ( $_REQUEST['action'] ){
        case "get_auth_info":
          ApiHelper::json_return(Auth::get_auth_info());
          break;
        
        default:
          throw new ApiException("Ação inválida");
          break;
      }
      break;
  }