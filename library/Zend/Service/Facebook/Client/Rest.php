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
 * Facebook client for 'old' rest auth
 *
 * the old client for facebook
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Zend_Service_Facebook
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Zend_Service_Facebook_Client_Rest 
extends Zend_Service_Facebook_Client
{

    /**
     * URI of Facebook's REST API
     *
     * @var string $api
     */
    static protected $api_url = 'http://api.new.facebook.com/restserver.php';

    /**
     * Facebok application API key 
     *
     * @var string $apiKey 32 character api_key from Facebook
     */
    protected $_api_key;

    /**
     * Facebok application secret 
     *
     * The Facebook secret token is used to both sign requests sent to 
     * Facebook and to verify requests sent from Facebook to your application.
     *
     * @var string $secret 32 character secret from Facebook
     */
    protected $_api_secret;

    /**
     * Version of the API to use
     *
     * @var string $version
     */
    static protected $_version = '1.0';

    /**
     * Currently logged in user
     * 
     * @var string $sessionKey
     */
    protected $_session_key;

    /**
     * domain base for login cookies through Connect
     * 
     * @var string $_base_domain
     */
    protected $_base_domain;

    /**
     * Use secret as session secret or not
     *
     * @var bool $useSessionSecret
     */
    protected $_use_session_secret = false;

    /**
     * Zend_Uri of this web service
     * @var Zend_Uri_Http
     */
    protected $_uri = null;

    /**{{
     *  automatic parameter gathering sources
     */
    const PARAM_SOURCE_POST = 'post';
    const PARAM_SOURCE_GET = 'get';
    const PARAM_SOURCE_GET_POST = 'get_post';
    const PARAM_SOURCE_COOKIE = 'cookie';
    static $val_prefix = array(
        self::PARAM_SOURCE_POST => 'fb_sig',
        self::PARAM_SOURCE_GET => 'fb_sig',
        self::PARAM_SOURCE_GET_POST => 'fb_post_sig',
        );
    /**}} */

    /**
     * Construct 
     * 
     * @param string $key API key
     * @param string $secret API secret
     * @param string $session_key optional session key to init with
     * @param string $base_domain domain to set cookies on
     *
     * @return void
     */
    public function __construct($key, $secret, 
        $session_key=null, $base_domain=null) 
    {
        $this->setApiKey($key);
        $this->setApiSecret($secret);
        $this->setSessionKey($session_key);
        $this->setBaseDomain($base_domain);

        $this->setUri(self::$api_url);
    }

    /**
     * setApiKey 
     * 
     * @param string $key 
     * @return self
     */
    public function setApiKey($key) 
    {
        $this->_api_key = $key;
        return $this;
    }

    /**
     * setApiSecret 
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
     * setSessionKey 
     * 
     * @param string $session_key default null
     * @return self
     */
    public function setSessionKey($session_key = null) 
    {
        $this->_session_key = $session_key;
        return $this;
    }

    /**
     * setBaseDomain 
     * 
     * @param string $base_domain 
     * @return self
     */
    public function setBaseDomain($base_domain) 
    {
        $this->_base_domain = $base_domain;
        return $this;
    }


    /**
     * setUseSessionSecret 
     * 
     * @param bool $ss 
     * @return self
     */
    public function setUseSessionSecret($ss) 
    {
        $this->_use_session_secret = $ss;
        return $this;
    }

    /**
     * getApiKey 
     * 
     * @return string
     */
    public function getApiKey() 
    {
        return $this->_api_key;
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
    public function getSessionKey() 
    {
        return $this->_session_key;
    }

    /**
     * getBaseDomain 
     * 
     * @return string
     */
    public function getBaseDomain()
    {
        return $this->_base_domain;
    }


    /**
     * getUseSessionSecret 
     * 
     * @return bool
     */
    public function getUseSessionSecret()
    {
        return $this->_use_session_secret;
    }

    /**
     * do we have a current authenticated user
     * 
     * @return string (== true if has)
     */
    public function hasUserAuth()
    {
        return $this->getSessionKey();
    }

    /**
     * Call method 
     * 
     * Used by all of the interface classes to send a request to the Facebook
     * API. It builds the standard argument list, munges that with the 
     * arguments passed to it, signs the request and sends it along to the
     * API. 
     *
     * arguments automatically added are the first few for every request:
     *  * api_key
     *  * call_id
     *  * sig
     *  * v
     *  * format
     *
     * Any formal error encountered is thrown as an exception.
     * 
     * @param mixed $method Method to call
     * @param array $args   Arguments to send
     * @param string $response_format type of response to receive
     *
     * @return object response data
     */
    public function callRestMethod($method, $args = array(), 
        $response_format = Zend_Service_Facebook::RESPONSE_JSON) 
    {
        $args['api_key'] = $this->getApiKey();
        $args['v']       = self::$_version;
        $args['format']  = $response_format;
        $args['method']  = $method;
        $args['call_id'] = microtime(true);
        if ($this->getUseSessionSecret()) {
            $args['ss'] = 1;
        }
        if ($args['session_key'] === true) {
            $args['session_key'] = $this->getSessionKey();
        }
        $args['sig'] = $this->generateSig($args);

        $this->resetParameters()->setUri($this->getUri());

        $this->setParameterPost($args);

        try {
            $response = $this->request('POST');
        } catch (Exception $e) {
            throw new Zend_Service_Facebook_Exception($e->getMessage(), $e->getCode());
        }

        return $this->parseResponse($response, $response_format);
    }

    /**
     * make a generic request 
     *
     * not supported in this client
     * 
     * @param string $url 
     * @param array $args 
     * @param string $http_method 
     * @param string $response_format 
     * @return void
     */
    public function makeRequest($url, $args = array(), 
        $http_method = Zend_Http_Client::GET, 
        $response_format = Zend_Service_Facebook::RESPONSE_JSON) 
    {
        throw new Zend_Service_Facebook_Exception('Only REST methods are supported in this client');
    }

    /**
     * verify a facebook signature
     * 
     * @param array $fb_params 
     * @param string $sig 
     * @return bool
     */
    public function verifySignature(array $fb_params, $sig) 
    {
        return $this->generateSig($fb_params) == $sig;
    }

    /**
     * generate a facebook signature
     * 
     * @param array $args 
     * @return string
     */
    public function generateSig(array $args) 
    {
        if (isset($args['sig'])) {
            unset($args['sig']);
        }

        ksort($args);
        $sig = '';
        foreach ($args as $k => $v) {
            $sig .= $k . '=' . $v;
        }

        $sig .= $this->getApiSecret();
        return md5($sig);
    }

    /**
     * getLogoutUrl 
     * 
     * @param string $next callback after logout
     * @return string
     */
    public function getLogoutUrl(array $params = array()) 
    {
        $page = self::getFacebookUrl().'/logout.php';
        $params['app_key'] = $this->getApiKey();

        return $page . '?' . http_build_query($params);
    }

    /**
     * getLoginUrl 
     * 
     * @param array $perms extra params to request
     * @return string
     */
    public function getLoginUrl(array $params = array()) 
    {
        $page = self::getFacebookUrl() . '/login.php';
        $params['api_key'] = $this->getApiKey();
        $params['v'] = self::$_version;

        return $page . '?' . http_build_query($params);
    }

    /**
     * getPromptPermissionsUrl 
     * 
     * @param array $perms extra params to request
     * @return string
     */
    public function getPromptPermissionsUrl(array $params = array()) 
    {
        $page = self::getFacebookUrl() . '/connect/prompt_permissions.php';
        $params['api_key'] = $this->getApiKey();
        $params['v'] = self::$_version;

        return $page . '?' . http_build_query($params);
    }

    /**
     * getAddUrl 
     * 
     * @param array $perms extra params to request
     * @return string
     */
    public function getAddUrl(array $params = array()) 
    {
        $page = self::getFacebookUrl() . '/add.php';
        $params['api_key'] = $this->getApiKey();

        return $page . '?' . http_build_query($params);
    }


    /**
     * getAutoFacebookParams 
     *
     * returns the array of parameters gathered from facebook connect or a facebook post
     * including:
     *  * user
     *  * ss
     *  * session_key
     *  * expires
     *
     *  @todo figure out login.php callback format here
     * 
     * @param array $source one or more sources to pull from (null is all)
     * @param int $timeout oldest acceptable params
     * @return array parameters gathered
     */
    public function getAutoFacebookParams(array $source=null, $timeout = 172800) 
    {
        if (is_array($source)) $source = array_change_key_case($source);
        elseif ($source) $source = strtolower($source);

        $params = array();

        if (!$source || $source == self::PARAM_SOURCE_POST || !empty($source[self::PARAM_SOURCE_POST])) {
            $params = array_merge($params, 
                $this->getFacebookParams($_POST, 
                    self::$val_prefix[self::PARAM_SOURCE_POST],
                    $timeout));
        }
        if (!$params && (!$source || $source == self::PARAM_SOURCE_GET || !empty($source[self::PARAM_SOURCE_GET]))) {
            $params = array_merge($params, 
                $this->getFacebookParams($_GET, 
                    self::$val_prefix[self::PARAM_SOURCE_GET],
                    $timeout));
            $params = array_merge($params, 
                $this->getFacebookParams($_POST, 
                    self::$val_prefix[self::PARAM_SOURCE_GET_POST],
                    $timeout));
        }
        if (!$params && (!$source || $source == self::PARAM_SOURCE_COOKIE || !empty($source[self::PARAM_SOURCE_COOKIE]))) {
            $params = array_merge($params, 
                $this->getFacebookParams($_COOKIE, 
                    $this->getApiKey()));
            $base_domain_cookie = 'base_domain_' . $this->getApiKey();
            if (isset($_COOKIE[$base_domain_cookie])) {
                $this->setBaseDomain($_COOKIE[$base_domain_cookie]);
            }
        }
        return $params;
    }

    /**
     * load some params onto the current client
     * 
     * @param mixed $user 
     * @param mixed $session_key 
     * @param mixed $ss 
     * @return self
     */
    public function loadFacebookParams($user, $session_key = null, $ss = null) 
    {
        if (is_array($user)) extract($user);
        $this->setUserId($user);
        $this->setSessionKey($session_key);
        $this->setSessionSecret($ss); //todo huh?
        return $this;
    }

    /**
     * load facebook params from current request (cookie or get etc)
     * 
     * @param array $source which sources to try to look at
     * @param int $timeout 
     * @return self
     */
    public function autoLoadFacebookParams(array $source=null, $timeout = 172800) 
    {
        return $this->loadFacebookParams($this->getAutoFacebookParams($source, $timeout));
    }

    /**
     * from a given array, extract the valid params from facebook
     * 
     * @param array $params 
     * @param string $namespace as prefix for params
     * @param int $timeout max age of timestamp
     * @return array params as gathered (stripped of namespace)
     */
    public function getFacebookParams(array $params, $namespace, $timeout = null) 
    {
        $prefix =  $namespace . '_';
        $prefix_len = strlen($prefix);
        $fb_params = array();

        if ($timeout && 
            (!isset($params[$prefix.'time']) 
             || time() - $params[$prefix.'time'] > $timeout)) {
            return array();
        }

        foreach ($params as $k => $v) {
            if (strpos($k, $prefix) === 0) {
                $fb_params[substr($k, $prefix_len)] = $v;
            }
        }

        $sig = isset($params[$namespace]) ? $params[$namespace] : null;
        if (!$sig || !$this->verifySignature($fb_params, $sig)) {
            return array();
        }
        return $fb_params;
    }


    /**
     * sets facebook specific cookies for connect in current base domain
     * 
     * @param string $user id
     * @param string $session_key 
     * @param int $expires 
     * @param bool $session_secret 
     * @return self
     */
    public function setCookies($user, $session_key, $expires=null, $session_secret=null) 
    {
        $cookies = array();
        $cookies['user'] = $user;
        $cookies['session_key'] = $session_key;
        if ($expires != null) $cookies['expires'] = $expires;
        if ($session_secret != null) $cookies['ss'] = $session_secret;

        foreach ($cookies as $name => $val) {
            $ckname = $this->getApiKey() . '_'. $name;
            setcookie($ckname, $val, (int)$expires, '', $this->getBaseDomain());
            $_COOKIE[$ckname] = $val;
        }
        $sig = $this->generateSig($cookies);
        setcookie($this->getApiKey(), $sig, (int)$expires, '', $this->getBaseDomain());
        $_COOKIE[$this->getApiKey()] = $sig;

        if ($this->getBaseDomain() != null) {
            $base_domain_cookie = 'base_domain_' . $this->getApiKey();
            setcookie($base_domain_cookie, $this->getBaseDomain(), (int)$expires, '', $this->getBaseDomain());
            $_COOKIE[$base_domain_cookie] = $this->getBaseDomain();
        }

        return $this;
    }

    /**
     * clears all facebook cookies for current base domain
     * 
     * @return self
     */
    public function clearCookies() 
    {
        $cookies = array('user', 'session_key', 'expires', 'ss');
        foreach ($cookies as $name) {
            $ckname = $this->getApiKey() . '_' . $name;
            setcookie($ckname,
                false,
                time() - 3600,
                '',
                $this->getBaseDomain());
            unset($_COOKIE[$ckname]);
        }
        setcookie($this->getApiKey(), false, time() - 3600, '', $this->getBaseDomain());
        unset($_COOKIE[$this->getApiKey()]);

        return $this;
    }

}
