<?php
require_once 'models/MidiaGestoModel.php';

class MidiaGestoController
{


	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de MidiaGestos
	*/
	public function listarMidiaGestoAction()
	{
		$o_midia_gesto = new MidiaGestoModel();
		
		//Listando os MidiaGestos cadastrados
		$v_midia_gestos = $o_midia_gesto->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de MidiaGestos
		$o_view = new View('views/listarMidiaGesto.phtml');
		
		//Passando os dados do MidiaGesto para a View
		$o_view->setParams(array('v_midia_gestos' => $v_midia_gestos));
		
		//Imprimindo código HTML
		$o_view->showPage();
	}
	
	
	/**
	* Gerencia a  de criação
	* e edição dos MidiaGestos 
	*/
	public function manterMidiaGestoAction()
	{
		$o_midia_gesto = new MidiaGestoModel();
		
		//verificando se o id do MidiaGesto foi passado
		if( isset($_REQUEST['in_id']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['in_id']) )
				//buscando dados do MidiaGesto
				$o_midia_gesto->loadById($_REQUEST['in_id']);
			
		if(count($_POST) > 0)
		{
			$o_midia_gesto->setTitulo(DataFilter::cleanString($_POST['st_titulo']));
			$o_midia_gesto->setFilepath(DataFilter::cleanString($_POST['st_filepath']));
			$o_midia_gesto->setJson(DataFilter::cleanString($_POST['st_json']));
			$o_midia_gesto->setTipoMidia(DataFilter::cleanString($_POST['st_tipo_midia']));
			
			//salvando dados e redirecionando para a lista de MidiaGestos
			if($o_midia_gesto->save() > 0)
				Application::redirect('?controle=MidiaGesto&acao=listarMidiaGesto');
		}
			
		$o_view = new View('views/manterMidiaGesto.phtml');
		$o_view->setParams(array('o_midia_gesto' => $o_midia_gesto));
		$o_view->showPage();
	}
	
	/**
	* Gerencia a requisições de exclusão dos MidiaGestos
	*/
	public function apagarMidiaGestoAction()
	{
		if( DataValidator::isNumeric($_GET['in_id']) )
		{
			//apagando o MidiaGesto
			$o_MidiaGesto = new MidiaGestoModel();
			$o_MidiaGesto->loadById($_GET['in_id']);
			$o_MidiaGesto->delete();
			
			Application::redirect('?controle=MidiaGesto&acao=listarMidiaGesto');
		}	
	}
}	