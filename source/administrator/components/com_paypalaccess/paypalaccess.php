<?php
/*
 * Joomla! component PayPal Access
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

// Require the base controller
require_once (JPATH_COMPONENT.'/controller.php');
$controller	= new PaypalAccessController( );

// Perform the Request task
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();

