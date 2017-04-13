<?php
/**
 * @version     $Id
 * @package     JSNUniform
 * @subpackage  Plugin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2015 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');

class plgSystemUniform extends JPlugin
{
	public function onAfterInitialise()
	{
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();

		if ($app->isSite() && $document->getType() == 'html')
		{
			if (JPluginHelper::isEnabled('system', 'cache') && version_compare(JVERSION, '3.0.0', '>='))
			{
				$dispatcher = JEventDispatcher::getInstance();
				$refObj = new ReflectionObject($dispatcher);
				$refProp = $refObj->getProperty('_observers');
				$refProp->setAccessible(true);
				$observers = $refProp->getValue($dispatcher);
				foreach($observers as $index => $object)
				{
					if (is_a($object, 'plgSystemCache'))
					{
						$options = $object->_cache->cache->_options;

						if (is_array($options))
						{
							if (isset($options['browsercache']) && isset($options['defaultgroup']))
							{
								if ($options['browsercache'] == '1' && $options['defaultgroup'] == 'page')
								{
									$options['browsercache'] = '0';
									$object->_cache->cache->_options = $options;
								}
							}
						}
					}
				}
			}
		}
	}

    public function onAfterRender()
    {
        $app = JFactory::getApplication();
        $input = $app->input;

        if ($app->isAdmin() && $input->getVar('option', '') == 'com_uniform')
        {
            $html = $app->getBody();
            if (($input->getVar('view', '') == 'forms') || ($input->getVar('view', '') == 'configuration') || ($input->getVar('view', '') == 'submissions') || ($input->getVar('view', '') == 'form'))
            {
                // Remove scrollspy jQuery conflict
                if (preg_match_all("/\\$\('\.subhead'\)\.scrollspy\(\{[^\r\n]+\}\);/", $html, $matches, PREG_SET_ORDER))
                {
                    $html = preg_replace("/\\$\('\.subhead'\)\.scrollspy\(\{[^\r\n]+\}\);/", '',  $html);
                    $app->setBody($html);
                }
                if (preg_match_all("/\\jQuery\('\.hasTooltip'\)\.tooltip\(\{[^\r\n]+\}\);/", $html, $matches, PREG_SET_ORDER))
                {
                    $html = preg_replace("/\\jQuery\('\.hasTooltip'\)\.tooltip\(\{[^\r\n]+\}\);/", '',  $html);
                    $app->setBody($html);
                }
            }
            if ($input->getVar('view', '') == 'form')
            {
                if (preg_match_all("/jQuery\('\[rel=tooltip\]'\)\.tooltip\(\);/", $html, $matches, PREG_SET_ORDER))
                {
                    $html = preg_replace("/jQuery\('\[rel=tooltip\]'\)\.tooltip\(\);/", '', $html);
                    $app->setBody($html);
                }
                //if (preg_match('#<script[^>]+src="[^"]*/js/template\.js[^"]*"[^>]*></script>#', $html, $match))
                //{
                //$html = preg_replace('#<script[^>]+src="[^"]*/js/template\.js[^"]*"[^>]*></script>#', '', $html);
                //$app->setBody($html);
                //}

            }
        }
    }

    public function onBeforeRender()
    {
        $app = JFactory::getApplication();
		if ($app->isSite())
		{
			//Check if using recaptcha v2
			$captchaParams = JPluginHelper::getPlugin('captcha', 'recaptcha');
			$params = json_decode(@$captchaParams->params);
			if (version_compare(@$params->version, '2.0', '>=')) {
				$jVersion		= new JVersion;
				$jShortVersion 	= $jVersion->getShortVersion();

				if (version_compare($jShortVersion, '3.5.0', '<'))
				{
					$document = JFactory::getDocument();
					$document->addScriptDeclaration('
					var JSNUFOnloadCallback = function() {
						jQuery(".jsn-uf-grecaptchav2").each(function(){
							grecaptcha.render(jQuery(this).attr("id"), {"sitekey" : jQuery(this).attr("data-sitekey"), "theme": jQuery(this).attr("data-theme"), "timeout":jQuery(this).attr("data-timeout")});
						})
					}
					');
					$document->addScript('https://www.google.com/recaptcha/api.js?onload=JSNUFOnloadCallback&render=explicit', 'text/javascript', true, true);
				}
				else
				{
					// Load callback first for browser compatibility
					$file = 'https://www.google.com/recaptcha/api.js?onload=JoomlaInitReCaptcha2&render=explicit&hl=' . JFactory::getLanguage()->getTag();
					JHtml::_('script', $file);
					JHtml::_('script', 'plg_captcha_recaptcha/recaptcha.min.js', false, true);
				}
			}
		}
    }
}