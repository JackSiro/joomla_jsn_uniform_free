<?php

/**
 * @version     $Id: form.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Helpers
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2016 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
defined('_JEXEC') or die('Restricted access');

/**
 *  JSNUniform generate form helper
 *
 * @package     Joomla.Administrator
 * @subpackage  com_uniform
 * @since       1.6
 */
class JSNFormGenerateHelper
{

	/**
	 * Generate html code for a form which includes all the required fields
	 *
	 * @param   object  $dataGenrate     Data genrate
	 *
	 * @param   string  $layout          The layout genrate
	 *
	 * @param   object  $dataSumbission  Data submission
	 *
	 * @return void
	 */
	public static function generate($dataGenrate = null, $dataSumbission = null, $pageContainer = null, $paymentType = null)
	{
		$formElement = array();

		foreach ($dataGenrate as $data)
		{
			$fileType = preg_replace('/[^a-z]/i', "", $data->type);
			$method = "field{$fileType}";
			if (method_exists('JSNFormGenerateHelper', $method))
			{
				$formElement[$data->position][] = self::$method($data, $dataSumbission, $paymentType);
			}
		}
		$getContainer = json_decode($pageContainer);
		$columnOutput = '';
		foreach ($getContainer as $items)
		{
			if ($items)
			{
				$columnOutput .= "<div class='jsn-row-container row-fluid jsn-uf-row-container'>";

				foreach ($items as $item)
				{
					$columName = !empty($item->columnName) ? $item->columnName : "left";
					$columClass = !empty($item->columnClass) ? $item->columnClass : "span12";
					$dataColumn = isset($formElement[$columName]) ? $formElement[$columName] : array();
					$columnOutput .= "<div class=\"jsn-container-{$columName} {$columClass}\">";
					if (!empty($dataColumn))
					{
						$columnOutput .= implode("\n", $dataColumn);
					}
					$columnOutput .= "</div>";
				}
				$columnOutput .= "</div>";
			}
		}
		return $columnOutput;
	}

	/**
	 * Return span number based on bootstrap grid layout
	 *
	 * @param   string  $styles       Style Column
	 *
	 * @param   int     $columnCount  Count column
	 *
	 * @return array
	 */
	public static function getColumnSizes($styles, $columnCount)
	{
		$spans = explode('-', $styles);
		$spanCount = count($spans);

		if ($spanCount < $columnCount)
		{
			$spans = array_merge($spans, array_fill(0, $columnCount - $spanCount, 1));
		}
		elseif ($spanCount > $columnCount)
		{
			$spans = array_slice($spans, 0, $columnCount);
		}

		$spanSum = array_sum($spans);
		$ratio = 12 / $spanSum;

		foreach ($spans as $index => $span)
		{
			$spans[$index] = ceil($span * $ratio);
		}

		$spans[] = 12 - array_sum($spans);
		return $spans;
	}

	/**
	 * Generate html code for "Website" data field
	 *
	 * @param   object  $data            Data field
	 *
	 * @param   array   $dataSumbission  Data submission
	 *
	 * @return string
	 */
	public static function fieldWebsite($data, $dataSumbission)
	{

		$identify = !empty($data->identify) ? $data->identify : "";
		$hideField = !empty($data->options->hideField) ? 'hide' : '';
		$customClass = !empty($data->options->customClass) ? $data->options->customClass : "";
		$required = !empty($data->options->required) ? '<span class="required">*</span>' : '';
		$requiredWebsite = !empty($data->options->required) ? 'website-required' : '';
		$instruction = !empty($data->options->instruction) ? " <i original-title=\"" . htmlentities(JText::_($data->options->instruction), ENT_QUOTES, "UTF-8") . "\" class=\"icon-question-sign\"></i>" : '';
		$sizeInput = !empty($data->options->size) ? $data->options->size : '';
		$defaultValue = !empty($dataSumbission[$data->id]) ? $dataSumbission[$data->id] : '';
		$placeholder = !empty($data->options->value) ? JText::_($data->options->value) : "";
		$title = $data->label != 'Website' ? $data->label : 'JSN_UNIFORM_DEFAULT_LABEL_WEBSITE';
		$html = "<div class=\"control-group {$customClass} {$identify} {$hideField}\" data-id=\"{$data->id}\"><label  class=\"control-label\">" . htmlentities(JText::_($title), ENT_QUOTES, "UTF-8") . "{$required}{$instruction}</label><div class=\"controls\"><input class=\"website {$requiredWebsite} {$sizeInput}\" id=\"{$data->id}\" name=\"{$data->id}\" type=\"text\" value=\"{$defaultValue}\" placeholder=\"" . htmlentities($placeholder, ENT_QUOTES, "UTF-8") . "\" /></div></div>";
		return $html;
	}

	/**
	 * Generate html code for "SingleLineText" data field
	 *
	 * @param   object  $data            Data field
	 *
	 * @param   array   $dataSumbission  Data submission
	 *
	 * @return string
	 */
	public static function fieldSingleLineText($data, $dataSumbission)
	{

		$limitValue = "";
		$styleClassLimit = "";
		$identify = !empty($data->identify) ? $data->identify : "";
		if (isset($data->options->limitation) && $data->options->limitation == 1)
		{
			$josnLimit = json_encode(array('limitMin' => $data->options->limitMin, 'limitMax' => $data->options->limitMax, 'limitType' => $data->options->limitType));
			if (isset($data->options->limitMax) && isset($data->options->limitMin) && $data->options->limitMax >= $data->options->limitMin && $data->options->limitMax > 0 && $data->options->limitMin >= 0)
			{
				if ($data->options->limitMax != 0 && $data->options->limitType == 'Characters')
				{
					$limitValue = "data-limit='{$josnLimit}' maxlength=\"{$data->options->limitMax}\"";
				}
				else
				{
					$limitValue = "data-limit='{$josnLimit}'";
				}
				$styleClassLimit = "limit-required";
			}

		}
		$title = $data->label != 'Single Line Text' ? $data->label : 'JSN_UNIFORM_DEFAULT_LABEL_SINGLE_LINE_TEXT';
		$defaultValue = !empty($dataSumbission[$data->id]) ? $dataSumbission[$data->id] : '';
		$required = !empty($data->options->required) ? '<span class="required">*</span>' : '';
		$requiredBlank = !empty($data->options->required) ? 'blank-required' : '';
		$hideField = !empty($data->options->hideField) ? 'hide' : '';
		$customClass = !empty($data->options->customClass) ? $data->options->customClass : "";
		$instruction = !empty($data->options->instruction) ? " <i original-title=\"" . htmlentities(JText::_($data->options->instruction), ENT_QUOTES, "UTF-8") . "\" class=\"icon-question-sign\"></i>" : '';
		$sizeInput = !empty($data->options->size) ? $data->options->size : '';
		$placeholder = !empty($data->options->value) ? JText::_($data->options->value) : "";
		$html = "<div class=\"control-group {$customClass} {$identify} {$hideField}\" data-id=\"{$data->id}\"><label  class=\"control-label\">" . htmlentities(JText::_($title), ENT_QUOTES, "UTF-8") . "{$required}{$instruction}</label><div class=\"controls {$requiredBlank}\"><input {$limitValue} class=\"{$styleClassLimit} {$sizeInput}\" id=\"{$data->id}\" name=\"{$data->id}\" type=\"text\" value=\"" . htmlentities($defaultValue, ENT_QUOTES, "UTF-8") . "\" placeholder=\"" . htmlentities($placeholder, ENT_QUOTES, "UTF-8") . "\" /></div></div>";
		return $html;
	}

	/**
	 * Generate html code for "Phone" data field
	 *
	 * @param   object  $data            Data field
	 *
	 * @param   array   $dataSumbission  Data submission
	 *
	 * @return string
	 */
	public static function fieldDate($data, $dataSumbission)
	{

		$identify = !empty($data->identify) ? $data->identify : "";
		$hideField = !empty($data->options->hideField) ? 'hide' : '';
		$customClass = !empty($data->options->customClass) ? $data->options->customClass : "";
		$required = !empty($data->options->required) ? '<span class="required">*</span>' : '';
		$requiredBlank = !empty($data->options->required) ? 'group-blank-required' : '';
		$sizeInput = 'input-small';
		$valueDate = '';
		$valueDateRange = '';
		if (isset($dataSumbission['date'][$data->id]))
		{
			$valueDate = isset($dataSumbission['date'][$data->id]['date']) ? $dataSumbission['date'][$data->id]['date'] : "";
			$valueDateRange = isset($dataSumbission['date'][$data->id]['daterange']) ? $dataSumbission['date'][$data->id]['daterange'] : "";
		}
		$instruction = !empty($data->options->instruction) ? " <i original-title=\"" . htmlentities(JText::_($data->options->instruction), ENT_QUOTES, "UTF-8") . "\" class=\"icon-question-sign\"></i>" : '';
		$dateSettings = json_encode($data->options);
		$placeholder = !empty($data->options->dateValue) ? JText::_($data->options->dateValue) : "";
		$placeholderDateRange = !empty($data->options->dateValueRange) ? JText::_($data->options->dateValueRange) : "";
		if (isset($data->options->timeFormat) && $data->options->timeFormat == "1" && isset($data->options->dateFormat) && $data->options->dateFormat == "1")
		{
			$sizeInput = 'input-medium';
		}
		$title = $data->label != 'Date/Time' ? $data->label : 'JSN_UNIFORM_DEFAULT_LABEL_DATE';
		$html = "<div class=\"control-group {$customClass} {$identify} {$hideField}\" data-id=\"{$data->id}\">
					<label  class=\"control-label\">" . htmlentities(JText::_($title), ENT_QUOTES, "UTF-8") . "{$required}{$instruction}</label>
						<div class=\"controls {$requiredBlank}\">
							<div class=\"input-append jsn-inline\"><input data-jsnUf-date-settings='" . htmlentities($dateSettings, ENT_QUOTES, "UTF-8") . "' placeholder=\"" . htmlentities($placeholder, ENT_QUOTES, "UTF-8") . "\" value=\"" . $valueDate . "\" class=\"jsn-daterangepicker {$sizeInput}\" id=\"{$data->id}\" name=\"date[{$data->id}][date]\" type=\"text\" readonly /></div>
								";
		if ($data->options->enableRageSelection == "1" || $data->options->enableRageSelection == 1)
		{
			$html .= "<div class=\"input-append jsn-inline\"><input data-jsnUf-date-settings='" . htmlentities($dateSettings, ENT_QUOTES, "UTF-8") . "' placeholder=\"" . htmlentities($placeholderDateRange, ENT_QUOTES, "UTF-8") . "\" value=\"" . htmlentities($valueDateRange, ENT_QUOTES, "UTF-8") . "\" class=\"jsn-daterangepicker {$sizeInput}\" id=\"range_{$data->id}\" name=\"date[{$data->id}][daterange]\" type=\"text\" readonly /></div>";
		}
		$html .= "</div></div>";
		return $html;
	}

