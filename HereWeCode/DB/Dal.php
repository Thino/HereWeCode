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
	
  private function select($table, $where = '', $fields = '*', $order = '', $limit = null, $offset = null)
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
  
    private function insert($table, array $data)
    {
        $fields = implode(',', array_keys($data));
        $values = implode(',', array_values($data));
        $query = 'INSERT INTO ' . $table . ' (' . $fields . ') ' . ' VALUES (' . $values . ');';		
		$q = $this->connection->prepare($query);
		$ret = $q->execute();			
		if ( $ret )		
			return $this->connection->lastInsertId(); 
		else
			return -1; 
    }
   
    
    private function update($table, array $data, $where = '')
    {
        $set = array();		
        foreach ($data as $field => $value) {			
            $set[] = $field . '=' . $value;
        }		
        $set = implode(',', $set);
        $query = 'UPDATE ' . $table . ' SET ' . $set . (($where) ? ' WHERE ' . $where : '');
		$q = $this->connection->prepare($query);		
		return $q->execute(); 	
    }
   
   
    private function delete($table, $where = '')
    {
        $query = 'DELETE FROM ' . $table
               . (($where) ? ' WHERE ' . $where : '');
        $q = $this->connection->prepare($query);		
		return $q->execute(); 	
    } 
	
	
	
	 public function getAuthUserId($user, $pass) {
	 
		// No user or/and no password 
		if ( !isset($user) || !isset($pass) )				
			return -2;			
		
		$result = Dal::getInstance()->select('MEMBER',"username = '$user' AND password = '$pass'",'idMember');		
		if ( $result->rowCount() > 0 )
		{
			$row = $result->fetch(PDO::FETCH_ASSOC);		
			return $row['idMember'];
		}
		// Bad user/password combinaison 
		return -3;		 
	 }
	
	
	
   // Return basics informations of a user
   public function getMemberWithId($id)
   {		
		return $this->select('MEMBER',"idMember = $id",'username,picture,isAdmin');			
   }
   
   // Allow to auth a member with username and password 
    public function authMember($username,$password)
   { 
		$resultId = $this->getAuthUserId($username,$password);	
		// if resultId is an error code, we return an empty set
		return $this->select('MEMBER',"idMember = $resultId",'idMember,username,picture,isAdmin');			
   }
   
   // Add a member in the database 
   public function addMember($username,$password,$picture)
   { 	
		return $this->insert('MEMBER',array("username"=>"'$username'","password"=>"'$password'","picture"=>"'$picture'","isAdmin"=>"false"));	
   }
   
   // Verify if a user is an admin
   public function isAdmin($id)
   { 
		$result = $this->select('MEMBER',"idMember = $id",'isAdmin');		
		if ( $result->rowCount() > 0 )
		{
			$row = $result->fetch(PDO::FETCH_ASSOC);		
			return $row['isAdmin'];
		}
		return false;		
   }
   
   
   public function updateMember($id,$password,$picture)
   {
		if ( empty($password) )		
			return $this->update('MEMBER',array("picture"=>"'$picture'"),"idMember = $id");	
		return $this->update('MEMBER',array("password"=>"'$password'","picture"=>"'$picture'"),"idMember = $id");						
   }
   
    public function deleteMember($id)
   {		
		return $this->delete('MEMBER',"idMember = $id");				
   }
   
   
   
   
   
   
   
  


 //$result = Dal::getInstance()->insert('MEMBER',array("username"=>"'moi'","password"=>"'pass'","picture"=>"'lllll'","isAdmin"=>"false"));	// INSERT. Retourn id inséré ou -1 si erreur
 //Dal::getInstance()->update('MEMBER',array("username"=>"'moiiiiiii'","password"=>"'passi'"),'idMember = 13') // UPDATE retourne true si ok false sinon
//Dal::getInstance()->delete('MEMBER','idMember = 13') // DELETE true et false	  
   
   



	




  
  

}

