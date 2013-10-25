<?php
/**
* Essa classe é responsável por renderizar os arquivos HTML
* Diretório Pai - lib  
* @package Exemplo simples com MVC
* @author DigitalDev
* @version 0.1.1
*/
class View
{
	/**
	* Armazena o conteúdo HTML
	* @var string
	*/
	private $st_contents;

		/**
	* Armazena o conteúdo HTML
	* @var string
	*/
	private $st_header;

		/**
	* Armazena o conteúdo HTML
	* @var string
	*/
	private $st_footer;
	
	/**
	* Armazena o nome do arquivo de visualização
	* @var string
	*/
	private $st_view;
	
	/**
	* Armazena os dados que devem ser mostrados ao reenderizar o 
	* arquivo de visualização
	* @var Array
	*/
	private $v_params;
	
	/**
	* 
	* @var string
	*/
	private $view_header;

	/**
	* 
	* @var string
	*/
	private $view_footer;
	
	/**
	* É possivel efetuar a parametrização do objeto ao instanciar o mesmo,
	* $st_view é o nome do arquivo de visualização a ser usado e 
	* $v_params são os dados que devem ser utilizados pela camada de visualização
	* 
	* @param string $st_view
	* @param Array $v_params
	*/
	function __construct($st_view = null, $v_params = null, $view_header="views/header.phtml", $view_footer="views/footer.phtml") 
	{
		if($st_view != null)
			$this->setView($st_view);
		$this->v_params = $v_params;
		$this->view_header = $view_header;
		$this->view_footer = $view_footer;
	}	
	
	/**
	* Define qual arquivo html deve ser renderizado
	* @param string $st_view
	* @throws Exception
	*/
	public function setView($st_view)
	{
		if(file_exists($st_view))
			$this->st_view = $st_view;
		else
			throw new Exception("View File '$st_view' don't exists");		
	}
	
	/**
	* Retorna o nome do arquivo que deve ser renderizado
	* @return string 
	*/
	public function getView()
	{
		return $this->st_view;
	}
	
	/**
	* Define os dados que devem ser repassados à view
	* @param Array $v_params
	*/
	public function setParams(Array $v_params)
	{
		$this->v_params = $v_params;	
	}
	
	/**
	* Retorna os dados que foram ser repassados ao arquivo de visualização
	* @return Array
	*/
	public function getParams()
	{
		return $this->v_params;
	}
	
	/**
	* Retorna uma string contendo todo 
	* o conteudo do arquivo de visualização
	* 
	* @return string
	*/
	public function getContents()
	{
		ob_start();
		if(isset($this->st_view))
			require_once $this->st_view;
		$this->st_contents = ob_get_contents();
		ob_end_clean();
		return $this->st_contents;	
	}

	/**
	* Retorna uma string contendo o header 
	* do conteudo do arquivo de visualização
	* 
	* @return string
	*/
	public function getHeader()
	{
		ob_start();
		require_once $this->view_header;
		$this->st_header = ob_get_contents();
		ob_end_clean();
		return $this->st_header;	
	}

	/**
	* Retorna uma string contendo o footer 
	* do conteudo do arquivo de visualização
	* 
	* @return string
	*/
	public function getFooter()
	{
		ob_start();
		require_once $this->view_footer;
		$this->st_footer = ob_get_contents();
		ob_end_clean();
		return $this->st_footer;	
	}
	
	/**
	* Imprime o arquivo de visualização 
	*/
	public function showContents()
	{
		echo $this->getContents();
		exit;
	}

	public function showPage(){
		echo $this->getHeader(); 
		echo $this->getContents();
		echo $this->getFooter();
		exit;
	}
}
?>