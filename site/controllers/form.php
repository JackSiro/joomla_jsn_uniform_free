<?php

/**
 * @version     $Id: form.php 19014 2012-11-28 04:48:56Z thailv $
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

jimport('joomla.application.component.controller');

/**
 * Form controllers of JControllerForm
 *
 * @package     Controllers
 * @subpackage  Form
 * @since       1.6
 */
class JSNUniformControllerForm extends JSNBaseController
{

	protected $option = JSN_UNIFORM;

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}

    /**
     * Save File sent by Ajax Upload
     * Save uploaded path to $_Session
     * @return Html messages
     */
    public function uploadFile()
    {
        // Check for request forgeries
        JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));

		$app 					= JFactory::getApplication();
		$input 					= $app->input;
		$index 					= $input->getInt('index', 0);
		$postData 				= $input->getArray($_POST);
		$postData ['field_id'] 	= $input->getInt('field_id', 0);
		$postData ['form_id'] 	= $input->getInt('form_id', 0);
		$postData ['is_last'] 	= $input->getCmd('is_last', '');

        if (strtolower($input->getMethod()) != 'post')
        {
        	$return = array('status'=> 'error', 'field_id' => $postData['field_id'],'message' => 'Invalid request', 'index' => $index);
        	echo json_encode($return);
        	exit();
        }

        // Check for request forgeries.
        if (@$_SERVER['CONTENT_LENGTH'] < (int) (ini_get('post_max_size')) * 1024 * 1024)
        {
            $model 				= $this->getModel('submission');
            $configFolderUpload = JSNUniformHelper::getDataConfig('folder_upload');
            $targetDir 			= isset($configFolderUpload->value) ? $configFolderUpload->value : "images/jsnuniform/";

            // Get files

            $input 		= JFactory::getApplication()->input;
            $filesData 	= $_FILES['files'];
            $fileData 	= array();

            //If you want to upload multiple file, skip this foreach function
            foreach ($filesData AS $key => $value)
            {
                $fileData[0][$key] = $value[0];
            }

            //If you want to upload multiple file, skip this foreach function
	        if (!empty($postData['form_id']) && JSNUniformHelper::checkStateForm($postData['form_id']))
            {
                if ($model->uploadFile($fileData, $postData, $targetDir))
                {
                    $return = array('status'=> 'success', 'message' =>'', 'field_id' => $postData['field_id'], 'index' => $index);
                    echo json_encode($return);
                    exit();
                }
                else
                {
                    $error = json_decode($model->getError());
                    $return = array('status'=> 'error', 'field_id' => $error->field_id, 'message' => $error->message, 'index' => $index);
                    echo json_encode($return);
                    exit();
                }
            }
            else
            {
                $return = array('status'=> 'error', 'field_id' => $postData['field_id'],'message' =>'Invalid request', 'index' => $index);
                echo json_encode($return);
                exit();
            }
        }
        else
        {
            $postMaxSize = (int) ini_get('post_max_size');
            if ($postMaxSize > (int) (ini_get('upload_max_filesize')))
            {
                $postMaxSize = (int) (ini_get('upload_max_filesize'));
            }

            $return = array('status'=> 'error', 'field_id' => $postData['field_id'], 'message' =>JText::sprintf('JSN_UNIFORM_POST_MAX_SIZE', $postMaxSize), 'index' => $index);
            echo json_encode($return);
            exit();
        }
    }

	/**
	 * Save data submission
	 *
	 * @return Html messages
	 */
	public function save()
	{
		JSession::checkToken() or die( 'Invalid Token' );
		// Check for request forgeries.
		if (@$_SERVER['CONTENT_LENGTH'] < (int) (ini_get('post_max_size')) * 1024 * 1024)
		{
			$input = JFactory::getApplication()->input;
			$postData = $input->getArray($_POST);
			$model = $this->getModel('form');
			if (!empty($postData['form_id']) && JSNUniformHelper::checkStateForm($postData['form_id']))
			{
				$return = $model->save($postData);
			}

			if (isset($return->error))
			{
				//echo json_encode(array('error' => $return->error));
				echo '<input type="hidden" name="error" value=\'' . htmlentities(json_encode($return->error), ENT_QUOTES, "UTF-8") . '\'/>';
				exit();
			}
			else
			{
				if (isset($return->actionForm) && $return->actionForm == 'message')
				{
					//	echo json_encode(array('message' => $return->actionFormData));
					echo '<input type="hidden" name="message" value=\'' . htmlentities($return->actionFormData, ENT_QUOTES, "UTF-8") . '\'/>';
					exit();
				}
				elseif (isset($return->actionForm) && $return->actionForm == 'url')
				{
					//echo "<div class=\"src-redirect\">{$return->actionFormData}</div>";
					echo '<input type="hidden" name="redirect" value=\'' . htmlentities($return->actionFormData, ENT_QUOTES, "UTF-8") . '\'/>';
					exit();
				}
				else
				{
					exit();
				}
			}
		}
		else
		{
			$postMaxSize = (int) ini_get('post_max_size');
			if ($postMaxSize > (int) (ini_get('upload_max_filesize')))
			{
				$postMaxSize = (int) (ini_get('upload_max_filesize'));
			}
			//echo json_encode(array('error' => array('max-upload' => JText::sprintf('JSN_UNIFORM_POST_MAX_SIZE', $postMaxSize))));
			echo '<input type="hidden" name="error" value=\'' . htmlentities(json_encode(array('max-upload' => JText::sprintf('JSN_UNIFORM_POST_MAX_SIZE', $postMaxSize))), ENT_QUOTES, "UTF-8") . '\'/>';
			exit();
		}
	}

	/**
	 *     get html form
	 *
	 * @return string
	 */
	function getHtmlForm()
	{
		$input = JFactory::getApplication()->input;
		$formId = $input->getInt('form_id', 0);

		if ($formId)
		{
			$formName = md5(date("Y-m-d H:i:s")) . rand(0, 1000);
			echo JSNUniformHelper::generateHTMLPages($formId, $formName, "ajax");
			exit();
		}
	}

	/**
	 *  Refresh captcha
	 *
	 * @return string
	 */
	function refreshCaptcha()
	{
		JSession::checkToken('get') or die( 'Invalid Token' );

		require_once JPATH_ROOT . '/components/com_uniform/libraries/3rd-party/securimage/securimage.php';

		$input    = JFactory::getApplication()->input;
		$formName = $input->getString('namespace', '');

		$img = new Securimage();
		$img->case_sensitive = true; // true to use case sensitve codes - not recommended
		$img->image_bg_color = new Securimage_Color("#ffffff"); // image background color
		$img->text_color = new Securimage_Color("#000000"); // captcha text color
		$img->num_lines = 0; // how many lines to draw over the image
		$img->line_color = new Securimage_Color("#0000CC"); // color of lines over the image
		$img->namespace = $formName;
		$img->signature_color = new Securimage_Color(rand(0, 64), rand(64, 128), rand(128, 255)); // random signature color
		ob_start();
		$img->show(JPATH_ROOT . '/components/com_uniform/libraries/3rd-party/securimage/backgrounds/bg4.png');
		$dataCaptcha = base64_encode(ob_get_clean());
		$html = 'data:image/png;base64,' . $dataCaptcha;
		echo $html;exit();
	}

}
