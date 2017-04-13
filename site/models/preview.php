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
/**
 * JSNUniform model Submission
 *
 * @package     Models
 * @subpackage  Submission
 * @since       1.6
 */

class JSNUniformModelPreview extends JModelItem
{
    public function getDataFields($formId)
    {
        $db = JFactory::getDBO();
        $db->setQuery(
            $db ->getQuery(true)
                ->select('fi.field_identifier,fi.field_title,fo.form_title,fo.form_id,fi.field_type,fi.field_id')
                ->from('#__jsn_uniform_fields AS fi')
                ->join('INNER', '#__jsn_uniform_forms AS fo ON fo.form_id = fi.form_id')
                ->where('fi.form_id=' . intval($formId))
                ->order('fi.field_ordering ASC')
        );
        return $db->loadObjectList();

    }

    /**
     * Retrieve submission for use in page submitted detail
     *
     * @return Object
     */
    public function getDataSubmission($submissionId)
    {
        $db = JFactory::getDBO();
        $item = new stdClass;
        $query = $db->getQuery(true)
            ->select('*')
            ->from("#__jsn_uniform_submission_data")
            ->where('submission_id =' . (int) $submissionId);
        $db->setQuery($query);
        $submissions = $db->loadObjectList();
        foreach ($submissions as $submission)
        {
            $item->{"sd_" . $submission->field_id} = $submission->submission_data_value;
        }
        return $item;
    }

    public function getFormPages($formId)
    {
        $db = JFactory::getDBO();
        $db->setQuery($db->getQuery(true)->select('*')->from("#__jsn_uniform_form_pages")->where('form_id=' . intval($formId)));
        return $db->loadObjectList();

    }

    public function preview($post)
    {
        JModelLegacy::addIncludePath(JPATH_COMPONENT.'/models');
        $model   = JModelLegacy::getInstance('form','JSNUniformModel');
        return $model->preview($post);
    }
}