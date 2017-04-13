<?php

/**
 * @version     $Id: $
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
set_time_limit(999999999999);

$input 		= JFactory::getApplication()->input;
$getData 	= $input->getArray($_GET);
$formId 	= $input->get('form_id');
if ($formId)
{
    $fieldIdentifier = $this->_viewField['fields']['identifier'];
    $listViewField = $this->_viewField['field_view'];
    $fieldTitle = $this->_viewField['fields']['title'];
    $fieldType = $this->_viewField['fields']['type'];
    $arrayField = explode(",", str_replace("&quot;", '', $listViewField));
    $data = array();
    $dataItem = array();
    $dataItemLikert = array();
    $dataLikertTitle = array();
    for ($i = 0; $i < count($fieldIdentifier); $i++)
    {
        if (in_array($fieldIdentifier[$i], $arrayField))
        {
            if ($fieldType[$fieldIdentifier[$i]] == 'likert')
            {
                if ( $this->_dataExport)
                {
                    $foo = array();
                    foreach ($this->_dataExport AS $dt)
                    {
                        $rows = json_decode(json_decode($dt->{$fieldIdentifier[$i]})->settings)->rows;
                        foreach ($rows AS $row)
                        {
                            if (!in_array($row, $foo))
                            {
                                $foo[] = $row;
                            }
                        }

                    }
                    $countItems = count($foo) - 1;
                    foreach($foo as $bar)
                    {
                        $dataItemLikert[] = $bar->text;
                        $dataLikertTitle[] = $countItems.':::' .$fieldTitle[$i];
                    }

                }

            }
            else
            {
                $dataItem[] = JText::_($fieldTitle[$i]);
            }

        }
    }
    $likertDataTitle = array();
    $data[] = array_merge(array_unique($dataLikertTitle), $likertDataTitle);
    $dataItem = array_merge($dataItemLikert, $dataItem);
    $dataItem[] = JText::_("JGRID_HEADING_ID");
    $data[] = $dataItem;
    if (is_array($arrayField))
    {
        if ($this->_dataExport)
        {
            $listExport = $getData['list_export'];
            if (isset($listExport) && $listExport != '')
            {
                $listExport = explode(',', $listExport);
                foreach ($this->_dataExport as $i => $item)
                {
                    if (in_array($item->submission_id, $listExport))
                    {
                        $dataItem = array();
                        $dataLikert = array();
                        foreach ($arrayField as $j => $field)
                        {
                            $contentField = "";
                            if (isset($fieldType[$field]))
                            {
                                if ($fieldType[$field] == 'likert')
                                {
                                    $contentField = JSNUniformHelper::getDataField($fieldType[$field], $item, $field, $formId, false, false, 'export');
                                    $contentField = $contentField ? $contentField : "";
                                    if ($contentField)
                                    {
                                        $contentField = explode('<br/>', $contentField);
                                        foreach ( $contentField as $content)
                                        {
                                            $content = strip_tags($content);
                                            $content = explode(":", $content);
                                            $dataLikert[] = $content[1];
                                        }

                                    }
                                }
                                else
                                {
                                    $contentField = JSNUniformHelper::getDataField($fieldType[$field], $item, $field, $formId, false, false, 'export');
                                    $contentField = $contentField ? strip_tags($contentField) : "";
                                    if ($field == 'submission_created_by' && !$item->$field)
                                    {
                                        $contentField = isset($listUser[$item->$field]) ? $listUser[$item->$field] : "Guest";
                                    }
                                    $dataItem[] = $contentField;
                                }
                            }
                        }
                        $dataItem = array_merge($dataLikert, $dataItem);
                        $dataItem[] = $item->submission_id;
                        $data[]     = $dataItem;
                    }
                }
            }
            else{
                foreach ($this->_dataExport as $i => $item)
                {
                    $dataItem = array();
                    $dataLikert = array();
                    foreach ($arrayField as $j => $field)
                    {
                        $contentField = "";
                        if (isset($fieldType[$field]))
                        {
                            if ($fieldType[$field] == 'likert')
                            {
                                $contentField = JSNUniformHelper::getDataField($fieldType[$field], $item, $field, $formId, false, false, 'export');
                                $contentField = $contentField ? $contentField : "";
                                if ($contentField)
                                {
                                    $contentField = explode('<br/>', $contentField);
                                    foreach ( $contentField as $content)
                                    {
                                        $content = strip_tags($content);
                                        $content = explode(":", $content);
                                        $dataLikert[] = $content[1];
                                    }
                                }
                                else
                                {
                                    //Find what item isset $field then count statement. Set text = N/A
                                    $maxcount = 0;
                                    foreach ($this->_dataExport AS $temp)
                                    {
                                        if (isset($temp->{$field}))
                                        {

                                            $count = count(json_decode((json_decode($temp->{$field})->settings))->rows);
                                            if ($count > $maxcount)
                                            {
                                                $maxcount = $count;
                                            }										}
                                    }
                                    for ($k = 0; $k < ($maxcount); $k++)
                                    {
                                        $dataLikert[] = "N/A";
                                    }
                                }
                            }
                            else
                            {
                                $contentField = JSNUniformHelper::getDataField($fieldType[$field], $item, $field, $formId, false, false, 'export');
                                $contentField = $contentField ? strip_tags($contentField) : "";
                                if ($field == 'submission_created_by' && !$item->$field)
                                {
                                    $contentField = isset($listUser[$item->$field]) ? $listUser[$item->$field] : "Guest";
                                }
                                $dataItem[] = $contentField;
                            }

                        }

                    }
                    $dataItem = array_merge($dataLikert, $dataItem);
                    $dataItem[] = $item->submission_id;
                    $data[] = $dataItem;
                }
            }
        }
    }
    if (isset($getData['e']) && $getData['e'] == "excel")
    {
        include_once JSN_UNIFORM_LIB_PHPEXCEL;
        // generate file (constructor parameters are optional)
        $xls = new Excel_XML('UTF-8', false, 'JSN UniForm submission data');
        $xls->addArray($data);
        $xls->generateXML('jsn-uniform-' . $this->_infoForm->form_title . '-excel-' . date("Y-m-d"));
        exit();
    }
    else if (isset($getData['e']) && $getData['e'] == "csv")
    {
        $fileName = 'jsn-uniform-' . $this->_infoForm->form_title . '-csv-' . date("Y-m-d");
        $fileName = preg_replace('/[^aA-zZ0-9\_\-]/', '', $fileName);
        header("Content-type:text/octect-stream; charset=UTF-8");
        header("Content-Disposition:attachment;filename={$fileName}.csv");
        $output = fopen('php://output', 'w');
        foreach ($data as $items)
        {
            fputcsv($output, $items);
        }
        fclose($output);
        exit();
    }
    else if (isset($getData['e']) && $getData['e'] == "pdf")
    {
        $html = '';
        $firstHeading = $data[0];
        $secondHeading = $data[1];
        unset($data[0]);
        unset($data[1]);

        $likert = array();
        if (!empty ($firstHeading))
        {
            foreach ($firstHeading AS $i => $group)
            {
                $matches = explode(':::', $group);
                if ($matches['0'] > 0)
                {
                   $likert[$i]['count'] = $matches[0];
                   $likert[$i]['name'] = $matches[1];
                }

            }
        }

        foreach ($data AS $row)
        {
            $html .= '<h1>'.JTEXT::_("JSN_UNIFORM_FORM_SUBMISSION_ID").': '.$row[count($secondHeading)-1].'</h1><br/>';
            $totalLikert = 0;
            foreach ($likert AS $likertItem)
            {
                $html .= '<br/><strong>'.$likertItem['name'].'</strong><br/><br>';
                $html .= '<table border="1"><tr>';
                for ($i=$totalLikert; $i <= $likertItem['count']+$totalLikert; $i++)
                {
                    $html .= '<td>&nbsp;<strong>'.$secondHeading[$i].'</strong></td>';

                }
                $html .= '</tr><tr>';
                for ($i=$totalLikert; $i <= $likertItem['count']+$totalLikert; $i++)
                {
                    $html .= '<td>&nbsp;'.$row[$i].'</td>';

                }
                $html .= '</tr></table><br/>';
                $totalLikert = $i;
            }

            for ($i = $totalLikert; $i < count($secondHeading)-1; $i++)
            {
                $html .= '<br/><strong>'.$secondHeading[$i].'</strong>: ';
                if ($row[$i] == '')
                {
                    $html .= 'N/A<br/>';
                }
                else
                {
                    $html .= $row[$i].'<br/>';
                }

            }
        }

        //Create pdf file from $html
        include_once JSN_UNIFORM_LIB_TCPDF;
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('JSN UniForm');
        $pdf->SetTitle('Submission Data - Form '.$formId.'');

        $pdf->SetPrintHeader(false);
        $pdf->setFooterData(array(0,64,0), array(0,64,128));
        //$pdf->SetPrintFooter(false);
        // set default header data
        $pdf->setFooterData(array(0,64,0), array(0,64,128));

        // set header and footer fonts
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));


        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);


        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set font
        $fontFamilyConfig = JSNUniformHelper::getDataConfig('pdf_export_font_family');
        $fontSizeConfig = JSNUniformHelper::getDataConfig('pdf_export_font_size');

        $customFontFamily = isset($fontFamilyConfig->value) ? $fontFamilyConfig->value : "";
        $fontSize = isset($fontSizeConfig->value) ? $fontSizeConfig->value : "10";


        $fontName = 'dejavusans';
        if ($customFontFamily != '')
        {

            $fontName = TCPDF_FONTS::addTTFfont(dirname(JSN_UNIFORM_LIB_TCPDF).'/customfonts/'.$customFontFamily.'.ttf', 'TrueTypeUnicode', '', 96);
            try{
                TCPDF_FONTS::addTTFfont(dirname(JSN_UNIFORM_LIB_TCPDF).'/customfonts/'.$customFontFamily.'b.ttf', 'TrueTypeUnicode', '', 96);
                TCPDF_FONTS::addTTFfont(dirname(JSN_UNIFORM_LIB_TCPDF).'/customfonts/'.$customFontFamily.'-Bold.ttf', 'TrueTypeUnicode', '', 96);
            }
            catch (Exception  $e){}
        }
        $pdf->SetFont($fontName, '', $fontSize);

        // add a page
        $pdf->AddPage('L','A4');
        // write the text
        $pdf->writeHTML($html, $linebreak = true, $fill = false, $reseth = true, $cell = true, $align = '');

        $tmp_path = JFactory::getApplication()->getCfg('tmp_path').'/';
        $fileName = 'jsn_uniform_export_'.$formId.'_'.rand(0, 9999999).'.pdf';
        $pdf->Output($tmp_path.$fileName, 'F');
        header("Content-type:text/octect-stream; charset=UTF-8");
        header("Content-Disposition:attachment;filename=$fileName");
        readfile($tmp_path.$fileName);
    }
}
