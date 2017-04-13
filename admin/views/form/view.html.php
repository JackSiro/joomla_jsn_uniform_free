<?php

/**
 * @version     $Id: view.html.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Form
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2016 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
require_once('components/com_uniform/views/emailsettings/view.html.php');
require_once('components/com_uniform/models/emailsettings.php');
/**
 * View class for a list of Form.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_uniform
 * @since       1.5
 */
class JSNUniformViewForm extends JSNBaseView
{
	protected $_document;
	protected $_formLayout;
	protected $items;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @see     fetch()
	 * @since   11.1
	 */
	public function display($tpl = null)
	{
		// Initialize variables
		$session = JFactory::getSession();
		$seesionQueue = $session->get('application.queue');
		$input = JFactory::getApplication()->input;

		$this->urlAction = $input->getString('tmpl', '');

		$this->_document = JFactory::getDocument();
		$this->_item = $this->get('Item');
		$emailModel = JModelForm::getInstance('EmailSettings', 'JSNUniformModel');
		$this->checkSubmitModal = false;

		$layout				= $this->getLayout();

		if ($seesionQueue[0]['type'] != "warning")
		{
			$session->clear('form_page_', 'form-design-' . $this->_item->form_id);
			$session->clear('form_list_page', 'form-design-' . $this->_item->form_id);

			$session->clear('form_page_', 'form-design-');
			$session->clear('form_list_page', 'form-design-');

			if ($seesionQueue[0])
			{
				if ($this->urlAction == "component")
				{
					$this->checkSubmitModal = true;
				}
			}
		}
		$this->_listFontType = array(" Verdana, Geneva, sans-serif", "\"Times New Roman\", Times, serif", "\"Courier New\", Courier, monospace", "Tahoma, Geneva, sans-serif", "Arial, Helvetica, sans-serif", "\"Trebuchet MS\", Arial, Helvetica, sans-serif", "\"Arial Black\", Gadget, sans-serif", "\"Lucida Sans Unicode\", \"Lucida Grande\", sans-serif", "\"Palatino Linotype\", \"Book Antiqua\", Palatino, serif", "\"Comic Sans MS\", cursive");
		$formContent = $this->_item->form_content;
		$this->_listPage = JSNUniformHelper::getListPage($formContent, $this->_item->form_id);
		$this->_form = $this->get('Form');
		$this->_fromEmail = array();
		if (empty($this->_item->form_id))
		{
			$this->_fromConfig = $this->get('DataConfig');
			$this->formAction = 0;
			$this->formActionData = '';
			foreach ($this->_fromConfig as $formConfig)
			{
				if (isset($formConfig->name) && $formConfig->name == 'email_notification')
				{
					$this->_fromEmail = json_decode($formConfig->value);
				}
				if (isset($formConfig->name) && $formConfig->name == 'form_action')
				{
					$this->formAction = $formConfig->value;
				}
			}
			foreach ($this->_fromConfig as $formConfig)
			{
				if ($this->formAction == 1 && $formConfig->name == 'form_action_url')
				{
					$this->formActionData = $formConfig->value;
				}
				if ($this->formAction == 2 && $formConfig->name == 'form_action_menu')
				{
					$this->formActionData = json_decode($formConfig->value);
				}
				if ($this->formAction == 3 && $formConfig->name == 'form_action_article')
				{
					$this->formActionData = json_decode($formConfig->value);
				}
				if ($this->formAction == 4 && $formConfig->name == 'form_action_message')
				{
					$this->formActionData = $formConfig->value;
				}
			}
		}
		else
		{
			$this->_fromEmail = $this->get('FormEmail');
		}
		$this->form_page = isset($formContent[0]->page_content) ? $formContent[0]->page_content : "";
		$this->actionForm = array('redirect_to_url' => "", 'menu_item' => "", 'menu_item_title' => "", 'article' => "", 'article_title' => "", 'message' => "", 'action' => "1");
		$this->actionForm = JSNUniformHelper::actionFrom($this->_item->form_post_action, $this->_item->form_post_action_data);
		$this->globalFormStyle = JSNUniformHelper::getDataConfig("form_style");
		$this->formStyle = new stdClass;
		if (!empty($this->_item->form_style))
		{
			$this->formStyle = json_decode($this->_item->form_style);
		}
		else
		{
			$this->formStyle->themes_style->light = '{"background_color":"","background_active_color":"#FCF8E3","border_thickness":"0","border_color":"","border_active_color":"#FBEED5","rounded_corner_radius":"0","padding_space":"10","margin_space":"0","text_color":"#333333","font_type":" Verdana, Geneva, sans-serif","font_size":"14","field_background_color":"#ffffff","field_border_color":"","field_shadow_color":"","field_text_color":"#666666","message_error_background_color":"#B94A48","message_error_text_color":"#FFFFFF"}';
			$this->formStyle->themes_style->dark = '{"background_color":"","background_active_color":"#444444","border_thickness":"0","border_color":"","border_active_color":"#666666","rounded_corner_radius":"0","padding_space":"10","margin_space":"0","text_color":"#C6C6C6","font_type":" Verdana, Geneva, sans-serif","font_size":"14","field_background_color":"#000000","field_border_color":"#111111","field_shadow_color":"#000000","field_text_color":"#333333","message_error_background_color":"#B94A48","message_error_text_color":"#FFFFFF"}';
			$this->formStyle->themes = array('light', 'dark');
		}

		if (!empty($this->globalFormStyle))
		{
			$globalFormStyle = json_decode($this->globalFormStyle->value);

			if (!empty($globalFormStyle->themes_style))
			{
				foreach ($globalFormStyle->themes_style as $key => $value)
				{
					$this->formStyle->themes_style->{$key} = $value;
				}
			}
			if (!empty($globalFormStyle->themes))
			{
				foreach ($globalFormStyle->themes as $key => $value)
				{
					$this->formStyle->themes[] = $value;
				}
			}
		}

		if (!empty($this->formStyle->theme) && !empty($this->formStyle->themes_style) && $this->formStyle->theme != "jsn-style-light" && $this->formStyle->theme != "jsn-style-dark")
		{
			$theme = str_replace("jsn-style-", "", $this->formStyle->theme);
			if (!empty($this->formStyle->themes_style->{$theme}))
			{
				$styles = json_decode($this->formStyle->themes_style->{$theme});
			}
			$this->formStyle->background_color = !empty($styles->background_color) ? $styles->background_color : "";
			$this->formStyle->background_active_color = !empty($styles->background_active_color) ? $styles->background_active_color : "";
			$this->formStyle->border_active_color = !empty($styles->border_active_color) ? $styles->border_active_color : "";
			$this->formStyle->border_thickness = !empty($styles->border_thickness) ? $styles->border_thickness : "";
			$this->formStyle->border_color = !empty($styles->border_color) ? $styles->border_color : "";
			$this->formStyle->rounded_corner_radius = !empty($styles->rounded_corner_radius) ? $styles->rounded_corner_radius : "";
			$this->formStyle->padding_space = !empty($styles->padding_space) ? $styles->padding_space : "";
			$this->formStyle->margin_space = !empty($styles->margin_space) ? $styles->margin_space : "";
			$this->formStyle->text_color = !empty($styles->text_color) ? $styles->text_color : "";
			$this->formStyle->font_type = !empty($styles->font_type) ? $styles->font_type : "";
			$this->formStyle->font_size = !empty($styles->font_size) ? $styles->font_size : "";
		}
		else
		{
			$this->formStyle->background_color = !empty($this->formStyle->background_color) ? $this->formStyle->background_color : "";
			$this->formStyle->background_active_color = !empty($this->formStyle->background_active_color) ? $this->formStyle->background_active_color : "";
			$this->formStyle->border_active_color = !empty($this->formStyle->border_active_color) ? $this->formStyle->border_active_color : "";
			$this->formStyle->border_thickness = !empty($this->formStyle->border_thickness) ? $this->formStyle->border_thickness : "";
			$this->formStyle->border_color = !empty($this->formStyle->border_color) ? $this->formStyle->border_color : "";
			$this->formStyle->rounded_corner_radius = !empty($this->formStyle->rounded_corner_radius) ? $this->formStyle->rounded_corner_radius : "";
			$this->formStyle->padding_space = !empty($this->formStyle->padding_space) ? $this->formStyle->padding_space : "";
			$this->formStyle->margin_space = !empty($this->formStyle->margin_space) ? $this->formStyle->margin_space : "";
			$this->formStyle->text_color = !empty($this->formStyle->text_color) ? $this->formStyle->text_color : "";
			$this->formStyle->font_type = !empty($this->formStyle->font_type) ? $this->formStyle->font_type : "";
			$this->formStyle->font_size = !empty($this->formStyle->font_size) ? $this->formStyle->font_size : "";

			if (empty($this->formStyle->background_active_color) && empty($this->formStyle->border_active_color) && empty($this->formStyle->border_thickness) && empty($this->formStyle->border_color) && empty($this->formStyle->rounded_corner_radius) && empty($this->formStyle->background_color) && empty($this->formStyle->font_size) && empty($this->formStyle->text_color) && empty($this->formStyle->margin_space) && empty($this->formStyle->padding_space))
			{
				$this->formStyle->theme = "";
				$this->formStyle->themes_style->light = '{"background_color":"","background_active_color":"#FCF8E3","border_thickness":"0","border_color":"","border_active_color":"#FBEED5","rounded_corner_radius":"0","padding_space":"10","margin_space":"0","text_color":"#333333","font_type":" Verdana, Geneva, sans-serif","font_size":"14","field_background_color":"#ffffff","field_border_color":"","field_shadow_color":"","field_text_color":"#666666","message_error_background_color":"#B94A48","message_error_text_color":"#FFFFFF"}';
				$this->formStyle->themes_style->dark = '{"background_color":"","background_active_color":"#444444","border_thickness":"0","border_color":"","border_active_color":"#666666","rounded_corner_radius":"0","padding_space":"10","margin_space":"0","text_color":"#C6C6C6","font_type":" Verdana, Geneva, sans-serif","font_size":"14","field_background_color":"#000000","field_border_color":"#111111","field_shadow_color":"#000000","field_text_color":"#333333","message_error_background_color":"#B94A48","message_error_text_color":"#FFFFFF"}';
				$this->formStyle->themes = array('light', 'dark');
			}
		}
		// Hide the main menu
		$input->set('hidemainmenu', true);

		// Initialize toolbar
		$this->initToolbar();

		// Get config
		$config = JSNConfigHelper::get();
		$msgs = '';

		if (!$config->get('disable_all_messages'))
		{
			$msgs = JSNUtilsMessage::getList('FORMS');
			$msgs = count($msgs) ? JSNUtilsMessage::showMessages($msgs) : '';
		}
		// Assign variables for rendering
		$this->assignRef('msgs', $msgs);

		// Display the template
		parent::display($tpl);

		// Load assets
		JSNUniformHelper::addAssets();
		$this->addAssets();
	}

