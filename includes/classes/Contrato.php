<?php

  Class Contrato {

    private $id;
    private $numero;
    private $status;
    private $dt_inicio;
    private $dt_termino;
    private $dt_encerramento;
    private $dt_aditamento;
    private $orcamento_inicial;
    private $saldo_orcamento;
    private $nome_empresa;
  
    private $table_name = "contrato";

    public function get_id(){ return $this->id; }
    public function set_id($id): self { $this->id = $id; return $this; }

    public function get_numero(){ return $this->numero; }
    public function set_numero($numero): self { $this->numero = $numero; return $this; }

    public function get_status(){ return $this->status; }
    public function set_status($status): self { $this->status = $status; return $this; }

    public function get_dt_inicio(){ return $this->dt_inicio; }
    public function set_dt_inicio($dt_inicio): self { $this->dt_inicio = $dt_inicio; return $this; }

    public function get_dt_termino(){ return $this->dt_termino; }
    public function set_dt_termino($dt_termino): self { $this->dt_termino = $dt_termino; return $this; }

    public function get_dt_encerramento(){ return $this->dt_encerramento; }
    public function set_dt_encerramento($dt_encerramento): self { $this->dt_encerramento = $dt_encerramento; return $this; }

    public function get_dt_aditamento(){ return $this->dt_aditamento; }
    public function set_dt_aditamento($dt_aditamento): self { $this->dt_aditamento = $dt_aditamento; return $this; }

    public function get_saldo_orcamento(){ return $this->saldo_orcamento; }
    public function set_saldo_orcamento($saldo_orcamento): self { $this->saldo_orcamento = $saldo_orcamento; return $this; }

    public function get_nome_empresa(){ return $this->nome_empresa; }
    public function set_nome_empresa($nome_empresa): self { $this->nome_empresa = $nome_empresa; return $this; }

    public function get_table_name(){ return $this->table_name; }

    public function to_object($array){
      $fields = array_keys(get_object_vars($this));
      foreach ( $array as $key => $data ){
        if ( in_array($key, $fields) ){          
          $this->$key = $data;
        }
        
      }

      return $this;
    }

    public function to_array(){            
      return get_object_vars($this);    
    }

    public static function get_class_vars($object){
      return array_keys(get_class_vars(get_class($object))); // $object
    }

    public function create(){            
      $Crud = new Crud();
      $new_data = $this->to_array();
      unset($new_data['create_date']);
      unset($new_data['id']);
      return $Crud->create($table = $this->get_table_name(), $new_data );
    }

    public function edit(){
      $Crud = new Crud(); 
      $new_data = $this->to_array();
      $id = $this->get_id();
      unset($new_data['id']);

      $data = $this->list(["id" => $id, "limit" => 1]);
      
      if ( !$data )
        throw new NotFoundException("Contrato id: {$id} não encontrado, impossível editar o registro");

      return $Crud->update($table = $this->get_table_name(), $new_data, $where = "id = :id", $bind = [ ":id" => $id ]);
    }

    public function delete($id){
      $data = $this->list(["id" => $id, "limit" => 1]);
    
      if ( !$data )
        throw new NotFoundException("Contrato id: {$id} não encontrado, impossível excluir o registro");

      $Crud = new Crud();
      return $Crud->delete($table = $this->get_table_name(), $where = "id = :id", $bind = [ ":id" => $id ]);
    }

    private function create_sql_filter($filters = []){
      $where = "";
      $bind = [];
      $class_vars = self::get_class_vars($this);
      if ( $filters ){
        foreach ( $filters as $key => $filter ){
          if ( in_array($key, $class_vars) ){ // create where based on class attributes 
            $where .= "\r\n AND {$this->get_table_name()}.{$key} = :{$key} ";
            $bind[":{$key}"] = $filter;
          }else {
            switch ( $key ){
              case "order":
                $where .= "ORDER BY :{$key}";
                $bind[":{$key}"] = $filter;
                break;

              case "limit":
                $where .= "\r\n LIMIT :{$key}";
                $bind[":{$key}"] = $filter;
                break;
            }
          }
        }
      }

      return [ "where" => $where, "bind" => $bind ];
    }

    public function list($filters = []){
      $Crud = new Crud();

      $where = ( !empty($filters) ?  " \r\n 1 = 1 " : "" );
      $field_list = "*";
      if ( !isset($filters['return_all_fields']) || $filters['return_all_fields'] != true ){ 
        if ( !isset($filters['ignore_fields']) || empty($filters['ignore_fields']) ){ // remove password and token
          $ignore_fields = ["password", "api_token"];
        }else {
          $ignore_fields = $filters['ignore_fields'];
        }
        $field_list = $Crud->get_fields(["table" => $this->get_table_name(), "ignore_fields" => $ignore_fields, "return_query_field_list" => true ] );
      }

      $bind = [];
      if ( $filters ){
        $sql_filter = self::create_sql_filter($filters);
        $where .= $sql_filter["where"];
        $bind = $sql_filter["bind"];
      }
      return $Crud->read($table = $this->get_table_name(), $where, $bind, $fields = $field_list);
    }

    public static function get_list_options(){
      $Contrato = new Contrato();
      $list = $Contrato->list(["ORDER" => "name asc"]);

      $return = [];
      foreach ( $list as $data ){
        $return[$data['id']] = $data['name'];
      }
      
      return $return;
    }

    public function to_datatable($list){
      $return = [];
      $columns = ["Número", "Empresa", "Status", "Início", "Término", "Encerrado em", "Aditamento", "Orçamento", "Saldo", "Ações"];
      $items = [];
      //Helper::debug_data($list);
      foreach ( $list as $key => $data ){
        $buttons = ["data" => $data, "action" => ["edit", "view_tarefas", "view_recursos", "view_resumo", "delete"],];
         
        $temp = [    
          [
            "text" => $data['numero'], "classes" => ["text-center"],
            "attributes" => [ "order" => $data['numero'], "raw" => $data['numero']],
          ], 
          [
            "text" => $data['nome_empresa'], "classes" => ["text-left"],
            "attributes" => [ "order" => $data['nome_empresa'], "raw" => $data['nome_empresa']],
          ], 
          [
            "text" => $data['status'], "classes" => ["text-center"],
            "attributes" => [ "order" => $data['status'], "raw" => $data['status'],]
          ],
          [
            "text" => $data['dt_inicio'], "classes" => ["text-center"],
            "attributes" => [ "order" => $data['dt_inicio'], "raw" => $data['dt_inicio'],],
            "format" => "date_br",
          ],
          [
            "text" => $data['dt_termino'], "classes" => ["text-center"],
            "attributes" => [ "order" => $data['dt_termino'], "raw" => $data['dt_termino'],],
            "format" => "date_br",
          ],
          [
            "text" => $data['dt_encerramento'], "classes" => ["text-center"],
            "attributes" => [ "order" => $data['dt_encerramento'], "raw" => $data['dt_encerramento'],],
            "format" => "date_br",
          ],
          [
            "text" => $data['dt_aditamento'], "classes" => ["text-center"],
            "attributes" => [ "order" => $data['dt_aditamento'], "raw" => $data['dt_aditamento'],],
            "format" => "date_br",
          ],
          [
            "text" => $data['orcamento_inicial'], "classes" => ["text-end"],
            "attributes" => [ "order" => $data['orcamento_inicial'], "raw" => $data['orcamento_inicial'],],
            "format" => "number_br",
          ],
          [
            "text" => $data['saldo_orcamento'], "classes" => ["text-end"],
            "attributes" => [ "order" => $data['saldo_orcamento'], "raw" => $data['saldo_orcamento'],],
            "format" => "number_br",
          ],
          /*
          */
          [
            "text" =>  $this->create_button($buttons), "classes" => ["text-center"],
            "attributes" => ["order" => '', "raw" => '',],
          ]
        ];
        $items[] = $temp;
      }

      return [ "columns" => $columns, "items" => $items ];
    }

    public static function get_list_options_ativo(){
      $list = [
        [ "id" => "Ativo", "name" => "Ativo" ],
        [ "id" => "Inativo", "name" => "Inativo" ],
      ];
      $return = [];
      foreach ( $list as $data ){
        $return[$data['id']] = $data['name'];
      }
      
      return $return;
    }
    public static function get_form($mode = "create"){
      $form = [
        "id" => "form_contrato",
        "fields" => [
          ['id' => 'id', 'type' => 'hidden', 'required' => true],
          ['id' => 'nome_empresa',  'label' => 'Nome da Empresa', 'required' => true,
            'type' => 'text', 'attributes' => ['minlength' => 1, 'maxlength' => 200, "placeholder" => "Informe o Nome da Empresa"]],  

          ['id' => 'numero',  'label' => 'Número', 'required' => true,
            'type' => 'text', 'attributes' => ['minlength' => 1, 'maxlength' => 200, "placeholder" => "Informe o Número/Código do contrato"]],  


          ['id' => 'dt_inicio',  'label' => 'Início', 'required' => true,
            'type' => 'date', 'attributes' => ['minlength' => 1, 'maxlength' => 10, "placeholder" => "Informe a data de início do contrato"]],  
          
          ['id' => 'dt_termino',  'label' => 'Término', 'required' => true,
            'type' => 'date', 'attributes' => ['minlength' => 1, 'maxlength' => 10, "placeholder" => "Informe a data de término do contrato"]],  
          
          ['id' => 'dt_aditamento',  'label' => 'Aditamento',
            'type' => 'date', 'attributes' => ['minlength' => 1, 'maxlength' => 10, "placeholder" => "Informe a data de aditamento do contrato"]],      
          
          ['id' => 'dt_encerramento',  'label' => 'Encerramento',
            'type' => 'date', 'attributes' => ['minlength' => 1, 'maxlength' => 10, "placeholder" => "Informe a data de encerramento do contrato"]],         
                
          ['id' => 'status','label' => 'Status', 'required' => true,
            'type' => 'select', 'attributes' => ['emptyval' => 'Selecione...',
            ], 
            'options' => self::get_list_options_ativo()
          ],        
          
          ['id' => 'orcamento_inicial',  'label' => 'Orçamento Inicial', 'classes' => ['text-end'],
            'type' => 'number', 'attributes' => ['minlength' => 1, 'step' => ".01", "placeholder" => "Informe a valor de orçamento inicial"]],         
          
          ['id' => 'saldo_orcamento',  'label' => 'Saldo Disponível', 'classes' => ['text-end'],
            'type' => 'number', 'attributes' => ['minlength' => 1, 'step' => ".01", "placeholder" => "Informe a valor de orçamento disponível"]],         
        ]
      ];

      switch ( $mode ){
        case "edit":
          $form['fields'][] = [
            "id" => "id", "name" => "id", "type" => "text", "label" => "Id",
            "required" => true ,
            "classes" => [ "form-control", "input-sm", "text-left", "limpar", "form-control-sm", ],
            "attributes" => ["min" => 1 ]
          ];
          break;
      }

      return $form;
    }

    public static function create_checkbox($id, $value, $label){
      $html = "<div class=\"form-check\">
                <input class=\"form-check-input\" type=\"checkbox\" value=\"{$value}\" id=\"{$id}\" name=\"{$id}\" >
                <label class=\"form-check-label\" for=\"{$id}\">
                  $label
                </label>
              </div>";

      return $html;
    }
    public static function get_form_tarefas(){
      $Tarefa = new Tarefa();
      $list = $Tarefa->list();

      $checkboxes = "";
      foreach ( $list as $key => $dados ){
        $checkboxes .= self::create_checkbox("tarefas[][{$dados['id']}]", $dados['id'], $dados['nome']);
      }

      return $checkboxes;
    }

    public static function get_form_recursos(){
      $Recurso = new Recurso();
      $list = $Recurso->list();

      $checkboxes = "";
      foreach ( $list as $key => $dados ){
        $checkboxes .= self::create_checkbox("recursos[][{$dados['id']}]", $dados['id'], $dados['nome'] . " - {$dados['tipo']}");
      }

      return $checkboxes;
    }

    public function create_button($parameters = []){
      
      $buttons = [];
      foreach ( $parameters['action'] as $key => $action ){
        switch ( $action ){
          case  "edit":
            $buttons[] = [ 
              "class" => array("api", "dropdown-item", "btn_edit", "text-dark"), 
              "description" => Helper::get_icon(["icon" => "edit" ]) . " Editar ", 
              "type" => "link",
              "attributes" => [
                "data-object" => get_class($this),
                "data-action" => $action,
                "data-id" => $parameters['data']['id'],
                //"data-modal_description" => "Editar Processo: {$parameters['data']['id']} - {$parameters['data']['name']}",                
                "data-bs-toggle" => "modal",
                "data-bs-target" => "#modal_form_contrato",      
                "data-form_id" => "contrato",                                
              ],
              "href" => "#",
              //"required_permission" => "contrato_edit",
            ];
            break;

          case  "delete":
            $buttons[] = [ 
              "class" => array("api", "dropdown-item", "btn_delete", "text-danger"), 
              "description" => Helper::get_icon(["icon" => "delete" ]) . " Excluir ", 
              "type" => "link",
              "attributes" => [
                "data-object" => get_class($this),
                "data-action" => $action,
                "data-id" => $parameters['data']['id'],
                "data-object_info" => json_encode($parameters['data'], true),
               // "data-modal_description" => "Excluir Usuário: {$parameters['data']['id']} - {$parameters['data']['name']}",                
                //"data-bs-toggle" => "modal",
                //"data-bs-target" => "#modal_form_contrato",                                      
              ],
              "href" => "#",
              //"required_permission" => "contrato_delete",
            ];
            break;

          case  "view_tarefas":
            $buttons[] = [ 
              "class" => array("api", "dropdown-item", "btn_detalhes",), 
              "description" => Helper::get_icon(["icon" => "check" ]) . " Tarefas ", 
              "type" => "link",              
              "attributes" => [
                "data-object" => get_class($this),
                "data-action" => $action,
                "data-id" => $parameters['data']['id'],
                "data-object_info" => json_encode($parameters['data'], true),
               // "data-modal_description" => "Excluir Usuário: {$parameters['data']['id']} - {$parameters['data']['name']}",                
                //"data-bs-toggle" => "modal",
                //"data-bs-target" => "#modal_form_contrato",                                      
              ],
              "href" => "/tarefa_contrato?id_contrato={$parameters['data']['id']}",
              //"required_permission" => "contrato_delete",
            ];
            break;

          case  "view_recursos":
            $buttons[] = [ 
              "class" => array("api", "dropdown-item", "btn_detalhes",), 
              "description" => Helper::get_icon(["icon" => "tool" ]) . " Recursos ", 
              "type" => "link",              
              "attributes" => [
                "data-object" => get_class($this),
                "data-action" => $action,
                "data-id" => $parameters['data']['id'],
                "data-object_info" => json_encode($parameters['data'], true),
               // "data-modal_description" => "Excluir Usuário: {$parameters['data']['id']} - {$parameters['data']['name']}",                
                //"data-bs-toggle" => "modal",
                //"data-bs-target" => "#modal_form_contrato",                                      
              ],
              "href" => "/recurso_contrato?id_contrato={$parameters['data']['id']}",
              //"required_permission" => "contrato_delete",
            ];
            break;

          case  "view_resumo":
            $buttons[] = [ 
              "class" => array("api", "dropdown-item", "btn_resumo", "text-dark"), 
              "description" => Helper::get_icon(["icon" => "edit" ]) . " Resumo/Detalhes ", 
              "type" => "link",
              "attributes" => [
                "data-object" => get_class($this),
                "data-action" => $action,
                "data-id" => $parameters['data']['id'],
                //"data-modal_description" => "Editar Processo: {$parameters['data']['id']} - {$parameters['data']['name']}",                
                "data-bs-toggle" => "modal",
                "data-bs-target" => "#modal_resumo",      
                "data-form_id" => "contrato",                                
              ],
              "href" => "#",
              //"required_permission" => "contrato_edit",
            ];
            break;
        }
      }

      if ( !$buttons )
        return "";
      
      return Helper::create_html_button_dropdown($buttons);

    }


  }