<?php
require_once 'models/AcessoModel.php';
if ( ! session_id() ) @ session_start();

class AcessoController
{


	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de Acessos
	*/
	public function listarAcessoAction()
	{
		$o_acesso = new AcessoModel();
		
		//Listando os Acessos cadastrados
		$v_acessos = $o_acesso->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de Acessos
		$o_view = new View('views/listarAcesso.phtml');
		
		//Passando os dados do Acesso para a View
		$o_view->setParams(array('v_acessos' => $v_acessos));
		
		//Imprimindo código HTML
		$o_view->showPage();
	}
	
	
	/**
	* Gerencia a  de criação
	* e edição dos Acessos 
	*/
	public function manterAcessoAction()
	{
		$o_acesso = new AcessoModel();
		
		//verificando se o id do Acesso foi passado
		if( isset($_REQUEST['in_id']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['in_id']) )
				//buscando dados do Acesso
				$o_acesso->loadById($_REQUEST['in_id']);
			
		if(count($_POST) > 0)
		{
			$o_acesso->setNome(DataFilter::cleanString($_POST['st_nome']));
			$o_acesso->setDescricao(DataFilter::cleanString($_POST['st_descricao']));
			$o_acesso->setAdminAcessos(DataFilter::cleanString($_POST['bo_admin_acessos'])=="true");
			$o_acesso->setAdminGestos(DataFilter::cleanString($_POST['bo_admin_gestos'])=="true");
			
			//salvando dados e redirecionando para a lista de Acessos
			if($o_acesso->save() > 0)
				Application::redirect('?controle=Acesso&acao=listarAcesso');
		}
			
		$o_view = new View('views/manterAcesso.phtml');
		$o_view->setParams(array('o_acesso' => $o_acesso));
		$o_view->showPage();
	}
	
	/**
	* Gerencia a requisições de exclusão dos Acessos
	*/
	public function apagarAcessoAction()
	{
		if( DataValidator::isNumeric($_GET['in_id']) )
		{
			//apagando o Acesso
			$o_Acesso = new AcessoModel();
			$o_Acesso->loadById($_GET['in_id']);
			$o_Acesso->delete();
			
			Application::redirect('?controle=Acesso&acao=listarAcesso');
		}	
	}
}	