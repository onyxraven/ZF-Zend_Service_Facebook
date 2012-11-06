<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Feed_Writer
 * @subpackage Zend_Feed_Writer_Extensions_MediaRSS
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'Zend/Service/Facebook/Client.php';

/**
 * Facebook client for OAuth 2 auth
 *
 * the new client for facebook
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Zend_Service_Facebook
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Facebook_Client_Oauth2 extends Zend_Service_Facebook_Client
{

    /**
     * URI of Facebook's APIs (as starting point)
     *
     * @var string
     */
    protected static $_graph_api_url = 'https://graph.facebook.com/';

    protected static $_rest_api_url = 'https://api.facebook.com/method/';

    /**
     * Facebok app id
     *
     * @var int
     */
    protected $_api_id;

    /**
     * Facebok application secret 
     *
     * The Facebook secret token is used to both sign requests sent to 
     * Facebook and to verify requests sent from Facebook to your application.
     *
     * @var string
     */
    protected $_api_secret;

    /**
     * Currently logged in token
     * 
     * @var string
     */
    protected $_access_token_str;

    /**
     * Zend_Uri of this web service
     *
     * @var Zend_Uri_Http
     */
    protected $_uri = null;

    /**
     * Construct 
     * 
     * @param int $api_id
     *            API id
     * @param string $secret
     *            API secret
     * @param string $access_token_string
     *            optional access token to init with
     *
     * @return void
     */
    public function __construct($api_id, $secret, $access_token_str = null) 
    {
        $this->setApiId($api_id);
        $this->setApiSecret($secret);
        $this->setAccessTokenString($access_token_str);

        $this->setUri(self::$_graph_api_url);
    }

    /**
     * set api id
     * 
     * @param int $id 
     * @return self
     */
    public function setApiId($id) 
    {
        $this->_api_id = $id;
        return $this;
    }

    /**
     * set api secret 
     * 
     * @param string $secret 
     * @return self
     */
    public function setApiSecret($secret) 
    {
        $this->_api_secret = $secret;
        return $this;
    }

    /**
     * setAccessToken string 
     * 
     * @param string $access_token
     *            default null
     * @return self
     */
    public function setAccessTokenString($access_token_str = null) 
    {
        $this->_access_token_str = $access_token_str;
        return $this;
    }

    /**
     * getApiId 
     * 
     * @return string
     */
    public function getApiId() 
    {
        return $this->_api_id;
    }

    /**
     * getApiSecret 
     * 
     * @return string
     */
    public function getApiSecret() 
    {
        return $this->_api_secret;
    }

    /**
     * getSessionKey 
     * 
     * @return string
     */
    public function getAccessTokenString() 
    {
        return $this->_access_token_str;
    }

    /**
     * does this client have a user auth
     * 
     * @return string (== true on has auth)
     */
    public function hasUserAuth()
    {
        return $this->getAccessTokenString();
    }

    /**
     * Call REST method 
     * 
     * Used by all of the interface classes to send a request to the Facebook
     * API. It builds the standard argument list, munges that with the 
     * arguments passed to it, signs the request and sends it along to the
     * API. 
     *
     * Any formal error encountered is thrown as an exception.
     * 
     * @param mixed $method
     *            Method to call
     * @param array $args
     *            Arguments to send
     * @param string $response_format
     *            which response format to receive
     *
     * @return object response data
     */
    public function callRestMethod($method, $args = array(), $response_format = Zend_Service_Facebook::RESPONSE_JSON)
    {
        //build new rest api url
        $url = self::$_rest_api_url;
        $args['method'] = $method;
        $args['format'] = $response_format;
        if (!empty($args['session_key'])) {
            //change out session_key with access token
            $args['access_token'] = $args['session_key'];
            unset($args['session_key']);
        }
        return $this->makeRequest($url, $args, Zend_Http_Client::POST, array(), $response_format);
    }

    /**
     * make generic request
     * 
     * @param string $url
     *            url to request
     * @param array $args
     *            arguments
     * @param string $http_method
     *            http method to use
     * @param string $response_format
     *            response format to receive
     * @param array $files
     *            field_name => filepath array
     *            
     * @return object response data
     */
    public function makeRequest($url, $args = array(), $http_method = Zend_Http_Client::GET, $files = array(), $response_format = Zend_Service_Facebook::RESPONSE_JSON)
    {
        //build new api url
        if (strpos($url, 'https') !== 0) {
            $url = self::$_graph_api_url . ltrim($url,'/');
        }
        $this->resetParameters()->setUri($url);

        //'sign'
        if (!empty($args['access_token']) && $args['access_token'] === true) {
            $args['access_token'] = $this->getAccessTokenString();
        }

        if (count($files) > 0) {
            foreach ($files as $formname => $filename) {
                $this->setFileUpload($filename, $formname);
            }
        }
        
        //set appropriate parameters
        switch ($http_method) {
            case Zend_Http_Client::GET:
            case Zend_Http_Client::DELETE:
                $this->setParameterGet($args);
                break;
            case Zend_Http_Client::POST:
                $this->setParameterPost($args);
                break;
            default:
                throw new Zend_Service_Facebook_Exception('Unknown HTTP Method');
        }

        try {
            $response = $this->request($http_method);
        } catch (Exception $e) {
            throw new Zend_Service_Facebook_Exception($e->getMessage(), $e->getCode());
        }

        return $this->parseResponse($response, $response_format);
    }

    /**
     * generate the facebook auth url
     * 
     * @param string $redirect_uri 
     * @param array $scope 
     * @param string $display 
     * @return string
     */
    public function getAuthorizationUrl($redirect_uri, $scope = array(), $display = null)
    {
        $params = array(
            'client_id' => $this->getApiId(),
                'redirect_uri' => $redirect_uri
        );

        //todo verify scope as extended permissions
        if ($scope) {
            if (is_array($scope))
                $scope = implode(',', $scope);
            $params['scope'] = $scope;
        }
        //todo verify display allowed values
        if ($display)
            $params['display'] = $display;

        return self::$_graph_api_url . 'oauth/authorize?'.http_build_query($params);
    }

    /**
     * exchange authorization code for access token
     * 
     * @param string $redirect_uri
     *            (same as with getAuthorizationUrl)
     * @param string $code
     *            code back from facebook callback
     * @param bool $set
     *            set the token on the current client
     * @return string
     */
    public function getAccessToken($redirect_uri, $code, $set = true) 
    {
        $params = array(
            'client_id' => $this->getApiId(),
            'client_secret' => $this->getApiSecret(),
            'redirect_uri' => $redirect_uri,
                'code' => $code
            );

        //facebook currently returns the access token form urlencoded (like old oauth1.0)
        $result = $this->makeRequest('oauth/access_token', $params, Zend_Http_Client::POST, array(), Zend_Service_Facebook::RESPONSE_QUERY);
        $token = $result['access_token'];

        if ($set)
            $this->setAccessTokenString($token);
        return $result;
    }

    /**
     * exchange a list of session tokens for oauth access tokens
     * 
     * @param array $sessions
     *            list of sessions
     * @param bool $set
     *            set the token on the current client (first/only one)
     * @return object
     */
    public function upgradeSession($sessions, $set = true) 
    {
        if (is_array($sessions)) {
            $sessions = implode(',', $sessions);
        }
        $params = array(
            'type' => 'client_cred',
            'client_id' => $this->getApiId(),
            'client_secret' => $this->getApiSecret(),
                'sessions' => $sessions
            );

        $result = $this->makeRequest('oauth/exchange_sessions', $params, Zend_Http_Client::POST, array(), Zend_Service_Facebook::RESPONSE_JSON);

        if ($set) {
            $token = current($result);
            $this->setAccessTokenString($token['access_token']);
        }

        return $result;
    }

}
