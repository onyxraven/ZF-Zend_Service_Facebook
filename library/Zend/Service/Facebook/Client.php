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

require_once 'Zend/Http/Client.php';

/**
 * Facebook client abstract
 *
 * interface definition for the different client libraries to access facebook
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Zend_Service_Facebook
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

abstract class Zend_Service_Facebook_Client
extends Zend_Http_Client
{

    /**
     * call a REST style method
     * 
     * @param string $method 
     * @param array $args 
     * @param string $response_format 
     * @return object
     */
    abstract public function callRestMethod($method, $args = array(), 
        $response_format=Zend_Service_Facebook::RESPONSE_JSON);

    /**
     * make a generic request
     * 
     * @param string $url 
     * @param array $args 
     * @param string $http_method 
     * @param string $response_format 
     * @return object
     */
    abstract public function makeRequest($url, $args = array(), 
        $http_method=Zend_Http_Client::GET, 
        $response_format=Zend_Service_Facebook::RESPONSE_JSON);

    /**
     * do we have a user authenticated, stored in the client
     * 
     * @return mixed (==true for if we have one)
     */
    abstract public function hasUserAuth();

    /**
     * parseResponse 
     * 
     * Parses the raw response from Facebook
     *
     * @param Zend_Http_Response $response Response obj
     * @param string $response_format type of response to expect
     *
     * @return object Parsed response
     */
    protected function parseResponse(Zend_Http_Response $response, 
        $response_format = Zend_Service_Facebook::RESPONSE_JSON) 
    {
        $result = null;

        if (empty($response)) {
            throw new Zend_Service_Facebook_Exception('Empty HTTP Response', 10000);
        }
        $responseBody = $response->getBody();

        try {
            switch ($response_format) {
                case Zend_Service_Facebook::RESPONSE_JSON:
                    //stringify all ints since its decoding them DUMB.
                    $responseBody = preg_replace('/"(\w+)":(\d+),/', '"$1":"$2",', $responseBody);
                    $result = Zend_Json::decode($responseBody);
                    break;
                case Zend_Service_Facebook::RESPONSE_QUERY:
                    parse_str($responseBody, $result);
                    break;
                default:
                    $result = $responseBody;
            }
        } catch (Exception $e) {
            throw new Zend_Service_Facebook_Exception(
                'Could not parse response: '.$e->getMessage(),
                (200000 + $e->getCode()));
        }
        if (empty($result)) {
            throw new Zend_Service_Facebook_Exception('Empty Response Body', 20000);
        }

        if (is_array($result)) {
            $code = 0;
            $message = null;
            if (isset($result['fb_error']['code'])) {
                $code = (int)$result['fb_error']['code'];
            }
            if (isset($result['fb_error']['msg'])) {
                $message = $result['fb_error']['msg'];
            }
            if (isset($result['error_code'])) {
                $code = (int)$result['error_code'];
            }
            if (isset($result['error_msg'])) {
                $message = $result['error_msg'];
            }
            if ($code > 0 && !is_null($message)) {
                throw new Zend_Service_Facebook_Exception($message, $code);
            }
        }

        if ($response->isError()) {
            throw new Zend_Service_Facebook_Exception(
                'HTTP Response: ' . $response->getMessage() . ' ('.$responseBody.')',
                (10000 + $response->getStatus()));
        }

        return $result;
    }

}
