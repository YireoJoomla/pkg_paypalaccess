<?php
/**
 * Joomla! component PayPal access
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2011
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die( 'Restricted access' );

// Require helper
require_once JPATH_COMPONENT.DS.'helpers'.DS.'helper.php';

// Require the controller
require_once JPATH_COMPONENT.DS.'controller.php';
$controller = new PayPalAccessController( );

// Perform the Request task
$controller->execute( JRequest::getCmd('task'));
$controller->redirect();
