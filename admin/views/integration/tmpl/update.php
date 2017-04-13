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
$ext = $this->extension;
?>

<div class="jsn-uniform-page-install">
    <div class="jsn-bootstrap">
        <div id="jsn-update-plugin-action">
            <h1><?php echo JText::sprintf('JSN_UNIFORM_INTEGRATION_UPDATE_PLUGIN_TITLE', strtoupper((string) $ext->name)) ?></h1>
            <div class="jsn-uniform-install-infomartion">
                <p><?php echo JText::_('JSN_UNIFORM_INTEGRATION_UPDATE_INTRO_DESC'); ?></p>
                <div class="alert alert-warning">
                    <span class="label label-important"><?php echo JText::_('JSN_UNIFORM_INTEGRATION_UPDATE_IMPORTANT_NOTE'); ?></span>
                    <ul>
                        <li><?php echo JText::_('JSN_UNIFORM_INTEGRATION_UPDATE_INTRO_NOTE'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="jsn-update-plugin-login">
            <form name="JSNUpdatePluginLogin" method="POST" class="form-horizontal" autocomplete="off">
                <h2><?php echo JText::_('JSN_UNIFORM_INTEGRATION_UPDATE_LOGIN_HEAD'); ?></h2>
                <p><?php echo JText::_('JSN_UNIFORM_INTEGRATION_UPDATE_LOGIN_DESC'); ?></p>
                <div class="row-fluid">
                    <div class="span6">
                        <div class="control-group">
                            <label for="username" class="inline"><?php echo JText::_('JGLOBAL_USERNAME'); ?>:</label>
                            <input type="text" value="" class="input-xlarge" id="username" name="customer_username" />
                        </div>
                    </div>
                    <div class="span6">
                        <div class="control-group">
                            <label for="password" class="inline"><?php echo JText::_('JGLOBAL_PASSWORD'); ?>:</label>
                            <input type="password" value="" class="input-xlarge" id="password" name="customer_password" />
                        </div>
                    </div>
                </div>
                <div id="jsn-upgrade-message" class="alert alert-error"></div>
                
                <div class="form-actions">
                    <button class="btn btn-primary" id="jsn-update-next-login" disabled><?php echo JText::_('JSN_UNIFORM_UPDATE'); ?></button>
                    <button class="btn btn-cancel" id="jsn-update-cancel"><?php echo JText::_('JSN_UNIFORM_CANCEL'); ?></button>
                </div>
                
                <input type="hidden" name="identified_name" value="<?php echo $this->identified_name; ?>"> 
                <input type="hidden" name="extension_name" value="<?php echo $ext->name; ?>">  
                <input type="hidden" name="edition" value="<?php echo $ext->edition; ?>">
                <input type="hidden" name="authentication" value="<?php echo $ext->authentication ? $ext->authentication : 0; ?>">
                <input type="hidden" name="install" value="0">
                <input type="hidden" name="token" value="<?php echo JSession::getFormToken(); ?>">   
                <?php echo JHtml::_('form.token'); ?>       
            </form>
        </div>
    </div>
</div>

