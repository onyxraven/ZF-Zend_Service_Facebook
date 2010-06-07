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
class Zend_Service_Facebook_Data_StreamActionLink 
extends Zend_Service_Facebook_Data 
{

    /**
     * maximum text length
     */
    const MAX_TEXT_LEN = 25;

    /**
     * setText 
     * 
     * @param string $text 
     * @param bool $truncate truncate the string on the max text length?
     * @return self
     */
    public function setText($text, $truncate=false) 
    {
        //todo eval mb vs iconv
        $len = mb_strlen(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'), 'UTF-8');
        if ($len > self::MAX_TEXT_LEN) {
            if ($truncate) $text = mb_strimwidth($text, 0, self::MAX_TEXT_LEN, '', 'UTF-8');
            else throw new Zend_Service_Facebook_Exception('String too long');
        }
        $this->data['text'] = $text;
        return $this;
    }

    /**
     * setHref 
     * 
     * @param string $url 
     * @return self
     */
    public function setHref($url) 
    {
        //todo validate url
        $this->data['href'] = $url;
        return $this;
    }

}
