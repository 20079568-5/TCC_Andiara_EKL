<?php

  Class Helper {

    public static function debug_data($data = "" ){
      echo "<pre>";
        print_r($data);
      echo "</pre>";
    }
    
    public static function is_mobile() {
      return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }
    
    public static function create_input($parameters){
      $attr_list = ( isset($parameters['attributes']) ? self::build_custom_attr($parameters['attributes']) : "" );
      $class_list  = ( isset($parameters['classes']) ? self::build_custom_class($parameters['classes']) : "" );
      
      $input_index = "";
      if ( isset($parameters['prefix']) ) { 
        $index = ( isset($parameters['prefix']) ? "[{$parameters['prefix']}]" : "" );
        $input_index = ( isset($parameters['prefix']) ? " data-input_index=\"{$parameters['prefix']}\" " : "" ); 
        $new_name = "{$parameters['prefix']}{$index}[{$parameters['name']}]";
        $new_id = "{$parameters['prefix']}{$index}[{$parameters['id']}]";
        
        $parameters['name'] = $new_name;
        $parameters['id'] = $new_id;
      }
      
      switch ( $parameters['type'] ){
        case "select":
          $options_list = self::build_select_options($parameters['options']);
          $input = "<select id=\"{$parameters['id']}\" name=\"{$parameters['name']}\" class=\"{$class_list} selectpicker\" {$attr_list} {$input_index} >
              {$options_list}
          </select>";
          $parameters["input"] = $input;
          return self::build_form_group_row($parameters);
          break;
        
        case "textarea";
          $input = "<textarea id=\"{$parameters['id']}\" name=\"{$parameters['name']}\" class=\"{$class_list}\" {$attr_list} {$input_index} >{$parameters['VALUE']}</textarea>";
          $parameters["input"] = $input;
          return self::build_form_group_row($parameters);
          break;

        case "checkbox":
          $input = "  <div class=\"form-check form-check-inline\">
                          <input class=\"form-check-input {$class_list}\" id=\"{$parameters['id']}\" name=\"{$parameters['name']}\" value=\"{$parameters['VALUE']}\" data-cbx-id=\"{$parameters['VALUE']}\"  {$attr_list} {$input_index} >
                          <label class=\"form-check-label\" for=\"{$parameters['name']}\" >{$parameters['label']}</label>';
                      </div>";
          break;

        default:
          $input = "<input type=\"{$parameters['type']}\" id=\"{$parameters['id']}\" name=\"{$parameters['name']}\" class=\"{$class_list}\" {$attr_list} {$input_index} value=\"{$parameters['VALUE']}\" >";
          $parameters["input"] = $input;
          return self::build_form_group_row($parameters);
          break;
      }
    }

    public static function build_form_group_row($parameters){
      $label = ( isset($parameters['no_label']) ? "" : "<label for=\"{$parameters['id']}\" class=\"col-sm col-form-label\">{$parameters['label']}</label>" );
      $row_classes = ( isset($parameters['row_classes']) ? self::build_custom_class($parameters['row_classes']) : "" );
    
      return "<div class=\"form-group form-group-sm form-row {$row_classes} \">
                  {$label}
                  <div class=\"col-sm col-sm-9\">
                    {$parameters['input']}
                  </div>
              </div>";
    }

    public static function build_custom_attr($array = []){
      $attr_list = "";
      foreach ( $array as $key => $dados ){
        if ( $key == "data-select2-parametros" || $key == "data-select2_parametros" ){
          $attr_list .= " data-select2_parametros='" . json_encode($dados, true). "' ";
        }else {
          $attr_list .= " {$key}=\"{$dados}\"";    
        }
          
      }

      return $attr_list;            
    }

    public static function build_custom_class($data = ""){
      $class_list = $data;
      if ( is_array($data) ){
        $class_list = implode(" ", $data);    
        //$class_list .= "form-control form-control-sm";            
      }
      return $class_list;
    }

    public static function build_select_options($data = [], $selected = null ){ 
      $options_list = "";
      
      foreach ( $data['values'] as $key => $item ){
        $description = $item[$data['description']];
        if ( isset($data['concat_description']) && is_array($data['concat_description']) ){
          $description = "";
          $i = count($data['concat_description']);                
          foreach ( $data['concat_description'] as $temp ){
            $description .= $item[$temp];
            $last_iteration = !(--$i); //boolean true/false
            if ( !$last_iteration ){
              $description .= " - ";
            }
          }
        }
        $selected_option = ( $selected == $item[$data['key']] ? "selected" : "" );
        $options_list .= "<option value=\"{$item[$data['key']]}\" {$selected_option} >{$description}</option>";
      }
      return $options_list;
    }

    public static function validate_form($form, $form_data, $by_pass_required = []){

      $ret_array = [];
      foreach ( $form['fields'] as $field ){

        //$fieldDescription = "Campo ". ( isset($field['label']) ? "'" . $field['label'] . "'" : "" ) . ( isset($field['attributes']['sufix']) ? " <small >" . $field['attributes']['suffix'] . "</small>" : "") . "<span class=\"text-danger\" > <small>(name: '" . $field['id'] . "' type: '" . $field['type'] . "')</small></span>";
        $fieldDescription = "Campo  ". ( isset($field['label']) ? "<span class=\"text-danger\">" . $field['label'] . "</span>" : "" ) . ( isset($field['attributes']['sufix']) ? " <small >" . $field['attributes']['suffix'] . "</small>" : "") ;

        if ( $field['required'] && !in_array($field['id'], $by_pass_required)){ 
          if ( !isset($form_data[$field['id']]) || empty($form_data[$field['id']]) && !strlen($form_data[$field['id']]) ) throw new FormException($fieldDescription . " é obrigatório", $field['id']); // check with strlen to allow 0 as value
        }

        if ( !$field['required'] && empty($form_data[$field['id']]) && !strlen($form_data[$field['id']]) ){
          unset($form_data[$field['id']]);
        }

        if ( isset($form_data[$field['id']]) ){ // validate field

          $data = trim($form_data[$field['id']]); // remove whitespaces 
          
          switch ( $field['type'] ){ 
            case "number":
              if ( !is_numeric($data) ) throw new FormException($fieldDescription . "\r\n Valor: '" . $data . "' não numérico", $field['id'] );    
              break;

            case "radio":
            case "select": 
              $allowedOptions = array_keys($field['options']);
              if ( !$field['required'] && ( empty($data) && !strlen($data) ) ) break; //allow empty value
              if ( !isset($field['options'][$data]) ) throw new FormException($fieldDescription . "\r\n Valor: '" . $data . "' Inválido. Apenas os valores " . json_encode($allowedOptions, true) . " são permitidos", $field['id'] );
              break;
            
            case "email":
              if ( !$field['required'] && ( empty($data) && !strlen($data) ) ) break; //allow empty value
              if ( !self::validate("email", $data) ) throw new FormException($fieldDescription . "\r\n Valor: '" . $data . "' Informe um endereço de e-mail válido", $field['id']);
              break;

            default:
              if ( !$field['required'] && ( empty($data) && !strlen($data) ) ) break; //allow empty value

          }

          // custom attributes validation
          if ( isset($field['attributes']) ) {

            if ( isset($field['attributes']['minlength']) && ( strlen($data) < $field['attributes']['minlength'] )  ) 
              throw new FormException($fieldDescription . "\r\n Deve conter no mínimo " . $field['attributes']['minlength'] . " caracteres", $field['id']);  

            if ( isset($field['attributes']['maxlength']) && ( strlen($data) > $field['attributes']['maxlength'] )  ) 
              throw new FormException($fieldDescription . "\r\n Deve conter no máximo " . $field['attributes']['maxlength'] . " caracteres", $field['id']);  

            if ( isset($field['attributes']['min']) && ( $data < $field['attributes']['min'] )  ) 
              throw new FormException($fieldDescription . "\r\n Valor deve ser maior ou igual a: " . $field['attributes']['min'], $field['id']);  

            if ( isset($field['attributes']['max']) && ( $data > $field['attributes']['max'] )  ) 
              throw new FormException($fieldDescription . "\r\n Valor deve ser menor ou igual a: " . $field['attributes']['max'], $field['id']);

            if ( isset($field['attributes']['data-type']) && !empty($data) ){

              switch ($field['attributes']['data-type']){
                case "cnpj":
                  if ( !self::validate("cnpj", $data) ) throw new FormException($fieldDescription . "\r\n O CNPJ informado: " . $data  . " é inválido", $field['id']);
                  break;
              }

            }

          }
        }

        $ret_array[$field['id']] = $form_data[$field['id']];
      }

      return $ret_array;
    }

    public static function validate($type, $value){
      switch ( $type ){
        case "cnpj":
          return self::validateCnpj($value);
          break;
        case "email":
          return self::validateEmail($value);
          break;
      }
    }
    
    public static function validateCnpj($cnpj){

      if ( empty($cnpj) ) throw new Exception("Valor não informado. Impossível realizar a validação");
      $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
              
      // Valida tamanho
      if (strlen($cnpj) != 14)
        return false;

      // Verifica se todos os digitos são iguais
      if (preg_match('/(\d)\1{13}/', $cnpj))
        return false;	

      // Valida primeiro dígito verificador
      for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
        $soma += $cnpj[$i] * $j;
        $j = ($j == 2) ? 9 : $j - 1;
      }

      $resto = $soma % 11;
      if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
        return false;

      // Valida segundo dígito verificador
      for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
        $soma += $cnpj[$i] * $j;
        $j = ($j == 2) ? 9 : $j - 1;
      }

      $resto = $soma % 11;
      return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    }

    public static function validateEmail($email){
      $email = filter_var($email, FILTER_SANITIZE_EMAIL);
      return ( filter_var($email, FILTER_VALIDATE_EMAIL) ? true : false );
    }

    public static function getFormFieldNames($form) {
      $list = array();
      foreach($form AS $form_element) if ($form_element['id'] != 'id') $list[] = $form_element['id'];      
      return $list;
    }  

    public static function format($type, $value, $options = [] ){

      $decimals = ( !isset($options['decimals']) ? 2 : $options['decimals'] );
      if ( $value == "" ){
        return $value;
      }
      $new_value = $value;

      switch ($type){
        case "bytes":
          $unidade = ( isset($options['unit']) ? $options['unit'] : "MB" );
          $new_value = self::convert_bytes($value, $unidade);
          $new_value = self::format("number_br", $new_value);
          break;

        case "float":
          //number_format ( float $number , int $decimals , string $dec_point , string $thousands_sep )
          $new_value = number_format ( $value , $decimals, "." , "" );
          break;

        case "number":
          $new_value = number_format ( $value , $decimals, "." , "," );
          break;

        case "number_br":
          //number_format ( float $number , int $decimals , string $dec_point , string $thousands_sep )
          $new_value = number_format ( $value , $decimals, "," , "." );
          break;

        case "cpf":
        case "cnpj":
          // remover caracters não numericos 
          $cnpj_cpf = preg_replace("/\D/", '', $value);
          
          switch ( strlen($cnpj_cpf) ){
            // cpf
            case "11":
              $new_value = preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);                        
              break;
        
            //cnpj
            case "14":
              $new_value = preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
          }
          break;

        case "date":
          $dateFormat = ( isset($options['date_format']) ? $options['date_format'] : "Y-m-d" );
          $new_value = date($dateFormat, strtotime($value));
          break;

        case "date_br":
          $dateFormat = ( isset($options['date_format']) ? $options['date_format'] : "d/m/Y" );
          $new_value = date($dateFormat, strtotime($value));
          break;
              
        case "date_time":
          $dateFormat = ( isset($options['date_format']) ? $options['date_format'] : "Y-m-d" );
          $timeFormat = ( isset($options['time_format']) ? $options['time_format'] : "H:i:s" );
          $new_value = date($dateFormat . " " . $timeFormat, strtotime($value));
          break;

        case "phone":            
          switch ( strlen($value) ){
            case "10":
              $new_value = '(' . substr($value, 0, 2) . ') ' . substr($value, 2, 4)  . '-' . substr($value, 6);
              break;

            case "11":
              $new_value = '(' . substr($value, 0, 2) . ') ' . substr($value, 2, 5)  . '-' . substr($value, 7);
              break;
                
            default:
              $new_value = $value;
              break;
          }
          break;

        case "cep":
        case "zip_code":
          $new_value = substr($value, 0, 5) . '-' . substr($value, 5, 3);
          break;

      }

      return $new_value;
    }
    
    public static function convert_bytes($size,$unit = "MB")  {
      if($unit == "KB") {
        return $fileSize = round($size / 1024,4) ;
      }
      if($unit == "MB") {
        return $fileSize = round($size / 1024 / 1024,4) ;	
      }
      if($unit == "GB") {
        return $fileSize = round($size / 1024 / 1024 / 1024,4) ;
      }
    }

    // search array for specific key = value
    public static function searchSubArray(Array $array, $key, $value) {   
      foreach ($array as $subarray){  
          if (isset($subarray[$key]) && $subarray[$key] == $value)
          return $subarray;       
      } 
    }

    public static function array_keys_exists(array $keys, array $arr) {
      return !array_diff_key(array_flip($keys), $arr);
    }

    // *    $arr - associative multi keys data array
    // *    $group_by_fields - array of fields to group by
    // *    $sum_by_fields - array of fields to calculate sum in group
    public static function array_group_by($arr, $group_by_fields = false, $sum_by_fields = false, $RETORNAR_DIFERENCAS  = false) {

      if ( empty($group_by_fields) ) return; // * nothing to group

      //$fld_count = 'grp:count'; // * field for count of grouped records in each record group
      $fld_count = 'group_count'; // * field for count of grouped records in each record group

      // * format sum by
      if (!empty($sum_by_fields) && !is_array($sum_by_fields)) {
          $sum_by_fields = array($sum_by_fields);
      }

      // * protected  from collecting
      $fields_collected = array();

      // * do
      $out = array();
      foreach($arr as $value) {
          $newval = array();
          $key = '';
          foreach ($group_by_fields as $field) {
              $key .= $value[$field].'_';
              $newval[$field] = $value[$field];
              unset($value[$field]);
          }
          // * format key
          $key = substr($key,0,-1);

          // * count
          if (isset($out[$key])) { // * record already exists
              $out[$key][$fld_count]++;
          } else {
              $out[$key] = $newval;
              $out[$key][$fld_count]=1;
          }
          $newval = $out[$key];

          // * sum by
          if (!empty($sum_by_fields)) {
              foreach ($sum_by_fields as $sum_field) {
                  if (!isset($newval[$sum_field])) $newval[$sum_field] = 0;
                  $newval[$sum_field] += $value[$sum_field];
                  unset($value[$sum_field]);
              }
          }

          // * collect differencies
          if (!empty($value) && $RETORNAR_DIFERENCAS == true ) {
              foreach ($value as $field=>$v) if (!is_null($v)) {
                  if (!is_array($v)) {
                      $newval[$field][$v] = $v;
                  } else $newval[$field][join('_', $v)] = $v; // * array values 
              }
          }
          $out[$key] = $newval;
      }
      return array_values($out);
    }

    public static function get_js_triggers(){
        return  [ "onClick", "onChange", "onBlur", "onFocus", "data-select2-parametros", "data-select2-campo-pesquisa", "data-object_info" ];
    }

    public static function create_html_custom_attributes($attributes = []){ 
      $custom_attributes = "";
      if ( !$attributes )
        return $custom_attributes;

      foreach ( $attributes as $key => $value ){
        if ( in_array($key, self::get_js_triggers()) ){
          $custom_attributes .= " {$key}='{$value}'";
        }else {
          $custom_attributes .= " {$key}=\"{$value}\" ";
        }
        
      }
      return $custom_attributes;

    }

    public static function create_html_custom_classes($class = null){
      if ( !$class )
        return "";

      return ( is_array($class) ? implode(" ", $class) : (!empty($class) ? $class : "" ) );
    }

    public static function create_html_button($parameters){
      /*
      */
      if ( isset($parameters['required_permission']) && !empty($parameters['required_permission']) ){
        if ( !Auth::has_permission($permission_code = "user_edit", $user_id = @$_SESSION['auth']['user_id'], $throw_exception = false ) )
          return;
      }
      $custom_attributes = ( isset($parameters['attributes']) ? self::create_html_custom_attributes($parameters['attributes']) : "" );
      $classes = ( isset($parameters['class']) ? self::create_html_custom_classes($parameters['class']) : "" );

      if ( isset($parameters['dropdown']) ){
        return self::create_html_button_dropdown($parameters);
        /*
        $dropdown = "<div class=\"dropdown\">
                        <button class=\"btn btn-sm btn-xs btn-xsm dropdown-toggle {$classes} \" {$custom_attributes} type=\"button\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">Ações...</button>
                        <ul class=\"dropdown-menu w-100\" >";
                          foreach ( $parameters['dropdown'] as $key => $data ){
                            $dropdown .= "<li>" . self::create_html_button($data) . "</li>";

                          }
        $dropdown .= "</ul>
                    </div>";
        */

        return $dropdown;
      }

      switch ( $parameters['type'] ){
        case "link":
          return "<a href=\"{$parameters['href']}\" class=\"{$classes}\" {$custom_attributes} >{$parameters['description']}</a>\r\n";
          break;

        default:
          return "<button type=\"{$parameters['type']}\" class=\"{$classes}\" {$custom_attributes} >{$parameters['description']}</button>\r\n";
          break;
      }
    }

    public static function create_html_button_dropdown($parameters){
      $custom_attributes = ( isset($parameters['attributes']) ? self::create_html_custom_attributes($parameters['attributes']) : "" );
      $classes = ( isset($parameters['class']) ? self::create_html_custom_classes($parameters['class']) : "" );

      $dropdown = "<div class=\"dropdown\">
                        <button class=\"btn btn-sm btn-xs btn-xsm dropdown-toggle {$classes} \" {$custom_attributes} type=\"button\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">...</button>
                        <ul class=\"dropdown-menu w-100_\" >";
                          foreach ( $parameters as $key => $data ){
                            if ( isset($data['required_permission']) && !empty($data['required_permission']) ){
                              if ( !Auth::has_permission($permission_code = $data['required_permission'], $user_id = @$_SESSION['auth']['user_id'], $throw_exception = false ) )
                                continue;
                            }
                            $dropdown .= "<li>" . self::create_html_button($data) . "</li>";

                          }
        $dropdown .= "</ul>
                    </div>";

        return $dropdown;
    }

    public static function translate($string) {
      $list = [
        "name" => "nome",
        "ashes" => "cinzas",
        "yes" => "sim",
        "no" => "não",
        "weight" => "peso",
        "buttons" => "Ações/Botões",
        "tracking" => "rastreamento",
        "code" => "código",
        "tracking_code_id" => "Rastreabilidade"
      ];

      return ( isset($list[$string]) ? $list[$string] : $string ); 
    }

    public static function get_icon($parameters){
      $class = "";
      switch ( $parameters['icon'] ){
        case "alert":
          $icon = "alert-triangle";
          $class = "danger ";
          break;

        case "ok":
        case "yes":
        case "check":
        case "true":
        case "1":
          $icon = "check-circle";
          $class = "success";
          break;
        
        case "x":
        case "no";
        case "false":
        case "0":
          $icon = "x-circle";
          $class = "danger";
          break;

        case "print":
          $icon = "printer";
          break;

        default:
          $icon = $parameters['icon'];
          break;
      }

      return "<span class=\"text-{$class}\" data-feather=\"{$icon}\"></span>";
    }

    public static function replace_key_value($string, $data, $start_delimiter = "{{", $end_delimiter = "}}"){
			if ( !$string )
        return "";
        //throw new Exception("String base para substitução de valores não informada");


			if ( !$data || !is_array($data) )
        return $string;
        //throw new Exception("Array com variáveis para sustituição não informado ou inválido");
      

			foreach ( $data as $key => $value ){

				$find = $start_delimiter . $key . $end_delimiter;
				
				if ( is_array($value) )
          $string = Helper::replaceKeyValue($string, $value, $start_delimiter, $end_delimiter);
          
          if ( $value == null)
            $value = "";
					
          $string = str_replace($find, $value, $string);
			}

			return $string;
		}
  }