	/**
	 * Generate html code for "Currency" data field
	 *
	 * @param   object  $data            Data field
	 *
	 * @param   array   $dataSumbission  Data submission
	 *
	 * @return string
	 */
	public static function fieldCurrency($data, $dataSumbission)
	{

		$identify = !empty($data->identify) ? $data->identify : "";
		$hideField = !empty($data->options->hideField) ? 'hide' : '';
		$customClass = !empty($data->options->customClass) ? $data->options->customClass : "";
		$required = !empty($data->options->required) ? '<span class="required">*</span>' : '';
		$requiredBlank = !empty($data->options->required) ? 'group-blank-required' : '';
		//$sizeInput = !empty($data -> options -> size) ? $data -> options -> size : '';
		$defaultValue = "";
		$centsValue = "";
		if (isset($dataSumbission['currency'][$data->id]))
		{
			$defaultValue = isset($dataSumbission['currency'][$data->id]['value']) ? $dataSumbission['currency'][$data->id]['value'] : "";
			$centsValue = isset($dataSumbission['currency'][$data->id]['cents']) ? $dataSumbission['currency'][$data->id]['cents'] : "";
		}
		$options['Dollars'] = array('prefix' => '$', 'cents' => 'Cents');
		$options['Haht'] = array('prefix' => '฿', 'cents' => 'Satang');
		$options['Taiwan'] = array('prefix' => '$', 'cents' => 'Cents');
		$options['Francs'] = array('prefix' => 'CHF', 'cents' => 'Rappen');
		$options['Krona'] = array('prefix' => 'kr', 'cents' => 'Ore');
		$options['SGDollars'] = array('prefix' => '$', 'cents' => 'Cents');
		$options['Ruble'] = array('prefix' => 'руб', 'cents' => 'Kopek');
		$options['Pounds'] = array('prefix' => '£', 'cents' => 'Pence');
		$options['Grosze'] = array('prefix' => 'zł', 'cents' => 'Groszey');
		$options['NZD'] = array('prefix' => '$', 'cents' => 'Cents');
		$options['NOK'] = array('prefix' => 'kr', 'cents' => 'Ore');
		$options['Yen'] = array('prefix' => '¥', 'cents' => '');
		$options['Forint'] = array('prefix' => 'Ft', 'cents' => 'Filler');
		$options['HKD'] = array('prefix' => '$', 'cents' => 'Cents');
		$options['Euros'] = array('prefix' => '€', 'cents' => 'Cents');
		$options['DKK'] = array('prefix' => 'kr', 'cents' => 'Ore');
		$options['Koruna'] = array('prefix' => 'Kč', 'cents' => 'Haléřů');
		$options['CAD'] = array('prefix' => '$', 'cents' => 'Cents');
		$options['BRL'] = array('prefix' => 'R$', 'cents' => 'Centavos');
		$options['AUD'] = array('prefix' => '$', 'cents' => 'Cents');
		$options['Pesos'] = array('prefix' => '$', 'cents' => 'Centavos');
		$options['Ringgit'] = array('prefix' => 'RM', 'cents' => 'Sen');
		$options['Shekel'] = array('prefix' => '₪', 'cents' => 'Agora');
		$options['Zloty'] = array('prefix' => 'zł', 'cents' => 'Grosz');
		$options['Rupee'] = array('prefix' => '₹', 'cents' => '');

		//	$defaultValue = !empty($dataSumbission[$data -> id]) ? $dataSumbission[$data -> id] : '';
		$instruction = !empty($data->options->instruction) ? " <i original-title=\"" . htmlentities(JText::_($data->options->instruction), ENT_QUOTES, "UTF-8") . "\" class=\"icon-question-sign\"></i>" : '';
		$placeholder = !empty($data->options->value) ? JText::_($data->options->value) : "";
		$placeholderCents = !empty($data->options->cents) ? JText::_($data->options->cents) : "";
		$inputContent = "";
		$edition = defined('JSN_UNIFORM_EDITION') ? JSN_UNIFORM_EDITION : "pro";
		$paymentActive = '';
		if(strtolower($edition) != 'free')
		{
			$paymentActive = isset($data->options->paymentMoneyValue) && $data->options->paymentMoneyValue == "Yes" ? 'payment-active' : '';
		}
		if (isset($data->options->format))
		{
			$showHelpBlock = "";
			if (!empty($data->options->showCurrencyTitle) && $data->options->showCurrencyTitle == "Yes")
			{
				$showHelpBlock = "<span class=\"jsn-help-block-inline\">" . $data->options->format . "</span>";
			}

			$inputContent = "<div class=\"input-prepend jsn-inline currency-value\"><div class=\"controls-inner\"><span class=\"add-on\">" . $options[$data->options->format]['prefix'] . "</span><input name=\"currency[{$data->id}][value]\" type=\"text\" placeholder=\"" . htmlentities($placeholder, ENT_QUOTES, "UTF-8") . "\" class=\"input-medium currency {$paymentActive}\" value=\"{$defaultValue}\"></div>{$showHelpBlock}</div>";
			if ($data->options->format != "Yen" && $data->options->format != "Rupee")
			{
				$showHelpBlockSents = "";
				if (!empty($data->options->showCurrencyTitle) && $data->options->showCurrencyTitle == "Yes")
				{
					$showHelpBlockSents = "<span class=\"jsn-help-block-inline\">" . $options[$data->options->format]['cents'] . "</span>";
				}
				$inputContent .= "<div class=\"jsn-inline currency-cents\"><div class=\"controls-inner\"><input name=\"currency[{$data->id}][cents]\" type=\"text\" placeholder=\"{$placeholderCents}\" class=\"input-mini currency {$paymentActive}\" value=\"{$centsValue}\" maxlength=\"2\"></div>{$showHelpBlockSents}</div>";
			}
		}
		$title = $data->label != 'Currency' ? $data->label : 'JSN_UNIFORM_DEFAULT_LABEL_CURRENCY';
		$html = "<div class=\"control-group {$customClass} {$identify} {$hideField}\" data-id=\"{$data->id}\"><label  class=\"control-label\">" . htmlentities(JText::_($title), ENT_QUOTES, "UTF-8") . "{$required}{$instruction}</label><div class=\"controls {$requiredBlank} currency-control clearfix\"><div class=\"clearfix\">{$inputContent}</div></div></div>";
		return $html;
	}

	/**
	 * Generate html code for "Phone" data field
	 *
	 * @param   object  $data            Data field
	 *
	 * @param   array   $dataSumbission  Data submission
	 *
	 * @return string
	 */
	public static function fieldPhone($data, $dataSumbission)
	{

		$identify = !empty($data->identify) ? $data->identify : "";
		$title = $data->label != 'Phone' ? $data->label : 'JSN_UNIFORM_DEFAULT_LABEL_PHONE';
		$hideField = !empty($data->options->hideField) ? 'hide' : '';
		$customClass = !empty($data->options->customClass) ? $data->options->customClass : "";
		$required = !empty($data->options->required) ? '<span class="required">*</span>' : '';
		$requiredBlank = !empty($data->options->required) ? 'group-blank-required' : '';
		//$sizeInput = !empty($data -> options -> size) ? $data -> options -> size : '';
		$defaultValue = "";
		$oneValue = "";
		$twoValue = "";
		$threeValue = "";
		if (isset($dataSumbission['phone'][$data->id]))
		{
			$defaultValue = isset($dataSumbission['phone'][$data->id]['default']) ? $dataSumbission['phone'][$data->id]['default'] : "";
			$oneValue = isset($dataSumbission['phone'][$data->id]['one']) ? $dataSumbission['phone'][$data->id]['one'] : "";
			$twoValue = isset($dataSumbission['phone'][$data->id]['two']) ? $dataSumbission['phone'][$data->id]['two'] : "";
			$threeValue = isset($dataSumbission['phone'][$data->id]['three']) ? $dataSumbission['phone'][$data->id]['three'] : "";
		}

		//	$defaultValue = !empty($dataSumbission[$data -> id]) ? $dataSumbission[$data -> id] : '';
		$instruction = !empty($data->options->instruction) ? " <i original-title=\"" . htmlentities(JText::_($data->options->instruction), ENT_QUOTES, "UTF-8") . "\" class=\"icon-question-sign\"></i>" : '';
		$placeholder = !empty($data->options->value) ? JText::_($data->options->value) : "";
		$placeholderOneField = !empty($data->options->oneField) ? JText::_($data->options->oneField) : "";
		$placeholderTwoField = !empty($data->options->twoField) ? JText::_($data->options->twoField) : "";
		$placeholderThreeField = !empty($data->options->threeField) ? JText::_($data->options->threeField) : "";
		$inputContent = "<input class=\"phone jsn-input-medium-fluid\" id=\"{$data->id}\" name=\"phone[{$data->id}][default]\" type=\"text\" value=\"{$defaultValue}\" placeholder=\"" . htmlentities($placeholder, ENT_QUOTES, "UTF-8") . "\" />";
		if (isset($data->options->format) && $data->options->format == "3-field")
		{
			$inputContent = "<div class=\"jsn-inline\"><input id=\"one_{$data->id}\" name=\"phone[{$data->id}][one]\" value='" . htmlentities($oneValue, ENT_QUOTES, "UTF-8") . "' type=\"text\" placeholder=\"" . htmlentities($placeholderOneField, ENT_QUOTES, "UTF-8") . "\" class=\"phone jsn-input-mini-fluid\"></div>
							<span class=\"jsn-field-prefix\">-</span>
							<div class=\"jsn-inline\"><input id=\"two_{$data->id}\" name=\"phone[{$data->id}][two]\" value='" . htmlentities($twoValue, ENT_QUOTES, "UTF-8") . "' type=\"text\" placeholder=\"" . htmlentities($placeholderTwoField, ENT_QUOTES, "UTF-8") . "\" class=\"phone jsn-input-mini-fluid\"></div>
							<span class=\"jsn-field-prefix\">-</span>
							<div class=\"jsn-inline\"><input id=\"three_{$data->id}\" name=\"phone[{$data->id}][three]\" value='" . htmlentities($threeValue, ENT_QUOTES, "UTF-8") . "' type=\"text\" placeholder=\"" . htmlentities($placeholderThreeField, ENT_QUOTES, "UTF-8") . "\" class=\"phone jsn-input-mini-fluid\"></div>";
		}
		$html = "<div class=\"control-group {$customClass} {$identify} {$hideField}\" data-id=\"{$data->id}\"><label  class=\"control-label\">" . htmlentities(JText::_($title), ENT_QUOTES, "UTF-8") . "{$required}{$instruction}</label><div class=\"controls {$requiredBlank}\">{$inputContent}</div></div>";
		return $html;
	}

