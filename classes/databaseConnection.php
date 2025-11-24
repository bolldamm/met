<?php 
	/**
	 * 
	 * Clase que incorpora todas las propiedades y metodos para poder interactuar con la base de datos.
	 * @author eData S.L.
	 * @version 4.0
	 * 
	 */

	class DatabaseConnection{
		
		/**
		 * 
		 * Nos indica el host donde se encuentra la base de datos
		 * @var string
		 * 
		 */
		private $host;
		
		/**
		 * 
		 * Nos indica el usuario que se utiliza para conectarse a la base de datos
		 * @var string
		 * 
		 */
		private $user;
		
		/**
		 * 
		 * Nos indica el password que utiliza el usuario para conectarse a la base de datos
		 * @var string
		 * 
		 */
		private $password;
		
		/**
		 * 
		 * La base de datos a la cual nos queremos conectar
		 * @var string
		 * 
		 */
		private $dataBase;
		
		/**
		 * 
		 * Almacenamos el identificador de la clase mysqli, que es la que nos permite interactuar directamente a la base de datos
		 * @var resource
		 * 
		 */
		private $link;
		
		/**
		 * 
		 * Realizamos la instancia de la clase databaseConnection
		 * @param string $host
		 * @param string $user
		 * @param string $password
		 * @param string $dataBase
		 * 
		 */
		function __construct($host,$user,$password,$dataBase){
			$this->host=$host;
			$this->user=$user;
			$this->password=$password;
			$this->dataBase=$dataBase;
		}
	
		/**
		 * 
		 * Realizamos la conexion a la base de datos mediante la clase mysqli
		 * 
		 */
		function connect(){
			$this->link=new mysqli($this->host,$this->user,$this->password,$this->dataBase);
			$this->link->set_charset("utf8");
		}		
		
		/**
		 * 
		 * Nos permite hacer llamadas de store procedures y functions de mysql
		 * @example Si quisiesemos llamar a un store que nos devolviese una persona a traves de su id, el valor de
		 * $storeName seria el siguiente: CALL ed_sp_obtener_persona($id_persona), donde id_persona es la variable que se ha declarado
		 * para almacenar el id de persona
		 * @param string $storeName
		 */
		
		function callProcedure($storeName){
			$result=$this->link->query($storeName) or die($this->link->error);
			if($this->link->more_results()){
				$this->link->next_result();
			}
			return $result;
		}
		
		function getData($result){
			return $result->fetch_assoc();
		}
		
		
		/**
		 * 
		 * Nos retorna el numero de registros devueltos por la base de datos al atributo $result
		 * 
		 */
		
		function getNumberRows($result){
			return $result->num_rows;
		}
		
		
		/**
		 * 
		 * Establece una transaccion
		 * 
		 */
		function startTransaction(){
			$this->link->query("START TRANSACTION");
		}
		
		
		/**
		 * 
		 * Cierra una transaccion
		 * 
		 */
		
		function endTransaction(){
			$this->link->query("COMMIT");
		}
		
		
		/**
		 * 
		 * Realizamos la desconexion a la base de datos mediante la clase mysqli
		 * 
		 */
		function disconnect(){
			$this->link->close();
		}
	
	}
?>