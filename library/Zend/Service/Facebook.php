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
require_once 'Zend/Service/Facebook/Methods.php';

/**
 * Facebook Service API Client frontend
 *
 * Code ported from official facebook API library and unofficial PEAR library.  Implements
 * basics to be able to call the old REST api, the new REST api and the new Graph api
 *
 * @link http://wiki.developers.facebook.com/index.php/PHP
 * @link http://pear.php.net/package/Services_Facebook
 * @link http://wiki.developers.facebook.com/index.php/API
 * @link http://developers.facebook.com/docs/
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Zend_Service_Facebook
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Facebook 
{

    /**
     * Local HTTP Client cloned from statically set client
     *
     * @var Zend_Service_Facebook_Client
     */
    protected $_client;

    /**
     * currently logged in uid
     * 
     * @var         string      $_user_id
     */
    protected $_user_id;

    /**
     * Constants that define return formats from facebook
     */
    const RESPONSE_JSON = 'json';
    const RESPONSE_XML = 'xml';
    const RESPONSE_QUERY = 'query';

    /**
     * Construct 
     * 
     * @param Zend_Service_Facebook_Client $client client object
     * @param string $user_id user_id
     *
     * @return void
     */
    public function __construct(Zend_Service_Facebook_Client $client, $user_id=null) 
    {
        $this->setClient($client);
        $this->setUserId($user_id);
    }

    /**
     * set facebook Client 
     * 
     * @param Zend_Service_Facebook_Client $client client to use
     * @return self
     */
    public function setClient(Zend_Service_Facebook_Client $client) 
    {
        $this->_client = $client;
        return $this;
    }

    /**
     * get facebook Client 
     * 
     * @return Zend_Service_Facebook_Client
     */
    public function getClient() 
    {
        return $this->_client;
    }

    /**
     * set a user id as being the default/current user id 
     * 
     * @param string $user_id 
     * @return self
     */
    public function setUserId($user_id) 
    {
        $this->_user_id = $user_id;
        return $this;
    }

    /**
     * get the current/default user id
     * 
     * @return string
     */
    public function getUserId() 
    {
        return $this->_user_id;
    }

    /**
     * generate a generic facebook url 
     * 
     * @param string $subdomain
     * @return string
     */
    public function getFacebookUrl($subdomain = 'www') 
    {
        return 'http://' . $subdomain . '.facebook.com';
    }

    /**
     * overload for getting the different Methods classes
     * 
     * @param string $name 
     * @return Zend_Service_Facebook_Method
     */
    public function __get($name) 
    {
        $name = strtolower($name);
        return $this->loadMethods($name);
    }

    /**
     * internal cache/storage for the Zend_Service_Facebook_Methods classes
     * 
     * @var array
     */
    protected $_knownMethodClasses = array();

    /**
     * set a method class for a name.  Allows easy overriding/extension 
     * 
     * @param string $name 
     * @param Zend_Service_Facebook_Methods $obj
     * @return $this
     */
    public function setMethodClass($name, Zend_Service_Facebook_Methods $obj)
    {
        $name = strtolower($name);
        $this->_knownMethodClasses[$name] = $obj;
        return $this;
    }

    /**
     * load a Methods class by name
     * 
     * @param string $name 
     * @return Zend_Service_Facebook_Methods
     */
    public function loadMethods($name)
    {
        $name = strtolower($name);
        if (!empty($this->_knownMethodClasses[$name])) return $this->_knownMethodClasses[$name];

        $class = __CLASS__ . '_Methods_' . ucfirst($name);
        //TODO load class code
        return $this->_knownMethodClasses[$name] = new $class($this);
    }

}