	/**
	 * Generate html code for "ParagraphText" data field
	 *
	 * @param   object  $data            Data field
	 *
	 * @param   array   $dataSumbission  Data submission
	 *
	 * @return string
	 */
	public static function fieldParagraphText($data, $dataSumbission)
	{

		$identify = !empty($data->identify) ? $data->identify : "";
		$title = $data->label != 'Paragraph Text' ? $data->label : 'JSN_UNIFORM_DEFAULT_LABEL_PARAGRAPH_TEXT';
		$hideField = !empty($data->options->hideField) ? 'hide' : '';
		$customClass = !empty($data->options->customClass) ? $data->options->customClass : "";
		$limitValue = "";
		$styleClassLimit = "";
		if (isset($data->options->limitation) && $data->options->limitation == 1)
		{
			$josnLimit = json_encode(array('limitMin' => $data->options->limitMin, 'limitMax' => $data->options->limitMax, 'limitType' => $data->options->limitType));
			if (isset($data->options->limitMax) && isset($data->options->limitMin) && $data->options->limitMax >= $data->options->limitMin && $data->options->limitMax > 0 && $data->options->limitMin >= 0)
			{
				if ($data->options->limitMax != 0 && $data->options->limitType == 'Characters')
				{
					$limitValue = "data-limit='{$josnLimit}' maxlength=\"{$data->options->limitMax}\"";
				}
				else
				{
					$limitValue = "data-limit='{$josnLimit}'";
				}
				$styleClassLimit = "limit-required";
			}
		}
		$sizeInput = !empty($data->options->size) ? $data->options->size : '';
		$defaultValue = !empty($dataSumbission[$data->id]) ? $dataSumbission[$data->id] : '';
		$required = !empty($data->options->required) ? '<span class="required">*</span>' : '';
		$requiredBlank = !empty($data->options->required) ? 'blank-required' : '';
		$rows = !empty($data->options->rows) && (int) $data->options->rows ? $data->options->rows : '10';
		$instruction = !empty($data->options->instruction) ? " <i original-title=\"" . htmlentities(JText::_($data->options->instruction), ENT_QUOTES, "UTF-8") . "\" class=\"icon-question-sign\"></i>" : '';
		$placeholder = !empty($data->options->value) ? JText::_($data->options->value) : "";
		$html = "<div class=\"control-group {$customClass} {$identify} {$hideField}\" data-id=\"{$data->id}\"><label  class=\"control-label\">" . htmlentities(JText::_($title), ENT_QUOTES, "UTF-8") . "{$required}{$instruction}</label><div class=\"controls {$requiredBlank}\"><textarea {$limitValue} rows=\"{$rows}\" class=\" {$styleClassLimit} {$sizeInput}\" id=\"{$data->id}\" name=\"{$data->id}\" placeholder=\"" . htmlentities($placeholder, ENT_QUOTES, "UTF-8") . "\">{$defaultValue}</textarea></div></div>";
		return $html;
	}

	/**
	 * Generate html code for "Number" data field
	 *
	 * @param   object  $data            Data field
	 *
	 * @param   array   $dataSumbission  Data submission
	 *
	 * @return string
	 */
	public static function fieldNumber($data, $dataSumbission)
	{

		$identify = !empty($data->identify) ? $data->identify : "";
		$title = $data->label != 'Number' ? $data->label : 'JSN_UNIFORM_DEFAULT_LABEL_NUMBER';
		$hideField = !empty($data->options->hideField) ? 'hide' : '';
		$customClass = !empty($data->options->customClass) ? $data->options->customClass : "";
		$limitValue = "";
		$styleClassLimit = "";
		if (isset($data->options->limitation) && $data->options->limitation == 1)
		{
			$data->options->limitType = isset($data->options->limitType) ? $data->options->limitType : 'Characters';
			$josnLimit = json_encode(array('limitMin' => $data->options->limitMin, 'limitMax' => $data->options->limitMax, 'limitType' => $data->options->limitType));
			if (isset($data->options->limitMax) && isset($data->options->limitMin) && $data->options->limitMax >= $data->options->limitMin && $data->options->limitMax > 0 && $data->options->limitMin >= 0)
			{
				$limitValue = "data-limit='{$josnLimit}'";
				$styleClassLimit = "number-limit-required";
			}
		}
		$sizeInput = !empty($data->options->size) ? $data->options->size : '';
		$defaultValue = "";
		$defaultValueDecimal = "";
		if ($dataSumbission)
		{
			$defaultValue = isset($dataSumbission['number'][$data->id]['value']) ? $dataSumbission['number'][$data->id]['value'] : "";
			$defaultValueDecimal = isset($dataSumbission['number'][$data->id]['decimal']) ? $dataSumbission['number'][$data->id]['decimal'] : "";
		}
		$required = !empty($data->options->required) ? '<span class="required">*</span>' : '';
		$requiredInteger = !empty($data->options->required) ? 'integer-required' : '';
		$instruction = !empty($data->options->instruction) ? " <i original-title=\"" . htmlentities(JText::_($data->options->instruction), ENT_QUOTES, "UTF-8") . "\" class=\"icon-question-sign\"></i>" : '';
		$placeholder = $data->options->value;
		$placeholderDecimal = !empty($data->options->decimal) ? $data->options->decimal : "";
		$showDecimal = "";
		$edition = defined('JSN_UNIFORM_EDITION') ? JSN_UNIFORM_EDITION : "pro";
		$paymentActive = '';
		if(strtolower($edition) != 'free')
		{
			$paymentActive = isset($data->options->paymentMoneyValue) && $data->options->paymentMoneyValue == "Yes" ? 'payment-active' : '';
		}
		if (empty($defaultValue) && $defaultValue == '')
		{
			$defaultValue = $placeholder;
		}

		if (!empty($data->options->showDecimal) && $data->options->showDecimal == "1")
		{
			$showDecimal = "<span class=\"jsn-field-prefix\">.</span><input {$limitValue} class=\"number input-mini number-decimal\" name=\"number[{$data->id}][decimal]\" type=\"number\" value=\"" . htmlentities($defaultValueDecimal, ENT_QUOTES, "UTF-8") . "\" placeholder=\"" . htmlentities($placeholderDecimal, ENT_QUOTES, "UTF-8") . "\" />";
		}
		$html = "<div class=\"control-group {$customClass} {$identify} {$hideField}\" data-id=\"{$data->id}\"><label  class=\"control-label\">" . htmlentities(JText::_($title), ENT_QUOTES, "UTF-8") . "{$required}{$instruction}</label><div class=\"controls\"><input {$limitValue} class=\"number {$requiredInteger} {$styleClassLimit} {$sizeInput} {$paymentActive}\" id=\"{$data->id}\" name=\"number[{$data->id}][value]\" type=\"number\" value=\"" . htmlentities($defaultValue, ENT_QUOTES, "UTF-8") . "\" placeholder=\"" . htmlentities($placeholder, ENT_QUOTES, "UTF-8") . "\" />{$showDecimal}</div></div>";
		return $html;
	}

	/**
	 * Generate html code for "Name" data field
	 *
	 * @param   object  $data            Data field
	 *
	 * @param   array   $dataSumbission  Data submission
	 *
	 * @return string
	 */
	public static function fieldName($data, $dataSumbission)
	{


		$identify = !empty($data->identify) ? $data->identify : "";
		$hideField = !empty($data->options->hideField) ? 'hide' : '';
		$customClass = !empty($data->options->customClass) ? $data->options->customClass : "";
		$required = !empty($data->options->required) ? '<span class="required">*</span>' : '';
		$requiredBlank = !empty($data->options->required) ? 'group-blank-required' : '';
		$instruction = !empty($data->options->instruction) ? " <i original-title=\"" . htmlentities(JText::_($data->options->instruction), ENT_QUOTES, "UTF-8") . "\" class=\"icon-question-sign\"></i>" : '';
		$listField = !empty($data->options->sortableField) ? json_decode($data->options->sortableField) : array("vtitle", "vfirst", "vmiddle", "vlast");
		$title = $data->label != 'Name' ? $data->label : 'JSN_UNIFORM_DEFAULT_LABEL_NAME';
		$html = "<div class=\"control-group {$customClass} {$identify} {$hideField}\" data-id=\"{$data->id}\"><label  class=\"control-label\">" . htmlentities(JText::_($title), ENT_QUOTES, "UTF-8") . "{$required}{$instruction}</label><div id=\"{$data->id}\" class=\"controls {$requiredBlank}\">";
		$valueFirstName = '';
		$valueLastName = '';
		$valueMiddle = '';

		if (!empty($dataSumbission))
		{
			$valueFirstName = isset($dataSumbission['name'][$data->id]['first']) ? $dataSumbission['name'][$data->id]['first'] : "";
			$valueLastName = isset($dataSumbission['name'][$data->id]['last']) ? $dataSumbission['name'][$data->id]['last'] : "";
			$valueTitle = isset($dataSumbission['name'][$data->id]['title']) ? $dataSumbission['name'][$data->id]['title'] : "";
			$valueMiddle = isset($dataSumbission['name'][$data->id]['suffix']) ? $dataSumbission['name'][$data->id]['suffix'] : "";
		}

		// Auto insert joomla user Name if check autoinsertname and logged in
		if (isset($data->options->autoInsertName) && $data->options->autoInsertName == 1)
		{
			$user = JFactory::getUser();
			if (!$user->guest)
			{
				$name = explode(' ', $user->name, 2);

				if (!empty($data->options->vfirst) && !empty($data->options->vlast))
				{
					$valueFirstName = $name[0];
					$valueLastName = end($name);
				}
				elseif(!empty($data->options->vfirst))
				{
					$valueFirstName = $user->name;
				}
				elseif(!empty($data->options->vlast))
				{
					$valueLastName = $user->name;
				}
				elseif(!empty($data->options->vmiddle))
				{
					$valueMiddle = $user->name;
				}
			}
		}

		$sizeInput = !empty($data->options->size) ? $data->options->size : '';
		foreach ($listField as $field)
		{
			switch($field)
			{
				case "vtitle":
					if (!empty($data->options->vtitle))
					{

						$html .= "<select class=\"jsn-input-fluid\" name=\"name[{$data->id}][title]\">";
						if (isset($data->options->items) && is_array($data->options->items))
						{
							foreach ($data->options->items as $option)
							{
								if (!empty($valueTitle))
								{
									if (isset($option->text) && $option->text == $valueTitle)
									{
										$selected = "selected='selected'";
									}
									else
									{
										$selected = "";
									}
								}
								else
								{
									if ($option->checked == 1 || $option->checked == 'true')
									{
										$selected = "selected='selected'";
									}
									else
									{
										$selected = "";
									}
								}
								$html .= "<option {$selected} value=\"{$option->text}\">{$option->text}</option>";
							}
						}
						$html .= "</select>&nbsp;&nbsp;";
					}
					break;
				case "vfirst":
					if (!empty($data->options->vfirst))
					{
						$html .= "<input type=\"text\" class=\"{$sizeInput}\" value='" . htmlentities($valueFirstName, ENT_QUOTES, "UTF-8") . "' name=\"name[{$data->id}][first]\" placeholder=\"" . htmlentities(JText::_("First"), ENT_QUOTES, "UTF-8") . "\" />&nbsp;&nbsp;";
					}
					break;
				case "vmiddle":
					if (!empty($data->options->vmiddle))
					{
						$html .= "<input name=\"name[{$data->id}][suffix]\" type=\"text\" value=\"" . htmlentities($valueMiddle, ENT_QUOTES, "UTF-8") . "\" class=\"{$sizeInput}\" placeholder=\"" . htmlentities(JText::_("Middle"), ENT_QUOTES, "UTF-8") . "\" />&nbsp;&nbsp;";
					}
					break;
				case "vlast":
					if (!empty($data->options->vlast))
					{
						$html .= "<input type=\"text\" class=\"{$sizeInput}\" value='" . htmlentities($valueLastName, ENT_QUOTES, "UTF-8") . "' name=\"name[{$data->id}][last]\" placeholder=\"" . htmlentities(JText::_("Last"), ENT_QUOTES, "UTF-8") . "\" />";
					}
					break;
			}
		}
		$html .= "</div></div>";
		return $html;
	}

