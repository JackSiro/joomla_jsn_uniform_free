<?php
/**
 * @version     $Id$
 * @package     JSNUniform
 * @subpackage  Fields
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2016 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import library
require_once JPATH_ROOT . '/libraries/joomla/form/fields/plugins.php';

/**
 * Create Uniform Plugin combo-box
 *
 * @package     JSNUniform
 * @subpackage  AdminComponent
 * @since       1.0.0
 */
class JFormFieldJSNUFPlugins extends JFormFieldPlugins
{
	/**
	 * The form field type.
	 *
	 * @var	string
	 */
	protected $type = 'JsnPlugins';

	/**
	 * Get defined payment gateway profiles.
	 *
	 * @return  array
	 */
	protected function getOptions()
	{
		// Initialize variables
		$prefix = isset($this->element['prefix']) ? (string) $this->element['prefix'] : '';
		$filter = isset($this->element['filter']) ? (string) $this->element['filter'] : '';

		
		// Call parent method to get list of plugin
		$options = parent::getOptions();
		
		$edition = defined('JSN_UNIFORM_EDITION') ? JSN_UNIFORM_EDITION : "pro";
		
		if (strtolower($edition) == "free")
		{
			return array($options[0]);
		}
		
		// Loop thru results to filter
		foreach ($options AS $k => $v)
		{
			
			// Only filter if option is a plugin
			if (is_file(JPATH_ROOT . '/plugins/' . (string) $this->element['folder'] . "/{$v->value}/{$v->value}.php"))
			{
				if ($prefix AND strpos($v->value, $prefix) !== 0)
				{
					unset($options[$k]);
				}

				if ($filter AND ! preg_match($filter, $v->value))
				{
					unset($options[$k]);
				}
			} 
			elseif ($k != '0') 
			{
				unset($options[$k]);
			}
		}
		
		return $options;
	}
	
	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$pro = isset($this->element['pro']) ? (string) $this->element['pro'] : '';
		
		$html = array();
		$attr = '';
	
		// Initialize some field attributes.
		$attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->multiple ? ' multiple' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= $this->autofocus ? ' autofocus' : '';
		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true' || (string) $this->disabled == '1'|| (string) $this->disabled == 'true')
		{
			$attr .= ' disabled="disabled"';
		}
	
		$edition = defined('JSN_UNIFORM_EDITION') ? JSN_UNIFORM_EDITION : "pro";
		/*if (strtolower($edition) == "free" && $pro == 'true')
		{
			$attr .= ' disabled="disabled"';
		}*/
		
		// Initialize JavaScript field attributes.
		$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';
	
		// Get the field options.
		$options = (array) $this->getOptions();
	
		// Create a read-only list (no name) with hidden input(s) to store the value(s).
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true')
		{
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
	
			// E.g. form field type tag sends $this->value as array
			if ($this->multiple && is_array($this->value))
			{
				if (!count($this->value))
				{
					$this->value[] = '';
				}
	
				foreach ($this->value as $value)
				{
					$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"/>';
				}
			}
			else
			{
				$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>';
			}
		}
		else
			// Create a regular list.
		{
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		}
	
		return implode($html);
	}	
}
