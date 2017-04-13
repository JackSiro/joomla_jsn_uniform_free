<?php

/**
 * @version     $Id: form.php 19013 2012-11-28 04:48:47Z thailv $
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
jimport('joomla.application.component.controllerform');

/**
 * Form controllers of JControllerForm
 *
 * @package     Controllers
 * @subpackage  Form
 * @since       1.6
 */
class JSNUniformControllerForm extends JControllerForm
{

	protected $option = JSN_UNIFORM;
	
	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   11.1
	 */
	public function save($key = null, $urlVar = null)
	{
		JSession::checkToken('post') or die( 'Invalid Token' );
		$input = JFactory::getApplication()->input;
		$redirectUrl = $input->getVar('redirect_url', '');
		$redirectUrlForm = $input->getVar('redirect_url_form', '');
		$openArticle = $input->getVar('open_article', '');
		$formId = $input->getInt('form_id', '');
		//$redirectUrlToPreview = $input->getVar('redirect_url_to_preview', '');
		parent::save();

		$redirect = $this->redirect;

		if ($redirectUrl)
		{
			$this->setRedirect(JRoute::_($redirectUrl, false), JText::_('JLIB_APPLICATION_SAVE_SUCCESS'));
		}
		if ($openArticle)
		{
			$this->setRedirect($redirect . '&opentarticle=open');
		}
// 		if ($redirectUrlToPreview)
// 		{
// 			$this->setRedirect($redirect . '&redirecturltopreview=open');
// 		}		
		if ($redirectUrlForm)
		{
			$this->setRedirect($redirectUrlForm . '&form_id=' . $formId);
		}
		$session = JFactory::getSession();
		$sessionQueue = $session->get('registry');
		$sessionQueue->set('com_jsnuniform', null);
	}

	/**
	 * Save page form to session
	 *
	 * @return void
	 */
	public function savePage()
	{
		JSession::checkToken('get') or die( 'Invalid Token' );
		$input = JFactory::getApplication()->input;
		$postData = $input->getArray($_POST);
		
		if ($postData['form_content'] != '')
		{
			$form_content = $input->post->get('form_content', array(), 'array');
			$form_content = array('form_content' => $form_content[0]);
			$postData = array_merge($postData, $form_content);

		}
		$session = JFactory::getSession();
		$formId = isset($postData['form_id']) ? $postData['form_id'] : 0;
		
		/*$postDataFormContent 	= json_decode($postData['form_content']);
		$pageTitle 				= $this->elementPageTitle();
		array_push($postDataFormContent, $pageTitle);
		
		$postData['form_content'] = json_encode($postDataFormContent);*/
		
		if (!empty($postData['form_list_container']))
		{
			$formPageName = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($postData['form_page_name']) : $postData['form_page_name'];
			$session->set('form_container_page_' . $formPageName, $postData['form_list_container'], 'form-design-' . $formId);
		}
		if (!empty($postData['form_page_name']))
		{
			$tmpIdentify = array();
			$formContent = '';
			if (isset($postData['form_content']))
			{
				$formContent = is_array($postData['form_content']) ? json_encode($postData['form_content']) : $postData['form_content'];
				$formContent = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($formContent) : $formContent;
			}
			$formPageName = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($postData['form_page_name']) : $postData['form_page_name'];
			$session->set('form_page_' . $formPageName, $formContent, 'form-design-' . $formId);
		}
		if (!empty($postData['form_list_page']))
		{
			$count = 0;
			foreach ($postData['form_list_page'] as $listPage)
			{
				$dataField = "";
				$pageName = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($listPage[0]) : $listPage[0];
				if (isset($pageName) && isset($postData['form_page_name']))
				{
					$dataField = $session->get('form_page_' . $pageName, '', 'form-design-' . $formId);
					if (!empty($dataField))
					{
						if (!is_array($dataField))
						{
							$dataField = json_decode($dataField);
						}
						foreach ($dataField as $index => $field)
						{
							$count++;
							if (!empty($field->identify))
							{
								while (in_array($field->identify, $tmpIdentify))
								{
									$field->identify = $field->identify . '_' . ($count + 1);
								}
								$tmpIdentify[] = $field->identify;
								$dataField[$index]->identify = preg_replace('/[^a-z0-9-._]/i', "", $field->identify);
							}
						}
						$session->set('form_page_' . $pageName, json_encode($dataField), 'form-design-' . $formId);
					}
				}
			}
			$formListPage = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes(json_encode($postData['form_list_page'])) : json_encode($postData['form_list_page']);
			$session->set('form_list_page', $formListPage, 'form-design-' . $formId);
		}
		jexit();

	}

