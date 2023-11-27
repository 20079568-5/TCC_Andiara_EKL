<?php

	Class Auth {

		public static function login($login, $password){
			if ( empty($login) || empty($password) )
				throw new Exception("Usuário ou senha não informados");

			$User = new User();
			$password = $User->create_password($login, $password);
			$login = $User->list(["login" => $login, "password" => $password, "active" => true]);
			if ( !$login ){ 
				sleep(5);
				throw new AuthException("Usuario ou senha inválidos");
			}

      return self::create_session($login);
		}

		public static function token_login($token){
			if ( empty($token) )
				throw new Exception("Token API não informado");

			$User = new User();
			$login = $User->list(["api_token" => $token, "active" => true, "limit" => 1]);
			if ( !$login ){ 
				sleep(5);
				throw new AuthException("Token inválido");
			}

      return self::create_session($login);
		}

    public static function create_session($user){

			session_start();

			$_SESSION['auth']['user_id'] = $user[0]['id'];
			$_SESSION['auth']['user_name'] = $user[0]['name'];
			$_SESSION['auth']['user_id'] = $user[0]['id'];
			$_SESSION['auth']['authenticated'] = true;
			
			return $_SESSION['auth'];

    }

		public static function logout(){
			session_destroy();
			unset($_SESSION);
		}

		public static function has_permission($permission_code, $user_id, $throw_exception = false){
			return true;
			$Permission = new Permission();
			$permission_info = $Permission->list(["code" => $permission_code, "limit" => 1]);

			if ( !$permission_info ){
				if ( $throw_exception )
					throw new NotFoundException("Permissão {$permission_code} não encontrada");

				return false;
			}
				
			$User = new User();
			$user = $User->list(["id" => $user_id]);

			if ( !$user ){
				if ( $throw_exception )
					throw new NotFoundException("Usuário {$user_id} não encontrado");

				return false;
			}

			if ( !$user[0]['active'] ){
				if ( $throw_exception )
					throw new AuthException("Usuário {$user_id} bloqueado");

				return false;
			}

			$UserPermission = new UserPermission();
			$list = $UserPermission->list(["user_id" => $user_id, "permission_id" => $permission_info[0]['id'], "limit" => 1]);

			if ( !$list ){
				if ( $throw_exception )
					throw new AuthException("Usuário não possui a permissão {$permission_info[0]['description']}");

				return false;
			}
			return true;
		}

    public static function get_auth_info(){
      return ( isset($_SESSION['auth']) ? $_SESSION['auth'] : [] );
    }
	}