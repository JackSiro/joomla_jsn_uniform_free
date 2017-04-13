<?php

/**
 * @version     $Id: form.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Models
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2016 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');

/**
 * JSNUniform model Form
 *
 * @package     Modales
 * @subpackage  Form
 * @since       1.6
 */
class JSNUniformModelForm extends JModelAdmin
{

	protected $option = JSN_UNIFORM;

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   11.1
	 */
	public function getTable($type = 'JsnForm', $prefix = 'JSNUniformTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return        mixed        A JForm object on success, false on failure
	 *
	 * @since        1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_uniform.form', 'form', array('control' => 'jform', 'load_data' => $loadData));
		return $form;
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see JModelForm::loadFormData()
	 *
	 * @return object
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_uniform.edit.form.data', array());
		if (empty($data))
		{
			$data = $this->getItem();
		}
		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   11.1
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		$formId = isset($item->form_id) ? $item->form_id : "";
		$item->form_content = $this->getFormPages($formId);

		// Set default layout for form when it is not exists
		if (empty($item->form_layout) || !is_dir(JSN_UNIFORM_PAGEDESIGN_LAYOUTS_PATH . DS . $item->form_layout))
		{
			$item->form_layout = 'default';
		}
		if (empty($item->form_id))
		{
			$dataConfig = $this->getDataConfig();

			if (!empty($dataConfig))
			{
				$session = JFactory::getSession();
				foreach ($dataConfig as $data)
				{
					if (isset($data->name) && $data->name == 'form_action')
					{
						$decoData = json_decode($data->value);
						$item->form_post_action = isset($decoData->action) ? $decoData->action : '';
						$item->form_post_action_data = isset($decoData->action_data) ? $decoData->action_data : '';
					}
					if (isset($data->name) && $data->name == 'list_email')
					{
						$dataConfigListEmail = json_decode($data->value);
						$configEmailNotify0 = new stdClass;
						$configEmailNotify1 = new stdClass;
						$configEmailNotify0->template_message = isset($dataConfigListEmail->template_message) ? $dataConfigListEmail->template_message : "";
						$configEmailNotify0->template_subject = isset($dataConfigListEmail->template_subject) ? $dataConfigListEmail->template_subject : "";
						$configEmailNotify0->template_notify_to = '0';
						$configEmailNotify1->template_message = isset($dataConfigListEmail->template_message) ? $dataConfigListEmail->template_message : "";
						$configEmailNotify1->template_subject = isset($dataConfigListEmail->template_subject) ? $dataConfigListEmail->template_subject : "";
						$configEmailNotify1->template_notify_to = '1';
						$session->set('emailsettings_notify_1', $configEmailNotify1);
						$session->set('emailsettings_notify_0', $configEmailNotify0);
					}
				}
			}
		}
		return $item;
	}

