<?php

/**
 * @version     $Id:
 * @package     JSNUniform
 * @subpackage  Controller
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2016 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Disable notice and warning by default for our products.
// The reason for doing this is if any notice or warning appeared then handling JSON string will fail in our code.
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

/**
 * Update controller of JSN Framework Sample component
 * 
 * @package     Controllers
 * @subpackage  Update
 * @since       1.6
 */
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class JSNUniformControllerIntegration extends JControllerForm
{
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    public function confirm()
    {
        JSession::checkToken('get') or die( 'Invalid Token' );
       
        $config = JFactory::getConfig();
        $jVer   = new JVersion;
        $input 	= JFactory::getApplication()->input;

        // Get the product info
        
        $edition    	= $input->getString('edition', 'PREMIUM');
        $identifiedName = $input->getString('identified_name', '');
        $extensionName 	= $input->getString('extension_name', '');

        // Build query string
        $query[] = 'joomla_version=' . $jVer->RELEASE;
        $query[] = 'username=' . urlencode($input->getUsername('customer_username'));
        $query[] = 'password=' . urlencode($input->getString('customer_password'));
        $query[] = 'identified_name=' . $identifiedName;
        $query[] = 'edition=' . strtolower(urlencode($edition));

        // Build final URL for downloading update
        $url = JSN_UNIFORM_DOWNLOAD_UPDATE_URL . '&' . implode('&', $query);

        // Generate file name for update package
        $name[] = $identifiedName;

        if ($edition)
        {
            $name[] = strtolower(str_replace(' ', '_', $edition));
        }

        $name[] = 'j' . $jVer->RELEASE;
        $name[] = date('YmdHis');       
        $name[] = 'install.zip';
        
        $name   = implode('_', $name);

        // Set maximum execution time
        ini_set('max_execution_time', 300);     

        // Try to download the update package
        try
        {

            // Check if temporary directory exists
            if (!is_dir($config->get('tmp_path')) OR !is_writable($config->get('tmp_path')))
            {
            	echo json_encode(array('type'=>'error', 'message' => JText::_('JSN_UNIFORM_INSTALLER_TEMPORARY_DIRECTORY_NOT_WRITABLE'), 'path' => ''));
            }
            
            $path = $config->get('tmp_path') . '/' . $name;
            
            if (!JSNUtilsHttp::get($url, $path, true))
            {
            	echo json_encode(array('type'=>'error', 'message' => JText::_('JSN_UNIFORM_DOWNLOAD_PACKAGE_FAIL'), 'path' => ''));
            	die;
            }
        }
        catch (Exception $e)
        {
            echo json_encode(array('type'=>'error', 'message' => JText::_('JSN_UNIFORM_DOWNLOAD_PACKAGE_FAIL'), 'path' => ''));
            die;
        }

        if (!is_file($path))
        {
        	echo json_encode(array('type'=>'error', 'message' => JText::_('JSN_UNIFORM_FILE_NOT_EXISTED'), 'path' => ''));
        	die;
        }
        
        // Validate downloaded update package
        if (filesize($path) < 10)
        {
        	// Get LightCart error code
        	$errorCode = file_get_contents($path); 	
        	// Is the package file a valid file?
        	if (is_file($path))
        	{
        		JFile::delete($path);
        	}
        	echo json_encode(array('type'=>'error', 'message' => JText::sprintf('JSN_UNIFORM_LIGHTCART_ERROR_' . $errorCode, $extensionName), 'path' => ''));
        	die;
        }
               
        echo json_encode(array('type'=>'success', 'message' => '', 'path' => $path));
        die;
    }   

    public function install()
    {
        JSession::checkToken('get') or die( 'Invalid Token');
        
        // Get Joomla version object
        $JVersion = new JVersion;
        $config = JFactory::getConfig();
        
        // Initialize update package path
        $input 		= JFactory::getApplication()->input;
        $postData 	= $input->getArray($_POST);
        $path     	= $postData['path'];  

        if (!is_file($path))
        {
        	JFile::delete($path);
        	echo json_encode(array('type'=>'error', 'message' => JText::_('JSN_UNIFORM_PACKAGE_FILE_NOT_FOUND')));
            die;
        }
 
        $filetypes = explode('.', $path);
        
        if (count($filetypes) < 2)
        {
        	JFile::delete($path);
        	echo json_encode(array('type'=>'error', 'message' => JText::_('JSN_UNIFORM_INSTALLER_PACKAGE_SAVING_FAILED')));
           	die;
        }
 
        array_shift($filetypes);
        	
        // Media file names should never have executable extensions buried in them.
        $executable = array(
        		'php', 'js', 'exe', 'phtml', 'java', 'perl', 'py', 'asp','dll', 'go', 'ade', 'adp', 'bat', 'chm', 'cmd', 'com', 'cpl', 'hta', 'ins', 'isp',
        		'jse', 'lib', 'mde', 'msc', 'msp', 'mst', 'pif', 'scr', 'sct', 'shb', 'sys', 'vb', 'vbe', 'vbs', 'vxd', 'wsc', 'wsf', 'wsh'
        );
        	
        $check = array_intersect($filetypes, $executable);
        	
        if (!empty($check))
        {
        	JFile::delete($path);
        	echo json_encode(array('type'=>'error', 'message' => JText::_('JSN_UNIFORM_INSTALLER_PACKAGE_SAVING_FAILED')));
           	die;
        }
  
        $filetype = array_pop($filetypes);
        if ($filetype == '' || $filetype == false || !in_array($filetype, array('zip', 'bz2', 'gz')))
        {
        	JFile::delete($path);
        	echo json_encode(array('type'=>'error', 'message' => JText::_('JSN_UNIFORM_INSTALLER_PACKAGE_SAVING_FAILED')));
           	die;
        }  
        
        $xssCheck = file_get_contents($path, false, null, -1, 256);
        $htmlTags = array(
        		'abbr', 'acronym', 'address', 'applet', 'area', 'audioscope', 'base', 'basefont', 'bdo', 'bgsound', 'big', 'blackface', 'blink',
        		'blockquote', 'body', 'bq', 'br', 'button', 'caption', 'center', 'cite', 'code', 'col', 'colgroup', 'comment', 'custom', 'dd', 'del',
        		'dfn', 'dir', 'div', 'dl', 'dt', 'em', 'embed', 'fieldset', 'fn', 'font', 'form', 'frame', 'frameset', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        		'head', 'hr', 'html', 'iframe', 'ilayer', 'img', 'input', 'ins', 'isindex', 'keygen', 'kbd', 'label', 'layer', 'legend', 'li', 'limittext',
        		'link', 'listing', 'map', 'marquee', 'menu', 'meta', 'multicol', 'nobr', 'noembed', 'noframes', 'noscript', 'nosmartquotes', 'object',
        		'ol', 'optgroup', 'option', 'param', 'plaintext', 'pre', 'rt', 'ruby', 's', 'samp', 'script', 'select', 'server', 'shadow', 'sidebar',
        		'small', 'spacer', 'span', 'strike', 'strong', 'style', 'sub', 'sup', 'table', 'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead', 'title',
        		'tr', 'tt', 'ul', 'var', 'wbr', 'xml', 'xmp', '!DOCTYPE', '!--'
        );
        	
        foreach ($htmlTags as $tag)
        {
        	// A tag is '<tagname ', so we need to add < and a space or '<tagname>'
        	if (stristr($xssCheck, '<' . $tag . ' ') || stristr($xssCheck, '<' . $tag . '>'))
        	{
        		JFile::delete($path);
        		echo json_encode(array('type'=>'error', 'message' => JText::_('JSN_UNIFORM_INSTALLER_PACKAGE_SAVING_FAILED')));
           		die;
        	}
        }
        
        // Switch off debug mode to catch JInstaller error message manually
        $debug	= $config->get('debug');
        $config->set('debug', version_compare($JVersion->RELEASE, '3.0', '<') ? false : true);
        // Load extension installation library
        jimport('joomla.installer.helper');

        // Extract downloaded package
        $unpackedInfo 	= JInstallerHelper::unpack($path);
        $installer 		= JInstaller::getInstance();

        // Restore debug settings
        $config->set('debug', $debug);
        if (empty($unpackedInfo) OR ! isset($unpackedInfo['dir']))
        {
        	JFile::delete($path);
        	echo json_encode(array('type'=>'error', 'message' => JText::_('JSN_UNIFORM_EXTRACT_PACKAGE_FAIL')));
            die;
        }

        // Install extracted package
        if (!$installer->install($unpackedInfo['dir']))
        {
        	JInstallerHelper::cleanupInstall($path, $unpackedInfo['dir']);
        	echo json_encode(array('type'=>'error', 'message' => JText::_('JSN_UNIFORM_INSTALL_PACKAGE_FAIL')));
            die;
        }

        JInstallerHelper::cleanupInstall($path, $unpackedInfo['dir']);

        echo json_encode(array('type'=>'success'));
        die;
    } 

    /**
     * Set pluign status
     *
     * @param int $id
     * @param int $status
     *
     * @return json
     */
    public function setStatus()
    {
        JSession::checkToken('get') or die( 'Invalid Token' );
        $input 	= JFactory::getApplication()->input;
        $enable = $input->getInt('enable', 0);
        $id		= $input->getInt('ext_id', 0);
        $model 	= $this->getModel();
       
        $r = $model->setStatus($id, $enable);
        
        if ($r)
        {
        	echo json_encode(array('type'=>'success'));
        	die;
        }
        
		echo json_encode(array('type'=>'error', 'message' => JText::_('JSN_UNIFORM_SET_STATUS_UNSUCCESSFULLY')));
        die;
    }
 
    /**
     * Remove (uninstall) an extension
     *
     * @param   array  $eid  An array of identifiers
     *
     * @return  boolean  True on success
     */
    
    public function remove()
    {
    	JSession::checkToken('get') or die( 'Invalid Token' );
    	   	
    	$input 		= JFactory::getApplication()->input;
    	$postData 	= $input->getArray($_POST);
    	$id     	= (int) $postData['plugin_id'];
    	
    	// Get an installer object for the extension type
    	$installer 	= JInstaller::getInstance();
    	$row 		= JTable::getInstance('extension');
    	
    	// Uninstall the chosen extensions
    	$msgs 	= '';
    	$result = false;
    	
    	$id = trim($id);
    	$row->load($id);
    	$result = false;
    	
    	$langstring = 'JSN_UNIFORM_TYPE_TYPE_' . strtoupper($row->type);
    	$rowtype = JText::_($langstring);

    	if (strpos($rowtype, $langstring) !== false)
    	{
    		$rowtype = $row->type;
    	}
    	
    	if ($row->type && $row->type != 'language')
    	{
    		$result = $installer->uninstall($row->type, $id);
    	
    		// Build an array of extensions that failed to uninstall
    		if ($result === false)
    		{
    			// There was an error in uninstalling the package
    			$msgs = JText::sprintf('JSN_UNIFORM_UNINSTALL_ERROR', $rowtype);  	
    			echo json_encode(array('type'=>'error', 'message' => $msgs));
    			die;
    		}   	
    		// Package uninstalled sucessfully
    		echo json_encode(array('type'=>'success', 'message' => ''));
    		die;
    	}
    	
    	echo json_encode(array('type'=>'error', 'message' => $msgs));
    	die;
    }
}
