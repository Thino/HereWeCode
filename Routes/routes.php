<?php

// File which contains all routes of our app. 

$app->map('/:x+', function($x) {
	http_response_code(200);
})->via('OPTIONS');


$app->add(new \BasicHttpAuthentication());

$app->get('/rest/api/v0.1/member/:id',function ($id) { MemberModel::getInstance()->getMemberWithId($id);});
$app->put('/rest/api/v0.1/member/:id',function ($id) { MemberModel::getInstance()->updateMemberWithId($id);});
$app->post('/rest/api/v0.1/member',function () { MemberModel::getInstance()->addMember();});
$app->delete('/rest/api/v0.1/member/:id',function ($id) { MemberModel::getInstance()->deleteMemberWithId($id);});

$app->get('/rest/api/v0.1/facility',function () { FacilityModel::getInstance()->getFacilities();});
$app->post('/rest/api/v0.1/facility',function () { FacilityModel::getInstance()->addFacility();});
$app->get('/rest/api/v0.1/facility/:id',function ($id) { FacilityModel::getInstance()->getFacilityWithId($id);});
$app->put('/rest/api/v0.1/facility/:id',function ($id) { FacilityModel::getInstance()->updateFacilityWithId($id);});
$app->delete('/rest/api/v0.1/facility/:id',function ($id) { FacilityModel::getInstance()->deleteFacilityWithId($id);});

$app->get('/rest/api/v0.1/place',function () { PlaceModel::getInstance()->getPlaces();});
$app->post('/rest/api/v0.1/place',function () { PlaceModel::getInstance()->addPlace();});
$app->get('/rest/api/v0.1/place/:id',function ($id) { PlaceModel::getInstance()->getPlaceWithId($id);});
$app->put('/rest/api/v0.1/place/:id',function ($id) { PlaceModel::getInstance()->updatePlaceWithId($id);});
$app->delete('/rest/api/v0.1/place/:id',function ($id) { PlaceModel::getInstance()->deletePlaceWithId($id);});
$app->get('/rest/api/v0.1/place/:id/facility',function ($id) { PlaceModel::getInstance()->getFacilitiesWithPlaceId($id);});
$app->post('/rest/api/v0.1/place/:id/facility',function ($id) { PlaceModel::getInstance()->addFacilityWithPlaceId($id);});






$app->get('/rest/api/v0.1/comment/:id',function ($id) { CommentModel::getInstance()->getCommentWithId($id);});
$app->put('/rest/api/v0.1/comment/:id',function ($id) { CommentModel::getInstance()->updateCommentWithId($id);});
$app->delete('/rest/api/v0.1/comment/:id',function ($id) { CommentModel::getInstance()->deleteCommentWithId($id);});

$app->get('/rest/api/v0.1/place/:id/comment',function ($id) { CommentModel::getInstance()->getCommentsWithPlaceId($id);});
$app->post('/rest/api/v0.1/place/:id/comment',function ($id) { CommentModel::getInstance()->addCommentToPlace($id);});

$app->post('/rest/auth/session',function () { MemberModel::getInstance()->authMember();});
$app->post('/rest/api/v0.1/place/search',function () { PlaceModel::getInstance()->searchPlace();});



$app->get('/specs',function () {

	readfile("specs.html");

});