	/**
	 * Generate html code for "FileUpload" data field
	 *
	 * @param   object  $data            Data field
	 *
	 * @param   array   $dataSumbission  Data submission
	 *
	 * @return string
	 */
	public static function fieldFileUpload($data, $dataSumbission)
	{

		$identify = !empty($data->identify) ? $data->identify : "";
		$title = $data->label != 'File Upload' ? $data->label : 'JSN_UNIFORM_DEFAULT_LABEL_FILE_UPLOAD';
		$hideField = !empty($data->options->hideField) ? 'hide' : '';
		$customClass = !empty($data->options->customClass) ? $data->options->customClass : "";
		$requiredBlank = !empty($data->options->required) ? 'blank-required' : '';
		$multiple = !empty($data->options->multiple) ? 'multiple' : '';
		$required = !empty($data->options->required) ? '<span class="required">*</span>' : '';
		$instruction = !empty($data->options->instruction) ? " <i original-title=\"" . htmlentities(JText::_($data->options->instruction), ENT_QUOTES, "UTF-8") . "\" class=\"icon-question-sign\"></i>" : '';
		$html = "<div class=\"control-group {$customClass} {$identify} {$hideField}\" data-id=\"{$data->id}\"><label  class=\"control-label\">" . htmlentities(JText::_($title), ENT_QUOTES, "UTF-8") . "{$required}{$instruction}</label><div class=\"controls {$requiredBlank}\"><input id=\"{$data->id}\" class=\"input-file\" name=\"{$data->id}[]\" {$multiple} type=\"file\" /></div></div>";
		return $html;
	}

	/**
	 * Generate html code for "Email" data field
	 *
	 * @param   object  $data            Data field
	 *
	 * @param   array   $dataSumbission  Data submission
	 *
	 * @return string
	 */
	public static function fieldEmail($data, $dataSumbission)
	{


		$identify = !empty($data->identify) ? $data->identify : "";
		$title = $data->label != 'Email' ? $data->label : 'JSN_UNIFORM_DEFAULT_LABEL_EMAIL';
		$hideField = !empty($data->options->hideField) ? 'hide' : '';
		$customClass = !empty($data->options->customClass) ? $data->options->customClass : "";
		$required = !empty($data->options->required) ? '<span class="required">*</span>' : '';
		$requiredEmail = !empty($data->options->required) ? 'email-required' : '';
		$instruction = !empty($data->options->instruction) ? " <i original-title=\"" . htmlentities(JText::_($data->options->instruction), ENT_QUOTES, "UTF-8") . "\" class=\"icon-question-sign\"></i>" : '';


		$defaultValue = !empty($dataSumbission[$data->id]) ? $dataSumbission[$data->id] : '';
		$defaultValueConfirm = !empty($dataSumbission[$data->id . "_confirm"]) ? $dataSumbission[$data->id . "_confirm"] : '';
		// Auto insert joomla user email if check autoinsertemail and logged in.
		if(isset($data->options->autoInsertEmail) && $data->options->autoInsertEmail == 1)
		{
			$user = JFactory::getUser();
			if (!$user->guest)
			{
				$defaultValue = $user->email;
			}
		}


		$sizeInput = !empty($data->options->size) ? $data->options->size : '';
		$placeholder = !empty($data->options->value) ? JText::_($data->options->value) : "";
		$placeholderConfirm = !empty($data->options->valueConfirm) ? JText::_($data->options->valueConfirm) : "";

		$html = "<div class=\"control-group {$customClass} {$identify} {$hideField}\" data-id=\"{$data->id}\"><label  class=\"control-label\">" . htmlentities(JText::_($title), ENT_QUOTES, "UTF-8") . "{$required}{$instruction}</label><div class=\"controls\">";
		$html .= "<div class=\"row-fluid\"><input class=\"email {$requiredEmail} {$sizeInput}\" id=\"{$data->id}\" name=\"{$data->id}\" type=\"text\" value=\"" . htmlentities($defaultValue, ENT_QUOTES, "UTF-8") . "\" placeholder=\"" . htmlentities($placeholder, ENT_QUOTES, "UTF-8") . "\" /></div>";
		if (!empty($data->options->requiredConfirm))
		{
			$html .= "<div class=\"row-fluid\"><input class=\"{$sizeInput} jsn-email-confirm\" id=\"{$data->id}_confirm\" name=\"{$data->id}_confirm\" type=\"text\" value=\"" . htmlentities($defaultValueConfirm, ENT_QUOTES, "UTF-8") . "\" placeholder=\"" . htmlentities($placeholderConfirm, ENT_QUOTES, "UTF-8") . "\" /></div>";
		}
		$html .= "</div></div>";
		return $html;
	}

	/**
	 * Generate html code for "DropDown" data field
	 *
	 * @param   object  $data            Data field
	 *
	 * @param   array   $dataSumbission  Data submission
	 *
	 * @return string
	 */
	public static function fieldDropdown($data, $dataSumbission, $paymentType)
	{
		JHtml::_('jquery.framework');
		JSNHtmlAsset::addStyle(JURI::base(true) . '/components/com_uniform/assets/js/libs/select2/select2.css');
		JSNHtmlAsset::addScript(JURI::base(true) . '/components/com_uniform/assets/js/libs/select2/select2.js');
		$identify = !empty($data->identify) ? $data->identify : "";
		$title = $data->label != 'Dropdown' ? $data->label : 'JSN_UNIFORM_DEFAULT_LABEL_DROPDOWN';
		$hideField = !empty($data->options->hideField) ? 'hide' : '';
		$customClass = !empty($data->options->customClass) ? $data->options->customClass : "";
		$required = !empty($data->options->required) ? '<span class="required">*</span>' : '';
		$randomDropdown = !empty($data->options->randomize) ? 'dropdown-randomize' : '';
		$instruction = !empty($data->options->instruction) ? " <i original-title=\"" . htmlentities(JText::_($data->options->instruction), ENT_QUOTES, "UTF-8") . "\" class=\"icon-question-sign\"></i>" : '';
		$defaultValue = !empty($dataSumbission[$data->id]) ? $dataSumbission[$data->id] : "";
		$sizeInput = !empty($data->options->size) ? $data->options->size : '';
		$dataSettings = !empty($data->options->itemAction) ? $data->options->itemAction : '';
		preg_match_all('/\[PRICE:(.*)\]/U', $dataSettings, $matches, PREG_SET_ORDER);
		preg_match_all('/\[QUANTITY:(.*)\]/U', $dataSettings, $matchesQuantity, PREG_SET_ORDER);
		$countMatchesQuantity = count($matchesQuantity);
		if (count($matches))
		{
			foreach ( $matches as $matche)
			{
				if (!$countMatchesQuantity)
				{
					if ($data->options->paymentMoneyValue == "Yes")
					{
						$dataSettings = str_replace($matche[0], '|' .$matche[1] . '|1', $dataSettings);
					}
					else
					{
						$dataSettings = str_replace($matche[0], '', $dataSettings);
					}
				}
				else
				{
					if ($data->options->paymentMoneyValue == "Yes")
					{
						$dataSettings = str_replace($matche[0], '|' . $matche[1], $dataSettings);
					}
					else
					{
						$dataSettings = str_replace($matche[0], '', $dataSettings);
					}
				}
			}
		}
		if ($countMatchesQuantity)
		{
			foreach ( $matchesQuantity as $matche)
			{
				if ($data->options->paymentMoneyValue == "Yes")
				{
					$dataSettings = str_replace($matche[0], '|' . $matche[1], $dataSettings);
				}
				else
				{
					$dataSettings = str_replace($matche[0], '', $dataSettings);
				}
			}
		}

		$requiredBlank = !empty($data->options->firstItemAsPlaceholder) && !empty($data->options->required) ? 'dropdown-required' : '';
		$edition = defined('JSN_UNIFORM_EDITION') ? JSN_UNIFORM_EDITION : "pro";
		$paymentActive = '';
		if(strtolower($edition) != 'free')
		{
			$paymentActive = isset($data->options->paymentMoneyValue) && $data->options->paymentMoneyValue == "Yes" ? 'payment-active' : '';
		}
		$html = "<div class='control-group {$customClass} {$identify} {$hideField}' data-settings='" . htmlentities($dataSettings, ENT_QUOTES, "UTF-8") . "' data-id='{$data->id}'><label  class='control-label'>" . htmlentities(JText::_($title), ENT_QUOTES, "UTF-8") . "{$required}{$instruction}</label><div class='controls {$requiredBlank}'><select id='{$data->id}' class='dropdown {$sizeInput} {$randomDropdown} {$paymentActive} jsn-uf-select2-dropdown' name='{$data->id}'>";

		if (isset($data->options->items) && is_array($data->options->items))
		{
			foreach ($data->options->items as $index => $option)
			{
				if (!empty($defaultValue))
				{
					if (isset($option->text) && $option->text == $defaultValue)
					{
						$selected = "selected='selected'";
					}
					else
					{
						$selected = "";
					}
				}
				else
				{
					if ($option->checked == 1 || $option->checked == 'true')
					{
						$selected = "selected='selected'";
					}
					else
					{
						$selected = "";
					}
				}
				$selectDefault = "";
				if ($selected)
				{
					$selectDefault = "data-selectdefault=\"true\"";
				}
				if (!empty($data->options->firstItemAsPlaceholder) && $index == 0)
				{
					$html .= "<option {$selected} {$selectDefault} value=\"\">" . htmlentities(JText::_($option->text), ENT_QUOTES, "UTF-8") . "</option>";
				}
				else
				{
					$value = $option->text;
					$label = $option->text;
					preg_match_all('/\[PRICE:(.*)\]/U', $option->text, $matchesPrice, PREG_SET_ORDER);
					$price = 0;
					if (count($matchesPrice))
					{
						$label = str_replace($matchesPrice[0][0], '', $label);
						$value = $label . '|' .$matchesPrice[0][1] . '|1';
						$price = $matchesPrice[0][1];
					}
					preg_match_all('/\[QUANTITY:(.*?)\]/U', $option->text, $matchesQty, PREG_SET_ORDER);
					$qty = 1;
					if (count($matchesQty))
					{
						$label = trim(str_replace($matchesQty[0][0], '', $label));
						$value = $label . ' |' .$matchesPrice[0][1] .' |'. $matchesQty[0][1];
						$qty = $matchesQty[0][1];
					}
					if(!empty($price))
					{
						if($paymentType != '' && JPluginHelper::isEnabled('uniform', (string) $paymentType) == true)
						{
							$dispatcher = JEventDispatcher::getInstance();
							JPluginHelper::importPlugin('uniform', (string) $paymentType);
							$itemPrice  = $dispatcher->trigger('displayCurrency', $price);
							$totalPrice = $price * $qty;
							$totalPrice = $dispatcher->trigger('displayCurrency', $totalPrice);
							if ($data->options->paymentMoneyValue == 'Yes')
							{
								if ($data->options->showPriceLabel == 'Yes')
								{
									$label = $label . ' ( ' . strip_tags($itemPrice[0]) . ' x ' . $qty . ' = ' . strip_tags($totalPrice[0]) . ' )';
								}
							}
						}
					}
					if (isset($data->options->paymentMoneyValue))
					{
						if ($data->options->paymentMoneyValue != 'Yes')
						{
							$value = $label;
						}
					}
					$jsnUfPrice = !empty($data->options->paymentMoneyValue) && $data->options->paymentMoneyValue == 'Yes' ? "data-jsnUfPrice=\"{$price}\"" : '';
					$jsnUfQty = !empty($data->options->paymentMoneyValue) && $data->options->paymentMoneyValue == 'Yes' ? "data-jsnUfQty=\"{$qty}\"" : '';
					$html .= "<option class=\"jsn-column-item\" {$selected} {$selectDefault} {$jsnUfPrice} {$jsnUfQty} value=\"" . htmlentities($value, ENT_QUOTES, "UTF-8") . "\">" . htmlentities($label, ENT_QUOTES, "UTF-8") . "</option>";
				}
			}
		}
		$textOthers = !empty($data->options->labelOthers) ? $data->options->labelOthers : "Others";
		if (!empty($data->options->allowOther))
		{
			$html .= "<option class=\"lbl-allowOther\" value=\"Others\">" . $textOthers . "</option>";
			$html .= "</select>";
			$html .= "<div class=\"jsn-column-item jsn-uniform-others\"><textarea class='jsn-dropdown-Others hide' name=\"fieldOthers[{$data->id}]\"  rows=\"3\"></textarea></div></div>";
		}
		else
		{
			$html .= "</select></div>";
		}
		$html .= "</div>";
		return $html;
	}

