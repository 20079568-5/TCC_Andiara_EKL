<?php

  ApiHelper::set_headers();
  ApiHelper::null_empty_fields();
  try {
    if ( !isset($_REQUEST['api']) || empty($_REQUEST['api']) )
      throw new ApiException("Api não informada");

    if ( !isset($_REQUEST['action']) || empty($_REQUEST['action']) )
      throw new ApiException("Ação não informada");

    $api_list = ["user", "auth", "contrato", "recurso", "tarefa", "tarefas_contrato", "recursos_contrato"];

    if ( !in_array($_REQUEST['api'], $api_list) )
      throw new ApiException("Api inválida");

    ApiHelper::is_allowed($_REQUEST['api'], $_REQUEST['action'], @$_REQUEST['auth_token']);

    include_once("{$_REQUEST['api']}.api.php");

  } catch ( AuthException $error ){
    ApiHelper::create_error_return($error, @$_REQUEST['api'], 401); // unauthorized

  }catch ( PermissionException $error ){
    ApiHelper::create_error_return($error, @$_REQUEST['api'], 403); // forbidden

  }catch ( FormException $error ){
    ApiHelper::create_error_return($error, @$_REQUEST['api'], 400); // bad request / invalid data

  }catch ( Exception $error ){
    ApiHelper::create_error_return($error, @$_REQUEST['api'], 500); // interal or generic error

  }