	/**
	 * Override save method to save form fields to database
	 *
	 * @param   array  $data  Data form
	 *
	 * @return boolean
	 */
	public function save($data)
	{

		$user = JFactory::getUser();
		$input = JFactory::getApplication()->input;
		$postData = $input->getArray($_POST);
		if ($postData['jform'] != '')
		{
			$jform = $input->post->get('jform', array(), 'array');
			$jform = array('jform' => $jform);
			$postData = array_merge($postData, $jform);
		}
		if($postData['form_post_action_data4'] != '')
		{
			$post_action_data = $input->post->get('form_post_action_data4', array(), 'array');
			$post_action_data = array('form_post_action_data4' => $post_action_data[0]);
			$postData         = array_merge($postData, $post_action_data);
		}

		$emailModel = JModelForm::getInstance('EmailSettings', 'JSNUniformModel');
		if(isset($postData['jform']['email-settings']['administrator']))
		{
			$emailModel->saveForm($postData['jform']['email-settings']['administrator']);
		}
		if(isset($postData['jform']['email-settings']['submiter']))
		{
			$emailModel->saveForm($postData['jform']['email-settings']['submiter']);
		}
		$checkCreate = true;
		$data['form_submitter'] = isset($postData['form_submitter']) ? json_encode($postData['form_submitter']) : '';
		$data['form_post_action_data'] = isset($postData['form_post_action_data' . $data['form_post_action']]) ? $postData['form_post_action_data' . $data['form_post_action']] : '';
		if ($data['form_post_action_data'])
		{
			$data['form_post_action_data'] = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($data['form_post_action_data']) : $data['form_post_action_data'];
		}
		$data['form_notify_submitter'] = isset($data['form_notify_submitter']) ? "1" : "0";
		$globalStyle = array();
		
		if (!empty($postData['form_style']['themes_style']))
		{
			$themeStyle = array();
			$themes = array();
			foreach ($postData['form_style']['themes_style'] as $key => $value)
			{
				if ($key == "light" || $key == "dark")
				{
					$themeStyle[$key] = $value;
					$themes[] = $key;
				}
				else
				{
					$globalStyle['themes_style'][$key] = $value;
					$globalStyle['themes'][] = $key;
				}
			}
			$postData['form_style']['themes_style'] = $themeStyle;
			$postData['form_style']['themes'] = $themes;
		}
		$formSettings = new stdClass;
		$plgName = JSNUniformHelper::getPluginUniform();

		if (isset($plgName) && !empty($plgName)) {
			if (is_array($plgName)) {
				foreach ($plgName as $k => $v) {
					$v = (array) $v;
					$name = form_ . '' . $v['value'];
					$formSettings->$name = !empty($postData[$name]) ? $postData[$name] : '';
					JSNUniformControllerForm::do_ajax('saveBackEnd',$v['value'],$postData[$name]);
				}
			}
		}
		$formSettings->form_show_mailchimp_subcriber = !empty($postData['jsn_form_mailchimp_subcriber']['form_show_mailchimp_subcriber']) ? $postData['jsn_form_mailchimp_subcriber']['form_show_mailchimp_subcriber'] : "No";
		$formSettings->form_mailchimp_subcriber_text = !empty($postData['jsn_form_mailchimp_subcriber']['form_mailchimp_subcriber_text']) ? $postData['jsn_form_mailchimp_subcriber']['form_mailchimp_subcriber_text'] : JText::_('JSN_UNIFORM_CUSTOM_MESSAGE_FOR_MAILCHIMP_SUBCRIBER');
		$formSettings->form_payment_money_value_text = !empty($postData['jsn_form_total_money']['form_payment_money_value_text']) ? $postData['jsn_form_total_money']['form_payment_money_value_text'] : JText::_('JSN_UNIFORM_TOTAL_MONEY');
		$formSettings->form_payment_money_value = !empty($postData['jsn_form_total_money']['form_payment_money_value']) ? $postData['jsn_form_total_money']['form_payment_money_value'] : '';
		$formSettings->form_show_total_money_text = !empty($postData['jsn_form_total_money']['form_show_total_money_text']) ? $postData['jsn_form_total_money']['form_show_total_money_text'] : "Yes";
		$formSettings->form_btn_next_text = !empty($postData['jsn_form_button']['form_btn_next_text']) ? $postData['jsn_form_button']['form_btn_next_text'] : JText::_('NEXT');
		$formSettings->form_btn_prev_text = !empty($postData['jsn_form_button']['form_btn_prev_text']) ? $postData['jsn_form_button']['form_btn_prev_text'] : JText::_('PREV');
		$formSettings->form_btn_submit_text = !empty($postData['jsn_form_button']['form_btn_submit_text']) ? $postData['jsn_form_button']['form_btn_submit_text'] : JText::_('SUBMIT');
		$formSettings->form_btn_submit_custom_class = !empty($postData['jsn_form_button']['form_btn_submit_custom_class']) ? $postData['jsn_form_button']['form_btn_submit_custom_class'] : '';
        $formSettings->form_btn_preview_text = !empty($postData['jsn_form_button']['form_btn_preview_text']) ? $postData['jsn_form_button']['form_btn_preview_text'] : JText::_('RESET');
        $formSettings->form_state_btn_preview_text = !empty($postData['jsn_form_button']['form_state_btn_preview_text']) ? $postData['jsn_form_button']['form_state_btn_preview_text'] : "No";
        $formSettings->form_btn_reset_text = !empty($postData['jsn_form_button']['form_btn_reset_text']) ? $postData['jsn_form_button']['form_btn_reset_text'] : JText::_('RESET');
		$formSettings->form_state_btn_reset_text = !empty($postData['jsn_form_button']['form_state_btn_reset_text']) ? $postData['jsn_form_button']['form_state_btn_reset_text'] : "No";
		$formSettings->action_save_submissions = !empty($postData['jsn_uniform_settings']['action_save_submissions']) ? $postData['jsn_uniform_settings']['action_save_submissions'] : 0;
		$data['form_settings'] = json_encode($formSettings);
		$data['form_style'] = !empty($postData['form_style']) ? json_encode($postData['form_style']) : "";
		$data['form_style'] = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($data['form_style']) : $data['form_style'];
		$globalStyle = !empty($globalStyle) ? json_encode($globalStyle) : "";
		$globalStyle = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($globalStyle) : $globalStyle;
		$db = JFactory::getDBO();
		$query = "REPLACE INTO `#__jsn_uniform_config` (name, value) VALUES ('form_style'," . $db->quote($globalStyle) . ")";
		$db->setQuery($query);
		if (!$db->execute())
		{
			JError::raiseWarning(500, $db->getErrorMsg());
		}
		if (empty($data['form_id']) || $data['form_id'] == 0)
		{
			$data['form_created_by'] = $user->id;
			$data['form_created_at'] = date('Y-m-d H:i:s');
			$edition = defined('JSN_UNIFORM_EDITION') ? JSN_UNIFORM_EDITION : "pro";
			if (strtolower($edition) == "free")
			{
				$dataListForm = JSNUniformHelper::getForms();

				if (count($dataListForm) >= 3)
				{
					$checkCreate = false;
				}
			}
		}
		else
		{
			$data['form_modified_by'] = $user->id;
			$data['form_modified_at'] = date('Y-m-d H:i:s');
		}

		if ($checkCreate)
		{			
			if (($result = parent::save($data)))
			{
				$formId = $this->getState($this->getName() . '.id');
				$this->saveField($data['form_id'], $data['form_layout']);

				$this->saveListEmail($postData, $formId);
				$this->EmailTepmplates($formId, $data['form_id']);
			}
			return $result;
		}
		else
		{
			$msg = JText::sprintf('JSN_UNIFORM_YOU_HAVE_REACHED_THE_LIMITATION_OF_3_FORM_IN_FREE_EDITION', 0) . ' <a class="jsn-link-action" href="index.php?option=com_uniform&view=upgrade">' . JText::_("JSN_UNIFORM_UPGRADE_EDITION") . '</a>';
			$this->setError($msg);
			return false;
		}
	}

