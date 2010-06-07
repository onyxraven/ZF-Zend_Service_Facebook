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

/**
 * Facebook Methods abstract
 *
 * abstract for the seperate methods classes
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Zend_Service_Facebook
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Service_Facebook_Methods 
{

    /**
     * reference to the 'core' facebook frontend object
     * 
     * @var mixed
     */
    protected $_core;

    /**
     * constructor
     * 
     * @param Zend_Service_Facebook $core 
     * @return void
     */
    public function __construct(Zend_Service_Facebook $core) 
    {
        $this->_core = $core;
    }

    /**
     * call a 'REST' style method
     * 
     * @param string $method REST method name
     * @param array $args arguments
     * @return object result of call
     */
    public function callRestMethod($method, $args = array()) 
    {
        return $this->_core->getClient()->callRestMethod($method, $args);
    }

    /**
     * make a generic request (non-rest)
     * 
     * @param string $url url (can be relative, default graph api)
     * @param array $args arguments to the call
     * @param string $http_method which REST api method to call
     * @return object results of call
     */
    public function makeRequest($url, $args = array(), $http_method = Zend_Http_Client::GET) 
    {
        return $this->_core->getClient()->makeRequest($url, $args, $http_method);
    }

}
