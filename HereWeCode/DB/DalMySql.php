<?php

class DalMySql
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
        $query = 'UPDATE ' . $table . ' SET ' . $set . (($where) ? ' WHERE ' . $where : '') . ';';
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
	
	
	
	/////////////////////////////////////////////////////////
	///////////////////// MEMBER ////////////////////////////
	/////////////////////////////////////////////////////////
	
	
	 public function getAuthUserId($user, $pass) {
	 
		// No user or/and no password 
		if ( !isset($user) || !isset($pass) )				
			return -2;			
		
		$result = $this->select('MEMBER',"username = '$user' AND password = '$pass'",'idMember');		
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
   
   	
	/////////////////////////////////////////////////////////
	///////////////////// FACILITY //////////////////////////
	/////////////////////////////////////////////////////////
	
	 public function getFacilities() {	 
		return $this->select('FACILITY');	
	}
	
    // Add a facility in the database 
	public function addFacility($name,$iconNoItem,$iconRed,$iconOrange,$iconGreen)
	{ 	
		return $this->insert('FACILITY',array("name"=>"'$name'","iconNoItem"=>"'$iconNoItem'","iconRed"=>"'$iconRed'","iconOrange"=>"'$iconOrange'","iconGreen"=>"'$iconGreen'"));	
	}
	
	// Return basics informations of a user
    public function getFacilityWithId($id)
    {		
		return $this->select('FACILITY',"idFacility = $id");			
    }
	
	public function updateFacility($id,$name,$no,$red,$orange,$green)
   {		
		return $this->update('FACILITY',array("name"=>"'$name'","iconNoItem"=>"'$no'","iconRed"=>"'$red'","iconOrange"=>"'$orange'","iconGreen"=>"'$green'"),"idFacility = $id");						
   }
   
    public function deleteFacility($id)
   {		
		return $this->delete('FACILITY',"idFacility = $id");				
   }
   
   
   /////////////////////////////////////////////////////////
   ///////////////////// PLACE /////////////////////////////
   /////////////////////////////////////////////////////////
  
	// Search a place in database 
	public function searchPlace($toSearch,$startAt,$maxResults,$isValidate)
	{ 	
		return $this->select('PLACE',"(name LIKE '%$toSearch%' OR summary LIKE '%$toSearch%' OR address LIKE '%$toSearch%' ) AND approved = $isValidate ",'idPlace, name, summary, address, approved','',"$maxResults","$startAt");		
	}
	
	// Add a place in the database 
   public function addPlace($name,$summary,$address,$member)
   { 	
		return $this->insert('PLACE',array("name"=>"'$name'","summary"=>"'$summary'","address"=>"'$address'","approved"=>"false","idMember"=>"$member"));	
   }
   
   // Return basics informations of a place
    public function getPlaceWithId($id)
    {		
		return $this->select('PLACE',"idPlace = $id","idPlace,name,summary,address");			
    }
	
	//Update a place
	public function updatePlace($id,$name,$summary,$address)
    {		
		return $this->update('PLACE',array("name"=>"'$name'","summary"=>"'$summary'","address"=>"'$address'"),"idPlace = $id");						
    }
	
	 public function deletePlace($id)
    {		
		return $this->delete('PLACE',"idPlace = $id");				
    }
 
   /////////////////////////////////////////////////////////
   ///////////////////// COMMENT ///////////////////////////
   /////////////////////////////////////////////////////////
 
 	public function getComments($idPlace) {	 
		return $this->select('COMMENT',"idPlace = $idPlace");	
	}
  
	public function addCommentToPlace($text,$idMember,$idPlace)
    { 	
		$date = date('Y-m-d H:i:s');
		$text = $this->connection->quote($text);
		return $this->insert('COMMENT',array("text"=>"$text","date"=>"'$date'","idMember"=>$idMember,"idPlace"=>$idPlace));	
    }
	
	public function getCommentWithId($id)
    {		
		return $this->select('COMMENT',"idComment = $id","idComment,idMember,date,text");			
    }
	
	public function updateComment($id,$text)
    {	
		$text = $this->connection->quote($text);
		return $this->update('COMMENT',array("text"=>"$text"),"idComment = $id");						
    }
	
	 public function deleteComment($id)
    {		
		return $this->delete('COMMENT',"idComment = $id");				
    }

}