	/**
	 * Generate html code for "DropDown" data field
	 *
	 * @param   object  $data            Data field
	 *
	 * @param   array   $dataSumbission  Data submission
	 *
	 * @return string
	 */
	public static function fieldList($data, $dataSumbission, $paymentType)
	{

		$identify = !empty($data->identify) ? $data->identify : "";
		$title = $data->label != 'List' ? $data->label : 'JSN_UNIFORM_DEFAULT_LABEL_LIST';
		$hideField = !empty($data->options->hideField) ? 'hide' : '';
		$customClass = !empty($data->options->customClass) ? $data->options->customClass : "";
		$required = !empty($data->options->required) ? '<span class="required">*</span>' : '';
		$requiredBlank = !empty($data->options->required) ? 'list-required' : '';
		$randomList = !empty($data->options->randomize) ? 'list-randomize' : '';
		$instruction = !empty($data->options->instruction) ? " <i original-title=\"" . htmlentities(JText::_($data->options->instruction), ENT_QUOTES, "UTF-8") . "\" class=\"icon-question-sign\"></i>" : '';
		$defaultValue = !empty($dataSumbission[$data->id]) ? $dataSumbission[$data->id] : "";
		$sizeInput = !empty($data->options->size) ? $data->options->size : '';
		$multiple = !empty($data->options->multiple) ? "multiple" : "size='4'";
		$edition = defined('JSN_UNIFORM_EDITION') ? JSN_UNIFORM_EDITION : "pro";
		$paymentActive = '';
		if(strtolower($edition) != 'free')
		{
			$paymentActive = isset($data->options->paymentMoneyValue) && $data->options->paymentMoneyValue == "Yes" ? 'payment-active' : '';
		}
		$html = "<div class=\"control-group {$customClass} {$identify} {$hideField} \" data-id=\"{$data->id}\"><label  class=\"control-label\">" . htmlentities(JText::_($title), ENT_QUOTES, "UTF-8") . "{$required}{$instruction}</label><div class=\"controls {$requiredBlank}\"><select {$multiple} id=\"{$data->id}\" class=\"list {$sizeInput} {$randomList} {$paymentActive}\" name=\"{$data->id}[]\">";
		if (isset($data->options->items) && is_array($data->options->items))
		{
			foreach ($data->options->items as $option)
			{
				if (!empty($defaultValue))
				{
					if (isset($option->text) && $option->text == $defaultValue)
					{
						$selected = "selected='selected'";
					}
					else
					{
						$selected = "";
					}
				}
				else
				{
					if ($option->checked == 1 || $option->checked == 'true')
					{
						$selected = "selected='selected'";
					}
					else
					{
						$selected = "";
					}
				}
				$value = $option->text;
				$label = $option->text;
				preg_match_all('/\[PRICE:(.*)\]/U', $option->text, $matchesPrice, PREG_SET_ORDER);
				$price = 0;
				if (count($matchesPrice))
				{
					$label = str_replace($matchesPrice[0][0], '', $label);
					$value = $label . '|' .$matchesPrice[0][1] . '|1';
					$price = $matchesPrice[0][1];
				}
				preg_match_all('/\[QUANTITY:(.*?)\]/U', $option->text, $matchesQty, PREG_SET_ORDER);
				$qty = 1;
				if (count($matchesQty))
				{
					$label = trim(str_replace($matchesQty[0][0], '', $label));
					$value = $label . '|' .$matchesPrice[0][1] .'|'. $matchesQty[0][1];
					$qty = $matchesQty[0][1];
				}
				if(!empty($price))
				{
					if($paymentType != '' && JPluginHelper::isEnabled('uniform', (string) $paymentType) == true)
					{
						$dispatcher = JEventDispatcher::getInstance();
						JPluginHelper::importPlugin('uniform', (string) $paymentType);
						$itemPrice  = $dispatcher->trigger('displayCurrency', $price);
						$totalPrice = $price * $qty;
						$totalPrice = $dispatcher->trigger('displayCurrency', $totalPrice);
						if ($data->options->paymentMoneyValue == 'Yes')
						{
							if ($data->options->showPriceLabel == 'Yes')
							{
								$label = $label . ' ( ' . strip_tags($itemPrice[0]) . ' x ' . $qty . ' = ' . strip_tags($totalPrice[0]) . ' )';
							}
						}
					}
				}
				if (isset($data->options->paymentMoneyValue))
				{
					if ($data->options->paymentMoneyValue != 'Yes')
					{
						$value = $label;
					}
				}
				$jsnUfPrice = !empty($data->options->paymentMoneyValue) && $data->options->paymentMoneyValue == 'Yes' ? "data-jsnUfPrice=\"{$price}\"" : '';
				$jsnUfQty = !empty($data->options->paymentMoneyValue) && $data->options->paymentMoneyValue == 'Yes' ? "data-jsnUfQty=\"{$qty}\"" : '';
				$html .= "<option class=\"jsn-column-item\" {$selected} {$jsnUfPrice} {$jsnUfQty} value=\"" . $value . "\">" . htmlentities($label, ENT_QUOTES, "UTF-8") . "</option>";
			}
		}
		$html .= "</select></div></div>";
		return $html;
	}

	/**
	 * Generate html code for "Country" data field
	 *
	 * @param   object  $data            Data field
	 *
	 * @param   array   $dataSumbission  Data submission
	 *
	 * @return string
	 */
	public static function fieldCountry($data, $dataSumbission)
	{

		$identify = !empty($data->identify) ? $data->identify : "";
		$title = $data->label != 'Country' ? $data->label : 'JSN_UNIFORM_DEFAULT_LABEL_COUNTRY';
		$hideField = !empty($data->options->hideField) ? 'hide' : '';
		$customClass = !empty($data->options->customClass) ? $data->options->customClass : "";
		$required = !empty($data->options->required) ? '<span class="required">*</span>' : '';
		$requiredBlank = !empty($data->options->required) ? 'blank-required' : '';
		$instruction = !empty($data->options->instruction) ? " <i original-title=\"" . htmlentities(JText::_($data->options->instruction), ENT_QUOTES, "UTF-8") . "\" class=\"icon-question-sign\"></i>" : '';
		$defaultValue = !empty($dataSumbission[$data->id]) ? $dataSumbission[$data->id] : "";
		$sizeInput = !empty($data->options->size) ? $data->options->size : '';
		$html = "<div class=\"control-group {$customClass} {$identify} {$hideField} \" data-id=\"{$data->id}\"><label  class=\"control-label\">" . htmlentities(JText::_($title), ENT_QUOTES, "UTF-8") . "{$required}{$instruction}</label><div class=\"controls {$requiredBlank}\"><select id=\"{$data->id}\" class=\"{$sizeInput}\" name=\"{$data->id}\">";
		if (isset($data->options->items) && is_array($data->options->items))
		{
			foreach ($data->options->items as $option)
			{
				if (!empty($defaultValue))
				{
					if (isset($option->text) && $option->text == $defaultValue)
					{
						$selected = "selected='selected'";
					}
					else
					{
						$selected = "";
					}
				}
				else
				{
					if (isset($option->checked) && $option->checked == 1)
					{
						$selected = "selected='selected'";
					}
					else
					{
						$selected = "";
					}
				}
				$html .= "<option {$selected} value=\"" . htmlentities($option->text, ENT_QUOTES, "UTF-8") . "\">" . htmlentities(JText::_($option->text), ENT_QUOTES, "UTF-8") . "</option>";
			}
		}
		$html .= "</select></div></div>";
		return $html;
	}

