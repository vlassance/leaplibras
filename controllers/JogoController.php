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

		$o_view->setParams(array('v_niveis' => $v_niveis));
		
		//Imprimindo código HTML
		$o_view->showPage();
	}

	public setJogo(){

		$uid=4;
		$usuario = new UsuarioModel();
		$usuario = $usuario->loadbyID($uid);

		this->setUsuario($)
		
		$nivelusuario = new NivelUsuarioModel();
		$listunivel = $nivelusuario->loadByIdUsuario($uid);

		this->setListunievl($listunivel);
	}

	public setUsuario($usuario){
		$this->usuario = $usuario;
	}

	public setListunievl($listunivel){
		$this->listunivel = $listunivel;
	}
}
?>