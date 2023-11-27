<?php
  if ( !Auth::get_auth_info() )
    header("Location: /login");
  ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.108.0">
    <title>Contratos</title>
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" type="text/css" href="/assets/plugins/datatables/datatables.min.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/plugins/bootstrap5/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/plugins/sweetalert2/sweetalert2.min.css"/>
    <meta name="theme-color" content="#712cf9">
    <style>
      .modal-lg, .modal, .modal-dialog{
      min-width: 90% !important;
      max-width: 90% !important;
      }
    </style>
    <!-- Custom styles for this template -->
    <link href="/assets/css/dashboard.css" rel="stylesheet">
  </head>
  <body style="background-color: #eff3f9;">
    <header class="navbar navbar-dark sticky-top bg-secondary flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6" href="#">TCC Andiara - Gestão de Contratos </a>
      <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
      </button>
    </header>
    <div class="container-fluid">
      <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse" style="background-color: #eff3f9;">
          <div class="position-sticky pt-3 sidebar-sticky">
            <ul class="nav flex-column">
              <li class="nav-item">
                <a class="nav-link " aria-current="page" href="/recurso/">
                <span data-feather="shield" class="align-text-bottom"></span>
                Recursos
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" href="/contrato/">
                <span data-feather="layers" class="align-text-bottom"></span>
                Contratos
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link " href="/tarefa/">
                <span data-feather="check" class="align-text-bottom"></span>
                Tarefas
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link " href="/tarefa_contrato/">
                <span data-feather="list" class="align-text-bottom"></span>
                Tarefas x Contratos
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/recurso_contrato/">
                <span data-feather="tool" class="align-text-bottom"></span>
                Recursos x Contratos
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/logout/">
                <span data-feather="log-out" class="align-text-bottom"></span>
                Sair
                </a>
              </li>
            </ul>
          </div>
        </nav>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
          <div class="row">
            <div class="col-12">
              <h2>
                Lista de Contratos
                <a href="#" class="btn btn-sm bg-secondary text-white" id="btn_create" >Adicionar</a>
              </h2>
              <div class="">
                <?php
                  $Contrato = new Contrato();
                  $list = $Contrato->list();
                  //Helper::debug_data($list);
                  //$list = $Body->get_datatable_list($list);
                  //echo DataTable::array_to_table($list);
                  $parameters = $Contrato->to_datatable($list);
                  //Helper::debug_data($parameters);
                  echo DataTable::create($parameters);
                ?>
              </div>

              <div class="modal fade modal-lg" id="modal_form_contrato" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Cadastro de Contrato</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <form id="contrato" name="contrato" class="form form-group form-sm" >
                        
                      </form>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                      <button type="button" class="btn btn-primary btn_submit_form" id="btn_submit_form" data-form_id="contrato">Confirmar</button>
                    </div>
                  </div>
                </div>
              </div>
              
              <br>
              <br>
              <hr>
              <div class="modal fade modal-lg" id="modal_resumo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Cadastro de Contrato</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <div class="resumos">
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
    <script type="text/javascript" src="/assets/plugins/bootstrap5/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="/assets/plugins/datatables/datatables.min.js"></script>
    <script type="text/javascript" src="/assets/plugins/sweetalert2/sweetalert2.min.js"></script>
    <script type="text/javascript" src="/assets/js/controller/html.js"></script>
    <script type="text/javascript" src="/assets/js/app.js"></script>
    <!-- Icons 
    <script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
    -->
    <script src="/assets/plugins/feather/feather.js"></script>
    <script>
      feather.replace()
    </script>
    <script>
      $(document).ready(function(){
      
        // buscar o formulário
        $.get("/api/?api=contrato&action=get_form", function(data){
          console.log(data);
          if ( data.fields ){
            $.each(data.fields, function(k,v){
              $("#contrato").append(gerar_campo(v))
            })

             // buscar as tarefas
            $.get("/api/?api=contrato&action=get_form_tarefas", function(data){
              console.log(data);
              if ( data.tarefas ){
                $("#contrato").append(data.tarefas)
              }
            });
            
            // buscar as recursos
            $.get("/api/?api=contrato&action=get_form_recursos", function(data){
              console.log(data);
              if ( data.recursos ){
                $("#contrato").append(data.recursos)
              }
            });
          }
        });
        
       

        $("#btn_create").on("click", function(){
          let form = $("form#contrato");
          $(form).trigger("reset");
          $(form).data("action", "create");
          $(form).closest(".modal").modal("show");
          $(form).closest(".modal").find("#btn_submit_form").data("action", "create");

          $(".recursos").show();
          $(".tarefas").show();
        });
      
        $(".btn_edit").on("click", function(){
          let id = $(this).data("id");
          let form_id = $(this).data("form_id");
          let form = $(`form#${form_id}`);
          let action = $(this).data("action");
          console.log(id);  
          $(".recursos").hide();
          $(".tarefas").hide();
          $.get(`/api/?api=contrato&action=list&id=${id}`, function(data){
            console.warn(data);
            if ( data[0].id ){              
              $(form).trigger("reset");
              $(form).data(`action`, action);
      
              let details = data[0];
              console.warn(details);
              console.table(details);
              $(form).find(`:input[name='id']`).val(id);
              $.each(details, function(k,v){
                if ( k != "id" ){ // prevent old data to form
                  $(form).find(`:input[name='${k}']`).val(v);
                }
              })
              $(form).closest(".modal").modal("show");
              $(form).closest(".modal").find("#btn_submit_form").data("action", "edit");
            }
          })
        });


      
        $(".btn_resumo").on("click", function(){
          let id = $(this).data("id");
          let form_id = $(this).data("form_id");
          let form = $(`form#${form_id}`);
          let action = $(this).data("action");
          console.log(id);  
          $(".recursos").hide();
          $(".tarefas").hide();
          $.get(`/api/?api=contrato&action=list_resumos&id=${id}`, function(data){
            console.warn(data);
            if ( data.resumos ){              
              $(".resumos").html(data.resumos);
              $(".resumos").find("table").each(function(){
                $(this).find('tr').find('th:last, td:last').remove(); //remover a coluna de ações
              })
              $("#modal_resumo").modal("show");
            }
          })
        });
      
        $(document).on("submit", "form", function(e){
          e.preventDefault();
          let form_name = $(this).attr("name");
          let action = $(this).data("action");
          submit_form(form_name, action);
        });

        function submit_form(form_name, action){
          let formdata = new FormData($(`form[name='${form_name}']`)[0]);
          formdata.append(`action`, action);
          formdata.append(`api`, `contrato`);
          console.log(formdata);
          var link = "/api/";
          $.ajax({
            type: 'POST',
            url: link,
            data: formdata ,
            processData: false,
            contentType: false
      
          }).fail(function(data){
            let response = (data.responseJSON);
            console.log(data.responseJSON);
            popup("error", response.popup);
          }).done(function (data) {
            console.log(data);
            data.popup.reload = true;
            popup("success", data.popup);
            
          });
        }
        
        $(document).on("click", ".btn_submit_form", function(){
          console.log(this);
          let form_id = $(this).data("form_id");
          let form = $(`form#${form_id}`);
          $(form).trigger("submit");
        })
            
        $(document).on("click", ".btn_delete", function(){
          let object_info = $(this).data("object_info");
          let text = `O cadastro de ${object_info.name} será excluído e não poderá ser recuperado`
      
          Swal.fire({
            title: 'Excluir o cadastro?',
            text: text,
            //showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Excluir',         
            cancelButtonText: `Cancelar`,
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
              let formdata = new FormData();
              formdata.append(`action`, `delete`);
              formdata.append(`api`, `contrato`);
              formdata.append(`id`, object_info.id)
              var link = "/api/";
              $.ajax({
                type: 'POST',
                url: link,
                data: formdata ,
                processData: false,
                contentType: false
      
              }).fail(function(data){
                let response = (data.responseJSON);
                console.log(data.responseJSON);
                popup("error", response.popup);
              }).done(function (data) {
                console.log(data);
                data.popup.reload = true;
                popup("success", data.popup);
              });
            } 
          })
        })
      
        $("table").DataTable(
          {
            language: {
              url: '/assets/plugins/datatables/lang_pt_br.json',
            },
          }
        );
      });
      
    </script>
  </body>
</html>