	/**
	 * Generate html code for "Choices" data field
	 *
	 * @param   object  $data            Data field
	 *
	 * @param   array   $dataSumbission  Data submission
	 *
	 * @return string
	 */
	public static function fieldChoices($data, $dataSumbission, $paymentType)
	{

		$identify = !empty($data->identify) ? $data->identify : "";
		$title = $data->label != 'Multiple Choice' ? $data->label : 'JSN_UNIFORM_DEFAULT_LABEL_MULTIPLE_CHOICE';
		$hideField = !empty($data->options->hideField) ? 'hide' : '';
		$customClass = !empty($data->options->customClass) ? $data->options->customClass : "";
		$required = !empty($data->options->required) ? '<span class="required">*</span>' : '';
		$instruction = !empty($data->options->instruction) ? " <i original-title=\"" . htmlentities(JText::_($data->options->instruction), ENT_QUOTES, "UTF-8") . "\" class=\"icon-question-sign\"></i>" : '';
		$requiredChoices = !empty($data->options->required) ? 'choices-required' : '';
		$randomChoices = !empty($data->options->randomize) ? 'choices-randomize' : '';
		$dataSettings = !empty($data->options->itemAction) ? $data->options->itemAction : '';
		preg_match_all('/\[PRICE:(.*)\]/U', $dataSettings, $matches, PREG_SET_ORDER);
		preg_match_all('/\[QUANTITY:(.*)\]/U', $dataSettings, $matchesQuantity, PREG_SET_ORDER);
		if ($dataSettings != '')
		{
			$tmpDataSettings = json_decode($dataSettings, true);

			if (count($tmpDataSettings))
			{
				$refinedDataSettings = array();

				foreach($tmpDataSettings as $key => $tmpDataSetting)
				{
					$refinedDataSettings[strip_tags($key)] = $tmpDataSetting;
				}

				if (count($refinedDataSettings))
				{
					$refinedDataSettings = (object) $refinedDataSettings;
					$dataSettings = json_encode($refinedDataSettings);
				}
			}
		}
		$countMatchesQuantity = count($matchesQuantity);
		if (count($matches))
		{
			foreach ( $matches as $matche)
			{
				if (!$countMatchesQuantity)
				{
					if($data->options->paymentMoneyValue == "Yes")
					{
						$dataSettings = str_replace($matche[0], '|' . $matche[1] . '|1', $dataSettings);
					}
					else{
						$dataSettings = str_replace($matche[0], '', $dataSettings);
					}
				}
				else
				{
					if($data->options->paymentMoneyValue == "Yes")
					{
						$dataSettings = str_replace($matche[0], '|' . $matche[1], $dataSettings);
					}
					else
					{
						$dataSettings = str_replace($matche[0], '', $dataSettings);
					}
				}
			}
		}
		if ($countMatchesQuantity)
		{
			foreach ( $matchesQuantity as $matche)
			{
				if($data->options->paymentMoneyValue == "Yes")
				{
					$dataSettings = str_replace($matche[0], '|' . $matche[1], $dataSettings);
				}
				else
				{
					$dataSettings = str_replace($matche[0], '', $dataSettings);
				}
			}
		}
		$edition = defined('JSN_UNIFORM_EDITION') ? JSN_UNIFORM_EDITION : "pro";
		$paymentActive = '';
		if(strtolower($edition) != 'free')
		{
			$paymentActive = isset($data->options->paymentMoneyValue) && $data->options->paymentMoneyValue == "Yes" ? 'payment-active' : '';
		}
		$html = "<div class='control-group {$customClass} {$identify} {$hideField}' data-settings='" . htmlentities($dataSettings, ENT_QUOTES, "UTF-8") . "' data-id='{$data->id}'><label  class='control-label'>" . htmlentities(JText::_($title), ENT_QUOTES, "UTF-8") . "{$required}{$instruction}</label><div class='controls {$requiredChoices}'><div id='{$data->id}' class='choices jsn-columns-container {$data->options->layout} {$randomChoices} {$paymentActive}'>";

		$defaultValue = isset($dataSumbission[$data->id]) ? $dataSumbission[$data->id] : "";
		if (isset($data->options->items) && is_array($data->options->items))
		{
			foreach ($data->options->items as $i => $option)
			{
				if (!empty($defaultValue))
				{
					if (isset($option->text) && $option->text == $defaultValue)
					{
						$checked = "checked='checked'";
					}
					else
					{
						$checked = "";
					}
				}
				else
				{
					if (isset($option->checked) && $option->checked == "true")
					{
						$checked = "checked='checked'";
					}
					else
					{
						$checked = "";
					}
				}

				$value = $option->text;
				$label = $option->text;
				preg_match_all('/\[PRICE:(.*)\]/U', $option->text, $matchesPrice, PREG_SET_ORDER);
				$price = 0;
				if (count($matchesPrice))
				{
					$label = str_replace($matchesPrice[0][0], '', $label);
					$value = $label . '|' .$matchesPrice[0][1] . '|1';
					$price = $matchesPrice[0][1];
				}
				preg_match_all('/\[QUANTITY:(.*?)\]/U', $option->text, $matchesQty, PREG_SET_ORDER);
				$qty = 1;
				if (count($matchesQty))
				{
					$label = trim(str_replace($matchesQty[0][0], '', $label));
					$value = $label . ' |' .$matchesPrice[0][1] .' |'. $matchesQty[0][1];
					$qty = $matchesQty[0][1];
				}
				if(!empty($price))
				{
					if($paymentType != '' && JPluginHelper::isEnabled('uniform', (string) $paymentType) == true)
					{
						$dispatcher = JEventDispatcher::getInstance();
						JPluginHelper::importPlugin('uniform', (string) $paymentType);
						$itemPrice  = $dispatcher->trigger('displayCurrency', $price);
						$totalPrice = $price * $qty;
						$totalPrice = $dispatcher->trigger('displayCurrency', $totalPrice);
						if ($data->options->paymentMoneyValue == 'Yes')
						{
							if ($data->options->showPriceLabel == 'Yes')
							{
								$label = $label . ' ( ' . $itemPrice[0] . ' x ' . $qty . ' = ' . $totalPrice[0] . ' )';
							}
						}
					}
				}
				if (isset($data->options->paymentMoneyValue))
				{
					if ($data->options->paymentMoneyValue != 'Yes')
					{
						$value = $label;
					}
				}
				$jsnUfPrice = !empty($data->options->paymentMoneyValue) && $data->options->paymentMoneyValue == 'Yes' ? "data-jsnUfPrice=\"{$price}\"" : '';
				$jsnUfQty = !empty($data->options->paymentMoneyValue) && $data->options->paymentMoneyValue == 'Yes' ? "data-jsnUfQty=\"{$qty}\"" : '';
				$html .= "<div class=\"jsn-column-item\"><label class=\"radio\"><input {$checked} name=\"{$data->id}\" {$jsnUfPrice} {$jsnUfQty} value=\"" . strip_tags($value) . "\" type=\"radio\" />" . $label . "</label></div>";
			}
		}
		$textOthers = !empty($data->options->labelOthers) ? $data->options->labelOthers : "Others";
		if (!empty($data->options->allowOther))
		{
			$html .= "<div class=\"jsn-column-item jsn-uniform-others\"><label class=\"radio lbl-allowOther\"><input class=\"allowOther\" name=\"{$data->id}\" value=\"Others\" type=\"radio\" />" . htmlentities($textOthers, ENT_QUOTES, "UTF-8") . "</label>";
			$html .= "<textarea disabled=\"disabled\" class='jsn-value-Others' name=\"fieldOthers[{$data->id}]\" rows=\"3\"></textarea></div>";
		}
		$html .= "<div class=\"clearbreak\"></div></div></div></div>";

		return $html;
	}

	/**
	 * Generate html code for "Checkboxes" data field
	 *
	 * @param   object  $data            Data field
	 *
	 * @param   array   $dataSumbission  Data submission
	 *
	 * @return string
	 */
	public static function fieldCheckboxes($data, $dataSumbission, $paymentType)
	{
		$identify = !empty($data->identify) ? $data->identify : "";
		$title = $data->label != 'Checkboxes' ? $data->label : 'JSN_UNIFORM_DEFAULT_LABEL_CHECKBOXES';
		$hideField = !empty($data->options->hideField) ? 'hide' : '';
		$customClass = !empty($data->options->customClass) ? $data->options->customClass : "";
		$required = !empty($data->options->required) ? '<span class="required">*</span>' : '';
		$instruction = !empty($data->options->instruction) ? " <i original-title=\"" . htmlentities(JText::_($data->options->instruction), ENT_QUOTES, "UTF-8") . "\" class=\"icon-question-sign\"></i>" : '';
		$requiredCheckbox = !empty($data->options->required) ? 'checkbox-required' : '';
		$randomCheckbox = !empty($data->options->randomize) ? 'checkbox-randomize' : '';
		$dataSettings = !empty($data->options->itemAction) ? $data->options->itemAction : '';
		preg_match_all('/\[PRICE:(.*)\]/U', $dataSettings, $matches, PREG_SET_ORDER);
		preg_match_all('/\[QUANTITY:(.*)\]/U', $dataSettings, $matchesQuantity, PREG_SET_ORDER);

		if ($dataSettings != '')
		{
			$tmpDataSettings = json_decode($dataSettings, true);

			if (count($tmpDataSettings))
			{
				$refinedDataSettings = array();

				foreach($tmpDataSettings as $key => $tmpDataSetting)
				{
					$refinedDataSettings[strip_tags($key)] = $tmpDataSetting;
				}

				if (count($refinedDataSettings))
				{
					$refinedDataSettings = (object) $refinedDataSettings;
					$dataSettings = json_encode($refinedDataSettings);
				}
			}
		}
		$countMatchesQuantity = count($matchesQuantity);
		if (count($matches))
		{
			foreach ( $matches as $matche)
			{
				if (!$countMatchesQuantity)
				{
					if ($data->options->paymentMoneyValue == "Yes")
					{
						$dataSettings = str_replace($matche[0], '|' . $matche[1] . '|1', $dataSettings);
					}
					else
					{
						$dataSettings = str_replace($matche[0], '', $dataSettings);
					}
				}
				else
				{
					if ($data->options->paymentMoneyValue == "Yes")
					{
						$dataSettings = str_replace($matche[0], '|' .$matche[1], $dataSettings);
					}
					else
					{
						$dataSettings = str_replace($matche[0], '', $dataSettings);
					}
				}
			}
		}
		if ($countMatchesQuantity)
		{
			foreach ( $matchesQuantity as $matche)
			{
				if ($data->options->paymentMoneyValue == "Yes")
				{
					$dataSettings = str_replace($matche[0], '|' .$matche[1], $dataSettings);
				}
				else
				{
					$dataSettings = str_replace($matche[0], '', $dataSettings);
				}
			}
		}
		$edition = defined('JSN_UNIFORM_EDITION') ? JSN_UNIFORM_EDITION : "pro";
		$paymentActive = '';
		if(strtolower($edition) != 'free')
		{
			$paymentActive = isset($data->options->paymentMoneyValue) && $data->options->paymentMoneyValue == "Yes" ? 'payment-active' : '';
		}
		$limitChoises = '';
		if (isset($data->options->limitation) && $data->options->limitation == 1)
		{
			$limitChoises = 'data-limit="'. $data->options->limitMax .'"' ;

		}

		$html = "<div class='control-group {$customClass} {$identify} {$hideField}' data-settings='" . htmlentities($dataSettings, ENT_QUOTES, "UTF-8") . "' data-id='{$data->id}'><label class='control-label'>" . htmlentities(JText::_($title), ENT_QUOTES, "UTF-8") . "{$required}{$instruction}</label><div class='controls'><div id='{$data->id}' class='checkboxes jsn-columns-container {$data->options->layout} {$randomCheckbox} {$requiredCheckbox} {$paymentActive}' $limitChoises>";
		$defaultValue = isset($dataSumbission[$data->id]) ? $dataSumbission[$data->id] : "";
		if (isset($data->options->items) && is_array($data->options->items))
		{
			foreach ($data->options->items as $i => $option)
			{
				$checked = "";
				if (!empty($defaultValue))
				{
					if (isset($option->text) && in_array($option->text, $defaultValue))
					{
						$checked = "checked='checked'";
					}
				}
				else
				{
					if (isset($option->checked) && $option->checked == "true")
					{
						$checked = "checked='checked'";
					}
				}

				$value = $option->text;
				$label = $option->text;
				preg_match_all('/\[PRICE:(.*)\]/U', $option->text, $matchesPrice, PREG_SET_ORDER);
				$price = 0;
				if (count($matchesPrice))
				{
					$label = str_replace($matchesPrice[0][0], '', $label);
					$value = $label . '|' .$matchesPrice[0][1] . '|1';
					$price = $matchesPrice[0][1];
				}
				preg_match_all('/\[QUANTITY:(.*?)\]/U', $option->text, $matchesQty, PREG_SET_ORDER);
				$qty = 1;
				if (count($matchesQty))
				{
					$label = trim(str_replace($matchesQty[0][0], '', $label));
					$value = $label . ' |' .$matchesPrice[0][1] .' |'. $matchesQty[0][1];
					$qty = $matchesQty[0][1];
				}
				if(!empty($price))
				{
					if($paymentType != '' && JPluginHelper::isEnabled('uniform', (string) $paymentType) == true)
					{
						$dispatcher = JEventDispatcher::getInstance();
						JPluginHelper::importPlugin('uniform', (string) $paymentType);
						$itemPrice  = $dispatcher->trigger('displayCurrency', $price);
						$totalPrice = $price * $qty;
						$totalPrice = $dispatcher->trigger('displayCurrency', $totalPrice);
						if ($data->options->paymentMoneyValue == 'Yes')
						{
							if ($data->options->showPriceLabel == 'Yes')
							{
								$label = $label . ' ( ' . $itemPrice[0] . ' x ' . $qty . ' = ' . $totalPrice[0] . ' )';
							}
						}
					}
				}
				if (isset($data->options->paymentMoneyValue))
				{
					if ($data->options->paymentMoneyValue != 'Yes')
					{
						$value = $label;
					}
				}
				$jsnUfPrice = !empty($data->options->paymentMoneyValue) && $data->options->paymentMoneyValue == 'Yes' ? "data-jsnUfPrice=\"{$price}\"" : '';
				$jsnUfQty = !empty($data->options->paymentMoneyValue) && $data->options->paymentMoneyValue == 'Yes' ? "data-jsnUfQty=\"{$qty}\"" : '';
				$html .= "<div class=\"jsn-column-item\"><label class=\"checkbox\"><input {$checked} name=\"{$data->id}[]\" {$jsnUfPrice} {$jsnUfQty} value=\"" . strip_tags($value) . "\" type=\"checkbox\" />" . $label . "</label></div>";
			}
		}
		$textOthers = !empty($data->options->labelOthers) ? $data->options->labelOthers : "Others";
		if (!empty($data->options->allowOther))
		{
			$html .= "<div class=\"jsn-column-item jsn-uniform-others\"><label class=\"checkbox lbl-allowOther\"><input class=\"allowOther\" value=\"Others\" type=\"checkbox\" />" . htmlentities($textOthers, ENT_QUOTES, "UTF-8") . "</label>";
			$html .= "<textarea disabled=\"disabled\" class='jsn-value-Others' name=\"{$data->id}[]\"  rows=\"3\"></textarea></div>";
		}


		$html .= "<div class=\"clearbreak\"></div></div></div></div>";

		return $html;
	}

