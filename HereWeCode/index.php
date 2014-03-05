<?php
	
	 if (isset($_SERVER['HTTP_ORIGIN'])) {
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


	

	
	
	
	
require_once 'Slim/Slim.php';
require_once 'Models/MemberModel.php';
require_once 'Slim/Middleware.php';
require_once 'Slim/Middleware/BasicHttpAuthentication.php';


\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->map('/:x+', function($x) {
    http_response_code(200);
})->via('OPTIONS');


$app->add(new \BasicHttpAuthentication());

$app->get('/rest/api/version/member/:id',function ($id) { MemberModel::getInstance()->getMemberWithId($id);});




$app->run(); 


?>