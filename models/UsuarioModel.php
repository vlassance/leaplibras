<?php
require_once 'models/AcessoModel.php';

class UsuarioModel extends PersistModelAbstract
{
	private $in_id;
	private $st_nome;
	private $st_data_nascimento;
	private $st_genero;
	private $st_email;
	private $st_fbid;
	private $in_id_nivel_acesso;
	
	function __construct()
	{
		parent::__construct();
		$this->createTableUsuario();
	}
	
	private function setParams($obj)
	{	
		if ($obj) {
			$this->setId($obj->con_in_id);
			$this->setNome($obj->con_st_nome);
			$this->setDataNascimento($obj->con_st_data_nascimento);
			$this->setGenero($obj->con_st_genero);
			$this->setEmail($obj->con_st_email);
			$this->setFbid($obj->con_st_fbid);
			$this->setIdNivelAcesso($obj->con_in_id_nivel_acesso);
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
	
	public function setNome( $st_nome )
	{
		$this->st_nome = $st_nome;
		return $this;
	}
	
	public function getNome()
	{
		return $this->st_nome;
	}
	
	public function setDataNascimento( $st_data_nascimento )
	{
		$this->st_data_nascimento = '';
		$tz  = new DateTimeZone('America/Sao_Paulo');
		if ($st_data_nascimento) {
			$dn  = new DateTime($st_data_nascimento, $tz);
			$this->st_data_nascimento = $dn->format('Y-m-d');
		}
		return $this;
	}
	
	public function getDataNascimento()
	{
		return $this->st_data_nascimento;
	}
	
	public function setGenero( $st_genero )
	{
		$this->st_genero = $st_genero;
		return $this;
	}
	
	public function getGenero()
	{
		return $this->st_genero;
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
	
	public function setFbid( $st_fbid )
	{
		$this->st_fbid = $st_fbid;
		return $this;
	}
	
	public function getFbid()
	{
		return $this->st_fbid;
	}
	
	public function setIdNivelAcesso( $in_id_nivel_acesso )
	{
		$this->in_id_nivel_acesso = $in_id_nivel_acesso;
		return $this;
	}
	
	public function getIdNivelAcesso()
	{
		return $this->in_id_nivel_acesso;
	}
	
	public function getNivelAcesso()
	{
		if (!DataValidator::isNumeric($this->in_id_nivel_acesso) || $this->in_id_nivel_acesso <= 0)
			return null;
		
		$nivel_acesso = new AcessoModel();
		return  $nivel_acesso->loadById($this->in_id_nivel_acesso);
	}
	
	/**
	* Retorna um array contendo os usuarios
	* @param string $st_nome
	* @return Array
	*/
	public function _list( $st_nome = null )
	{
		if(!is_null($st_nome))
			$st_query = "SELECT * FROM tbl_usuario WHERE con_st_nome LIKE '%$st_nome%';";
		else
			$st_query = 'SELECT * FROM tbl_usuario;';	
		
		$v_usuarios = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_usuario = new UsuarioModel();
				$o_usuario->setParams($o_ret);
				array_push($v_usuarios, $o_usuario);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_usuarios;
	}
	
	/**
	* Retorna os dados de um usuario referente
	* a um determinado Id
	* @param integer $in_id
	* @return UsuarioModel
	*/
	public function loadById( $in_id )
	{
		$st_query = "SELECT * FROM tbl_usuario WHERE con_in_id = $in_id;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setParams($o_ret);		
		return $this;
	}
	
	public function loadByFbid( $st_fbid )
	{
		$st_query = "SELECT * FROM tbl_usuario WHERE con_st_fbid = '$st_fbid';";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setParams($o_ret);		
		return $this;
	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de usuario. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/
	public function save()
	{	
		if(is_null($this->in_id)) {
			$st_query = "INSERT INTO tbl_usuario
						(
							con_st_nome,
							con_st_data_nascimento,
							con_st_genero,
							con_st_email,
							con_st_fbid,
							con_in_id_nivel_acesso
						)
						VALUES
						(
							'$this->st_nome',
							'$this->st_data_nascimento',
							'$this->st_genero',
							'$this->st_email',
							'$this->st_fbid',
							$this->in_id_nivel_acesso
						);";
		} else {
			$st_query = "UPDATE
							tbl_usuario
						SET
							con_st_nome = '$this->st_nome',
							con_st_data_nascimento = '$this->st_data_nascimento',
							con_st_genero = '$this->st_genero',
							con_st_email = '$this->st_email',
							con_st_fbid = '$this->st_fbid',
							con_in_id_nivel_acesso = $this->in_id_nivel_acesso
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
					if($this->o_db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
						$o_ret = $this->o_db->query('SELECT last_insert_rowid() AS com_in_id')->fetchObject();
						$id = $o_ret->com_in_id;
					} else
						$id = $this->o_db->lastInsertId();
					$this->setId($id);
					return $id;
					
				}
				else
					return $this->in_id;
		}
		catch (PDOException $e)
		{ }
		return false;				
	}

	/**
	* Deleta os dados persistidos na tabela de
	* usuario usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->in_id))
		{
			$st_query = "DELETE FROM
							tbl_usuario
						WHERE con_in_id = $this->in_id";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	
	/**
	* Cria tabela para armazernar os dados de usuario, caso
	* ela ainda não exista.
	* @throws PDOException
	*/
	private function createTableUsuario()
	{
		/*
		* No caso do Sqlite, o AUTO_INCREMENT é automático na chave primaria da tabela
		* No caso do MySQL, o AUTO_INCREMENT deve ser especificado na criação do campo
		*/
		if($this->o_db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite')
			$st_auto_increment = '';
		else
			$st_auto_increment = 'AUTO_INCREMENT';
		
		$st_query = "CREATE TABLE IF NOT EXISTS tbl_usuario
					(
						con_in_id INTEGER NOT NULL $st_auto_increment,
						con_st_nome CHAR(200),
						con_st_data_nascimento CHAR(10),
						con_st_genero CHAR(200),
						con_st_email CHAR(200),
						con_st_fbid CHAR(200),
						con_in_id_nivel_acesso INTEGER,
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