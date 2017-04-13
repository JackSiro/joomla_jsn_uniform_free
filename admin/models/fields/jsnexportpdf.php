<?php
/**
 * @version    $Id$
 * @package    JSN_Framework
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2016 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Create radio buttons.
 *
 * Below is a sample field declaration for generating radio input field:
 *
 * <code>&lt;field
 *     name="disable_all_messages" type="jsnradio" default="0" filter="int"
 *     label="JSN_SAMPLE_DISABLE_ALL_MESSAGES_LABEL" description="JSN_SAMPLE_DISABLE_ALL_MESSAGES_DESC"
 * &gt;
 *     &lt;option value="0"&gt;JNO&lt;/option&gt;
 *     &lt;option value="1"&gt;JYES&lt;/option&gt;
 * &lt;/field&gt;</code>
 *
 * @package  JSN_Framework
 * @since    1.0.0
 *
 */
class JFormFieldJSNExportPDF extends JSNFormField
{
    /**
     * The form field type.
     *
     * @var    string
     */
    protected $type = 'jsnexportpdf';

    /**
     * Get the field label markup.
     *
     * @return  string
     */
    protected function getLabel()
    {
        // Preset label
        $label = '';

        if ($this->hidden)
        {
            return $label;
        }
        if (empty($this->element['label']))
        {
            return;
        }
        // Get the label text from the XML element, defaulting to the element name
        $text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
        $text = $this->translateLabel ? JText::_($text) : $text;

        // Build the class for the label
        $class = array('control-label');
        $class[] = !empty($this->description) ? ' hasTip' : '';
        $class[] = $this->required == true ? ' required' : '';
        $class[] = !empty($this->labelClass) ? ' ' . $this->labelClass : '';
        $class = implode('', $class);

        // Add the opening label tag and class attribute
        $label .= '<label class="' . $class . '"';

        // If a description is specified, use it to build a tooltip
        if (!empty($this->description))
        {
            $label .= ' title="' . htmlspecialchars(($this->translateDescription ? JText::_($this->description) : $this->description), ENT_COMPAT, 'UTF-8') . '"';
        }

        // Add the label text and closing tag
        $label .= '>' . $text . ($this->required ? '<span class="star">&#160;*</span>' : '') . '</label>';

        return $label;
    }

    /**
     * Get the radio button field input markup.
     *
     * @return  string
     */
    protected function getInput()
    {
        // Preset output
        $html = array();

        // Get radio button options
        $options = $this->getPdfExportFonts();

        // Generate HTML code
        $html[] = '<div id="jsnconfig-export-pdf-field" class="control-group"><label class="control-label" original-title="">'.(JText::_("JSN_UNIFORM_FONT_FAMILY")).'</label><div class="controls"><select id="'.$this->id.'" name="' . $this->name . '" ' . $class . $onclick . $checked . $disabled . '>';
        $html[] = '<option value="">'.(JText::_("JSN_UNIFORM_PDF_EXPORT_DEFAULT_FONT")).'</option>';
        foreach ($options AS $option)
        {
            if ($this->value == $option)
            {
                $html[] = '<option value="'.$option.'" selected="selected">'.$option.'</option>';
            }
            else
            {
                $html[] = '<option value="'.$option.'">'.$option.'</option>';
            }

        }
        $html[] = '</select></div></div>';
        return implode($html);
    }

    public function getPdfExportFonts()
    {
        $fontDirectory = dirname(JSN_UNIFORM_LIB_TCPDF).DIRECTORY_SEPARATOR.'customfonts';
        $files = scandir($fontDirectory);
        $extensions = array("ttf");
        $fonts = array();

        foreach ($files as $filename)
        {
            $filepath = $fontDirectory.DIRECTORY_SEPARATOR.$filename;

            if(is_file($filepath))
            {
                $ext = pathinfo($filepath, PATHINFO_EXTENSION);
                if (in_array($ext, $extensions))
                {
                    $fonts[] = ucfirst(basename($filepath, ".ttf"));
                }
            }
        }

        return $fonts;
    }
}
