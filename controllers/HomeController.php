<?php
/**
* @package Exemplo simples com MVC
* @author DigitalDev
* @version 0.1.1
* 
* Camada - Controladores ou Controllers
* Diretório Pai - controllers 
* 
* Controlador que deverá ser chamado quando não for
* especificado nenhum outro
*/
class HomeController
{
	/**
	* Ação que deverá ser executada quando 
	* nenhuma outra for especificada, do mesmo jeito que o
	* arquivo index.html ou index.php é executado quando nenhum é
	* referenciado
	*/
	public function listarHomeAction()
	{
		//definindo qual o arquivo HTML que será usado para
		//mostrar a lista de contatos
		$o_view = new View('views/listarHome.phtml');
		
		//Imprimindo código HTML
		$o_view->showPage();
	}
}
?>