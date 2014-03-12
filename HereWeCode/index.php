<?php


	
require_once 'Slim/Slim.php';
require_once 'Models/MemberModel.php';
require_once 'Models/FacilityModel.php';
require_once 'Models/PlaceModel.php';
require_once 'Models/CommentModel.php';
require_once 'Slim/Middleware.php';
require_once 'Slim/Middleware/BasicHttpAuthentication.php';
	
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
	
	


\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->map('/:x+', function($x) {
    http_response_code(200);
})->via('OPTIONS');


$app->add(new \BasicHttpAuthentication());

$app->get('/rest/api/version/member/:id',function ($id) { MemberModel::getInstance()->getMemberWithId($id);});
$app->put('/rest/api/version/member/:id',function ($id) { MemberModel::getInstance()->updateMemberWithId($id);});
$app->post('/rest/api/version/member',function () { MemberModel::getInstance()->addMember();});
$app->delete('/rest/api/version/member/:id',function ($id) { MemberModel::getInstance()->deleteMemberWithId($id);});

$app->get('/rest/api/version/facilities',function () { FacilityModel::getInstance()->getFacilities();});
$app->post('/rest/api/version/facility',function () { FacilityModel::getInstance()->addFacility();});
$app->get('/rest/api/version/facility/:id',function ($id) { FacilityModel::getInstance()->getFacilityWithId($id);});
$app->put('/rest/api/version/facility/:id',function ($id) { FacilityModel::getInstance()->updateFacilityWithId($id);});
$app->delete('/rest/api/version/facility/:id',function ($id) { FacilityModel::getInstance()->deleteFacilityWithId($id);});


$app->post('/rest/api/version/place',function () { PlaceModel::getInstance()->addPlace();});
$app->get('/rest/api/version/place/:id',function ($id) { PlaceModel::getInstance()->getPlaceWithId($id);});
$app->put('/rest/api/version/place/:id',function ($id) { PlaceModel::getInstance()->updatePlaceWithId($id);});
$app->delete('/rest/api/version/place/:id',function ($id) { PlaceModel::getInstance()->deletePlaceWithId($id);});


$app->get('/rest/api/version/comment/:id',function ($id) { CommentModel::getInstance()->getCommentWithId($id);});
$app->put('/rest/api/version/comment/:id',function ($id) { CommentModel::getInstance()->updateCommentWithId($id);});
$app->delete('/rest/api/version/comment/:id',function ($id) { CommentModel::getInstance()->deleteCommentWithId($id);});

$app->get('/rest/api/version/place/:id/comment',function ($id) { CommentModel::getInstance()->getCommentsWithPlaceId($id);});
$app->post('/rest/api/version/place/:id/comment',function ($id) { CommentModel::getInstance()->addCommentToPlace($id);});

$app->post('/rest/auth/session',function () { MemberModel::getInstance()->authMember();});
$app->post('/rest/api/version/places/search',function () { PlaceModel::getInstance()->searchPlace();});



$app->run(); 


?>