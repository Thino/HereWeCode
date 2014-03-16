<?php


error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('UTC');


// Bootstrap allows to test Slim apps with root
// But surrender because of big problems with auth headers
// The api gets Apache headers and we can't set it with the solution
// So, use of Google Chrome Postman

require_once 'Slim/Slim.php';
require_once 'Models/MemberModel.php';
require_once 'Models/FacilityModel.php';
require_once 'Models/PlaceModel.php';
require_once 'Models/CommentModel.php';
require_once 'Slim/Middleware.php';
require_once 'Slim/Middleware/BasicHttpAuthentication.php';




\Slim\Slim::registerAutoloader();


class Slim_Framework_TestCase extends PHPUnit_Framework_TestCase
{
    private $testingMethods = array('get', 'post', 'put', 'delete');

    public function setup()
    {
	 // New test slim app
        $app = new \Slim\Slim(array(
            'version'        => '0.0.0',
            'debug'          => false,
            'mode'           => 'testing'           
        ));

        // Include our core application file
        require_once 'Routes/routes.php';
	    $this->app = $app;
		
		
    }


    
    private function request($method, $path, $formVars = array(), $optionalHeaders = array())
    {
		 // get stdout
        ob_start();

        // Mock environment
        \Slim\Environment::mock(array_merge(array(
            'REQUEST_METHOD' => strtoupper($method),
            'PATH_INFO'      => $path,
            'SERVER_NAME'    => 'local.dev',			
            'slim.input'     => http_build_query($formVars)
        ), $optionalHeaders));
		
		

        // Allow to get requests and response in tests
        $this->request  = $this->app->request();
        $this->response = $this->app->response();
		
				
       
        $this->app->run();
	 // Return the response body
        return ob_get_clean();
    }

	// MAgic method to use our protocols ( $testingMethods )
    public function __call($method, $arguments) {		
        if (in_array($method, $this->testingMethods)) {
            list($path, $formVars, $headers) = array_pad($arguments, 3, array());
            return $this->request($method, $path, $formVars, $headers);
        }
        throw new \BadMethodCallException(strtoupper($method) . ' is not supported');
    }
}
