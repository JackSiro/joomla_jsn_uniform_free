<?php

/**
 * @version     $Id: submission.php 19013 2012-11-28 04:48:47Z thailv $
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
jimport('joomla.html.parameter');

/**
 * JSNUniform model Submission
 *
 * @package     Models
 * @subpackage  Submission
 * @since       1.6
 */
class JSNUniformModelSubmission extends JModelAdmin
{

	protected $option = JSN_UNIFORM;
	var $_formId;
	var $_submissionId;

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
	public function getTable($type = 'JsnSubmission', $prefix = 'JSNUniformTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return    mixed    A JForm object on success, false on failure
	 *
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_uniform.submission', 'submission', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		return $form;
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
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
		$table = $this->getTable();

		if ($pk > 0)
		{
			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}
		}

		// Convert to the JObject before adding other data.
		$properties = $table->getProperties(1);
		$item = Joomla\Utilities\ArrayHelper::toObject($properties, 'JObject');

		if (property_exists($item, 'params'))
		{
			$registry = new JRegistry;
			$registry->loadString($item->params);
			$item->params = $registry->toArray();
		}
		$edition = defined('JSN_UNIFORM_EDITION') ? strtolower(JSN_UNIFORM_EDITION) : "pro";
		if (isset($item->form_id) && isset($item->submission_id) && $edition == "free")
		{
			$this->_db->setQuery(
				$this->_db->getQuery(true)
					->select('submission_id')
					->from("#__jsn_uniform_submissions")
					->order('submission_id ASC')
					->where('form_id =' . (int) $item->form_id, 'AND')
				, 300, 1
			);
			$maxId = $this->_db->loadResult();
			if (!empty($maxId) && $maxId < $item->submission_id)
			{
				return false;
			}
		}
		$this->_formId = $item->form_id;
		$this->_submissionId = $item->submission_id;
		$this->_input = JFactory::getApplication()->input;
		$menu = JFactory::getApplication()->getMenu();
		$menuItem = $menu->getItem((int) $this->_input->get("Itemid"));
		$params = json_decode($menuItem->params);
		$formId = !empty($params->form_id) ? $params->form_id : "0";
		if ($formId != $item->form_id)
		{
			$item = new stdClass;
		}

