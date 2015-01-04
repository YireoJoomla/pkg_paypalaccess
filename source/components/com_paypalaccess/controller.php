<?php
/**
 * Joomla! component PayPal Access
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Include the loader
require_once(JPATH_ADMINISTRATOR.'/components/com_paypalaccess/lib/loader.php');
require_once(JPATH_COMPONENT.'/lib/auth.php');

/**
 * PayPal Access Controller 
 *
 * @package MageBridge
 */
class PayPalAccessController extends YireoController
{
    /**
     * Constructor
     *
     * @access public
     * @param null
     * @return null
     */
    public function __construct()
    {
        $this->_allow_tasks = array('login', 'logout', 'callback');
        parent::__construct();
        $this->registerTask('postlogin', 'login');
    }

    /*
     * Login method 
     *
     * @param null
     * @return null
     */
    public function login()
    {
        // Variables
        $application = JFactory::getApplication();
        $user = JFactory::getUser();
        $returnUrl = $this->getReturnUrl();
        $hostname = JURI::getInstance()->toString(array('host'));

        // Check whether profile-information already exists
        if(!empty($_SESSION['ppa']['profile'])) {
            $profile = $_SESSION['ppa']['profile'];
            require_once JPATH_COMPONENT.'/models/user.php';
            $ppa_user = new PayPalAccessModelUser();
            $name = (isset($profile->name)) ? $profile->name : null;
            $ppa_user->login($profile->email, $name);

            echo '<script type="text/javascript">window.opener.location.reload(); self.close();</script>';
            $application->close();
            return;
        }

        // Check for the parameters
        $params = JComponentHelper::getParams('com_paypalaccess');
        $client_id = $params->get('client_id');
        $client_secret = $params->get('client_secret');
        if(empty($client_id) || empty($client_secret)) {
            $application->redirect($returnUrl, JText::_('PayPal Access is not configured properly yet'));
            $application->close();
            return;
        }

        // Check for CURL
        if (function_exists('curl_init') == false) {
            $application->redirect($returnUrl, JText::_('PHP CURL-support is required'));
            $application->close();
            return;
        }

        // Fetch the authorization URL
        $ppaccess = new PayPalAccess($client_id, $client_secret, 'openid email profile', $returnUrl);
        $authUrl = $ppaccess->get_auth_url();

        $application->redirect($authUrl);
        $application->close();
        return;
    }

    /*
     * Login method 
     *
     * @param null
     * @return null
     */
    public function logout()
    {
        // Fetch the return-URL
        $returnUrl = $this->getReturnUrl();

        // Wipe out the current session
        unset($_SESSION['ppa']);

        // Check for the parameters
        $params = JComponentHelper::getParams('com_paypalaccess');
        $client_id = $params->get('client_id');
        $client_secret = $params->get('client_secret');

        // Logout from PayPal Access
        $ppaccess = new PayPalAccess($client_id, $client_secret, 'openid email profile', $returnUrl);
        $ppaccess->end_session();

        // Redirect
        $application = JFactory::getApplication();
        $application->logout();
        $application->redirect($returnUrl);
        $application->close();
        return;
    }

    /*
     * Popup-close method 
     *
     * @param null
     * @return null
     */
    public function callback()
    {
        // System variables
		$session = JFactory::getSession();
        $application = JFactory::getApplication();

        // Do a bogus-call to initialize the session
		$sessionQueue = $session->get('application.queue');
			
        // Check for an unexpected error
        $error = JRequest::getCmd('error');
        if(!empty($error)) {
            unset($_SESSION['ppa']);
            echo '<script type="text/javascript">window.opener.location.reload(); self.close();</script>';
            $application->close();
            exit;
        }

        // Check for the parameters
        $params = JComponentHelper::getParams('com_paypalaccess');
        $client_id = $params->get('client_id');
        $client_secret = $params->get('client_secret');
        $returnUrl = $this->getReturnUrl();

        // Get the PayPal object
        $ppaccess = new PayPalAccess($client_id, $client_secret, 'openid email profile', $returnUrl);
        $token = $ppaccess->get_access_token();
        $profile = $ppaccess->get_profile();
        $_SESSION['ppa']['profile'] = $profile;

        // Check for an email-address and login in Joomla!
        if(isset($profile->email)) {
            require_once JPATH_COMPONENT.'/models/user.php';
            $ppa_user = new PayPalAccessModelUser();
            $name = (isset($profile->name)) ? $profile->name : null;
            $rt = $ppa_user->login($profile->email, $name);

            if($rt == false) {
                unset($_SESSION['ppa']);
            }
        }
    
        // Close this popup and refresn the parent-screen
        echo '<script type="text/javascript">window.opener.location.reload(); self.close();</script>';

        // Close Joomla!
        $application->close();
        return;
    }

    /*
     * Method to get the return-URL
     *
     * @param null
     * @return null
     */
    public function getReturnUrl()
    {
        $popup = (JRequest::getCmd('tmpl') == 'component') ? true : false;
        if ($popup) {
            $returnUrl = JRoute::_('index.php?option=com_paypalaccess&task=callback&tmpl=component');

        } else {
            if (!empty($_SESSION['ppa']['return_url'])) {
                $returnUrl = $_SESSION['ppa']['return_url'];
            } else {
                $returnUrl = base64_decode(JRequest::getString('return'));
                if(strpos($returnUrl, JURI::root()) == false) $returnUrl = JURI::root();
                $_SESSION['ppa']['return_url'] = $returnUrl;
            }
            if (empty($returnUrl)) $returnUrl = JRoute::_('index.php');
        }

        if(preg_match('/^(http|https)\:/', $returnUrl) == false) {
            $returnUrl = JURI::base().preg_replace('/^\//', '', $returnUrl);
        }

        return $returnUrl;
    }
}
