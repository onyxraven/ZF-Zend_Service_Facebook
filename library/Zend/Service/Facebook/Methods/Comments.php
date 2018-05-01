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
 * Search 'comments.' in 
 * @link http://wiki.developers.facebook.com/index.php/API
 * @link http://developers.facebook.com/docs/reference/rest/#data-retrieval-methods
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Zend_Service_Facebook
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Facebook_Methods_Comments 
extends Zend_Service_Facebook_Methods 
{

    /**
     * Get Comments
     *
     * @param string $xid 
     * @param string $object_id 
     * @link http://developers.facebook.com/docs/reference/rest/comments.get
     */
    public function get($xid = null, $object_id = null) 
    {
        if (!$xid && !$object_id) {
            throw new Zend_Service_Facebook_Exception('xid or object_id must be specified');
        }
        if ($xid) $args['xid'] = $xid;
        if ($object_id) $args['object_id'] = $object_id;

        return $this->callRestMethod('comments.get', $args);
    }

    /**
     * add comment
     *
     * @param string $text
     * @param string $xid 
     * @param string $object_id 
     * @param string $title
     * @param string $url
     * @param string $publish_to_stream
     * @param int $uid  User's ID, optional if session key present
     * @param string $session_key Facebook session key
     *
     * @link http://developers.facebook.com/docs/reference/rest/comments.add 
     */
    public function add($text, $xid = null, $object_id = null,
                        $title = null, $url = null, $publish_to_stream = false,
                        $uid = null, $session_key = null) 
    {
        $args = array('text'=>$text);
        if (!$session_key) {
            $session_key = $this->_core->getClient()->hasUserAuth();
        }
        $uid = ($uid) ? $uid : $this->_core->getUserId();
        if (!$uid && !$session_key) {
            throw new Zend_Service_Facebook_Exception('UID or Session Key Required');
        }
        if ($session_key) $args['session_key'] = $session_key;
        if ($uid) $args['uid'] = $uid;

        if (!$xid && !$object_id) {
            throw new Zend_Service_Facebook_Exception('xid or object_id must be specified');
        }
        if ($xid) $args['xid'] = $xid;
        if ($object_id) $args['object_id'] = $object_id;

        if ($publish_to_stream && !($title && $url)) {
            throw new Zend_Service_Facebook_Exception('Title and Url neccesary if publish to stream');
        }
        if ($title) $args['title'] = $title;
        if ($url) $args['url'] = $url;
        if ($publish_to_stream) $args['publish_to_stream'] = (int) $publish_to_stream;

        return $this->callRestMethod('comments.add', $args);
    }

    /**
     * remove comments
     *
     * @param string $post_id id to get comments from
     * @param int $uid  User's ID, optional if session key present
     * @param string $session_key Facebook session key
     *
     * @link http://wiki.developers.facebook.com/index.php/Stream.remove
     * @link http://developers.facebook.com/docs/reference/rest/stream.remove
     */
    public function remove($comment_id, $xid = null, $object_id = null, $session_key = null) 
    {
        $args = array('comment_id'=>$comment_id);
        if ($session_key) $args['session_key'] = $session_key;

        if (!$xid && !$object_id) {
            throw new Zend_Service_Facebook_Exception('xid or object_id must be specified');
        }
        if ($xid) $args['xid'] = $xid;
        if ($object_id) $args['object_id'] = $object_id;

        return $this->callRestMethod('comments.remove', $args);
    }

}
