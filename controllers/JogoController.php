<?php
if ( ! session_id() ) @ session_start();
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
	private $questao;
	
	private $level;
	
	/**
	* Ação que deverá ser executada quando 
	* nenhuma outra for especificada, do mesmo jeito que o
	* arquivo index.html ou index.php é executado quando nenhum é
	* referenciado
	*/
	public function listarJogoAction()
	{
		if (isset($_GET['level']))
			$level_atual = $_GET['level'];
		else
			$level_atual = 1;
		if (isset($_GET['questao']))
			$questao_atual = $_GET['questao'];
		else
			$questao_atual = 1;
		//calcProxQuestao($level_atual, $questao_atual);
		
		
		$_SESSION['level'] = $_GET['level'];
		if (isset($_GET['questao']))
			$_SESSION['questao'] = $_GET['questao'];
		else
			$_SESSION['questao'] = 1;
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de contatos
		$o_view = new View('views/listarJogo.phtml');

		//$o_view->setParams(array('v_niveis' => $v_niveis));
		
		//Imprimindo código HTML
		$o_view->showPage();
	}

	
}
?>