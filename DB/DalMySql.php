<?php

require_once 'DB/DalInterface.php';

class DalMySql implements DalInterface
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
			$this->connection = new PDO( $dns, $utilisateur, $motDePasse, array( PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );
			$this->connection->exec("SET CHARACTER SET utf8");
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

	//SELECT requests
	private function select($table, $where = '', $fields = '*', $order = '', $limit = null, $offset = null)
	{	
		$query = 'SELECT ' . $fields . ' FROM ' . $table
		. (($where) ? ' WHERE ' . $where : '')              
		. (($order) ? ' ORDER BY ' . $order : '')
		. (($limit) ? ' LIMIT ' . $limit : '')
		. (($offset && $limit) ? ' OFFSET ' . $offset : '')
		. ';';	
		
		return $this->connection->query($query);       
	}
	//INSERT REQUESTS
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

	// UPDATE REQUESTS
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

	// DELETE requests
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
		
		$user = $this->connection->quote($user);
		$pass = $this->connection->quote($pass);		
		$result = $this->select('MEMBER',"username = $user AND password = $pass",'idMember');		
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
		return $this->select('MEMBER',"idMember = $id",'idMember,username,picture,isAdmin');			
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
		$username = $this->connection->quote($username);
		$password = $this->connection->quote($password);
		$picture = $this->connection->quote($picture);
		return $this->insert('MEMBER',array("username"=>"$username","password"=>"$password","picture"=>"$picture","isAdmin"=>"false"));	
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

	// Update a member ( just password and picture )
	public function updateMember($id,$password,$picture)
	{
		$password = $this->connection->quote($password);
		$picture = $this->connection->quote($picture);
		return $this->update('MEMBER',array("password"=>"$password","picture"=>"$picture"),"idMember = $id") ;
	}

	// Delete a member
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
		$name = $this->connection->quote($name);
		$iconNoItem = $this->connection->quote($iconNoItem);
		$iconRed = $this->connection->quote($iconRed);
		$iconOrange = $this->connection->quote($iconOrange);
		$iconGreen = $this->connection->quote($iconGreen); 
		return $this->insert('FACILITY',array("name"=>"$name","iconNoItem"=>"$iconNoItem","iconRed"=>"$iconRed","iconOrange"=>"$iconOrange","iconGreen"=>"$iconGreen"));	
	}
	
	// Return basics informations of a user
	public function getFacilityWithId($id)
	{		
		return $this->select('FACILITY',"idFacility = $id");			
	}
	
	//Update a facility ( name and icons )
	public function updateFacility($id,$name,$no,$red,$orange,$green)
	{
		$name = $this->connection->quote($name);
		$no = $this->connection->quote($no);
		$red = $this->connection->quote($red);
		$orange = $this->connection->quote($orange);
		$green = $this->connection->quote($green);   
		return $this->update('FACILITY',array("name"=>"$name","iconNoItem"=>"$no","iconRed"=>"$red","iconOrange"=>"$orange","iconGreen"=>"$green"),"idFacility = $id");						
	}

	//Delete
	public function deleteFacility($id)
	{		
		return $this->delete('FACILITY',"idFacility = $id");				
	}


	/////////////////////////////////////////////////////////
	///////////////////// PLACE /////////////////////////////
	/////////////////////////////////////////////////////////

	// All places approved
	public function getPlaces() {	 
		return $this->select('PLACE',"approved = true");		
	}  


	// Search a place in database 
	public function searchPlace($toSearch,$startAt,$maxResults,$isValidate)
	{
		$toSearch = '%'.$toSearch.'%';
		$toSearch = $this->connection->quote($toSearch);		
		return $this->select('PLACE,COMMENT',"(name LIKE $toSearch OR summary LIKE $toSearch OR address LIKE $toSearch ) AND approved = $isValidate AND COMMENT.idPlace = PLACE.idPlace GROUP BY PLACE.idPlace",'PLACE.idPlace,PLACE.name,PLACE.summary,PLACE.address, AVG(COMMENT.placeMark) as placeMark,PLACE.approved,PLACE.idMember','placeMark DESC',"$maxResults","$startAt");		
	}
	
	
	// Add a place in the database 
	public function addPlace($name,$summary,$address,$member)
	{ 
		$name = $this->connection->quote($name);
		$summary = $this->connection->quote($summary);
		$address = $this->connection->quote($address);   
		return $this->insert('PLACE',array("name"=>"$name","summary"=>"$summary","address"=>"$address","approved"=>"false","idMember"=>"$member"));	
	}

	// Return basics informations of a place
	public function getPlaceWithId($id)
	{		
		return $this->select('PLACE',"idPlace = $id","idPlace,name,summary,address");			
	}
	
	// Approve a place 
	public function approvePlace($id)
	{		
		return $this->update('PLACE',array("approved"=>"true"),"idPlace = $id");			
	}
	
	//Update a place
	public function updatePlace($id,$name,$summary,$address)
	{		
		$name = $this->connection->quote($name);
		$summary = $this->connection->quote($summary);
		$address = $this->connection->quote($address);
		return $this->update('PLACE',array("name"=>"$name","summary"=>"$summary","address"=>"$address"),"idPlace = $id");						
	}
	
	// Delete a place
	public function deletePlace($id)
	{		
		return $this->delete('PLACE',"idPlace = $id");		
	}
	
	// Get all the facilities for a place
	public function  getFacilitiesWithPlaceId($id)
	{
		return $this->select('PLACE,CRITERIA,FACILITY',"CRITERIA.idPlace = PLACE.idPlace AND FACILITY.idFacility = CRITERIA.idFacility AND PLACE.idPlace = $id GROUP BY FACILITY.idFacility","FACILITY.idFacility,FACILITY.name, FACILITY.iconGreen, FACILITY.iconOrange, FACILITY.iconRed");   	
		
	}
	
	// Get mark for a facility in a place
	public function getMarkForFacilityForPlace($idPlace,$idFacility)
	{
		return $this->select('PLACE,COMMENT,MARK,FACILITY',"COMMENT.idPlace = PLACE.idPlace AND MARK.idComment = COMMENT.idComment AND FACILITY.idFacility = MARK.idFacility AND PLACE.idPlace = $idPlace AND FACILITY.idFacility = $idFacility GROUP BY FACILITY.idFacility","AVG(MARK.mark) as mark");   	
	}
	
	// Add a criteria to a place with a facility
	public function addCriteria($idPlace,$idFacility,$availability,$free)
	{
		return $this->insert('CRITERIA',array("idFacility"=>"$idFacility","idPlace"=>"$idPlace","availability"=>"$availability","free"=>"$free"));	
	}

	/////////////////////////////////////////////////////////
	///////////////////// COMMENT ///////////////////////////
	/////////////////////////////////////////////////////////
	
	// return all comments for a place
	public function getComments($idPlace) {	 
		return $this->select('COMMENT, MEMBER',"idPlace = $idPlace AND COMMENT.idMember = MEMBER.idMember","COMMENT.idComment, COMMENT.placeMark, COMMENT.date, COMMENT.text, COMMENT.idMember, MEMBER.username ");	
	}

	// Create a comment for a place
	public function addCommentToPlace($text,$idMember,$idPlace)
	{ 	
		$date = date('Y-m-d H:i:s');
		$text = $this->connection->quote($text);
		return $this->insert('COMMENT',array("text"=>"$text","date"=>"'$date'","idMember"=>$idMember,"idPlace"=>$idPlace));	
	}
	
	// get specific comment
	public function getCommentWithId($id)
	{		
		return $this->select('COMMENT',"idComment = $id","idComment,idMember,date,text");			
	}
	
	// Update comment
	public function updateComment($id,$text)
	{	
		$text = $this->connection->quote($text);
		return $this->update('COMMENT',array("text"=>"$text"),"idComment = $id");						
	}
	
	// and delete it
	public function deleteComment($id)
	{		
		return $this->delete('COMMENT',"idComment = $id");				
	}

}