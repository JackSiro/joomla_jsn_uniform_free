<?php
/**
 * 4.1.1     $Id: uniform.defines.php 19014 2012-11-28 04:48:56Z thailv $
 * @package     JSNUniform
 * @subpackage  Config
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2016 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

defined('_JEXEC') or die('Restricted access');

// Only define below constant if product has multiple edition
define('JSN_UNIFORM_EDITION', 'PREMIUM');

// Define product identified name and version
define('JSN_UNIFORM_IDENTIFIED_NAME', 'ext_uniform');
define('JSN_UNIFORM_VERSION', '4.1.1');
define('JSN_UNIFORM_REQUIRED_JOOMLA_VER', '3.0');

// Define some necessary links
define('JSN_UNIFORM_INFO_LINK', 'http://www.joomlashine.com/joomla-extensions/jsn-uniform.html');
define('JSN_UNIFORM_DOC_LINK', 'http://www.joomlashine.com/joomla-extensions/jsn-uniform-docs.zip');
define('JSN_UNIFORM_REVIEW_LINK', 'http://www.joomlashine.com/joomla-extensions/jsn-uniform-on-jed.html');
define('JSN_UNIFORM_UPDATE_LINK', 'index.php?option=com_uniform&view=update');
define('JSN_UNIFORM_INTEGRATION_CHECK_URL', 'http://www.joomlashine.com/versioning/product_version.php?category=cat_extension');
define('JSN_UNIFORM_DOWNLOAD_UPDATE_URL',	'http://www.joomlashine.com/index.php?option=com_lightcart&controller=remoteconnectauthentication&task=authenticate&tmpl=component&upgrade=yes');

// If product has multiple edition, define upgrade link also
define('JSN_UNIFORM_UPGRADE_LINK', 'index.php?option=com_uniform&view=upgrade');

// If product is commercial, define buy link too
define('JSN_UNIFORM_BUY_LINK', 'http://www.joomlashine.com/joomla-extensions/jsn-uniform-buy-now.html');

// JSN UniForm backend constants
define('JSN_UNIFORM_PAGEDESIGN_ELEMENTS_PATH', JPATH_ROOT . '/administrator/components/com_uniform/assets/elements/');
define('JSN_UNIFORM_PAGEDESIGN_LAYOUTS_PATH', JPATH_ROOT . '/administrator/components/com_uniform/assets/layouts/');
define('JSN_UNIFORM_PAGEDESIGN_THEMES_PATH', JPATH_ROOT . '/administrator/components/com_uniform/assets/themes/');
define('JSN_UNIFORM_ASSETS_URI', JURI::base(true) . '/components/com_uniform/assets');
define('JSN_UNIFORM_FOLDER_UPLOAD', 'components/com_uniform/assets/upload');
define('JSN_UNIFORM', 'com_uniform');
define('JSN_UNIFORM_CATEGORY_IDENTIFIED_NAME', 'cat_ext_uniform');
define('JSN_UNIFORM_CATEGORY_PLUGINS_IDENTIFIED_NAME', 'cat_ext_uniform_plugins');

// Register autoload for helpers
JLoader::register('JSNUniformHelper', dirname(__FILE__) . '/helpers/uniform.php');
JLoader::register('JSNFormGenerateHelper', dirname(__FILE__) . '/helpers/form.php');

// JSN UniForm frontend constants
define('JSN_UNIFORM_ASSETS_UPLOAD', JPATH_ROOT . '/components/com_uniform/assets/upload');
define('JSN_UNIFORM_LINK_UPLOAD', JURI::root() . 'components/com_uniform/assets/upload/');
define('JSN_UNIFORM_FOLDER_ATTACH', JPATH_ROOT . '/components/com_uniform/assets/upload/');
define('JSN_UNIFORM_ADMIN_ASSETS_THEME', JURI::base(true) . '/components/com_uniform/assets/themes');
define('JSN_UNIFORM_FOLDER_TMP', JPATH_ROOT . '/tmp');

// reCAPTCHA keys
define('JSN_UNIFORM_LIB_CAPTCHA', JPATH_ROOT . '/components/com_uniform/libraries/3rd-party/recaptchalib.php');
define('JSN_UNIFORM_CAPTCHA_PUBLICKEY', "6LfdRNESAAAAAOOvsIa0AySHXcT7r6w7-QKJxIhz");
define('JSN_UNIFORM_CAPTCHA_PRIVATEKEY', "6LfdRNESAAAAADKbXLNxaZ8lvgwFosg__DOOy_DK");

//php excel
define('JSN_UNIFORM_LIB_PHPEXCEL', JPATH_COMPONENT_ADMINISTRATOR . '/libraries/3rd-party/php-excel.class.php');

//tcpdf library
define('JSN_UNIFORM_LIB_TCPDF', JPATH_COMPONENT_ADMINISTRATOR . '/libraries/3rd-party/tcpdf/tcpdf.php');