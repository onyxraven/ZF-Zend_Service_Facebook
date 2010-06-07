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

require_once 'Zend/Service/Facebook/Data.php';

/**
 * Facebook Data structures for a stream action link
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Zend_Service_Facebook
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Zend_Service_Facebook_Data_StreamAttachment 
extends Zend_Service_Facebook_Data 
{

    /**
     * setName 
     * 
     * @param string $in 
     * @return self
     */
    public function setName($in) 
    {
        $this->data['name'] = $in;
        return $this;
    }

    /**
     * setHref 
     * 
     * @param string $in 
     * @return self
     */
    public function setHref($in) 
    {
        //todo validate url
        $this->data['href'] = $in;
        return $this;
    }

    /**
     * setCaption 
     * 
     * @param string $in 
     * @return self
     */
    public function setCaption($in) 
    {
        $this->data['caption'] = $in;
        return $this;
    }

    /**
     * setDescription 
     * 
     * @param string $in 
     * @return self
     */
    public function setDescription($in) 
    {
        $this->data['description'] = $in;
        return $this;
    }

    /**
     * setProperty 
     * 
     * @param string $key 
     * @param string $value 
     * @return self
     */
    public function setProperty($key, $value=null) 
    {
        if (is_array($key)) {
            foreach ($key as $k=>$v) $this->setProperty($k, $v);
        }
        $this->data['properties'][$key] = $value;
        return $this;
    }

    /**
     * setMedia 
     * 
     * @param string $type 
     * @param array $params 
     * @return self
     */
    public function setMedia($type, $params) 
    {
        $params['type'] = $type;
        $this->data['media'][] = $params;
        return $this;
    }

    /**
     * setImage 
     * 
     * @param string $src 
     * @param string $href 
     * @return self
     */
    public function setImage($src, $href) 
    {
        //todo validate url
        $params = array('src' => $src, 'href' => $href);
        return $this->setMedia('image', $params);
    }

    /**
     * setFlash
     *
     * @param string $swfsrc 
     * @param string $imgsrc 
     * @param int $width 
     * @param int $height 
     * @param int $expanded_width 
     * @param int $expanded_height 
     * @return self
     */
    public function setFlash($swfsrc, $imgsrc, 
        $width = null, $height = null, 
        $expanded_width = null, $expanded_height = null) 
    {
        //todo validate url
        $params = array('swfsrc' => $swfsrc, 'imgsrc' => $imgsrc);
        if ($width) $params['width'] = $width;
        if ($height) $params['height'] = $height;
        if ($expanded_width) $params['expanded_width'] = $expanded_width;
        if ($expanded_height) $params['expanded_height'] = $expanded_height;
        return $this->setMedia('flash', $params);
    }

    /**
     * setMp3 
     * 
     * @param string $src 
     * @param string $title 
     * @param string $artist 
     * @param string $album 
     * @return self
     */
    public function setMp3($src, $title = null, $artist = null, $album = null) 
    {
        //todo validate url
        $params = array('src' => $src);
        if ($title) $params['title'] = $title;
        if ($artist) $params['artist'] = $artist;
        if ($album) $params['album'] = $album;
        return $this->setMedia('mp3', $params);
    }

    /**
     * setCommentsXid 
     * 
     * @param mixed $in 
     * @return self
     */
    public function setCommentsXid($in) 
    {
        $this->data['comments_xid'] = $in;
        return $this;
    }

    /**
     * setOther 
     * 
     * @param string $key 
     * @param mixed $value 
     * @return self
     */
    public function setOther($key, $value) 
    {
        $this->data[$key] = $value;
        return $this;
    }

}