	/**
	 * Setup toolbar.
	 *
	 * @return void
	 */
	protected function initToolbar()
	{
		JToolBarHelper::apply('form.apply');
		JToolBarHelper::save('form.save');
		$input = JFactory::getApplication()->input;
		$id    = $input->getInt('form_id', 0);

		$config 	= JFactory::getConfig();
		$secret 	= $config->get('secret');
		$token 		= JSession::getFormToken();
		$edition = defined('JSN_UNIFORM_EDITION') ? strtolower(JSN_UNIFORM_EDITION) : "pro";

		$dhtml = "<a data-edition= '" . $edition . "' data-secret='" . md5($secret . $token) . "' class='btn btn-small' id='jsn-uf-preview' href='" . JURI::root() . "index.php?option=com_uniform&view=form&layout=preview&form_id=" . $id . "'><span class='icon-eye' title='" . JText::_('JSN_UNIFORM_PREVIEW_BUTTON') . "'></span> " . JText::_('JSN_UNIFORM_PREVIEW_BUTTON') . "</a>";

		$menu = JToolBar::getInstance('toolbar');
		$menu->appendButton('Custom', $dhtml, JText::_('JSN_UNIFORM_PREVIEW_BUTTON'));

	    $JVersion 	= new JVersion;
    	$JVersion 	= $JVersion->getShortVersion();

    	$classToolbar = '';

    	if (version_compare($JVersion, '3.7', '>='))
    	{
    		$classToolbar = 'j37-more';
    	}

		// Create a toolbar button that drop-down a sub-menu when clicked
		JSNMenuHelper::addEntry(
			'toolbar-save', 'JSN_UNIFORM_SAVE_AND_SHOW', '', false, 'jsn-icon16 jsn-icon-file', 'toolbar', $classToolbar
		);

		// Declare 1st-level menu items
		JSNMenuHelper::addEntry(
			'component',
			'JSN_UNIFORM_FORM_VIA_MENU_ITEM_COMPONENT',
			'',
			false,
			'',
			'toolbar-save'
		);

		JSNMenuHelper::addEntry(
			'module',
			'JSN_UNIFORM_FORM_IN_MODULE_POSITION_MODULE',
			'index.php?option=com_uniform&task=launchAdapter&type=module',
			false,
			'',
			'toolbar-save',
			'action-save-show'
		);

		JSNMenuHelper::addEntry(
			'article-content-plugin',
			'JSN_UNIFORM_FORM_INSIDE_ARTICLE_CONTENT_PLUGIN',
			'',
			false,
			'',
			'toolbar-save'
		);

		if (count($optionMenus = JSNUniformHelper::getOptionMenus()))
		{
			foreach ($optionMenus AS $option)
			{
				JSNMenuHelper::addEntry(
					preg_replace('/[^a-z0-9\-_]/', '-', $option->text),
					$option->text,
					'index.php?option=com_uniform&task=launchAdapter&type=menu&menutype=' . $option->value,
					false,
					'',
					'toolbar-save.component',
					'action-save-show'
				);
			}
		}

		JToolBarHelper::cancel('form.cancel', 'JSN_UNIFORM_CLOSE');

		JSNUniformHelper::initToolbar('JSN_UNIFORM_FORM_PAGETITLE', 'uniform-forms', false);
	}

