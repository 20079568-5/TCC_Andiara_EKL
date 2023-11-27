<?php 
  class ChannelException extends Exception{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
      // some code
  
      // make sure everything is assigned properly
      parent::__construct($message, $code, $previous);
    }
  
    // custom string representation of object
    public function __toString() {
      return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
  }

  class FormExceptionOriginal extends Exception{

    private $field;
    
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
        // some code

        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
    
  }

  class FormException extends Exception{

    private $field;
    
    // Redefine the exception so message isn't optional
    public function __construct($message, $field = null) {
        // some code

        // make sure everything is assigned properly
        parent::__construct($message);
        $this->set_field($field);
    }
    
    public function get_field(){ return $this->field; }
    public function set_field($field): self { $this->field = $field; return $this; }
    
  }

  class NetworkException extends Exception{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
      // some code
  
      // make sure everything is assigned properly
      parent::__construct($message, $code, $previous);
    }
  }

  class NotFoundException extends Exception{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
      // some code
  
      // make sure everything is assigned properly
      parent::__construct($message, $code, $previous);
    }
  }  

  class PermissionExceptionOriginal extends Exception{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
      // some code
  
      // make sure everything is assigned properly
      parent::__construct($message, $code, $previous);
    }
  }

  class PermissionException extends Exception{

    private $required_permission;
    
    // Redefine the exception so message isn't optional
    public function __construct($message, $field = null) {
        // some code

        // make sure everything is assigned properly
        parent::__construct($message);
        $this->set_field($field);
    }   

    public function get_required_permission(){ return $this->required_permission; }
    public function set_required_permission($required_permission): self { $this->required_permission = $required_permission; return $this; }
  }
  
  class SystemException extends Exception{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
      // some code
  
      // make sure everything is assigned properly
      parent::__construct($message, $code, $previous);
    }
  
    // custom string representation of object
    public function __toString() {
      return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
  }  

  class ApiException extends Exception{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
      // some code
  
      // make sure everything is assigned properly
      parent::__construct($message, $code, $previous);
    }
  }  

  class AuthException extends Exception{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
      // some code
  
      // make sure everything is assigned properly
      parent::__construct($message, $code, $previous);
    }
  }  