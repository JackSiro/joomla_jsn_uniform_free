<?php
/**
 * @version     $Id:
 * @package     JSNUniform
 * @subpackage  Integration
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2016 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Integration view of JSN Framework Sample component
 *
 * @package     Joomla.Administrator
 * @subpackage  com_uniform
 * @since       1.5
 */
class JSNUniformViewIntegration extends JSNBaseView
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 */
	function display($tpl = null)
	{
		// Get config parameters
		$config = JSNConfigHelper::get();
		// Get messages
		$msg = '';
		$editionOfUniform	= defined('JSN_UNIFORM_EDITION') ? JSN_UNIFORM_EDITION : "PRO";

		if (!$config->get('disable_all_messages'))
		{
			$msgs = JSNUtilsMessage::getList('CONFIGURATION');
			$msgs = count($msgs) ? JSNUtilsMessage::showMessages($msgs) : '';
		}
		
		$input           	= JFactory::getApplication()->input;
		$model				= $this->getModel();
		$layout				= $this->getLayout();
		
		
		$identifiedName 	= $input->getString('identified_name', '');
		$edition 			= $input->getString('edition', '');
		$extId	 			= $input->getInt('extension_id', 0);
		
		if ($layout == 'install' || $layout == 'update')
		{		
			$plugins   	= $model->getData();
			$extension  = $plugins[$identifiedName];
			
			// Assign variables for rendering
			$this->extension = $extension;
			$this->identified_name = $identifiedName;
		}
		elseif ($layout == 'uninstall')
		{
			$this->extension 	= $model->getExtension($extId);
			$extension  		= $this->extension;
			$element 			= $extension->element;
			// Assign variables for rendering
			$this->relatedForms 	= $model->getRelatedFormByPaymentType($element);
			$this->identified_name 	= $identifiedName;			
		}
		else
		{
			// Assign variables for rendering
			$plugins		= $model->getData();
			$this->plugins 	= $plugins;
			// Initialize toolbar
			JSNUniformHelper::initToolbar('JSN_UNIFORM_INTEGRATION_PAGE_TITLE', 'uniform-integration', false);
			JSNUniformHelper::addSubmenu($input->get('view', 'integration'));

		}
		
		$this->editionOfUniform = $editionOfUniform;
		// Load assets
		JSNUniformHelper::addAssets();
		$this->_addAssets();	
		$this->msgs = $msgs;
		// Display the template
		parent::display($tpl);		
	}

	/**
	 * Add the libraries css and javascript
	 *
	 * @return void
	 */
	private function _addAssets()
	{
		$token = JSession::getFormToken();
		
		$arrayTranslated = array(
				'JSN_UNIFORM_PAYMENT_GATEWAY_SETTING_TITLE', 
				'JSN_UNIFORM_INTEGRATION_INSTALL_TITLE', 
				'JSN_UNIFORM_INTEGRATION_UPDATE_TITLE', 
				'JSN_UNIFORM_CANCEL', 
				'JSN_UNIFORM_SAVE',
				'JSN_UNIFORM_PLUGIN_UNINSTALL_TITLE',
				'JSN_UNIFORM_PLUGIN_UNINSTALL_CONFIRM'
		);
		echo JSNHtmlAsset::loadScript('uniform/integration', array('token' => $token, 'language' => JSNUtilsLanguage::getTranslated($arrayTranslated)), true);
	}
}
