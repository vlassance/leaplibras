<?php
require_once 'models/QuestaoModel.php';
require_once 'models/NivelModel.php';
require_once 'models/MidiaGestoModel.php';

class QuestaoController
{


	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de Questoes
	*/
	public function listarQuestaoAction()
	{
		$o_questao = new QuestaoModel();
		
		//Listando os Questoes cadastrados
		$v_questoes = $o_questao->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de Questoes
		$o_view = new View('views/listarQuestao.phtml');
		
		//Passando os dados do Questao para a View
		$o_view->setParams(array('v_questoes' => $v_questoes));
		
		//Imprimindo código HTML
		$o_view->showPage();
	}
	
	
	/**
	* Gerencia a  de criação
	* e edição dos Questoes 
	*/
	public function manterQuestaoAction()
	{
		$nivel = new NivelModel();
		$v_niveis = $nivel->_list();
		$midia_gesto = new MidiaGestoModel();
		$v_midia_gestos = $midia_gesto->_list();
		
		$o_questao = new QuestaoModel();
		
		//verificando se o id do Questao foi passado
		if( isset($_REQUEST['in_id']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['in_id']) )
				//buscando dados do Questao
				$o_questao->loadById($_REQUEST['in_id']);
			
		if(count($_POST) > 0)
		{
			$o_questao->setTitulo(DataFilter::cleanString($_POST['st_titulo']));
			$o_questao->setIdNivel(DataFilter::cleanString($_POST['in_id_nivel']));
			$o_questao->setIdMidiaGesto(DataFilter::cleanString($_POST['in_id_midia_gesto']));
			
			//salvando dados e redirecionando para a lista de Questoes
			if($o_questao->save() > 0)
				Application::redirect('?controle=Questao&acao=listarQuestao');
		}
			
		$o_view = new View('views/manterQuestao.phtml');
		$o_view->setParams(array('o_questao' => $o_questao, 'v_niveis' => $v_niveis, 'v_midia_gestos' => $v_midia_gestos));
		$o_view->showPage();
	}
	
	/**
	* Gerencia a requisições de exclusão dos Questoes
	*/
	public function apagarQuestaoAction()
	{
		if( DataValidator::isNumeric($_GET['in_id']) )
		{
			//apagando o Questao
			$o_Questao = new QuestaoModel();
			$o_Questao->loadById($_GET['in_id']);
			$o_Questao->delete();
			
			Application::redirect('?controle=Questao&acao=listarQuestao');
		}	
	}
}	