<?php // user Api file

  $User = new User();
  switch ( $_SERVER['REQUEST_METHOD'] ){
    case "GET":
      switch ( $_REQUEST['action'] ){
        case "get_form":
          $mode = ( isset($_REQUEST['mode']) ? $_REQUEST['mode'] : "create" );
          ApiHelper::json_return(User::get_form($mode));
          break;

        case "list":
          Auth::has_permission($permission_code = "user_list", $user_id = $_SESSION['auth']['user_id'], $throw_exception = true );
          if ( !isset($_REQUEST["limit"]) || empty($_REQUEST["limit"]) )
            $_REQUEST["limit"] = 1000;

          ApiHelper::json_return($User->list($_REQUEST));          
          break;
        
        case "list_datatable":
          Auth::has_permission($permission_code = "user_list", $user_id = $_SESSION['auth']['user_id'], $throw_exception = true );
          $_REQUEST['ignore_fields'] = ["create_date", "password", "api_token"];
          if ( !isset($_REQUEST["limit"]) || empty($_REQUEST["limit"]) )
            $_REQUEST["limit"] = 1000;

          ApiHelper::json_return($User->get_datatable_list($User->list($_REQUEST)));          
          break;

        default:
          throw new ApiException("Ação inválida");
          break;
      }
      break;

    case "POST":
      switch ( $_REQUEST['action'] ){
        case "create":            
          Auth::has_permission($permission_code = "user_create", $user_id = $_SESSION['auth']['user_id'], $throw_exception = true );
          Helper::validate_form(User::get_form("create"), $_REQUEST, $bypass_required = ["id"]);
          if ( $_POST['password'] != $_POST['password2'] )
            throw new FormException("A senha e a confirmação de senha devem ser iguais.", $field = "password2");
          $User->build($_POST);
          $new_data = $User->create();

          $return = [
            "type" => "ok",
            "message" => "Usuário criado com sucesso! Id: {$new_data}",
            "id" => $new_data,
          ];
          ApiHelper::json_return($new_data);
          break;

        case "edit":
          Auth::has_permission($permission_code = "user_edit", $user_id = $_SESSION['auth']['user_id'], $throw_exception = true );
          Helper::validate_form(User::get_form(), $_POST, $bypass_required = []);
          $user = $User->list(["id" => $_POST['id']]);
          if ( !$user )
            throw new NotFoundException("Usuário id: {$_POST['id']}  não encontrada ");

          $User->to_object($_POST);
          $new_data = $User->edit();
          $return = [
            "type" => "ok",
            "message" => "Usuário id: {$user[0]['login']} - {$user[0]['name']} editado com sucesso",
            "id" => $_POST['id'],
          ];
          ApiHelper::json_return($return);
          break;

        case "delete":
          Auth::has_permission($permission_code = "user_delete", $user_id = $_SESSION['auth']['user_id'], $throw_exception = true );
          Helper::validate_form(User::get_form(), $_POST, $bypass_required = []);
          $user = $User->list(["id" => $_POST['id']]);
          if ( !$user )
            throw new NotFoundException("Usuário id: {$_POST['id']}  não encontrado ");

          $User->to_object($_POST);
          $new_data = $User->delete($_POST['id']);
          $return = [
            "type" => "ok",
            "message" => "Usuário {$user[0]['login']} - {$user[0]['name']} ecluída com sucesso",
            "id" => $_POST['id'],
          ];
          ApiHelper::json_return($return);
          break;
  

        default:
          throw new ApiException("Ação inválida");
          break;
      }
      break;

    case "GET":
      break;
  }