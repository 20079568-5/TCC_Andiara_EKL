<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="/assets/plugins/datatables/datatables.min.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/plugins/bootstrap5/css/bootstrap.min.css"/>
    <style>
      .divider:after,
      .divider:before {
        content: "";
        flex: 1;
        height: 1px;
        background: #eee;
      }
      .h-custom {
        height: calc(100% - 73px);
      }
      .hide {
        display: none;
      }
      @media (max-width: 450px) {
        .h-custom {
          height: 100%;
        }
      }

      .form-control {
        border-color: #2c5f6e !important;
      }

      .verde-andiara {
        color: #2c5f6e !important;
      }
    </style>
  </head>
  <body style="">
    <section class="vh-100">
      <div class="container-fluid h-custom">
        <div class="row d-flex justify-content-center align-items-center h-100">
          <div class="col-md-9 col-lg-6 col-xl-5 text-center">
            <img src="/assets/img/logo.png" class="img-fluid" alt="logo" width="400" ><hr class="verde-andiara">
          </div>
          <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
            <form method="post" >
              <div class="form-outline mb-4 hide">
                                
                <div class="alert alert-danger login_text" role="alert">
                  
                </div>

              </div>
              <div class="form-outline mb-4">
                <input type="text" id="login" name="login" class="form-control form-control-lg" placeholder="Digite seu Login" />
                <input type="hidden" id="action" name="action" class="form-control form-control-lg" value="login" />
                <input type="hidden" id="api" name="api" class="form-control form-control-lg" value="auth" />
                <label class="form-label" for="login">Login</label>
              </div>

              <div class="form-outline mb-3">
                <input type="password" id="password" name="password" class="form-control form-control-lg" placeholder="Digite sua Senha" />
                <label class="form-label" for="password">Senha</label>
              </div>

              <div class="text-center text-lg-start mt-4 pt-2">
                <button type="submit" class="btn btn-secondary btn-lg" style="padding-left: 2.5rem; padding-right: 2.5rem; color: white; background-color: #2c5f6e !important;">Entrar</button>
              </div>

            </form>
          </div>
        </div>
      </div>
        
    </section>
  </body>
  <footer>
    <script type="text/javascript" src="/assets/plugins/bootstrap5/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="/assets/plugins/datatables/datatables.min.js"></script>
    <script>

      $("form").on("submit", function(e){
        $(".hide").hide();
        e.preventDefault();
        
        let parameters = $(this).serialize();
        $.post("/api/", parameters, function(data){
          $(".login_text").html('Login realizado com sucesso!');
          $(".login_text").removeClass("alert-danger").addClass("alert-success");
          $(".hide").show();
          window.location.href = "/contrato";
          
        }).fail(function(data){
          let response = data.responseJSON;
          $(".login_text").removeClass("alert-success").addClass("alert-danger");
          $(".login_text").html(response.error_message);
          $(".hide").show();
        });

      });

    </script>
  </footer>
</html>