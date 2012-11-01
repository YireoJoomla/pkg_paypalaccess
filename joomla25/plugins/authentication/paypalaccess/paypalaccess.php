<?php
/**
 * Joomla! PayPal Access Authentication plugin
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2011
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
    public function onAuthenticate( $credentials, $options, &$response )
    {
        // Disable backend usage
        if(JFactory::getApplication()->isSite() == false) {
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Could not authenticate';
            return false;
        }

        // Check the token
        if(!isset($_SESSION['ppa']['token'])) {
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'No token';
            return false;
        }

        if($_SESSION['ppa']['token'] != JFactory::getSession()->getToken()) {
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Invalid token';
            return false;
        }

        // Give success when these credentials list a successful token
        if(!isset($credentials['email'])) {
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Could not authenticate';
            return false;
        }

        if(isset($_SESSION['ppa']['email'])) $response->email = $_SESSION['ppa']['email'];
        if(isset($_SESSION['ppa']['first'])) $response->fullname = $_SESSION['ppa']['first'];
        if(isset($_SESSION['ppa']['last'])) $response->fullname .= ' '.$_SESSION['ppa']['last'];
        $response->fullname = trim($response->fullname);
		$response->status = JAUTHENTICATE_STATUS_SUCCESS;
		$response->error_message = '';
        return true;
    }

    /**
     * Joomla! 1.6 alias
     * 
     * @access public
     * @param array $credentials
     * @param array $options
     * @param object $response
     * @return bool
     */
    public function onUserAuthenticate( $credentials, $options, &$response )
    {
        return $this->onAuthenticate($credentials, $options, $response);
    }
}
