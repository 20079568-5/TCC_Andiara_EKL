  <?php
  # PDO Wrapper, supporting MySQL and Sqlite
  # Usage:
  #   $db = new db();
  #
  #   // table, data
  #   $db->create('users', [
  #     'fname' => 'john',
  #     'lname' => 'doe'
  #   ]);
  #   
  #   // table, where, where-bind
  #   $db->read('users', "fname LIKE :search", [
  #     ':search' => 'j%'
  #   ]);
  #
  #   // table, data, where, where-bind
  #   $db->update('users', [
  #     'fname' => 'jame'
  #   ], 'gender = :gender', [
  #     ':gender' => 'female'
  #   ]);
  #   
  #   // table, where, where-bind
  #   $db->delete('users', 'lname = :lname', [
  #     ':lname' => 'doe'
  #   ]);

  class Crud {

      private $config = [
        # "dbdriver" => "sqlite",
        # "sqlitedb" => "path/to/db.sqlite"
        
        "dbdriver" => DB_CONFIG['SERVER'],
        "dbuser" => DB_CONFIG['USER'],
        "dbpass" => DB_CONFIG['PASSWORD'],
        "dbname" => DB_CONFIG['DB'],
        "dbhost" => DB_CONFIG['HOST'],
        "charset" => DB_CONFIG['CHARSET']
      ];

      public function __construct(){
        $this->db();
      }

      function db() {
        $dbhost = $this->config['dbhost'];
        $dbuser = $this->config['dbuser'];
        $dbpass = $this->config['dbpass'];
        $dbname = $this->config['dbname'];
        $charset = $this->config['charset'];

        # $sqlitedb = $this->config['sqlitedb'];
        $options = [
          PDO::ATTR_PERSISTENT => true, 
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES   => false,
            
        ];

        //Helper::debugData($this->config);
        try {
          switch($this->config["dbdriver"]) {
            case "sqlite":
              $conn = "sqlite:{$sqlitedb}";
              break;
            
            case "MYSQL":
            case "mysql":
              $conn = "mysql:host={$dbhost};dbname={$dbname};charset={$charset}";
              break;
            default:
              echo "Unsuportted DB Driver! Check the configuration.";
              exit(1);
          }

          $this->db = new PDO($conn, $dbuser, $dbpass, $options);
            
        } catch(PDOException $e) {
          //echo $e->getMessage(); exit(1);
          throw $e;
        }
      }

      function run($sql, $bind=[]) {
        $sql = trim($sql);
        try {

          $result = $this->db->prepare($sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
          $result->execute($bind);
          return $result;

        } catch (PDOException $e) {
          //echo $e->getMessage(); exit(1);
          throw $e;
        }
      }

      function create($table, $data) {
        $fields = $this->filter($table, $data);

        $sql = "INSERT INTO " . $table . " (" . implode( ", ", $fields) . ") VALUES (:" . implode(", :", $fields) . ");";

        $bind = [];
        foreach($fields as $field)
          $bind[":$field"] = $data[$field];

        $result = $this->run($sql, $bind);
        return $this->db->lastInsertId();
      }

      function read($table, $where="", $bind=[], $fields="*") {
        $sql = "SELECT " . $fields . " FROM " . $table;
        if(!empty($where))
          $sql .= " WHERE " . $where;
        $sql .= ";";

        $result = $this->run($sql, $bind);
        $result->setFetchMode(PDO::FETCH_ASSOC);

        $rows = [];
        while($row = $result->fetch()) {
          $rows[] = $row;
        }

        return $rows;
      }

      function update($table, $data, $where, $bind=[]) {
        $fields = $this->filter($table, $data);
        $fieldSize = sizeof($fields);

        $sql = "UPDATE " . $table . " SET ";
        for($f = 0; $f < $fieldSize; ++$f) {
            if($f > 0)
                $sql .= ", ";
            $sql .= $fields[$f] . " = :update_" . $fields[$f]; 
        }
        $sql .= " WHERE " . $where . ";";

        foreach($fields as $field){

            $bind[":update_$field"] = $data[$field];

        }
        
        //echo $sql;
        $result = $this->run($sql, $bind);
        //return ( [ "SQL" => $sql, "BINDING" => $bind, "ROWS" => $result->rowCount() ]);
        return $result->rowCount();
      }

      function delete($table, $where, $bind="") {
        $sql = "DELETE FROM " . $table . " WHERE " . $where . ";";
        $result = $this->run($sql, $bind);
        return $result->rowCount();
      }

      private function filter($table, $data) {
        $driver = $this->config['dbdriver'];

        if($driver == 'sqlite') {
          $sql = "PRAGMA table_info('" . $table . "');";
          $key = "name";
        } elseif($driver == 'mysql') {
          $sql = "DESCRIBE " . $table . ";";
          $key = "Field";
        } else {    
          $sql = "SELECT DISTINCT COLUMN_NAME FROM information_schema.columns WHERE table_name = '" . $table . "';";
          $key = strtoupper("column_name");
        }   

        if(false !== ($list = $this->run($sql))) {
          $fields = [];
          //Helper::debugData($list);
          foreach($list as $record)
            $fields[] = $record[$key];
          return array_values(array_intersect($fields, array_keys($data)));
        }

        return [];
      }

      public function execute_query($sql, $bind = []){        
        if ( empty($sql) )
          throw new Exception("Instrução SQL não informada");

        return $this->run($sql, $bind)->fetchAll(PDO::FETCH_ASSOC);

      }

      public static function create_where_like($field, $value, $delimiter = " ", $prefix = "" ){                    
        $search_expression = [ $field ];
        $search_values = explode($delimiter, trim($value));
        $filter = "";

        if ($search_values ){            
          $binding_values = [];
          $counter = 0;
          foreach ($search_expression as $key => $data ) {
            foreach ( $search_values as $key2 => $value ){                    
              $parameter = $prefix . "_" . $counter . "_X" ;                    

              $filter .= " AND " . $data . " LIKE :" . $parameter . " \r\n";
              $binding_values[':'. $parameter] = "%" . $value . "%";                    
              $counter++;
                
            }
              
          }

        }
        
        return [ "query_filter" => $filter, "binding" => $binding_values ];

      }

      public function get_fields($parameters = [] ){ // return list of table fields
        
        if ( !isset($parameters['table']) || empty($parameters['table']) )
          throw new Exception("Tabela não informada, impossível buscar a lista de campos");

        $sql = "SHOW COLUMNS FROM {$parameters['table']}";

        $fields = $this->execute_query($sql, $bind = []);

        if ( !isset($parameters['ignore_fields']) || empty($parameters['ignore_fields']) )
          return $fields;

        $field_list = [];
        foreach ( $fields as $key => $field ){
          if ( !in_array($field['Field'], $parameters['ignore_fields']) )
            $field_list[] = $field['Field'];
        }
        
        if ( isset($parameters['return_query_field_list']) && $parameters['return_query_field_list'] ){
          $prefix = ( isset($parameters['prefix']) && !empty($parameters['prefix']) ? "{$parameters['prefix']}."  : "" );
          return "{$prefix}" . implode(",{$prefix}\r\n ", $field_list);
        }       

        return $field_list;
      }

  }