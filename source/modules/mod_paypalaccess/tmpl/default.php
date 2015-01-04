<?php
/**
 * Joomla! module PayPal Access
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access'); 
?>
<div class="paypalaccess">
    <?php if(empty($ppa['profile'])) { ?>
        <?php if($linktype == 'anchor') { ?>
            <a class="lightbox" rel="lightbox" href="<?php echo $login_url; ?>"><img src="<?php echo $image ?>" /></a>
        <?php } else { ?>
            <form>
            <?php $js_options = 'width=400,height=550,resizable=no,scrollbars=yes,toolbar=yes,location=yes'; ?>
            <input type="image" name="login" src="<?php echo $image ?>" onclick="window.open('<?php echo $login_url; ?>', '<?php echo JText::_('PayPal Access'); ?>', '<?php echo $js_options; ?>'); return false;" border="0" />
            </form>
        <?php } ?>
    <?php } else { ?>
        <?php $name = (!empty($ppa['profile']->name)) ? $ppa['profile']->name : $ppa['profile']->email; ?>
        <div class="welcome">
            <?php echo JText::sprintf('Hello, %s', $name); ?>
        </div>
        <div class="other">
            <a href="<?php echo $logout_url; ?>"><?php echo JText::_('Logout from PayPal Access.'); ?></a>
        </div>
    <?php } ?>
</div>
<?php if($debug && !empty($ppa)) { ?>
<pre><?php print_r($ppa); ?></pre>
<a href="<?php echo $reset_url; ?>"><?php echo JText::_('Reset'); ?></a>
<?php } ?>
