<?php
// no direct access
defined('_JEXEC') or die;
?>
<style type="text/css">
    .samlogin-dash-leftcontent-medium{float:left;  min-width: 300px; max-width: 70%;}
    .samlogin-dash-leftcontent{float:left;  min-width: 300px; max-width: 100%;}
    .samlogin-dash-leftcontent-large{float:left;  min-width: 90%; max-width: 100%;}
    .samlogin-dash-minipanel{min-width: 250px; width: 100%; max-width: 300px; }
    .samlogin-dash-minipanel-left{float:left;  margin-right: 10px;}
    .samlogin-dash-minipanel-right{float:right;  margin-left: 10px;}
    .samlogin-dash-minipanel-bottom{}

    #tab-settings-content_intab{

        width: 550px;

    }
    /*
    
        .settingsFieldsetTabs{
            float:left; margin-right: 5px;
            height: 500px;
        }
    
    
        #tab-settings-content{
            float: none;
              height: 500px;
              overflow-y: scroll;
              overflow-x: visible;
              padding-left: 5px;
              padding-right: 7px;
        }
        /* when non modal
        #paramsForm{
            width: 800px;
            
        }*/
    /*
     #paramsForm{
        width: 100% !important;
        
    }
    */

    @media (max-width:767px){
        .settingsFieldsetTabs{
            float:none; 
            height: inherit;
        }
        #tab-settings-content{
            float: none;
            /*     width: 700px; */
            height: inherit;
            overflow-y: auto;
            overflow-x: auto;
            padding-left: 5px;
        }
    }

    @media (min-width:700px){
        .samlogin-dash-main{
            width: 670px;
        }

    }

    @media (max-width:1446px){
        .samlogin-version-panel{
            clear: right !important;
            margin-top: 10px;
            /*   position: fixed; */
        }


    }

    @media (max-width:1098px){
        .samlogin-dash-main,.samlogin-dash-minipanel-bottom{
            clear: both !important;
            margin: 10px;
            width: 100%; 
            max-width: 100%;
            min-width: 100%;
            padding: 20px;
            float: left !important;
            -o-box-sizing: border-box;
            -ms-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            -khtml-box-sizing: border-box;
            box-sizing: border-box;
        }
    }


    @media (max-width:1083px){
        .samlogin-dash-minipanel-right{
            clear: right;
            margin-top: 10px;
        }
        .samlogin-dash-minipanel-right:first-child{
            margin-top: 0px;
        }
    }

    @media (max-width:729px){
        .samlogin-dash-minipanel{
            clear: both !important;
            margin: 10px;
            width: 100%; 
            max-width: 100%;
            min-width: 100%;
            padding: 20px;
            float: left !important;
            -o-box-sizing: border-box;
            -ms-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            -khtml-box-sizing: border-box;
            box-sizing: border-box;
        }


    }

    /* hide Joomla 2.5 huglyness*/
    div#element-box div.m {
        padding: 0px;
        background: transparent;
        border: none;
    }




