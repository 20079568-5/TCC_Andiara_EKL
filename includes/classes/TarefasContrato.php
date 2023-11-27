<?php

  Class TarefasContrato {

    private $id;
    private $id_contrato;
    private $id_tarefa;
    private $validade_meses;
  
    private $table_name = "tarefas_contrato";

    public function get_id(){ return $this->id; }
    public function set_id($id): self { $this->id = $id; return $this; }

    public function get_id_contrato(){ return $this->id_contrato; }
    public function set_id_contrato($id_contrato): self { $this->id_contrato = $id_contrato; return $this; }

    public function get_id_tarefa(){ return $this->id_tarefa; }
    public function set_id_tarefa($id_tarefa): self { $this->id_tarefa = $id_tarefa; return $this; }

    public function get_validade_meses(){ return $this->validade_meses; }
    public function set_validade_meses($validade_meses): self { $this->validade_meses = $validade_meses; return $this; }

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
        throw new NotFoundException("TarefaContrato id: {$id} não encontrado, impossível editar o registro");

      return $Crud->update($table = $this->get_table_name(), $new_data, $where = "id = :id", $bind = [ ":id" => $id ]);
    }

    public function delete($id){
      $data = $this->list(["id" => $id, "limit" => 1]);
    
      if ( !$data )
        throw new NotFoundException("TarefaContrato id: {$id} não encontrado, impossível excluir o registro");

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
      $TarefaContrato = new TarefaContrato();
      $list = $TarefaContrato->list(["ORDER" => "name asc"]);

      $return = [];
      foreach ( $list as $data ){
        $return[$data['id']] = $data['name'];
      }
      
      return $return;
    }

    public function list_details($filters = []){
      $Crud = new Crud();
      $sql = "SELECT 
                tarefas_contrato.*,
                contrato.numero numero_contrato,
                tarefas.nome nome_tarefa,
                tarefas.opcional_obrigatoria
              
              FROM 
                tarefas_contrato 
                
              inner join contrato on
                contrato.id = tarefas_contrato.id_contrato

              inner join tarefas on
                  tarefas.id = tarefas_contrato.id_tarefa
                                  
              WHERE 
                1 = 1

      ";

      $bind = [];
      if ( $filters ){
        $sql_filter = self::create_sql_filter($filters);
        $sql .= $sql_filter["where"];
        $bind = $sql_filter["bind"];
      }

      return $Crud->execute_query($sql, $bind);
    }

    public static function get_list_options_contrato(){
      $Contrato = new Contrato();
      $list = $Contrato->list(["ORDER" => "numero asc"]);

      $return = [];
      foreach ( $list as $data ){
        $return[$data['id']] = $data['numero'];
      }
      
      return $return;
    }

    public static function get_list_options_tarefa(){
      $Tarefa = new Tarefa();
      $list = $Tarefa->list(["ORDER" => "nome asc"]);

      $return = [];
      foreach ( $list as $data ){
        $return[$data['id']] = $data['nome'];
      }
      
      return $return;
    }

    public function to_datatable($list){
      $return = [];
      $columns = ["Contrato", "Tarefa", "Opcional/Obrigatória", "Validade <small>meses</small>", "Ações"];
      $items = [];
      //Helper::debug_data($list);
      foreach ( $list as $key => $data ){
        $buttons = ["data" => $data, "action" => ["edit", "upload", "delete"],];
         
        $temp = [     
          [
            "text" => $data['numero_contrato'], "classes" => ["text-center"],
            "attributes" => [ "order" => $data['numero_contrato'], "raw" => $data['numero_contrato']],
          ],
          [
            "text" => $data['nome_tarefa'], "classes" => ["text-left"],
            "attributes" => [ "order" => $data['nome_tarefa'], "raw" => $data['nome_tarefa'],]
          ],
          [
            "text" => $data['opcional_obrigatoria'], "classes" => ["text-center"],
            "attributes" => [ "order" => $data['opcional_obrigatoria'], "raw" => $data['opcional_obrigatoria'],],
            //"format" => "date_br",
          ],
          [
            "text" => $data['validade_meses'], "classes" => ["text-right"],
            "attributes" => [ "order" => $data['validade_meses'], "raw" => $data['validade_meses'],],
            //"format" => "date_br",
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
    public static function get_list_options_opcional_obrigatoria(){
      $list = [
        [ "id" => "Opcional", "name" => "Opcional" ],
        [ "id" => "Obrigatória", "name" => "Obrigatória" ],
      ];
      $return = [];
      foreach ( $list as $data ){
        $return[$data['id']] = $data['name'];
      }
      
      return $return;
    }

    public static function get_form($mode = "create"){
      $form = [
        "id" => "form_tarefas_contrato",
        "fields" => [
          ['id' => 'id', 'type' => 'hidden', 'required' => true],  
          ['id' => 'id_contrato','label' => 'Contrato', 'required' => true,
            'type' => 'select', 'attributes' => ['emptyval' => 'Selecione...',
            ], 
            'options' => self::get_list_options_contrato()
          ],       
          ['id' => 'id_tarefa','label' => 'Tarefa', 'required' => true,
            'type' => 'select', 'attributes' => ['emptyval' => 'Selecione...',
            ], 
            'options' => self::get_list_options_tarefa()
          ],       
          
          ['id' => 'status','label' => 'Status', 'required' => true,
            'type' => 'select', 'attributes' => ['emptyval' => 'Selecione...',
            ], 
            'options' => self::get_list_options_ativo()
          ],   
          
          ['id' => 'validade_meses',  'label' => 'Validade <small>meses</small>',
            'type' => 'number', 'attributes' => ['minlength' => 1, 'step' => ".01", "placeholder" => "Informe a validade"]],         
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

    public static function get_form_upload($mode = "create"){
      $form = [
        "id" => "form_tarefas_contrato_upload",
        "fields" => [
          ['id' => 'id_tarefa_contrato', 'type' => 'hidden', 'required' => true],            
          ['id' => 'arquivo',  'label' => 'Arquivo',
            'type' => 'file', 'attributes' => [ "placeholder" => "Selecione o arquivo"]],         
        ]
      ];

      return $form;
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
                "data-bs-target" => "#modal_form_tarefa",      
                "data-form_id" => "tarefas_contrato",                                
              ],
              "href" => "#",
              //"required_permission" => "tarefa_edit",
            ];
            break;
            
          case  "upload":
            $buttons[] = [ 
              "class" => array("api", "dropdown-item", "btn_upload", "text-dark"), 
              "description" => Helper::get_icon(["icon" => "upload" ]) . " Anexos ", 
              "type" => "link",
              "attributes" => [
                "data-object" => get_class($this),
                "data-action" => $action,
                "data-id" => $parameters['data']['id'],
                //"data-modal_description" => "Editar Processo: {$parameters['data']['id']} - {$parameters['data']['name']}",                
                "data-bs-toggle" => "modal",
                "data-bs-target" => "#modal_form_tarefa_upload",      
                "data-form_id" => "tarefas_contrato_upload",                                
              ],
              "href" => "#",
              //"required_permission" => "tarefa_edit",
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
                //"data-bs-target" => "#modal_form_tarefa",                                      
              ],
              "href" => "#",
              //"required_permission" => "tarefa_delete",
            ];
            break;
        }
      }

      if ( !$buttons )
        return "";
      
      return Helper::create_html_button_dropdown($buttons);

    }

  }