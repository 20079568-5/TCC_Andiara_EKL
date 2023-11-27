<?php // Contrato Api file

  $Contrato = new Contrato();
  switch ( $_SERVER['REQUEST_METHOD'] ){
    case "GET":
      switch ( $_REQUEST['action'] ){
        case "get_form":
          $mode = ( isset($_REQUEST['mode']) ? $_REQUEST['mode'] : "create" );
          ApiHelper::json_return(Contrato::get_form($mode));
          break;

        case "get_form_tarefas":
          $mode = ( isset($_REQUEST['mode']) ? $_REQUEST['mode'] : "create" );       
          $tarefas = Contrato::get_form_tarefas($mode);
          $tarefas = "<div class='col tarefas'>
                        <div class='row'>
                         <div class='col'>
                          <h4>Tarefas:</h4> <hr>
                          </div>
                        </div>
                        <div class='row'>
                          <div class='col'>
                            {$tarefas}
                          </div>
                        </div>
                      </div>";
          ApiHelper::json_return(["tarefas" => $tarefas]);
          break;

        case "get_form_recursos":
          $mode = ( isset($_REQUEST['mode']) ? $_REQUEST['mode'] : "create" );       
          $recursos = Contrato::get_form_recursos($mode);
          $recursos = "<div class='col recursos'>
                        <div class='row'>
                          <div class='col'>
                          <h4>Recursos:</h4> <hr>
                          </div>
                        </div>
                        <div class='row'>
                          <div class='col'>
                            {$recursos}
                          </div>
                        </div>
                      </div>";
          ApiHelper::json_return(["recursos" => $recursos]);
          break;

        case "list":
          if ( !isset($_REQUEST["limit"]) || empty($_REQUEST["limit"]) )
            $_REQUEST["limit"] = 1000;

          ApiHelper::json_return($Contrato->list($_REQUEST));          
          break;
        
        case "list_datatable":
          $_REQUEST['ignore_fields'] = ["create_date", "password", "api_token"];
          if ( !isset($_REQUEST["limit"]) || empty($_REQUEST["limit"]) )
            $_REQUEST["limit"] = 1000;

          ApiHelper::json_return($Contrato->get_datatable_list($Contrato->list($_REQUEST)));          
          break;


        case "list_resumos":
          $_REQUEST['ignore_fields'] = ["create_date", "password", "api_token"];
          if ( !isset($_REQUEST["limit"]) || empty($_REQUEST["limit"]) )
            $_REQUEST["limit"] = 1000;

          $TarefasContrato = new TarefasContrato();
          $lista_tarefas = $TarefasContrato->list_details(["id_contrato" => $_REQUEST['id']]);
          
          $RecursosContrato = new RecursosContrato();
          $lista_recursos = $RecursosContrato->list_details(["id_contrato" => $_REQUEST['id']]);
          
          $contrato = $Contrato->list(['id' => $_REQUEST['id']]);          
          
          $resumo = "<h4>Contrato</h4><hr>" . DataTable::create($Contrato->to_datatable($contrato)) . "<h4>Tarefas</h4><hr>" . DataTable::create($TarefasContrato->to_datatable($lista_tarefas)) .  "<h4>Recursos</h4><hr>" . DataTable::create($RecursosContrato->to_datatable($lista_recursos));
          ApiHelper::json_return(["resumos" => $resumo]);          
          break;

        default:
          throw new ApiException("Ação inválida");
          break;
      }
      break;

    case "POST":
      switch ( $_REQUEST['action'] ){
        case "create":            
          Helper::validate_form(Contrato::get_form("create"), $_REQUEST, $bypass_required = ["id",]);
          
          $Contrato->to_object($_POST);
          $new_data = $Contrato->create();

          if ( $new_data ){
            if ( $_POST['tarefas'] ){
              $TarefasContrato = new TarefasContrato();
              foreach ( $_POST['tarefas'] as $tarefa ){
                foreach ( $tarefa as $key => $id_tarefa ){
                  $TarefasContrato->set_id_contrato($new_data);
                  $TarefasContrato->set_id_tarefa($id_tarefa);
                  $TarefasContrato->create();

                }
              }
            }
          }
          if ( $new_data ){
            if ( $_POST['recursos'] ){
              $RecursosContrato = new RecursosContrato();
              foreach ( $_POST['recursos'] as $recurso ){
                foreach ( $recurso as $key => $id_recurso ){
                  $RecursosContrato->set_id_contrato($new_data);
                  $RecursosContrato->set_id_recurso($id_recurso);
                  $RecursosContrato->create();

                }
              }
            }
          }
          

          $message = "Contrato criado com sucesso! Id: {$new_data}";
          $return = [
            "type" => "ok",
            "message" => $message,
            "id" => $new_data,
            "request" => $_REQUEST,
            "post" => $_POST,
            "popup" => ["type" => "success", "title" => "Tudo certo!", "text" => $message,]
          ];
          ApiHelper::json_return($return);
          break;

        case "edit":
          Helper::validate_form(Contrato::get_form(), $_POST, $bypass_required = []);
         
          $contrato = $Contrato->list(["id" => $_POST['id']]);
          if ( !$Contrato )
            throw new NotFoundException("Contrato id: {$_POST['id']}  não encontrada ");
          
          $Contrato->to_object($_POST);
          $new_data = $Contrato->edit();
          $message = "Contrato id: {$contrato[0]['id']} editado com sucesso";
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

          $contrato = $Contrato->list(["id" => $_POST['id']]);
          if ( !$Contrato )
            throw new NotFoundException("Contrato id: {$_POST['id']}  não encontrado ");
          
          $Contrato->to_object($_POST);
          $new_data = $Contrato->delete($_POST['id']);
          $message = "Contrato {$contrato[0]['id']} - {$contrato[0]['name']} excluído com sucesso";
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