<?php

class MidiaGestoModel extends PersistModelAbstract
{
	private $in_id;
	private $st_titulo;
	private $st_filepath;
	private $st_json;
	private $st_tipo_midia;
	
	function __construct()
	{
		parent::__construct();
		$this->createTableMidiaGesto();
	}
	
	private function setParams($obj)
	{	
		$this->setId($obj->con_in_id);
		$this->setTitulo($obj->con_st_titulo);
		$this->setFilepath($obj->con_st_filepath);
		$this->setJson($obj->con_st_json);
		$this->setTipoMidia($obj->con_st_tipo_midia);
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
	
	public function setTitulo( $st_titulo )
	{
		$this->st_titulo = $st_titulo;
		return $this;
	}
	
	public function getTitulo()
	{
		return $this->st_titulo;
	}
	
	public function setFilepath( $st_filepath )
	{
		$this->st_filepath = $st_filepath;
		return $this;
	}
	
	public function getFilepath()
	{
		return $this->st_filepath;
	}
	
	public function setJson( $st_json )
	{
		$this->st_json = $st_json;
		return $this;
	}
	
	public function getJson()
	{
		return $this->st_json;
	}
	
	public function setTipoMidia( $st_tipo_midia )
	{
		$this->st_tipo_midia = $st_tipo_midia;
		return $this;
	}
	
	public function getTipoMidia()
	{
		return $this->st_tipo_midia;
	}
	
	/**
	* Retorna um array contendo os midia_gestos
	* @param string $st_titulo
	* @return Array
	*/
	public function _list( $st_titulo = null )
	{
		if(!is_null($st_titulo))
			$st_query = "SELECT * FROM tbl_midia_gesto WHERE con_st_titulo LIKE '%$st_titulo%';";
		else
			$st_query = 'SELECT * FROM tbl_midia_gesto;';	
		
		$v_midia_gestos = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_midia_gesto = new MidiaGestoModel();
				$o_midia_gesto->setParams($o_ret);
				array_push($v_midia_gestos, $o_midia_gesto);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_midia_gestos;
	}
	
	/**
	* Retorna os dados de um midia_gesto referente
	* a um determinado Id
	* @param integer $in_id
	* @return MidiaGestoModel
	*/
	public function loadById( $in_id )
	{
		$v_midia_gestos = array();
		$st_query = "SELECT * FROM tbl_midia_gesto WHERE con_in_id = $in_id;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setParams($o_ret);		
		return $this;
	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de midia_gesto. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/
	public function save()
	{	
		if(is_null($this->in_id)) {
			$st_query = "INSERT INTO tbl_midia_gesto
						(
							con_st_titulo,
							con_st_filepath,
							con_st_json,
							con_st_tipo_midia
						)
						VALUES
						(
							'$this->st_titulo',
							'$this->st_filepath',
							'$this->st_json',
							'$this->st_tipo_midia'
						);";
		} else {
			$st_query = "UPDATE
							tbl_midia_gesto
						SET
							con_st_titulo = '$this->st_titulo',
							con_st_filepath = '$this->st_filepath',
							con_st_json = '$this->st_json',
							con_st_tipo_midia = '$this->st_tipo_midia'
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
	* midia_gesto usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->in_id))
		{
			$st_query = "DELETE FROM
							tbl_midia_gesto
						WHERE con_in_id = $this->in_id";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	
	/**
	* Cria tabela para armazernar os dados de midia_gesto, caso
	* ela ainda não exista.
	* @throws PDOException
	*/
	private function createTableMidiaGesto()
	{
		/*
		* No caso do Sqlite, o AUTO_INCREMENT é automático na chave primaria da tabela
		* No caso do MySQL, o AUTO_INCREMENT deve ser especificado na criação do campo
		*/
		if($this->o_db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite')
			$st_auto_increment = '';
		else
			$st_auto_increment = 'AUTO_INCREMENT';
		
		$st_query = "CREATE TABLE IF NOT EXISTS tbl_midia_gesto
					(
						con_in_id INTEGER NOT NULL $st_auto_increment,
						con_st_titulo CHAR(200),
						con_st_filepath CHAR(200),
						con_st_json CHAR(200),
						con_st_tipo_midia CHAR(200),
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