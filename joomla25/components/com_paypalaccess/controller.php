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

// Include the parent controller
jimport( 'joomla.application.component.controller' );

/**
 * PayPal Access Controller 
 *
 * @package MageBridge
 */
class PayPalAccessController extends JController
{
    /* 
     * Constructor
     *
     * @param null
     * @return null
     */
    public function __construct()
    {
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
        $application = JFactory::getApplication();
        $returnUrl = $this->getReturnUrl();

        require_once JPATH_COMPONENT.DS.'models'.DS.'openid.php';

        try {
            $hostname = JURI::getInstance()->toString(array('host'));
            $openid = new OpenID($hostname);

            if(!$openid->mode) {
                $application->redirect($openid->authUrl());
                $application->close();
                return;

            } elseif($openid->mode == 'cancel') {
                $application->redirect($returnUrl, JText::_('Cancelled PayPal Access'), 'error');
                $application->close();
                return;

            } else {
        
                if($openid->validate()) {
                    $attributes = $openid->getAttributes();
                    $_SESSION['ppa'] = $attributes;
                    $_SESSION['ppa']['token'] = JFactory::getSession()->getToken();

                    if(isset($attributes['email'])) {
                        require_once JPATH_COMPONENT.DS.'models'.DS.'user.php';
                        $ppa_user = new PayPalAccessModelUser();
                        $ppa_user->login($attributes['email']);
                    }

                    $application->redirect($returnUrl, JText::_('Succesfull authentication through PayPal Access'));
                    $application->close();
                    return;

                } else {

                    $application->redirect($returnUrl, JText::_('PayPal Access authentication failed'), 'error');
                    $application->close();
                    return;

                }
            }
        } catch(ErrorException $e) {
            $application->redirect($returnUrl, JText::_('PayPal Access Error: '.$e->getMessage()), 'error');
            $application->close();
            return;
        }
    }

    /*
     * Login method 
     *
     * @param null
     * @return null
     */
    public function logout()
    {
        $returnUrl = $this->getReturnUrl();
        unset($_SESSION['ppa']);
        unset($_SESSION['ppa_return']);

        $application = JFactory::getApplication();
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
    public function close()
    {
        // Do a bogus-call to initialize the session
		$session =& JFactory::getSession();
		$sessionQueue = $session->get('application.queue');

        // Close this popup and refresn the parent-screen
        echo '<script type="text/javascript">self.close(); window.opener.location.reload();</script>';

        // Close Joomla!
        $application = JFactory::getApplication();
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

        if($popup) {
            $returnUrl = JRoute::_('index.php?option=com_paypalaccess&task=close&tmpl=component');

        } else {
            if(!empty($_SESSION['ppa_return'])) {
                $returnUrl = $_SESSION['ppa_return'];
            } else {
                $returnUrl = base64_decode(JRequest::getString('return'));
                $_SESSION['ppa_return'] = $returnUrl;
            }
            if(empty($returnUrl)) $returnUrl = JRoute::_('index.php');
        }

        return $returnUrl;
    }
}
