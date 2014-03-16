<?php


interface DalInterface
{  
	
	/////////////////////////////////////////////////////////
	///////////////////// MEMBER ////////////////////////////
	/////////////////////////////////////////////////////////
	
	
	public function getAuthUserId($user, $pass);	
	
	public function getMemberWithId($id);   

	public function authMember($username,$password);
	
	public function addMember($username,$password,$picture);
	
	public function isAdmin($id); 

	public function updateMember($id,$password,$picture);
	
	public function deleteMember($id);
	
	/////////////////////////////////////////////////////////
	///////////////////// FACILITY //////////////////////////
	/////////////////////////////////////////////////////////
	
	public function getFacilities();	
	
	public function addFacility($name,$iconNoItem,$iconRed,$iconOrange,$iconGreen);	
	
	public function getFacilityWithId($id);   
	
	public function updateFacility($id,$name,$no,$red,$orange,$green);
	
	public function deleteFacility($id);
	

	/////////////////////////////////////////////////////////
	///////////////////// PLACE /////////////////////////////
	/////////////////////////////////////////////////////////

	public function getPlaces();
	
	public function searchPlace($toSearch,$startAt,$maxResults,$isValidate);	
	
	public function addPlace($name,$summary,$address,$member);   

	public function getPlaceWithId($id);
	
	public function updatePlace($id,$name,$summary,$address);
	
	public function deletePlace($id);
	
	public function  getFacilitiesWithPlaceId($id);
	
	public function getMarkForFacilityForPlace($idPlace,$idFacility);
	
	public function addCriteria($idPlace,$idFacility,$availability,$free);

	
	/////////////////////////////////////////////////////////
	///////////////////// COMMENT ///////////////////////////
	/////////////////////////////////////////////////////////

	public function getComments($idPlace);

	public function addCommentToPlace($text,$idMember,$idPlace);
	
	public function getCommentWithId($id);
	
	public function updateComment($id,$text); 
	
	public function deleteComment($id);  

}