	/**
	 * load data field on session
	 *
	 * @return json code
	 */
	public function loadSessionField()
	{
		JSession::checkToken('get') or die( 'Invalid Token' );
		$input = JFactory::getApplication()->input;
		$postData = $input->getArray($_POST);
		
		if ($postData['form_content'] != '')
		{
			$form_content = $input->post->get('form_content', array(), 'array');
			$form_content = array('form_content' => $form_content[0]);
			$postData = array_merge($postData, $form_content);
		}

		/*$postDataFormContent 	= json_decode($postData['form_content']);
		$pageTitle 				= $this->elementPageTitle();
		array_push($postDataFormContent, $pageTitle);
		
		$postData['form_content'] = json_encode($postDataFormContent);*/
		
		$formId = isset($postData['form_id']) ? $postData['form_id'] : 0;
		$session = JFactory::getSession();
		$formPage = array();
		$tmpIdentify = array();

		if (isset($postData['form_page_name']) && isset($postData['form_content']))
		{
			$formContent = is_array($postData['form_content']) ? json_encode($postData['form_content']) : $postData['form_content'];
			$formContent = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($formContent) : $formContent;

			$formPageName = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($postData['form_page_name']) : $postData['form_page_name'];
			$session->set('form_page_' . $formPageName, $formContent, 'form-design-' . $formId);
		}
		if (!empty($postData['form_list_page']))
		{
			$count = 0;
			foreach ($postData['form_list_page'] as $listPage)
			{
				$dataField = "";
				$pageName = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($listPage[0]) : $listPage[0];
				if (isset($pageName) && isset($postData['form_page_name']))
				{
					$dataField = $session->get('form_page_' . $pageName, '', 'form-design-' . $formId);

					if (isset($dataField))
					{
						if (!is_array($dataField))
						{
							$dataField = json_decode($dataField);
						}
						if (is_array($dataField))
						{
							foreach ($dataField as $index => $field)
							{
								$count++;
								while (in_array($field->identify, $tmpIdentify))
								{
									$field->identify = $field->identify . '_' . ($count + 1);
								}
								$tmpIdentify[] = $field->identify;
								$dataField[$index]->identify = preg_replace('/[^a-z0-9-._]/i', "", $field->identify);
							}
							$session->set('form_page_' . $pageName, json_encode($dataField), 'form-design-' . $formId);
						}

						if (!empty($dataField) && $dataField != 'null')
						{
							$formPage = array_merge($formPage, $dataField);
						}
					}
				}
			}
			if (!empty($formPage))
			{
				echo json_encode($formPage);
			}
		}

		jexit();

	}

