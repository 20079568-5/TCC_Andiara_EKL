<?php

  Class User {

    private $id;
    private $login;
    private $name;
    private $email;
    private $password;
    private $create_date;
    private $active;
    private $api_token;
  
    private $table_name = "user";
    const MIN_PASS_LEN = 8;

    public function get_id(){ return $this->id; }
    public function set_id($id): self { $this->id = $id; return $this; }

    public function get_login(){ return $this->login; }
    public function set_login($login): self { $this->login = $login; return $this; }

    public function get_name(){ return $this->name; }
    public function set_name($name): self { $this->name = $name; return $this; }

    public function get_email(){ return $this->email; }
    public function set_email($email): self { $this->email = $email; return $this; }
    
    public function get_create_date(){ return $this->create_date; }
    public function set_create_date($create_date): self { $this->create_date = $create_date; return $this; }

    public function get_active(){ return $this->active; }
    public function set_active($active): self { $this->active = $active; return $this; }

    public function get_api_token(){ return $this->api_token; }
    /* public function set_api_token($api_token): self { $this->api_token = $api_token; return $this; } */

    public function get_table_name(){ return $this->table_name; }

    public function create_password($login, $password){
      if ( strlen($password) < self::MIN_PASS_LEN )
        throw new FormException("A senha deve conter ao menos " . self::MIN_PASS_LEN . " caracteres", $field = "password");

      return md5($login.$password.PW_SALT);
    }

    private function create_api_token($login, $password){
      return md5($login.$password.rand().API_TOKEN_SALT);
    }

    public function build($data){
      $data['password'] = $this->create_password($data['login'], $data['password']);
      $data['api_token'] = $this->create_api_token($data['login'], $data['password']);
      return $this->to_object($data);
    }

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
      $user_data = $this->to_array();
      unset($user_data['create_date']);

      if ( empty($user_data['password']) )
        throw new Exception("Senha não informada, impossível criar o cadastro");
      return $Crud->create($table = $this->get_table_name(), $user_data );
    }

    public function edit(){
      $Crud = new Crud();
      $new_data = $this->to_array();
      $id = $new_data['id'];
      unset($new_data['id']);

      if ( empty($user_data['password']) )
        throw new Exception("Senha não informada, impossível editar o cadastro");

      return $Crud->update($table = $this->get_table_name(), $new_data, $where = "id = :id", $bind = [ ":id" => $id ]);
    }

    public function delete($id){
      $data = $this->list(["id" => $id, "limit" => 1]);
      
      if ( !$data )
        throw new NotFoundException("Usuário id: {$id} não encontrado, impossível excluir o registro");

      $Crud = new Crud();
      return $Crud->delete($table = $this->get_table_name(), $where = "id = :id", $bind = [ ":id" => $id ]);
    }

    public function change_password($password, $password2){
      $this->set_password($password, $password2);
      $this->edit();
    }

    public function set_password($password, $password2){
      if ($pass1 != $pass2)
        throw new Exception('As senhas digitadas precisam ser iguais.');

      if (strlen($pass1) < self::MIN_PASS_LEN)
        throw new Exception('A senha deve ter no mínimo ' . self::MIN_PASS_LEN . ' caracteres.');

      $this->password = self::create_password($this->login, $password);
    }

    public function set_api_token($password){
      if (strlen($pass1) < self::MIN_PASS_LEN)
        throw new Exception('A senha deve ter no mínimo ' . self::MIN_PASS_LEN . ' caracteres.');

      $this->api_token = self::create_api_token($this->login, $password);
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

    public static function get_form($mode = "create"){
      $form = [
          "id" => "form_user",
          "fields" => [
            
            [
              "id" => "login", "name" => "login", "type" => "text", "label" => "Login",
              "required" => true,
              "classes" => [ "form-control", "input-sm", "text-left", "limpar", "form-control-sm", ],
              "attributes" => ["minlength" => 3 ]
            ],
            [
              "id" => "name", "name" => "name", "type" => "text", "label" => "Nome",
              "required" => true,
              "classes" => [ "form-control", "input-sm", "text-left", "limpar", "form-control-sm", ],
              "attributes" => ["minlength" => 3 ]
            ],
            [
              "id" => "email", "name" => "email", "type" => "email", "label" => "E-mail",
              "required" => true,
              "classes" => [ "form-control", "input-sm", "text-left", "limpar", "form-control-sm", ],
              "attributes" => ["minlength" => 10 ]
            ],
            [
              "id" => "password", "name" => "password", "type" => "password", "label" => "Senha",
              "required" => true,
              "classes" => [ "form-control", "input-sm", "text-left", "limpar", "form-control-sm", ],
              "attributes" => ["minlength" => 8 ]
            ],
            [
              "id" => "password2", "name" => "password2", "type" => "password", "label" => "Confirmação da Senha",
              "required" => true,
              "classes" => [ "form-control", "input-sm", "text-left", "limpar", "form-control-sm", ],
              "attributes" => ["minlength" => 8 ]
            ],
            [
              "id" => "active", "name" => "active", "type" => "select", "label" => "Status",
              "required" => true,
              "classes" => [ "form-control", "input-sm", "form-select", "limpar", "form-control-sm", ],
              "attributes" => [ "data-db-origin" => "active", "emptyval" => "Selecione" ],
              "options" => [ 
                "1" => "Ativo",
                "0" => "Bloqueado",
              ]
            ]

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

    public function create_button($parameters = []){
      
      $buttons = [];
      foreach ( $parameters['action'] as $key => $action ){
        switch ( $action ){
          case  "edit":
            $buttons[] = [ 
              "class" => array("api", "dropdown-item", "btn_edit", "text-dark"), 
              "description" => "Editar ", 
              "type" => "link",
              "attributes" => [
                "data-object" => get_class($this),
                "data-action" => $action,
                "data-id" => $parameters['data']['id'],
                "data-modal_description" => "Editar Usuário: {$parameters['data']['id']} - {$parameters['data']['name']}",                
                "data-bs-toggle" => "modal",
                "data-bs-target" => "#modal_form_user",                                      
              ],
              "href" => "#",
              "required_permission" => "user_edit",
            ];
            break;

          case  "delete":
            $buttons[] = [ 
              "class" => array("api", "dropdown-item", "btn_delete", "text-dark"), 
              "description" => "Excluir ", 
              "type" => "link",
              "attributes" => [
                "data-object" => get_class($this),
                "data-action" => $action,
                "data-id" => $parameters['data']['id'],
                "data-modal_description" => "Excluir Usuário: {$parameters['data']['id']} - {$parameters['data']['name']}",                
                "data-bs-toggle" => "modal",
                "data-bs-target" => "#modal_form_user",                                      
              ],
              "href" => "#",
              "required_permission" => "user_delete",
            ];
            break;
        }
      }

      if ( !$buttons )
        return "";
      
      return Helper::create_html_button_dropdown($buttons);

    }

    public function get_datatable_list($list){

      $return = [];
      foreach ( $list as $key => $data ){
        $list[$key]['buttons'] = $this->create_button(["data" => $data, "action" => ["edit", "delete"],]);
        
      }
      return $list;
      return [ "data" => $list ];
    }
  }