	/**
	 * Save all field in page form
	 *
	 * @param   type  $dataFormId      Form id
	 *
	 * @param   type  $dataFormLayout  Form layout
	 *
	 * @return void
	 */
	public function saveField($dataFormId, $dataFormLayout)
	{
		$input = JFactory::getApplication()->input;
		$postData = $input->getArray($_POST);
		if ($postData['jform'] != '')
		{
			$jform = $input->post->get('jform', array(), 'array');
			$jform = array('jform' => $jform);
			$postData = array_merge($jform, $postData);
		}
		$edition = defined('JSN_UNIFORM_EDITION') ? JSN_UNIFORM_EDITION : "pro";
		$session = JFactory::getSession();
		$identify = array();
		$fieldSB = array();
		$pageId = array();
		$count = 0;
		$checkTableSubmission = true;
		$formId = $this->getState($this->getName() . '.id');
		$formId = intval($formId);
		$fieldIds = array();
		//get data form page
		$this->_db->setQuery($this->_db->getQuery(true)->select('page_id')->from('#__jsn_uniform_form_pages')->where('form_id=' . $formId))->order("page_id ASC");
		$dataFormPages = $this->_db->loadObjectList();
		$listFormPages = array();
		if (!empty($dataFormPages))
		{
			foreach ($dataFormPages as $FormPage)
			{
				$listFormPages[] = $FormPage->page_id;
			}
		}
		if (!JSNUniformHelper::checkTableSql("jsn_uniform_submissions_{$formId}"))
		{
			$checkTableSubmission = false;
		}
		else
		{
			$this->_db->setQuery("SHOW COLUMNS FROM #__jsn_uniform_submissions_{$formId}");
			$columnSubmission = $this->_db->loadObjectList();
		}
		if (isset($postData['name_page']))
		{
			foreach ($postData['name_page'] as $value => $text)
			{

				$dataField = array();
				$parsedFields = array();
                $formPages = array();
				$formFields = array();
                $pageContainer = "";
				if ($dataFormId == 0)
				{
					$formFields = json_decode($session->get('form_page_' . $value, '', 'form-design-'));

					//$pcontainer = $_SESSION['__form-design-']['form_container_page_' . $value];
					$pcontainer = $session->get('form_container_page_' . $value, '', 'form-design-');
					if(is_null($pcontainer))
					{
						$pcontainer = $session->set('form_container_page_' . $value, '[[{"columnName":"left","columnClass":"span12"}]]', 'form-design-');
					}
					$pageContainer = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($pcontainer) : $pcontainer;
					$pageContainer = json_decode($pageContainer);
				}
				else
				{
					$formFields = json_decode($session->get('form_page_' . $value, '', 'form-design-' . $dataFormId));

					//$pcontainer = $_SESSION['__form-design-'. $dataFormId]['form_container_page_' . $value];
					$pcontainer = $session->get('form_container_page_' . $value, '', 'form-design-' . $dataFormId);
					if(is_null($pcontainer))
					{
						$pcontainer = $session->set('form_container_page_' . $value, '[[{"columnName":"left","columnClass":"span12"}]]', 'form-design-' . $dataFormId);
					}
					$pageContainer = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($pcontainer) : $pcontainer;
					$pageContainer = json_decode($pageContainer);
				}
				if (!empty($formFields) && is_array($formFields))
				{
					foreach ($formFields as $index => $field)
					{
						if (($index < 10 && strtolower($edition) == "free") || strtolower($edition) != "free")
						{
							$count++;
							$options = $field->options;
							if (in_array($field->identify, $identify))
							{
								$field->identify = $field->identify . $count;
							}
							$identify[] = $field->identify;
							$field->identify = preg_replace('/[^a-z0-9-._]/i', "", $field->identify);
							$table = JTable::getInstance('JsnField', 'JSNUniformTable');
							$table->bind(array('form_id' => $formId, 'field_id' => isset($field->id) ? $field->id : null, 'field_type' => $field->type, 'field_identifier' => $field->identify, 'field_title' => $options->label, 'field_instructions' => isset($options->instruction) ? $options->instruction : null, 'field_position' => $field->position, 'field_ordering' => $index, 'field_settings' => json_encode($field)));
							if (!$table->store())
							{
								$this->setError($table->getError());
								$result = false;
							}
							$fieldIds[] = $table->field_id;
							$fieldSB[] = "sb_" . $table->field_id;

							$parsedFields[] = array('id' => $table->field_id, 'type' => $table->field_type, 'position' => $table->field_position, 'identify' => $table->field_identifier, 'label' => $table->field_title, 'instruction' => $table->field_instructions, 'options' => $field->options);
						}
					}
				}

				if (in_array($value, $listFormPages))
				{
					$formPages['page_id'] = $value;
				}
				$formPages['page_title']     = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($text) : $text;
				$formPages['form_id']        = $formId;
				$formPages['page_content']   = isset($parsedFields) ? json_encode($parsedFields) : "";
				$formPages['page_container'] = json_encode($pageContainer);

				$table = JTable::getInstance('JsnPage', 'JSNUniformTable');
				$table->bind($formPages);
				if (!$table->store())
				{
					$this->setError($table->getError());
					$result = false;
				}
				$pageId[] = $table->page_id;
			}
		}
		if (!empty($fieldIds))
		{
			$this->_db->setQuery("DELETE FROM #__jsn_uniform_fields WHERE form_id={$formId} AND field_id NOT IN (" . implode(', ', $fieldIds) . ")");
			$this->_db->execute();
		}
		else
		{
			$this->_db->setQuery("DELETE FROM #__jsn_uniform_fields WHERE form_id={$formId}");
			$this->_db->execute();
		}
		if (!empty($pageId))
		{
			$this->_db->setQuery("DELETE FROM #__jsn_uniform_form_pages WHERE form_id={$formId} AND page_id NOT IN (" . implode(', ', $pageId) . ")");
			$this->_db->execute();
		}
	}

