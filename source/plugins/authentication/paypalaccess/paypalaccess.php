<?php
/**
 * Joomla! PayPal Access Authentication plugin
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Import the parent class
jimport( 'joomla.plugin.plugin' );

/**
 * PayPal Access Authentication Plugin
 */
class plgAuthenticationPaypalaccess extends JPlugin
{
    /**
     * Handle the event that is generated when an user tries to login
     * 
     * @access public
     * @param array $credentials
     * @param array $options
     * @param object $response
     * @return bool
     */
    public function onUserAuthenticate( $credentials, $options, &$response )
    {
		$response->type = 'PaypalAccess';

        // Disable backend usage
        if(JFactory::getApplication()->isSite() == false) {
            $response->status = JAuthentication::STATUS_FAILURE;
			$response->error_message = 'Could not authenticate';
            return false;
        }

        // Check for the profile-array
        if(empty($_SESSION['ppa']['profile']->email)) {
            $response->status = JAuthentication::STATUS_FAILURE;
			$response->error_message = 'Invalid profile';
            return false;
        }

        // Profile-variable is validated
        $profile = $_SESSION['ppa']['profile'];

        // Fetch the user-record for this email-address
        $db = JFactory::getDBO();
        $query = "SELECT id FROM #__users WHERE `email` = ".$db->Quote($profile->email);
        $db->setQuery($query);
        $row = $db->loadObject();
        
        // If there is no such a row, fail authentication
        if (empty($row) || !isset($row->id) || !$row->id > 0) {
            $response->status = JAuthentication::STATUS_FAILURE;
			$response->error_message = 'No user found';
            return false;
        }

        // Complete authentication
		$user = JFactory::getUser($row->id);
        $response->email = $user->email;
        $response->fullname = $user->name;
		$response->language = $user->getParam('language');
        $response->status = JAuthentication::STATUS_SUCCESS;
		$response->error_message = '';

        return true;
    }
}
