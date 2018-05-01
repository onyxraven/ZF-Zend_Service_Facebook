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
 * @category Zend
 * @package Zend_Feed_Writer
 * @subpackage Zend_Feed_Writer_Extensions_MediaRSS
 * @copyright Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 * @version $Id$
 */

require_once 'Zend/Service/Facebook/Methods.php';

/**
 * Facebook Auth methods
 *
 * Enables serialization for the structures that need to be built for the api
 *
 * @link http://developers.facebook.com/docs/reference/api/
 *      
 * @category Zend
 * @package Zend_Service
 * @subpackage Zend_Service_Facebook
 * @copyright Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */
class Zend_Service_Facebook_Methods_Graph extends Zend_Service_Facebook_Methods
{

    public function get($id, $relationship = null, $parameters = array(), $access_token = true)
    {
        $url = $id;
        if ($relationship)
            $url .= '/' . $relationship;
        if ($access_token)
            $parameters['access_token'] = $access_token;
        
        return $this->makeRequest($url, $parameters, Zend_Http_Client::GET);
    }

    public function post($id, $relationship, $args = array(), $files = array(), $access_token = true)
    {
        $url = $id;
        if ($relationship)
            $url .= '/' . $relationship;
        if (! $access_token) {
            $access_token = $this->_core->getClient()->hasUserAuth();
        }
        if (! $access_token) {
            throw new Zend_Service_Facebook_Exception('must have auth to post');
        }
        $args['access_token'] = $access_token;
        
        return $this->makeRequest($url, $args, Zend_Http_Client::POST, $files);
    }

    public function delete($id, $relationship = null, $access_token = true)
    {
        $url = $id;
        if ($relationship)
            $url .= '/' . $relationship;
        if (! $access_token) {
            $access_token = $this->_core->getClient()->hasUserAuth();
        }
        if (! $access_token) {
            throw new Zend_Service_Facebook_Exception('must have auth to post');
        }
        $args = array(
                'access_token' => $access_token
        );
        
        return $this->makeRequest($url, $args, Zend_Http_Client::DELETE);
    }
}
