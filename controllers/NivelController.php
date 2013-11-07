<?php
require_once 'models/NivelModel.php';

class NivelController
{


	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de Niveis
	*/
	public function listarNivelAction()
	{
		$o_nivel = new NivelModel();
		
		//Listando os Niveis cadastrados
		$v_niveis = $o_nivel->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de Niveis
		$o_view = new View('views/listarNivel.phtml');
		
		//Passando os dados do Nivel para a View
		$o_view->setParams(array('v_niveis' => $v_niveis));
		
		//Imprimindo código HTML
		$o_view->showPage();
	}
	
	
	/**
	* Gerencia a  de criação
	* e edição dos Niveis 
	*/
	public function manterNivelAction()
	{
		$o_nivel = new NivelModel();
		
		//verificando se o id do Nivel foi passado
		if( isset($_REQUEST['in_id']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['in_id']) )
				//buscando dados do Nivel
				$o_nivel->loadById($_REQUEST['in_id']);
			
		if(count($_POST) > 0)
		{
			$o_nivel->setNome(DataFilter::cleanString($_POST['st_nome']));
			$o_nivel->setIdAnterior(DataFilter::cleanString($_POST['in_id_anterior']));
			$o_nivel->setIdProximo(DataFilter::cleanString($_POST['in_id_proximo']));
			$o_nivel->setLevel(DataFilter::cleanString($_POST['st_level']));
			$o_nivel->setAleatorio(DataFilter::cleanString($_POST['bo_aleatorio'])=="true");
			$o_nivel->setTotalQuestoes(DataFilter::cleanString($_POST['in_total_questoes']));
			
			//salvando dados e redirecionando para a lista de Niveis
			if($o_nivel->save() > 0)
				Application::redirect('?controle=Nivel&acao=listarNivel');
		}
			
		$o_view = new View('views/manterNivel.phtml');
		$o_view->setParams(array('o_nivel' => $o_nivel));
		$o_view->showPage();
	}
	
	/**
	* Gerencia a requisições de exclusão dos Niveis
	*/
	public function apagarNivelAction()
	{
		if( DataValidator::isNumeric($_GET['in_id']) )
		{
			//apagando o Nivel
			$o_Nivel = new NivelModel();
			$o_Nivel->loadById($_GET['in_id']);
			$o_Nivel->delete();
			
			Application::redirect('?controle=Nivel&acao=listarNivel');
		}	
	}
}	