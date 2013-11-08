<?php
require_once 'models/NivelUsuarioModel.php';

class NivelUsuarioController
{


	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de NivelUsuarios
	*/
	public function listarNivelUsuarioAction()
	{
		$o_nivel_usuario = new NivelUsuarioModel();
		
		//Listando os NivelUsuarios cadastrados
		$v_nivel_usuarios = $o_nivel_usuario->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de NivelUsuarios
		$o_view = new View('views/listarNivelUsuario.phtml');
		
		//Passando os dados do NivelUsuario para a View
		$o_view->setParams(array('v_nivel_usuarios' => $v_nivel_usuarios));
		
		//Imprimindo código HTML
		$o_view->showPage();
	}
	
	
	/**
	* Gerencia a  de criação
	* e edição dos NivelUsuarios 
	*/
	public function manterNivelUsuarioAction()
	{
		$o_nivel_usuario = new NivelUsuarioModel();
		
		//verificando se o id do NivelUsuario foi passado
		if( isset($_REQUEST['in_id']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['in_id']) )
				//buscando dados do NivelUsuario
				$o_nivel_usuario->loadById($_REQUEST['in_id']);
			
		if(count($_POST) > 0)
		{
			$o_nivel_usuario->setIdUsuario(DataFilter::cleanString($_POST['in_id_usuario']));
			$o_nivel_usuario->setIdNivel(DataFilter::cleanString($_POST['in_id_nivel']));
			$o_nivel_usuario->setMaxScore(DataFilter::cleanString($_POST['in_max_score']));
			
			//salvando dados e redirecionando para a lista de NivelUsuarios
			if($o_nivel_usuario->save() > 0)
				Application::redirect('?controle=NivelUsuario&acao=listarNivelUsuario');
		}
			
		$o_view = new View('views/manterNivelUsuario.phtml');
		$o_view->setParams(array('o_nivel_usuario' => $o_nivel_usuario));
		$o_view->showPage();
	}
	
	/**
	* Gerencia a requisições de exclusão dos NivelUsuarios
	*/
	public function apagarNivelUsuarioAction()
	{
		if( DataValidator::isNumeric($_GET['in_id']) )
		{
			//apagando o NivelUsuario
			$o_NivelUsuario = new NivelUsuarioModel();
			$o_NivelUsuario->loadById($_GET['in_id']);
			$o_NivelUsuario->delete();
			
			Application::redirect('?controle=NivelUsuario&acao=listarNivelUsuario');
		}	
	}
}	