	/**
	 * load page on session
	 *
	 * @return json code
	 */
	public function loadPage()
	{
		JSession::checkToken('get') or die( 'Invalid Token' );
		$input = JFactory::getApplication()->input;
		$post = $input->getArray($_POST);
		$form_page_old_content = $input->post->get('form_page_old_content', array(), 'array');
		$form_page_old_content = array('form_page_old_content' => $form_page_old_content[0]);
		$post = array_merge($post, $form_page_old_content);
		$form_page_old_container = $input->post->get('form_page_old_container', array(), 'array');
		$form_page_old_container = array('form_page_old_container' => $form_page_old_container[0]);
		$post = array_merge($post, $form_page_old_container);

		$formId = isset($post['form_id']) ? $post['form_id'] : 0;
		$dataPage = "";
		$pageDefault = isset($post['join_page']) ? $post['join_page'] : '';

		if (!empty($post['form_page_name']))
		{
			$session = JFactory::getSession();
			$formPageName = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($post['form_page_name']) : $post['form_page_name'];
			$formPage = $session->get('form_page_' . $formPageName, '', 'form-design-' . $formId);

			if (isset($post['form_page_old_name']) && $post['form_page_old_name'] != $formPageName)
			{

				if (!empty($post['form_page_old_content']))
				{

					$formContentOld = is_array($post['form_page_old_content']) ? json_encode($post['form_page_old_content']) : $post['form_page_old_content'];
					$formOldContent = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($formContentOld) : $formContentOld;
					$session->set('form_page_' . $post['form_page_old_name'], $formOldContent, 'form-design-' . $formId);
				}
				if (!empty($post['form_page_old_container']))
				{

					$formContainerOld = is_array($post['form_page_old_container']) ? json_encode($post['form_page_old_container']) : $post['form_page_old_container'];
					$formContainerOld = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($formContainerOld) : $formContainerOld;
					$session->set('form_container_page_' . $post['form_page_old_name'], $formContainerOld, 'form-design-' . $formId);
				}
			}

			if (isset($formPage) && $pageDefault != 'defaultPage')
			{
				if (is_array($formPage))
				{
					$dataPage = json_encode($formPage);
				}
				else
				{
					$dataPage = $formPage;
				}
			}
			else
			{
				if (!empty($post['form_id']))
				{
					$formId = (int) $post['form_id'];
					$model = $this->getModel('form');
					$items = $model->getItem($formId);
					if (!empty($items->form_content))
					{
						foreach ($items->form_content as $formContent)
						{
							$session->set('form_page_' . $formContent->page_id, $formContent->page_content, 'form-design-' . $formId);
						}
						$dataPage = $session->get('form_page_' . $formPageName, '', 'form-design-' . $formId);
					}
				}
				else
				{
					$dataPage = $session->get('form_page_' . $formPageName, '', 'form-design-' . $formId);
				}
			}
		}
		$containerPage = $session->get('form_container_page_' . $formPageName, '', 'form-design-' . $formId);
		$containerPage = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($containerPage) : $containerPage;
		if (!empty($post['join_page']) && $post['join_page'] == "join" && isset($post['form_list_page']) && count($post['form_list_page']) > 1)
		{
			$dataListPage = array();
			$listPage = $session->get('form_list_page');
			$formPageIndex = array();
			$countPosition = 0;
			$listPageContainer = array();
			foreach ($post['form_list_page'] as $index => $listPage)
			{
				$pageName = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($listPage[0]) : $listPage[0];
				if (!empty($pageName) && !empty($post['form_page_name']))
				{
					$positionContainer = array();
					$pageContent = $session->get('form_page_' . $pageName, '', 'form-design-' . $formId);
					$pageContainer = $session->get('form_container_page_' . $pageName, '', 'form-design-' . $formId);
					$pageContainer = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($pageContainer) : $pageContainer;
					$pageContainer = json_decode($pageContainer);

					foreach ($pageContainer as $containerDetail)
					{
						$countPosition++;
						foreach ($containerDetail as $cd)
						{

								$position = explode("_", $cd->columnName);
								$positionContainer[$cd->columnName] = $position[0] . "_" . ($countPosition);
								$cd->columnName = $position[0] . "_" . ($countPosition);


							$listPageContainer[$countPosition-1][] = $cd;
						}
					}
					if (!empty($pageContent) && $pageContent != 'null')
					{
						$pContent = array();
						$pageContent = json_decode($pageContent);
						foreach ($pageContent as $pct)
						{

								$pct->position = $positionContainer[$pct->position];

							$pContent[] = $pct;
						}
						$dataListPage = array_merge($dataListPage, $pContent);
					}
				}
				if ($index == 0)
				{
					$formPageIndex[] = $pageName;
					$pageName1 = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($listPage[1]) : $listPage[1];
					$formPageIndex[] = $pageName1;
				}
				else
				{
					$session->clear('form_page_' . $pageName, 'form-design-' . $formId);
				}
			}
			$dataListPageEncode = json_encode($dataListPage);
			$session->clear('form_list_page', 'form-design-' . $formId);
			$session->set('form_page_' . $formPageIndex[0], $dataListPageEncode, 'form-design-' . $formId);
			$session->set('form_list_page', json_encode($formPageIndex), 'form-design-' . $formId);
			$session->set('form_container_page_' . $formPageIndex[0], json_encode($listPageContainer), 'form-design-' . $formId);
			echo json_encode(array('dataField' => $dataListPageEncode, 'containerPage' => json_encode($listPageContainer)));
		}
		else
		{
			echo json_encode(array('dataField' => $dataPage, 'containerPage' => $containerPage));
		}
		jexit();

	}

