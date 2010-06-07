<?php
/* ERROR HANDLING */
error_reporting(E_ALL|E_STRICT);
ini_set('display_startup_errors', 1);  
ini_set('display_errors', 1); 

/* INCLUDE PATHING */
$root = dirname(__FILE__);
set_include_path($root . '/trunk/library' . PATH_SEPARATOR 
                 . $root . '/incubator/library' . PATH_SEPARATOR
                 . $root . '/library' . PATH_SEPARATOR
                 . get_include_path() 
                ); 

/**
 * Zend Autoloader
 */
require_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();
$loader->suppressNotFoundWarnings(false);

/////////////////////////////////////////////////
//
/////////////////////////////////////////////////
//
/////////////////////////////////////////////////

$api_id = '';
$api_key = '';
$api_secret = '';
$a_session_secret = '';
$an_auth_token = '';
$a_code = '';

$client = new Zend_Service_Facebook_Client_Rest($api_key,
                                                $api_secret,
                                                $a_session_secret
                                               );

$oauthclient = new Zend_Service_Facebook_Client_Oauth2($api_id,
                                                       $api_secret,
                                                       $an_auth_token
                                                      );

//$url = $oauthclient->getAuthorizationUrl('http://example.org/facebook_callback',
                                         //'publish_stream'
                                        //);
//echo $url; exit;

//$token = $oauthclient->getAccessToken('http://example.org/facebook_callback',
                               //$a_code
                              //);

//$res = $oauthclient->callRestMethod('users.getInfo', array('uids' => '4', 
                                                           //'fields' => 'name',
                                                           //'access_token' => true,
                                                           //));
                                                           //

$fb = new Zend_Service_Facebook($oauthclient);
//$res = $fb->users->getInfo(null, '4');
$res = $fb->graph->get('4', null, null, true);

print_r($fb);

print_r($res);
