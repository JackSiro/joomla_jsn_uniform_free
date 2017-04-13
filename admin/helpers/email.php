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

// No direct
defined('_JEXEC') or die('Restricted access');

require_once JPATH_ROOT . '/administrator/components/com_uniform/uniform.defines.php';
require_once JPATH_ROOT . '/administrator/components/com_uniform/helpers/uniform.php';

class JSNUniFormEmailHelper
{
	/**
	 * Get all form fields of a form
	 * 
	 * @param int $formID	the form id
	 * 
	 * @return mixed  An array of data items on success, false on failure.
	 */
	public function getFormFields($formID)
	{
		$totalPage 	= (int) $this->totalPage($formID);
		$db			= JFactory::getDBO();
		$query 		= $db->getQuery(true);
		
		if ((int) $totalPage > 1)
		{
			$fieldOrderID = array('field_id');
			$query->clear();
			$query->select('*');
			$query->from($db->quoteName('#__jsn_uniform_form_pages'));
			$query->where($db->quoteName('form_id') . ' = ' . (int) $formID);
			$query->order("page_id ASC");
			$db->setQuery($query);
			$pageColumns = $db->loadObjectList();

			foreach($pageColumns as $pageColumn)
			{
				$pageContents = json_decode($pageColumn->page_content);
				foreach ( $pageContents as $pageContent)
				{
					$fieldOrderID[] = $pageContent->id;
				}
			}	

			$fieldOrderID = array_unique($fieldOrderID);
			$fieldOrderID = implode(',', $fieldOrderID);		

			$query->clear();
			$query->select('*');
			$query->from($db->quoteName('#__jsn_uniform_fields'));
			$query->where($db->quoteName('form_id') . ' = ' . (int) $formID);
			$query->order("FIELD(" . $fieldOrderID . ")");
			$db->setQuery($query);
			return $db->loadObjectList();
		}
		else 
		{
			$query->clear();
			$query->select('*');
			$query->from($db->quoteName('#__jsn_uniform_fields'));
			$query->where($db->quoteName('form_id') . ' = ' . (int) $formID);
			$query->order("field_ordering ASC");
			$db->setQuery($query);
			return $db->loadObjectList();
		}	
		
	}

