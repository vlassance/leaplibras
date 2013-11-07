<?php
require_once 'models/TabelaModel.php';

class TabelaController
{
	/**
	* Efetua a manipulação dos modelos necessários
	* para a aprensentação da lista de Niveis
	*/
	public function listarTabelaAction()
	{
		$o_tabela = new TabelaModel();
		
		//Listando as Tabelas existentes
		$v_tabelas = $o_tabela->_list();
		
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de Niveis
		$o_view = new View('views/listarTabela.phtml');
		
		//Passando os dados do Nivel para a View
		$o_view->setParams(array('v_tabelas' => $v_tabelas));
		
		//Imprimindo código HTML
		$o_view->showContents();
	}

}	