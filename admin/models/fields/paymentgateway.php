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

/**
 * Create payment gateway manager form.
 */
class JFormFieldPaymentGateway extends JFormField
{
	/**
	 * The form field type
	 * @var string
	 */
	protected $type = 'PaymentGateway';

	/**
	 * Always return null to disable label markup generation.
	 * @return  string
	 */
	protected function getLabel()
	{
		return '';
	}

	/**
	 * Generate markup for management form.
	 * @return  string
	 */
	protected function getInput()
	{
		$edition = defined('JSN_UNIFORM_EDITION') ? JSN_UNIFORM_EDITION : "pro";
		
		if (strtolower($edition) == "free")
		{
			return '<div class="alert alert-danger" style="background-color: #f2dede; border-color: #eed3d7; color: #b94a48; text-align: center">' . JText::_('JSN_UNIFORM_THIS_FUNCTION_IS_ONLY_ON_PRO_EDITION'). '</div>';
		}

		// Generate field container id
		$id = str_replace('_', '-', $this->id) . '-field';

		// Preset output
		$html[] = '
	<div class="jsn-page-list">';

		// Start payment gateway profile listing
		$html[] = '
	<div class="sortable-container">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="center" width="20" nowrap="nowrap">#</th>
					<th class="center" width="20" nowrap="nowrap">' . JText::_('JSN_UNIFORM_FIELD_ID') . '</th>
					<th class="title" nowrap="nowrap">' . JText::_('JSN_UNIFORM_NAME') . '</th>
					<th class="description" nowrap="nowrap">' . JText::_('JSN_UNIFORM_DESCRIPTION') . '</th>
					<th class="center" width="10" nowrap="nowrap">' . JText::_('JSN_UNIFORM_VERSION') . '</th>
					<th class="center" width="10" nowrap="nowrap">' . JText::_('JSN_UNIFORM_ENABLE') . '</th>
					<th class="center" width="10" nowrap="nowrap">' . JText::_('JSN_UNIFORM_ACTION') . '</th>
				</tr>
			</thead>
			<tbody>';

		// Preset counting
		$row = 0;

		foreach ($this->getItems() AS $item)
		{
			// Counting
			$row++;

			// Generate table row markup code
			$html[] = '
				<tr>
					<td class="center">' . $row . '<input type="hidden" id="' . $this->id . '_' . $item->extension_id . '" name="payments[' . $item->extension_id . ']" value="' . ($item->enabled ? 1 : 0) . '" /></td>
					<td class="center">' . $item->extension_id . '</td>
					<td>' . JText::_($item->name) . '</td>
					<td>' . JText::_($item->description) . '</td>
					<td class="center">' . JText::_($item->version) . '</td>
					<td class="center"><input type="checkbox"' . ($item->enabled ? ' checked="checked"' : '') . ' onclick="var field = document.getElementById(\'' . $this->id . '_' . $item->extension_id . '\'); if (field) field.value = 1 - parseInt(field.value);" /></td>
					<td class="jsn-column-mini center">
						<div class="jsn-iconbar">
								<a class="payment_item_edit" href="index.php?option=com_uniform&view=paymentgatewaysettings&tmpl=component&extension_id='.$item->extension_id.'"><i class="jsn-icon16 jsn-icon-pencil"></i> </a>
							</div>
					</td>
				</tr>';
		}

		if ($row == 0)
		{
			$html[] = '<tr><td class="center" colspan="7">' . JText::_('JSN_UNIFORM_PAYMENT_NOT_FOUND_ANY_PROFILE') . '</td></tr>';
		}

		// Finalize output
		$html[] = '
				</tbody>
			</table>
		</div>
	</div>';

		return implode($html);
	}

	/**
	 * Get defined payment gateway profiles.
	 *
	 * @return  array
	 */
	protected function getItems()
	{
		// Query database for payment gateway profiles
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__extensions');
		$query->where(array('LOWER(element) LIKE \'payment%\'', 'folder=\'uniform\'', 'type=\'plugin\''));
		$db->setQuery($query);
		$items = $db->loadObjectList();
		if(count($items))
		{
			$this->translate($items);
			return $items;
		}

		return array();
	}
	
	protected function translate(&$items)
	{
		$lang = JFactory::getLanguage();
		foreach($items as &$item)
		{
			//JPlugin::loadLanguage('plg_' . $item->folder . '_' . $item->element);
			$lang->load('plg_' . $item->folder . '_' . $item->element);
			if (strlen($item->manifest_cache))
			{
				$data = json_decode($item->manifest_cache);
				if ($data)
				{
					foreach($data as $key => $value)
					{
						if ($key == 'type')
						{
							continue;
						}
						$item->$key = $value;
					}
				}
			}
			$item->author_info = @$item->authorEmail .'<br />'. @$item->authorUrl;
			$item->client = $item->client_id ? JText::_('JADMINISTRATOR') : JText::_('JSITE');
			$item->name = JText::_($item->name);
			$item->description = JText::_(@$item->description);
		}
	}
}