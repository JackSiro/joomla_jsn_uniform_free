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
// No direct access
defined('_JEXEC') or die('Restricted access');

class JSNUniformViewPaymentGatewaySettings extends JSNBaseView
{
	function display($tmp = null)
	{
		// Load assets
		JSNUniformHelper::addAssets();
		
		$lang 			= JFactory::getLanguage();
		
		$input 			= JFactory::getApplication()->input;
		$extensionId 	= $input->getInt('extension_id', 0);
		$model 			= $this->getModel();
		$extension 		= $model->getExtensionInfo($extensionId);
		
		if (!count($extension))
		{
			$html = '<br/> <div class="alert alert-danger">' . JText::_('JSN_UNIFORM_EXTENSION_NOT_FOUND'). '</div>';
			echo $html;
			return;
		}
		
		$this->extension_name = (string) $extension->element;
		
		if (JPluginHelper::isEnabled('uniform', (string) $this->extension_name) !== true)
		{
			$html = '<br/> <div class="alert alert-danger">' . JText::sprintf('JSN_UNIFORM_PLUGIN_IS_NOT_EXISTED_OR_ENABLED', strtoupper(str_replace('_', ' ', (string) $this->extension_name))). '</div>';
			
			echo $html;
			return;
		}
		parent::display($tmp);

		// Load assets
		JSNUniformHelper::addAssets();
		$this->addAssets();
	}

	/**
	 * Add the libraries css and javascript
	 *
	 * @return void
	 *
	 * @since        1.6
	 */
	protected function addAssets()
	{
		
	}
}