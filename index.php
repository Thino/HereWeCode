<?php



require_once 'Slim/Slim.php';
require_once 'Models/MemberModel.php';
require_once 'Models/FacilityModel.php';
require_once 'Models/PlaceModel.php';
require_once 'Models/CommentModel.php';
require_once 'Slim/Middleware.php';
require_once 'Slim/Middleware/BasicHttpAuthentication.php';

\Slim\Slim::registerAutoloader();




if (isset($_SERVER['HTTP_ORIGIN']))
{
	header("Access-Control-Allow-Origin: *");
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Max-Age: 86400');    
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
	header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");         

	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
	header("Access-Control-Allow-Headers: Authorization, Content-Type");
} 

$app = new \Slim\Slim();

// Add of routes for the app
require_once 'Routes/routes.php';




$app->run(); 


?>