	/**
	 * Save list email submit
	 *
	 * @param   type  $post    Post submit data form
	 *
	 * @param   type  $formId  Form id
	 *
	 * @return void
	 */
	public function saveListEmail($post, $formId)
	{
		
		if (!empty($post['form_email_notification']) && is_array($post['form_email_notification']))
		{
			$sendEmail = isset($post['form_email_notification']) ? $post['form_email_notification'] : '';
			$postEmailName = isset($post['form_email_notification_name']) ? $post['form_email_notification_name'] : '';
			$postEmailUserId = isset($post['form_email_notification_user_id']) ? $post['form_email_notification_user_id'] : '';

			if ($post['task'] != 'save2copy')
			{
				$postEmailId = isset($post['semail_id']) ? $post['semail_id'] : '';
			}
                        $this->_db = JFactory::getDBO();
			$this->_db->setQuery("DELETE FROM #__jsn_uniform_emails WHERE form_id=" . (int) $formId);
			$this->_db->execute();
			
			jimport('joomla.mail.helper');
			foreach (array_unique($sendEmail) as $email)
			{
				$isEmailAddress = JMailHelper::isEmailAddress($email);

				if ($isEmailAddress)
				{
					$table = JTable::getInstance('JsnEmail', 'JSNUniformTable');
					$table->bind(array('form_id' => $formId, 'email_name' => isset($postEmailName[$email]) ? $postEmailName[$email] : null, 'email_address' => $email, 'user_id' => isset($postEmailUserId[$email]) ? $postEmailUserId[$email] : null));
					if (!$table->store())
					{
						$this->setError($table->getError());
					}
				}
			}
		}
		else
		{
			$this->_db->setQuery("DELETE FROM #__jsn_uniform_emails WHERE form_id={$formId}");
			$this->_db->execute();
		}
	}

