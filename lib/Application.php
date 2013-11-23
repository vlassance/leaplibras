<?php
function __autoload($st_class)
{
	if(file_exists('lib/'.$st_class.'.php'))
		require_once 'lib/'.$st_class.'.php';
}

session_start();

require_once 'models/UsuarioModel.php';
require_once 'models/NivelUsuarioModel.php';



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

	//Último nível jogado pelo usuário
	private $nivelusuario;

	// Máximo level que o usuário pode jogar
	private $levelusuario;

	// Porcentagem atingida no último nível jogado pelo usuário
	private $maxscorepercent;
	
	
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

		//TODO: função que verifica se usuário está logado
		$uid=1;

		$this->init($uid);

		$_SESSION['usuario'] = $this->getUsuario()->getNome();

		$_SESSION['idusuario'] = $uid;

		$_SESSION['levelusuario'] = $this->getLevelUsuario();

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

	public function init($uid){

		$usuario = new UsuarioModel();
		$usuario = $usuario->loadbyID($uid);
		

		// Último nível jogado pelo usuário
		$nivelusuario = new NivelUsuarioModel();
		$nivelusuario = $nivelusuario->loadMaxLevelByIdUsuario($uid);

		$maxscore = $nivelusuario->getMaxScore();

		$totalquestoes = $nivelusuario->getNivel()->getTotalQuestoes();

		$maxscorepercent = 100 * $maxscore / $totalquestoes;
		
		$threshold = $nivelusuario->getNivel()->getPctAprovacao();

		$levelusuario = $nivelusuario->getNivel()->getLevel();

		if($maxscorepercent >= $threshold)
			$levelusuario++;			

		$this->setUsuario($usuario);
		$this->setNivelUsuario($nivelusuario);
		$this->setLevelUsuario($levelusuario);
	}

	public function setUsuario($usuario){
		$this->usuario = $usuario;
	}

	public function setNivelUsuario($nivelusuario){
		$this->nivelusuario = $nivelusuario;
	}

	public function setLevelUsuario($levelusuario){
		$this->levelusuario = $levelusuario;
	}

	public function getUsuario(){
		return $this->usuario;
	}

	public function getNivelUsuario(){
		return $this->nivelusuario;
	}

	public function getLevelUsuario(){
		return $this->levelusuario;
	}
}
?>