	/**
	 * get count field
	 *
	 * @return  void
	 */
	public static function getcountfield()
	{
		JSession::checkToken('get') or die( 'Invalid Token' );
		$input = JFactory::getApplication()->input;
		$postData = $input->getArray($_POST);
		$fieldId = isset($postData['field_id']) ? $postData['field_id'] : 0;
		$formId = isset($postData['form_id']) ? $postData['form_id'] : 0;
		if ($formId && $fieldId)
		{
			echo json_encode(JSNUniformHelper::getDataSumbissionByField($fieldId, $formId));
		}
		jexit();
	}
	/**
	 * Load ajax form all Plugin in uniform
	 * @return  void
	 */
	public static function do_ajax($func,$name,$param){
		$input = JFactory::getApplication()->input;
		$postData = $input->getArray($_POST);
		if (JSession::checkToken('get') == false)
		{
			JSession::checkToken();
			if (JSession::checkToken() == false)
			{
				die( 'Invalid Token' );
			}

		}

		if(isset($postData['val'])){
			$param = (array) json_decode($postData['val']);
			JPluginHelper::importPlugin('uniform', $param['plgName']);
			$dispatcher = JEventDispatcher::getInstance();
			$results = $dispatcher->trigger($param['func'], array($param));
			echo json_encode($results[0]);
			jexit();
		}else{
			JPluginHelper::importPlugin('uniform', $name);
			$dispatcher = JEventDispatcher::getInstance();
			$dispatcher->trigger($func, array($param));
		}
	}
	
	public function getFormDataForPreview()
	{
		JSession::checkToken('get') or die( 'Invalid Token' );
		$input 		= JFactory::getApplication()->input;
		$postData 	= $input->getArray($_POST);
		$formId 	= $postData['jform']['form_id'];
			
		$model 		= $this->getModel();
		$formPages 	= $model->getFormPagePreview();
		$items 		= $model->getItemPreview();
			
			
		$result = array('items' => $items, 'formpages' => $formPages, 'formId' => $formId);
		echo json_encode($result);
		exit();		
	}
	
	/*public function elementPageTitle()
	{
		$option 				= new stdClass();
		$option->label 			= JText::_('JSN_UNIFORM_ELEMENT_PAGE_TITLE');
		$option->instruction 	= '';
		$option->required 		= '';
		$option->limitation 	= '';
		$option->limitMin 		= '';
		$option->limitMax 		= '';
		$option->size 			= '';
		$option->value 			= '';
		$option->identify 		= 'page_title';
		$option->customClass 	= '';
		$option->decimal 		= '';
		
		$elementPageTitle 				= new stdClass();
		$elementPageTitle->id 			= '';
		$elementPageTitle->identify 	= 'page_title';
		$elementPageTitle->options 		= $option;
		$elementPageTitle->position 	= '';
		$elementPageTitle->type 		= 'page_title';
		$elementPageTitle->token 		= JSession::getFormToken();
		
		return $elementPageTitle;
	}*/
}
