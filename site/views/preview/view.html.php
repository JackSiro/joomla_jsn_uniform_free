<?php

/**
 * @version     $Id: view.html.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Submission
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2016 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');


jimport('joomla.application.component.view');
jimport('joomla.application.helper');

/**
 * View class for a list of Submission.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_uniform
 * @since       1.5
 */
class JSNUniformViewPreview extends JSNBaseView
{
    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  A string if successful, otherwise a JError object.
     *
     * @see     fetch()
     * @since   11.1
     */
    public function display($tpl = null)
    {
        $postData = $this->_postData;
        $this->_dataFields = JSNUniformModelPreview::getDataFields($postData['form_id']);
        $this->_dataSubmission = JSNUniformModelPreview::preview($postData);
        $this->_formPages = JSNUniformModelPreview::getFormPages($postData['form_id']);
        parent::display($tpl);
        exit();
    }
}
