<?php

class AcessoModel extends PersistModelAbstract
{
	private $in_id;
	private $st_nome;
	private $st_descricao;
	private $bo_admin_acessos;
	private $bo_admin_gestos;
	
	function __construct()
	{
		parent::__construct();
		$this->createTableAcesso();
	}
	
	private function setParams($obj)
	{
		$bo_admin_acessos = ($obj->con_in_admin_acessos == 1)? true : false;
		$bo_admin_gestos = ($obj->con_in_admin_gestos == 1)? true : false;
		
		$this->setId($obj->con_in_id);
		$this->setNome($obj->con_st_nome);
		$this->setDescricao($obj->con_st_descricao);
		$this->setAdminAcessos($bo_admin_acessos);
		$this->setAdminGestos($bo_admin_gestos);
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
	
	public function setDescricao( $st_descricao )
	{
		$this->st_descricao = $st_descricao;
		return $this;
	}
	
	public function getDescricao()
	{
		return $this->st_descricao;
	}
	
	public function setAdminAcessos( $bo_admin_acessos )
	{
		$this->bo_admin_acessos = $bo_admin_acessos;
		return $this;
	}
	
	public function getAdminAcessos()
	{
		return $this->bo_admin_acessos;
	}
	
	public function setAdminGestos( $bo_admin_gestos )
	{
		$this->bo_admin_gestos = $bo_admin_gestos;
		return $this;
	}
	
	public function getAdminGestos()
	{
		return $this->bo_admin_gestos;
	}
	
	/**
	* Retorna um array contendo os acessos
	* @param string $st_nome
	* @return Array
	*/
	public function _list( $st_nome = null )
	{
		if(!is_null($st_nome))
			$st_query = "SELECT * FROM tbl_acesso WHERE con_st_nome LIKE '%$st_nome%';";
		else
			$st_query = 'SELECT * FROM tbl_acesso;';	
		
		$v_acessos = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_acesso = new AcessoModel();
				$o_acesso->setParams($o_ret);
				array_push($v_acessos, $o_acesso);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_acessos;
	}
	
	/**
	* Retorna os dados de um acesso referente
	* a um determinado Id
	* @param integer $in_id
	* @return AcessoModel
	*/
	public function loadById( $in_id )
	{
		$v_acessos = array();
		$st_query = "SELECT * FROM tbl_acesso WHERE con_in_id = $in_id;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setParams($o_ret);		
		return $this;
	}
	
	public function loadUserAccess()
	{
		$v_acessos = array();
		$st_query = "SELECT * FROM tbl_acesso WHERE con_in_admin_acessos = 0 and con_in_admin_gestos = 0;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setParams($o_ret);		
		return $this;
	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de acesso. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/
	public function save()
	{
		$in_admin_acessos = ($this->bo_admin_acessos)? 1 : 0;
		$in_admin_gestos = ($this->bo_admin_gestos)? 1 : 0;
		
		if(is_null($this->in_id)) {
			$st_query = "INSERT INTO tbl_acesso
						(
							con_st_nome,
							con_st_descricao,
							con_in_admin_acessos,
							con_in_admin_gestos
						)
						VALUES
						(
							'$this->st_nome',
							'$this->st_descricao',
							$in_admin_acessos,
							$in_admin_gestos
						);";
		} else {
			$st_query = "UPDATE
							tbl_acesso
						SET
							con_st_nome = '$this->st_nome',
							con_st_descricao = '$this->st_descricao',
							con_in_admin_acessos = $in_admin_acessos,
							con_in_admin_gestos = $in_admin_gestos
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
	* acesso usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->in_id))
		{
			$st_query = "DELETE FROM
							tbl_acesso
						WHERE con_in_id = $this->in_id";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	
	/**
	* Cria tabela para armazernar os dados de acesso, caso
	* ela ainda não exista.
	* @throws PDOException
	*/
	private function createTableAcesso()
	{
		/*
		* No caso do Sqlite, o AUTO_INCREMENT é automático na chave primaria da tabela
		* No caso do MySQL, o AUTO_INCREMENT deve ser especificado na criação do campo
		*/
		if($this->o_db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite')
			$st_auto_increment = '';
		else
			$st_auto_increment = 'AUTO_INCREMENT';
		
		$st_query = "CREATE TABLE IF NOT EXISTS tbl_acesso
					(
						con_in_id INTEGER NOT NULL $st_auto_increment,
						con_st_nome CHAR(200),
						con_st_descricao CHAR(200),
						con_in_admin_acessos INTEGER,
						con_in_admin_gestos INTEGER,
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