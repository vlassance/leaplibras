<?php
require_once 'models/UsuarioModel.php';
require_once 'models/NivelModel.php';

class NivelUsuarioModel extends PersistModelAbstract
{
	private $in_id;
	private $in_id_usuario;
	private $in_id_nivel;
	private $in_max_score;
	
	function __construct()
	{
		parent::__construct();
		$this->createTableNivelUsuario();
	}
	
	private function setParams($obj)
	{
		if ($obj) {
			$this->setId($obj->con_in_id);
			$this->setIdUsuario($obj->con_in_id_usuario);
			$this->setIdNivel($obj->con_in_id_nivel);
			$this->setMaxScore($obj->con_in_max_score);
		}
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
	
	public function setIdUsuario( $in_id_usuario )
	{
		$this->in_id_usuario = $in_id_usuario;
		return $this;
	}
	
	public function getIdUsuario()
	{
		return $this->in_id_usuario;
	}
	
	public function getUsuario()
	{
		if (!DataValidator::isNumeric($this->in_id_usuario) || $this->in_id_usuario <= 0)
			return null;
		
		$usuario = new UsuarioModel();
		return  $usuario->loadById($this->in_id_usuario);
	}
	
	public function setIdNivel( $in_id_nivel )
	{
		$this->in_id_nivel = $in_id_nivel;
		return $this;
	}
	
	public function getIdNivel()
	{
		return $this->in_id_nivel;
	}
	
	public function getNivel()
	{
		if (!DataValidator::isNumeric($this->in_id_nivel) || $this->in_id_nivel <= 0)
			return null;
		
		$nivel = new NivelModel();
		return  $nivel->loadById($this->in_id_nivel);
	}
	
	public function setMaxScore( $in_max_score )
	{
		$this->in_max_score = $in_max_score;
		return $this;
	}
	
	public function getMaxScore()
	{
		return $this->in_max_score;
	}
	
	/**
	* Retorna um array contendo os nivel_usuarios
	* @return Array
	*/
	public function _list()
	{
		$st_query = 'SELECT * FROM tbl_nivel_usuario;';	
		
		$v_nivel_usuarios = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_nivel_usuario = new NivelUsuarioModel();
				$o_nivel_usuario->setParams($o_ret);
				array_push($v_nivel_usuarios, $o_nivel_usuario);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_nivel_usuarios;
	}
	
	/**
	* Retorna os dados de um nivel_usuario referente
	* a um determinado Id
	* @param integer $in_id
	* @return NivelUsuarioModel
	*/
	public function loadById( $in_id )
	{
		$v_nivel_usuarios = array();
		$st_query = "SELECT * FROM tbl_nivel_usuario WHERE con_in_id = $in_id;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setParams($o_ret);		
		return $this;
	}

	public function loadMaxLevelByIdUsuario( $in_id_usuario )
	{
		$v_nivel_usuarios = array();
		$st_query = "SELECT nu.*
					 FROM tbl_nivel_usuario nu
					 JOIN tbl_nivel n
					 ON nu.con_in_id_nivel = n.con_in_id
					 WHERE nu.con_in_id_usuario = $in_id_usuario
					 ORDER BY n.con_st_level DESC 
					 LIMIT 1;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setParams($o_ret);		
		return $this;
	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de nivel_usuario. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/
	public function save()
	{
		if(is_null($this->in_id)) {
			$st_query = "INSERT INTO tbl_nivel_usuario
						(
							con_in_id_usuario,
							con_in_id_nivel,
							con_in_max_score
						)
						VALUES
						(
							$this->in_id_usuario,
							$this->in_id_nivel,
							$this->in_max_score
						);";
		} else {
			$st_query = "UPDATE
							tbl_nivel_usuario
						SET
							con_in_id_usuario = $this->in_id_usuario,
							con_in_id_nivel = $this->in_id_nivel,
							con_in_max_score = $this->in_max_score
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
	* nivel_usuario usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->in_id))
		{
			$st_query = "DELETE FROM
							tbl_nivel_usuario
						WHERE con_in_id = $this->in_id";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	
	/**
	* Cria tabela para armazernar os dados de nivel_usuario, caso
	* ela ainda não exista.
	* @throws PDOException
	*/
	private function createTableNivelUsuario()
	{
		/*
		* No caso do Sqlite, o AUTO_INCREMENT é automático na chave primaria da tabela
		* No caso do MySQL, o AUTO_INCREMENT deve ser especificado na criação do campo
		*/
		if($this->o_db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite')
			$st_auto_increment = '';
		else
			$st_auto_increment = 'AUTO_INCREMENT';
		
		$st_query = "CREATE TABLE IF NOT EXISTS tbl_nivel_usuario
					(
						con_in_id INTEGER NOT NULL $st_auto_increment,
						con_in_id_usuario INTEGER,
						con_in_id_nivel INTEGER,
						con_in_max_score INTEGER,
						PRIMARY KEY(con_in_id),
						CONSTRAINT uq_ID UNIQUE (con_in_id_usuario, con_in_id_nivel)
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