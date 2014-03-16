<?php


require_once 'DB/DalMySql.php';



class DalTests extends PHPUnit_Framework_TestCase
{
	
	//Run tests with default database db_herewecode.sql
	
	public function testGetAuthUserId()
	{
		$result = DalMySql::getInstance()->getAuthUserId("Elytio","dHVsb3JhcGE="); 		
		$this->assertEquals($result,1);
	}


	
	/////////////////////////////////////////////////////////
	///////////////////// MEMBER ////////////////////////////
	/////////////////////////////////////////////////////////


	public function testGetMemberWithId()
	{
		$result = DalMySql::getInstance()->getMemberWithId(1); 	
		$this->assertEquals(json_encode($result->fetch(PDO::FETCH_ASSOC)),'{"idMember":"1","username":"Elytio","picture":"nothing","isAdmin":"1"}');				
	}


	public function testAuthMember()
	{
		$result = DalMySql::getInstance()->authMember("Elytio","dHVsb3JhcGE=");   
		$this->assertEquals(json_encode($result->fetch(PDO::FETCH_ASSOC)),'{"idMember":"1","username":"Elytio","picture":"nothing","isAdmin":"1"}');	
	}


	public function testAddMember()
	{
		$result = DalMySql::getInstance()->addMember("Elytioo","dHVsb3JhcGE=","photo !");   
		$this->assertEquals($result,3);	
		$result = DalMySql::getInstance()->addMember("Elytioo","dHVsb3JhcGE=","photo !");   
		$this->assertEquals($result,-1);	
	}

	public function testIsAdmin()
	{
		$result = DalMySql::getInstance()->isAdmin(1);   
		$this->assertEquals($result,true);		
	}

	public function testUpdateMember()
	{
		$result = DalMySql::getInstance()->updateMember(1,"dHVsb3JhcGE=","photo 2 !");   
		$this->assertEquals($result,true);	
		
	}

	public function testDeleteMember()
	{
		$result = DalMySql::getInstance()->deleteMember(2);   
		$this->assertEquals($result,true);						
	}

	/////////////////////////////////////////////////////////
	///////////////////// FACILITY //////////////////////////
	/////////////////////////////////////////////////////////

	public function testGetFacilities() {

		$result = DalMySql::getInstance()->getFacilities(); 	
		$this->assertEquals(json_encode($result->fetch(PDO::FETCH_ASSOC)),'{"idFacility":"1","name":"Wifi","iconNoItem":"http:\/\/78.124.135.14\/HereWeCode\/img\/NoWifi.jpg","iconRed":"http:\/\/78.124.135.14\/HereWeCode\/img\/RedWifi.jpg","iconOrange":"http:\/\/78.124.135.14\/HereWeCode\/img\/OrangeWifi.jpg","iconGreen":"http:\/\/78.124.135.14\/HereWeCode\/img\/GreenWifi.jpg"}');	
		$this->assertEquals(json_encode($result->fetch(PDO::FETCH_ASSOC)),'{"idFacility":"2","name":"Comfort","iconNoItem":"http:\/\/78.124.135.14\/HereWeCode\/img\/NoChair.jpg","iconRed":"http:\/\/78.124.135.14\/HereWeCode\/img\/RedChair.jpg","iconOrange":"http:\/\/78.124.135.14\/HereWeCode\/img\/OrangeChair.jpg","iconGreen":"http:\/\/78.124.135.14\/HereWeCode\/img\/GreenChair.jpg"}');		
		
	}	
	
	public function testAddFacility()
	{ 
		$result = DalMySql::getInstance()->addFacility('test','test','test','test','test'); 	
		$this->assertEquals($result,6);
		$result = DalMySql::getInstance()->addFacility('test','test','test','test','test'); 	
		$this->assertEquals($result,-1); // Name must be unique
		
	}

	
	public function testGetFacilityWithId()
	{		
		$result = DalMySql::getInstance()->getFacilityWithId(6); 	
		$this->assertEquals(json_encode($result->fetch(PDO::FETCH_ASSOC)),'{"idFacility":"6","name":"test","iconNoItem":"test","iconRed":"test","iconOrange":"test","iconGreen":"test"}');			
	}
	
	
	public function testUpdateFacility()
	{
		$result = DalMySql::getInstance()->updateFacility('6','test1','test1','test1','test1','test1');
		$this->assertEquals($result,true);
		$result = DalMySql::getInstance()->getFacilityWithId(6); 	
		$this->assertEquals(json_encode($result->fetch(PDO::FETCH_ASSOC)),'{"idFacility":"6","name":"test1","iconNoItem":"test1","iconRed":"test1","iconOrange":"test1","iconGreen":"test1"}');					
	}   

	public function testDeleteFacility()
	{		
		$result = DalMySql::getInstance()->deleteFacility(6);
		$this->assertEquals($result,true);				
	}

