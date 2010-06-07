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
require_once 'Zend/Service/Facebook/Data/ConnectAccount.php';

/**
 * Facebook Auth methods
 *
 * Enables serialization for the structures that need to be built for the api
 * Search 'connect.' in 
 * @link http://wiki.developers.facebook.com/index.php/API
 * @link http://developers.facebook.com/docs/reference/rest/auth.revokeExtendedPermission
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Zend_Service_Facebook
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Facebook_Methods_Connect 
extends Zend_Service_Facebook_Methods 
{

    /**
     * Unconnected Friends Count
     *
     * @deprecated
     * @param string $session_key Facebook session key (required)
     * @link http://wiki.developers.facebook.com/index.php/Connect.getUnconnectedFriendsCount
     * @link http://developers.facebook.com/docs/reference/rest/auth.revokeExtendedPermission
     */
    public function getUnconnectedFriendsCount($session_key = null) 
    {
        if (!$session_key) {
            $session_key = $this->_core->getClient()->hasUserAuth();
        }

        return $this->callRestMethod('connect.getUnconnectedFriendsCount', 
            array('session_key' => $session_key));
    }

    /**
     * Register Users 
     * 
     * The accounts array may hold up to 1,000 accounts. Each account should hold
     * these array keys: (account_id and account_url are optional)
     *
     * @deprecated
     * @param array $accounts array of Zend_Service_Facebook_Connect_Account objs
     * @link http://wiki.developers.facebook.com/index.php/Connect.registerUsers
     * @link http://developers.facebook.com/docs/reference/rest/connect.registerUsers
     */
    public function registerUsers(array $accounts) 
    {
        $jsonAccts = array();
        foreach ($accounts as $a) {
            if (!($a instanceof Zend_Service_Facebook_Data_ConnectAccount)) continue;
            $jsonAccts[] = $a->toArray();
        }
        if (!$jsonAccts) {
            throw new Zend_Service_Facebook_Exception('No valid accounts');
        }

        return $this->callRestMethod('connect.registerUsers', 
            array('accounts' => Zend_Json::encode($jsonAccts)));
    }

    /**
     * unregisterUsers 
     * 
     * This method allows a site to unregister a connected account. You should 
     * call this method if the user deletes his account on your site.
     * 
     * @deprecated
     * @param array $emailHashes An array of email_hashes to unregister
     * @link http://wiki.developers.facebook.com/index.php/Connect.unregisterUsers
     * @link http://developers.facebook.com/docs/reference/rest/connect.unregisterUsers
     */
    public function unregisterUsers(array $emailHashes) 
    {
        $jsonHashes = array();
        foreach ($accounts as $a) {
            if ($a instanceof Zend_Service_Facebook_Data_ConnectAccount) {
                $jsonHashes[] = $a->email_hash;
            } else {
                $jsonHashes[] = $a;
            }
        }
        if (!$jsonHashes) {
            throw new Zend_Service_Facebook_Exception('No valid hashes');
        }

        return $this->callRestMethod('connect.unregisterUsers', 
            array('email_hashes' => Zend_Json::encode($jsonHashes)));
    }

}
