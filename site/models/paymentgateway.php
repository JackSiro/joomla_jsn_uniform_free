<?php

/**
 * @version     $Id: submissions.php 19013 2012-11-28 04:48:47Z thailv $
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


/**
 * JSNUniform model PaymentgateWay
 *
 * @package     Models
 * @subpackage  Submissions
 * @since       1.6
 */
class JSNUniformModelPaymentgateWay extends JSNBaseModel
{
	public function getDataForm($formID)
	{
		$this->_db->setQuery($this->_db->getQuery(true)->select('*')->from('#__jsn_uniform_forms')->where("form_id = " . (int) $formID));
		$dataForms = $this->_db->loadObject();
		return $dataForms;
	}

	public function getActionForm($formAction, $formData, &$return)
	{
		switch($formAction)
		{
			case 1:
				$return->actionForm = "url";
				$return->actionFormData = $formData;
				break;
			case 2:
				$this->_db->setQuery($this->_db->getQuery(true)->select('link')->from("#__menu")->where("id = " . (int) $formData));
				$menuItem = $this->_db->loadObject();
				$return->actionForm = "url";
				$return->actionFormData = isset($menuItem->link) ? $menuItem->link : '';
				break;
			case 3:
				require_once JPATH_SITE . '/components/com_content/helpers/route.php';
				$this->_db->setQuery($this->_db->getQuery(true)->select('a.catid,CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug')->from("#__content AS a")->join("LEFT", "#__categories AS cc ON a.catid = cc.id")->where('a.id = ' . (int) $formData));
				$article = $this->_db->loadObject();
				$return->actionForm = "url";
				$return->actionFormData = JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid));
				break;
			case 4:
				$return->actionForm = "message";
				$return->actionFormData = $formData;
				break;
		}
	}

	public function deleteSubmissionData($subID, $formID, $method)
	{
		try
		{
			// delete submission
			$this->_db->setQuery("DELETE FROM #__jsn_uniform_submissions WHERE submission_id = {$subID} AND form_id = {$formID}");
			$this->_db->execute();
			// delete submission data
			$this->_db->setQuery("DELETE FROM #__jsn_uniform_submission_data WHERE submission_id = {$subID} AND form_id = {$formID}");
			$this->_db->execute();
			$this->_db->setQuery($this->_db->getQuery(true)->select('count(submission_id)')->from("#__jsn_uniform_submissions")->where("form_id=" . (int) $formID));
			$countSubs= $this->_db->loadResult();
			$table = JTable::getInstance('JsnForm', 'JSNUniformTable');
			$table->bind(array('form_id' => (int) $formID, 'form_submission_cout' => $countSubs));
			if (!$table->store())
			{
				return false;
			}
		}
		catch (Exception $e)
		{
			return false;
		}
		return true;
	}
	
}
