<?php

require_once 'DB/DalMySql.php';

require_once 'GeoCoder/src/autoload.php';


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


	// All exiting places
	public function getPlaces()
	{		
		$app = \Slim\Slim::getInstance();		
		$result = DalMySql::getInstance()->getPlaces();		
		$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
		$app->response()->status(200);			
		$app->response()->body(json_encode($result->fetchAll(PDO::FETCH_ASSOC),JSON_UNESCAPED_UNICODE));		
	}

	// Function to calculate a distance between two points on the earth
	private function getDistance($lat1,$long1,$lat2,$long2)
	{
		$pi80 = M_PI / 180;
		$lat1 *= $pi80;
		$long1 *= $pi80;
		$lat2 *= $pi80;
		$long2 *= $pi80;
		
		$r = 6372.797; // mean radius of Earth in km
		$dlat = $lat2 - $lat1;
		$dlng = $long2 - $long1;
		$a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
		$c = 2 * atan2(sqrt($a), sqrt(1 - $a)); 
		return  $r * $c * 1000;  		
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
				$orderBy = "placeMark";
				if ( isset($input->orderBy) && ( $input->orderBy == 'placeMark' || $input->orderBy == 'distance') )
				$orderBy = $input->orderBy;	
				if ( $input->approved === "false")
				{					
					$headers = apache_request_headers();
					if ( isset($headers['Authorization']))
					$authid = BasicHttpAuthentication::authenticate($headers['Authorization']);
					else
					$authid = -1;	
					
					if ( ! DalMySql::getInstance()->isAdmin($authid) )
					{ 
						$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
						$app->response()->status(401);		
						echo ('{"error":"Only an admin can list not approved places"}');
						return;				
					}
				}	
				$result = DalMySql::getInstance()->searchPlace($input->query,$input->startAt,$input->maxResults,$input->approved);
				$rs = $result->fetchAll(PDO::FETCH_ASSOC);
				
				// Geocoder to locate ip and calculate distance with address		
				$ip = $app->request()->getIp();				
				$geocoder = new \Geocoder\Geocoder();
				$adapter  = new \Geocoder\HttpAdapter\SocketHttpAdapter();
				$chain    = new \Geocoder\Provider\ChainProvider(array(
				new \Geocoder\Provider\GoogleMapsProvider($adapter, 'fr_FR', 'France', true),
				new \Geocoder\Provider\FreeGeoIpProvider($adapter)											
				));
				$geocoder->registerProvider($chain);
				$ret =  array();	
				$i=0;
				
				
				// Locate and calculate marks
				foreach($rs as $row)
				{					
					$georp = $geocoder->geocode($row['address']);					
					$georc = $geocoder->geocode($ip);								
					$distance = $this->getDistance($georp->getLatitude(),$georp->getLongitude(),$georc->getLatitude(),$georc->getLongitude());
					$ar = array( 'idPlace' => $row['idPlace'],
					'name' => $row['name'],
					'summary' => $row['summary'] ,
					'address' => $row['address'],
					'placeMark' => $row['placeMark'],
					'distance' => $distance,
					'approved' => $row['approved'],
					'idMember' => $row['idMember']);
					$i++;
					$ret[$i] = $ar;					
				}
				if ( $orderBy == 'distance' )
				{					
					$sorted = array();
					foreach ($ret as $key => $row)
					{
						$sorted[$key] = $row['distance'];
					}
					array_multisort($sorted, SORT_ASC, $ret);
				}	
				if ( $orderBy == 'placeMark' )
				{
					$sorted = array();
					foreach ($ret as $key => $row)
					{
						$sorted[$key] = $row['placeMark'];
					}
					array_multisort($sorted, SORT_DESC, $ret);
				}
				$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
				$app->response()->status(200);			
				echo json_encode($ret,JSON_UNESCAPED_UNICODE);					
				return;											
			}			
		}	
		$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
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
				$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
				$app->response()->status(201);			
				echo ("{\"id\":\"$result\"}");	
				return;					
			}
			else
			{
				$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
				$app->response()->status(409);		
				echo ('{"error":"Insert fail ! Maybe name is already used"}');	
				return;	
			}
		}	
		$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
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
			$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
			$app->response()->status(200);			
			echo json_encode($result->fetch(PDO::FETCH_ASSOC),JSON_UNESCAPED_UNICODE);	
		}
		else
		{
			$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
			$app->response()->status(404);	
			echo ('{"error":"No place were found"}');	
		} 
	}

	// Return basics informations of a place
	public function  getFacilitiesWithPlaceId($id)
	{		
		$app = \Slim\Slim::getInstance();			
		$result = DalMySql::getInstance()->getFacilitiesWithPlaceId($id);
		$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
		$app->response()->status(200);			
		$ret =  array();
		$rs = $result->fetchAll(PDO::FETCH_ASSOC);
		foreach($rs as $row)
		{
			$note = DalMySql::getInstance()->getMarkForFacilityForPlace($id,$row['idFacility']);
			$mark = $note->fetch(PDO::FETCH_ASSOC);
			$ar = array( 
			'idFacility' => $row['idFacility'],
			'name' => $row['name'],			
			'mark' => $mark['mark']
			);		
			if ( $mark['mark'] <= 1 )
			$ar['icon'] = $row['iconRed'];
			else if ( $mark['mark'] <= 3 )
			$ar['icon'] = $row['iconOrange'];
			else if ( $mark['mark'] <= 5 )
			$ar['icon'] = $row['iconGreen'];
			array_push($ret,$ar);		
			
		}		
		echo json_encode($ret,JSON_UNESCAPED_UNICODE);	
	}

	// Add a facility to a place
	public function addFacilityWithPlaceId($id)
	{
		$app = \Slim\Slim::getInstance();
		$body = $app->request()->getBody();
		$input = json_decode($body,TRUE); 	
		foreach ( $input as $q )
		{	
			$result = DalMySql::getInstance()->addCriteria($id,$q['id'],$q['availability'],$q['free']);	
			if ( $result <= 0 )
			{
				$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
				$app->response()->status(409);		
				echo ('{"error":"Insert fail !"}');	
				return;	
			}			
		}			
		$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
		$app->response()->status(201);	
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
					$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
					$app->response()->status(204);					
				}	
				else
				{
					$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
					$app->response()->status(400);
					echo ('{"error":" This name already exists"}');		
				}				
			}
			else
			{
				$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
				$app->response()->status(400);	
				echo ('{"error":"Name, Summary and Address are required !"}');			
			}
		}			
		else
		{
			$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
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
				$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
				$app->response()->status(204);					
			}
			else
			{
				$app->response()->header('Content-Type','application/json ; charset=utf-8');	
				$app->response()->status(520);	
				echo ('{"error":"Internal problem with the delete"}');			
			}	
		}
		else
		{
			$app->response()->header('Content-Type', 'application/json ; charset=utf-8');	
			$app->response()->status(401);	
			echo ('{"error":"Bad Authorization ( You can delete only places you have created )"}');	
		}		
	}


}