	/**
	 * Add the libraries css and javascript
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addAssets()
	{
		$app 			= JFactory::getApplication();
		$cConfig 		= JSNConfigHelper::get('com_uniform');
		$googleApiKey 	= '';
		if (isset($cConfig->form_google_map_api_key) && $cConfig->form_google_map_api_key != '')
		{
			$googleApiKey = '&key=' . $cConfig->form_google_map_api_key;
		}
		else
		{
			$app->enqueueMessage(JText::_('JSN_UNIFORM_GOOGLE_MAP_API_KEY_IS_MISSING'), 'notice');
		}
		$input = $app->input;
		$token = JSession::getFormToken();
		$cParams = JComponentHelper::getComponent('com_uniform');
		$jce_component_id = $cParams->id;

		JSNHtmlAsset::addStyle(
			array(
				JURI::base(true) . '/components/com_uniform/assets/js/libs/colorpicker/css/colorpicker.css',
				JSN_URL_ASSETS . '/3rd-party/jquery-jwysiwyg/jquery.wysiwyg-0.9.css',
				JURI::base(true) . '/components/com_uniform/assets/js/libs/select2/select2.css',
				JSN_URL_ASSETS . '/3rd-party/codemirror/lib/codemirror.css',
				JSN_URL_ASSETS . '/3rd-party/jquery-tipsy/tipsy.css'
			)
		);

		if (JFactory::getUser()->getParam('editor') == 'jce')
		{
			if ( file_exists(JPATH_ROOT . "/administrator/components/com_jce/includes/constants.php") ) {
				include_once JPATH_ROOT . "/administrator/components/com_jce/includes/constants.php";
			}

			include_once JPATH_ROOT . "/administrator/components/com_jce/includes/loader.php";
			include_once JPATH_ROOT . "/administrator/components/com_jce/models/editor.php";
			include_once JPATH_ROOT . "/components/com_jce/editor/libraries/classes/token.php";
			include_once JPATH_ROOT . "/administrator/components/com_jce/classes/parameter.php";
			$wf = WFEditor::getInstance();
			// toolbar theme
			$toolbar = explode('.', $wf->getParam('editor.toolbar_theme', 'default'));

			$editor             = New WFModelEditor();
			$setting            = $editor->getEditorSettings();
			$jce_etag           = $setting['etag'];
			$jce_theme          = $setting['theme'];
			$jce_toolbar        = $toolbar[0];
			$jce_base_url       = $setting['base_url'];
			$jce_language       = $setting['language'];
			$jce_directionality = $setting['directionality'];


			JEditor::getInstance('jce');

			// set default component id
			//JSNHtmlAsset::addStyle(JUri::root(true) . '/administrator/index.php?option=com_jce&view=editor&layout=editor&task=pack&type=css&component_id='.$jce_component_id . '&'. $token.'=1');
			//JSNHtmlAsset::addScript(JUri::root(true) . '/administrator/index.php?option=com_jce&view=editor&layout=editor&task=pack&component_id='.$jce_component_id . '&'. $token.'=1');
		}
		elseif (JPluginHelper::isEnabled('editors', 'tinymce') == true)
		{
			JSNHtmlAsset::addScript(JUri::root(true) . '/media/editors/tinymce/tinymce.min.js');
			JSNHtmlAsset::addScript(JUri::root(true) . '/media/editors/tinymce/plugins/table/plugin.min.js');
			JSNHtmlAsset::addScript(JUri::root(true) . '/media/editors/tinymce/plugins/link/plugin.min.js');
			JSNHtmlAsset::addScript(JUri::root(true) . '/media/editors/tinymce/plugins/image/plugin.min.js');
			JSNHtmlAsset::addScript(JUri::root(true) . '/media/editors/tinymce/plugins/code/plugin.min.js');
			JSNHtmlAsset::addScript(JUri::root(true) . '/media/editors/tinymce/plugins/hr/plugin.min.js');
			JSNHtmlAsset::addScript(JUri::root(true) . '/media/editors/tinymce/plugins/charmap/plugin.min.js');
			JSNHtmlAsset::addScript(JUri::root(true) . '/media/editors/tinymce/plugins/autolink/plugin.min.js');
			JSNHtmlAsset::addScript(JUri::root(true) . '/media/editors/tinymce/plugins/lists/plugin.min.js');
			JSNHtmlAsset::addStyle(JUri::root(true) . '/media/editors/tinymce/plugins/importcss/plugin.min.js');
			JSNHtmlAsset::addScript(JUri::root(true) . '/media/editors/tinymce/themes/modern/theme.min.js');
			JSNHtmlAsset::addStyle(JUri::root(true) . '/media/editors/tinymce/skins/lightgray/content.inline.min.css');
			JSNHtmlAsset::addStyle(JUri::root(true) . '/media/editors/tinymce/skins/lightgray/content.min.css');
			JSNHtmlAsset::addStyle(JUri::root(true) . '/media/editors/tinymce/skins/lightgray/skin.min.css');
			JSNHtmlAsset::addStyle(JUri::root(true) . '/media/editors/tinymce/skins/lightgray/skin.ie7.min.css');
		}

		$formSubmitter = isset($this->_item->form_submitter) ? json_decode($this->_item->form_submitter) : "";

		$formAction = 0;
		$formActionData = '';
		if (!empty($this->formAction))
		{
			$formAction = $this->formAction;
			$formActionData = isset($this->formActionData) ? $this->formActionData : '';
		}
		$arrayTranslated = array('JSN_UNIFORM_MOVE_UP_CONTAINER', 'JSN_UNIFORM_MOVE_DOWN_CONTAINER', 'JSN_UNIFORM_ADD_CONTAINER_COLUMN', 'JSN_UNIFORM_DELETE_CONTAINER', 'JSN_UNIFORM_DELETE_CONTAINER_COLUMN', 'JSN_UNIFORM_CONFIRM_DELETE_CONTAINER', 'JSN_UNIFORM_CONFIRM_DELETE_CONTAINER_COLUMN', 'JSN_UNIFORM_COLOR_CONFIRM_RESET', 'JSN_UNIFORM_COLOR_CONFIRM_DELETE', 'JSN_UNIFORM_COLOR_CONFIRM_EXISTS', 'JSN_UNIFORM_ALL_FORM_FIELD_ARE_HIDDEN', 'JSN_UNIFORM_ALL_FORM_FIELD_ARE_DISPLAYED', 'TITLES', 'JSN_UNIFORM_DATE_HOUR_TEXT', 'JSN_UNIFORM_DATE_MINUTE_TEXT', 'JSN_UNIFORM_DATE_CLOSE_TEXT', 'JSN_UNIFORM_DATE_PREV_TEXT', 'JSN_UNIFORM_DATE_NEXT_TEXT', 'JSN_UNIFORM_DATE_CURRENT_TEXT', 'JSN_UNIFORM_DATE_MONTH_JANUARY', 'JSN_UNIFORM_DATE_MONTH_FEBRUARY', 'JSN_UNIFORM_DATE_MONTH_MARCH', 'JSN_UNIFORM_DATE_MONTH_APRIL', 'JSN_UNIFORM_DATE_MONTH_MAY', 'JSN_UNIFORM_DATE_MONTH_JUNE', 'JSN_UNIFORM_DATE_MONTH_JULY', 'JSN_UNIFORM_DATE_MONTH_AUGUST', 'JSN_UNIFORM_DATE_MONTH_SEPTEMBER', 'JSN_UNIFORM_DATE_MONTH_OCTOBER', 'JSN_UNIFORM_DATE_MONTH_NOVEMBER', 'JSN_UNIFORM_DATE_MONTH_DECEMBER', 'JSN_UNIFORM_DATE_MONTH_JANUARY_SHORT', 'JSN_UNIFORM_DATE_MONTH_FEBRUARY_SHORT', 'JSN_UNIFORM_DATE_MONTH_MARCH_SHORT', 'JSN_UNIFORM_DATE_MONTH_APRIL_SHORT', 'JSN_UNIFORM_DATE_MONTH_MAY_SHORT', 'JSN_UNIFORM_DATE_MONTH_JUNE_SHORT', 'JSN_UNIFORM_DATE_MONTH_JULY_SHORT', 'JSN_UNIFORM_DATE_MONTH_AUGUST_SHORT', 'JSN_UNIFORM_DATE_MONTH_SEPTEMBER_SHORT', 'JSN_UNIFORM_DATE_MONTH_OCTOBER_SHORT', 'JSN_UNIFORM_DATE_MONTH_NOVEMBER_SHORT', 'JSN_UNIFORM_DATE_MONTH_DECEMBER_SHORT', 'JSN_UNIFORM_DATE_DAY_SUNDAY', 'JSN_UNIFORM_DATE_DAY_MONDAY', 'JSN_UNIFORM_DATE_DAY_TUESDAY', 'JSN_UNIFORM_DATE_DAY_WEDNESDAY', 'JSN_UNIFORM_DATE_DAY_THURSDAY', 'JSN_UNIFORM_DATE_DAY_FRIDAY', 'JSN_UNIFORM_DATE_DAY_SATURDAY', 'JSN_UNIFORM_DATE_DAY_SUNDAY_SHORT', 'JSN_UNIFORM_DATE_DAY_MONDAY_SHORT', 'JSN_UNIFORM_DATE_DAY_TUESDAY_SHORT', 'JSN_UNIFORM_DATE_DAY_WEDNESDAY_SHORT', 'JSN_UNIFORM_DATE_DAY_THURSDAY_SHORT', 'JSN_UNIFORM_DATE_DAY_FRIDAY_SHORT', 'JSN_UNIFORM_DATE_DAY_SATURDAY_SHORT', 'JSN_UNIFORM_DATE_DAY_SUNDAY_MIN', 'JSN_UNIFORM_DATE_DAY_MONDAY_MIN', 'JSN_UNIFORM_DATE_DAY_TUESDAY_MIN', 'JSN_UNIFORM_DATE_DAY_WEDNESDAY_MIN', 'JSN_UNIFORM_DATE_DAY_THURSDAY_MIN', 'JSN_UNIFORM_DATE_DAY_FRIDAY_MIN', 'JSN_UNIFORM_DATE_DAY_SATURDAY_MIN', 'JSN_UNIFORM_DATE_DAY_WEEK_HEADER', 'JSN_UNIFORM_EMAIL_SETTINGS', 'JSN_UNIFORM_SELECT_MENU_ITEM', 'JSN_UNIFORM_SELECT_ARTICLE', 'JSN_UNIFORM_FORM_APPEARANCE', 'JSN_UNIFORM_SELECT', 'JSN_UNIFORM_SAVE', 'JSN_UNIFORM_CANCEL', 'JSN_UNIFORM_ADD_FIELD', 'JSN_UNIFORM_BUTTON_SAVE', 'JSN_UNIFORM_BUTTON_CANCEL', 'JSN_UNIFORM_CONFIRM_CONVERTING_FORM', 'JSN_UNIFORM_UPGRADE_EDITION_TITLE', 'JSN_UNIFORM_YOU_HAVE_REACHED_THE_LIMITATION_OF_10_FIELD_IN_FREE_EDITION', 'JSN_UNIFORM_YOU_HAVE_REACHED_THE_LIMITATION_OF_1_PAGE_IN_FREE_EDITION', 'JSN_UNIFORM_UPGRADE_EDITION', 'JSN_UNIFORM_CONFIRM_SAVE_FORM', 'JSN_UNIFORM_NO_EMAIL', 'JSN_UNIFORM_NO_EMAIL_DES', 'JSN_UNIFORM_CONFIRM_DELETING_A_FIELD', 'JSN_UNIFORM_CONFIRM_DELETING_A_FIELD_DES', 'JSN_UNIFORM_BTN_BACKUP', 'JSN_UNIFORM_IF_CHECKED_VALUE_DUPLICATION', 'JSN_UNIFORM_EMAIL_SUBMITTER_TITLE', 'JSN_UNIFORM_EMAIL_ADDRESS_TITLE', 'JSN_UNIFORM_LAUNCHPAD_PLUGIN_SYNTAX', 'JSN_UNIFORM_LAUNCHPAD_PLUGIN_SYNTAX_DES', 'JSN_UNIFORM_FORM_LIMIT_FILE_EXTENSIONS', 'JSN_UNIFORM_FOR_SECURITY_REASONS_FOLLOWING_FILE_EXTENSIONS', 'JSN_UNIFORM_FORM_LIMIT_FILE_SIZE', 'STREET_ADDRESS', 'ADDRESS_LINE_2', 'CITY', 'POSTAL_ZIP_CODE', 'STATE_PROVINCE_REGION', 'FIRST', 'MIDDLE', 'LAST', 'COUNTRY', 'JSN_UNIFORM_ALLOW_USER_CHOICE', 'JSN_UNIFORM_SET_ITEM_PLACEHOLDER', 'JSN_UNIFORM_SET_ITEM_PLACEHOLDER_DES', 'JSN_UNIFORM_SHOW_DATE_FORMAT', 'JSN_UNIFORM_SHOW_TIME_FORMAT', 'JSN_UNIFORM_ENABLE_RANGE_SELECTION', 'JSN_UNIFORM_YOU_CAN_NOT_HIDE_THE_COPYLINK', 'JSN_UNIFORM_CUSTOM_DATE_FORMAT', 'JSN_UNIFORM_LIST_ELEMENT_DESCRIPTION_LABEL', 'JSN_UNIFORM_MULTIPLE_CHOICE_ELEMENT_DESCRIPTION_LABEL', 'JSN_UNIFORM_IDENTIFICATION_CODE_ELEMENT_DESCRIPTION_LABEL', 'JSN_UNIFORM_NO_FIELD_DES', 'JSN_UNIFORM_NO_FIELD', 'JSN_UNIFORM_NO_EMAIL_DES', 'JSN_UNIFORM_SELECTED', 'JSN_UNIFORM_NO_EMAIL', 'JSN_UNIFORM_SELECT_FIELD', 'JSN_UNIFORM_SELECT_FIELDS', 'JSN_UNIFORM_PLACEHOLDER_EMAIL_FROM_0', 'JSN_UNIFORM_PLACEHOLDER_EMAIL_REPLY_TO_0', 'JSN_UNIFORM_PLACEHOLDER_EMAIL_SUBJECT_0', 'JSN_UNIFORM_PLACEHOLDER_EMAIL_FROM_1', 'JSN_UNIFORM_PLACEHOLDER_EMAIL_REPLY_TO_1', 'JSN_UNIFORM_PLACEHOLDER_EMAIL_SUBJECT_1', 'JSN_UNIFORM_CLOSE', 'JSN_UNIFORM_MODAL_PREVIEW_TITLE', 'JSN_UNIFORM_THIS_FUNCTION_IS_ONLY_ON_PRO_EDITION_SCRIPT','JSN_UNIFORM_EDIT_ELEMENT','JSN_UNIFORM_PROPERTIES','JSN_UNIFORM_DUPLICATE_ELEMENT','JSN_UNIFORM_DELETE_ELEMENT','JSN_UNIFORM_GENERAL','JSN_UNIFORM_VALUES','JSN_UNIFORM_ALL_FIELD','JSN_UNIFORM_STANDARD_FIELD','JSN_UNIFORM_EXTRA_FIELD','JSN_UNIFORM_SEARCH_LABEL','JSN_UNIFORM_BTN_CLEAR','JSN_UNIFORM_YEN','JSN_UNIFORM_RUPEE','JSN_UNIFORM_SET_ITEM_SHOW_PRICE_LABEL','JSN_UNIFORM_EXAMPLE_CODE','JSN_UNIFROM_YEAR_RANGE_MUST_BE_GREATER_THAN','JSN_UNIFORM_CLOSE','JSN_UNIFORM_TITLE','JSN_UNIFORM_IMAGES','JSN_UNIFORM_LINK','JSN_UNIFORM_MORE_INFO','JSN_UNIFORM_TITLE','JSN_UNIFORM_REQUIRED','JSN_UNIFORM_HIDDEN','JSN_UNIFORM_INSTRUCTION','JSN_UNIFORM_SIZE','JSN_UNIFORM_MINI','JSN_UNIFORM_SMALL','JSN_UNIFORM_MEDIUM','JSN_UNIFORM_LARGE','JSN_UNIFORM_LIMIT_TEXT','JSN_UNIFORM_WITHIN','JSN_UNIFORM_WORDS','JSN_UNIFORM_CHARACTERS','JSN_UNIFORM_REQUIRE_LENGTH','JSN_UNIFORM_ONE_COLUMN','JSN_UNIFORM_TWO_COLUMN','JSN_UNIFORM_THREE_COLUMN','JSN_UNIFORM_SIDE_BY_SIDE','JSN_UNIFORM_RANDOMIZE_ITEMS','JSN_UNIFORM_ENABLE_PAYMENT_FIELD','JSN_UNIFORM_AUTO','JSN_UNIFORM_PREDEFINED_VALUE','JSN_UNIFORM_AND','JSN_UNIFORM_SELECT_CHOICE_YES','JSN_UNIFORM_SELECT_CHOICE_NO','JSN_UNIFORM_LAYOUT','JSN_UNIFORM_OTHERS','JSN_UNIFORM_AND','JSN_UNIFORM_ROWS','JSN_UNIFORM_COLUMNS','JSN_UNIFORM_LIMIT_CHOICES','JSN_UNIFORM_ALLOW_MULTIPLE_SELECTION','JSN_UNIFORM_SHOW_PRICE_LABEL','JSN_UNIFORM_AUTO_INSERT_NAME_OF_CURRENT_LOGIN_USER','JSN_UNIFORM_TITLES','JSN_UNIFORM_NO_DUPLICATES','JSN_UNIFORM_AUTO_INSERT_EMAIL_OF_CURRENT_LOGIN_USER','JSN_UNIFORM_REQUIRED_CONFIRMATION','JSN_UNIFORM_ITEMS','JSN_UNIFORM_DISABLE_MULTIPLE_SELECTION','JSN_UNIFORM_ENABLE_SHOW_IN_NOTIFICATION_EMAIL','JSN_UNIFORM_MULTIPLE_UPLOADS','JSN_UNIFORM_LIMIT_FILE','JSN_UNIFORM_YEAR_RANGE_SELECTION','JSN_UNIFORM_SHOW_DECIMAL','JSN_UNIFORM_LIMIT_NUMBER','JSN_UNIFORM_PHONE_FORMAT','JSN_UNIFORM_CURRENCY_FORMAT','JSN_UNIFORM_SHOW_CURRENCY_TITLE','JSN_UNIFORM_ENCRYPTION','JSN_UNIFORM_CODE_ID_PREFIX','JSN_UNIFORM_TOTAL_MONEY','JSN_UNIFORM_TOTAL_MONEY_TEXT','JSN_UNIFORM_SHOW_TOTAL_MONEY','JSN_UNIFORM_SUBMIT_BUTTON_TEXT','JSN_UNIFORM_SHOW_BUTTON_RESET','JSN_UNIFORM_RESET_BUTTON_TEXT','JSN_UNIFORM_SHOW_BUTTON_PREVIEW','JSN_UNIFORM_PREVIEW_BUTTON_TEXT','JSN_UNIFORM_NEXT_BUTTON_TEXT','JSN_UNIFORM_PREV_BUTTON_TEXT','JSN_UNIFORM_DESIGNATE_AS_MAILCHIMP_SUBCRIBER','JSN_UNIFORM_MESSAGE_TO_SUBCRIBE_TO_MAILCHIMP','JSN_UNIFORM_SHOW_MAILCHIMP_SUBCRIBER','JSN_UNIFORM_MESSAGES_TEXT','JSN_UNIFORM_EXAMPLE_CODE','JSN_UNIFORM_GOOGLE_MAP_DESCRIPTION');
		$params = JComponentHelper::getParams('com_media');
		$listEx = '';
		$extensions = $params->get('upload_extensions');
		if ($extensions)
		{
			$extensions = explode(",", $extensions);
			$exs = array();
			foreach ($extensions as $ex)
			{
				$exs[] = strtolower($ex);
			}
			$listEx = implode(", ", array_unique($exs));
		}
		$extensions = str_replace(",", ", ", $extensions);
		$limitSize = $params->get('upload_maxsize');
		$configSizeSever = (int) (ini_get('post_max_size'));
		if ($limitSize > $configSizeSever)
		{
			$limitSize = $configSizeSever;
		}
		if ($limitSize > (int) (ini_get('upload_max_filesize')))
		{
			$limitSize = (int) (ini_get('upload_max_filesize'));
		}
		$session = JFactory::getSession();
		$openArticle = $input->getVar('opentarticle', '');
		//$redirectURLToPreview = $input->getVar('redirecturltopreview', '');
		$this->pageContent = $session->get('page_content', '', 'form-design-' . $this->_item->form_id);
		$this->edition = defined('JSN_UNIFORM_EDITION') ? strtolower(JSN_UNIFORM_EDITION) : "pro";

		JSNHtmlAsset::registerDepends('uniform/libs/googlemaps/jquery.ui.map', array('jquery', 'jquery.ui'));
		JSNHtmlAsset::registerDepends('uniform/libs/googlemaps/jquery.ui.map.services', array('jquery', 'jquery.ui', 'uniform/libs/googlemaps/jquery.ui.map'));
		JSNHtmlAsset::registerDepends('uniform/libs/googlemaps/jquery.ui.map.extensions', array('jquery', 'jquery.ui', 'uniform/libs/googlemaps/jquery.ui.map'));
		$uri = JUri::getInstance();
		//if ($googleApiKey != '')
		//{
			JSNHtmlAsset::addScript($uri->getScheme() . '://maps.googleapis.com/maps/api/js?v=3.23' . $googleApiKey . '&libraries=places');
		//}
		JSNHtmlAsset::registerDepends('uniform/libs/jquery.tmpl', array('jquery'));
		JSNHtmlAsset::registerDepends('uniform/libs/jquery-ui-timepicker-addon', array('jquery', 'jquery.ui'));
		JSNHtmlAsset::registerDepends('uniform/libs/jquery.placeholder', array('jquery'));
		JSNHtmlAsset::registerDepends('uniform/libs/colorpicker/js/colorpicker', array('jquery'));
		JSNHtmlAsset::registerDepends('uniform/libs/select2/select2', array('jquery'));
		JSNHtmlAsset::registerDepends('uniform/layout', array('jquery', 'jquery.ui'));
		JSNHtmlAsset::addScript(JSN_UNIFORM_ASSETS_URI .'/js/jsn.jquery.noconflict.js');
		$app = JFactory::getApplication();
		$getData = $app->input->getArray($_GET);
		$titleForm = isset($getData['form']) ? $getData['form'] : '';


		$defaultArray = array(
			'urlBase'            => JURI::base(),
			'token'              => $token,
			'pathRoot'           => JURI::root(),
			'opentArticle'       => $openArticle,
			'baseZeroClipBoard'  => JSN_URL_ASSETS . '/3rd-party/jquery-zeroclipboard/ZeroClipboard.swf',
			'pageContent'        => $this->pageContent,
			'edition'            => $this->edition,
			'checkSubmitModal'   => $this->checkSubmitModal,
			'urlAction'          => $this->urlAction,
			'form_style'         => $this->_item->form_style,
			'dataEmailSubmitter' => $formSubmitter,
			'language'           => JSNUtilsLanguage::getTranslated($arrayTranslated),
			'formActionData'     => $formActionData,
			'formAction'         => $formAction,
			'limitEx'            => $listEx,
			'limitSize'          => $limitSize,
			'titleForm'          => $titleForm,
			//'redirectURLToPreview' => $redirectURLToPreview
		);

		if (JFactory::getConfig()->get('editor') == 'jce')
		{
			$defaultArray['jce_etag']           = $jce_etag;
			$defaultArray['jce_theme']          = $jce_theme;
			$defaultArray['jce_toolbar']        = $jce_toolbar;
			$defaultArray['jce_language']       = $jce_language;
			$defaultArray['jce_base_url']       = $jce_base_url;
			$defaultArray['jce_component_id']   = $jce_component_id;
			$defaultArray['jce_directionality'] = $jce_directionality;

			echo JSNHtmlAsset::loadScript('uniform/form', $defaultArray, true);
		}
		else
		{
			echo JSNHtmlAsset::loadScript('uniform/form', $defaultArray, true);
		}

	}
}