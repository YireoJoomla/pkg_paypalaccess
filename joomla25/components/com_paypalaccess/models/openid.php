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

require_once JPATH_COMPONENT.DS.'models'.DS.'openid'.DS.'abstract.php';

class OpenID extends LightOpenID
{
    protected $definedAttributes = array(
        'id'        => 'https://www.paypal.com/webapps/auth/schema/payerID',
        'email'     => 'http://axschema.org/contact/email',
        'fname'     => 'http://axschema.org/namePerson/first',
        'lname'     => 'http://axschema.org/namePerson/last',
        'fullname'  => 'http://schema.openid.net/contact/fullname',
        'postcode'  => 'http://axschema.org/contact/postalCode/home',
        'country'   => 'http://axschema.org/contact/country/home',
        'street1'   => 'http://schema.openid.net/contact/street1',
        'street2'   => 'http://schema.openid.net/contact/street2',
        'city'      => 'http://axschema.org/contact/city/home',
        'state'     => 'http://axschema.org/contact/state/home',
        'phone'     => 'http://axschema.org/contact/phone/default',
    );

    public function __construct($host)
    {
        parent::__construct($host);
        if(JRequest::getCmd('task') == 'postlogin') {
            $returnUrl = JRoute::_('index.php?option=com_paypalaccess&task=postlogin&tmpl=component');
        } else {
            $returnUrl = JRoute::_('index.php?option=com_paypalaccess&task=login&tmpl=component');
        }
        $this->returnUrl = preg_replace('/\&amp\;/', '&', JURI::root().$returnUrl);
        $this->required = array('id', 'email', 'fname', 'lname');
        $this->optional = array('fullname', 'postcode', 'country', 'street1', 'street2', 'city', 'state', 'phone');
        $this->identity = 'https://www.paypal.com/webapps/auth/server';
    }

    protected function axParams()
    {
        $params = parent::axParams();
        foreach($params as $index => $param) {
            $name = preg_replace('/^openid.ax.type./', '', $index);
            if(array_key_exists($name, $this->definedAttributes)) {
                $params[$index] = $this->definedAttributes[$name];
            }
        }

        return $params;
    }

    protected function getAxAttributes()
    {
        $attributes = parent::getAxAttributes();
        $newAttributes = array();
        foreach($attributes as $name => $value) {
            $name = preg_replace('/([a-zA-Z0-9]+)\//', '', $name);
            $newAttributes[$name] = $value;
        }
        return $newAttributes;
    }
}
