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
class JSNUniFormCurrencyHelper
{
	private $_currencyCode   = 'USD';		// string ID related with the currency (ex : language)
	private $_symbol    		= '$';	// Printable symbol
	private $_numberDecimal 		= 2;	// Number of decimals past colon (or other)
	private $_decimal   		= '.';	// Decimal symbol ('.', ',', ...)
	private $_thousands 		= ','; 	// Thousands separator ('', ' ', ',')
	private $_position = 'left';


	public function __construct($currencyCode, $numberDecimal, $decimal, $thousands, $symbol, $position)
	{
		$this->_currencyCode = $currencyCode;
		$this->_symbol = $symbol;
		$this->_numberDecimal = $numberDecimal;
		$this->_thousands = $thousands;
		$this->_decimal = $decimal;
		$this->_position = $position;
	}

	public function displayCurrency($number)
	{
		$price = $this->getFormattedCurrency($number);
		$display = '';
		switch($this->_position)
		{
			case 'left':
				$display = $this->_symbol . $price;
				break;
			case 'right':
				$display = $price . $this->_symbol;
				break;
			case 'left_with_space':
				$display = $this->_symbol .' '. $price;
				break;
			case 'right_with_space':
				$display = $price .' '. $this->_symbol;
				break;
			default:
				$display = $price;
				break;
		}

		return $display;
	}

	public function getFormattedCurrency($number)
	{
		$res = number_format((float)$number, (int)$this->_numberDecimal, $this->_decimal, $this->_thousands);
		return $res;
	}
}