	/////////////////////////////////////////////////////////
	///////////////////// PLACE /////////////////////////////
	/////////////////////////////////////////////////////////

	
	public function testGetPlaces() {

		$result = DalMySql::getInstance()->getPlaces();
		$this->assertEquals(json_encode($result->fetchAll(PDO::FETCH_ASSOC),JSON_UNESCAPED_UNICODE),'[{"idPlace":"1","name":"Mc Donalds","summary":"Un bon vieux Macdo situé à la Pardieu à Clermont-Ferrand. Tout neuf et assez sympa !","address":"La pardieu 63000 Clermont-Ferrand","approved":"1","idMember":"1"},{"idPlace":"2","name":"ISIMA","summary":"Ecole d\'ingé de Clermont ! Lieu spécial geeks !","address":"Campus des Cézeaux 63000 Clermont-Ferrand","approved":"1","idMember":"2"}]');	
		
	}   
	
	public function testSearchPlace()
	{
		$result = DalMySql::getInstance()->searchPlace("MACDO",0,10,true);
		$this->assertEquals(json_encode($result->fetchAll(PDO::FETCH_ASSOC),JSON_UNESCAPED_UNICODE),'[{"idPlace":"1","name":"Mc Donalds","summary":"Un bon vieux Macdo situé à la Pardieu à Clermont-Ferrand. Tout neuf et assez sympa !","address":"La pardieu 63000 Clermont-Ferrand","placeMark":"4","approved":"1","idMember":"1"}]');	
	}	
	
	public function testAddPlace()
	{ 
		$result = DalMySql::getInstance()->addPlace("KFC","test","test","2");
		$this->assertEquals($result,3);	
		$result = DalMySql::getInstance()->addPlace("KFC","test","test","2");
		$this->assertEquals($result,-1);	
	}

	
	public function testGetPlaceWithId()
	{		
		$result = DalMySql::getInstance()->getPlaceWithId(3);
		$this->assertEquals(json_encode($result->fetch(PDO::FETCH_ASSOC),JSON_UNESCAPED_UNICODE),'{"idPlace":"3","name":"KFC","summary":"test","address":"test"}');			
	}	
	
	public function testApprovePlace()
	{		
		$result = DalMySql::getInstance()->approvePlace(3);
		$this->assertEquals($result,true);			
	}
	
	public function testUpdatePlace()
	{		
		$result = DalMySql::getInstance()->updatePlace(3,"KFC","test1","test1");
		$this->assertEquals($result,true);
		$result = DalMySql::getInstance()->getPlaceWithId(3);
		$this->assertEquals(json_encode($result->fetch(PDO::FETCH_ASSOC),JSON_UNESCAPED_UNICODE),'{"idPlace":"3","name":"KFC","summary":"test1","address":"test1"}');			
	}	
	
	public function testAddCriteria()
	{
		$result = DalMySql::getInstance()->addCriteria(3,1,true,true);
		$this->assertEquals($result,11);
	}
	
	public function testDeletePlace()
	{		
		$result = DalMySql::getInstance()->deletePlace(3);
		$this->assertEquals($result,true);
	}	
	
	public function testGetMarkForFacilityForPlace()
	{
		$result = DalMySql::getInstance()->getMarkForFacilityForPlace(2,1);
		$this->assertEquals($result->fetch(PDO::FETCH_ASSOC)['mark'],5.0);
	}   
	
	/////////////////////////////////////////////////////////
	///////////////////// COMMENT ///////////////////////////
	/////////////////////////////////////////////////////////
	
	
	public function testGetComments() {	 
		$result = DalMySql::getInstance()->getComments(1);
		$this->assertEquals(json_encode($result->fetchAll(PDO::FETCH_ASSOC),JSON_UNESCAPED_UNICODE),'[{"idComment":"1","placeMark":"4","date":"2013-12-31","text":"Je kiffe le MacDo =D","idMember":"1","username":"Elytio"}]');			
	}  
	
	public function testAddCommentToPlace()
	{ 	
		$result = DalMySql::getInstance()->addCommentToPlace("test",1,1);
		$this->assertEquals($result,5);			
	}
	
	
	public function testGetCommentWithId()
	{		
		$result = DalMySql::getInstance()->getCommentWithId(2);
		$this->assertEquals(json_encode($result->fetch(PDO::FETCH_ASSOC),JSON_UNESCAPED_UNICODE),'false');		
	}	
	
	public function testUpdateComment()
	{	
		$result = DalMySql::getInstance()->updateComment(5,"coucou");
		$this->assertEquals($result,true);			
	}
	
	
	public function testDeleteComment()
	{		
		$result = DalMySql::getInstance()->deleteComment(5);
		$this->assertEquals($result,true);						
	}
	
	
	
}









