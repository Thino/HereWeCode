<?php

require_once 'DB/DalMySql.php';


class PlaceModel
{

   protected static $instance;   
   protected function __construct() { }  
   protected function __clone() { }    
  
  public static function getInstance()
  {	
    if (!isset(self::$instance)) 
    {
      self::$instance = new self; 
    }    
    return self::$instance;
  }	
 
   
   // Research for a specific place
    public function searchPlace()
   { 
		$app = \Slim\Slim::getInstance();
		$body = $app->request()->getBody();
		$input = json_decode($body); 		
		if ( isset($input->query) &&  isset($input->startAt) &&  isset($input->maxResults) && isset($input->approved) && !empty($input->approved))
		{
			if ( $input->startAt >= 0 && $input->startAt < 10000 && $input->maxResults > 0 && $input->maxResults < 10000)
			{				
				if ( $input->approved === "false"   )
				{					
					$headers = apache_request_headers();
					if ( isset($headers['Authorization']))
						$authid = BasicHttpAuthentication::authenticate($headers['Authorization']);
					else
						$authid = -1;				
					if ( ! DalMySql::getInstance()->isAdmin($authid) )
					{
						$app->response()->header('Content-Type', 'application/json');	
						$app->response()->status(401);		
						echo ('{"error":"Only an admin can list not approved places"}');
						return;				
					}
				}	
				
				$result = DalMySql::getInstance()->searchPlace($input->query,$input->startAt,$input->maxResults,$input->approved);
				$app->response()->header('Content-Type', 'application/json');	
				$app->response()->status(200);			
				echo json_encode($result->fetchAll(PDO::FETCH_ASSOC));		
				return;						
			}			
		}	
		$app->response()->header('Content-Type', 'application/json');	
		$app->response()->status(409);		
		echo ('{"error":"Bad json input"}');	
   }
   
   // Add a new place
   public function addPlace()
   { 
		$app = \Slim\Slim::getInstance();
		$body = $app->request()->getBody();
		$input = json_decode($body); 		
		if ( isset($input->name) && !empty($input->name) && isset($input->summary) &&  !empty($input->summary) && isset($input->address) && !empty($input->address))
		{
			$headers = apache_request_headers();
			$idUser = BasicHttpAuthentication::authenticate($headers['Authorization']);
			$result = DalMySql::getInstance()->addPlace($input->name,$input->summary,$input->address,$idUser);
			if ( $result > 0 )
			{
				$app->response()->header('Content-Type', 'application/json');	
				$app->response()->status(201);			
				echo ("{\"id\":\"$result\"}");	
				return;					
			}
			else
			{
				$app->response()->header('Content-Type', 'application/json');	
				$app->response()->status(409);		
				echo ('{"error":"Insert fail ! Maybe name is already used"}');	
				return;	
			}
		}	
		$app->response()->header('Content-Type', 'application/json');	
		$app->response()->status(409);		
		echo ('{"error":"Bad json input ( name, summary and address are NEEDED )"}');	
   }
   
   
   // Return basics informations of a place
   public function getPlaceWithId($id)
   {		
		$app = \Slim\Slim::getInstance();		
		$result = DalMySql::getInstance()->getPlaceWithId($id);	
		if ( $result->rowCount() > 0 )
		{
			$app->response()->header('Content-Type', 'application/json');	
			$app->response()->status(200);			
			echo json_encode($result->fetch(PDO::FETCH_ASSOC));	
		}
		else
		{
			$app->response()->header('Content-Type', 'application/json');	
			$app->response()->status(404);	
			echo ('{"error":"No place were found"}');	
		} 
   }
   
   // Update a place
   public function  updatePlaceWithId($id)
   {		
		$app = \Slim\Slim::getInstance();	
		// Get the Authorization header to verify if user modify his place ( or if he is admin )
		$headers = apache_request_headers();
		$authid = BasicHttpAuthentication::authenticate($headers['Authorization']);
		if ( $authid == $id || DalMySql::getInstance()->isAdmin($authid)  )
		{
			$body = $app->request()->getBody();
			$input = json_decode($body); 		
			if ( isset($input->name) && isset($input->summary) && isset($input->address) && !empty($input->name) && !empty($input->summary) && !empty($input->address) )
			{
				if ( DalMySql::getInstance()->updatePlace($id,$input->name,$input->summary,$input->address) )
				{
					$app->response()->header('Content-Type', 'application/json');	
					$app->response()->status(204);
				}	
				else
				{
					$app->response()->header('Content-Type', 'application/json');	
					$app->response()->status(400);
					echo ('{"error":" This name already exists"}');		
				}				
			}
			else
			{
				$app->response()->header('Content-Type', 'application/json');	
				$app->response()->status(400);	
				echo ('{"error":"Name, Summary and Address are required !"}');			
			}
		}			
		else
		{
			$app->response()->header('Content-Type', 'application/json');	
			$app->response()->status(401);	
			echo ('{"error":"Bad Authorization ( You can only modify a place you have created )"}');	
		}			
   }
   
   // Delete a place
   public function  deletePlaceWithId($id)
   {		
		$app = \Slim\Slim::getInstance();	
		// Get the Authorization header to verify if the user in an admin or can delete his own place
		$headers = apache_request_headers();
		$authid = BasicHttpAuthentication::authenticate($headers['Authorization']);
		if ( $authid == $id || DalMySql::getInstance()->isAdmin($authid)  )
		{
			if ( DalMySql::getInstance()->deletePlace($id) )
			{
				$app->response()->header('Content-Type', 'application/json');	
				$app->response()->status(204);					
			}
			else
			{
				$app->response()->header('Content-Type', 'application/json');	
				$app->response()->status(520);	
				echo ('{"error":"Internal problem with the delete"}');			
			}	
		}
		else
		{
			$app->response()->header('Content-Type', 'application/json');	
			$app->response()->status(401);	
			echo ('{"error":"Bad Authorization ( You can delete only places you have created )"}');	
		}		
   }
   

}

	


