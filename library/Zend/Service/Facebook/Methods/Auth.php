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
 * Search 'auth.' in 
 * @link http://wiki.developers.facebook.com/index.php/API
 * @link http://developers.facebook.com/docs/reference/rest/#login/auth-methods
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Zend_Service_Facebook
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Facebook_Methods_Auth 
extends Zend_Service_Facebook_Methods 
{

    /**
     * Create a token for login
     *
     * @link http://wiki.developers.facebook.com/index.php/Auth.createToken
     * @link http://developers.facebook.com/docs/reference/rest/auth.createToken
     */
    public function createToken() 
    {
        return $this->callRestMethod('auth.createToken');
    }

    /**
     * Convert auth_token into a session_key
     *
     * @param string $authToken auth_token from callback
     * @param bool $generateSecret generate a session secret (default false)
     * @param string $hostUrl host url for base domain setting (default empty)
     * @link http://wiki.developers.facebook.com/index.php/Auth.getSession
     * @link http://developers.facebook.com/docs/reference/rest/auth.getSession
     */
    public function getSession($token, $generateSecret = null, $hostUrl = null) 
    {
        $args = array('auth_token' => $token);
        if ($generateSecret !== null) {
            $args['generate_session_secret'] = ($generateSecret) ? 'true' : 'false';
        }
        if ($hostUrl !== null) $args['host_url'] = $hostUrl;

        return $this->callRestMethod('auth.getSession', $args);
    }

    /**
     * Promote session to session secret
     *
     * @param string $session_key Facebook session key (required)
     *
     * @link http://wiki.developers.facebook.com/index.php/Auth.promoteSession
     * @link http://developers.facebook.com/docs/reference/rest/auth.promoteSession
     */
    public function promoteSession($session_key = null) 
    {
        if (!$session_key) {
            $session_key = $this->_core->getClient()->hasUserAuth();
        }

        if (!$session_key) {
            throw new Zend_Service_Facebook_Exception('Session Key Required');
        }

        return $this->callRestMethod('auth.promoteSession',
            array('session_key' => $session_key));
    }

    /**
     * Expire session 
     *
     * @param string $session_key Facebook session key (required)
     *
     * @link http://wiki.developers.facebook.com/index.php/Auth.expireSession
     * @link http://developers.facebook.com/docs/reference/rest/auth.expireSession
     */
    public function expireSession($session_key = null) 
    {
        if (!$session_key) {
            $session_key = $this->_core->getClient()->hasUserAuth();
        }

        if (!$session_key) {
            throw new Zend_Service_Facebook_Exception('Session Key Required');
        }

        return $this->callRestMethod('auth.expireSession',
            array('session_key' => $session_key));
    }

    /**
     * Revoke authorization 
     *
     * @param int $uid Facebook userid (or sessionkey)
     * @param string $session_key Facebook session key (or uid)
     * 
     * @link http://wiki.developers.facebook.com/index.php/Auth.revokeAuthorization
     * @link http://developers.facebook.com/docs/reference/rest/auth.expireSession
     */
    public function revokeAuthorization($uid = null, $session_key = null) 
    {
        if (!$session_key) {
            $session_key = $this->_core->getClient()->hasUserAuth();
        }

        $uid = ($uid !== null) ? $uid : $this->_core->getUserId();
        if (!$uid && !$session_key) {
            throw new Zend_Service_Facebook_Exception('UID or Session Key Required');
        }

        $args = array();
        if (!empty($session_key)) $args['session_key'] = $session_key;
        if (!empty($uid)) $args['uid'] = $uid;

        return $this->callRestMethod('auth.revokeAuthorization', $args);
    }

    /**
     * App permissions
     * @todo update to new permissions list
     * @todo put in a different location
     * @link http://wiki.developers.facebook.com/index.php/Extended_permissions
     * @var array
     */
    public static $appPermissions = array(
        'publish_stream',
        'email',
        'read_stream',
        'read_mailbox',
        'offline_access',
        'status_update',
        'photo_upload',
        'dreate_event',
        'rsvp_event',
        'sms',
        'video_upload',
        'create_note',
        'share_item',
        );

    /**
     * Revoke extended permission 
     * 
     * @param string $perm extended permission
     * @param int $uid Facebook userid (or sessionkey)
     * @param string $session_key Facebook session key (or uid)
     * @link http://developers.facebook.com/docs/reference/rest/auth.revokeExtendedPermission
     */
    public function revokeExtendedPermission($perm, $uid = null, $session_key = null) 
    {
        if (!$session_key) {
            $session_key = $this->_core->getClient()->hasUserAuth();
        }

        $uid = ($uid !== null) ? $uid : $this->_core->getUserId();
        if (!$uid && !$session_key) {
            throw new Zend_Service_Facebook_Exception('UID or Session Key Required');
        }

        if (!in_array($perm, self::$appPermissions)) {
            throw new Zend_Service_Facebook_Exception('Bad name');
        }

        $args = array('perm' => $perm);
        if (!empty($session_key)) $args['session_key'] = $session_key;
        if (!empty($uid)) $args['uid'] = $uid;

        return $this->callRestMethod('auth.revokeAuthorization', $args);
    }

}
