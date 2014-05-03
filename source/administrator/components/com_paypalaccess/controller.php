<?php
/**
 * Joomla! component PayPal Access
 *
 * @author Yireo
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com/
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

// Include the loader
require_once JPATH_COMPONENT.'/lib/loader.php';

class PaypalAccessController extends YireoController
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
        $this->_default_view = 'home';
        parent::__construct();
    }
}
