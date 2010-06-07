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
 * Search 'users.' in 
 * @link http://wiki.developers.facebook.com/index.php/API
 * @link http://developers.facebook.com/docs/reference/rest/#data-retrieval-methods
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Zend_Service_Facebook
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Facebook_Methods_Users 
extends Zend_Service_Facebook_Methods 
{

    /**
     * Is app user
     *
     * Uses the passed in user ID or session key to determine
     * if the user is a user of the application.
     * 
     * @param int $uid Facebook user ID
     * @param string $session_key Facebook session key
     *
     * @link http://wiki.developers.facebook.com/index.php/Users.isAppUser
     * @link http://developers.facebook.com/docs/reference/rest/users.isAppUser
     */
    public function isAppUser($uid = null, $session_key = null) 
    {
        if (!$session_key) {
            $session_key = $this->_core->getClient()->hasUserAuth();
        }

        $uid = ($uid) ? $uid : $this->_core->getUserId();
        if (!$uid && !$session_key) {
            throw new Zend_Service_Facebook_Exception('UID or Session Key Required');
        }

        $args = array();
        if ($session_key) $args['session_key'] = $session_key;
        if ($uid) $args['uid'] = $uid;

        return $this->callRestMethod('users.isAppUser', $args);
    }

    /**
     * Set a user's status message
     *
     * Set $status to true to clear the status or a string to change the 
     * actual status message.
     *
     * @param mixed $status Set to true to clear status
     * @param bool $verb set to true to not use 'is' in status
     * @param int $uid Facebook userid
     * @param string $session_key Facebook session key
     * 
     * @return boolean True on success, false on failure
     * @link http://wiki.developers.facebook.com/index.php/Users.setStatus
     * @link http://wiki.developers.facebook.com/index.php/Extended_permission
     * @link http://developers.facebook.com/docs/reference/rest/users.setStatus
     */
    public function setStatus($status, $verb = false, $uid = null, $session_key = null) 
    {
        if (!$session_key) {
            $session_key = $this->_core->getClient()->hasUserAuth();
        }

        $uid = ($uid) ? $uid : $this->_core->getUserId();
        if (!$uid && !$session_key) {
            throw new Zend_Service_Facebook_Exception('UID or Session Key Required');
        }

        $args = array();
        if ($session_key) $args['session_key'] = $session_key;
        if ($uid) $args['uid'] = $uid;

        if (is_bool($status) && $status === true) {
            $args['clear'] = 'true';
        } else {
            $args['status'] = $status;
        }
        if ($verb) $args['verb'] = 'true';

        return $this->callRestMethod('users.setStatus', $args); 
    }

    /**
     * info fields available without a session
     * @var array
     */
    public static $infoFieldsNoSession = array(
        'uid',
        'first_name',
        'last_name',
        'name',
        'locale',
        'affiliations',
        'pic_square',
        'profile_url',
        );

    /**
     *  info fields available with a session
     *  @var array
     */
    public static $infoFieldsSession = array(
        'about_me',
        'activities',
        'birthday',
        'birthday_date',
        'books',
        'current_location',
        'education_history',
        'email_hashes',
        'has_added_app',
        'hometown_location',
        'hs_info',
        'interests',
        'is_app_user',
        'meeting_for',
        'meeting_sex',
        'movies',
        'music',
        'notes_count',
        'pic',
        'pic_small',
        'pic_big',
        'pic_with_logo',
        'pic_small_with_logo',
        'pic_square_with_logo',
        'pic_big_with_logo',
        'political',
        'profile_blurb',
        'profile_update_time',
        'proxied_email',
        'quotes',
        'relationship_status',
        'religion',
        'sex',
        'significant_other_id',
        'status',
        'timezone',
        'tv',
        'username',
        'wall_count',
        'website',
        'work_history',
        );

    /**
     * Get user info
     *
     * @param array $fields List of fields to retrieve
     * @param mixed $uids   A single uid or array of uids
     * @param string $session_key Facebook session key
     * 
     * @link http://wiki.developers.facebook.com/index.php/Users.getInfo
     * @link http://wiki.developers.facebook.com/index.php/Extended_permission
     * @link http://developers.facebook.com/docs/reference/rest/users.getInfo
     */
    public function getInfo($fields = array(), $uids = null, $session_key = null) 
    {
        if (!$session_key) {
            $session_key = $this->_core->getClient()->hasUserAuth();
        }

        $args = array();
        if (empty($uids)) {
            $args['uids'] = $this->_core->getUserId();
        } else {
            if (!is_array($uids)) $uids = array($uids);
            $args['uids'] = implode(',', $uids);
        }

        $availFields = self::$infoFieldsNoSession;
        if (!empty($session_key)) {
            $args['session_key'] = $session_key;
            $availFields = array_merge($availFields, self::$infoFieldsSession);
        }

        if (empty($fields)) {
            $fields = $availFields;
        } else {
            if (!is_array($fields)) {
                $fields = array($fields);
            }
            foreach ($fields as $k => $f) {
                if (!in_array($f, $availFields)) {
                    unset($fields[$k]);
                }
            }
        }
        if (!$fields) {
            throw new Zend_Service_Facebook_Exception('No valid fields');
        }
        $args['fields'] = implode(',', $fields);

        return $this->callRestMethod('users.getInfo', $args);
    }

    /**
     *  info fields available from standardinfo
     *  @var array
     */
    public static $standardInfoFields = array(
        'uid',
        'first_name',
        'last_name',
        'name',
        'timezone',
        'birthday',
        'sex',
        'affiliations', 
        'locale',
        'profile_url',
        'proxied_email',
        );

    /**
     * Get user info for analytics only
     *
     * @param array $fields List of fields to retrieve
     * @param mixed $uids   A single uid or array of uids
     * 
     * @link http://wiki.developers.facebook.com/index.php/Users.getStandardInfo
     * @link http://developers.facebook.com/docs/reference/rest/users.getStandardinfo
     */
    public function getStandardInfo(array $fields = array(), $uids = null) 
    {
        $args = array();
        if (empty($uids)) {
            $args['uids'] = $this->_core->getUserId();
        } else if (is_array($uids)) {
            $args['uids'] = implode(',', $uids);
        }

        if (empty($fields)) {
            $fields = self::$standardInfoFields;
        } else {
            if (!is_array($fields)) {
                $fields = array($fields);
            }
            foreach ($fields as $k => $f) {
                if (!in_array($f, self::$standardInfoFields)) {
                    unset($fields[$k]);
                }
            }
        }
        if (!$fields) {
            throw new Zend_Service_Facebook_Exception('No valid fields');
        }
        $args['fields'] = implode(',', $fields);

        return $this->callRestMethod('users.getStandardInfo', $args);
    }

    /**
     * Get the currently logged in uid
     *
     * Returns the Facebook uid of the person currently "logged in" as 
     * specified by $sessionKey.
     *
     * @param string $session_key Facebook session key
     *
     * @link http://wiki.developers.facebook.com/index.php/Users.getLoggedInUser
     * @link http://developers.facebook.com/docs/reference/rest/users.getLoggedInUser
     */
    public function getLoggedInUser($session_key = null) 
    {
        if (!$session_key) {
            $session_key = $this->_core->getClient()->hasUserAuth();
        }

        if (!$session_key) {
            throw new Zend_Service_Facebook_Exception('Session Key Required');
        }

        return $this->callRestMethod('users.getLoggedInUser', 
            array('session_key' => $session_key));
    }

    /**
     * @todo move p to common area
     * @link http://wiki.developers.facebook.com/index.php/Extended_permissions
     */
    public static $appPermissions = array(
        'publish_stream',
        'email',
        'read_stream',
        'read_mailbox',
        'offline_access',
        'status_update',
        'photo_upload',
        'create_event',
        'rsvp_event',
        'sms',
        'video_upload',
        'create_note',
        'share_item',
        );

    /**
     * Has given extended permission
     *
     * @param string  $perm Permission to check
     * @param int $uid  User's ID, optional if session key present
     * @param string $session_key Facebook session key
     * 
     * @link http://wiki.developers.facebook.com/index.php/Users.hasAppPermission
     * @link http://developers.facebook.com/docs/reference/rest/users.hasAppPermission
     */
    public function hasAppPermission($perm, $uid = null, $session_key = null) 
    {
        if (!$session_key) {
            $session_key = $this->_core->getClient()->hasUserAuth();
        }

        $uid = ($uid) ? $uid : $this->_core->getUserId();
        if (!$uid && !$session_key) {
            throw new Zend_Service_Facebook_Exception('UID or Session Key Required');
        }

        if (!in_array($perm, self::$appPermissions)) {
            throw new Zend_Service_Facebook_Exception('bad name');
        }

        $args = array('ext_perm' => $perm);
        if ($session_key) $args['session_key'] = $session_key;
        if ($uid) $args['uid'] = $uid;

        return $this->callRestMethod('users.hasAppPermission', $args);
    }

    /**
     * Is Verified
     *
     * @link http://wiki.developers.facebook.com/index.php/Users.isVerified
     * @link http://developers.facebook.com/docs/reference/rest/users.isVerified
     */
    public function isVerified($session_key = null) 
    {
        if (!$session_key) {
            $session_key = $this->_core->getClient()->hasUserAuth();
        }

        if (!$session_key) {
            throw new Zend_Service_Facebook_Exception('Session Key Required');
        }

        return $this->callRestMethod('users.isVerified', 
            array('session_key', $session_key));
    }

    //message.getThreadsInFolder
    //status.get
    //status.set

}
