<?php
if ( ! session_id() ) @ session_start();
require_once 'models/NivelModel.php';
require_once 'models/NivelUsuarioModel.php';
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
	private $o_view;
	
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
		$this->calcProxQuestao();
		
		$nivel = new NivelModel();
		$nivel = $nivel->loadByLevel($this->level);
		
		if (!$nivel->getId()) {
			$this->o_view = new View('views/parabens.phtml');
		} else {
			$this->o_view = new View('views/listarJogo.phtml');
			$this->setParametros();
		}
		
		//Imprimindo código HTML
		$this->o_view->showPage();
	}
	
	private function setParametros() {
		$nivel = new NivelModel();
		$nivel = $nivel->loadByLevel($this->level);
		$total_questoes = $nivel->getTotalQuestoes();
		$titulo_level = $nivel->getNome();
		
		$mostra_ajuda = false;
		if ((int)$this->level == 1)
			$mostra_ajuda = true;
		
		$id_usuario = $_SESSION['id_usuario'];
		
		$pontuacao = 100*($this->questao-1)/$total_questoes;
		
		$nivel_usuario = new NivelUsuarioModel();
		$nivel_usuario = $nivel_usuario->loadByUsuarioNivel($id_usuario, $this->level);
		if (!$nivel_usuario->getId())
			$max_pontuacao = 0;
		else
			$max_pontuacao = 100*$nivel_usuario->getMaxScore()/$total_questoes;
		
		$nome_gesto = "Teste nome";
		/*$url_midia = "letrab.jpg";
		$tipo_midia = "I";*/
		$url_midia = "//www.youtube.com/embed/3iUZju5h5gw";
		$tipo_midia = "V";
		$json_gesto = "teste_gest";
		
		$this->o_view->setParams(array(
			'nome_gesto' => $nome_gesto,
			'titulo_level' => $titulo_level,
			'mostra_ajuda' => $mostra_ajuda,
			'url_midia' => $url_midia,
			'tipo_midia' => $tipo_midia,
			'pontuacao' => $pontuacao,
			'max_pontuacao' => $max_pontuacao,
			'json_gesto' => $json_gesto,
		));
	}
	
	private function calcProxQuestao() {
		if (isset($_GET['level']))
			$this->level = (int)$_GET['level'];
		else
			$this->level = 1;
		if (isset($_GET['questao']))
			$this->questao = (int)$_GET['questao'];
		else
			$this->questao = 1;
		
		$nivel = new NivelModel();
		$nivel = $nivel->loadByLevel($this->level);
		$total_questoes = $nivel->getTotalQuestoes();
		
		$this->questao++;
		if ($this->questao > $total_questoes) {
			$this->questao = 1;
			$this->level++;
		}
	}

	
}
?>