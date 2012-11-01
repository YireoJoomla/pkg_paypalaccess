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

class PayPalAccessModelUser
{
    /*
     * Method to login an user by its email-address
     *
     * @param string $email
     * @return bool|JUser 
     */
    public function login($email = null)
    {
        jimport('joomla.plugin.helper');
        if(JPluginHelper::isEnabled('authentication', 'paypalaccess') == false) {
            return false;
        }

        $user = self::loadByEmail($email);
        if(!empty($user) && isset($user->username)) {
            $username = $user->username;
        } else {
            $username = $email;
        }

        $options = array(
            'remember' => true,
        );

        $credentials = array(
            'username' => $username,
            'email' => $email,
            'password' => null,
        );

        $application = JFactory::getApplication();
        $application->login($credentials, $options);
    }

    /*
     * Method to load an user-record by its email address
     *
     * @param string $email
     * @return bool|JUser 
     */
    public function loadByEmail($email = null)
    {
        // Abort if the email is not set
        if(empty($email)) {
            return false;
        }

        // Fetch the user-record for this email-address
        $db = JFactory::getDBO();
        $query = "SELECT id FROM #__users WHERE `email` = ".$db->Quote($email);
        $db->setQuery($query);
        $row = $db->loadObject();

        // If there is no such a row, this user does not exist
        if(empty($row) || !isset($row->id) || !$row->id > 0) {
            return false;
        }

        // Load the user by its user-ID
        $user_id = $row->id;
        $user = JFactory::getUser();
        if($user->load($user_id) == false) {
            return false;
        }

        return $user;
    }
}
