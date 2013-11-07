<?php

class TabelaModel extends PersistModelAbstract
{
	private $st_url;
	private $st_titulo;
	private $tabelas_info;
	
	function __construct()
	{
		parent::__construct();
		$this->init();
	}
	
	public function init()
	{
		$this->tabelas_info = array(
			array("Níveis do Jogo", "?controle=Nivel&acao=listarNivel"),
			array("Níveis de Acesso dos Usuários", "?controle=Acesso&acao=listarAcesso"),
			array("Usuários", "?controle=Usuario&acao=listarUsuario"),
			array("Informações Usuário por Nível", "?controle=NivelUsuario&acao=listarNivelUsuario"),
			array("Questões do Jogo", "?controle=Questao&acao=listarQuestao"),
			array("Gestos e suas Mídias", "?controle=MidiaGesto&acao=listarMidiaGesto"),
		);
	}
	
	/**
	 * Setters e Getters
	 */
	
	public function setUrl( $st_url )
	{
		$this->st_url = $st_url;
		return $this;
	}
	
	public function getUrl()
	{
		return $this->st_url;
	}
	
	public function setTitle( $st_title )
	{
		$this->st_title = $st_title;
		return $this;
	}
	
	public function getTitle()
	{
		return $this->st_title;
	}
	
	public function _list()
	{	
		$v_tabelas = array();
		foreach($this->tabelas_info AS $tabela_info)
		{
			$o_tabela = new TabelaModel();
			$o_tabela->setTitle($tabela_info[0]);
			$o_tabela->setUrl($tabela_info[1]);
			array_push($v_tabelas, $o_tabela);
		}			
		return $v_tabelas;
	}
}
?>