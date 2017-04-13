<?php
/**
 * @version     $Id
 * @package     JSNUniform
 * @subpackage  Update
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2016 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


// Display messagess
if (JFactory::getApplication()->input->getInt('ajax') != 1)
{
    echo $this->msgs;
}

$ext 	= $this->extension;
$lang	= JFactory::getLanguage();
$lang->load('plg_' . $ext->folder . '_' . $ext->element);
?>

<div class="jsn-uniform-page-uninstall">
    <div class="jsn-bootstrap">
        <div id="jsn-uninstall-plugin-action">
            <h1><?php echo JText::sprintf('JSN_UNIFORM_INTEGRATION_UNINSTALL_PLUGIN_TITLE', JText::_($ext->name)); ?></h1>
            <div class="jsn-uniform-install-infomartion">
                <div class="alert alert-warning">
                    <span class="label label-important"><?php echo JText::_('JSN_UNIFORM_GENERAL_IMPORTANT_NOTE'); ?></span>
                    <ul>
                        <li><?php echo JText::_('JSN_UNIFORM_INTEGRATION_UNINSTALL_INTRO_DESC'); ?></li>
                    </ul>
                </div>
                <div>           
					<?php if (empty($this->relatedForms)) { ?>
						<p style="text-align: center; font-weight: bold;"><?php echo JText::_('JSN_UNIFORM_DONT_HAVE_RELATED_FORM'); ?></p>
					<?php } else { ?>
						<p><?php echo JText::_('JSN_UNIFORM_INTEGRATION_UNINSTALL_RELATED_FORM_LIST'); ?>: </p>   
						<ul>
						<?php foreach ($this->relatedForms as $form) { ?>
							<li><?php echo $form->form_title; ?></li>
						<?php } ?>
						 </ul>
					<?php } ?>
                </div>
            </div>
        </div>
        <div id="jsn-uninstall-">
            <form name="JSNUninstallPlugin" method="POST" class="form-horizontal" autocomplete="off">
                <div class="form-actions">
                    <button class="btn btn-primary" id="jsn-uf-uninstall"><?php echo JText::_('JSN_UNIFORM_UNINSTALL'); ?></button>
                    <button class="btn btn-cancel" id="jsn-uf-uninstall-cancel"><?php echo JText::_('JSN_UNIFORM_CANCEL'); ?></button>
                </div>
                <input type="hidden" name="plugin_id" value="<?php echo $ext->extension_id; ?>">  
                <?php echo JHtml::_('form.token'); ?>       
            </form>
        </div>
    </div>
</div>

