<?php

/**
 * @version     $Id: forms.php 19014 2012-11-28 04:48:56Z thailv $
 * @package     JSNUniform
 * @subpackage  Controller
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2016 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');
/**
 * Forms controllers of JControllerForm
 * 
 * @package     Controllers
 * @subpackage  Forms
 * @since       1.6
 */
class JSNUniformControllerForms extends JSNBaseController
{
	public function __construct($config = array())
	{
		// Get input object
		$this->input = JFactory::getApplication()->input;

		parent::__construct($config);
	}
	

	/**
	 *  view select form
	 * 
	 * @return html code
	 */
	public function viewSelectForm()
	{
		$user	= JFactory::getUser();
		$userId		= $user->get('id');
		
		$isCreate = (bool) $user->authorise('core.create');
		$isEdit = (bool) $user->authorise('core.edit');

		if (!$userId || (!$isCreate && !$isEdit))
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$uri	= JUri::root(true);
		$document = JFactory::getDocument();
		$document->addScript($uri . '/media/system/js/mootools-core.js');
		$document->addScript($uri . '/media/system/js/core.js');
		$document->addScript($uri . '/media/system/js/mootools-more.js');
		JHtml::_('jquery.framework');
		$jsCode = '(function($){
				$(document).ready(function () {
					var form = $("select.jform_request_form_id");
					form.change(function () {
					if (form.val() == 0) {
                        $(this).css("background", "#CC0000").css("color", "#fff")
                        $("#select-forms").attr("disabled", "disabled");
                    } else {
                        $("#select-forms").removeAttr("disabled");
                        form.css("background", "#FFFFDD").css("color", "#000")
                    }						
					}).trigger("change");
					
					$("#select-forms").click(function () {
	                    if (window.parent)
	                    {
	                        window.parent.jsnSelectForm($("select.jform_request_form_id").val());
	                    }
	                });
				});
			})(jQuery)';
		
		$document->addScriptDeclaration($jsCode);
		echo JSNUniformHelper::getSelectForm('jform[params][form_id]', 'jform_params_form_id', "contentfrontend");
	}
}