	/**
	 * Generate html code for "Address" data field
	 *
	 * @param   object  $data            Data field
	 *
	 * @param   array   $dataSumbission  Data submission
	 *
	 * @return string
	 */
	public static function fieldAddress($data, $dataSumbission)
	{
		$valueStreet = '';
		$valueLine2 = '';
		$valueCity = '';
		$valueCode = '';
		$valueState = '';
		$valueCountry = '';
		if (!empty($dataSumbission))
		{
			$valueStreet = isset($dataSumbission['address'][$data->id]['street']) ? $dataSumbission['address'][$data->id]['street'] : "";
			$valueLine2 = isset($dataSumbission['address'][$data->id]['line2']) ? $dataSumbission['address'][$data->id]['line2'] : "";
			$valueCity = isset($dataSumbission['address'][$data->id]['city']) ? $dataSumbission['address'][$data->id]['city'] : "";
			$valueCode = isset($dataSumbission['address'][$data->id]['code']) ? $dataSumbission['address'][$data->id]['code'] : "";
			$valueState = isset($dataSumbission['address'][$data->id]['state']) ? $dataSumbission['address'][$data->id]['state'] : "";
			$valueCountry = isset($dataSumbission['address'][$data->id]['country']) ? $dataSumbission['address'][$data->id]['country'] : "";
		}

		$identify = !empty($data->identify) ? $data->identify : "";
		$title = $data->label != 'Address' ? $data->label : 'JSN_UNIFORM_DEFAULT_LABEL_ADDRESS';
		$hideField = !empty($data->options->hideField) ? 'hide' : '';
		$customClass = !empty($data->options->customClass) ? $data->options->customClass : "";
		$requiredBlank = !empty($data->options->required) ? 'group-blank-required' : '';
		$required = !empty($data->options->required) ? '<span class="required">*</span>' : '';
		$instruction = !empty($data->options->instruction) ? " <i original-title=\"" . htmlentities(JText::_($data->options->instruction), ENT_QUOTES, "UTF-8") . "\" class=\"icon-question-sign\"></i>" : '';
		$listField = !empty($data->options->sortableField) ? json_decode($data->options->sortableField) : array("vstreetAddress", "vstreetAddress2", "vcity", "vstate", "vcode", "vcountry");

		$html = "<div class=\"control-group {$customClass} jsn-group-field {$identify} {$hideField} \" data-id=\"{$data->id}\"><label  class=\"control-label\">" . htmlentities(JText::_($title), ENT_QUOTES, "UTF-8") . "{$required}{$instruction}</label><div id=\"{$data->id}\" class=\"controls {$requiredBlank}\">";

		$position2 = array_search('vstreetAddress2', $listField);
		$position1 = array_search('vstreetAddress', $listField);
		if ($position1 > $position2)
		{
			$position2 = array_search('vstreetAddress', $listField);
			$position1 = array_search('vstreetAddress2', $listField);
		}
		$sortableField = array();
		$field = array();
		foreach ($listField as $i => $val)
		{
			if (isset($data->options->$val) && $data->options->$val == 1)
			{
				$sortableField[] = $val;
			}
			switch($val)
			{
				case "vstreetAddress":
					$field[$val] = "<input type=\"text\" value='" . htmlentities($valueStreet, ENT_QUOTES, "UTF-8") . "' name=\"address[{$data->id}][street]\" placeholder=\"" . htmlentities(JText::_("Street_Address"), ENT_QUOTES, "UTF-8") . "\" class=\"jsn-input-xxlarge-fluid\" />";
					break;
				case "vstreetAddress2":
					$field[$val] = "<input type=\"text\" value='" . htmlentities($valueLine2, ENT_QUOTES, "UTF-8") . "' name=\"address[{$data->id}][line2]\" placeholder=\"" . htmlentities(JText::_("Address_Line_2"), ENT_QUOTES, "UTF-8") . "\" class=\"jsn-input-xxlarge-fluid\" />";
					break;
				case "vcity":
					$field[$val] = "<input value='" . htmlentities($valueCity, ENT_QUOTES, "UTF-8") . "' type=\"text\" name=\"address[{$data->id}][city]\" class=\"jsn-input-xlarge-fluid\" placeholder=\"" . htmlentities(JText::_("City"), ENT_QUOTES, "UTF-8") . "\" />";
					break;
				case "vstate":
					$field[$val] = "<input value='" . htmlentities($valueState, ENT_QUOTES, "UTF-8") . "'  name=\"address[{$data->id}][state]\" type=\"text\" placeholder=\"" . htmlentities(JText::_("State_Province_Region"), ENT_QUOTES, "UTF-8") . "\" class=\"jsn-input-xlarge-fluid\" />";
					break;
				case "vcode":
					$field[$val] = "<input value='" . htmlentities($valueCode, ENT_QUOTES, "UTF-8") . "'  type=\"text\" name=\"address[{$data->id}][code]\" class=\"jsn-input-xlarge-fluid\" placeholder=\"" . htmlentities(JText::_("Postal_Zip_code"), ENT_QUOTES, "UTF-8") . "\" />";
					break;
				case "vcountry":
					$field[$val] = "<select class=\"jsn-input-xlarge-fluid\" name=\"address[{$data->id}][country]\">";
					if (isset($data->options->country) && is_array($data->options->country))
					{
						foreach ($data->options->country as $option)
						{
							if (!empty($valueCountry))
							{
								if (isset($option->text) && $option->text == $valueCountry)
								{
									$selected = "selected='selected'";
								}
								else
								{
									$selected = "";
								}
							}
							else
							{
								if (isset($option->checked) && $option->checked == 1)
								{
									$selected = "selected='selected'";
								}
								else
								{
									$selected = "";
								}
							}
							$field[$val] .= "<option {$selected} value=\"" . htmlentities($option->text, ENT_QUOTES, "UTF-8") . "\">" . htmlentities(JText::_($option->text), ENT_QUOTES, "UTF-8") . "</option>";
						}
					}
					$field[$val] .= "</select>";
					break;
			}
		}
		if ($position1 > 0)
		{
			$check = 0;
			for ($i = 0; $i < $position1; $i++)
			{
				if ($check % 2 == 0)
				{
					$html .= '<div class="row-fluid">';
				}
				$html .= "<div class='span6'>" . $field[$sortableField[$i]] . "</div>";
				if ($check % 2 != 0 || $i == $position1 - 1)
				{
					$html .= "</div>\n";
				}
				$check++;
			}
		}
		$html .= "<div class='row-fluid'><div class='span12'>" . $field[$sortableField[$position1]] . "</div></div>\n";
		$check = 0;
		for ($i = $position1 + 1; $i < $position2; $i++)
		{
			if ($check % 2 == 0)
			{
				$html .= '<div class="row-fluid">';
			}
			$html .= "<div class='span6'>" . $field[$sortableField[$i]] . "</div>";
			if ($check % 2 != 0 || $i == $position2 - 1)
			{
				$html .= "</div>\n";
			}
			$check++;
		}

		if($sortableField[$position2] == 'vstreetAddress2')
		{
			$html .= "<div class='row-fluid'><div class='span12'>" . $field[$sortableField[$position2]] . "</div></div>\n";
		}

		$check = 0;

		if ($position2 < count($sortableField))
		{
			for ($i = $position2; $i < count($sortableField); $i++)
			{
				if($sortableField[$i] == 'vcity' || $sortableField[$i] == 'vstate' || $sortableField[$i] == 'vcode' || $sortableField[$i] == 'vcountry')
				{
					if ($check % 2 == 0)
					{
						$html .= '<div class="row-fluid">';
					}
					$html .= "<div class='span6'>" . $field[$sortableField[$i]] . "</div>";

					if ($check % 2 != 0 || $i == count($sortableField) - 1)
					{
						$html .= "</div>\n";
					}
					$check++;
				}
			}
		}
		$html .= "</div></div>";
		return $html;
	}

	/**
	 * Generate html code for "Password" data field
	 *
	 * @param   object  $data            Data field
	 *
	 * @param   array   $dataSumbission  Data submission
	 *
	 * @return string
	 */
	public static function fieldPassword($data, $dataSumbission)
	{

		$limitValue = "";
		$styleClassLimit = "";
		$identify = !empty($data->identify) ? $data->identify : "";
		$title = $data->label != 'Password' ? $data->label : 'JSN_UNIFORM_DEFAULT_LABEL_PASSWORD';
		if (isset($data->options->limitation) && $data->options->limitation == 1 && $data->options->limitMax > 0)
		{
			$josnLimit = json_encode(array('limitMin' => $data->options->limitMin, 'limitMax' => $data->options->limitMax));
			if (isset($data->options->limitMax) && isset($data->options->limitMin) && $data->options->limitMax >= $data->options->limitMin && $data->options->limitMax > 0 && $data->options->limitMin >= 0)
			{
				$limitValue = "data-limit='{$josnLimit}'";
				$styleClassLimit = "limit-password-required";
			}
		}
		//$defaultValue = !empty($dataSumbission[$data->id])?$dataSumbission[$data->id]:'';
		$required = !empty($data->options->required) ? '<span class="required">*</span>' : '';
		$requiredBlank = !empty($data->options->required) ? 'group-blank-required' : '';
		$hideField = !empty($data->options->hideField) ? 'hide' : '';
		$customClass = !empty($data->options->customClass) ? $data->options->customClass : "";
		$instruction = !empty($data->options->instruction) ? " <i original-title=\"" . htmlentities(JText::_($data->options->instruction), ENT_QUOTES, "UTF-8") . "\" class=\"icon-question-sign\"></i>" : '';
		$sizeInput = !empty($data->options->size) ? $data->options->size : '';
		$placeholder = !empty($data->options->value) ? JText::_($data->options->value) : "";
		$placeholderConfirm = !empty($data->options->valueConfirmation) ? JText::_($data->options->valueConfirmation) : "";
		$confirmHtml = "";
		if (!empty($data->options->confirmation))
		{
			$confirmHtml = "<div><input {$limitValue} class=\"{$sizeInput}\" name=\"password[{$data->id}][]\" type=\"password\" value=\"\" placeholder=\"" . htmlentities($placeholderConfirm, ENT_QUOTES, "UTF-8") . "\" /></div>";
		}
		$html = "<div class=\"control-group {$customClass} jsn-group-field {$identify} {$hideField}\" data-id=\"{$data->id}\">
		<label  class=\"control-label\">" . htmlentities(JText::_($title), ENT_QUOTES, "UTF-8") . "{$required}{$instruction}</label>
			<div class=\"controls {$requiredBlank} {$styleClassLimit}\" id=\"{$data->id}\" >
				<div><input {$limitValue} class=\"{$sizeInput}\" name=\"password[{$data->id}][]\" type=\"password\" value=\"\" placeholder=\"" . htmlentities($placeholder, ENT_QUOTES, "UTF-8") . "\" /></div>
				{$confirmHtml}
			</div>
		</div>";
		return $html;
	}


