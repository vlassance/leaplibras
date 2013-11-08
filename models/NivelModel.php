<?php

class NivelModel extends PersistModelAbstract
{
	private $in_id;
	private $st_nome;
	private $in_id_anterior;
	private $in_id_proximo;
	private $st_level;
	private $bo_aleatorio;
	private $in_total_questoes;
	
	function __construct()
	{
		parent::__construct();
		$this->createTableNivel();
	}
	
	private function setParams($obj)
	{
		$bo_aleatorio = ($obj->con_in_aleatorio == 1)? true : false;
		
		$this->setId($obj->con_in_id);
		$this->setNome($obj->con_st_nome);
		$this->setIdAnterior($obj->con_in_id_anterior);
		$this->setIdProximo($obj->con_in_id_proximo);
		$this->setLevel($obj->con_st_level);
		$this->setAleatorio($bo_aleatorio);
		$this->setTotalQuestoes($obj->con_in_total_questoes);
	}
	
	/**
	 * Setters e Getters
	 */
	
	public function setId( $in_id )
	{
		$this->in_id = $in_id;
		return $this;
	}
	
	public function getId()
	{
		return $this->in_id;
	}
	
	public function setNome( $st_nome )
	{
		$this->st_nome = $st_nome;
		return $this;
	}
	
	public function getNome()
	{
		return $this->st_nome;
	}
	
	public function setIdAnterior( $in_id_anterior )
	{
		$this->in_id_anterior = $in_id_anterior;
		return $this;
	}
	
	public function getIdAnterior()
	{
		return $this->in_id_anterior;
	}
	
	public function setIdProximo( $in_id_proximo )
	{
		$this->in_id_proximo = $in_id_proximo;
		return $this;
	}
	
	public function getIdProximo()
	{
		return $this->in_id_proximo;
	}
	
	public function setLevel( $st_level )
	{
		$this->st_level = $st_level;
		return $this;
	}
	
	public function getLevel()
	{
		return $this->st_level;
	}
	
	public function setAleatorio( $bo_aleatorio )
	{
		$this->bo_aleatorio = $bo_aleatorio;
		return $this;
	}
	
	public function getAleatorio()
	{
		return $this->bo_aleatorio;
	}
	
	public function setTotalQuestoes( $in_total_questoes )
	{
		$this->in_total_questoes = $in_total_questoes;
		return $this;
	}
	
	public function getTotalQuestoes()
	{
		return $this->in_total_questoes;
	}
	
	/**
	* Retorna um array contendo os niveis
	* @param string $st_nome
	* @return Array
	*/
	public function _list( $st_nome = null )
	{
		if(!is_null($st_nome))
			$st_query = "SELECT * FROM tbl_nivel WHERE con_st_nome LIKE '%$st_nome%';";
		else
			$st_query = 'SELECT * FROM tbl_nivel;';	
		
		$v_niveis = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_nivel = new NivelModel();
				$o_nivel->setParams($o_ret);
				array_push($v_niveis, $o_nivel);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_niveis;
	}
	
	/**
	* Retorna os dados de um nivel referente
	* a um determinado Id
	* @param integer $in_id
	* @return NivelModel
	*/
	public function loadById( $in_id )
	{
		$v_niveis = array();
		$st_query = "SELECT * FROM tbl_nivel WHERE con_in_id = $in_id;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setParams($o_ret);		
		return $this;
	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de nivel. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/
	public function save()
	{
		$in_aleatorio = ($this->bo_aleatorio)? 1 : 0;
		
		$id_ant_sql = $id_prox_sql = "NULL";
		if (isset($this->in_id_anterior) && $this->in_id_anterior != "")
			$id_ant_sql = $this->in_id_anterior;
		if (isset($this->in_id_proximo) && $this->in_id_proximo != "")
			$id_prox_sql = $this->in_id_proximo;
		
		if(is_null($this->in_id)) {
			$st_query = "INSERT INTO tbl_nivel
						(
							con_st_nome,
							con_in_id_anterior,
							con_in_id_proximo,
							con_st_level,
							con_in_aleatorio,
							con_in_total_questoes
						)
						VALUES
						(
							'$this->st_nome',
							$id_ant_sql,
							$id_prox_sql,
							'$this->st_level',
							$in_aleatorio,
							$this->in_total_questoes
						);";
		} else {
			$st_query = "UPDATE
							tbl_nivel
						SET
							con_st_nome = '$this->st_nome',
							con_in_id_anterior = $id_ant_sql,
							con_in_id_proximo = $id_prox_sql,
							con_st_level = '$this->st_level',
							con_in_aleatorio = $in_aleatorio,
							con_in_total_questoes = $this->in_total_questoes
						WHERE
							con_in_id = $this->in_id";
		}
		try
		{	
			if($this->o_db->exec($st_query) > 0)
				if(is_null($this->in_id))
				{
					
					/*
					* verificando se o driver usado é sqlite e pegando o ultimo id inserido
					* por algum motivo, a função nativa do PDO::lastInsertId() não funciona com sqlite
					*/
					if($this->o_db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite')
					{
						$o_ret = $this->o_db->query('SELECT last_insert_rowid() AS com_in_id')->fetchObject();
						return $o_ret->com_in_id;
					}
					else
						return $this->o_db->lastInsertId();
					
				}
				else
					return $this->in_id;
		}
		catch (PDOException $e)
		{
		}
		return false;				
	}

	/**
	* Deleta os dados persistidos na tabela de
	* nivel usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->in_id))
		{
			$st_query = "DELETE FROM
							tbl_nivel
						WHERE con_in_id = $this->in_id";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	
	/**
	* Cria tabela para armazernar os dados de nivel, caso
	* ela ainda não exista.
	* @throws PDOException
	*/
	private function createTableNivel()
	{
		/*
		* No caso do Sqlite, o AUTO_INCREMENT é automático na chave primaria da tabela
		* No caso do MySQL, o AUTO_INCREMENT deve ser especificado na criação do campo
		*/
		if($this->o_db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite')
			$st_auto_increment = '';
		else
			$st_auto_increment = 'AUTO_INCREMENT';
		
		$st_query = "CREATE TABLE IF NOT EXISTS tbl_nivel
					(
						con_in_id INTEGER NOT NULL $st_auto_increment,
						con_st_nome CHAR(200),
						con_in_id_anterior INTEGER,
						con_in_id_proximo INTEGER,
						con_st_level CHAR(200),
						con_in_aleatorio INTEGER,
						con_in_total_questoes INTEGER,
						PRIMARY KEY(con_in_id)
					)";

		//executando a query;
		try
		{
			$this->o_db->exec($st_query);
		}
		catch(PDOException $e)
		{
			throw $e;
		}	
	}
}
?>