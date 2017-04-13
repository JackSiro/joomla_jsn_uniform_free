/*------------------------------------------------------------------------
 # Full Name of JSN UniForm
 # ------------------------------------------------------------------------
 # author    JoomlaShine.com Team
 # copyright Copyright (C) 2016 JoomlaShine.com. All Rights Reserved.
 # Websites: http://www.joomlashine.com
 # Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 # @license - GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html

 -------------------------------------------------------------------------*/
define([
    'jquery',
    'jsn/libs/modal',
    'jquery.json',
    'jquery.ui' ],
    function ($, JSNModal) {
        var JSNUniformIntegration = function (params) {
            this.token = params.token;
            this.lang = params.language;
            this.init();
        }
        JSNUniformIntegration.prototype = {
            init:function () {
                var self = this;

                this.registerEvents(this.token);    

                $('#jsn-purchase-button').click(function(){
                    $('#jsn-uf-install-infomartion').hide();
                    $('#jsn-uf-login-form').show();
                    $('.ui-dialog-buttonpane', window.parent.document).remove();
                });                  
            },
            //Register events
            registerEvents:function (token) {
                var self = this;                
                $('.jsn-uf-page-integration').on('click', '.plugin_item_edit', function(event) {
                    event.preventDefault();
                    var rand 		= Math.floor((Math.random()*100)+1);
                    var selfSelect 	= this;
                    var link 		= $(this).attr('href');
                    var title 		= self.lang["JSN_UNIFORM_PAYMENT_GATEWAY_SETTING_TITLE"];
                    var iframeID 	= 'iframe-plugin-settings-modal-' + rand;
                    selfSelect.modal = new JSNModal({
                        width:$(window).width()*0.9,
                        height:$(window).height() *0.85,
                        url: link,
                        title: title,
                        scrollable: true,
                        buttons:[
                            {
                                text: self.lang["JSN_UNIFORM_SAVE"],
                                class:'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
                                click:$.proxy( function(){
                                    try{
                                        self.savePaymentSettings(selfSelect.modal, iframeID);
                                        selfSelect.modal.close();
                                    }catch(e){
                                        alert(e);
                                    }

                                }, this)
                            },
                            {
                                text: self.lang["JSN_UNIFORM_CANCEL"],
                                class: 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
                                click: $.proxy( function(){
                                    selfSelect.modal.close();
                                }, this)
                            }
                        ]

                    });
                    
                    selfSelect.modal.iframe.attr('id', iframeID);
                    selfSelect.modal.iframe.css('overflow-x', 'hidden');
                    selfSelect.modal.show();
                });

                $('.jsn-uf-page-integration').on('click', '.plugin-item-status', function(event){
                    var value   = $(this).attr('data-enabled');
                    var id      = $(this).attr('data-ext-id');
                    var $this   = $(this);
                    $(this).parent().find('.status-loading-process').css('display','inline-block');
                    $(this).hide();
                    if (value == 0)
                    {
                        var icon = '<i class="icon-publish"></i>';
                        var status = 1;
                    }
                    else
                    {
                        var icon = '<i class="icon-unpublish"></i>';
                        var status = 0;
                    }
                    $.ajax({
                        url: 'index.php?option=com_uniform&task=integration.setStatus&' + token + '=1',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            enable: parseInt(value),
                            ext_id: parseInt(id)
                        },
                        success: function(data) {  
                            if (data.type == 'success') {
                                $this.html(icon);
                                $this.attr('data-enabled', status);
                                $this.parent().find('.status-loading-process').css('display','none');
                                $this.show();
                            } else {
                                alert(data.message);
                                //$this.next().hide();
                                $this.parent().find('.status-loading-process').css('display','none');
                                $this.show();
                            }
                            return false;
                        }
                    });
                });
                
                //install plugin
                $('.jsn-uf-page-integration').on('click', '.jsn-installed-plugin-btn, .jsn-updated-plugin-btn', function (event) {
                	 event.preventDefault();                	 
                	 var authentication		= $(this).attr('data-auth'); 
                	 var identifiedName		= $(this).attr('data-identified-name');  
                	 var pluginName			= $(this).attr('data-plugin-name');  
                     var edition			= $(this).attr('data-edition');
                     var itemSelf		  	= $(this);
                     
                     var rand       		= Math.floor((Math.random()*100)+1);                    
                     var link        		= $(this).attr('href') + '&identified_name=' + identifiedName + '&edition=' + edition;
                     var iframeID    		= 'iframe-download-plugin-modal-' + rand;   
                     var isInstalled		= $(this).attr('data-install'); 
                     
                     if (authentication == 1)
                     {
                        if (isInstalled == 0)
                        {
                            var title       = self.lang["JSN_UNIFORM_INTEGRATION_INSTALL_TITLE"];
                            var pluginModalWindow = new JSNModal({
                                width: 800,
                                height: 550,
                                url: link,
                                title: title,
                                scrollable: true,
                                buttons:[
                                    {
                                        text: self.lang["JSN_UNIFORM_CANCEL"],
                                        class: 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
                                        click: $.proxy( function(){
                                        	pluginModalWindow.close();
                                        }, this)
                                    }
                                ],
                                open: function(){
                                    var iframeContent = $('#' + iframeID).contents();
                                    
                                    iframeContent.find('#jsn-install-cancel').click(function (e) {
                                    	pluginModalWindow.close();
                                    });
                                    
                                    self.downloadPlugin(iframeContent, identifiedName, pluginModalWindow, iframeID, true);
                                }
                            });
                        }
                        else
                        {
                            var title       = self.lang["JSN_UNIFORM_INTEGRATION_UPDATE_TITLE"];
                            var pluginModalWindow = new JSNModal({
                                width: 800,
                                height: 550,
                                url: link,
                                title: title,
                                scrollable: true,
                                open: function(){
                                    var iframeContent = $('#' + iframeID).contents();
                                    self.downloadPlugin(iframeContent, identifiedName, pluginModalWindow, iframeID, false);
                                }
                            });
                        }                    
                        
                        pluginModalWindow.iframe.attr('id', iframeID);
                        pluginModalWindow.iframe.css('overflow-x', 'hidden');
                        pluginModalWindow.show();                        
                     }
                     else
                     {
                    	 $(this).closest('td').find('.update-info').hide();
                    	 $(this).closest('td').find('.install-update-process').css('display', 'inline-block');
                    	 
                    	 var dataForm = [];
                    	 dataForm.push({'name':'identified_name', 'value': identifiedName});
                    	 dataForm.push({'name':'edition', 'value': edition});
                    	 dataForm.push({'name':'extension_name', 'value': pluginName});

                    	 $.ajax({
                    	 url: 'index.php?option=com_uniform&task=integration.confirm&' + token + '=1',
                    	 type: 'POST',
                    	 dataType: 'json',
                    	 data: dataForm,
                    	 success: function(data) {  
	                    		 if (data.type == 'success') 
	                    		 {
	                    			 self.installPlugin(data.path, identifiedName, token); 
	                    		 }
	                    		 else 
	                    		 {
	                    			 alert(data.message);
	                    			 itemSelf.closest('td').find('.update-info').show();
	                    			 itemSelf.closest('td').find('.install-update-process').css('display', 'none');
	                    		 }
	                    		 
	                    		 return false;
                    		}
                    	 });  
                    	 
                    	 return false; 
                     } 
                });
                
                // uninstall modal confirm
                $('.jsn-uf-page-integration').on('click', '.plugin_item_uninstall', function(event){
                	event.preventDefault();
                	var $this 				= $(this);
                    var rand 				= Math.floor((Math.random()*100)+1);
                    var selfSelect 			= this;
                    var identifiedName		= $(this).attr('data-identified-name');  
                    var id					= $(this).attr('data-ext-id');
                    var link        		= $(this).attr('href') + '&identified_name=' + identifiedName + '&extension_id=' + id;
                    var title 				= self.lang["JSN_UNIFORM_PLUGIN_UNINSTALL_TITLE"];
                    var iframeID 			= 'iframe-plugin-item-uninstall-modal-' + rand;
                    selfSelect.uninstallModal = new JSNModal({
                    	width: 800,
                        height: 550,
                        url: link,
                        title: title,
                        scrollable: true,
                        open: function(){
                            var iframeContent = $('#' + iframeID).contents();
                            self.removePlugin(iframeContent, identifiedName, id, iframeID, selfSelect.uninstallModal, token, $this);
                        }
                    });
                    
                    selfSelect.uninstallModal.iframe.attr('id', iframeID);
                    selfSelect.uninstallModal.iframe.css('overflow-x', 'hidden');
                    selfSelect.uninstallModal.show();
                });
                
                
            },
            removePlugin:function(iframeContent, identifiedName, id, iframeID, modal, token, $this)
            {
            	var self = this;
            	// uninstall plugin
            	iframeContent.on('click', '#jsn-uf-uninstall-cancel', function(event){
            		modal.close();
            		return false;
            	});           	
            	
           	 
            	iframeContent.on('click', '#jsn-uf-uninstall', function(event){
            		var textConfirm = self.lang["JSN_UNIFORM_PLUGIN_UNINSTALL_CONFIRM"];
            		if (!window.confirm(textConfirm)) 
            		{
            			modal.close();
            			return false;
        		    }
                    modal.close();
                    $this.closest('td').find('.update-info').hide();
                    $this.closest('td').find('.install-update-process').css('display', 'inline-block');
               	 	
                    $.ajax({
                        url: 'index.php?option=com_uniform&task=integration.remove&' + token + '=1',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            plugin_id: parseInt(id)
                        },
                        success: function(data) { 
                            if (data.type == 'success') {
                            	location.reload(); 
                            } else {
                            	alert(data.message)
                            	$this.closest('td').find('.update-info').show();
                            	$this.closest('td').find('.install-update-process').css('display', 'none');
                            }
                            return false;
                        }
                    });
                    return false;
                });
            	return false;
            },
            savePaymentSettings:function(modal, iframeID){
                var iframe = $('#' + iframeID);
                
                var form = iframe.contents();
                
                var dataForm = [];
                var paymentGateway = $(form).find('.extension_name').val();

                $(form).find('input[name],select[name]').each(function(){
                    var item = {};
                    if($(this).attr('name') != undefined){
                        if($(this).attr('name') != 'controller'){
                            if($(this).attr('type') == 'radio'){
                                if($(this).is(':checked')){
                                    item.name = $(this).attr('name');
                                    item.value = $(this).val();
                                    dataForm.push(item);
                                }
                            }
                            else{
                                item.name = $(this).attr('name');
                                if($(this).attr('name') == 'ordering'){
                                    item.name = 'jform[' + $(this).attr('name') + ']';
                                }
                                item.value = $(this).val();
                                dataForm.push(item)
                            }
                        }
                    }
                });
                
                var extensionName = {};
                extensionName.name = 'jform[extension_name]';
                extensionName.value = paymentGateway;
                dataForm.push(extensionName);
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: 'index.php?option=com_uniform&view=paymentgatewaysettings&tmpl=component&task=paymentgatewaysettings.save',
                    data: dataForm,
                    success: function(reponse)
                    {
                        if(reponse)
                        {
                            if (reponse.result == 'success')
                            {
                                modal.close();
                            }
                            else
                            {
                                alert(reponse.message)
                            }
                        }
                    }
                })
            },
            installPlugin:function (path, id, token) {
                $('#'+id).closest('td').find('.update-info').hide();
                $('#'+id).closest('td').find('.install-update-process').css('display','inline-block');

                $.ajax({
                    url: 'index.php?option=com_uniform&task=integration.install&' + token + '=1',
                    type: 'POST',
                    dataType: 'json',
                    data:{
                        path:path
                    },
                    success: function(data) {   
                        if (data.type == 'error') 
                        {                            
                            alert(data.message);
                            location.reload();
                        } 
                        else 
                        {                            
                            location.reload(); 
                        }                        
                        return false;
                    }                    
                });  
            },             
            downloadPlugin:function(iframe_content, dataID, modal, iframeID, install){
                var self = this;              
                /*iframe_content.find('#jsn-update-cancel').click(function(){
                    modal.close();
                    return false;
                });*/
                
                iframe_content.on('click', '#jsn-install-cancel, #jsn-update-cancel', function(event){
                	modal.close();
                	return false;
                });
                
                if (install)
                {
                	var confirmButton = iframe_content.find('#jsn-install-next-login');
                }
                else
                {
                	var confirmButton = iframe_content.find('#jsn-update-next-login');
                }
                
                iframe_content.on('keyup change', '#username, #password', function(event) {
					self.customerInfo = {
						username: iframe_content.find('input[name="customer_username"]').val(),
						password: iframe_content.find('input[name="customer_password"]').val()
					};

					if (self.customerInfo.username != '' && self.customerInfo.password != '') {
						confirmButton.removeAttr('disabled');
					} else {
						confirmButton.attr('disabled', 'disabled');
					}
				});
                
                iframe_content.on('click', '#jsn-install-next-login, #jsn-update-next-login', function(event){
                    event.preventDefault();
                    var iframe = $('#' + iframeID);                
                    var form = iframe.contents();
                    var dataForm = [];
                    
                    $(form).find('input[name],select[name]').each(function(){
                        var item = {};
                        if($(this).attr('name') != undefined){
                            if($(this).attr('name') != 'controller'){
                                if($(this).attr('type') == 'radio'){
                                    if($(this).is(':checked')){
                                        item.name = $(this).attr('name');
                                        item.value = $(this).val();
                                        dataForm.push(item);
                                    }
                                }
                                else
                                {
                                    item.name = $(this).attr('name');
                                    if($(this).attr('name') == 'ordering'){
                                        item.name = 'jform[' + $(this).attr('name') + ']';
                                    }
                                    item.value = $(this).val();
                                        dataForm.push(item)
                                }
                            }
                        }
                    });
                    
                    var token = $(form).find('input[name="token"]').val();
                    self.showOverlay(iframeID);	
                    $.ajax({
                        url: 'index.php?option=com_uniform&task=integration.confirm&' + token + '=1',
                        type: 'POST',
                        dataType: 'json',
                        data: dataForm,
                        success: function(data) {   
                            if (data.type == 'success') {
                                self.installPlugin(data.path, dataID, token);
                                self.hideOverlay(iframeID);
                                modal.close();
                            } else {
                                alert(data.message);
                                self.hideOverlay(iframeID);
                            }
                            return false;
                        }
                    });  
                    return false;
                });               
            },
            showOverlay: function(iframeID) {
            	var iframe = $('#' + iframeID);
                 
                var form = iframe.contents();
                 
                if (!form.find('.jsn-modal-overlay').length) 
                {
                	form.find("body").append($("<div/>", {
                        "class":"jsn-modal-overlay",
                        "style":"z-index: 1000; display: inline;"
                    })).append($("<div/>", {
                        "class":"jsn-modal-indicator",
                        "style":"display:block"
                    })).addClass("jsn-loading-page");
                    
                }
                $('#' + iframeID).find('.jsn-modal-overlay').show();
                $('#' + iframeID).find('.jsn-modal-indicator').show();
                
            },
            
            hideOverlay: function(iframeID) {
            	var iframe = $('#' + iframeID);               
                var form = iframe.contents();            	
                form.find('.jsn-modal-overlay').remove();
                form.find('.jsn-modal-indicator').remove();
            }            
        }
        return JSNUniformIntegration;
    });
