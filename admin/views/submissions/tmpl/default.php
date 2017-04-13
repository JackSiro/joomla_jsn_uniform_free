<?php

/**
 * @version     $Id: default.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Submissions
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2016 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

// load tooltip behavior
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');

$arrayScriptField = "";
$arrayField = array();
$listOrder = $this->escape($this->_state->get('list.ordering'));
$listDirn = $this->escape($this->_state->get('list.direction'));
$listPositionField = $this->escape($this->_state->get('filter.position_field'));
$listViewField = $this->escape($this->_state->get('filter.list_view_field'));
$dateSubmission = $this->escape($this->_state->get('filter.date_submission'));
$formId = $this->_formId;
$formInfo = $this->_formInfo;
$paymentStatus = $this->_state->get('filter.filter_payment_status');
if ($formId)
{
	$fieldTitle = $this->_viewField['fields']['title'];
	$fieldType = $this->_viewField['fields']['type'];

	$fieldIdentifier = $this->_viewField['fields']['identifier'];
	$fieldSort = $this->_viewField['fields']['sort'];
	$styleClass = $this->_viewField['fields']['styleclass'];
	$listViewField = $this->_viewField['field_view'];

	if (!$listPositionField)
	{
		$listPositionField = implode(",", $fieldIdentifier);
	}

	$arrayScriptField = str_replace("&quot;", '"', $listViewField);
	$arrayField = explode(",", str_replace("&quot;", '', $listViewField));
}
?>
<div id="submissions-list" class="jsn-page-list">
<?php

// Display messages
if (JFactory::getApplication()->input->getInt('ajax') != 1)
{
	echo $this->msgs;
}

?>
<script>
	var field = [<?php echo (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($arrayScriptField) : $arrayScriptField; ?>];
</script>
<div class="jsn-bootstrap">
	<form action="<?php echo JRoute::_('index.php?option=com_uniform&view=submissions'); ?>" class="form-inline" method="post" id="adminForm" name="adminForm">
		<fieldset class="jsn-fieldset-filter">
			<?php
			if ($formId)
			{
				?>
				<div class="pull-left jsn-fieldset-search">
					<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
					<input type="text" class="input-medium" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->_state->get('filter.search')); ?>" title="<?php echo JText::_('JSN_UNIFORM_FORM_SEARCH_IN_TITLE'); ?>" />
					<input type="text" class="input-medium" placeholder="- <?php echo JText::_('JSN_UNIFORM_PLACEHOLDER_SELECT_DATE'); ?> -" name="filter_date_submission" id="filter_date_submission" value="<?php echo $this->escape($this->_state->get('filter.date_submission')); ?>" title="<?php echo JText::_('JSN_UNIFORM_FORM_SEARCH_IN_DATE_SUBMISSION'); ?>" />
					<button class="btn btn-icon" type="submit"><i class="icon-search"></i></button>
					<button class="btn btn-icon" type="button" onclick="document.id('filter_search').value = '';
				document.id('filter_date_submission').value = '';
				document.id('list_view_field').value = '';
				document.id('filter_position_field').value = '';
				this.form.submit();"><i class="icon-remove"></i>
					</button>
				</div>
				<?php
			}
			?>
		<div class="pull-right jsn-fieldset-select">
			<?php
			if ($formId)
			{

			    if (isset($formInfo->form_payment_type) && !empty($formInfo->form_payment_type))
                {
                    //Call payment plugin function to render combo box (filter transaction status)
                	$dispatcher = JEventDispatcher::getInstance();
                    JPluginHelper::importPlugin('uniform', (string) $formInfo->form_payment_type);
                    try
                    {
                        $renderPaymentStatusComboBox = $dispatcher->trigger('renderPaymentStatusComboBox', array($paymentStatus));

                        if (count($renderPaymentStatusComboBox))
                        {
                            echo $renderPaymentStatusComboBox[0];
                        }
                    }
                    catch(Exception $e)
                    {

                    }
                } ?>
				<select name="filter_form_id" onchange="this.form.submit()" class="inputbox" id="filter_form_id">
					<option value="">- <?php echo JText::_('JSN_UNIFORM_SELECT_FORMS'); ?> -</option>
					<?php echo JHtml::_('select.options', JSNUniformHelper::getOptionForms(), 'value', 'text', $formId); ?>
				</select>
				<button class="select-field btn btn-icon" onclick="return false;" title="<?php echo JText::_('JSN_UNIFORM_SELECT_FIELDS'); ?>" id="select_field">
					<i class="icon-list"></i>
				</button>
				<?php echo $this->limitBox; ?>
	    </div>
		<div id="submission-fields-list" class="jsn-bootstrap">
			<div class="popover bottom">
				<div class="arrow"></div>
				<h3 class="popover-title"><?php echo JText::_('JSN_UNIFORM_SELECT_FIELDS'); ?></h3>

				<div class="popover-content">
					<?php
					$itemsDisabled = "";
					$items = "";
					for ($i = 0; $i < count($fieldIdentifier); $i++)
					{
						$checked = "";
						if (in_array($fieldIdentifier[$i], $arrayField))
						{
							$checked = 'checked="checked"';
						}
						$items .= '<li class="' . $styleClass[$i] . ' jsn-item ui-state-default"><label class="checkbox"><input ' . $checked . ' type="checkbox" title="' . JText::_($fieldTitle[$i]) . '" name="field[]" value="' . $fieldIdentifier[$i] . '">' . JText::_($fieldTitle[$i]) . '</label></li>';
					}
					?>
					<ul class="jsn-items-list ui-sortable">
						<?php echo $items;?>
					</ul>
					<div class="form-actions">
						<input type="button" class="btn btn-primary" id="done" onclick="return false;" name="done" value="Done">
					</div>
				</div>
			</div>
				<?php
			}
			?>
		</div>
		</fieldset>
		<?php
		if ($formId)
		{
			?>
			<table class="table table-bordered table-striped jsn-table-centered">
				<thead>
				<tr>
					<th width="10">
						<input type="checkbox" name="toggle" value="" onclick="<?php echo 'Joomla.checkAll(this);'; ?>" />
					</th>
					<?php

					for ($i = 0; $i < count($fieldIdentifier); $i++)
					{
						if (in_array($fieldIdentifier[$i], $arrayField))
						{
//							$fieldname = $arrayField[$i];
//
//						    if ($fieldType[$fieldname] == 'likert')
//							{
//								$likertItemName = json_decode($this->_items[0]->$fieldname);
//								$likertItemNameRows = json_decode($likertItemName->settings);
//								$rows = $likertItemNameRows->rows;
//								foreach ( $rows  as $row)
//								{
//									echo "<th class='" . $fieldIdentifier[$i] . "'>" . JHtml::_('grid.sort', $row->text, '', $listDirn, $listOrder) . "</th>";
//								}
//
//							}
//							else
//							{
								echo "<th class='" . $fieldIdentifier[$i] . "'>" . JHtml::_('grid.sort', $fieldTitle[$i], $fieldSort[$i], $listDirn, $listOrder) . "</th>";
//							}

						}

					}
					?>
					<th width="30">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'sb.submission_id', $listDirn, $listOrder); ?>
					</th>
					<th width="30">

					</th>
				</tr>
				</thead>
				<tbody>
					<?php
					if ($this->_items)
					{
						foreach ($this->_items as $i => $item)
						{
							?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="jsn-column-select">
								<?php echo JHtml::_('grid.id', $i, $item->submission_id); ?>
							</td>
							<?php
							if (is_array($arrayField))
							{
								foreach ($arrayField as $j => $field)
								{
									$contentField = "";
									if (isset($fieldType[$field]))
									{
										$contentField = JSNUniformHelper::getDataField($fieldType[$field], $item, $field, $formId, true, true, 'list');

//										$contentFieldLikert = $contentField ? $contentField : "N/A";

										$contentField = trim($contentField) != '' ? str_replace("\n", "<br/>", trim(htmlentities(strip_tags($contentField), ENT_QUOTES, "UTF-8"))) : "N/A";

										$contentField = str_replace('&lt;br/&gt;', ' - ', $contentField);

//										if ($fieldType[$field] == 'likert')
//										{
//											$likertFields = explode('<br/>', $contentFieldLikert);
//											$likert = array();
//											foreach ($likertFields as $likertField)
//											{
//												$likertField = strip_tags($likertField);
//												$likertField = explode(':', $likertField);
//
//												$contentField = '<td><a href="' . JRoute::_('index.php?option=com_uniform&view=submission&submission_id=' . (int) $item->submission_id) . '">' . $likertField[1] . '</a> </td>';
//												$likert[] = $contentField;
//
//											}
//											$contentField = implode(' ', $likert);
//										}
										if ($j < 3)
										{
											$contentField = '<td><a href="' . JRoute::_('index.php?option=com_uniform&view=submission&submission_id=' . (int) $item->submission_id) . '">' . $contentField . '</a> </td>';


										}
										elseif ($field == 'submission_created_by' && !$item->$field)
										{
											$contentField = isset($item->$field) ? JSNUniformHelper::getUserNameById($item->$field) : "Guest";
											$contentField = "<td>{$contentField}</td>";
										}
										else
										{
											$contentField = "<td>{$contentField}</td>";
										}
										echo $contentField;
									}
								}
							}
							?>
							<td class="jsn-column-id">
								<?php echo $item->submission_id; ?>
							</td>
							<td>
								<a class="jsn-link-action" href="<?php echo JRoute::_('index.php?option=com_uniform&view=submission&submission_id=' . (int) $item->submission_id); ?>"><?php echo JText::_('JSN_UNIFORM_DETAIlS'); ?></a>
							</td>
						</tr>
							<?php
						}
					}
					else
					{
						?>
					<tr>
						<td style="text-align: center;" colspan="<?php echo count($arrayField) + 3; ?>">
							<span class="nodata"><?php echo JText::_('JSN_UNIFORM_NO_DATA'); ?></span>
						</td>
					</tr>
						<?php
					}
					?>
				</tbody>
				<tfoot>
				<tr>
					<td colspan="<?php echo count($arrayField) + 3; ?>">
						<?php
						echo $this->_pagination->getListFooter();
						?>
					</td>
				</tr>
				</tfoot>
			</table>
			<?php
		}
		else
		{
			?>
			<div class="jsn-bglabel jsn-section-content">
				<?php echo JText::_('JSN_UNIFORM_SUBMISSIONS_SELECT_FORM'); ?>
				<select name="filter_form_id" onchange="this.form.submit()" class="inputbox" id="filter_form_id">
					<option value="">- <?php echo JText::_('JSN_UNIFORM_SELECT_FORMS'); ?> -</option>
					<?php echo JHtml::_('select.options', JSNUniformHelper::getOptionForms(), 'value', 'text', $formId); ?>
				</select>
			</div>
			<?php
		}
		?>
		<div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<input type="hidden" name="old_form_id" value="<?php echo $formId; ?>" />
			<input type="hidden" name="list_view_field" id="list_view_field" value="<?php echo $listViewField; ?>" />
			<input type="hidden" name="filter_position_field" id="filter_position_field" value="<?php echo $listPositionField; ?>" />
			<input type="hidden" name="list_submission_export" id="list_submission_export" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
</div>
<?php
// Display footer
JSNHtmlGenerate::footer();


