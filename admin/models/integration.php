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

/**
 * JSNUniform model Integration
 *
 * @package     Models
 * @subpackage  Integration
 * @since       1.6
 */
class JSNUniformModelIntegration extends JSNBaseModel
{
	public function __construct($config = array())
	{
		$this->app = JFactory::getApplication();
		parent::__construct($config);
	}

	
	/**
	 * Get Installed Plugins
	 * 
	 * @return ObjectList
	 */
	public function getInstalledPlugins()
	{
		// Query database for payment gateway profiles
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('*');
		$query->from('#__extensions');
		$query->where(array('folder = ' . $db->quote('uniform') , 'type=' . $db->quote('plugin')));
		
		$db->setQuery($query);
		$items = $db->loadObjectList();

		$results = array();
		
		if (count($items))
		{
			$lang = JFactory::getLanguage();
			
			foreach ($items as $key => $value) 
			{		
				$lang->load('plg_' . $value->folder . '_' . $value->element);
				$is_enabled = $value->enabled;
				$data 		= json_decode($value->manifest_cache);
				$edition 	= 'PREMIUM';
				$version	= (string) $data->version;
				$is_setting = false;
				$identifiedName = '';
				
				if (file_exists(JPATH_PLUGINS . '/uniform/' . $value->element . '/defines.php'))
				{
					require_once JPATH_PLUGINS . '/uniform/' . $value->element . '/defines.php';
					
					$edition    = 'JSN_UNIFORM_' . strtoupper($value->element) . '_EDITION';
					$version    = 'JSN_UNIFORM_' . strtoupper($value->element) . '_VERSION';
					$setting    = 'JSN_UNIFORM_' . strtoupper($value->element) . '_CONFIGURATION';
					$identifiedName    = 'JSN_UNIFORM_' . strtoupper($value->element) . '_IDENTIFIED_NAME';
					if (defined($edition))
					{
						eval('$edition    = ' . $edition . ';');
						eval('$version    = ' . $version . ';');
						eval('$is_setting = ' . $setting . ';');
						eval('$identifiedName = ' . $identifiedName . ';');
					}
				}
				
				if (count($data))
				{
					$tmpClass						= new stdClass;
					$tmpClass->name      			= JText::_($value->name);
					$tmpClass->element     			= JText::_($value->element);
					$tmpClass->extension_id 		= (int) $value->extension_id;
					$tmpClass->edit_link    		= 'index.php?option=com_uniform&view=paymentgatewaysettings&tmpl=component&extension_id='.$value->extension_id;
					$tmpClass->current_version     	= (string) $version;
					$tmpClass->is_installed    		= true;
					$tmpClass->is_updated    		= false;
					$tmpClass->edition      		= $edition;
					$tmpClass->new_version        	= '';
					$tmpClass->type        			= '';
					$tmpClass->authentication		= '';
					$tmpClass->is_online	 		= false;
					$tmpClass->is_setting	 		= $is_setting;
					$tmpClass->is_enabled	 		= $is_enabled;
					$tmpClass->identified_name	 	= $identifiedName;
						
				}
				$results['ext_jsnuniform_'. $value->element] =  $tmpClass;
			}
			
			return $results;
		}

		return $results;
	}

