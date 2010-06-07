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

require_once 'Zend/Service/Facebook/Methods.php';

/**
 * Facebook Auth methods
 *
 * Enables serialization for the structures that need to be built for the api
 * Search 'fql.' in 
 * @link http://wiki.developers.facebook.com/index.php/API
 * @link http://developers.facebook.com/docs/reference/rest/fql.query
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Zend_Service_Facebook
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Facebook_Methods_Fql 
extends Zend_Service_Facebook_Methods 
{

    /**
     * Send a FQL query
     *
     * @param string $query FQL query string
     * @param string $session_key Facebook session key (optional)
     * @link http://wiki.developers.facebook.com/index.php/Fql.query
     * @link http://developers.facebook.com/docs/reference/rest/fql.query
     */
    public function query($query, $session_key = null) 
    {
        if (!$session_key) {
            $session_key = $this->_core->getClient()->hasUserAuth();
        }

        $params = array('query' => $query);
        if (!empty($session_key)) $params['session_key'] = $session_key;

        return $this->callRestMethod('fql.query', $params);
    }

    /**
     * Send many fql queries
     *
     * @param array $queries queries as key-value pairs
     * @param string $session_key Facebook session key (optional)
     * @link http://wiki.developers.facebook.com/index.php/Fql.multiquery
     * @link http://developers.facebook.com/docs/reference/rest/fql.multiquery
     */
    public function multiquery(array $queries, $session_key = null) {
        if (!$session_key) {
            $session_key = $this->_core->getClient()->hasUserAuth();
        }

        $params = array('queries' => Zend_Json::encode($queries));
        if (!empty($session_key)) $params['session_key'] = $session_key;

        return $this->callRestMethod('fql.multiquery', $params);
    }

}
