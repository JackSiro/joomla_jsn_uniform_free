<?php
/**
 * @version    $Id$
 * @package    JSN_PageBuilder
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2016 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

// Import Controller Form Based

class JSNUniformControllerPaymentGatewaySettings extends JControllerForm
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function save()
	{
		JSession::checkToken() or die( 'Invalid Token' );
		// Get input object
		$input 			= JFactory::getApplication()->input;
		
		$data 			= $input->getVar('jform', array(), 'post', 'array');

		$extensionName	= $data['extension_name'];
		$dispatcher 	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('uniform', $extensionName);
		$result 		= $dispatcher->trigger('savePaymentGatewayConfig', array($data));
		
		if ($result) 
		{
			echo json_encode(array('result'=>'success', 'message' => ''));
		} 
		else 
		{
			echo json_encode(array('result' => 'failure', 'message' => JText::_('JSN_UNIFORM_SAVE_UNSUCCESSFULLY')));
		}
		return $result;
	}

	public function cancel($key = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$link = 'index.php?option=com_uniform&view=paymentgatewaysettings';
		$this->setRedirect($link);
	}

}