<?php
/**
 * Joomla! module - PayPal Access
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
*/

// Make sure this file is not called directly
defined('_JEXEC') or die();

// Fetch the variables
$linktype = $params->get('linktype', 'form');
$debug = $params->get('debug', 0);
$button = $params->get('button', 'blue');

// Construct other variables
$current_url = base64_encode(JURI::current());
$login_url = JRoute::_('index.php?option=com_paypalaccess&task=login&tmpl=component&return='.$current_url);
$logout_url = JRoute::_('index.php?option=com_paypalaccess&task=logout&return='.$current_url);
$reset_url = JRoute::_('index.php?option=com_paypalaccess&task=logout&return='.$current_url);
if($button == 'grey') {
    $image = 'https://www.paypalobjects.com/webstatic/en_US/developer/docs/lipp/loginwithpaypalbutton2.png';
} else {
    $image = 'https://www.paypalobjects.com/webstatic/en_US/developer/docs/lipp/loginwithpaypalbutton.png'; 
}

// PPA-data
$ppa = (!empty($_SESSION['ppa'])) ? $_SESSION['ppa'] : null;

// Include the layout-file
require(JModuleHelper::getLayoutPath('mod_paypalaccess'));