	/**
	 * Get all submissions of a form
	 *
	 * @param int $formID	the form id
	 * @param int $submissionID	the submission id
	 *
	 * @return mixed  An array of data items on success, false on failure.
	 */
	public function getSubmissionData($formID, $submissionID)
	{
		$db			= JFactory::getDBO();
		$query 		= $db->getQuery(true);
		$query->clear();
		$query->select('*');
		$query->from($db->quoteName('#__jsn_uniform_submission_data'));
		$query->where($db->quoteName('form_id') . ' = ' . (int) $formID);
		$query->where($db->quoteName('submission_id') . ' = ' . (int) $submissionID);
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Get form data of a form
	 *
	 * @param int $formID	the form id
	 *
	 * @return mixed  An array of data item on success, false on failure.
	 */
	public function getForms($formID)
	{
		$db			= JFactory::getDBO();
		$query 		= $db->getQuery(true);
		$query->clear();
		$query->select('*');
		$query->from($db->quoteName('#__jsn_uniform_forms'));
		$query->where($db->quoteName('form_id') . ' = ' . (int) $formID);
		$db->setQuery($query);
		
		return $db->loadObject();		
	}

	/**
	 * Get email templates of a form
	 *
	 * @param int $formID	the form id
	 *
	 * @return mixed  An array of data items on success, false on failure.
	 */
	public function getEmailTemplates($formID)
	{
		$db	= JFactory::getDBO();
		$query 		= $db->getQuery(true);
		$query->clear();
		$query->select('*');
		$query->from($db->quoteName('#__jsn_uniform_templates'));
		$query->where($db->quoteName('form_id') . ' = ' . (int) $formID);
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Get email data of a form
	 *
	 * @param int $formID	the form id
	 *
	 * @return mixed  An array of data items on success, false on failure.
	 */
	public function getEmailData($formID)
	{
		$db	= JFactory::getDBO();
		$query 		= $db->getQuery(true);
		$query->clear();
		$query->select('*');
		$query->from($db->quoteName('#__jsn_uniform_emails'));
		$query->where($db->quoteName('form_id') . ' = ' . (int) $formID);
		$db->setQuery($query);
		return $db->loadObjectList();		
	}

	/**
	 * Prepare data for sending email
	 *
	 * @param array $post	the post data
	 *
	 * @return void
	 */
	public function prepareDataForEmail($post)
	{

		$fieldList				= array();
		$submissions			= array();
		$dataContentEmail 		= array();
		$nameFileByIndentifier	= array();
		$requiredField			= array();
		$fileAttach				= array();
		$recepientEmail			= array();
		
		$fields 				= $this->getFormFields($post['form_id']);	
		$dataForms				= $this->getForms($post['form_id']);
		
		if (count($fields))
		{
			foreach ($fields as $field)
			{
				$fieldList [$field->field_id] = $field;
			}
		}

		if (count($fieldList))
		{
			$submissionData = $this->getSubmissionData($post['form_id'], $post['submission_id']);
			
			if (!count($submissionData)) return false;
			
			if (count($submissionData))
			{
				foreach ($submissionData as $submission)
				{
					$submissions [$submission->field_id] = $submission;
				}
				
				foreach ($fieldList as $key => $field)
				{
					if (isset($submissions[$key]))
					{
						$keyField = $key;
						$tmpSubmission = $submissions[$key];
						
						$fieldSettings = isset($field->field_settings) ? json_decode($field->field_settings) : "";
						
						if ($field->field_type == 'recepient-email')
						{
							$recepientEmail [] = $tmpSubmission->submission_data_value;
						}
						
						if ($field->field_type == 'google-maps') continue;
						
						if ($field->field_type == 'static-content' || $field->field_type == 'recepient-email' || $field->field_type == 'identification-code')
						{
							if ($fieldSettings->options->showInNotificationEmail == 'No')
							{
								continue;
							}
						}

						$tmpSubmissions = new stdClass();
						$tmpSubmissions->$keyField = $tmpSubmission->submission_data_value;
						
						if ($field->field_type == 'checkboxes' || $field->field_type == 'list')
						{
							if ($tmpSubmission->submission_data_value != '')
							{

								$tmpSubmission->submission_data_value = str_replace('["', '', $tmpSubmission->submission_data_value);
								$tmpSubmission->submission_data_value = str_replace('"]', '', $tmpSubmission->submission_data_value);
								$tmpSubmission->submission_data_value = str_replace('","', ',', $tmpSubmission->submission_data_value);
								$tmpSubmission->submission_data_value = str_replace('[', '', $tmpSubmission->submission_data_value);
								$tmpSubmission->submission_data_value = str_replace(']', '', $tmpSubmission->submission_data_value);
								
								$tmpSubmission->submission_data_value = $this->simpleUnicodeDecode($tmpSubmission->submission_data_value);
								$tmpSubmission->submission_data_value = explode(',', $tmpSubmission->submission_data_value);
								
								
								$tmpSubmissions->$keyField = json_encode($tmpSubmission->submission_data_value);
						
							}
						}
						
						if ($field->field_type == "file-upload")
						{
							$fileAttach[$field->field_identifier] = JSNUniformHelper::getDataField($field->field_type, $tmpSubmissions, $field->field_id, $post['form_id'], false, false, 'fileAttach');
						}
						
						$nameFileByIndentifier[$field->field_identifier] = $field->field_title;
						
						if (isset($fieldSettings->options->required))
						{
							$requiredField [$field->field_identifier] = $fieldSettings->options->required;
						}
						
						$contentField = JSNUniformHelper::getDataField($field->field_type, $tmpSubmissions, $field->field_id, $post['form_id'], false, false, 'email');
						$dataContentEmail[$field->field_identifier] = $contentField ? str_replace("\n", "<br/>", trim($contentField)) : "<span>N/A</span>";
					}
					else
					{
						if ($field->field_type == 'static-content')
						{
							$keyField = $key;
							$fieldSettings = isset($field->field_settings) ? json_decode($field->field_settings) : "";
							
							if ($fieldSettings->options->showInNotificationEmail == 'No')
							{
								continue;
							}
							$nameFileByIndentifier[$field->field_identifier] = $field->field_title;
							
							$tmpSubmissions = new stdClass();
							$tmpSubmissions->$keyField = $fieldSettings->options->value;
							$contentField = JSNUniformHelper::getDataField($field->field_type, $tmpSubmissions, $field->field_id, $post['form_id'], false, false, 'email');
							$dataContentEmail[$field->field_identifier] = $contentField ? str_replace("\n", "<br/>", trim($contentField)) : "<span>N/A</span>";
						}
						
					}	
				}	
			}
		}

		$templateData 	= $this->getEmailTemplates($post['form_id']);
		$emailData 		= $this->getEmailData($post['form_id']);

		if (count($recepientEmail))
		{
			foreach ($recepientEmail as $key => $recepientEmailItem) 
			{
				$recepientEmailItem = json_decode($recepientEmailItem);
				if (count($recepientEmailItem))
				{
					foreach($recepientEmailItem as $recepient)
					{
						$recepient = explode('|', $recepient);
						$emailName = trim(($recepient[0]));
						$email = trim(end($recepient));
						$dataRecepient = (object) array('email_id'=> '', 'form_id'=>'', 'user_id'=>'0', 'email_name'=>$emailName, 'email_address'=>$email, 'email_state'=>'1');
		
						$emailData = array_merge($emailData, array($dataRecepient));
					}
				}
			}
		}

		$formSubmitter 	= isset($dataForms->form_submitter) ? json_decode($dataForms->form_submitter) : '';
		$defaultSubject = isset($dataForms->form_title) ? $dataForms->form_title : '';

		if (count($templateData))
		{
			foreach ( $templateData as $emailTemplate ) 
			{
				$emailTemplate->template_message = trim ( $emailTemplate->template_message );
				
				if (! empty ( $emailTemplate->template_message )) 
				{
					preg_match_all ( '/\{\$([^\}]+)\}/i', $emailTemplate->template_message, $matches, PREG_SET_ORDER );
					
					if (count ( $matches )) 
					{
						for($z = 0, $countz = count ( $matches ); $z < $countz; $z ++) 
						{
							$emailTemplate->template_message = str_replace ( $matches [$z] [0], @$dataContentEmail [$matches [$z] [1]], $emailTemplate->template_message );
						}
					}
				} 
				else 
				{
					$htmlMessage = array ();
					if ($dataContentEmail) 
					{
						$htmlMessage = $this->emailTemplateDefault ( $dataContentEmail, $nameFileByIndentifier, $requiredField );
					}
					
					$emailTemplate->template_message = $htmlMessage;
				}
				
				preg_match_all ( '/\{\$([^\}]+)\}/i', $emailTemplate->template_subject, $matchesSubject, PREG_SET_ORDER );
				preg_match_all ( '/\{\$([^\}]+)\}/i', $emailTemplate->template_from, $matchesFrom, PREG_SET_ORDER );
				preg_match_all('/\{\$([^\}]+)\}/i', $emailTemplate->template_from_name, $matchesFromName, PREG_SET_ORDER);
				preg_match_all ( '/\{\$([^\}]+)\}/i', $emailTemplate->template_reply_to, $matchesReplyTo, PREG_SET_ORDER );
				
				if (count ( $matchesSubject )) 
				{
					for($sub = 0, $countsub = count ( $matchesSubject ); $sub < $countsub; $sub ++) 
					{
						$emailTemplate->template_subject = str_replace ( $matchesSubject [$sub] [0], @$dataContentEmail [$matchesSubject [$sub] [1]], $emailTemplate->template_subject );
					}
				}
				
				$emailTemplate->template_subject = ! empty ( $emailTemplate->template_subject ) ? $emailTemplate->template_subject : $defaultSubject;
				if (count ( $matchesFrom )) 
				{
					for($fr = 0, $countfr = count ( $matchesFrom ); $fr < $countfr; $fr ++) 
					{
						$emailTemplate->template_from = str_replace ( $matchesFrom [$fr] [0], @$dataContentEmail [$matchesFrom [$fr] [1]], $emailTemplate->template_from );
					}
				}

				if (count($matchesFromName))
				{
					for ($frn = 0, $countfrn = count($matchesFromName); $frn < $countfrn; $frn++)
					{
						$emailTemplate->template_from_name = str_replace($matchesFromName[$frn][0], @$dataContentEmail[$matchesFromName[$frn][1]], $emailTemplate->template_from_name);
					}
				
				}
				
				if (count ( $matchesReplyTo )) 
				{
					for($repto = 0, $countrepto = count ( $matchesReplyTo ); $repto < $countrepto; $repto ++) 
					{
						$emailTemplate->template_reply_to = str_replace ( $matchesReplyTo [$repto] [0], @$dataContentEmail [$matchesReplyTo [$repto] [1]], $emailTemplate->template_reply_to );
					}
				}
				$emailTemplate->template_subject	 	= strip_tags ( $emailTemplate->template_subject );
				$emailTemplate->template_from 			= strip_tags ( $emailTemplate->template_from );
				$emailTemplate->template_from_name 		= strip_tags($emailTemplate->template_from_name);
				$emailTemplate->template_reply_to 		= strip_tags ( $emailTemplate->template_reply_to );
				
				if ($emailTemplate->template_notify_to == 0 && count ( $formSubmitter )) 
				{
					$checkEmailSubmitter = false;
					
					$listEmailSubmitter = array ();
					foreach ( $formSubmitter as $item ) 
					{
						if (! empty ( $item )) 
						{
							$emailSubmitter = new stdClass ();
							$emailSubmitter->email_address = isset ( $dataContentEmail [$item] ) ? $dataContentEmail [$item] : "";
							
							if (! empty ( $emailSubmitter->email_address )) 
							{
								$listEmailSubmitter [] = $emailSubmitter;
							}
						}
					}
					
					$sent = $this->sendEmail ( $emailTemplate, $listEmailSubmitter, $fileAttach );

				}
				
				if ($emailTemplate->template_notify_to == 1) 
				{
					$sent = $this->sendEmail ( $emailTemplate, $emailData, $fileAttach );
				}
			}			
		}	
	}
	
	/**
	 * get content email
	 *
	 * @param type      $emailContent         email content
	 * @param   String  $requiredField        required field
	 *
	 * @return string
	 */
	public function emailTemplateDefault($emailContent, $nameFileByIndentifier, $requiredField)
	{

		$i = 0;
		$messageHtml = '';
	
		if (class_exists('JSNConfigHelper'))
		{
			$objUniformConfig = JSNConfigHelper::get('com_uniform');
		}
		else
		{
			$objUniformConfig = new stdClass;
			$objUniformConfig->form_show_empty_value_field_in_email = 0;
		}
	
		foreach ($emailContent as $key => $value)
		{
			$i++;
			$value = !empty($value) ? $value : 'Null';
			$name = !empty($nameFileByIndentifier[$key]) ? $nameFileByIndentifier[$key] : ' ';
			$required = '';
			if (isset($requiredField[$key]) && $requiredField[$key] == 1)
			{
				$required = '<span style="  color: red;font-weight: bold; margin: 0 5px;">*</span>';
			}
			if ($i % 2 == 0)
			{
				if ((int) $objUniformConfig->form_show_empty_value_field_in_email == 0)
				{
					if (strtolower(strip_tags($value)) != 'n/a')
					{
						$messageHtml .= '<tr style="background-color: #FEFEFE;">';
						if ($name)
						{
							$messageHtml .= ' <td style="width: 30%; font-weight: bold;border-left: 1px solid #DDDDDD;line-height: 20px;padding: 8px;text-align: left;vertical-align: top;">' . $name . $required . '</td>';
						}
						$messageHtml .= '<td style="border-left: 1px solid #DDDDDD;line-height: 20px;padding: 8px;text-align: left;vertical-align: top;">' . $value . '</td></tr>';
					}
				}
				else
				{
					$messageHtml .= '<tr style="background-color: #FEFEFE;">';
					if ($name)
					{
						$messageHtml .= ' <td style="width: 30%; font-weight: bold;border-left: 1px solid #DDDDDD;line-height: 20px;padding: 8px;text-align: left;vertical-align: top;">' . $name . $required . '</td>';
					}
					$messageHtml .= '<td style="border-left: 1px solid #DDDDDD;line-height: 20px;padding: 8px;text-align: left;vertical-align: top;">' . $value . '</td></tr>';
				}
			}
			else
			{
				if ((int) $objUniformConfig->form_show_empty_value_field_in_email == 0)
				{
					if (strtolower(strip_tags($value)) != 'n/a')
					{
						$messageHtml .= '<tr style="background-color: #F6F6F6;">';
						if ($name)
						{
							$messageHtml .= ' <td style="width: 30%; font-weight: bold;border-left: 1px solid #DDDDDD;line-height: 20px;padding: 8px;text-align: left;vertical-align: top;">' . $name . $required . '</td>';
						}
						$messageHtml .= '<td style="border-left: 1px solid #DDDDDD;line-height: 20px;padding: 8px;text-align: left;vertical-align: top;">' . $value . '</td></tr>';
					}
				}
				else
				{
					$messageHtml .= '<tr style="background-color: #F6F6F6;">';
					if ($name)
					{
						$messageHtml .= ' <td style="width: 30%; font-weight: bold;border-left: 1px solid #DDDDDD;line-height: 20px;padding: 8px;text-align: left;vertical-align: top;">' . $name . $required . '</td>';
					}
					$messageHtml .= '<td style="border-left: 1px solid #DDDDDD;line-height: 20px;padding: 8px;text-align: left;vertical-align: top;">' . $value . '</td></tr>';
				}
			}
		}
		return '<table style="border-spacing: 0;width: 100%;-moz-border-bottom-colors: none;-moz-border-left-colors: none;-moz-border-right-colors: none; -moz-border-top-colors: none; border-collapse: separate; border-color: #DDDDDD #DDDDDD #DDDDDD -moz-use-text-color; border-image: none; border-radius: 4px 4px 4px 4px;  border-style: solid solid solid none;border-width: 1px 1px 1px 0;"><tbody>' . $messageHtml . '</tbody></table>';
	}

	/**
	 * Send mail
	 * 
	 * @param object 	$templateData	the template data
	 * @param array 	$emailList		the email list
	 * @param array 	$attachFile		the attact file
	 * 
	 * @return Ambigous <string, boolean, JException>|boolean
	 */
	public function sendEmail($templateData, $emailList, $attachFile = null)
	{
		jimport('joomla.mail.helper');
		$fromname = '';
		$regex = '/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,63})$/';

		if (!empty($emailList) && is_array($emailList) && count($emailList))
		{

			try 
			{
				$emailContent = $templateData->template_message;
				preg_match_all('#(<img.*?>)#', $emailContent, $results, PREG_SET_ORDER);
				if ( count($results))
				{
					for ($i = 0, $count = count($results); $i < $count; $i++)
					{
						$imageTag = $results[$i][1];
	
						preg_match_all('# src="([^"]+)"#', $imageTag, $imageSrc, PREG_SET_ORDER);
						if (count($imageSrc))
						{
							for ($j = 0, $subcount = count($imageSrc); $j < $subcount; $j++)
							{
								$imgTag = $imageSrc[$j][1];
	
								preg_match_all('/^(http|https)/', $imgTag, $imageUrl, PREG_SET_ORDER);
								if (!count($imageUrl))
								{
									$url     = JUri::root() . $imgTag;
									$emailContent = str_replace($imgTag, $url, $emailContent);
								}
							}
						}
					}
				}
				$mail 		= JFactory::getMailer();
				$app 		= JFactory::getApplication();
				$jconfig 	= JFactory::getConfig();
				
				$objUniformConfig 	= JSNConfigHelper::get('com_uniform');
				$mailfromDefault 	= (string) $objUniformConfig->form_set_mail_from_default;
				$mailfrom 			= $jconfig->get('mailfrom');
				
				if ($mailfromDefault != '' && JMailHelper::isEmailAddress($mailfromDefault))
				{
					$mailfrom = $mailfromDefault;
				}			

				if (empty($templateData->template_from_name))
				{
					if ($templateData->template_from != '')
					{
						$fromname = $templateData->template_from;
					}
					else
					{
						$fromname = $jconfig->get('fromname');
					}
				}
				else
				{
					$fromname = $templateData->template_from_name;
				}
				//$fromname = empty($templateData->template_from_name) ? $jconfig->get('fromname') : $templateData->template_from_name;
					
				if ($templateData->template_from != '' && JMailHelper::isEmailAddress($templateData->template_from))
				{
					$mailfrom = $templateData->template_from;
				}
				
				$subject = $templateData->template_subject;
				$body = $emailContent;
				$sent = "";
				// Prepare email body
				$body = stripslashes($body);
				$mail->setSender(array($mailfrom, $fromname));
				$mail->setSubject($subject);
				$mail->isHTML(true);
				$mail->Encoding = 'base64';
				$mail->setBody($body);
				
				if (!empty($templateData->template_reply_to))
				{
					$isEmailAddress = JMailHelper::isEmailAddress($templateData->template_reply_to);
					if ($isEmailAddress)
					{
						$mail->addReplyTo(array($templateData->template_reply_to));
					}
					
				}

				if (!empty($templateData->template_attach) && !empty($attachFile))
				{
					$attach = json_decode($templateData->template_attach);
					foreach ($attach as $file)
					{
						if (!empty($attachFile[$file]))
						{
							foreach ($attachFile[$file] as $f)
							{
								$mail->addAttachment($f);
							}
						}
					}
				}
	
				foreach ($emailList as $email)
				{
					$isEmailAddress = JMailHelper::isEmailAddress($email->email_address);
					if ($isEmailAddress)
					{
						$recipient[] = $email->email_address;
						unset($email->email_address);
					}
				}

				if (count($recipient))
				{
					$mail->addRecipient(array_unique($recipient));
					$sent = $mail->Send();
					return $sent;
				}
				
				return false;
			} 
			catch (Exception $e) 
			{
				return false;
			}				
		}
		
		return false;
	}
			
	/**
	 * Get total page of a form
	 * 
	 * @param int $formID	the form id
	 * 
	 * @return mixed  An array of data item on success, false on failure.
	 */
	public function totalPage($formID)
	{
		$db			= JFactory::getDBO();
		$query 		= $db->getQuery(true);
		$query->clear();
		$query->select('count(*)');
		$query->from($db->quoteName('#__jsn_uniform_form_pages'));
		$query->where($db->quoteName('form_id') . ' = ' . (int) $formID);
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	/**
	 * Unicode decodeee
	 * @param string $str	the converted string
	 * @return (string)
	 */
	public function simpleUnicodeDecode($str) 
	{
		$str=str_ireplace("\u0001","?",$str);
		$str=str_ireplace("\u0002","?",$str);
		$str=str_ireplace("\u0003","?",$str);
		$str=str_ireplace("\u0004","?",$str);
		$str=str_ireplace("\u0005","?",$str);
		$str=str_ireplace("\u0006","?",$str);
		$str=str_ireplace("\u0007","•",$str);
		$str=str_ireplace("\u0008","?",$str);
		$str=str_ireplace("\u0009","?",$str);
		$str=str_ireplace("\u000A","?",$str);
		$str=str_ireplace("\u000B","?",$str);
		$str=str_ireplace("\u000C","?",$str);
		$str=str_ireplace("\u000D","?",$str);
		$str=str_ireplace("\u000E","?",$str);
		$str=str_ireplace("\u000F","¤",$str);
		$str=str_ireplace("\u0010","?",$str);
		$str=str_ireplace("\u0011","?",$str);
		$str=str_ireplace("\u0012","?",$str);
		$str=str_ireplace("\u0013","?",$str);
		$str=str_ireplace("\u0014","¶",$str);
		$str=str_ireplace("\u0015","§",$str);
		$str=str_ireplace("\u0016","?",$str);
		$str=str_ireplace("\u0017","?",$str);
		$str=str_ireplace("\u0018","?",$str);
		$str=str_ireplace("\u0019","?",$str);
		$str=str_ireplace("\u001A","?",$str);
		$str=str_ireplace("\u001B","?",$str);
		$str=str_ireplace("\u001C","?",$str);
		$str=str_ireplace("\u001D","?",$str);
		$str=str_ireplace("\u001E","?",$str);
		$str=str_ireplace("\u001F","?",$str);
		$str=str_ireplace("\u0020"," ",$str);
		$str=str_ireplace("\u0021","!",$str);
		$str=str_ireplace("\u0022","\"",$str);
		$str=str_ireplace("\u0023","#",$str);
		$str=str_ireplace("\u0024","$",$str);
		$str=str_ireplace("\u0025","%",$str);
		$str=str_ireplace("\u0026","&",$str);
		$str=str_ireplace("\u0027","'",$str);
		$str=str_ireplace("\u0028","(",$str);
		$str=str_ireplace("\u0029",")",$str);
		$str=str_ireplace("\u002A","*",$str);
		$str=str_ireplace("\u002B","+",$str);
		$str=str_ireplace("\u002C",",",$str);
		$str=str_ireplace("\u002D","-",$str);
		$str=str_ireplace("\u002E",".",$str);
		$str=str_ireplace("\u2026","…",$str);
		$str=str_ireplace("\u002F","/",$str);
		$str=str_ireplace("\u0030","0",$str);
		$str=str_ireplace("\u0031","1",$str);
		$str=str_ireplace("\u0032","2",$str);
		$str=str_ireplace("\u0033","3",$str);
		$str=str_ireplace("\u0034","4",$str);
		$str=str_ireplace("\u0035","5",$str);
		$str=str_ireplace("\u0036","6",$str);
		$str=str_ireplace("\u0037","7",$str);
		$str=str_ireplace("\u0038","8",$str);
		$str=str_ireplace("\u0039","9",$str);
		$str=str_ireplace("\u003A",":",$str);
		$str=str_ireplace("\u003B",";",$str);
		$str=str_ireplace("\u003C","<",$str);
		$str=str_ireplace("\u003D","=",$str);
		$str=str_ireplace("\u003E",">",$str);
		$str=str_ireplace("\u2264","=",$str);
		$str=str_ireplace("\u2265","=",$str);
		$str=str_ireplace("\u003F","?",$str);
		$str=str_ireplace("\u0040","@",$str);
		$str=str_ireplace("\u0041","A",$str);
		$str=str_ireplace("\u0042","B",$str);
		$str=str_ireplace("\u0043","C",$str);
		$str=str_ireplace("\u0044","D",$str);
		$str=str_ireplace("\u0045","E",$str);
		$str=str_ireplace("\u0046","F",$str);
		$str=str_ireplace("\u0047","G",$str);
		$str=str_ireplace("\u0048","H",$str);
		$str=str_ireplace("\u0049","I",$str);
		$str=str_ireplace("\u004A","J",$str);
		$str=str_ireplace("\u004B","K",$str);
		$str=str_ireplace("\u004C","L",$str);
		$str=str_ireplace("\u004D","M",$str);
		$str=str_ireplace("\u004E","N",$str);
		$str=str_ireplace("\u004F","O",$str);
		$str=str_ireplace("\u0050","P",$str);
		$str=str_ireplace("\u0051","Q",$str);
		$str=str_ireplace("\u0052","R",$str);
		$str=str_ireplace("\u0053","S",$str);
		$str=str_ireplace("\u0054","T",$str);
		$str=str_ireplace("\u0055","\u",$str);
		$str=str_ireplace("\u0056","V",$str);
		$str=str_ireplace("\u0057","W",$str);
		$str=str_ireplace("\u0058","X",$str);
		$str=str_ireplace("\u0059","Y",$str);
		$str=str_ireplace("\u005A","Z",$str);
		$str=str_ireplace("\u005B","[",$str);
		$str=str_ireplace("\u005C","\\",$str);
		$str=str_ireplace("\u005D","]",$str);
		$str=str_ireplace("\u005E","^",$str);
		$str=str_ireplace("\u005F","_",$str);
		$str=str_ireplace("\u0060","`",$str);
		$str=str_ireplace("\u0061","a",$str);
		$str=str_ireplace("\u0062","b",$str);
		$str=str_ireplace("\u0063","c",$str);
		$str=str_ireplace("\u0064","d",$str);
		$str=str_ireplace("\u0065","e",$str);
		$str=str_ireplace("\u0066","f",$str);
		$str=str_ireplace("\u0067","g",$str);
		$str=str_ireplace("\u0068","h",$str);
		$str=str_ireplace("\u0069","i",$str);
		$str=str_ireplace("\u006A","j",$str);
		$str=str_ireplace("\u006B","k",$str);
		$str=str_ireplace("\u006C","l",$str);
		$str=str_ireplace("\u006D","m",$str);
		$str=str_ireplace("\u006E","n",$str);
		$str=str_ireplace("\u006F","o",$str);
		$str=str_ireplace("\u0070","p",$str);
		$str=str_ireplace("\u0071","q",$str);
		$str=str_ireplace("\u0072","r",$str);
		$str=str_ireplace("\u0073","s",$str);
		$str=str_ireplace("\u0074","t",$str);
		$str=str_ireplace("\u0075","\u",$str);
		$str=str_ireplace("\u0076","v",$str);
		$str=str_ireplace("\u0077","w",$str);
		$str=str_ireplace("\u0078","x",$str);
		$str=str_ireplace("\u0079","y",$str);
		$str=str_ireplace("\u007A","z",$str);
		$str=str_ireplace("\u007B","{",$str);
		$str=str_ireplace("\u007C","|",$str);
		$str=str_ireplace("\u007D","}",$str);
		$str=str_ireplace("\u02DC","˜",$str);
		$str=str_ireplace("\u007E","~",$str);
		$str=str_ireplace("\u007F","",$str);
		$str=str_ireplace("\u00A2","¢",$str);
		$str=str_ireplace("\u00A3","£",$str);
		$str=str_ireplace("\u00A4","¤",$str);
		$str=str_ireplace("\u20AC","€",$str);
		$str=str_ireplace("\u00A5","¥",$str);
		$str=str_ireplace("\u0026quot;","\"",$str);
		$str=str_ireplace("\u0026gt;",">",$str);
		$str=str_ireplace("\u0026lt;",">",$str);
		return $str;
	}	
}