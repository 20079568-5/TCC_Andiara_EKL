<?php // Tarefa Api file

  $Tarefa = new Tarefa();
  switch ( $_SERVER['REQUEST_METHOD'] ){
    case "GET":
      switch ( $_REQUEST['action'] ){
        case "get_form":
          $mode = ( isset($_REQUEST['mode']) ? $_REQUEST['mode'] : "create" );
          ApiHelper::json_return(Tarefa::get_form($mode));
          break;

        case "list":
          if ( !isset($_REQUEST["limit"]) || empty($_REQUEST["limit"]) )
            $_REQUEST["limit"] = 1000;

          ApiHelper::json_return($Tarefa->list($_REQUEST));          
          break;
        
        case "list_datatable":
          $_REQUEST['ignore_fields'] = ["create_date", "password", "api_token"];
          if ( !isset($_REQUEST["limit"]) || empty($_REQUEST["limit"]) )
            $_REQUEST["limit"] = 1000;

          ApiHelper::json_return($Tarefa->get_datatable_list($Tarefa->list($_REQUEST)));          
          break;

        default:
          throw new ApiException("Ação inválida");
          break;
      }
      break;

    case "POST":
      switch ( $_REQUEST['action'] ){
        case "create":            
          Helper::validate_form(Tarefa::get_form("create"), $_REQUEST, $bypass_required = ["id",]);
          
          $Tarefa->to_object($_POST);

          $new_data = $Tarefa->create();
          $message = "Tarefa criado com sucesso! Id: {$new_data}";
          $return = [
            "type" => "ok",
            "message" => $message,
            "id" => $new_data,
            "popup" => ["type" => "success", "title" => "Tudo certo!", "text" => $message,]
          ];
          ApiHelper::json_return($return);
          break;

        case "edit":
          Helper::validate_form(Tarefa::get_form(), $_POST, $bypass_required = []);
         
          $tarefa = $Tarefa->list(["id" => $_POST['id']]);
          if ( !$Tarefa )
            throw new NotFoundException("Tarefa id: {$_POST['id']}  não encontrada ");
          
          $Tarefa->to_object($_POST);
          $new_data = $Tarefa->edit();
          $message = "Tarefa id: {$tarefa[0]['id']} editado com sucesso";
          $return = [
            "type" => "ok",
            "message" => $message,
            "id" => $_POST['id'],
            "POST" => $_POST,
            "popup" => ["type" => "success", "title" => "Tudo certo!", "text" => $message,]
          ];
          ApiHelper::json_return($return);
          break;

        case "delete":
          if ( !isset($_POST['id']) || empty($_POST['id']) )
            throw new FormException("Id não informado, impossível realizar operação");

          $tarefa = $Tarefa->list(["id" => $_POST['id']]);
          if ( !$Tarefa )
            throw new NotFoundException("Tarefa id: {$_POST['id']}  não encontrado ");
          
          $Tarefa->to_object($_POST);
          $new_data = $Tarefa->delete($_POST['id']);
          $message = "Tarefa {$tarefa[0]['id']} - {$tarefa[0]['name']} excluído com sucesso";
          $return = [
            "type" => "ok",
            "message" => $message,
            "popup" => ["type" => "success", "title" => "Tudo certo!", "text" => $message ],
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