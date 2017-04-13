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

jimport('joomla.application.component.modeladmin');

/**
 * JSNUniform model Payment Gateway Settings
 *
 * @package     Models
 * @subpackage  Submission
 * @since       1.6
 */
class JSNUniformModelPaymentGatewaySettings extends JSNBaseModel
{
	public function __construct($config = array()){
		$this->app = JFactory::getApplication();
		parent::__construct($config);
	}


	public function getExtensionInfo($id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__extensions');
		$query->where($db->quoteName('type') . ' = ' . $db->quote('plugin') . ' AND ' . $db->quoteName('folder') . ' = ' . $db->quote('uniform'). ' AND ' . $db->quoteName('extension_id') . ' = ' . $db->quote($id));
		$db->setQuery($query);
		return $db->loadObject();
	}
}