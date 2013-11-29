<?php
require_once 'models/NivelModel.php';
require_once 'models/MidiaGestoModel.php';

class QuestaoModel extends PersistModelAbstract
{
	private $in_id;
	private $st_titulo;
	private $in_id_nivel;
	private $in_id_midia_gesto;
	private $in_ordem;
	
	function __construct()
	{
		parent::__construct();
		$this->createTableQuestao();
	}
	
	private function setParams($obj)
	{
		$this->setId($obj->con_in_id);
		$this->setTitulo($obj->con_st_titulo);
		$this->setIdNivel($obj->con_in_id_nivel);
		$this->setIdMidiaGesto($obj->con_in_id_midia_gesto);
		$this->setOrdem($obj->con_in_ordem);
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
	
	public function setIdMidiaGesto( $in_id_midia_gesto )
	{
		$this->in_id_midia_gesto = $in_id_midia_gesto;
		return $this;
	}
	
	public function getIdMidiaGesto()
	{
		return $this->in_id_midia_gesto;
	}
	
	public function getMidiaGesto()
	{
		if (!DataValidator::isNumeric($this->in_id_midia_gesto) || $this->in_id_midia_gesto <= 0)
			return null;
		
		$nivel = new MidiaGestoModel();
		return  $nivel->loadById($this->in_id_midia_gesto);
	}
	
	public function setOrdem( $in_ordem )
	{
		$this->in_ordem = $in_ordem;
		return $this;
	}
	
	public function getOrdem()
	{
		return $this->in_ordem;
	}
	
	/**
	* Retorna um array contendo os questoes
	* @param string $st_titulo
	* @return Array
	*/
	public function _list( $st_titulo = null )
	{
		if(!is_null($st_titulo))
			$st_query = "SELECT * FROM tbl_questao WHERE con_st_titulo LIKE '%$st_titulo%';";
		else
			$st_query = 'SELECT * FROM tbl_questao;';	
		
		$v_questoes = array();
		try
		{
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject())
			{
				$o_questao = new QuestaoModel();
				$o_questao->setParams($o_ret);
				array_push($v_questoes, $o_questao);
			}
		}
		catch(PDOException $e)
		{}				
		return $v_questoes;
	}
	
	/**
	* Retorna os dados de um questao referente
	* a um determinado Id
	* @param integer $in_id
	* @return QuestaoModel
	*/
	public function loadById( $in_id )
	{
		$v_questoes = array();
		$st_query = "SELECT * FROM tbl_questao WHERE con_in_id = $in_id;";
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		$this->setParams($o_ret);		
		return $this;
	}
	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de questao. Se o ID for passado,
	* um UPDATE será executado, caso contrário, um
	* INSERT será executado
	* @throws PDOException
	* @return integer
	*/
	public function save()
	{
		if(is_null($this->in_id)) {
			$st_query = "INSERT INTO tbl_questao
						(
							con_st_titulo,
							con_in_id_nivel,
							con_in_id_midia_gesto,
							con_in_ordem
						)
						VALUES
						(
							'$this->st_titulo',
							$this->in_id_nivel,
							$this->in_id_midia_gesto,
							$this->in_ordem
						);";
		} else {
			$st_query = "UPDATE
							tbl_questao
						SET
							con_st_titulo = '$this->st_titulo',
							con_in_id_nivel = $this->in_id_nivel,
							con_in_id_midia_gesto = $this->in_id_midia_gesto,
							con_in_ordem = $this->in_ordem
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
	* questao usando como referencia, o id da classe.
	*/
	public function delete()
	{
		if(!is_null($this->in_id))
		{
			$st_query = "DELETE FROM
							tbl_questao
						WHERE con_in_id = $this->in_id";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	
	/**
	* Cria tabela para armazernar os dados de questao, caso
	* ela ainda não exista.
	* @throws PDOException
	*/
	private function createTableQuestao()
	{
		/*
		* No caso do Sqlite, o AUTO_INCREMENT é automático na chave primaria da tabela
		* No caso do MySQL, o AUTO_INCREMENT deve ser especificado na criação do campo
		*/
		if($this->o_db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite')
			$st_auto_increment = '';
		else
			$st_auto_increment = 'AUTO_INCREMENT';
		
		$st_query = "CREATE TABLE IF NOT EXISTS tbl_questao
					(
						con_in_id INTEGER NOT NULL $st_auto_increment,
						con_st_titulo CHAR(200),
						con_in_id_nivel INTEGER,
						con_in_id_midia_gesto INTEGER,
						con_in_ordem INTEGER,
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