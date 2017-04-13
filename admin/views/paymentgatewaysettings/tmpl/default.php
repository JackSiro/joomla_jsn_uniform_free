<?php
/**
 * @version    $Id$
 * @package    JSN_Uniform
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2016 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

defined('_JEXEC') or die;
$toolTipArray = array('maxTitleChars'=>20000);
JHTML::_('behavior.tooltip', '.JSNUFHasTip', $toolTipArray);
$lang->load('plg_uniform_' . $this->extension_name);
$dispatcher = JEventDispatcher::getInstance();
JPluginHelper::importPlugin('uniform', $this->extension_name);
?>
<div class="paymentgateway-settings">
	<form action="<?php echo JRoute::_('index.php?option=com_uniform&view=paymentgatewaysettings&tmpl=component'); ?>" method="post" name="adminForm" id="profile-form" class="form-validate form-horizontal" enctype="multipart/form-data">
		<?php $dispatcher->trigger('renderConfigForm'); ?>
		<input type="hidden" class="extension_name" value="<?php echo $this->extension_name;?>">
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>