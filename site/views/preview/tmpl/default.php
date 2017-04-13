<?php

/**
 * @version     $Id:$
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
$dataFields = $this->_dataFields;
$submission = $this->_dataSubmission;
?>

<div class="row-fluid jsn-uniform-preview">
    <?php
    if ($this->_formPages)
    {
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
                    $label = $fields->label != '' ? $fields->label : $fields->identify;
                    $submissionDetail .= '<tr class="jsn-uniform-preview-field"><td class="jsn-preview-field-name"><strong>' . $label . ': </strong></td><td id="' . $key . '">';
                    $contentField = "";
                    $contentFieldDetail = "";
                    if (isset($submission->$key))
                    {
                        if ($fields->type == "likert" || $fields->type == 'checkboxes' || $fields->type == 'list')
                        {
                            if ($fields->type == "likert")
                            {
                                $contentField = JSNUniformHelper::getDataField($fields->type, $submission, $key, $this->_item->form_id, false);
                                $contentField = str_replace('<strong>', '<li><strong>', $contentField);
                                $contentField = str_replace('</strong>', ' </strong>', $contentField);
                                $contentField = str_replace(PHP_EOL, '</li>', $contentField);
                                $contentField = '<ul>'.$contentField.'</ul>';
                            }
                            else if ($fields->type == "checkboxes" OR $fields->type == "list")
                            {
                                $contentField = JSNUniformHelper::getDataField($fields->type, $submission, $key, $this->_item->form_id, false);
                                $contentFieldArr = explode('<br/>', $contentField);
                                $contentField = '';
                                foreach ($contentFieldArr AS $line)
                                {
                                    if ($line != '')
                                    {
                                        $contentField .= '<li>'.$line.'</li>';
                                    }
                                }
                                if ($contentField == '')
                                {
                                    $contentField = '<span class="jsn-preview-field-no-value">N/A</span>';
                                }
                                $contentField = '<ul>'.$contentField.'</ul>';
                            }

                        }
                        else
                        {
                            $contentField = JSNUniformHelper::getDataField($fields->type, $submission, $key, $this->_item->form_id, false);
                        }

                        if ($fields->type == "file-upload")
                        {
                            $contentField = $submission->$key;
                        }
                        if ($fields->type == "email")
                        {

                            $contentFieldDetail = !empty($contentField) ? '<a href="mailto:' . htmlentities($contentField, ENT_QUOTES, "UTF-8") . '">' . htmlentities($contentField, ENT_QUOTES, "UTF-8") . '</a>' : "N/A";
                        }
                        else
                        {
                            $contentFieldDetail = $contentField;

                        }
                    }

                    $submissionDetail .= trim($contentFieldDetail) != '' ? str_replace("\n", "<br/>", trim(htmlentities($contentFieldDetail, ENT_QUOTES, "UTF-8"))) : "N/A";
                    $submissionDetail .= '</td></tr>';
                }
                else if (isset($fields->type) && $fields->type == 'static-content')
                {
                    //We don't need to show google map on preview
                    if ($fields->type == 'static-content')
                    {
                        $submissionDetail .= '<tr class="jsn-uniform-preview-field"><td><strong>' . $fields->label . ': </strong></td><td id="' . $key . '">';
                        $submissionDetail .= "<span class='clearfix'>" . $fields->options->value . "</td></tr>";
                    }
                }
            }
            ?>
            <div class="jsn-uniform-preview-page" data-title="<?php echo $formPages->page_title; ?>" data-value="<?php echo $formPages->page_id; ?>">
                <div class="">
                    <h3><?php echo $formPages->page_title; ?></h3>
                </div>

                <table class="submission-page-content table table-striped" id="dl_<?php echo $formPages->page_id; ?>">
                    <tr>
                        <th><?php echo JTEXT::_("JSN_UNIFORM_PREVIEW_FIELD_NAME");?></th>
                        <th><?php echo JTEXT::_("JSN_UNIFORM_PREVIEW_FIELD_VALUE");?></th>
                    </tr>
                    <?php  echo html_entity_decode($submissionDetail, ENT_QUOTES, 'UTF-8') ; ?>
                </table>
            </div>
            <?php
        }
    }
    ?>
</div>

<?php
$edition = defined('JSN_UNIFORM_EDITION') ? JSN_UNIFORM_EDITION : "pro";
if (strtolower($edition) == "free")
{
    echo "<div class=\"jsn-page-footer\"><a href=\"http://www.joomlashine.com/joomla-extensions/jsn-uniform.html\" target=\"_blank\">" . JText::_('JSN_UNIFORM_POWERED_BY') . "</a> by <a href=\"http://www.joomlashine.com\" target=\"_blank\">JoomlaShine</a></div>";
}
?>

