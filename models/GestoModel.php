<?php
/**
 * 
 * Responsável por gerenciar e persistir os dados de Gestos dos
 * Contatos da Agenda Telefonica
 * 
 * Camada - models ou modelo
 * Diretório Pai - models
 * Arquivo - GestoModel.php
 * 
 * @author DigitalDev
 * @version 0.1.1
 *
 */
class GestoModel extends PersistModelAbstract
{
	private $in_id;
	private $st_nome;
	private $st_json;
	
	function __construct()
	{
		parent::__construct();
		
		//executa método de criação da tabela de Gesto
		$this->createTableGesto();
	}
	
	
	/**
	 * Setters e Getters da
	 * classe GestoModel
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
	
	public function setJson( $st_json )
	{
		$this->st_json = $st_json;
		return $this;
	}
	
	public function getJson()
	{
		return $this->st_json;
	}
		
	
	/**
	* Retorna um array contendo os Gestos
	* @return Array
	*/
	public function _list( )
	{
		$st_query = "SELECT * FROM tbl_Gesto ";
		$v_Gestos = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_Gesto = new GestoModel();
				$o_Gesto->setId($o_ret->ges_in_id);
				$o_Gesto->setNome($o_ret->ges_st_nome);
				$o_Gesto->setJson($o_ret->ges_st_json);
				array_push($v_Gestos,$o_Gesto);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_Gestos;
	}
	
	/**
	* Retorna os dados de um Gesto referente
	* a um determinado Id
	* @param integer $in_id
	* @return GestoModel
	*/
	public function loadById( $in_id )
	{
		$v_contatos = array();
		$st_query = "SELECT * FROM tbl_Gesto WHERE ges_in_id = $in_id;";
		try 
		{
			$o_data = $this->o_db->query($st_query);
			$o_ret = $o_data->fetchObject();
			$this->setId($o_ret->ges_in_id);
			$this->setNome($o_ret->ges_st_nome);
			$this->setJson($o_ret->ges_st_json);
			return $this;
		}
		catch(PDOException $e)
		{}
		return false;	
	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de Gesto. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/
	public function save()
	{
		if(is_null($this->in_id))
			$st_query = "INSERT INTO tbl_Gesto
						(

							ges_st_nome,
							ges_st_json
						)
						VALUES
						(

							'$this->st_nome',
							'$this->st_json'
						);";
		else
			$st_query = "UPDATE
							tbl_Gesto
						SET
							ges_st_nome = '$this->st_nome',
							ges_st_json = '$this->st_json'
						WHERE
							ges_in_id = $this->in_id";
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
						$o_ret = $this->o_db->query('SELECT last_insert_rowid() AS ges_in_id')->fetchObject();
						return $o_ret->ges_in_id;
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
	* Gesto usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->in_id))
		{
			$st_query = "DELETE FROM
							tbl_Gesto
						WHERE ges_in_id = $this->in_id";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	
	
	
	/**
	* 
	* Cria tabela para armazernar os dados de Gesto, caso
	* ela ainda não exista.
	* @throws PDOException
	*/
	private function createTableGesto()
	{
		/*
		* No caso do Sqlite, o AUTO_INCREMENT é automático na chave primaria da tabela
		* No caso do MySQL, o AUTO_INCREMENT deve ser especificado na criação do campo
		*/
		if($this->o_db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite')
			$st_auto_increment = '';
		else
			$st_auto_increment = 'AUTO_INCREMENT';
		
		
		$st_query = "CREATE TABLE IF NOT EXISTS tbl_Gesto
					(
						ges_in_id INTEGER NOT NULL $st_auto_increment,
						ges_st_nome CHAR(255),
						ges_st_json TEXT,
						PRIMARY KEY(ges_in_id)
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