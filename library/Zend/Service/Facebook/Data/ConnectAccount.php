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
 * Facebook Data structures for connecting an account
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Zend_Service_Facebook
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Facebook_Data_ConnectAccount 
extends Zend_Service_Facebook_Data 
{

    /**
     * setEmail 
     * 
     * @param string $email 
     * @return self
     */
    public function setEmail($email) 
    {
        return $this->setEmailHash(self::hashEmail($email));
    }

    /**
     * setEmailHash 
     * 
     * @param string $hash 
     * @return self
     */
    public function setEmailHash($hash) 
    {
        $this->data['email_hash'] = $hash;
        return $this;
    }

    /**
     * setAccountId 
     * 
     * @param string $id 
     * @return self
     */
    public function setAccountId($id) 
    {
        $this->data['account_id'] = $id;
        return $this;
    }

    /**
     * setAccountUrl 
     * 
     * @param string $url 
     * @return self
     */
    public function setAccountUrl($url) 
    {
        //todo validate url
        $this->data['account_url'] = $url;
        return $this;
    }

    /**
     * hashEmail 
     * 
     * @param string $email Email to hash
     * @return string Hashed email address
     */
    static public function hashEmail($email) 
    {
        $email = strtolower(trim($email));
        $crc32 = sprintf("%u", crc32($email));
        $md5   = md5($email);
        return $crc32 . '_' . $md5;
    }

}
