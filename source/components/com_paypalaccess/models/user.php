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

class PayPalAccessModelUser
{
    /*
     * Method to login an user by its email-address
     *
     * @param string $email
     * @return bool|JUser 
     */
    public function login($email = null, $name = null)
    {
        jimport('joomla.plugin.helper');
        if (JPluginHelper::isEnabled('authentication', 'paypalaccess') == false) {
            return false;
        }

        $user = self::loadByEmail($email, $name);
        if (!empty($user) && isset($user->username)) {
            $username = $user->username;
        } else {
            $username = $email;
        }

        $options = array(
            'remember' => true,
        );

        $credentials = array(
            'username' => $username,
            'password' => $email,
        );

        $application = JFactory::getApplication();
        $rt = $application->login($credentials, $options);
        return $rt;
    }

    /*
     * Method to load an user-record by its email address
     *
     * @param string $email
     * @return bool|JUser 
     */
    public function loadByEmail($email = null, $name = null)
    {
        // Abort if the email is not set
        if (empty($email)) {
            return false;
        }

        // Set the name to email if it is empty
        if (empty($name)) {
            $name = $email;
        }

        // Fetch the user-record for this email-address
        $db = JFactory::getDBO();
        $query = "SELECT id FROM #__users WHERE `email` = ".$db->Quote($email);
        $db->setQuery($query);
        $row = $db->loadObject();

        // If there is no such a row, this user does not exist so lets create it
        if (empty($row) || !isset($row->id) || !$row->id > 0) {

            // Make this optional
            if(JComponentHelper::getParams('com_paypalaccess')->get('autocreate_user', 1) == 0) {
                return false;
            }

            // Construct a data-array for this user
            $data = array(
                'name' => $name,
                'username' => $email,
                'email' => $email,
                'guest' => 0,
                'password' => '',
                'password2' => '',
            );

            // Current date
            $now = new JDate();
            $data['registerDate'] = (method_exists('JDate', 'toSql')) ? $now->toSql() : $now->toMySQL();

            // Get the com_user table-class and use it to store the data to the database
            $table = JTable::getInstance('user', 'JTable');
            $table->bind($data);
            $table->store();

            // Fetch the user-record for this email-address
            $db = JFactory::getDBO();
            $query = "SELECT id FROM #__users WHERE `email` = ".$db->Quote($email);
            $db->setQuery($query);
            $row = $db->loadObject();

            // Check whether the current user is part of any groups
            $db->setQuery('SELECT * FROM `#__user_usergroup_map` WHERE `user_id`='.$table->id);
            $rows = $db->loadObjectList();
            if (empty($rows)) {
                $group_id = JComponentHelper::getParams('com_users')->get('new_usertype', 2);
                if (!empty($group_id)) {
                    $db->setQuery('INSERT INTO `#__user_usergroup_map` SET `user_id`='.$table->id.', `group_id`='.$group_id);
                    $db->query(); 
                }
            }

            // Load the user by its user-ID
            $user_id = $row->id;
            $user = new JUser();
            if ($user->load($user_id) == false) {
                return false;
            }

            return $user;
        }

        // Load the user by its user-ID
        $user_id = $row->id;
        $user = JFactory::getUser();
        if ($user->load($user_id) == false) {
            return false;
        }

        // Joomla! user plugins
        $options = array('remember' => 1, 'action' => 'core.login.site', 'return' => null);
        $user = JArrayHelper::fromObject($user);
        JPluginHelper::importPlugin('user');
        JFactory::getApplication()->triggerEvent('onUserLogin', array($user, $options));

        return $user;
    }
}