		return $item;
	}

	/**
	 * Retrieve fields for use in page submitted detail
	 *
	 * @return Object List
	 */
	public function getDataFields()
	{
		if (!empty($this->_formId) && is_numeric($this->_formId))
		{
			$this->_db->setQuery(
				$this->_db->getQuery(true)
					->select('fi.field_identifier,fi.field_title,fo.form_title,fo.form_id,fi.field_type,fi.field_id')
					->from('#__jsn_uniform_fields AS fi')
					->join('INNER', '#__jsn_uniform_forms AS fo ON fo.form_id = fi.form_id')
					->where('fi.form_id=' . intval($this->_formId))
					->order('fi.field_ordering ASC')
			);
			return $this->_db->loadObjectList();
		}
	}

	/**
	 * Retrieve submission for use in page submitted detail
	 *
	 * @return Object
	 */
	public function getDataSubmission()
	{
		if (!empty($this->_formId) && is_numeric($this->_formId) && !empty($this->_submissionId) && is_numeric($this->_submissionId))
		{
			$item = new stdClass;
			$query = $this->_db->getQuery(true)
				->select('*')
				->from("#__jsn_uniform_submission_data")
				->where('submission_id =' . (int) $this->_submissionId);
			$this->_db->setQuery($query);
			$submissions = $this->_db->loadObjectList();
			foreach ($submissions as $submission)
			{
				$item->{"sd_" . $submission->field_id} = $submission->submission_data_value;
			}
			return $item;
		}
	}

	/**
	 *  get info form
	 *
	 * @return type
	 */
	public function getInfoForm()
	{
		if (!empty($this->_formId) && is_numeric($this->_formId) && !empty($this->_submissionId) && is_numeric($this->_submissionId))
		{
			$this->_db->setQuery($this->_db->getQuery(true)->select('*')->from("#__jsn_uniform_forms")->where('form_id=' . intval($this->_formId)));

			return $this->_db->loadObject();
		}
	}

	/**
	 * get all page in form
	 *
	 * @return Object list
	 */
	public function getFormPages()
	{
		if (!empty($this->_formId) && is_numeric($this->_formId))
		{
			$this->_db->setQuery($this->_db->getQuery(true)->select('*')->from("#__jsn_uniform_form_pages")->where('form_id=' . intval($this->_formId)));
			return $this->_db->loadObjectList();
		}
	}

	/**
	 * getNextAndPreviousForm
	 *
	 * @return type
	 */
	public function getNextAndPreviousForm()
	{
		$formList = array();
		if (!empty($this->_formId) && is_numeric($this->_formId) && !empty($this->_submissionId) && is_numeric($this->_submissionId))
		{
			$this->_db->setQuery(
				$this->_db->getQuery(true)
					->select('submission_id')
					->from("#__jsn_uniform_submission_data")
					->where('form_id = ' . (int) $this->_formId)
					->order('submission_id ASC')
				, 300, 1
			);
			$maxId = $this->_db->loadResult();
			$edition = defined('JSN_UNIFORM_EDITION') ? strtolower(JSN_UNIFORM_EDITION)  : "premium";
			if ($this->_submissionId + 1 < $maxId || empty($maxId) || $edition != "free")
			{
				$this->_db->setQuery($this->_db->getQuery(true)->select('submission_id')->from("#__jsn_uniform_submission_data")->where('form_id = ' . (int) $this->_formId)->where('submission_id > ' . intval($this->_submissionId))->order('`submission_id` ASC'), 0, 1);
				$formList['next'] = $this->_db->loadResult();
			}
			$this->_db->setQuery($this->_db->getQuery(true)->select('submission_id')->from("#__jsn_uniform_submission_data")->where('form_id = ' . (int) $this->_formId)->where('submission_id < ' . intval($this->_submissionId))->order('`submission_id` DESC'), 0, 1);
			$formList['previous'] = $this->_db->loadResult();
		}
		return $formList;
	}


	/**
	 * Override save method to save form fields to database
	 *
	 * @return boolean
	 */
	public function save($data)
	{
		$input = JFactory::getApplication()->input;
		$postData = $input->getArray($_POST);
		$formId = !empty($data['filter_form_id']) ? $data['filter_form_id'] : 0;
		$this->_db->setQuery($this->_db->getQuery(true)->select('*')->from("#__jsn_uniform_forms")->where('form_id=' . intval($formId)));
		$infoForm = $this->_db->loadObject();
		$user = JFactory::getUser();
		$groupEditSubmision = isset($infoForm->form_access) ? $infoForm->form_access : "";

		if ((string) $infoForm->form_edit_submission == '1')
		{

			$checkEditSubmission = JSNUniformHelper::checkEditSubmission($user->id, $groupEditSubmision);
		}
		else
		{
			$checkEditSubmission = false;
		}

		if ($checkEditSubmission)
		{

			if (isset($postData['submission']) && is_array($postData['submission']) && isset($postData['filter_form_id']) && isset($postData['cid']))
			{
				foreach ($postData['submission'] as $key => $value)
				{
					if (is_array($value) && !empty($value['likert']))
					{
						$data = array();
						foreach ($value as $items)
						{
							if (isset($items))
							{
								foreach ($items as $k => $item)
								{
									$data[$k] = $item;
								}
							}
						}
						$value = $data ? json_encode($data) : "";
					}
					$query = $this->_db->getQuery(true);
					$query->update($this->_db->quoteName("#__jsn_uniform_submission_data"));
					$query->set("submission_data_value = " . $this->_db->Quote($value));
					$query->where('submission_id = ' . (int) $postData['cid']);
					$query->where('field_id = ' . (int) str_replace("sd_", "", $key));
					$this->_db->setQuery($query);
					$this->_db->execute();
				}
			}
		}
		return true;
	}

    /**
     *
     * Save file (from JSNUniformControllerForm::saveFile )
     * @return boolean
     */
	public function uploadFile($fileData, $postData, $targetDir)
    {
        // Remove this if you want to allow multiple upload file

        if (count($fileData) > 1)
        {
            $error = array();
            $error['field_id'] = $postData['field_id'];
            $error['message'] = "Only allows to upload one file";
            $this->setError(json_encode($error));
            return false;
        }

        //Check if has submission data on Session
        $session = JFactory::getSession();
        if ($session->has('jsn_submission_form_' . $postData['form_id']))
        {
            $submissionId = $session->get('jsn_submission_form_' . $postData['form_id']);
        }
        else
        {
            $error 				= array();
            $error['field_id'] 	= $postData['field_id'];
            $error['message'] 	= "Fail to upload file, please reload page";
            $this->setError(json_encode($error));
            return false;
        }

        $targetDir = str_replace("//", "/", $targetDir . '/jsnuniform_uploads/' . $postData['form_id'].'/');
        $done = 0;

        //Get Field Setting
        $this->_db->setQuery($this->_db->getQuery(true)->select('*')->from('#__jsn_uniform_fields')->where("form_id = " . (int) $postData['form_id']." AND field_type = 'file-upload'"));
        $fetchedFields = $this->_db->loadObjectList();

        $fieldsSettings = array();

        foreach ($fetchedFields AS $item)
        {
            $fieldsSettings[$item->field_id] = $item->field_settings;
        }

        foreach ($fileData AS $file)
        {
            $err = null;

            if (JSNUniformUpload::canUpload($file, $err, json_decode($fieldsSettings[$postData['field_id']])))
            {
                if (!JFilterInput::isSafeFile($file))
                {
                    $this->deleteRedundantData($submissionId, $targetDir, $postData['form_id']);
                    $error = array();
                    $error['field_id'] 	= $postData['field_id'];
                    $error['message'] 	= JTEXT::_("JSN_UNIFORM_UNSAFE_FILE");
                    $this->setError(json_encode($error));
                    return false;
                }
                else
                {
                    $nameFile = strtolower(date('YmdHiS') . $postData['field_id']. '_' .rand(100000000, 9999999999999) . '_' . $file['name']);
                    $filepath = JPath::clean($targetDir . $nameFile);
                    if (JFile::upload($file['tmp_name'], $filepath))
                    {
                        $fields = array(
                            'name' => $file['name'],
                            'link'=> $nameFile
                        );
                        $this->updateAfterUploadFile($submissionId, $postData['field_id'], json_encode($fields));
                        $done ++;
                    }
                    else
                    {
                        $this->deleteRedundantData($submissionId, $targetDir, $postData['form_id']);
                        $error 				= array();
                        $error['field_id'] 	= $postData['field_id'];
                        $error['message'] 	= JTEXT::_("JSN_UNIFORM_ERROR_UNABLE_TO_UPLOAD_FILE");
                        $this->setError(json_encode($error));
                        return false;
                    }
                }
            }
            else
            {
                $this->deleteRedundantData($submissionId, $targetDir, $postData['form_id']);
                $error 				= array();
                $error['field_id'] 	= $postData['field_id'];
                $error['message'] 	= $err;
                $this->setError(json_encode($error));
                return false;
            }
        }

        //if is last file, re-check to confirm all required field have data
        if ($postData['is_last'] == 'yes')
        {
            $fieldIdsToCheck = array();
            foreach ($fetchedFields AS $field)
            {
                //Get list field is required
                $fieldSettings = json_decode($field->field_settings);
                if ($fieldSettings->options->required == 1)
                {
                    $fieldIdsToCheck[] = $field->field_id;
                }
            }
            //Get missing field
            if ( !empty($fieldIdsToCheck) )
            {
                $missingField = $this->checkMissingData($fieldIdsToCheck, $submissionId);
                if ($missingField)
                {
                    $this->deleteRedundantData($submissionId, $targetDir, $postData['form_id']);
                    $error = array();
                    $error['field_id'] = $postData['field_id'];
                    $error['message'] = 'Required';
                    $this->setError(json_encode($error));
                    return false;
                }
            }

            require_once JPATH_ROOT . '/administrator/components/com_uniform/helpers/email.php';

            $dataForEmail = array();
            $dataForEmail ['form_id'] 		= $postData['form_id'];
            $dataForEmail ['submission_id'] = $submissionId;

            $objJSNUniFormEmailHelper = new JSNUniFormEmailHelper;
            $objJSNUniFormEmailHelper->prepareDataForEmail($dataForEmail);
        }

        //Recheck count files
        if ($done == count($fileData))
        {
        	return true;
        }
        else
        {
            $this->deleteRedundantData($submissionId, $targetDir, $postData['form_id']);
            $error = array();
            $error['field_id'] 	= $postData['field_id'];
            $error['message'] 	= JTEXT::_("JSN_UNIFORM_ERROR_UNABLE_TO_UPLOAD_FILE");
            $this->setError(json_encode($error));
            return false;
        }
    }

    /**
     * @param $fieldIdsToCheck
     * @param $submissionId
     * @return bool
     */
    public function checkMissingData($fieldIdsToCheck, $submissionId)
    {
        $fields = implode(',', $fieldIdsToCheck);
        //Delete uploaded file
        $this->_db->setQuery($this->_db->getQuery(true)->select('*')->from("#__jsn_uniform_submission_data")->where('submission_id=' . intval($submissionId) .' AND field_id IN ('.$fields.')'));
        $fields = $this->_db->loadObjectList();

        if ($fields)
        {
            foreach ($fields AS $field)
            {
                if (empty($field->submission_data_value))
                {
                    return $field->field_id;
                }
            }
        }

		//Ok, all field have data
        return false;
    }

    public function deleteRedundantData($submissionId, $targetDir, $formId)
    {
        //Delete uploaded file
        $this->_db->setQuery($this->_db->getQuery(true)->select('*')->from("#__jsn_uniform_submission_data")->where('submission_id=' . intval($submissionId) .' AND field_type = "file-upload" AND submission_data_value != ""'));
        $fields = $this->_db->loadObjectList();


        if ($fields)
        {
            foreach ($fields AS $field)
            {
                $fileInfo = json_decode($field->submission_data_value);
                JFile::delete($targetDir . $fileInfo->link);
            }
        }

        //Delete submission and re-count submissions
        $this->_db->setQuery('DELETE FROM #__jsn_uniform_submissions where submission_id = ' . (int) $submissionId);

        if (!$this->_db->execute())
        {
            return false;
        }

        $this->_db->setQuery('DELETE FROM #__jsn_uniform_submission_data where submission_id = ' . (int) $submissionId);

        if (!$this->_db->execute())
        {
            return false;
        }

        // Update count submission in forms views
        $formTable 			= JTable::getInstance('JsnForm', 'JSNUniformTable');
        $countSubmission 	= $this->getCountSubmission($formId);
        $formTable->bind(array('form_id' => (int) $formId, 'form_submission_cout' => (int) $countSubmission));

        if (!$formTable->store())
        {
           // $formTable->getError();
            return false;
        }

        return true;
    }

    public function updateAfterUploadFile($submissionId, $key, $value)
    {
        $query = $this->_db->getQuery(true);
        $query->update($this->_db->quoteName("#__jsn_uniform_submission_data"));
        $query->set("submission_data_value = " . $this->_db->Quote($value));
        $query->where('submission_id = ' . $submissionId);
        $query->where('field_id = ' . $key);
        $this->_db->setQuery($query);
        return $this->_db->execute();
    }


    /**
     * Method get count submission record
     * @param $formId
     *
     * @return mixed
     */
    public function getCountSubmission($formId)
    {
        $this->_db->setQuery($this->_db->getQuery(true)->select('count(submission_id)')->from("#__jsn_uniform_submissions")->where("form_id=" . (int) $formId));
        $countSubmission = $this->_db->loadResult();
        return $countSubmission;
    }
}
