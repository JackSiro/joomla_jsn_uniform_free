<?php
/**
 * @version    default_print.php$
 * @package    JSNUNIFORM
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2015 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');
$dataFields = $this->_dataFields;
$submission = $this->_dataSubmission;
$input 		= JFactory::getApplication()->input;
$submission_id 	= $input->get('submission_id');
// Display messages
if (JFactory::getApplication()->input->getInt('ajax') != 1)
{
	echo $this->msgs;
}
?>
<style media="print" type="text/css">
	table.submission-page-content tbody > tr > th{
		background-color: #eeeeee;
	}
	.jsn-form-bar, .jsn-page-actions.btn-group{
		display: none !important;
	}
	.submission-page {
		padding-top: 20px;
	}

</style>
	<div id="submission-settings" class="jsn-page-settings jsn-bootstrap">
	<form action="<?php echo JRoute::_('index.php?option=com_uniform&view=submission'); ?>" method="post" name="adminForm" id="adminForm">
		<div class="row-fluid ">
			<div class="submission-data" style="position: relative;">
				<div class="jsn-section-content">
					<div class="jsn-form-bar">
						<?php
						$formType = isset($this->_infoForm->form_type) ? $this->_infoForm->form_type : 1;
						if ($formType == 2)
						{
							?>
							<div class="control-group ">
								<label class="control-label"><?php echo JText::_('JSN_UNIFORM_DATA_PRESENTATION'); ?>
									:</label>

								<div class="controls">
									<select class="jsn-input-fluid" data-value="<?php echo $formType; ?>" id="jform_form_type">
										<option value="1"><?php echo JText::_('JSN_UNIFORM_TYPE_SINGLE_PAGE'); ?></option>
										<option value="2"><?php echo JText::_('JSN_UNIFORM_TYPE_MULTIPLE_PAGES'); ?></option>
									</select>
								</div>
							</div>
							<?php
						}
						?>
						<div class="control-group pull-right">
							<div class="controls">
								<div id="pop-print" class="btn hidden-print">
									<a href="#" onclick="window.print();return false;"><span class="icon-print"></span><?php echo JText::_('JSN_UNIFORM_SUBMISSION_PRINT')?></a>
								</div>
							</div>
						</div>
					</div>
					<div class="submission-content">
						<div class="jsn-page-actions btn-group" style="display: block;">
							<button class="btn btn-icon prev-page hide" onclick="return false;" disabled="disabled">
								<i class="icon-arrow-left"></i></button>
							<button class="btn btn-icon next-page hide" onclick="return false;" disabled="disabled">
								<i class="icon-arrow-right"></i></button>
						</div>
						<?php
						foreach ($this->_formPages as $formPages)
						{
							$pageContent = json_decode($formPages->page_content);
							$submissionDetail = "";
							$submissionEdit = "";

							foreach ($pageContent as $fields)
							{
								$key = "sd_" . $fields->id;
								if (isset($fields->type) && $fields->type != 'static-content' && $fields->type != 'google-maps')
								{
									if (isset($submission->$key))
									{
										$label = $fields->label != '' ? $fields->label : $fields->identify;
										$submissionDetail .= '<tr style="background-color: #FEFEFE;border-bottom:1px solid #DDDDDD"><th style="width: 30%; font-weight: bold;border-left: 1px solid #DDDDDD;line-height: 20px;padding: 8px;text-align: left;vertical-align: top;background-color:#EEEEEE;">' . $label . ':</th><td style="border-left: 1px solid #DDDDDD;border-right: 1px solid #DDDDDD;line-height: 20px;padding: 8px;text-align: left;vertical-align: top;" id="' . $key . '">';
										$contentField       = "";
										$contentFieldEdit   = "";
										$contentFieldDetail = "";

										$contentField     = JSNUniformHelper::getDataField($fields->type, $submission, $key, $this->_item->form_id, false);
										$contentFieldEdit = $contentField;

										if ($fields->type == "email")
										{

											$contentFieldDetail = !empty($contentField) ? '<a href="mailto:' . htmlentities($contentField, ENT_QUOTES, "UTF-8") . '">' . htmlentities($contentField, ENT_QUOTES, "UTF-8") . '</a>' : "N/A";
										}
										else
										{
											$contentFieldDetail = $contentField;
										}


										if (isset($fields->type) && ($fields->type == "checkboxes"))
										{
											if ($fields->type == "checkboxes")
											{

												$contentFieldDetail = str_replace(",", "<br/>", $contentFieldDetail);
												$contentFieldDetail = str_replace("[", "", $contentFieldDetail);
												$contentFieldDetail = str_replace("]", "", $contentFieldDetail);
											}
										}

										$submissionDetail .= htmlentities($contentFieldDetail, ENT_QUOTES, "UTF-8") ? str_replace("\n", "<br/>", trim(htmlentities($contentFieldDetail, ENT_QUOTES, "UTF-8"))) : "N/A";
										if (isset($fields->type) && ($fields->type == "checkboxes" || $fields->type == "list" || $fields->type == "paragraph-text"))
										{
											if ($fields->type == "checkboxes" || $fields->type == "list")
											{
												$contentFieldEdit = str_replace("<br/>", "\n", $contentFieldEdit);
												$contentFieldEdit = str_replace("\n\n", "\n", $contentFieldEdit);
												$contentFieldEdit = htmlentities($contentFieldEdit, ENT_QUOTES | ENT_IGNORE, "UTF-8");
											}
										}
										else if (isset($fields->type) && $fields->type == "likert")
										{
											$likertData = json_decode($submission->$key);
											$settings   = json_decode($likertData->settings);
											$tdRows     = "<input type=\"hidden\" class='jsn-likert-settings' data-value='{$key}' name='submission[{$key}][likert][settings]' value='" . htmlentities(json_encode(array('rows' => $settings->rows, 'columns' => $settings->columns)), ENT_QUOTES, "UTF-8") . "' />";
											$tdColumns  = '';

											foreach ($settings->rows as $row)
											{
												$tdRows .= "<tr>";
												$tdRows .= '<td>' . $row->text . '</td>';
												foreach ($settings->columns as $column)
												{
													$checked = '';
													foreach ($likertData->values as $k => $val)
													{
														if (($k == md5($row->text) || $k == $row->text) && $val == $column->text)
														{
															$checked = 'checked="checked" ';
														}
													}
													$tdRows .= "<td class=\"text-center\"><input " . $checked . " type=\"radio\" data-value='" . htmlentities($row->text, ENT_QUOTES, "UTF-8") . "' name='submission[{$key}][likert][values][" . htmlentities($row->text, ENT_QUOTES, "UTF-8") . "]'  value='" . htmlentities($column->text, ENT_QUOTES, "UTF-8") . "' /></td>";
												}
												$tdRows .= "</tr>";
											}
											foreach ($settings->columns as $column)
											{
												$tdColumns .= '<th class="text-center">' . $column->text . '</th>';
											}

										}
										else if (isset($fields->type) && $fields->type == "file-upload")
										{
											$submissionEdit .= $contentFieldEdit;
										}
										else
										{
											$contentFieldEdit = htmlentities($contentFieldEdit, ENT_QUOTES, "UTF-8");

										}

										$submissionDetail .= '</td></tr>';
									}
									else if (isset($fields->type) && $fields->type == 'static-content' || $fields->type == 'google-maps')
									{
										if ($fields->type == 'static-content')
										{
											$submissionDetail .= '<dt>' . $fields->label . ':</dt><dd id="' . htmlentities($key, ENT_QUOTES, "UTF-8") . '">';
											$submissionDetail .= "<dd class='clearfix'>" . htmlentities($fields->options->value, ENT_QUOTES, "UTF-8") . "</dd>";
										}
										else if ($fields->type == 'google-maps')
										{

											$height           = isset($fields->options->height) ? $fields->options->height : "";
											$width            = isset($fields->options->width) ? $fields->options->width : "";
											$formatWidth      = isset($fields->options->formatWidth) ? $fields->options->formatWidth : "";
											$googleMaps       = isset($fields->options->googleMaps) ? $fields->options->googleMaps : "";
											$googleMapsMarKer = isset($fields->options->googleMapsMarKer) ? $fields->options->googleMapsMarKer : "";
											$submissionDetail .= "<dd class='clearfix'><div class=\"content-google-maps\" data-width='{$width}{$formatWidth}' data-height='{$height}' data-value='{$googleMaps}' data-marker='" . htmlentities($googleMapsMarKer, ENT_QUOTES, "UTF-8") . "'><div class=\"google_maps map rounded\"></div></div></dd>";
										}

									}
								}
							}
							?>
							<div class="submission-page" data-title="<?php echo $formPages->page_title; ?>" data-value="<?php echo $formPages->page_id; ?>">
								<div class="submission-page-header">
									<h3><?php echo $formPages->page_title; ?></h3>
								</div>
								<table class="submission-page-content" id="dl_<?php echo $formPages->page_id; ?>" style="width:100%;border-top: 1px solid #DDDDDD;">
									<?php  echo html_entity_decode($submissionDetail, ENT_QUOTES, 'UTF-8') ; ?>
								</table>
							</div>
							<?php
						}

						?>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="filter_form_id" id="filter_form_id" value="<?php echo $this->_item->form_id; ?>" />
		<input type="hidden" name="cid" id="cid" value="<?php echo $this->_item->submission_id; ?>" />
		<input type="hidden" name="action" id="action" value="" />
		<input type="hidden" name="option" value="com_uniform" /> <input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</form>