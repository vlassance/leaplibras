<?php
require_once 'models/GestoModel.php';

/**
 * 
 * Responsável por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualização
 * 
 * Camada - Controladores ou Controllers
 * Diretório Pai - controllers
 * Arquivo - GestoController.php
 * 
 * @author DigitalDev
* @version 0.1.1
 *
 */
class GestoController
{


	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de Gestos
	*/
	public function listarGestoAction()
	{
		$o_gesto = new GestoModel();
		
		//Listando os Gestos cadastrados
		$v_gestos = $o_gesto->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de Gestos
		//$o_view = new View('trainer/listarGesto.phtml');
		$o_view = new View('trainerv03/index.phtml');
		
		//Passando os dados do Gesto para a View
		$o_view->setParams(array('v_gestos' => $v_gestos));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}
	
	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de Gestos
	*/
	public function todosGestosAction()
	{
		$o_gesto = new GestoModel();
		
		//Listando os Gestos cadastrados
		$v_gestos = $o_gesto->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de Gestos
		$o_view = new View('trainer/listarGesto.phtml');
		//$o_view = new View('trainerv03/index.phtml');
		
		//Passando os dados do Gesto para a View
		$o_view->setParams(array('v_gestos' => $v_gestos));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}
	
	/**
	* Gerencia a  de criação
	* e edição dos Gestos 
	*/
	public function manterGestoAction()
	{
		$o_gesto = new GestoModel();
		
		//verificando se o id do Gesto foi passado
		if( isset($_REQUEST['in_ges']) )
			//verificando se o id passado é valido
			if( DataValidator::isNumeric($_REQUEST['in_ges']) )
				//buscando dados do Gesto
				$o_gesto->loadById($_REQUEST['in_ges']);
			
		if(count($_POST) > 0)
		{
			$o_gesto->setNome(DataFilter::cleanString($_POST['st_nome']));
			$o_gesto->setJson($_POST['st_json']);
			
			//salvando dados e redirecionando para a lista de Gestos
			if($o_gesto->save() > 0)
				Application::redirect('?controle=Gesto&acao=listarGesto');
		}
			
		$o_view = new View('trainer/manterGesto.phtml');
		$o_view->setParams(array('o_gesto' => $o_gesto));
		$o_view->showContents();
	}
	
	/**
	* Gerencia a requisições de exclusão dos Gestos
	*/
	public function apagarGestoAction()
	{


		if( DataValidator::isNumeric($_REQUEST['in_ges']) )
		{
			//apagando o Gesto
			$o_Gesto = new GestoModel();
			$o_Gesto->loadById($_REQUEST['in_ges']);
			$o_Gesto->delete();
			
			Application::redirect('?controle=Gesto&acao=listarGesto');
		}	
	}
}	