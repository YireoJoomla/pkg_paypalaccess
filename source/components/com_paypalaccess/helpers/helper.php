<?php
/**
 * Joomla! component PayPal Access
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

class PpaHelper
{
    /*
     * Helper-method to return the default Joomla! usergroup ID
     *
     * @param null
     * @return int
     */
    public function getDefaultJoomlaGroupid()
    {
        $params = JComponentHelper::getParams('com_users');
        $group_id = $params->get('new_usertype');
        return $group_id;
    }

    /*
     * Helper-method to return the default Joomla! usergroup name
     *
     * @param null
     * @return string
     */
    public function getDefaultJoomlaGroup()
    {
        return null;
    }
}