</style>
<script type="text/javascript">

    window.maildiscojsonruleprogressive = 0;
    function getMailDiscoRuleEditorNewLine(regexp, idp) {
        maildiscojsonruleprogressive++;
        var toret = "<li class='maildiscorule maildiscorule_" + maildiscojsonruleprogressive + "'>" +
                "<table><tr><td rowspan=2><i class='drag-handle uk-icon-bars'></i><td colspan=2>" +
                "<input type='text' class='regexp' value='" + regexp + "' placeholder='regexp'>" +
                "<select class='idp' >" + jQuery("#jformsspas_idpentityid").html() + "</select>" +
                "<i class='js-remove uk-icon-times'></i></td></tr></table>" +
                "</li>";
        console.debug(regexp,idp);
        setTimeout(function(){
           console.debug(maildiscojsonruleprogressive,regexp,idp);    
        jQuery(".maildiscorule_" + maildiscojsonruleprogressive + " .idp").val(idp);
    },50);
        return toret;
    }

    function toJSONMailDiscoRuleEditor() {
        var arr = [];
        jQuery(".maildiscorule").each(function (i, e) {
            var regexp = jQuery(".regexp", e).val();
            var idp = jQuery(".idp", e).val();
            arr.push({regex: regexp, entity: idp});
        });
        jQuery("#jform_maildiscoverysettings").attr("readonly", "false");
        var textarea = jQuery("#jform_maildiscoverysettings");
        textarea.val(JSON.stringify(arr));
        jQuery("#jform_maildiscoverysettings").attr("readonly", "true");
    }

    function addDiscoRuleEditor() {
        jQuery("#maildiscoeditor").append(getMailDiscoRuleEditorNewLine("", ""));
        jQuery(".maildiscoeditor .regexp").on("change", toJSONMailDiscoRuleEditor);
        jQuery(".maildiscoeditor .idp").on("change", toJSONMailDiscoRuleEditor);
    }

    function fromJSONMailDiscoRuleEditor() {
        var arr = JSON.parse(jQuery("#jform_maildiscoverysettings").val());
        var html = "";
        jQuery.each(arr, function (i, e) {
            html += getMailDiscoRuleEditorNewLine(e.regex, e.entity);
        });
        jQuery("#maildiscoeditor").html(html);
        jQuery(".maildiscoeditor .regexp").on("change", toJSONMailDiscoRuleEditor);
        jQuery(".maildiscoeditor .idp").on("change", toJSONMailDiscoRuleEditor);
    }

    function prepareJSONMailDiscoRuleEditor() {
        // Editable list
        var textarea = jQuery("#jform_maildiscoverysettings").attr("readonly", "true");
        var json = jQuery.parseJSON(textarea.val());
        var li = "<ul id='maildiscoeditor' class='maildiscoeditor uk-list uk-list-striped'>";
        li +=
                // getMailDiscoRuleEditorNewLine("","")+
                // getMailDiscoRuleEditorNewLine("","")+
                "</ul><a class='uk-button uk-button-success' onClick='addDiscoRuleEditor();'>+ add rule</a><hr/><small>RESULTING JSON RULESET</small>";
        if (jQuery('#maildiscoeditor').length == 0) {
            var jsonEditor = textarea.parent().prepend(
                    li);
        }
        var editable = jQuery('#maildiscoeditor').get(0);
        var editableList = Sortable.create(maildiscoeditor, {
            filter: '.js-remove',
            handle: '.drag-handle',
            animation: 150,
            onFilter: function (evt) {
                var el = editableList.closest(evt.item); // get dragged item
                el && el.parentNode.removeChild(el);
                toJSONMailDiscoRuleEditor();
            },
            onSort: function (evt) {
                toJSONMailDiscoRuleEditor();
            },
            onEnd: function (evt) {
                toJSONMailDiscoRuleEditor();
            }
        });
        jQuery(".maildiscoeditor .regexp").on("change", toJSONMailDiscoRuleEditor);
        jQuery(".maildiscoeditor .idp").on("change", toJSONMailDiscoRuleEditor);
    }

    jQuery(document).ready(function () {
        prepareJSONMailDiscoRuleEditor();
        fromJSONMailDiscoRuleEditor();
    });
    function samlogin_doConfigTests() {

        jQuery(".statusOfCheck").html("<i class='uk-icon-refresh uk-icon-spin'></i>");
        jQuery(".configIsInSync").html("<i class='uk-icon-refresh uk-icon-spin'></i>");
        jQuery.ajax({
            url: window.samloginBaseAjaxURL,
            data: {
                task: "doConfigTests"
            },
            beforeSend: function (xhr) {
                //xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
            },
            dataType: 'json'
        }).done(function (data) {

            if (data == undefined || data == null || data == "" || data.additionalMessages == undefined) {
                data = {};
                data.additionalMessages = [{msg: "The server is not responding to AJAX requests, maybe your Joomla session expired or an error occurred, try to reload the page", level: "danger"}];
            }

            if (console && console.debug) {
                console.debug(data);
            }

            samlogin_processMessages(data);
            if (data.cronLink) {
                jQuery(".cronLink").attr("href", data.cronLink);
            }
            if (data.cronSuggestion) {
                jQuery(".cronSuggestion").html(data.cronSuggestion);
            }

            if (data.sessionHandler == "database") {
                jQuery(".sessionHandler .statusOfCheck").html("<i class='uk-icon-check'></i>" + data.sessionHandler).removeClass("uk-button-warning").removeClass("uk-button-primary").addClass("uk-button-success");
            } else {
                jQuery(".sessionHandler .statusOfCheck").html("<i class='uk-icon-check'></i>" + data.sessionHandler).addClass("uk-button-warning").removeClass("uk-button-primary").removeClass("uk-button-success");
            }
            if (data.sspCheck !== false) {
                jQuery(".sspCheck .statusOfCheck").html("<i class='uk-icon-check'></i>" + data.sspCheck).removeClass("uk-button-danger").removeClass("uk-button-primary").addClass("uk-button-success");
                jQuery(".sspCheck .guideLink").html("<div style='cursor:pointer;' class='uk-button uk-button-danger uk-button-mini' data-uk-modal=\"{target:'#install-ssp-modal'}\">reinstall</div>");
            } else {
                jQuery(".sspCheck .statusOfCheck").html("<i class='uk-icon-times'></i> Not installed")
                        .removeClass("uk-button-primary").removeClass("uk-button-success").addClass("uk-button-danger");
                jQuery(".sspCheck .guideLink").html("<div style='cursor:pointer;' class='uk-button uk-button-success' data-uk-modal=\"{target:'#install-ssp-modal'}\">install now</div>");
            }

            if (data.configIsInSync != undefined) {
                if (data.configIsInSync !== false) {
                    jQuery(".sspCheck .configIsInSync").removeClass("uk-button-danger").addClass("uk-button-success")
                            .html("<i class='uk-icon-check'> </i>").off("click");
                } else {
                    jQuery(".sspCheck .configIsInSync").css("cursor", "pointer").removeClass("uk-button-success").addClass("uk-button-danger")
                            .html("<i class='uk-icon-warning'></i> conf. not in sync").on("click", function (e) {
                        samlogin_saveSSPConf();
                    });
                }
            }

            if (data.keyrotation) {
                jQuery(".keyRotation .statusOfCheck").html("<i class='uk-icon-warning'></i> Key Rotation is ON").removeClass("uk-button-danger").removeClass("uk-button-success").addClass("uk-button-primary");
            } else {
                jQuery(".keyRotation .statusOfCheck").html("<i class='uk-icon-check'></i> Key Rotation is OFF")
                        .removeClass("uk-button-primary").removeClass("uk-button-danger").addClass("uk-button-success");
            }


            if (data.baseurlpath !== false) {
                if (data.baseurlpath !== true) {
                    jQuery(".baseURLPath .statusOfCheck").html("<i class='uk-icon-warning'></i> " + data.baseurlpath).removeClass("uk-button-success").removeClass("uk-button-primary").addClass("uk-button-danger");
                } else {
                    jQuery(".baseURLPath .statusOfCheck").html("<i class='uk-icon-check'></i> ")
                            .removeClass("uk-button-primary").addClass("uk-button-success").removeClass("uk-button-danger");
                }
                //jQuery(".baseURLPath .statusOfCheck").html("<i class='uk-icon-check'></i>").removeClass("uk-button-danger").removeClass("uk-button-primary").addClass("uk-button-success");
            } else {
                jQuery(".baseURLPath .statusOfCheck").html("<i class='uk-icon-times'></i>")
                        .removeClass("uk-button-primary").removeClass("uk-button-success").addClass("uk-button-danger");
            }

            if (data.userPlugin !== false) {
                jQuery(".userPlugin .statusOfCheck").html("<i class='uk-icon-check'></i> " + data.userPlugin).removeClass("uk-button-danger").removeClass("uk-button-primary").addClass("uk-button-success");
            } else {
                jQuery(".userPlugin .statusOfCheck").html("<i class='uk-icon-times'></i>")
                        .removeClass("uk-button-primary").removeClass("uk-button-success").addClass("uk-button-danger");
            }

            if (data.authPlugin !== false) {
                jQuery(".authPlugin .statusOfCheck").html("<i class='uk-icon-check'></i> " + data.authPlugin).removeClass("uk-button-danger").removeClass("uk-button-primary").addClass("uk-button-success");
            } else {
                jQuery(".authPlugin .statusOfCheck").html("<i class='uk-icon-times'></i>")
                        .removeClass("uk-button-primary").removeClass("uk-button-success").addClass("uk-button-danger");
            }

            if (data.secretsaltChanged !== false) {
                jQuery(".secretSalt .statusOfCheck").html("<i class='uk-icon-check'></i> ").removeClass("uk-button-danger").removeClass("uk-button-primary").addClass("uk-button-success");
            } else {
                jQuery(".secretSalt .statusOfCheck").html("<i class='uk-icon-times'></i>")
                        .removeClass("uk-button-primary").removeClass("uk-button-success").addClass("uk-button-danger");
            }
            if (data.adminpassChanged !== false) {
                jQuery(".adminPass .statusOfCheck").html("<i class='uk-icon-check'></i> ").removeClass("uk-button-danger").removeClass("uk-button-primary").addClass("uk-button-success");
            } else {
                jQuery(".adminPass .statusOfCheck").html("<i class='uk-icon-times'></i>")
                        .removeClass("uk-button-primary").removeClass("uk-button-success").addClass("uk-button-danger");
            }
            if (data.privKeyChanged !== false) {
                jQuery(".certChanged .statusOfCheck").html("<i class='uk-icon-check'></i> ").removeClass("uk-button-danger").removeClass("uk-button-primary").addClass("uk-button-success");
            } else {
                jQuery(".certChanged .statusOfCheck").html("<i class='uk-icon-times'></i>")
                        .removeClass("uk-button-primary").removeClass("uk-button-success").addClass("uk-button-danger");
            }


            if (data.metadataPublished !== false) {
                if (data.metadataPublished !== true) {
                    jQuery(".metadataPublished .statusOfCheck").html("<i class='uk-icon-warning'></i> " + data.metadataPublished).removeClass("uk-button-success").removeClass("uk-button-primary").addClass("uk-button-danger");
                } else {
                    jQuery(".metadataPublished .statusOfCheck").html("<i class='uk-icon-check'></i> ")
                            .removeClass("uk-button-primary").addClass("uk-button-success").removeClass("uk-button-danger");
                }
            } else {
                jQuery(".metadataPublished .statusOfCheck").html("<i class='uk-icon-times'></i>")
                        .removeClass("uk-button-primary").removeClass("uk-button-success").addClass("uk-button-danger");
            }


            if (data.metadataPublishedSSL !== false) {
                if (data.metadataPublished === false) {
                    jQuery(".metadataPublished .statusOfCheck").html("<i class='uk-icon-check'></i> No but SSL is enabled. ").removeClass("uk-button-danger").removeClass("uk-button-primary").addClass("uk-button-success");
                }
                if (data.metadataPublishedSSL !== true) {
                    jQuery(".metadataPublishedSSL .statusOfCheck").html("<i class='uk-icon-warning'></i> " + data.metadataPublishedSSL)
                            .removeClass("uk-button-primary").removeClass("uk-button-success").addClass("uk-button-danger");
                } else {
                    jQuery(".metadataPublished .statusOfCheck").html("<i class='uk-icon-check'></i> ").removeClass("uk-button-danger").removeClass("uk-button-primary").addClass("uk-button-success");
                    jQuery(".metadataPublishedSSL .statusOfCheck").html("<i class='uk-icon-check'></i> ").removeClass("uk-button-danger").removeClass("uk-button-primary").addClass("uk-button-success");
                }
            } else {
                jQuery(".metadataPublishedSSL .statusOfCheck").html("<i class='uk-icon-times'></i>")
                        .removeClass("uk-button-primary").removeClass("uk-button-success").addClass("uk-button-danger");
            }

            if (data.privatekey !== false) {
                if (data.privatekey === true) {
                    jQuery(".certProtected .statusOfCheck").html("<i class='uk-icon-check'></i> ").removeClass("uk-button-danger").removeClass("uk-button-primary").addClass("uk-button-success");
                } else {
                    jQuery(".certProtected .statusOfCheck").html(data.privatekey).removeClass("uk-button-danger").removeClass("uk-button-primary");
                }
            } else {
                jQuery(".certProtected .statusOfCheck").html("<i class='uk-icon-times'></i>")
                        .removeClass("uk-button-primary").removeClass("uk-button-success").addClass("uk-button-danger");
            }

            if (data.privatekeySSL !== false) {
                if (data.privatekeySSL === true) {
                    jQuery(".certProtected_ssl .statusOfCheck").html("<i class='uk-icon-check'></i> ").removeClass("uk-button-danger").removeClass("uk-button-primary").addClass("uk-button-success");
                } else {
                    jQuery(".certProtected_ssl .statusOfCheck").html(data.privatekeySSL).removeClass("uk-button-danger").removeClass("uk-button-primary");
                }
            } else {
                jQuery(".certProtected_ssl .statusOfCheck").html("<i class='uk-icon-times'></i>")
                        .removeClass("uk-button-primary").removeClass("uk-button-success").addClass("uk-button-danger");
            }



            if (data.logswww !== false) {
                if (data.logswww === true) {
                    jQuery(".logsProtected .statusOfCheck").html("<i class='uk-icon-check'></i> ").removeClass("uk-button-danger").removeClass("uk-button-primary").addClass("uk-button-success");
                } else {
                    jQuery(".logsProtected .statusOfCheck").html(data.logswww).removeClass("uk-button-danger").removeClass("uk-button-primary");
                }

            } else {
                jQuery(".logsProtected .statusOfCheck").html("<i class='uk-icon-times'></i>")
                        .removeClass("uk-button-primary").removeClass("uk-button-success").addClass("uk-button-danger");
            }

            if (data.logswwws !== false) {
                if (data.logswwws === true) {
                    jQuery(".logsProtected_ssl .statusOfCheck").html("<i class='uk-icon-check'></i> ").removeClass("uk-button-danger").removeClass("uk-button-primary").addClass("uk-button-success");
                } else {
                    jQuery(".logsProtected_ssl .statusOfCheck").html(data.logswwws).removeClass("uk-button-danger").removeClass("uk-button-primary");
                }

            } else {
                jQuery(".logsProtected_ssl .statusOfCheck").html("<i class='uk-icon-times'></i>")
                        .removeClass("uk-button-primary").removeClass("uk-button-success").addClass("uk-button-danger");
            }


            if (data.metarefresh !== false) {
                jQuery(".usingMetarefresh .statusOfCheck").html("<i class='uk-icon-check'></i> ").removeClass("uk-button-danger").removeClass("uk-button-primary").addClass("uk-button-success");
            } else {
                jQuery(".usingMetarefresh .statusOfCheck").html("<i class='uk-icon-times'></i>")
                        .removeClass("uk-button-primary").removeClass("uk-button-success").addClass("uk-button-danger");
            }

            if (data.metarefreshSAML2IdpLastUpdate !== false && data.metarefreshSAML2IdpLastUpdate !== "Never") {
                jQuery(".lastCronjobUpdate .statusOfCheck").html("<i class='uk-icon-clock-o'></i> " + data.metarefreshSAML2IdpLastUpdate).removeClass("uk-button-danger").removeClass("uk-button-primary").addClass("uk-button-info");
            } else {
                jQuery(".lastCronjobUpdate .statusOfCheck").html("<i class='uk-icon-times'></i>")
                        .removeClass("uk-button-primary").removeClass("uk-button-success").addClass("uk-button-danger");
            }

            jQuery('.statusOfCheck .uk-icon-question-circle').parent().removeClass('uk-button-danger'); //this makes the metadata not red when check result is unknown

        }).fail(function (xhr, textStatus, err) {
            if (xhr.getResponseHeader("x-logged-in").toLowerCase() == "Maybe-In-Transition".toLowerCase()) {
                location.reload(true);
            } else {
                samlogin_showToaster("JSON response error: " + err, "warning");
                samlogin_showToaster("You may want to disable php error reporting in Joomla global configuration to be able to go forward", "warning");
            }
        });
    }
    jQuery(document).ready(function () {
        samlogin_doConfigTests();
        jQuery(".samloginParamForm").on("submit", function (e) {
            samlogin_saveSettings();
            e.preventDefault();
            return false;
        });
        jQuery(".samloginParamForm input,.samloginParamForm select,.samloginParamForm textarea").on("change", function (e) {
            jQuery(".saveSettingsButton").addClass("uk-button-primary");
        });
    });</script>
