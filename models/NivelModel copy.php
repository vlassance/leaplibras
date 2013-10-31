<?php

class NivelModel extends PersistModelAbstract
{
	private $in_id;
	private $st_nome;
	private $st_email;
	
	function __construct()
	{
		parent::__construct();
		$this->createTableNivel();
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
	
	public function setEmail( $st_email )
	{
		$this->st_email = $st_email;
		return $this;
	}
	
	public function getEmail()
	{
		return $this->st_email;
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
				$o_nivel->setId($o_ret->con_in_id);
				$o_nivel->setNome($o_ret->con_st_nome);
				$o_nivel->setEmail($o_ret->con_st_email);
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
		$this->setId($o_ret->con_in_id);
		$this->setNome($o_ret->con_st_nome);
		$this->setEmail($o_ret->con_st_email);		
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
		if(is_null($this->in_id))
			$st_query = "INSERT INTO tbl_nivel
						(
							con_st_nome,
							con_st_email
						)
						VALUES
						(
							'$this->st_nome',
							'$this->st_email'
						);";
		else
			$st_query = "UPDATE
							tbl_nivel
						SET
							con_st_nome = '$this->st_nome',
							con_st_email = '$this->st_email'
						WHERE
							con_in_id = $this->in_id";
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
			throw $e;
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
						con_st_email CHAR(100),
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