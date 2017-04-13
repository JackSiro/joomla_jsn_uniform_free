<?php

/**
 * @version     $Id:
 * @package     JSNUniform
 * @subpackage  Configuration
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2016 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
?>

<div class="jsn-uf-page-integration jsn-page-list">
	<?php if ($this->editionOfUniform == 'FREE'){ ?>
	<div class="row-fluid form-vertical">
		<div class="span12"><span class="alert alert-success"style="width: 100%;text-align: center;width:100%;float:left;"><?php echo JText::_('JSN_UNIFORM_FEATURES_ARE_AVAILABEL_ONLY_IN_PRO_EDITION'); ?></span></div>
	</div>
	<?php } ?>
	<table class="table table-bordered table-striped jsn-table-centered">
		<thead>
			<tr>
				<th nowrap="nowrap"><?php echo JText::_('JSN_UNIFORM_INTEGRATION_NAME');?></th>
				<th nowrap="nowrap"><?php echo JText::_('JSN_UNIFORM_INTEGRATION_TYPE');?></th>
				<th nowrap="nowrap"><?php echo JText::_('JSN_UNIFORM_INTEGRATION_STATUS');?></th>
				<th nowrap="nowrap"><?php echo JText::_('JSN_UNIFORM_INTEGRATION_VERSION');?></th>
				<th nowrap="nowrap"><?php echo JText::_('JSN_UNIFORM_INTEGRATION_ACTION');?></th>
			</tr>   
		</thead>
		<tbody>  
		 
		<?php           
			foreach ($this->plugins as $key => $item) 
			{
				$isAuthenticated    = $item->authentication ? (int) $item->authentication : 0;
				$isInstalled       	= $item->is_installed ? (int) $item->is_installed : 0;
				$isSetting         	= $item->is_setting ? (int) $item->is_setting : 0;
				$isUpdated         	= $item->is_updated ? (int) $item->is_updated : 0;
				$currentClass		= $item->is_updated ? 'badge-important' : 'badge-warning';
				$updateClass		= $item->is_updated ? 'badge badge-success' : 'badge';
				?>
				<tr>
					<td class="jsn-column-title"><?php echo $item->name; ?></td>
					<td class="jsn-column-large"><?php echo $item->type;?></td>
					<td class="jsn-column-small">
					<?php if ($isInstalled) { ?>						
						<?php if($item->is_enabled) { ?>
							<a class="plugin-item-status btn btn-micro active" data-ext-id="<?php echo $item->extension_id; ?>" data-enabled="<?php echo $item->is_enabled; ?>" href="javascript:void(0)"><i class="icon-publish"></i></a>
							<span class="hide loading-process status-loading-process"></span>
						<?php } else { ?>
							<a class="plugin-item-status btn btn-micro" data-ext-id="<?php echo $item->extension_id; ?>" data-enabled="<?php echo $item->is_enabled; ?>" href="javascript:void(0)"><i class="icon-unpublish"></i></a>
							<span class="hide loading-process status-loading-process"></span>
						<?php } ?>
					<?php } ?>
					</td>  
					<td class="jsn-column-medium">					
						<span class="current_version badge <?php echo $currentClass ?>"><?php echo $item->current_version;?></span>
						<span class="new_version <?php echo $updateClass ?>"><?php echo $item->new_version;?></span>
					</td> 
					<td class="jsn-column-large">
						<?php if ($this->editionOfUniform != 'FREE'){ ?>
							<span class="update-info">
								
								<?php if ($isInstalled && $isSetting) { ?>
									<a class="plugin_item_edit btn btn-small btn-nfo" href="<?php echo $item->edit_link;?>"><i class="icon-pencil"></i> <?php echo JText::_('JSN_UNIFORM_INTEGRATION_SETTING');?></a>
								<?php } ?>
								
								<?php if ($isInstalled && $isUpdated) {
									// has new a update
									$actionLink = '<a data-plugin-name="'. $item->name .'" data-identified-name="'. $key .'" data-edition="'. $item->edition .'" data-auth="'. $isAuthenticated .'" data-install="'. $isInstalled .'" id="'. $key .'" class="jsn-updated-plugin-btn btn btn-small btn-warning" href="index.php?option=com_uniform&view=integration&tmpl=component&layout=update"><span class="icon-upload"></span>' . JText::_('JSN_UNIFORM_INTEGRATION_UPDATE') . '</a>';
								} elseif (!$isInstalled) { 
									// has a install
									$actionLink = ' <a data-plugin-name="'. $item->name .'" data-identified-name="'. $key .'" data-edition="'. $item->edition .'" data-auth="'. $isAuthenticated .'" data-install="'. $isInstalled .'" id="'. $key .'" class="jsn-installed-plugin-btn btn btn-small btn-success" href="index.php?option=com_uniform&view=integration&tmpl=component&layout=install"><span class="icon-box-add"></span>' . JText::_('JSN_UNIFORM_INTEGRATION_INSTALL') . '</a>';
					 			} else {
					 				$actionLink = '';
					 			} ?>
								<?php echo $actionLink;?>
								<?php if ($isInstalled) { 
									$disabled 	= '';
									$href 		= 'index.php?option=com_uniform&view=integration&tmpl=component&layout=uninstall';
									$class		= 'plugin_item_uninstall btn btn-small btn-danger';
									if ($item->element == 'mailchimp' || $item->element == 'payment_paypal') { 
										$disabled 	= 'disabled';
										$href 		= 'javascript:void(0);';
										$class 		= 'btn btn-small btn-danger';
									}
									?>
									<a <?php echo $disabled ?> data-ext-id="<?php echo $item->extension_id; ?>" data-identified-name="<?php echo $key ?>" data-edition="<?php echo $item->edition ?>" class="<?php echo $class; ?> " href="<?php echo $href; ?>"><i class="icon-remove"></i> <?php echo JText::_('JSN_UNIFORM_UNINSTALL');?></a>
								<?php } ?>
							</span>
							<span class="hide loading-process install-update-process"></span>
						<?php } ?>
					</td>
				</tr>                                
			<?php } ?>                  
		</tbody>
	</table>        
</div>
<?php
// Display footer
JSNHtmlGenerate::footer();
?>