<?php
/**
 * Joomla! component PayPal access
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die( 'Restricted access' );

// SDK-configuration
define('PP_CONFIG_PATH', JPATH_COMPONENT.'/sdk_config.ini');

// Require helper
require_once JPATH_COMPONENT.'/helpers/helper.php';

// Require the controller
require_once JPATH_COMPONENT.'/controller.php';
$controller = new PayPalAccessController( );

// Perform the Request task
$controller->execute( JRequest::getCmd('task'));
$controller->redirect();
