/*------------------------------------------------------------------------
 # Full Name of JSN UniForm
 # ------------------------------------------------------------------------
 # author    JoomlaShine.com Team
 # copyright Copyright (C) 2016 JoomlaShine.com. All Rights Reserved.
 # Websites: http://www.joomlashine.com
 # Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 # @license - GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 # @version $Id: configuration.js 19013 2012-11-28 04:48:47Z thailv $
 -------------------------------------------------------------------------*/
define([
    'jquery',
    'uniform/uniform',
    'uniform/help',
    'jsn/libs/modal',
    'jquery.ui',
    'uniform/dialogedition',
    'jquery.jwysiwyg09',
    'jquery.wysiwyg.colorpicker',
    'jquery.wysiwyg.table',
    'jquery.wysiwyg.cssWrap',
    'jquery.wysiwyg.image',
    'jquery.wysiwyg.link',
    'jquery.json',
    'jquery.ui',
    'jquery.tipsy' ],
    function ($, JSNUniform, JSNHelp, JSNModal, JSNUniformDialogEdition) {
        var JSNUniformConfigView = function (options) {
            this.options = $.extend({
                currentAction:currentAction
            }, options);
            this.lang = options.language;
            this.actionClasses = ['no-action', 'redirect-to-url', 'redirect-to-menu', 'show-article', 'show-message'];
            this.init();
        }
        JSNUniformConfigView.prototype = {
            init:function () {
                var self = this;
                this.actionSelect = $('#jsnconfigform_action');
                this.actionPanel = $('#form-default-settings');
                this.btnConfigSave = $("button[value='configuration.save']");
                this.btnapplyFolder = $("#apply-folder");
                this.inputFolderUpload = $("#jsnconfig_folder_upload");
                this.imgLoading = $("#jsn-apply-folder-loading");
                this.registerEvents();
                this.updateAction(this.options.currentAction);
                this.JSNUniform = new JSNUniform(this.options);
                this.JSNHelp = new JSNHelp();
                this.btnapplyFolder.click(function () {
                    self.applyFolder();
                });
                this.inputFolderUpload.bind('keypress', function (e) {
                    if (e.keyCode == '13') {
                        self.applyFolder();
                        return false;
                    }
                });
				/*
                if (this.options.edition.toLowerCase() == "free") {
                    $('input[name="jsnconfig[disable_show_copyright]"]').attr('disabled', 'disabled')
                    $('input[name="jsnconfig[disable_show_copyright]"]').parents('#jsnconfig_disable_show_copyright1-lbl').after(
                        $("<span />", {
                            "class": "label label-important label-pro",
                            "text": "PRO",
                            "style": "margin-left:10px"
                        })
                    );
                }
				*/
                //get menu item
                window.jsnGetSelectMenu = function (id, title, object, link) {
                    var valueMenu = new Object();
                    valueMenu.id = id;
                    valueMenu.title = title;
                    $("#jsnconfig_form_action_menu_title").val(title);
                    $("#jsnconfig_form_action_menu").val($.toJSON(valueMenu));
                    $.closeModalBox();
                };
                // get article
                window.jsnGetSelectArticle = function (id, title, catid, object, link) {
                    var valueArticle = new Object();
                    valueArticle.id = id;
                    valueArticle.title = title;
                    $("#jsnconfig_form_action_article_title").val(title);
                    $("#jsnconfig_form_action_article").val($.toJSON(valueArticle));
                    $.closeModalBox();
                };
                if (this.options.edition.toLowerCase() == "free") {
                    $("input[name='jsnconfig[disable_show_copyright]']").click(function () {
                        if ($(this).val() == 0) {
                            self.JSNUniformDialogEdition = new JSNUniformDialogEdition(self.options);
                            JSNUniformDialogEdition.createDialogLimitation($(this), self.lang["JSN_UNIFORM_YOU_CAN_NOT_HIDE_THE_COPYLINK"]);
                            return false;
                        }
                    });
                } else {
                    $("#jsnconfig-disable-show-copyright-field").remove();
                }
                $('.icon-question-sign').tipsy({
                    gravity:'w',
                    fade:true
                });
                if (this.options.editor == 'tinymce') {
                    tinymce.init({
                        selector: '#jsnconfig_form_action_message',
                        // General
                        directionality: 'ltr',
                        language: 'en',
                        mode: "specific_textareas",
                        autosave_restore_when_empty: false,
                        skin: "lightgray",
                        theme: "modern",
                        schema: "html5",
                        // Cleanup/Output
                        inline_styles: true,
                        gecko_spellcheck: true,
                        entity_encoding: "raw",
                        valid_elements: "",
                        extended_valid_elements: "hr[id|title|alt|class|width|size|noshade]",
                        force_br_newlines: false, force_p_newlines: true, forced_root_block: 'p',
                        toolbar_items_size: "small",
                        invalid_elements: "script,applet,iframe",
                        // Plugins
                        plugins: "table link image code hr charmap autolink lists importcss",
                        // Toolbar
                        toolbar1: "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | formatselect | bullist numlist",
                        toolbar2: "outdent indent | undo redo | link unlink anchor image code | hr table | subscript superscript | charmap",
                        removed_menuitems: "newdocument",
                        // URL
                        relative_urls: false,
                        remove_script_host: false,
                        //document_base_url : document_base_url,
                        // Layout
                        importcss_append: true,
                        // Advanced Options
                        resize: "both",
                        setup: function (ed) {
                            ed.on('keyup', function (e) {
                                $('#jsnconfig_form_action_message').text($.trim(tinymce.get($(this).attr("id")).getContent())).trigger('change')

                            });
                            ed.on('change', function (e) {
                                $('#jsnconfig_form_action_message').text($.trim(tinymce.get($(this).attr("id")).getContent())).trigger('change')
                            });
                        }
                    });
                }
                
                else if(this.options.editor == 'jce')
                {
                    var etag              = this.options.jce_etag;
                	var editorId          = $('[name="jsnconfig[form_action_message]"]').attr('id');
                    var jce_token         = this.options.jce_token;
                    var jce_toolbar       = this.options.jce_toolbar;
                    var componentId       = this.options.component_id;
                    var currentElement    = $('#' + editorId);
                    var document_base_url = this.options.jce_base_url;
                    $(this).css('opacity', 0);
                    try{
                    WFEditor.init({
                        editor_selector: 'custom_mes_editor',
                        toggle: false,
                        setup: function (ed) {
                            ed.onChange.add(function(ed, l) {
                                currentElement.html(l.content);
                                try
                                {
                                    setTimeout(function(){
                                        var content = tinyMCE.activeEditor.getContent({format : 'raw'});
                                        currentElement.html(content).trigger('change')
                                    }, 100)
                                }
                                catch (err)
                                {
                                    console.log(err)
                                }
                            });
                            ed.onKeyDown.add(function(ed, l) {
                                currentElement.html(l.content);
                                try
                                {
                                    setTimeout(function(){
                                        var content = tinyMCE.activeEditor.getContent({format : 'raw'});
                                        currentElement.html(content).trigger('change')
                                    }, 100)
                                }
                                catch (err)
                                {
                                    console.log(err)
                                }
                            });
                            ed.onInit.add(function(ed) {
                                $("#jsnconfig_form_action_message_tbl").css("width", "95%");
                            });
                        },
                        skin: jce_toolbar,
                        base_url: document_base_url,
                        language: 'en',
                        directionality: this.options.jce_directionality,
                        token: jce_token,
                        etag: etag,
                        theme: "advanced",
                        plugins: "autolink,cleanup,core,code,colorpicker,upload,format,charmap,contextmenu,browser,inlinepopups,media,clipboard,searchreplace,directionality,fullscreen,preview,source,table,textcase,print,style,nonbreaking,visualchars,visualblocks,xhtmlxtras,imgmanager,anchor,link,spellchecker,article,lists,formatselect,styleselect,fontselect,fontsizeselect,fontcolor,importcss,advlist,wordcount",
                        language_load: false,
                        component_id: componentId,
                        theme_advanced_buttons1: "newdocument,undo,redo,|,bold,italic,underline,strikethrough,justifyfull,justifycenter,justifyleft,justifyright,|,blockquote,formatselect,styleselect,removeformat,cleanup",
                        theme_advanced_buttons2: "fontselect,fontsizeselect,forecolor,backcolor,|,cut,copy,paste,pastetext,indent,outdent,numlist,bullist,sub,sup,textcase,charmap,hr",
                        theme_advanced_buttons3: "ltr,rtl,source,search,replace,|,table_insert,delete_table,|,row_props,cell_props,|,row_before,row_after,delete_row,|,col_before,col_after,delete_col,|,split_cells,merge_cells",
                        theme_advanced_buttons4: "visualaid,visualchars,visualblocks,nonbreaking,cite,abbr,acronym,del,ins,attribs,anchor,unlink,link,imgmanager,spellchecker",
                        theme_advanced_resizing: true,
                        content_css: "/templates/protostar/css/template.css?d9fd6ea4b9c3aab88acbafa5d60f2a6f",
                        schema: "mixed",
                        invalid_elements: "iframe,script,style,applet,body,bgsound,base,basefont,frame,frameset,head,html,id,ilayer,layer,link,meta,name,title,xml",
                        remove_script_host: false,
                        file_browser_callback: function (name, url, type, win) {
                            tinyMCE.activeEditor.plugins.browser.browse(name, url, type, win);
                        },
                        source_theme: "codemirror",
                        imgmanager_upload: {"max_size": 1024, "filetypes": ["jpg", "jpeg", "png", "gif"]},
                        spellchecker_engine: "browser",
                        formatselect_blockformats: {
                            "advanced.paragraph": "p",
                            "advanced.div": "div",
                            "advanced.div_container": "div_container",
                            "advanced.address": "address",
                            "advanced.pre": "pre",
                            "advanced.h1": "h1",
                            "advanced.h2": "h2",
                            "advanced.h3": "h3",
                            "advanced.h4": "h4",
                            "advanced.h5": "h5",
                            "advanced.h6": "h6",
                            "advanced.code": "code",
                            "advanced.samp": "samp",
                            "advanced.span": "span",
                            "advanced.section": "section",
                            "advanced.article": "article",
                            "advanced.aside": "aside",
                            "advanced.figure": "figure",
                            "advanced.dt": "dt",
                            "advanced.dd": "dd"
                        },
                        fontselect_fonts: "Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats",
                        compress: {"javascript": 1, "css": 1}
                    });}catch(e){console.debug(e);}

                    currentElement.attr('id', editorId);

                }
                else {
                    $("#jsnconfig_form_action_message").wysiwyg({
                        controls: {
                            bold: {visible: true},
                            italic: {visible: true},
                            underline: {visible: true},
                            strikeThrough: {visible: true},
                            justifyLeft: {visible: true},
                            justifyCenter: {visible: true},
                            justifyRight: {visible: true},
                            justifyFull: {visible: true},
                            indent: {visible: true},
                            outdent: {visible: true},
                            subscript: {visible: true},
                            superscript: {visible: true},
                            undo: {visible: true},
                            redo: {visible: true},
                            insertOrderedList: {visible: true},
                            insertUnorderedList: {visible: true},
                            insertHorizontalRule: {visible: true},
                            h4: {
                                visible: true,
                                className: 'h4',
                                command: ($.browser.msie || $.browser.safari) ? 'formatBlock' : 'heading',
                                arguments: ($.browser.msie || $.browser.safari) ? '<h4>' : 'h4',
                                tags: ['h4'],
                                tooltip: 'Header 4'
                            },
                            h5: {
                                visible: true,
                                className: 'h5',
                                command: ($.browser.msie || $.browser.safari) ? 'formatBlock' : 'heading',
                                arguments: ($.browser.msie || $.browser.safari) ? '<h5>' : 'h5',
                                tags: ['h5'],
                                tooltip: 'Header 5'
                            },
                            h6: {
                                visible: true,
                                className: 'h6',
                                command: ($.browser.msie || $.browser.safari) ? 'formatBlock' : 'heading',
                                arguments: ($.browser.msie || $.browser.safari) ? '<h6>' : 'h6',
                                tags: ['h6'],
                                tooltip: 'Header 6'
                            },
                            html: {visible: true},
                            increaseFontSize: {visible: true},
                            decreaseFontSize: {visible: true}
                        }
                    });
                }

            },
            //Submit ajax folder , check permission and create folder
            applyFolder:function () {
                var self = this;
                this.imgLoading.show();
                $("#message-apply").html("");
                var spanMessage = $("#message-apply");
                spanMessage.hide();
                $.ajax({
                    type:"POST",
                    dataType:'json',
                    url:"index.php?option=com_uniform&task=configuration.checkFolderUpload",
                    data:{
                        folder_tmp:$("#jsnconfig_folder_upload").val(),
                        folder_old:$("#folder_upload_old").val()
                    },
                    success:function (response) {
                        self.imgLoading.hide();
                        spanMessage.show();
                        if (response.success === true) {
                            spanMessage.attr("class", "label label-success").text(response.message);
                            $("#folder_upload_old").val($("#jsnconfig_folder_upload").val());
                        } else {
                            spanMessage.attr("class", "label label-important").text(response.message);
                        }
                        spanMessage.delay(3600).fadeOut(400);
                        self.btnConfigSave.removeAttr("disabled");
						$('#apply-folder').parent().parent().parent().parent().find('button').each(function (){
							if($(this).attr('value') === 'configuration.save'){
								$(this).trigger('click');
							}
						})	
                    }
                });
            }, //Register events
            registerEvents:function () {
                var self = this;
                this.actionSelect.bind('change', function () {
                    self.updateAction($(this).val());
					$('#jsnconfig-form-action-message-field').find('.createLink').each(function(){
						$(this).click(function (){
							$('.wysiwyg').find('legend').each(function (){
								$(this).parent().parent().parent().find('.ui-dialog-titlebar').css('display','none');
								$(this).css({'color':'#FFF','background':'#333','font-size':'18px','font-weight':'bold','padding':'5px 10px 0px 11px'})
								$(this).parent().find('label').css({'text-align':'right'});
							})
						})
					})
                })

                $('.jsn-page-configuration').on('click', '.payment_item_edit', function(event){
                    event.preventDefault();
                    var rand 	= Math.floor((Math.random()*100)+1);
                    var selfSelect = this;
                    var link = $(this).attr('href');
                    var title = 'Payment Gateway Settings';
                    var iframeID = 'iframe-payment-settings-modal-' + rand;
                    selfSelect.modal = new JSNModal({
                        width:$(window).width()*0.9,
                        height:$(window).height() *0.85,
                        url: link,
                        title: title,
                        scrollable: true,
                        buttons:[
                            {
                                text:'Save',
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
                                text:'Cancel',
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
            },
            savePaymentSettings: function(modal, iframeID){
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
            //Update action select box
            updateAction:function (actionIndex) {

                this.actionPanel.removeClass(this.actionClasses.join(' '));
                this.actionPanel.addClass(this.actionClasses[actionIndex]);
            }
        }
        return JSNUniformConfigView;
    });
