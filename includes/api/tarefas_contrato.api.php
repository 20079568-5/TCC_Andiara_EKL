<?php // TarefasContrato Api file
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

  $TarefasContrato = new TarefasContrato();
  switch ( $_SERVER['REQUEST_METHOD'] ){
    case "GET":
      switch ( $_REQUEST['action'] ){
        case "get_form":
          $mode = ( isset($_REQUEST['mode']) ? $_REQUEST['mode'] : "create" );
          ApiHelper::json_return(TarefasContrato::get_form($mode));
          break;

        case "get_form_upload":
          $mode = ( isset($_REQUEST['mode']) ? $_REQUEST['mode'] : "create" );
          ApiHelper::json_return(TarefasContrato::get_form_upload($mode));
          break;

        case "list":
          if ( !isset($_REQUEST["limit"]) || empty($_REQUEST["limit"]) )
            $_REQUEST["limit"] = 1000;

          ApiHelper::json_return($TarefasContrato->list($_REQUEST));          
          break;
        
        case "list_datatable":
          $_REQUEST['ignore_fields'] = ["create_date", "password", "api_token"];
          if ( !isset($_REQUEST["limit"]) || empty($_REQUEST["limit"]) )
            $_REQUEST["limit"] = 1000;

          ApiHelper::json_return($TarefasContrato->get_datatable_list($TarefasContrato->list_details($_REQUEST)));          
          break;

        case "list_datatable_uploads":
          $_REQUEST['ignore_fields'] = ["create_date", "password", "api_token"];
          if ( !isset($_REQUEST["limit"]) || empty($_REQUEST["limit"]) )
            $_REQUEST["limit"] = 1000;

          $Upload = new Upload();
          ApiHelper::json_return(["lista_uploads" => DataTable::create($Upload->to_datatable($Upload->list_short($_REQUEST)))]);          
          break;

        case "abrir_upload":
          if ( !isset($_REQUEST['id']) || !isset($_REQUEST['id_tarefa_contrato']) )
            throw new Exception("Arquivo não informado, impossível abrir");

          $Upload = new Upload();
          $arquivo = $Upload->list(['id' => $_REQUEST['id'], 'id_tarefa_contrato' => $_REQUEST['id_tarefa_contrato']]);
          if ( !$arquivo )
            throw new Exception("Arquivo id: {$_REQUEST['id']} id_tarefa_contrato: {$_REQUEST['id_tarefa_contrato']}  não encontrato");

          $caminho_upload = UPLOAD_FILES_DIR . $arquivo[0]['arquivo'];
          if ( !file_exists($caminho_upload) )
            throw new Exception("Falha ao abrir o arquivo {$_REQUEST['id']} id_tarefa_contrato: {$_REQUEST['id_tarefa_contrato']}");
          break;

        default:
          throw new ApiException("Ação inválida");
          break;
      }
      break;

    case "POST":
      switch ( $_REQUEST['action'] ){
        case "create":            
          Helper::validate_form(TarefasContrato::get_form("create"), $_REQUEST, $bypass_required = ["id",]);
          
          $TarefasContrato->to_object($_POST);

          $new_data = $TarefasContrato->create();
          $message = "TarefasContrato criado com sucesso! Id: {$new_data}";
          $return = [
            "type" => "ok",
            "message" => $message,
            "id" => $new_data,
            "popup" => ["type" => "success", "title" => "Tudo certo!", "text" => $message,]
          ];
          ApiHelper::json_return($return);
          break;

        case "upload":            
          Helper::validate_form(TarefasContrato::get_form_upload("create"), $_REQUEST, $bypass_required = ['arquivo']);
          if ( !$_FILES || !$_FILES['arquivo'])
            throw new Excpetion("Falha ao realizar upload, arquivo não enviado");

          $conteudo_arquivo = file_get_contents($_FILES['arquivo']['tmp_name']);
          $nome_arquivo = date("Y-m-d-h-i-ss") ."_{$_POST['id_tarefa_contrato']}_{$_FILES['arquivo']['name']}";
        
          //move_uploaded_file($_FILES['arquivo']['tmp_name'],  UPLOAD_FILES_DIR . $caminho_upload);
          file_put_contents(UPLOAD_FILES_DIR . $nome_arquivo, $conteudo_arquivo);
          $Upload = new Upload();
          $Upload->set_nome($_FILES['arquivo']['name']);
          $Upload->set_mime_type($_FILES['arquivo']['type']);
          $Upload->set_arquivo($nome_arquivo);
          $Upload->set_id_tarefa_contrato($_POST['id_tarefa_contrato']);

          $new_data = $Upload->create();
          $message = "Upload criado com sucesso! Id: {$new_data}";
          $return = [
            "type" => "ok",
            "message" => $message,
            "id" => $new_data,
            "popup" => ["type" => "success", "title" => "Tudo certo!", "text" => $message,]
          ];
          ApiHelper::json_return($return);
          break;

        case "delete_upload":

          $Upload = new Upload();
          if ( !isset($_POST['id']) || empty($_POST['id']) )
            throw new FormException("Id não informado, impossível realizar operação");

          $upload = $Upload->list(["id" => $_POST['id']]);
          if ( !$upload )
            throw new NotFoundException("Upload id: {$_POST['id']}  não encontrado ");
          
          $new_data = $Upload->delete($_POST['id']);
          $message = "Upload {$upload[0]['id']} - {$upload[0]['name']} excluído com sucesso";
          $return = [
            "type" => "ok",
            "message" => $message,
            "popup" => ["type" => "success", "title" => "Tudo certo!", "text" => $message ],
            "id" => $_POST['id'],
          ];
          ApiHelper::json_return($return);
          break;

        case "edit":
          Helper::validate_form(TarefasContrato::get_form(), $_POST, $bypass_required = []);
         
          $tarefa = $TarefasContrato->list(["id" => $_POST['id']]);
          if ( !$TarefasContrato )
            throw new NotFoundException("TarefasContrato id: {$_POST['id']}  não encontrada ");
          
          $TarefasContrato->to_object($_POST);
          $new_data = $TarefasContrato->edit();
          $message = "TarefasContrato id: {$tarefa[0]['id']} editado com sucesso";
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

          $tarefa = $TarefasContrato->list(["id" => $_POST['id']]);
          if ( !$TarefasContrato )
            throw new NotFoundException("TarefasContrato id: {$_POST['id']}  não encontrado ");
          
          $TarefasContrato->to_object($_POST);
          $new_data = $TarefasContrato->delete($_POST['id']);
          $message = "TarefasContrato {$tarefa[0]['id']} - {$tarefa[0]['name']} excluído com sucesso";
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