	/**
	 * Get data email template by form id
	 *
	 * @param   type  $formId      Form id
	 *
	 * @param   type  $dataFormId  Data form id
	 *
	 * @return void
	 */
	public function EmailTepmplates($formId, $dataFormId = null)
	{
		if (empty($dataFormId))
		{
			//Create Emailsettings
			$session = JFactory::getSession();
			$emailSettingsEmailSubmitted = $session->get('emailsettings_notify_0');
			$emailSettingsListEmail = $session->get('emailsettings_notify_1');

			if ($emailSettingsListEmail)
			{
				$emailSettingsListEmail->form_id = $formId;
				$this->saveEmailTemplates($emailSettingsListEmail);
			}
			else
			{
				$emailSettingsListEmail->form_id = $formId;
				$emailSettingsListEmail->template_notify_to = 1;
				$this->saveEmailTemplates($emailSettingsListEmail);
			}
			if ($emailSettingsEmailSubmitted)
			{
				$emailSettingsEmailSubmitted->form_id = $formId;
				$this->saveEmailTemplates($emailSettingsEmailSubmitted);
			}
			else
			{
				$emailSettingsEmailSubmitted->form_id = $formId;
				$emailSettingsEmailSubmitted->template_notify_to = 0;
				$this->saveEmailTemplates($emailSettingsEmailSubmitted);
			}
			$session->clear('emailsettings_notify_0');
			$session->clear('emailsettings_notify_1');
		}
	}

	/**
	 * Save email template
	 *
	 * @param   type  $dataEmailTemplate  Data email template
	 *
	 * @return void
	 */
	public function saveEmailTemplates($dataEmailTemplate)
	{
		if (!empty($dataEmailTemplate))
		{
			if(!empty($dataEmailTemplate->form_id)){
				$this->_db->setQuery("DELETE FROM #__jsn_uniform_templates WHERE form_id=".(int)$dataEmailTemplate->form_id." AND template_notify_to=".(int)$dataEmailTemplate->template_notify_to);
				$this->_db->execute();
			}
			$table = JTable::getInstance('JsnTemplate', 'JSNUniformTable');
			$table->bind($dataEmailTemplate);
			if (!$table->store())
			{
				$this->setError($table->getError());
			}
		}
	}

	/**
	 * Get data default config
	 *
	 * @return Object list
	 */
	public function getDataConfig()
	{

		$this->_db->setQuery($this->_db->getQuery(true)->select('*')->from("#__jsn_uniform_config"));
		return $this->_db->loadObjectList();
	}

	/**
	 * Override delete method to also delete form fields that associated
	 *
	 * @param   array  &$pks  id form
	 *
	 * @return boolean
	 */
	public function delete(&$pks)
	{
		$pks = (array) $pks;
		foreach ($pks as $id)
		{
			$this->_db->setQuery("DELETE FROM #__jsn_uniform_config WHERE name='position_form_{$id}'");
			$this->_db->execute();

			$this->_db->setQuery("DELETE FROM #__jsn_uniform_fields WHERE form_id={$id}");
			$this->_db->execute();

			$this->_db->setQuery("DELETE FROM #__jsn_uniform_submission_data WHERE form_id={$id}");
			$this->_db->execute();

			$this->_db->setQuery("DELETE FROM #__jsn_uniform_form_pages WHERE form_id={$id}");
			$this->_db->execute();

			$this->_db->setQuery("DELETE FROM #__jsn_uniform_templates WHERE form_id={$id}");
			$this->_db->execute();

			$this->_db->setQuery("DELETE FROM #__jsn_uniform_emails WHERE form_id={$id}");
			$this->_db->execute();

			$this->_db->setQuery("DELETE FROM #__jsn_uniform_submissions WHERE form_id={$id}");
			$this->_db->execute();

			if (!parent::delete($id))
			{
				return false;
			}
		}
		return true;
	}

