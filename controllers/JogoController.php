<?php
/**
* @package Exemplo simples com MVC
* @author Kenji
* @version 0.1.1
* 
* Camada - Controladores ou Controllers
* Diretório Pai - controllers 
* 
* Controlador que deverá ser chamado quando não for
* especificado nenhum outro
*/

require_once 'models/UsuarioModel.php';
require_once 'models/NivelUsuarioModel.php';

class JogoController
{

	private $usuario;

	//Lista dos níveis do usuário
	private $listunivel;

	//Último nível do usuário
	private $univel;

	/**
	* Ação que deverá ser executada quando 
	* nenhuma outra for especificada, do mesmo jeito que o
	* arquivo index.html ou index.php é executado quando nenhum é
	* referenciado
	*/
	public function listarJogoAction()
	{	
		$this->setJogo();

		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de contatos
		$o_view = new View('views/listarJogo.phtml');

		//$o_view->setParams(array('v_niveis' => $v_niveis));
		
		//Imprimindo código HTML
		$o_view->showPage();
	}

	public function setJogo(){

		$uid=2;
		$usuario = new UsuarioModel();
		$usuario = $usuario->loadbyID($uid);

		$this->setUsuario($usuario);
		
		$nivelusuario = new NivelUsuarioModel();
		$nivelusuario = $nivelusuario->loadMaxLevelByIdUsuario($uid);

		$maxscore = $nivelusuario->getMaxScore();
		$totalquestoes = $nivelusuario->getNivel()->getTotalQuestoes();

		$maxscorepercent = 100 * $maxscore / $totalquestoes;
		echo $maxscorepercent;

		$threshold = $nivelusuario->getNivel();
	}

	public function setUsuario($usuario){
		$this->usuario = $usuario;
	}

	public function setListunivel($listunivel){
		$this->listunivel = $listunivel;
	}
}
?>