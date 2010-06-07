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
require_once 'Zend/Service/Facebook/Data/StreamAttachment.php';
require_once 'Zend/Service/Facebook/Data/StreamActionLink.php';

/**
 * Facebook Auth methods
 *
 * Enables serialization for the structures that need to be built for the api
 * Search 'stream.' in 
 * @link http://wiki.developers.facebook.com/index.php/API
 * @link http://developers.facebook.com/docs/reference/rest/#data-retrieval-methods
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Zend_Service_Facebook
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Facebook_Methods_Stream 
extends Zend_Service_Facebook_Methods 
{

    /**
     * metadata fields able to get
     * @var array
     */
    public static $metadataFields = array(
        'albums',
        'profiles',
        'photo_tags',
        'strip_tags',
        );

    /**
     * Get Stream
     *
     * @param array $source_ids (default all)
     * @param int $start_time (unix time) default now-1day
     * @param int $end_time (unix time) default now
     * @param int $limit (32 bit) default 30
     * @param string $filter key (see stream.getFilters)
     * @param array $metadata (see self::$metadataFields)
     * @param int $viewer_id User's ID, to view, 0 for public, none for current session
     * @param string $session_key Facebook session key (required)
     * @link http://wiki.developers.facebook.com/index.php/Stream.get
     * @link http://developers.facebook.com/docs/reference/rest/stream.get
     */
    public function get($source_ids = array(), 
        $start_time = null, $end_time = null,
        $limit = 30, $filter_key = null, $metadata = array(),
        $viewer_id = null, $session_key = null) 
    {
        if (!$session_key) {
            $session_key = $this->_core->getClient()->hasUserAuth();
        }

        if (!$session_key) {
            throw new Zend_Service_Facebook_Exception('Session Key Required');
        }

        $args = array('session_key' => $session_key);
        if ($viewer_id) $args['viewer_id'] = $viewer_id;
        if ($source_ids) {
            if (!is_array($source_ids)) $source_ids = array($source_ids);
            $args['source_ids'] = Zend_Json::encode($source_ids);
        }
        if ($metadata) {
            if (!is_array($metadata)) $metadata = array($metadata);
            foreach ($metadata as $k=>$v) {
                if (!in_array($v, self::$metadataFields)) {
                    unset($metadata[$k]);
                }
            }
            $args['metadata'] = Zend_Json::encode($metadata);
        }
        if ($start_time) $args['start_time'] = $start_time;
        if ($end_time) $args['end_time'] = $end_time;
        if ($limit) $args['limit'] = $limit;
        if ($filter_key) $args['filter_key'] = $filter_key;

        return $this->callRestMethod('stream.get', $args);
    }

    /**
     * Get Comments on a post
     *
     * @param string $post_id id to get comments from
     *
     * @link http://wiki.developers.facebook.com/index.php/Stream.getComments
     * @link http://developers.facebook.com/docs/reference/rest/stream.getComments
     */
    public function getComments($post_id) 
    {
        return $this->callRestMethod('stream.getComments', 
            array('post_id' => $post_id));
    }

    /**
     * Get Stream Filters
     * @param int $uid  User's ID, optional if session key present
     * @param string $session_key Facebook session key
     *
     * @link http://wiki.developers.facebook.com/index.php/Stream.getFilters
     * @link http://developers.facebook.com/docs/reference/rest/stream.getFilters
     */
    public function getFilters($uid = null, $session_key = null) 
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

        return $this->callRestMethod('stream.getFilters', $args);
    }

    /**
     * Add Comment on a post
     *
     * @param string $post_id id to get comments from
     * @param int $uid  User's ID, optional if session key present
     * @param string $session_key Facebook session key
     *
     * @link http://wiki.developers.facebook.com/index.php/Stream.addComment
     * @link http://developers.facebook.com/docs/reference/rest/stream.addComment
     */
    public function addComment($post_id, $comment, $uid = null, $session_key = null) 
    {
        if (!$session_key) {
            $session_key = $this->_core->getClient()->hasUserAuth();
        }

        $uid = ($uid) ? $uid : $this->_core->getUserId();
        if (!$uid && !$session_key) {
            throw new Zend_Service_Facebook_Exception('UID or Session Key Required');
        }

        $args = array('post_id'=>$post_id, 'comment'=>$comment);
        if ($session_key) $args['session_key'] = $session_key;
        if ($uid) $args['uid'] = $uid;

        return $this->callRestMethod('stream.addComment', $args);
    }

    /**
     * Remove Comment
     *
     * @param string $comment_id id to get comments from
     * @param int $uid  User's ID, optional if session key present
     * @param string $session_key Facebook session key
     *
     * @link http://wiki.developers.facebook.com/index.php/Stream.removeComment
     * @link http://developers.facebook.com/docs/reference/rest/stream.removeComment
     */
    public function removeComment($comment_id, $uid = null, $session_key = null) 
    {
        if (!$session_key) {
            $session_key = $this->_core->getClient()->hasUserAuth();
        }

        $uid = ($uid) ? $uid : $this->_core->getUserId();
        if (!$uid && !$session_key) {
            throw new Zend_Service_Facebook_Exception('UID or Session Key Required');
        }

        $args = array('comment_id'=>$comment_id);
        if ($session_key) $args['session_key'] = $session_key;
        if ($uid) $args['uid'] = $uid;

        return $this->callRestMethod('stream.removeComment', $args);
    }

    /**
     * Add Like on a post
     *
     * @param string $post_id id to get comments from
     * @param int $uid  User's ID, optional if session key present
     * @param string $session_key Facebook session key
     *
     * @link http://wiki.developers.facebook.com/index.php/Stream.addLike
     * @link http://developers.facebook.com/docs/reference/rest/stream.addLike
     */
    public function addLike($post_id, $uid = null, $session_key = null) 
    {
        if (!$session_key) {
            $session_key = $this->_core->getClient()->hasUserAuth();
        }

        $uid = ($uid) ? $uid : $this->_core->getUserId();
        if (!$uid && !$session_key) {
            throw new Zend_Service_Facebook_Exception('UID or Session Key Required');
        }

        $args = array('post_id'=>$post_id);
        if ($session_key) $args['session_key'] = $session_key;
        if ($uid) $args['uid'] = $uid;

        return $this->callRestMethod('stream.addLike', $args);
    }

    /**
     * remove Like on a post
     *
     * @param string $post_id id to get comments from
     * @param int $uid  User's ID, optional if session key present
     * @param string $session_key Facebook session key
     *
     * @link http://wiki.developers.facebook.com/index.php/Stream.removeLike
     * @link http://developers.facebook.com/docs/reference/rest/stream.removeLike
     */
    public function removeLike($post_id, $uid = null, $session_key = null) 
    {
        if (!$session_key) {
            $session_key = $this->_core->getClient()->hasUserAuth();
        }

        $uid = ($uid) ? $uid : $this->_core->getUserId();
        if (!$uid && !$session_key) {
            throw new Zend_Service_Facebook_Exception('UID or Session Key Required');
        }

        $args = array('post_id'=>$post_id);
        if ($session_key) $args['session_key'] = $session_key;
        if ($uid) $args['uid'] = $uid;

        return $this->callRestMethod('stream.removeLike', $args);
    }

    /**
     * Publish to Stream
     *
     * @param string $message post message
     * @param Zend_Service_Facebook_Stream_Attachment $attachment attachment metadata
     * @param array $action_links array of Zend_Service_Facebook_Stream_ActionLink objs
     * @param string $target_id id where content is published
     * @param int $uid  User's ID, optional if session key present
     * @param string $session_key Facebook session key
     *
     * @link http://wiki.developers.facebook.com/index.php/Stream.publish
     * @link http://developers.facebook.com/docs/reference/rest/stream.publish
     */
    public function publish($message = null, 
        Zend_Service_Facebook_Data_StreamAttachment $attachment = null, 
        $action_links = null, 
        $target_id = null, $uid = null, $session_key = null) 
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
        if ($target_id) $args['target_id'] = $target_id;
        if ($message) $args['message'] = $message;

        if ($attachment) $args['attachment'] = $attachment->toJSON();

        if ($action_links) {
            if (!is_array($action_links)) {
                if ($action_links instanceof Zend_Service_Facebook_Data_StreamActionLink) {
                    $action_links = array($action_links);
                } else {
                    throw new Zend_Service_Facebook_Exception('Action Links bad format');
                }
            } 
            $links = array();
            foreach ($action_links as $l) {
                if (!($l instanceof Zend_Service_Facebook_Data_StreamActionLink)) continue;
                $links[] = $l->toArray();
            }
            if (!$links) {
                throw new Zend_Service_Facebook_Exception('Action Links bad format');
            }
            $args['action_links'] = Zend_Json::encode($links);
        }

        return $this->callRestMethod('stream.publish', $args);
    }

    /**
     * remove post
     *
     * @param string $post_id id to get comments from
     * @param int $uid  User's ID, optional if session key present
     * @param string $session_key Facebook session key
     *
     * @link http://wiki.developers.facebook.com/index.php/Stream.remove
     * @link http://developers.facebook.com/docs/reference/rest/stream.remove
     */
    public function remove($post_id, $uid = null, $session_key = null) 
    {
        if (!$session_key) {
            $session_key = $this->_core->getClient()->hasUserAuth();
        }

        $uid = ($uid) ? $uid : $this->_core->getUserId();
        if (!$uid && !$session_key) {
            throw new Zend_Service_Facebook_Exception('UID or Session Key Required');
        }

        $args = array('post_id'=>$post_id);
        if ($session_key) $args['session_key'] = $session_key;
        if ($uid) $args['uid'] = $uid;

        return $this->callRestMethod('stream.remove', $args);
    }

}
