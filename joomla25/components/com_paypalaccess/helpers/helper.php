<?php
/**
 * Joomla! component PayPal Access
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2011
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

class PpaHelper
{
    /*
     * Helper-method to get the current Joomla! core version
     * 
     * @param null
     * @return bool
     */
    public function isJoomlaVersion($version = null)
    {
        JLoader::import( 'joomla.version' );
        $jversion = new JVersion();
        if(version_compare( $jversion->RELEASE, $version, 'eq')) {
            return true;
        }
        return false;
    }

    /*
     * Helper-method to return the default Joomla! usergroup ID
     *
     * @param null
     * @return int
     */
    public function getDefaultJoomlaGroupid()
    {
        if(self::isJoomlaVersion('1.5')) {
            $group = self::getDefaultJoomlaGroup();
            $group_id = (int)JFactory::getACL()->get_group_id('', $group);
            return $group_id;
        } else {
            $params = JComponentHelper::getParams('com_users');
            $group_id = $params->get('new_usertype');
            return $group_id;
        }
    }

    /*
     * Helper-method to return the default Joomla! usergroup name
     *
     * @param null
     * @return string
     */
    public function getDefaultJoomlaGroup()
    {
        if(self::isJoomlaVersion('1.5')) {
            $conf = &JComponentHelper::getParams('com_users');
            return $conf->get('new_usertype', 'Registered');
        }
        return null;
    }
}