	/**
	 * Retrieve form email for use in page design
	 *
	 * @return ObjectList
	 */
	public function getFormEmail()
	{
		$formId = $this->getState($this->getName() . '.id');

		if (!empty($formId) && is_numeric($formId))
		{
			$this->_db->setQuery($this->_db->getQuery(true)->select('*')->from('#__jsn_uniform_emails')->where('form_id=' . intval($formId)));

			return $this->_db->loadObjectList();
		}
	}

	/**
	 * Get all page in form
	 *
	 * @param   type  $formId  Form id
	 *
	 * @return Object list
	 */
	public function getFormPages($formId)
	{
		if (!empty($formId) && is_numeric($formId))
		{
			$this->_db->setQuery($this->_db->getQuery(true)->select('*')->from('#__jsn_uniform_form_pages')->where('form_id=' . intval($formId))->order("page_id ASC"));
			return $this->_db->loadObjectList();
		}
	}

	/**
	 * Method to duplicate modules.
	 *
	 * @param   array  &$pks  An array of primary key IDs.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @since   1.6
	 * @throws  Exception
	 */
	public function duplicate(&$pks)
	{
		// Initialise variables.
		$user = JFactory::getUser();
		$db = $this->getDbo();

		// Access checks.
		if (!$user->authorise('core.create', 'com_uniform'))
		{
			throw new Exception(JText::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
		}

		$table = $this->getTable();
		$checkCreate = true;
		foreach ($pks as $pk)
		{
			$edition = defined('JSN_UNIFORM_EDITION') ? JSN_UNIFORM_EDITION : "pro";
			if (strtolower($edition) == "free")
			{
				$dataListForm = JSNUniformHelper::getForms();

				if (count($dataListForm) >= 3)
				{
					$checkCreate = false;
				}
			}
			if ($checkCreate)
			{
				if ($table->load($pk, true))
				{
					// Reset the id to create a new record.
					$table->form_id = 0;

					// Alter the title.
					$m = null;
					if (preg_match('#\((\d+)\)$#', $table->form_title, $m))
					{
						$table->form_title = preg_replace('#\(\d+\)$#', '(' . ($m[1] + 1) . ')', $table->form_title);
					}
					else
					{
						$table->form_title .= ' (2)';
					}
					// Unpublish duplicate module
					$table->form_state = 0;
					$table->form_submission_cout = 0;
					$table->form_last_submitted = '';
					if (!$table->check() || !$table->store())
					{
						throw new Exception($table->getError());
					}
					// Email
					$query = $db->getQuery(true);
					$query->select('*');
					$query->from('#__jsn_uniform_emails');
					$query->where('form_id=' . (int) $pk);

					$this->_db->setQuery((string) $query);
					$emails = $this->_db->loadObjectList();
					foreach ($emails as $email)
					{
						$email->email_id = 0;
						$email->form_id = $table->form_id;
						$tableEmail = JTable::getInstance('JsnEmail', 'JSNUniformTable');
						$tableEmail->bind($email);
						if (!$tableEmail->store())
						{
							$this->setError($tableEmail->getError());
						}
					}
					//Email template
					$query = $db->getQuery(true);
					$query->select('*');
					$query->from('#__jsn_uniform_templates');
					$query->where('form_id=' . (int) $pk);

					$this->_db->setQuery((string) $query);
					$templates = $this->_db->loadObjectList();
					foreach ($templates as $template)
					{
						$template->template_id = 0;
						$template->form_id = $table->form_id;
						$tableTemplate = JTable::getInstance('JSNTemplate', 'JSNUniformTable');
						$tableTemplate->bind($template);
						if (!$tableTemplate->store())
						{
							$this->setError($tableTemplate->getError());
						}
					}
					//Page and Field
					$query = $db->getQuery(true);
					$query->select('*');
					$query->from('#__jsn_uniform_form_pages');
					$query->where('form_id=' . (int) $pk);
					$query->order("page_id ASC");
					$this->_db->setQuery((string) $query);
					$pages = $this->_db->loadObjectList();
					foreach ($pages as $page)
					{
						$dataField = array();
						$fields = json_decode($page->page_content);
						$pageTemplate = json_decode($page->page_template);
						$formPages = array();
						$parsedFields = array();
						foreach ($fields as $index => $item)
						{
							$tableField = JTable::getInstance('JsnField', 'JSNUniformTable');
							$tableField->bind(array('form_id' => $table->form_id, 'field_type' => $item->type, 'field_identifier' => $item->identify, 'field_title' => $item->label, 'field_instructions' => isset($item->instruction) ? $item->instruction : null, 'field_position' => $item->position, 'field_ordering' => $index));
							if (!$tableField->store())
							{
								$this->setError($tableField->getError());
							}
							$fieldSettings = $item;
							$fieldSettings->id = $tableField->field_id;

							$tableUpdateField = JTable::getInstance('JsnField', 'JSNUniformTable');
							$tableUpdateField->bind(array('field_settings' => $fieldSettings, 'field_id' => $tableField->field_id));
							if (!$tableUpdateField->store())
							{
								$this->setError($tableUpdateField->getError());
							}
							$parsedFields[] = $fieldSettings;
						}
						$formPages['page_id'] = 0;
						$formPages['page_title'] = $page->page_title;
						$formPages['page_container'] = $page->page_container;
						$formPages['form_id'] = $table->form_id;
						$formPages['page_content'] = isset($parsedFields) ? json_encode($parsedFields) : "";

						$tablePage = JTable::getInstance('JsnPage', 'JSNUniformTable');
						$tablePage->bind($formPages);
						if (!$tablePage->store())
						{
							$this->setError($tablePage->getError());
						}
					}
				}
				else
				{
					throw new Exception($table->getError());
				}
			}
		}
		if (!$checkCreate)
		{
			$msg = JText::sprintf('JSN_UNIFORM_YOU_HAVE_REACHED_THE_LIMITATION_OF_3_FORM_IN_FREE_EDITION', 0) . ' <a class="jsn-link-action" href="index.php?option=com_uniform&view=upgrade">' . JText::_("JSN_UNIFORM_UPGRADE_EDITION") . '</a>';
			throw new Exception($msg);
		}
		return true;
	}

	public function getFormPagePreview()
	{
		JSession::checkToken('get') or die( 'Invalid Token' );
		
		$input = JFactory::getApplication()->input;
		$postData = $input->getArray($_POST);
		
		$dataFormLayout = $postData['jform']['form_layout'] ? $postData['jform']['form_layout'] : 'default';
		$dataFormId 	= $postData['jform']['form_id'] ? $postData['jform']['form_id'] : 0;
		
		if ($postData['jform'] != '')
		{
			$jform = $input->post->get('jform', array(), 'array');
			$jform = array('jform' => $jform);
			$postData = array_merge($jform, $postData);
		}
		$edition = defined('JSN_UNIFORM_EDITION') ? JSN_UNIFORM_EDITION : "pro";
		$session = JFactory::getSession();
		$identify = array();
		$fieldSB = array();
		$pageId = array();
		$count = 0;
		$results = array();
		
		if (isset($postData['name_page']))
		{
			foreach ($postData['name_page'] as $value => $text)
			{		
				$dataField = array();
				$parsedFields = array();
				$formPages = array();
				$formFields = array();
				$pageContainer = "";
				if ($dataFormId == 0)
				{
					$formFields = json_decode($session->get('form_page_' . $value, '', 'form-design-'));
					$pcontainer = $session->get('form_container_page_' . $value, '', 'form-design-');
					if(is_null($pcontainer))
					{
						$pcontainer = $session->set('form_container_page_' . $value, '[[{"columnName":"left","columnClass":"span12"}]]', 'form-design-');
					}
					$pageContainer = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($pcontainer) : $pcontainer;
					$pageContainer = json_decode($pageContainer);
				}
				else
				{
					$formFields = json_decode($session->get('form_page_' . $value, '', 'form-design-' . $dataFormId));
					$pcontainer = $session->get('form_container_page_' . $value, '', 'form-design-' . $dataFormId);
					if(is_null($pcontainer))
					{
						$pcontainer = $session->set('form_container_page_' . $value, '[[{"columnName":"left","columnClass":"span12"}]]', 'form-design-' . $dataFormId);
					}
					$pageContainer = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($pcontainer) : $pcontainer;
					$pageContainer = json_decode($pageContainer);
				}
				
				if (!empty($formFields) && is_array($formFields))
				{
					foreach ($formFields as $index => $field)
					{
						$count++;
						$options = $field->options;
						if (in_array($field->identify, $identify))
						{
							$field->identify = $field->identify . $count;
						}
						$identify[] = $field->identify;
						$field->identify = preg_replace('/[^a-z0-9-._]/i', "", $field->identify);
			
						$parsedFields[] = array(
								'id' => $count, 
								'type' => $field->type, 
								'position' => $field->position, 
								'identify' => $field->identify, 
								'label' => $options->label, 
								'instruction' => isset($options->instruction) ? $options->instruction : null,
								'options' => $field->options);
					}
				}
				$formPages['page_id'] = $value;				
				$formPages['page_title']     = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($text) : $text;
				$formPages['form_id']        = $dataFormId;
				$formPages['page_content']   = isset($parsedFields) ? base64_encode(json_encode($parsedFields)) : "";
				$formPages['page_container'] = json_encode($pageContainer);
				$results[] = $formPages;
			}
		}
		
		return $results;
	}
	
	public function getItemPreview()
	{
		$input 		= JFactory::getApplication()->input;
		$postData 	= $input->getArray($_POST);
		
		$items		= $postData['jform'];
			
		$formSettings = new stdClass;
		$plgName = JSNUniformHelper::getPluginUniform();
			
		if (isset($plgName) && !empty($plgName)) {
			if (is_array($plgName)) {
				foreach ($plgName as $k => $v) {
					$v = (array) $v;
					$name = form_ . '' . $v['value'];
					$formSettings->$name = !empty($postData[$name]) ? $postData[$name] : '';
				}
			}
		}
		$formSettings->form_show_mailchimp_subcriber = !empty($postData['jsn_form_mailchimp_subcriber']['form_show_mailchimp_subcriber']) ? $postData['jsn_form_mailchimp_subcriber']['form_show_mailchimp_subcriber'] : "No";
		$formSettings->form_mailchimp_subcriber_text = !empty($postData['jsn_form_mailchimp_subcriber']['form_mailchimp_subcriber_text']) ? $postData['jsn_form_mailchimp_subcriber']['form_mailchimp_subcriber_text'] : JText::_('JSN_UNIFORM_CUSTOM_MESSAGE_FOR_MAILCHIMP_SUBCRIBER');
		$formSettings->form_payment_money_value_text = !empty($postData['jsn_form_total_money']['form_payment_money_value_text']) ? $postData['jsn_form_total_money']['form_payment_money_value_text'] : JText::_('JSN_UNIFORM_TOTAL_MONEY');
		$formSettings->form_payment_money_value = !empty($postData['jsn_form_total_money']['form_payment_money_value']) ? $postData['jsn_form_total_money']['form_payment_money_value'] : '';
		$formSettings->form_show_total_money_text = !empty($postData['jsn_form_total_money']['form_show_total_money_text']) ? $postData['jsn_form_total_money']['form_show_total_money_text'] : "Yes";
		$formSettings->form_btn_next_text = !empty($postData['jsn_form_button']['form_btn_next_text']) ? $postData['jsn_form_button']['form_btn_next_text'] : JText::_('NEXT');
		$formSettings->form_btn_prev_text = !empty($postData['jsn_form_button']['form_btn_prev_text']) ? $postData['jsn_form_button']['form_btn_prev_text'] : JText::_('PREV');
		$formSettings->form_btn_submit_text = !empty($postData['jsn_form_button']['form_btn_submit_text']) ? $postData['jsn_form_button']['form_btn_submit_text'] : JText::_('SUBMIT');
		$formSettings->form_btn_submit_custom_class = !empty($postData['jsn_form_button']['form_btn_submit_custom_class']) ? $postData['jsn_form_button']['form_btn_submit_custom_class'] : '';
        $formSettings->form_btn_preview_text = !empty($postData['jsn_form_button']['form_btn_preview_text']) ? $postData['jsn_form_button']['form_btn_preview_text'] : JText::_('RESET');
        $formSettings->form_state_btn_preview_text = !empty($postData['jsn_form_button']['form_state_btn_preview_text']) ? $postData['jsn_form_button']['form_state_btn_preview_text'] : "No";
        $formSettings->form_btn_reset_text = !empty($postData['jsn_form_button']['form_btn_reset_text']) ? $postData['jsn_form_button']['form_btn_reset_text'] : JText::_('RESET');
		$formSettings->form_state_btn_reset_text = !empty($postData['jsn_form_button']['form_state_btn_reset_text']) ? $postData['jsn_form_button']['form_state_btn_reset_text'] : "No";
		$formSettings->action_save_submissions = !empty($postData['jsn_uniform_settings']['action_save_submissions']) ? $postData['jsn_uniform_settings']['action_save_submissions'] : 0;
		
		$items['form_content'] = base64_encode($items['form_content']);
		$items['form_settings'] = json_encode($formSettings);
		
		return $items;
	}
}
