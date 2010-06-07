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
 * Search 'admin.' in 
 * @link http://wiki.developers.facebook.com/index.php/API
 * @link http://developers.facebook.com/docs/reference/rest/#administrative-methods
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Zend_Service_Facebook
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Facebook_Methods_Admin 
extends Zend_Service_Facebook_Methods 
{

    /**
     * Default application property fields
     * 
     * @link http://wiki.developers.facebook.com/index.php/ApplicationProperties
     */
    public static $applicationProperties = array(
        'about_url',
        'app_id',
        'application_name',
        'authorize_url',
        'base_domain',
        'base_domains',
        'callback_url',
        'canvas_name',
        'connect_logo_url',
        'connect_preview_template',
        'connect_reclaim_url',
        'connect_url',
        'contact_email',
        'creator_uid',
        'dashboard_url',
        'default_column',
        'description',
        'desktop',
        'dev_mode',
        'edit_url',
        'email',
        'help_url',
        'icon_url',
        'iframe_enable_util',
        'ignore_ip_whitelist_for_ss',
        'info_changed_url',
        'installable',
        'ip_list',
        'is_mobile',
        'logo_url',
        'message_action',
        'post_authorize_redirect_url',
        'preload_fql',
        'privacy_url',
        'private_install',
        'profile_tab_url',
        'publish_action',
        'publish_self_action',
        'publish_self_url',
        'publish_url',
        'quick_transitions',
        'support_url',
        'tab_default_name',
        'targeted',
        'tos_url',
        'uninstall_url',
        'use_iframe',
        'video_rentals',
        'wide_mode',
        );

    /**
     * Gets property values previously set for an application.
     *
     * @param array $properties The properties to get, default is all
     *
     * @link http://wiki.developers.facebook.com/index.php/Admin.getAppProperties
     * @link http://developers.facebook.com/docs/reference/rest/admin.getAppProperties
     */
    public function getAppProperties($properties = null) 
    {
        if (empty($properties)) {
            $properties = self::$applicationProperties;
        } else {
            if (!is_array($properties)) {
                $properties = array($properties);
            }
            foreach ($properties as $k => $p) {
                if (!in_array($p, self::$applicationProperties)) {
                    unset($properties[$k]);
                }
            }
        }
        if (!$properties) {
            throw new Zend_Service_Facebook_Exception('no valid properties');
        }
        $stringProperties = Zend_Json::encode($properties);

        return $this->callRestMethod('admin.getAppProperties', 
            array('properties' => $stringProperties));
    }

    /**
     * Sets multiple properties for an application.
     *
     * @param array $properties Property / value assocative array of properties
     *
     * @link http://wiki.developers.facebook.com/index.php/Admin.setAppProperties
     * @link http://developers.facebook.com/docs/reference/rest/admin.setAppProperties
     */
    public function setAppProperties($properties = null) 
    {
        foreach ($properties as $k => $v) {
            if (!in_array($k, self::$applicationProperties)) {
                unset($properties[$k]);
            }
        }
        if (!$properties) {
            throw new Zend_Service_Facebook_Exception('no valid properties');
        }
        $stringProperties = Zend_Json::encode($properties);

        return $this->callRestMethod('admin.setAppProperties', 
            array('properties' => $jsonProperties));
    }

    /**
     * integrationPointNames 
     * 
     * @var array
     */
    public $integrationPointNames = array(
        'notifications_per_day',
        'announcement_notifications_per_week',
        'requests_per_day',
        'emails_per_day',
        'email_disable_message_location',
        );

    /**
     * Get the number of notifications your application can send on
     * behalf of a user per day.
     *
     * @return string integration point name
     *
     * @link http://wiki.developers.facebook.com/index.php/Admin.getAllocation
     * @link http://developers.facebook.com/docs/reference/rest/admin.getAllocation
     */
    public function getAllocation($name) 
    {
        if (!in_array($name, self::$integrationPointNames)) {
            throw new Zend_Service_Facebook_Exception('Bad name');
        }

        return $this->callRestMethod('admin.getAllocation', 
            array('integration_point_name' => $name));
    }

    //TODO metrics array
    //TODO getMetrics http://wiki.developers.facebook.com/index.php/Admin.getMetrics
    //http://developers.facebook.com/docs/reference/rest/admin.getAllocation

    /**
     * getRestrictions
     *
     * @link http://wiki.developers.facebook.com/index.php/Admin.getRestrictionInfo
     * @link http://developers.facebook.com/docs/reference/rest/admin.getRestrictionInfo
     */
    public function getRestrictionInfo() 
    {
        return $this->callRestMethod('admin.getRestrictionInfo');
    }
    //TODO setRestrictionInfo http://developers.facebook.com/docs/reference/rest/admin.setRestrictionInfo

    //TODO banUsers http://wiki.developers.facebook.com/index.php/Admin.banUsers
    //TODO banUsers http://developers.facebook.com/docs/reference/rest/admin.banUsers
    //TODO unbanUsers http://wiki.developers.facebook.com/index.php/Admin.unbanUsers
    //TODO getBannedUsers http://wiki.developers.facebook.com/index.php/Admin.getBannedUsers
    //TODO getBannedUsers http://developers.facebook.com/docs/reference/rest/admin.getBannedUsers

    /**
     * identifier fields
     * @var array
     */
    public static $appIdentifierFields = array(
        'application_id', 
        'application_api_key',
        'application_canvas_name',
        );
    /**
     * application.getPublicInfo (yes, not admin, but fits here)
     * 
     * @param mixed $identifier_name one of self::$appIdentifierFields
     * @param mixed $ident identifying value
     * @link http://wiki.developers.facebook.com/index.php/Application.getPublicInfo
     * @link http://developers.facebook.com/docs/reference/rest/application.getPublicInfo
     */
    public function getPublicInfo($identifier_name, $ident) 
    {
        if (!in_array($identifier_name, self::$appIdentifierFields)) {
            throw new Zend_Service_Facebook_Exception('Bad name');
        }

        return $this->callRestMethod('application.getPublicInfo', 
            array($identifier_name => $ident));
    }

    //intl.getTranslations
    //intl.uploadNativeStrings

}
