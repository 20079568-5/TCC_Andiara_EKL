
        // gera as os campos d emaneira dinamica vindos da api
        function gerar_campo (campo){
        
          var campoHtml = "";
          
          // classes adicionais vindas da api
          var classes = campo.classes ? campo.classes : "";

          // valor padrão vindo da api
          var defaultValue = campo.default ? campo.default : "";

          var required = campo.required ? "required" : "";

          var labelRequired = campo.required ? "<span style=\"color: red;\" > * </span>" : "";

          var attributes = "";
          var prefix = "";
          var suffix = "";

          if ( campo.attributes ){

              $.each(campo.attributes, function(key, value){

                  switch ( key ){

                      case "suffix":
                          suffix = " <small>" + value + "</small>";
                          break;

                      case "prefix":
                          prefix =  " <small>" + value + "</small>";
                          break;

                      case "emptyval":
                          break;
                      
                      default: 
                          attributes += " " + key + "=\"" + value + "\" "; 
                          break;
                  }
                  
              });

          }else{

              campo.attributes = [];

          }
          
          // hidden como exceção pois não vai label, nem linha dedicada no formulário
          switch ( campo.type ){

              // tratamento para tipo hidden, sem colunas, sem label
              case "hidden":
                  campoHtml += "<input type=\"" + campo.type + "\" id=\"" + campo.id + "\" name=\"" + campo.id + "\" class=\"form-control input-sm " + classes + "\" " + attributes + required + " /> " + suffix;
                  break;

              // tratamento para campos tipo option com botões toggle estilizados
              case "radio":

                  campoHtml += "<div class=\"form-group row\">";

                      // descrição do campo
                      campoHtml += "<label class=\"col-sm-4 col-form-label\" for=\"" + campo.id + "\">" + campo.label + " " + labelRequired + " </label> ";  

                          // coluna do campo
                          campoHtml += "<div class=\"col-sm-8\">";
                          
                          // parametros dos botões toggle do JS/bootstrap
                          campoHtml += "<div class=\"btn-group btn-group-toggle\" data-toggle=\"buttons\">";

                              // gera a lista de opções
                              $(campo.options).each(function(k, v){
                                          
                                  $.each(v, function(value, text){
                                  
                                      var selected = "";
                                      var active = "";
                                      
                                      // se vier algum valor setado como padrão, marcar como selecionado                            
                                      if ( value == defaultValue ){
                                          
                                          selected = " checked";
                                          active = "active";
                                          
                                      }
                                  
                                      campoHtml += "<label class=\"btn btn-outline-preto " + active + "\">";
                                          campoHtml += "<input type=\"radio\" name=\"" + campo.id + "\" id=\"" + campo.id + "\" value=\"" + value + "\" " + required + attributes + selected + ">" + text;
                                      campoHtml += "</label>";

                                  })
                                                      
                              });
                          
                          campoHtml += "</div>";
                          
                      campoHtml += "</div>";

                  campoHtml += "</div>";
                  break;

              case "checkbox-slot":
                  
                  //linha do campo padrão sempre
                  campoHtml += "<div class=\"form-group row\">";

                      // descrição do campo
                      campoHtml += "<label class=\"col-sm-4 col-form-label\" for=\"" + campo.id + "\">" + campo.label + " " + labelRequired + " </label> ";  
                  
                      // coluna do campo
                      campoHtml += "<div class=\"col-sm-8\">";

                          // div do input group
                          campoHtml += '<div class="input-group mb-3">';

                          campoHtml += '<div class="rm-box">';
                              //campoHtml += '<div class="border-slot"><span></span><span></span></div>';
                                  campoHtml += '<div class="slot-box">';
          
                                  // gera a lista de opções
                                  $(campo.options).each(function(k, v){
                                      
                                      $.each(v, function(value, text){
                                      
                                          var disabled = " ";
                                      
                                          // desabilita o slot (não disponível de acordo com a situação atual do equipamento)                                           
                                          $.each(campo.options_disabled, function(chave, valor){

                                              if ( chave == value ){

                                                  if ( valor == true ){

                                                      disabled = " disabled ";

                                                  }
                                                  
                                              }

                                          })                                         

                                          campoHtml += '<div class="slot-item">';

                                              campoHtml += "<input type=\"checkbox\" id=\"" + campo.id + "[" + value + "]\" name=\"" + campo.id + "[" + value + "]\" class=\"check-slot\" " + disabled + ">";
                                              campoHtml += "<label class=\"check-slot-label \" for=\"" + campo.id + "[" + value + "]\">" + text + "</label>";

                                          campoHtml += '</div>';

                                      })

                                  });
              
                                  campoHtml += '</div>';
              
                                  campoHtml += '</div>';
                              campoHtml += '</div>';
                          // fim do input group
                          campoHtml += "</div>";

                      // fim da coluna do campo
                      campoHtml += "</div>";

                  // fim da linha 
                  campoHtml += "</div>";
                  
                  break;


              // falta implementar
              case "checkbox":
                  break;

              // demais tipos normais com suporte a prefixo, sufixo e etc
              default:
                  //linha do campo padrão sempre
                  campoHtml += "<div class=\"form-group row\">";

                      // descrição do campo
                      campoHtml += "<label class=\"col-sm-4 col-form-label\" for=\"" + campo.id + "\">" + campo.label + " " + labelRequired + " </label> ";  
                      
                      // coluna do campo
                      campoHtml += "<div class=\"col-sm-8\">";

                          // div do input group
                          campoHtml += '<div class="input-group mb-3">';

                          // adiciona os elementos para prefixo
                          campo.attributes.prefix ? campoHtml += '<div class="input-group-prepend"><span class="input-group-text" id="basic-addon2">' + prefix + '</span></div>' : "";

                          // gera os campos de acordo com o tipo e regras específicas
                          switch ( campo.type ){
                              
                              case "select":
                              
                                  campoHtml += "<select id=\"" + campo.id + "\" name=\"" + campo.id + "\" class=\"form-control input-sm " + classes + "\" " + required + attributes + "  >";
                              
                                  if ( campo.attributes ){
                                      
                                      // define se o campo possui opção em branco 
                                      if ( campo.attributes.emptyval ){
                                          
                                          campoHtml += "<option value=\"\">" + campo.attributes.emptyval + "</option>"; 
                                          
                                      }
                                      
                                  }
                              
                                  // gera a lista de opções
                                  $(campo.options).each(function(k, v){
                                      
                                      $.each(v, function(value, text){
                                      
                                          var selected = "";
                                          
                                          // se vier algum valor setado como padrão, marcar como selecionado                            
                                          if ( value == defaultValue ){
                                              
                                              selected = "selected";
                                              
                                          }
                                                                  
                                          campoHtml += "<option value=\"" + value + "\" " + selected + " >" + text + "</option>";                        

                                      })
                                                          
                                  });
                              
                                  campoHtml += "</select>";
                                  break;

                              case "textarea":
                                  campoHtml += "<textarea id=\"" + campo.id + "\" name=\"" + campo.id + "\" class=\"form-control input-sm " + classes + "\" " + attributes + required + " >" + defaultValue + "</textarea>" ;
                                  break;

                              // demais campos, tipos comuns
                              default:
                                  campoHtml += "<input type=\"" + campo.type + "\" id=\"" + campo.id + "\" name=\"" + campo.id + "\" class=\"form-control input-sm " + classes + "\" " + attributes + required +" value=\"" + defaultValue + "\" /> " ;
                                  break;

                          }  

                              campo.attributes.suffix ? campoHtml += '<div class="input-group-append"><span class="input-group-text" id="basic-addon2">' + suffix + '</span></div>' : "";

                          // fim do input group
                          campoHtml += "</div>";


                      // fim da coluna do campo
                      campoHtml += "</div>";

                  // fim da linha 
                  campoHtml += "</div>";
                  break;
          }
          
          return campoHtml;
          
      }