	/**
	 * Generate html code for "Static Content" data field
	 *
	 * @param   object  $data            Data field
	 *
	 * @param   array   $dataSumbission  Data submission
	 *
	 * @return string
	 */
	public static function fieldStaticContent($data, $dataSumbission)
	{

		$identify = !empty($data->identify) ? $data->identify : "";
		$title = $data->label != 'Static Content' ? $data->label : 'JSN_UNIFORM_DEFAULT_LABEL_STATIC_CONTENT';
		$hideField = !empty($data->options->hideField) ? 'hide' : '';
		$customClass = !empty($data->options->customClass) ? $data->options->customClass : "";
		$value = isset($data->options->value) ? JText::_($data->options->value) : "";
		$html = "<div class=\"control-group {$customClass} {$identify} {$hideField} \" data-id=\"{$data->id}\"><label class=\"control-label\">" . htmlentities(JText::_($title), ENT_QUOTES, "UTF-8") . "</label><div class=\"controls clearfix\">{$value}</div></div>";
		return $html;
	}

	/**
	 * Generate html code for "Static Content" data field
	 *
	 * @param   object  $data            Data field
	 *
	 * @param   array   $dataSumbission  Data submission
	 *
	 * @return string
	 */
	public static function fieldGoogleMaps($data, $dataSumbission)
	{

		$identify = !empty($data->identify) ? $data->identify : "";
		$hideField = !empty($data->options->hideField) ? 'hide' : '';
		$customClass = !empty($data->options->customClass) ? $data->options->customClass : "";
		$height = isset($data->options->height) ? $data->options->height : "";
		$width = isset($data->options->width) ? $data->options->width : "";
		$formatWidth = isset($data->options->formatWidth) ? $data->options->formatWidth : "";
		$googleMaps = isset($data->options->googleMaps) ? $data->options->googleMaps : "";
		$googleMapsMarKer = isset($data->options->googleMapsMarKer) ? $data->options->googleMapsMarKer : "";
		$html = "<div class=\"control-group {$customClass} {$identify} {$hideField} \" data-id=\"{$data->id}\"><div class=\"content-google-maps clearfix\" data-width='{$width}{$formatWidth}' data-height='{$height}' data-value='{$googleMaps}' data-marker='" . htmlentities($googleMapsMarKer, ENT_QUOTES, "UTF-8") . "'><div class=\"google_maps map rounded\"></div></div></div>";
		return $html;
	}

	/**
	 * Generate html code for "Likert" data field
	 *
	 * @param   object  $data            Data field
	 *
	 * @param   array   $dataSumbission  Data submission
	 *
	 * @return string
	 */
	public static function fieldLikert($data, $dataSumbission)
	{

		$identify = !empty($data->identify) ? $data->identify : "";
		$title = $data->label != 'Likert' ? $data->label : 'JSN_UNIFORM_DEFAULT_LABEL_LIKERT';
		$hideField = !empty($data->options->hideField) ? 'hide' : '';
		$customClass = !empty($data->options->customClass) ? $data->options->customClass : "";
		$required = !empty($data->options->required) ? '<span class="required">*</span>' : '';
		$instruction = !empty($data->options->instruction) ? " <i original-title=\"" . htmlentities(JText::_($data->options->instruction), ENT_QUOTES, "UTF-8") . "\" class=\"icon-question-sign\"></i>" : '';
		$requiredLikert = !empty($data->options->required) ? 'likert-required' : '';
		$dataSettings = !empty($data->options->itemAction) ? $data->options->itemAction : '';
		preg_match_all('/\[PRICE:(.*)\]/U', $dataSettings, $matches, PREG_SET_ORDER);
		if (count($matches))
		{
			foreach ( $matches as $matche)
			{
				$dataSettings = str_replace($matche[0], '|' .$matche[1], $dataSettings);
			}
		}
		$html = "<div class='control-group {$customClass} {$identify} {$hideField}' data-settings='" . htmlentities($dataSettings, ENT_QUOTES, "UTF-8") . "' data-id='{$data->id}'><label class='control-label'>" . htmlentities(JText::_($title), ENT_QUOTES, "UTF-8") . "{$required}{$instruction}</label><div class='controls'>";

		if (isset($data->options->rows) && is_array($data->options->rows) && isset($data->options->columns) && is_array($data->options->columns))
		{
			$tdRows = "<tr>";
			$tdRows .= "<td class='likert_data_hidden'>";
			$tdRows .= "<input type=\"hidden\" name='likert[{$data->id}][settings]' value='".htmlentities(json_encode(array('rows'=>$data->options->rows,'columns'=>$data->options->columns)), ENT_QUOTES, "UTF-8")."' />";
			$tdRows .= "</td>";
			$tdRows .= "</tr>";
			$tdColumns = '';
			$html .= '';

			foreach ($data->options->rows as $row)
			{
				$tdRows .="<tr>";
				$tdRows .= '<td>'.$row->text.'</td>';
				foreach ($data->options->columns as $column)
				{
					$tdRows .= "<td class=\"text-center\"><input type=\"radio\" name='likert[{$data->id}][values][".md5($row->text)."]' value='".htmlentities($column->text, ENT_QUOTES, "UTF-8")."' /></td>";
				}
				$tdRows .="</tr>";
			}
			foreach ($data->options->columns as $column)
			{
				$tdColumns .= '<th class="text-center">'.$column->text.'</th>';
			}
			$html .= "<table class=\"table table-bordered table-striped {$requiredLikert}\">
				<thead>
					<tr>
						<th></th>
						{$tdColumns}
					</tr>
				</thead>
				<tbody>

					{$tdRows}

				</tbody>
			</table>";
		}

		$html .= "</div></div>";

		return $html;
	}

	/**
	 * Generate html code for "RecipientEmail" data field
	 *
	 * @param   object  $data            Data field
	 *
	 * @param   array   $dataSumbission  Data submission
	 *
	 * @return string
	 */
	public static function fieldRecepientEmail($data, $dataSumbission)
	{

		$identify = !empty($data->identify) ? $data->identify : "";
		$title = $data->label != 'Recipient Email' ? $data->label : 'JSN_UNIFORM_DEFAULT_LABEL_RECIPIENT_EMAIL';
		$hideField = !empty($data->options->hideField) ? 'hide' : '';
		$customClass = !empty($data->options->customClass) ? $data->options->customClass : "";
		$required = !empty($data->options->required) ? '<span class="required">*</span>' : '';
		$requiredBlank = !empty($data->options->required) ? 'list-required' : '';
		$randomList = !empty($data->options->randomize) ? 'list-randomize' : '';
		$instruction = !empty($data->options->instruction) ? " <i original-title=\"" . htmlentities(JText::_($data->options->instruction), ENT_QUOTES, "UTF-8") . "\" class=\"icon-question-sign\"></i>" : '';
		$defaultValue = !empty($dataSumbission[$data->id]) ? $dataSumbission[$data->id] : "";
		$sizeInput = !empty($data->options->size) ? $data->options->size : '';
		$disableMultiple = !empty($data->options->disableMultiple) && $data->options->disableMultiple == '1' ? "" : "multiple";
		$html = "<div class=\"control-group {$customClass} {$identify} {$hideField} \" data-id=\"{$data->id}\"><label  class=\"control-label\">" . htmlentities(JText::_($title), ENT_QUOTES, "UTF-8") . "{$required}{$instruction}</label><div class=\"controls {$requiredBlank}\"><select {$disableMultiple} id=\"{$data->id}\" class=\"list {$sizeInput} {$randomList} \" name=\"{$data->id}[]\">";
		if (isset($data->options->items) && is_array($data->options->items))
		{
			foreach ($data->options->items as $option)
			{
				if (!empty($defaultValue))
				{
					if (isset($option->text) && $option->text == $defaultValue)
					{
						$selected = "selected='selected'";
					}
					else
					{
						$selected = "";
					}
				}
				else
				{
					if ($option->checked == 1 || $option->checked == 'true')
					{
						$selected = "selected='selected'";
					}
					else
					{
						$selected = "";
					}
				}
				$value = $option->text;
				$label = $option->text;
				preg_match_all('/\[EMAIL:(.*)\]/U', $option->text, $matches, PREG_SET_ORDER);
				if (count($matches))
				{
					$label = str_replace($matches[0][0], '', $label);
					$value = $label . '|' .$matches[0][1];
					$email = $matches[0][1];
				}

				$html .= "<option class=\"jsn-column-item\" {$selected} data-email=\"{$email}\" value=\"" . $value . "\">" . htmlentities($label, ENT_QUOTES, "UTF-8") . "</option>";
			}
		}
		$html .= "</select></div></div>";
		return $html;
	}

	/**
	 * Generate html code for "IdentificationCode" data field
	 *
	 * @param   object  $data            Data field
	 *
	 * @param   array   $dataSumbission  Data submission
	 *
	 * @return string
	 */
	public static function fieldIdentificationCode($data, $dataSumbission)
	{

		$identify = !empty($data->identify) ? $data->identify : "";
		$title = $data->label != 'Identification Code' ? $data->label : 'JSN_UNIFORM_DEFAULT_LABEL_IDENTIFICATION_CODE';
		$hideField = !empty($data->options->hideField) ? 'hide' : '';
		$customClass = !empty($data->options->customClass) ? $data->options->customClass : "";
		$instruction = !empty($data->options->instruction) ? " <i original-title=\"" . htmlentities(JText::_($data->options->instruction), ENT_QUOTES, "UTF-8") . "\" class=\"icon-question-sign\"></i>" : '';
		$value = !empty($data->options->identificationCode) ? JText::_($data->options->identificationCode) : "";
		$value .= JSNUniformHelper::generateIdentificationCode();
		$html = "<div class=\"control-group {$customClass} {$identify} {$hideField}\" data-id=\"{$data->id}\"><label  class=\"control-label\">" . htmlentities(JText::_($title), ENT_QUOTES, "UTF-8") . "{$instruction}</label><div class=\"controls \"><span>$value</span><input type=\"hidden\" name=\"identification-code[{$data->id}]\" value=\"{$value}\"></div></div>";
		return $html;
	}
}