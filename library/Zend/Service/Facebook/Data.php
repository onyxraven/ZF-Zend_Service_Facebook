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

require_once 'Zend/Json.php';

/**
 * Facebook Data structures abstract
 *
 * Enables serialization for the structures that need to be built for the api
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Zend_Service_Facebook
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Service_Facebook_Data 
{

    /**
     * data 
     * 
     * @var array
     */
    public $data;

    /**
     * return a json serialized representation
     * 
     * @return string
     */
    public function toJSON() 
    {
        return Zend_Json::encode($this->toArray());
    }

    /**
     * return an array representation of this class
     * 
     * @return array
     */
    public function toArray() 
    {
        return $this->data;
    }

}
