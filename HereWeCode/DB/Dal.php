<?php

class Dal
{

   protected static $instance; 
   
   protected $connection;
  
   protected function __construct()
   {
		try
		{
			$dns = 'mysql:host=localhost;dbname=db_herewecode';
			$utilisateur = 'Elytio';
			$motDePasse = 'tulorapa';
			$this->connection = new PDO( $dns, $utilisateur, $motDePasse );
		} catch ( Exception $e )
		{
			echo "Connection à MySQL impossible : ", $e->getMessage();		
			die();
		}
	}
   
    protected function __clone() { } 
  
  public static function getInstance()
  {
    if (!isset(self::$instance)) 
    {
      self::$instance = new self; 
    }    
    return self::$instance;
  }
	
  public function select($table, $where = '', $fields = '*', $order = '', $limit = null, $offset = null)
  {
	
        $query = 'SELECT ' . $fields . ' FROM ' . $table
               . (($where) ? ' WHERE ' . $where : '')
               . (($limit) ? ' LIMIT ' . $limit : '')
               . (($offset && $limit) ? ' OFFSET ' . $offset : '')
               . (($order) ? ' ORDER BY ' . $order : '')
			   . ';';	
	   //echo  $query;       
	   return $this->connection->query($query);       
  }
  
  
  

}