<!--
<aside>    
    <div class="samlogin-dash-minipanel  samlogin-dash-minipanel-left   samlogin-dash-leftcontent-medium uk-panel uk-panel-box">
        <div class="uk-text-bold"><i class='uk-icon-info-circle'></i> SAMLogin version: <div class="uk-button uk-button-success samloginVersion"><?php echo $this->version; ?></div></div>
        <div class="uk-text-bottom"><i>Version color :</i></div>
        <span class="uk-button uk-button-mini uk-button-success">&nbsp;&nbsp;</span> Stable  <span class="uk-button uk-button-mini uk-button-primary">&nbsp;&nbsp;</span> Beta/Custom <span class="uk-button uk-button-mini uk-button-danger">&nbsp;&nbsp;</span> Outdated
<!--<div class=" uk-button uk-button-mini uk-button-primary" id="SkypeButton"></div>
<script type='text/javascript'>
    jQuery(document).ready(function(){
        Skype.ui({'imageColor': 'white','imageSize': 24,'name': 'chat','element': 'SkypeButton','participants': ['rastrano']});
    });
</script>
-->
<!--

    </div>
</aside>
-->

<span id='samlogin_top' style="position:absolute; top: 0px;"></span>




<div id="mainSamloginAdminBlock" style="float: left;">
    <!-- uk-tab-left uk-width-medium-1-2 -->
    <ul  class="uk-tab " data-uk-tab="{connect:'#tab-content'}">
        <li class="uk-active"><a href="#"><i class="uk-icon-check-square-o"></i><i class="uk-icon-ellipsis-v"></i> Checklist</a></li>
        <li><a href=""><i class='uk-icon-cogs'></i> Settings</a></li>
        <li><a href=""><i class='uk-icon-wrench'></i> Tools </a></li>
        <li class="uk-disabled"><a href=""><i class='uk-icon-eye'></i> Logs</a></li>
    </ul>
    <ul id="tab-content" class="uk-switcher uk-margin">
        <li class="checklistTab">
            <div id="mainSamloginAdminBlock" style='margin-top: 0px; margin-bottom: 10px;  max-width: 670px;' class="samlogin-dash-leftcontent uk-panel uk-panel-box samlogin-dash-main">

                <h3 style='margin: 0px;'><i class='uk-icon-check-square-o'></i><i class='uk-icon-ellipsis-v'></i> Configuration checklist 
                    <div style='cursor:pointer;' onclick="samlogin_doConfigTests();" class="uk-button uk-button-primary uk-button-mini"><i class='uk-icon-refresh'></i> test now</div></h3>
                <hr style=" margin-top: 4px; margin-bottom: 6px;"/>
                <div class="">
                    <table class="uk-table uk-table-condensed uk-table-hover uk-table-striped">
                        <thead>
                            <tr>
                                <th class=""><span>Check</span></th>
                                <th class=""><span>Status</span></th>
                                <th class=""><span>Action / Help</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr></tr>

                            <tr class="sspCheck">
                                <td class="">Linked SimpleSAMLphp installation</td>
                                <td class=" "><span class='statusOfCheck uk-button uk-button-mini uk-button-primary'><i class='uk-icon-refresh uk-icon-spin'></i></span></td>
                                <td class=""><span class="guideLink"></span> <span style="margin-top: 2px;" class="configIsInSync uk-button uk-button-mini"></span></td>
                            </tr>
                            <tr class='sessionHandler'>
                                <td class=" ">Joomla Session Storage (Session Handler)</td>
                                <td class=" "><span class='statusOfCheck uk-button uk-button-mini uk-button-primary'><i class='uk-icon-refresh uk-icon-spin'></i></span></td>
                                <td class="   "><span class="guideLink">Suggested setting is `database` <i class='uk-icon-database'></i> <a  href='index.php?option=com_config#page-system'>set it here</a> <small>(changing will log all users out)</small></span></td>
                            </tr>
                            <?php if ($this->checks['sspCheck']) { ?>
                                <tr class='keyRotation'>
                                    <td class=" ">Key Rotation Status</td>
                                    <td class=" "><span class='statusOfCheck uk-button uk-button-mini uk-button-primary'><i class='uk-icon-refresh uk-icon-spin'></i></span></td>
                                    <td class="   "><span class="guideLink"><?php echo JText::_('SAMLOGIN_KEYROTATE_GUIDELINK'); ?></span></td>
                                </tr>
                                <tr class='baseURLPath'>
                                    <td class="samlogin-spare-check">SimpleSAML BaseURL Path</td>
                                    <td class=" "><span class='statusOfCheck uk-button uk-button-mini uk-button-primary'><i class='uk-icon-refresh uk-icon-spin'></i></span></td>
                                    <td class="   "><span class="guideLink"><?php echo JText::_('SAMLOGIN_BASEURLPATH_GUIDELINK'); ?></span></td>
                                </tr>
                                <tr class='authPlugin'>
                                    <td class="samlogin-auth-plugin">Auth Plugin Enabled</td>
                                    <td class=" "><span class='statusOfCheck uk-button uk-button-mini uk-button-primary'><i class='uk-icon-refresh uk-icon-spin'></i></span></td>
                                    <td class="   "><span class="guideLink"><?php echo JText::_('SAMLOGIN_AUTHPLUGIN_GUIDELINK'); ?></span></td>
                                </tr>
                                <tr class='userPlugin'>
                                    <td class="samlogin-auth-plugin">User Plugin Enabled</td>
                                    <td class=" "><span class='statusOfCheck uk-button uk-button-mini uk-button-primary'><i class='uk-icon-refresh uk-icon-spin'></i></span></td>
                                    <td class="   "><span class="guideLink"><?php echo JText::_('SAMLOGIN_AUTHPLUGIN_GUIDELINK'); ?></span></td>
                                </tr>
                                <tr class='secretSalt'>
                                    <td class="samlogin-cert-protected">SSP Secret Salt Changed</td>
                                    <td class=" "><span class='statusOfCheck uk-button uk-button-mini uk-button-primary'><i class='uk-icon-refresh uk-icon-spin'></i></span></td>
                                    <td class="   "><span class="guideLink"><?php echo JText::_('SAMLOGIN_SECRETSALT_GUIDELINK'); ?></span></td>
                                </tr>
                                <tr class='adminPass'>
                                    <td class="samlogin-adminpass">SSP AdminPass is disabled or changed from default</td>
                                    <td class=" "><span class='statusOfCheck uk-button uk-button-mini uk-button-primary'><i class='uk-icon-refresh uk-icon-spin'></i></span></td>
                                    <td class="   "><span class="guideLink"><?php echo JText::_('SAMLOGIN_ADMINPASS_GUIDELINK'); ?></span></td>
                                </tr>
                                <tr class='metadataPublished'>
                                    <td class="samlogin-cert-protected">Metadata URL is published</td>
                                    <td class=" "><span class='statusOfCheck uk-button uk-button-mini uk-button-primary'><i class='uk-icon-refresh uk-icon-spin'></i></span></td>
                                    <td class="   "><span class="guideLink"><?php echo JText::_('SAMLOGIN_SSL_GUIDELINK'); ?></span></td>
                                </tr>      
                                <tr class='metadataPublishedSSL'>
                                    <td class="samlogin-cert-protected">Metadata URL is published on SSL (HTTPS)</td>
                                    <td class=" "><span class='statusOfCheck uk-button uk-button-mini uk-button-primary'><i class='uk-icon-refresh uk-icon-spin'></i></span></td>
                                    <td class="   "><span class="guideLink"><?php echo JText::_('SAMLOGIN_SSL_GUIDELINK'); ?></span></td>
                                </tr>        
                                <tr class='certChanged'>
                                    <td class="samlogin-cert-changed">SAML endpoint's default certificate was changed</td>
                                    <td class=" "><span class='statusOfCheck uk-button uk-button-mini uk-button-primary'><i class='uk-icon-refresh uk-icon-spin'></i></span></td>
                                    <td class="   "><span class="guideLink"><?php echo JText::_('SAMLOGIN_PRIVATEKEY_GENERATE_GUIDELINK'); ?></span></td>
                                </tr>
                                <tr class='certProtected'>
                                    <td class="samlogin-cert-protected">SAML endpoint's private key is protected form www access</td>
                                    <td class=" "><span class='statusOfCheck uk-button uk-button-mini uk-button-primary'><i class='uk-icon-refresh uk-icon-spin'></i></span></td>
                                    <td class="   "><span class="guideLink"><?php echo JText::_('SAMLOGIN_PRIVATEKEY_PROTECT_GUIDELINK'); ?></span></td>
                                </tr>
                                <tr class='certProtected_ssl'>
                                    <td class="samlogin-cert-protected">SAML endpoint's private key is protected form www access (https)</td>
                                    <td class=" "><span class='statusOfCheck uk-button uk-button-mini uk-button-primary'><i class='uk-icon-refresh uk-icon-spin'></i></span></td>
                                    <td class="   "><span class="guideLink"><?php echo JText::_('SAMLOGIN_PRIVATEKEY_PROTECT_GUIDELINK'); ?></span></td>
                                </tr>
                                <tr class='logsProtected'>
                                    <td class="samlogin-cert-protected">SSP logs are protected form www access</td>
                                    <td class=" "><span class='statusOfCheck uk-button uk-button-mini uk-button-primary'><i class='uk-icon-refresh uk-icon-spin'></i></span></td>
                                    <td class="   "><span class="guideLink"><?php echo JText::_('SAMLOGIN_PRIVATEKEY_PROTECT_GUIDELINK'); ?></span></td>
                                </tr>
                                <tr class='logsProtected_ssl'>
                                    <td class="samlogin-cert-protected">SSP logs are protected form www access (https)</td>
                                    <td class=" "><span class='statusOfCheck uk-button uk-button-mini uk-button-primary'><i class='uk-icon-refresh uk-icon-spin'></i></span></td>
                                    <td class="   "><span class="guideLink"><?php echo JText::_('SAMLOGIN_PRIVATEKEY_PROTECT_GUIDELINK'); ?></span></td>
                                </tr>

                                <tr class='usingMetarefresh'>
                                    <td class=" ">Using Metarefresh</td>
                                    <td class=" "><span class='statusOfCheck uk-button uk-button-mini uk-button-primary'><i class='uk-icon-refresh uk-icon-spin'></i></span></td>
                                    <td class="   "><span class="guideLink"><?php echo JText::_('SAMLOGIN_METAREFRESH_GUIDELINK'); ?></span></td>
                                </tr>

                                <tr class='lastCronjobUpdate'>
                                    <td class=" ">Last metarefresh cronjob update</td>
                                    <td class=" "><span class='statusOfCheck uk-button uk-button-primary'><i class='uk-icon-refresh uk-icon-spin'></i></span></td>
                                    <td class="   "><span class="guideLink"><?php echo JText::_('SAMLOGIN_METAREFRESH_GUIDELINK'); ?></span></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </li> 

        <li class="SettingsTab">
            <div class="SettingsTab_Actionbar" style="position: relative;">  
                <?php if (version_compare(JVERSION, '3.0', 'ge')) { ?>
                    <div data-uk-sticky="{top:90}" style="text-align: right;
                <?php } else { ?>
                         <div data-uk-sticky="{top:15}" style="text-align: right;
                     <?php } ?>
                     margin-top: -16px;
                     background: whitesmoke;
                     position: relative;
                     top: 0px;
                     left: 0px;
                     margin-bottom: -4px;
                     padding-bottom: 5px;
                     padding-top: 5px;
                     padding: 5px;
                     border: 1px solid lightgray; 
                     border-radius: 0px;
                     z-index: 999;
                     display: block;">
                    <span style='float: left;'><strong><i class='uk-icon-cogs'></i> Samlogin Settings</strong></span>   





                    <a style='margin-left: 3px; margin-left: 3px;'
                       href="#samlogin_top" class="uk-button uk-button-mini" 
                       data-uk-smooth-scroll>
                        <i class="uk-icon-arrow-circle-up"></i> Scroll to top
                    </a>

                    <button style="margin-right: 3px;" 
                            onClick="samlogin_saveSettings('intab');"

                            class="uk-button uk-button-mini saveSettingsButton" 
                            >
                        <i class="uk-icon-download"></i> Save Settings
                    </button>

                    <button style="" 
                            title="Open settings in a modal"

                            class="uk-button uk-button-mini" 
                            data-uk-modal="{target:'#settings-modal'}">
                        <i class="uk-icon-external-link-square"></i>
                    </button> 

                </div>

                <div class="stickyplaceholder" style="margin-top: -16px;
                     margin-top: -12px;
                     background: whitesmoke;
                     margin-bottom: -4px;
                     padding-bottom: 0px;
                     padding-top: 5px;
                     padding: 3px;
                     height: 23px;
                     border: 1px solid lightgray;
                     border-radius: 0px;
                     display: block;"></div>

            </div>
            <hr style='margin-top: 3px; margin-bottom: 6px;'/>
            <!-- now modal -->
            <?php
            $fromHTMLUniqueIdSuffix = "intab";
            include dirname(__FILE__) . "/snippets/settings-form.php";
            ?>
        </li> 
        <li class="">
            <?php
            include dirname(__FILE__) . "/snippets/tools.php";
            ?>
        </li>
        <li class="">
            Log section not yet available
        </li>
    </ul>
</div>

<aside>
    <div  class="samlogin-dash-minipanel  samlogin-dash-minipanel-right uk-panel uk-panel-box uk-panel-box" id="samlogin-system-checks">
        <div class="uk-text-bold"><i class='uk-icon-cog'></i> <?php echo JText::_('SAMLOGIN_SYSTEM_CHECKS'); ?></div>
        <table class="samlogin-system-table">
            <tbody>
                <tr>
                    <td class="samlogin-system-label">PHP </td>
                    <td class="samlogin-system-value"><span class='uk-button-mini uk-button'><?php echo $this->checks['php']; ?></span></td>
                </tr>
                <tr>
                    <td class="samlogin-system-label">php-curl </td>
                    <td class="samlogin-system-value">
                        <span class="uk-button uk-button-mini <?php echo $this->checks['curl'] ? 'uk-button-success' : 'uk-button-danger'; ?>">
                            <?php echo $this->checks['curl'] ? "<i class='uk-icon-check-circle'></i>" : "<i class='uk-icon-warning'></i>"; ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="samlogin-system-label">php-mcrypt </td>
                    <td class="samlogin-system-value">
                        <span class="uk-button uk-button-mini <?php echo $this->checks['mcrypt'] ? 'uk-button-success' : 'uk-button-danger'; ?>">
                            <?php echo $this->checks['mcrypt'] ? "<i class='uk-icon-check-circle'></i>" : "<i class='uk-icon-warning'></i>"; ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="samlogin-system-label">php-xml</td>
                    <td class="samlogin-system-value">
                        <span class="uk-button uk-button-mini <?php echo $this->checks['xml'] ? 'uk-button-success' : 'uk-button-danger'; ?>">
                            <?php echo $this->checks['xml'] ? "<i class='uk-icon-check-circle'></i>" : "<i class='uk-icon-warning'></i>"; ?>
                        </span>
                    </td>
                </tr>    
                <tr>
                    <td class="samlogin-system-label">php-pdo</td>
                    <td class="samlogin-system-value">
                        <span class="uk-button uk-button-mini <?php echo class_exists('PDO') ? 'uk-button-success' : 'uk-button-danger'; ?>">
                            <?php echo class_exists('PDO') ? "<i class='uk-icon-check-circle'></i>" : "<i class='uk-icon-warning'></i>"; ?>
                        </span>
                    </td>
                </tr>    
               <!-- <tr>
                        <td class="samlogin-system-label">JSON</td>
                        <td class="samlogin-system-value">
                            <span class="uk-button uk-button-mini <?php echo $this->checks['json'] ? 'uk-button-success' : 'uk-button-danger'; ?>">
                <?php echo $this->checks['json'] ? "<i class='uk-icon-check-circle'></i>" : "<i class='uk-icon-warning'></i>"; ?>
                            </span>
                        </td>
                </tr> -->
            </tbody>
        </table>
    </div>

    <div class="samlogin-dash-minipanel  samlogin-dash-minipanel-right uk-panel uk-panel-box samlogin-version-panel">

        <div class="uk-text-bold"><i class='uk-icon-info-circle'></i> SAMLogin version: <div class="uk-button uk-button-success samloginVersion"><?php echo $this->version; ?></div></div>
        <div class="uk-text-bottom"><i>Version color :</i></div>
        <span class="uk-button uk-button-mini uk-button-success">&nbsp;&nbsp;</span> Stable  <span class="uk-button uk-button-mini uk-button-primary">&nbsp;&nbsp;</span> Beta/Custom <span class="uk-button uk-button-mini uk-button-danger">&nbsp;&nbsp;</span> Outdated
        <!--<div class=" uk-button uk-button-mini uk-button-primary" id="SkypeButton"></div>
        <script type='text/javascript'>
            jQuery(document).ready(function(){
                Skype.ui({'imageColor': 'white','imageSize': 24,'name': 'chat','element': 'SkypeButton','participants': ['rastrano']});
            });
        </script>
        -->

    </div>
</aside>





<div style="clear: right; margin-left: 10px; margin-top: 10px;" class="uk-panel uk-panel-box uk-panel-box-primary samlogin-dash-minipanel samlogin-dash-minipanel-right samlogin-dash-minipanel-bottom" id="samlogin-fedinfo">
    <button style="float:right;" class='uk-button uk-button-mini' data-uk-tooltip="{pos:'top-left'}" 
            title="When you completed the checklist, copy the url shown in the textbox 
            below and share it with your Federation/IdP managers, asking to add this to the trusted metadata">
        <i class='uk-icon-question-circle'></i>
    </button>
    <label for="selfmetadata" class="uk-form-label">  <b><i class="uk-icon-code"></i></b> Self XML Metadata url: </label> 
    <input name="selfmetadata" style="width: 90%;" onclick="jQuery(this).select();" type="text" class="uk-form-controls-text" value="<?php echo $this->checks["metadataURL"]; ?>"/>
</div>

<div style="clear: right;  margin-left: 10px; margin-top: 10px;"  class="uk-panel uk-panel-box uk-panel-box-primary samlogin-dash-minipanel samlogin-dash-minipanel-right samlogin-dash-minipanel-bottom" id="samlogin-croninfo">
    <button style="float:right;" class='uk-button uk-button-mini' data-uk-tooltip="{pos:'top-left'}" 
            title="Please ensure to configure a cronjob that periodically fetches 
            the cronjob url as shown in this example crontab configuration,
            ask your system manager about the better way to configure a cronjob for fetching an url on your system">
        <i class='uk-icon-question-circle'></i>
    </button>
    <div class="uk-text-bold"><i class="uk-icon-cogs"></i> Cronjob Setup Info: </div>
    <p>
        <a class="cronLink" target="_blank" href="<?php echo $this->checks["cronLink"]; ?>">
            Cronjob URL (click to run now) <i class="uk-icon-external-link-square"></i></a> <p/>
    <i class="uk-icon-clock-o"></i>
    Crontab scheduling example:<p/><textarea class="cronSuggestion" style="font-size:10px; width: 100%; height: 10em;"><?php echo $this->checks["cronSuggestion"]; ?></textarea> 
</div>



<div style="clear: right;  margin-left: 10px; margin-top: 10px;"  class="uk-panel uk-panel-box uk-panel-box-secondary samlogin-dash-minipanel samlogin-dash-minipanel-right samlogin-dash-minipanel-bottom" id="samlogin-testlogin">
    <button style="float:right;" class='uk-button uk-button-mini' data-uk-tooltip="{pos:'top-left'}" 
            title="This box contains information on how to do a test SSO login to check if all is fine with simpleSAMLphp SSO trust and discoverying what attributes you are getting from the IdP">
        <i class='uk-icon-question-circle'></i>
    </button>
    <div class="uk-text-bold"><i class="uk-icon-info-circle"></i> SimpleSAMLphp SP test login procedure: </div>
    <p>
    <ol>
        <li> Ensure you have all green in the configuration checklist and that you are browsing the Joomla administrator with https://</li>
        <li> Ensure you added the XML metadata of the remote IdP or federation in <i>SAMLogin->Settings->Federation Metadata<i> and you set a proper discovery service or IdP EntityId in <i>SAMLogin->Settings->Discovery Service</i> </li>
                    <li> Ensure you shared your <a target="_blank" href="<?php echo $this->checks["metadataURL"]; ?>">Self-metadata URL <i class="uk-icon-external-link-square"></i> </a> with the IdP/Federation managers and they approved your SP entity in the trusted relying parties</li>
                    <li> Open this <a target='_blank' href='<?php echo JUri::root() . "/components/com_samlogin/simplesamlphp/www/module.php/core/authenticate.php?as=default-sp"; ?>'>SimpleSAMLphp authsource test page <i class="uk-icon-external-link-square"></i> </a> 
                    </li>
                    <li> Click forward (evenutally select your IdP if you have multiple choices)</li>
                    <li> Login at your IdP </li>
                    <li> You should see now a page showing a list of attribute names and values take note of this as this will help you to define AuthZ rules and tuning the Attr. Mappings in SAMLogin</li>
                    <li> If all went fine you are ready to publish the SAMLogin module or the login view and test a real Joomla login (if it fails try to tune Attr.Mappings accoding to the attribute names, N.B. you need an email attribute from your IdP, this is required by Joomla!)</li>
                    </ol>    
                    </p>
                    </div>




                    <!--
                           <div id="ssp-conf-debug">
                               <h2><?php echo JText::_('SAMLOGIN_SSP_CONF_DEBUG'); ?></h2>
                    <?php //echo $this->checks['sspConfDebug'];  ?>
                           </div>
                    -->

                    <!-- modals: -->   




                    <div  id='install-ssp-modal'class="uk-modal">
                        <div class="uk-modal-dialog">
                            <a class="uk-modal-close uk-close"></a>
                            <h4 style='margin-bottom: 0px; margin-top: 0px;'>Install simpleSAMLphp Wizard</h4> <hr style='margin:0; margin-bottom: 3px;'/>
                            <!-- This is the container enabling the JavaScript in click mode -->
                            <div class="install-ssp-modal-a">
                                <p style="text-align: left;">
                                    SimpleSAMLphp is a free software developed by UNINETT AS*  licensed under the CC-GNU LGPL version 2.1,
                                    this procedure will let you install a custom copy embedded in your Joomla just with a click.
                                    (It's a proven opensource SAML 2.0 library/software)
                                    <br/>
                                    <small>*Anyway the version you will install through this wizard may be a custom distro modified by the SAMlogin developer(s) 
                                        to better achieve integration with Joomla and your system environment</small>
                                </p>
                                <p>
                                    SimpleSAMLphp LGPL License:
                                    <textarea style="width:100%; height: 150px; font-size: 90%;"><?php echo htmlentities(file_get_contents(dirname(__FILE__) . "/SSP_LGPL_LICENSE")); ?></textarea>
                                </p>
                                <div style="display: block; width: 50%; margin: 0 auto; text-align: center;">
                                    <div style='' data-uk-dropdown="{mode:'click'}">
                                        <!-- This is the element toggling the dropdown -->
                                        <div ><button class="uk-button ">Click here to install it <i class="uk-icon-caret-down"></i></button></div>

                                        <!-- This is the dropdown -->
                                        <div style="width: 100%; font-size: 90%;" class="uk-dropdown">
                                            <ul style="text-align: center;" class="uk-nav uk-nav-dropdown">
                                                <li class="uk-nav-header">Select version and flavour</li>
                                                <li><a href="#" onClick="samlogin_installSSP('1.12.n1');" in_tag="ul"><i class="uk-icon-cloud-download"></i> Install SimpleSAMLphp <b>v.1.12</b><i>.n1 (from our github repo)</i> 
                                                        <span style="float: none; font-size: 70%;">
                                                            <div class="uk-badge uk-badge-warning">samlogin</div>                                                      
                                                            <div class="uk-badge uk-badge-warning">nginx</div>
                                                            <div class="uk-badge uk-badge-success">suggested choice</div>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li><a href="#" onClick="samlogin_installSSP('1.12.n1', true);" in_tag="ul"><i class="uk-icon-cloud-download"></i> Migration from SimpleSAMLphp v.1.11 to <b>v.1.12</b><i>.n1 (from our github repo)</i> 
                                                        <span style="float: none; font-size: 70%;">
                                                            <div class="uk-badge uk-badge-warning">samlogin</div>
                                                            <div class="uk-badge uk-badge-warning">nginx</div>
                                                            <div class="uk-badge uk-badge-danger">migration mode (1.11 to 1.12)</div>
                                                        </span>
                                                    </a>
                                                </li>
                                              <!--  <li><a href="#" onClick="samlogin_installSSP('1.12.n');" in_tag="ul"><i class="uk-icon-cloud-download"></i> Install SimpleSAMLphp <b>v.1.12</b><i>.n (from our github repo)</i> 
                                                        <span style="float: none; font-size: 70%;">
                                                            <div class="uk-badge uk-badge-warning">samlogin</div>
                                                            <div class="uk-badge uk-badge-warning">nginx</div>
                                                            <div class="uk-badge uk-badge-success">suggested choice</div>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li><a href="#" onClick="samlogin_installSSP('1.12.n', true);" in_tag="ul"><i class="uk-icon-cloud-download"></i> Migration from SimpleSAMLphp v.1.11 to <b>v.1.12</b><i>.n (from our github repo)</i> 
                                                        <span style="float: none; font-size: 70%;">
                                                            <div class="uk-badge uk-badge-warning">samlogin</div>
                                                            <div class="uk-badge uk-badge-warning">nginx</div>
                                                            <div class="uk-badge uk-badge-success">suggested choice</div>
                                                            <div class="uk-badge uk-badge-danger">migration mode - from 1.11, use this if you was on 1.11</div>
                                                        </span>
                                                    </a>
                                                </li> 
                                                -->
                                                <li><a href="#" onClick="samlogin_installSSP('1.11.n');" in_tag="ul"><i class="uk-icon-cloud-download"></i> Install SimpleSAMLphp <b>v.1.11</b><i>.n (from our github repo)</i> 
                                                        <span style="float: none; font-size: 70%;">
                                                            <div class="uk-badge uk-badge-warning">samlogin</div>
                                                            <div class="uk-badge uk-badge-warning">nginx</div>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li><a href="#" onClick="samlogin_installSSP('1.11.f');"  in_tag="ul"><i class="uk-icon-cloud-download"></i> Install SimpleSAMLphp <b>v.1.11</b><i>.f (from our github repo)</i> 
                                                        <span style="float: none; font-size: 70%;">
                                                            <div class="uk-badge uk-badge-warning">samlogin</div>
                                                            <div class="uk-badge uk-badge-warning">nginx</div>
                                                            <div class="uk-badge uk-badge-warning">F5 patch</div>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li class="uk-nav-header">If the Github download fails try those alternative repos:</li>
                                                <li><a href="#" onClick="samlogin_installSSP('1.12.n1-a');"  in_tag="ul">Install SimpleSAMLphp <b>v.1.12</b><i>.n1 (from alternate repo)</i> 
                                                        <span style="float: none; font-size: 70%;">
                                                            <div class="uk-badge uk-badge-warning">samlogin</div>
                                                            <div class="uk-badge uk-badge-warning">nginx</div>
                                                            <div class="uk-badge uk-badge-success">alternative repo</div>
                                                        </span>
                                                    </a>
                                                </li>
                                              <!--  <li><a href="#" onClick="samlogin_installSSP('1.12.n-a');"  in_tag="ul">Install SimpleSAMLphp <b>v.1.12</b><i>.n (from alternate repo)</i> 
                                                        <span style="float: none; font-size: 70%;">
                                                            <div class="uk-badge uk-badge-warning">samlogin</div>
                                                            <div class="uk-badge uk-badge-warning">nginx</div>
                                                            <div class="uk-badge uk-badge-success">alternative repo</div>
                                                        </span>
                                                    </a>
                                                </li> -->
                                                <li><a href="#" onClick="samlogin_installSSP('1.11.n-a');"  in_tag="ul">Install SimpleSAMLphp <b>v.1.11</b><i>.n (from alternate repo)</i> 
                                                        <span style="float: none; font-size: 70%;">
                                                            <div class="uk-badge uk-badge-warning">samlogin</div>
                                                            <div class="uk-badge uk-badge-warning">nginx</div>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li><a href="#" onClick="samlogin_installSSP('1.11.f-a');"  in_tag="ul">Install SimpleSAMLphp <b>v.1.11</b><i>.f (from alternate repo)</i> 
                                                        <span style="float: none; font-size: 70%;">
                                                            <div class="uk-badge uk-badge-warning">samlogin</div>
                                                            <div class="uk-badge uk-badge-warning">nginx</div>
                                                            <div class="uk-badge uk-badge-warning">F5 patch</div>
                                                        </span>
                                                    </a>
                                                </li>

                                            </ul>


                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="install-ssp-modal-b" style="display: none;">
                                <p style="text-align: center;">
                                    <i class="install-ssp-modal-b-icon uk-icon-cloud-download"></i> 
                                    <span class="install-ssp-modal-b-msg">
                                        Downloading from remote repository...
                                    </span>

                                <div class="uk-progress uk-progress-small uk-progress-danger uk-progress-striped uk-active">
                                    <div class="uk-progress-bar" style="width: 5%;"></div>
                                </div>
                                </p>
                            </div>
                        </div>
                    </div>



                    <div id='settings-modal'class="uk-modal">
                        <div style='width: 80%;'  class="uk-modal-dialog">
                            <a class="uk-modal-close uk-close"></a>
                            <h4 style='margin-bottom: 0px; margin-top: 0px; display: inline-block;'>Edit Samlogin Settings</h4> 
                            <div style="margin-right: 3px;" 
                                 onClick="samlogin_saveSettings('modal');"

                                 class="uk-button uk-button-mini uk-button-success" 
                                 >
                                <i class="uk-icon-download"></i> Save
                            </div> <hr style='margin:0; margin-bottom: 3px;'/>



                            <?php
                            $fromHTMLUniqueIdSuffix = "modal";
                            include dirname(__FILE__) . "/snippets/settings-form.php";
                            ?>
                        </div>
                    </div>


                    <div  id='empty-modal'class="uk-modal">
                        <div class="uk-modal-dialog">
                            <a class="uk-modal-close uk-close"></a>
                            <h4 style='margin-bottom: 0px; margin-top: 0px;'>Install simpleSAMLphp Wizard</h4> <hr style='margin:0; margin-bottom: 3px;'/>
                            template
                        </div>
                    </div>

                    <!-- end modals: -->   

                    </div>

                    <div class="samlogin-dash-leftcontent-large" id="samlogin-headfooter">
                        <hr/>
                        <small>
                            <a target="_blank" href="http://creativeprogramming.it/samlogin">SAMLogin</a> | Copyright &copy; 2013-<?php echo date('Y'); ?> <a href="http://www.creativeprogramming.it" target="_blank">creativeprogramming.it.</a>
                        </small>
                    </div>