	/**
	 * Get All online plugins in Server 
	 * 
	 * @return array
	 */
	public function getOnlinePlugins($offlineData)
	{
		$JVersion  = new JVersion;
		$joomlaVer = $JVersion->RELEASE;

		$onlinePlugins = array();
		try 
		{
			$response = JSNUtilsHttp::get(JSN_UNIFORM_INTEGRATION_CHECK_URL);
			$response = json_decode($response['body'], true);
			if (count($response))
			{
				foreach ($response['items'] as $key => $item)
				{
					if ($item['category_codename'] == JSN_UNIFORM_CATEGORY_IDENTIFIED_NAME)
					{
						$subItems = $item['items'];
						foreach ($subItems as $subKey => $subItem)
						{
							if ($subItem['category_codename'] == JSN_UNIFORM_CATEGORY_PLUGINS_IDENTIFIED_NAME)
							{
								$pluginCategories = $subItem['items'];
								
								foreach ($pluginCategories as $plgCKey => $plgCategory)
								{
									$pluginItems = $plgCategory['items'];
									foreach ($pluginItems as $pluginItem)
									{
										$tag = explode(';', $pluginItem['tags']);
										
										if (!in_array($joomlaVer, $tag)) continue;										
										
										$tmpItems = new stdClass; 
										$tmpItems->name      			= $pluginItem['name'];
										$tmpItems->element	 			= '';
										$tmpItems->extension_id 		= 0;
										$tmpItems->edit_link    		= '';
										$tmpItems->current_version     	= '';									
										$tmpItems->type			  		= $plgCategory['name'];
										$tmpItems->new_version  		= $pluginItem['version'];
										$tmpItems->edition 				= $pluginItem['edition'];
										$tmpItems->authentication 		= $pluginItem['authentication'];
										$tmpItems->is_updated    		= false;
										$tmpItems->is_installed 		= false;
										$tmpItems->is_online	 		= true;
										$tmpItems->is_setting	 		= false;
										$tmpItems->identified_name	 	= $pluginItem['identified_name'];
										if (isset($offlineData[$pluginItem['identified_name']]))
										{
											$offlineItem = $offlineData[$pluginItem['identified_name']];
											$tmpItems->is_installed = true;

											$tmpItems->extension_id 		= $offlineItem->extension_id;
											$tmpItems->edit_link    		= $offlineItem->edit_link;
											$tmpItems->current_version     	= $offlineItem->current_version;	
											$tmpItems->is_setting	 		= $offlineItem->is_setting;
											$tmpItems->is_enabled	 		= $offlineItem->is_enabled;
											$tmpItems->element	 			= $offlineItem->element;
											if (version_compare($offlineItem->current_version, $pluginItem['version'], '<'))
											{
												$tmpItems->is_updated = true;
											}
										}
										
										$onlinePlugins [$pluginItem['identified_name']] = $tmpItems;
										
									}
									
								}
								
								break;
							}
						}
						
						break;
					}
				}
			}
			
			return $onlinePlugins;
			
		}
		catch (Exception $e)
		{
			return $onlinePlugins;
		}	
	}
	/**
	 * Process data
	 * 
	 * @return array
	 */
	public function processData()
	{
		$offlineData 	= $this->getInstalledPlugins();
		$onlineData		= $this->getOnlinePlugins($offlineData);		
		$data 			= array_replace_recursive($offlineData, $onlineData);
		return $data;
	}

	/**
	 * Get processed data
	 *
	 * @return array
	 */
	public function getData()
	{
		$plugins = $this->processData();
		return $plugins;
	}	
	
	/**
	 * Get related form
	 *
	 * @return Object list
	 */
	public function getRelatedFormByPaymentType($paymentType)
	{
		if ($paymentType)
		{
			$this->_db->setQuery($this->_db->getQuery(true)->select('*')->from('#__jsn_uniform_forms')->where('form_payment_type="' . $paymentType . '"'));
			return $this->_db->loadObjectList();
		}
		
	}
	
	/**
	 * Get extention information
	 * 
	 * @param int $id the extension id
	 * 
	 * @return object
	 */
	public function getExtension($id)
	{
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('*');
		$query->from('#__extensions');
		$query->where(array('extension_id = ' . $db->quote((int) $id)));
		
		$db->setQuery($query);
		return $db->loadObject();		
	}
	
	/**
	 * Set pluign status
	 * 
	 * @param int $id
	 * @param int $status
	 * 
	 * @return bool true/false
	 */
	public function setStatus($id, $status)
	{
		if (!$id) return false;
		
		$db  	= JFactory::getDBO();
		$item 	= new stdClass;
		$item->extension_id = (int) $id;
		$item->enabled      = !$status;
		
		try
		{
			if ($db->updateObject('#__extensions', $item, 'extension_id'))
			{
				return true;
			}
		}
		catch (Exception $e)
		{
			return false;
		}		
	}
}