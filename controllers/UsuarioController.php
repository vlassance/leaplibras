<?php
require_once 'models/UsuarioModel.php';
if ( ! session_id() ) @ session_start();
class UsuarioController
{


	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de Usuarios
	*/
	public function listarUsuarioAction()
	{
		$o_usuario = new UsuarioModel();
		
		//Listando os Usuarios cadastrados
		$v_usuarios = $o_usuario->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de Usuarios
		$o_view = new View('views/listarUsuario.phtml');
		
		//Passando os dados do Usuario para a View
		$o_view->setParams(array('v_usuarios' => $v_usuarios));
		
		//Imprimindo código HTML
		$o_view->showPage();
	}
	
	
	/**
	* Gerencia a  de criação
	* e edição dos Usuarios 
	*/
	public function manterUsuarioAction()
	{
		$acesso = new AcessoModel();
		$v_acessos = $acesso->_list();
		
		$o_usuario = new UsuarioModel();
		
		//verificando se o id do Usuario foi passado
		if( isset($_REQUEST['in_id']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['in_id']) )
				//buscando dados do Usuario
				$o_usuario->loadById($_REQUEST['in_id']);
			
		if(count($_POST) > 0)
		{
			$o_usuario->setNome(DataFilter::cleanString($_POST['st_nome']));
			$o_usuario->setIdade(DataFilter::cleanString($_POST['in_idade']));
			$o_usuario->setGenero(DataFilter::cleanString($_POST['st_genero']));
			$o_usuario->setEmail(DataFilter::cleanString($_POST['st_email']));
			$o_usuario->setFbid(DataFilter::cleanString($_POST['st_fbid']));
			$o_usuario->setIdNivelAcesso(DataFilter::cleanString($_POST['in_id_nivel_acesso']));
			
			//salvando dados e redirecionando para a lista de Usuarios
			if($o_usuario->save() > 0)
				Application::redirect('?controle=Usuario&acao=listarUsuario');
		}
			
		$o_view = new View('views/manterUsuario.phtml');
		$o_view->setParams(array('o_usuario' => $o_usuario, 'v_acessos' => $v_acessos));
		$o_view->showPage();
	}
	
	/**
	* Gerencia a requisições de exclusão dos Usuarios
	*/
	public function apagarUsuarioAction()
	{
		if( DataValidator::isNumeric($_GET['in_id']) )
		{
			//apagando o Usuario
			$o_Usuario = new UsuarioModel();
			$o_Usuario->loadById($_GET['in_id']);
			$o_Usuario->delete();
			
			Application::redirect('?controle=Usuario&acao=listarUsuario');
		}	
	}
}	