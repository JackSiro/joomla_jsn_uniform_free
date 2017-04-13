<?php

/**
 * @version     $Id: form.php 19014 2017-03-30 09:48:56Z giangth $
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
/**
 * Form controllers of JControllerForm
 *
 * @package     Controllers
 * @subpackage  Form
 * @since       4.1.0
 */
class JSNUniformControllerPreview extends JSNBaseController
{
    public function preview()
    {
        JSession::checkToken() or die( 'Invalid Token' );
        $input = JFactory::getApplication()->input;
        $view = $this->getView('preview','html');
        $view->_postData = $input->getArray($_POST);
        parent::display();
    }
}