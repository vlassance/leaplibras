<?php
function __autoload($st_class)
{
	if(file_exists('lib/'.$st_class.'.php'))
		require_once 'lib/'.$st_class.'.php';
}

session_start();

require_once 'models/UsuarioModel.php';
require_once 'models/NivelUsuarioModel.php';
require_once 'models/NivelModel.php';
require_once("facebook.php");


/**
* @package Exemplo simples com MVC
* @author DigitalDev
* @version 0.1.1
* 
* Camada - Sistema / Controlladores
* Diretório Pai - lib 
* 
* Verifica qual classe controlador (Controller) o usuário deseja chamar
* e qual método dessa classe (Action) deseja executar
* Caso o controlador (controller) não seja especificado, o IndexControllers será o padrão
* Caso o método (Action) não seja especificado, o indexAction será o padrão
*/
class Application
{
	/**
	* Usada pra guardar o nome da classe
	* de controle (Controller) a ser executada
	* @var string
	*/
	protected $st_controller;
	
	
	/**
	* Usada para guardar o nome do metodo da
	* classe de controle (Controller) que deverá ser executado
	* @var string
	*/
	protected $st_action;
	
	private $usuario;

	private $level_usuario;
	
	
	/**
	* Verifica se os parametros de controlador (Controller) e acao (Action) foram
	* passados via parametros "Post" ou "Get" e os carrega tais dados
	* nos respectivos atributos da classe
	*/
	private function loadRoute()
	{
		/*
		* Se o controller nao for passado por GET,
		* assume-se como padrão o controller 'IndexController';
		*/
		$this->st_controller = isset($_REQUEST['controle']) ?  $_REQUEST['controle'] : 'index';
		
		/*
		* Se a action nao for passada por GET,
		* assume-se como padrão a action 'IndexAction';
		*/
		$this->st_action = isset($_REQUEST['acao']) ?  $_REQUEST['acao'] : 'index';
	}
	
	/**
	* 
	* Instancia classe referente ao Controlador (Controller) e executa
	* método referente e  acao (Action)
	* @throws Exception
	*/
	public function dispatch()
	{

		if ($this->obtainUserInfo()) {
			$_SESSION['nome_usuario'] = $this->usuario->getNome();
			$_SESSION['id_usuario'] = $this->usuario->getId();
			$_SESSION['level_usuario'] = $this->level_usuario;
		} else {
			$_SESSION['nome_usuario'] = "Visitante";
			$_SESSION['id_usuario'] = 0;
			$_SESSION['level_usuario'] = 0;
		}

		$this->loadRoute();
		
		//verificando se o arquivo de controle existe
		$st_controller_file = 'controllers/'.ucfirst($this->st_controller).'Controller.php';
		if(file_exists($st_controller_file))
			require_once $st_controller_file;
		else
			throw new Exception('Arquivo '.$st_controller_file.' nao encontrado');
			
		//verificando se a classe existe
		$st_class = $this->st_controller.'Controller';
		if(class_exists($st_class))
			$o_class = new $st_class;
		else
			throw new Exception("Classe '$st_class' nao existe no arquivo '$st_controller_file'");

		//verificando se o metodo existe
		$st_method = $this->st_action.'Action';
		if(method_exists($o_class,$st_method))
			$o_class->$st_method();
		else
			throw new Exception("Metodo '$st_method' nao existe na classe $st_class'");	
	}
	
	/**
	* Redireciona a chamada http para outra página
	* @param string $st_uri
	*/
	static function redirect( $st_uri )
	{
		header("Location: $st_uri");
	}

	public function obtainUserInfo(){

		$this->usuario = null;
		$this->level_usuario = null;
		
	    $facebook = $this->getFb();
		// Tenta obter informações do Fb sobre o usuário
		try {
			$user_profile = $facebook->api('/me','GET');
		} catch(FacebookApiException $e) {
			return false;
		}
		$fbid = $user_profile['id'];
		
		// Se não está logado, deve ficar como visitante
		if(!$user_profile || !$fbid)
			return false;
		
		$usuario = new UsuarioModel();
		$usuario = $usuario->loadbyFbid($fbid);
		
		// Se não está cadastrado, cadastra usuário
		if (!($usuario && $usuario->getId())) {
			if (array_key_exists('email', $user_profile) && array_key_exists('birthday', $user_profile))
				$usuario = $this->saveUser($user_profile);
			else
				return false;
		}
		
		// Usuário conectado e já possui cadastro
		if ($usuario && $usuario->getId()) {
			$this->usuario = $usuario;
			$this->level_usuario = $this->calcLevelUsuario($usuario->getId());
			
			return true;
		}

		return false;		
	}

	private function getFb() {
	    $config = array(
	        'appId' => '540608949358823',
	        'secret' => '1c574ed1666fcb5251d724eb0c064204',
	        'fileUpload' => false, // optional
	        'allowSignedRequest' => false, // optional, but should be set to false for non-canvas apps
	    );

	    return new Facebook($config);
	}

	private function calcLevelUsuario($id_usuario) {
		// Último nível jogado pelo usuário
		$nivel_usuario = new NivelUsuarioModel();
		$nivel_usuario = $nivel_usuario->loadMaxLevelByIdUsuario($id_usuario);
		
		if (!$nivel_usuario->getId())
			return 1;

		$max_score = $nivel_usuario->getMaxScore();

		$total_questoes = $nivel_usuario->getNivel()->getTotalQuestoes();

		$max_score_percent = 100 * $max_score / $total_questoes;
	
		$threshold = $nivel_usuario->getNivel()->getPctAprovacao();

		$level_usuario = $nivel_usuario->getNivel()->getLevel();

		if($max_score_percent >= $threshold)
			$level_usuario++;
		
		return $level_usuario;
	}

	private function saveUser($user_profile) {
		$usuario = new UsuarioModel();
		
		$usuario->setNome($user_profile['name']);
		$tz  = new DateTimeZone('America/Sao_Paulo');
		$birthday = DateTime::createFromFormat('m/d/Y', $user_profile['birthday'], $tz)->format('Y-m-d');
		$usuario->setDataNascimento($birthday);
		$usuario->setGenero(strtoupper($user_profile['gender'][0]));
		$usuario->setEmail($user_profile['email']);
		$usuario->setFbid($user_profile['id']);
		$acesso_user = new AcessoModel();
		$acesso_user = $acesso_user->loadUserAccess();
		$usuario->setIdNivelAcesso($acesso_user->getId());
		$usuario->save();
		
		return $usuario;
	}
}
?>