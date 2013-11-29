<?php
if ( ! session_id() ) @ session_start();
require_once 'models/QuestaoModel.php';
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
	private $id_usuario;
	
	private $o_view;
	
	private $questao;
	
	private $level;
	
	private $lista_questoes;
	
	/**
	* Ação que deverá ser executada quando 
	* nenhuma outra for especificada, do mesmo jeito que o
	* arquivo index.html ou index.php é executado quando nenhum é
	* referenciado
	*/
	public function listarJogoAction()
	{
		$this->id_usuario = $_SESSION['id_usuario'];
		
		if (isset($_GET['level']) && isset($_GET['questao'])) {
			$nivel_usuario = new NivelUsuarioModel();
			$nivel_usuario = $nivel_usuario->loadByUsuarioNivel($this->id_usuario, $_GET['level']);
			$nivel = new NivelModel();
			$nivel = $nivel->loadByLevel($_GET['level']);
			
			if ($_GET['questao'] > $nivel_usuario->getMaxScore()) {
				$nivel_usuario->setMaxScore($_GET['questao']);
				$nivel_usuario->save();
			}
		}
		
		$this->calcProxQuestao();
		
		$nivel = new NivelModel();
		$nivel = $nivel->loadByLevel($this->level);
		
		if (!$nivel->getId()) {
			$this->o_view = new View('views/parabens.phtml');
		} else {
			$this->calcListaQuestoes($nivel);
			$this->o_view = new View('views/listarJogo.phtml');
			$this->setParametros();
		}
		
		//Imprimindo código HTML
		$this->o_view->showPage();
	}
	
	private function calcProxQuestao() {
		if (isset($_GET['level']))
			$this->level = (int)$_GET['level'];
		else
			$this->level = 1;
		if (isset($_GET['questao']))
			$this->questao = (int)$_GET['questao'];
		else
			$this->questao = 0;
		
		$nivel = new NivelModel();
		$nivel = $nivel->loadByLevel($this->level);
		$total_questoes = $nivel->getTotalQuestoes();
		
		$this->questao++;
		if ($this->questao > $total_questoes) {
			$this->questao = 1;
			$this->level++;
		}
	}
	
	private function calcListaQuestoes($nivel) {
		$this->lista_questoes = array();
		if (isset($_SESSION['lista_questoes']) && $this->questao > 1)
			$this->lista_questoes = $_SESSION['lista_questoes'];
		else {
			//criar lista de questoes
			$questao = new QuestaoModel();
			$questoes = $questao->_listByNivel($nivel->getId());
			foreach ($questoes as $q) {
				$this->lista_questoes[] = $q->getId();
			}
		}
		
		$_SESSION['lista_questoes'] = $this->lista_questoes;
	}
	
	private function setParametros() {
		$nivel = new NivelModel();
		$nivel = $nivel->loadByLevel($this->level);
		$total_questoes = $nivel->getTotalQuestoes();
		$titulo_level = $nivel->getNome();
		$level = $this->level;
		$questao = $this->questao;
		
		$mostra_ajuda = true;
		if ((int)$this->level >= 3)
			$mostra_ajuda = false;
		
		$pontuacao = 100*($this->questao-1)/$total_questoes;
		
		$nivel_usuario = new NivelUsuarioModel();
		$nivel_usuario = $nivel_usuario->loadByUsuarioNivel($this->id_usuario, $this->level);
		if (!$nivel_usuario->getId())
			$max_pontuacao = 0;
		else
			$max_pontuacao = 100*$nivel_usuario->getMaxScore()/$total_questoes;
		
		$id_questao = $this->lista_questoes[$this->questao-1];
		$questao_model = new QuestaoModel();
		$questao_model = $questao_model->loadById($id_questao);
		$midia_gesto = $questao_model->getMidiaGesto();
			
		$nome_gesto = $questao_model->getTitulo();
		$url_midia = $midia_gesto->getFilepath();
		$tipo_midia = $midia_gesto->getTipoMidia();
		$json_gesto = $midia_gesto->getJson();
		
		$this->o_view->setParams(array(
			'nome_gesto' => $nome_gesto,
			'titulo_level' => $titulo_level,
			'mostra_ajuda' => $mostra_ajuda,
			'url_midia' => $url_midia,
			'tipo_midia' => $tipo_midia,
			'pontuacao' => $pontuacao,
			'max_pontuacao' => $max_pontuacao,
			'json_gesto' => $json_gesto,
			'level' => $level,
			'questao' => $questao,
		));
	}

	
}
?>