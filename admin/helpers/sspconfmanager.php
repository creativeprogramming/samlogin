<?php

// No direct access to this file
defined('_JEXEC') or die;

include_once(JPATH_COMPONENT_ADMINISTRATOR . "/libs/array_smart_dump.inc.php");

class SSPConfManager {

    static $saveConfModeSuffix = "";
    static $SAVECONF_PRODUCTION = "";
    static $SAVECONF_SIMULATE = ".virtual";

    public static function setSaveConfMode($type) {
        if (SAMLoginControllerAjax::aquireLock("saveconfmode")) {
            self::$saveConfModeSuffix = $type;
        } else {
            return false;
        }
        return true;
    }

    public static function commitSaveConfModeLock($type) {
        SAMLoginControllerAjax::releaseLock("saveconfmode");
    }

    public static function checkConfSync(&$ajaxMessages) {
        $toret = true;
        $filetoCheckArr = array(
            '/components/com_samlogin/simplesamlphp/config/authsources.php' =>
            '/components/com_samlogin/simplesamlphp/config/authsources' . self::$SAVECONF_SIMULATE . '.php',
            '/components/com_samlogin/simplesamlphp/config/config-metarefresh.php' =>
            '/components/com_samlogin/simplesamlphp/config/config-metarefresh' . self::$SAVECONF_SIMULATE . '.php',
            '/components/com_samlogin/simplesamlphp/config/module_cron.php' =>
            '/components/com_samlogin/simplesamlphp/config/module_cron' . self::$SAVECONF_SIMULATE . '.php',
            '/components/com_samlogin/simplesamlphp/config/config.php' =>
            '/components/com_samlogin/simplesamlphp/config/config' . self::$SAVECONF_SIMULATE . '.php',
        );
        //$tmpdir = JFactory::getApplication()->getCfg("tmp_path");
        foreach ($filetoCheckArr as $filetocheck => $virtualfile) {

            //  echo (JPATH_SITE.$filetocheck);
            //  echo "\n". (JPATH_SITE.$virtualfile);
            //  $app->enqueueMessage("preserving..." . JPATH_SITE . $filetopreserve, "warning");
            if (JFile::exists(JPATH_SITE . $filetocheck)) {
                if (!JFile::exists(JPATH_SITE . $virtualfile)) {

                    $copyop = JFile::copy(JPATH_SITE . $filetocheck, JPATH_SITE . $virtualfile);
                    if (!$copyop) {
                        throw new Exception("copy failed");
                    }
                    //return true; //first time boot
                    //$toret=true;
                } else {
                    $contentA = file_get_contents(JPATH_SITE . $filetocheck);
                    $contentB = file_get_contents(JPATH_SITE . $virtualfile);
                    if ($contentA != $contentB) {
                        $toret = false;
                        // include the Diff class
                        require_once(JPATH_COMPONENT_ADMINISTRATOR . "/libs/class.Diff.php");
                        // output the result of comparing two files as HTML
                        //OR a param $advancedUserMode= isset($_COOKIE["samlogin_advanced_user_mode"]);
                        $advancedUserMode = true;

                        if ($advancedUserMode) {
                            $msgdiff = Diff::toHTMLDiffOnlySkipSecrets(Diff::compare($contentA, $contentB, false));
                            $msgdiffCheck = strip_tags(trim($msgdiff));
                            $msg = "simpleSAMLphp's configuration file " . strtr(
                                            $filetocheck, array(
                                        "/components/com_samlogin/simplesamlphp/config/" => ""
                                            )
                                    ) . " is not in sync with your latest changes, click the "
                                    . "<span class='uk-button uk-button-mini' style='cursor:pointer;' onClick='samlogin_saveSSPConf();'>"
                                    . "<i class='uk-icon-download'></i>Put settings in production (SSP)</span>"
                                    . " toolbar button to commit changes.";

                            if (!empty($msgdiffCheck) && strlen($msgdiffCheck) < 500) {
                                $msg.= "<hr style='margin:0px; margin-top:2px;margin-bottom:2px;'/>"
                                        . "<small>Changes that will be applied (preview):</small> "
                                        . "<span style='font-size:80%;font-family: monospace;'>"
                                        . $msgdiff
                                        . "</span>";
                            }
                        } else {
                            $msg = "SimpleSAMLphp requires a "
                                    . "<span class='uk-button uk-button-mini' style='cursor:pointer;' onClick='samlogin_saveSSPConf();'>"
                                    . "<i class='uk-icon-download'></i>Put settings in production (SSP)</span>"
                                    . "";
                        }

                        $ajaxMessages[] = array("msg" => $msg,
                            //  "<code style='font-size: 80%;' lang='php'><pre class='diffFile'> $msgdiff </pre></code>",
                            "level" => SAMLoginControllerAjax::$AJAX_MESSAGE_WARNING);
                    }
                }

                // $app->enqueueMessage("failed to preserve conf file: " . $filetopreserve, "error");
            } else {
                throw new Exception("Configuration file $filetocheck doesn't exists");
            }
        }
        return $toret;
    }

    static function getAuthsourcesConf($getItFromProduction=true) {
        
        require_once(JPATH_SITE . "/components/com_samlogin/simplesamlphp/lib/_autoload.php");
        if (isset($config)) {
            unset($config);
        }
        if ($getItFromProduction){
                 require(JPATH_SITE . "/components/com_samlogin/simplesamlphp/config/authsources" . self::$SAVECONF_PRODUCTION . ".php");
        }else{
            require(JPATH_SITE . "/components/com_samlogin/simplesamlphp/config/authsources" . self::$saveConfModeSuffix . ".php");
        }
        return $config;
    }

    static function mergeParamsWithConf($params, $app) {

        $authsourcesConf = self::getAuthsourcesConf();

        $mySPEntityId = $params->get("ssp_my_sp_entityid", "auto");
        if (empty($mySPEntityId) || $mySPEntityId == "auto") {
            unset($authsourcesConf["default-sp"]["entityID"]);
        } else {
            $authsourcesConf["default-sp"]["entityID"] = $mySPEntityId;
        }

        $authsourcesConf["default-sp"]["signature.algorithm"] = $params->get("sp_signature_algorithm", null);
        if (is_null( $authsourcesConf["default-sp"]["signature.algorithm"])  || $authsourcesConf["default-sp"]["signature.algorithm"]=="default"){
            unset($authsourcesConf["default-sp"]["signature.algorithm"]);
        }
        $authsourcesConf["default-sp"]["NameIDPolicy"] = $params->get("sp_nameidpolicy", null);
        if (is_null( $authsourcesConf["default-sp"]["NameIDPolicy"])  || $authsourcesConf["default-sp"]["NameIDPolicy"]=="default" ){
            unset($authsourcesConf["default-sp"]["NameIDPolicy"]);
        }
        $authsourcesConf["default-sp"]["ProtocolBinding"] = $params->get("sp_protocol_binding", null);
        if (is_null($authsourcesConf["default-sp"]["ProtocolBinding"]) || $authsourcesConf["default-sp"]["ProtocolBinding"]=="default"){
            unset($authsourcesConf["default-sp"]["ProtocolBinding"]);
        }
        $authsourcesConf["default-sp"]["acs.Bindings"] = $params->get("sp_acs_bindings",null);
        
        if (is_null($authsourcesConf["default-sp"]["acs.Bindings"]) || in_array("*", $authsourcesConf["default-sp"]["acs.Bindings"])){
            unset($authsourcesConf["default-sp"]["acs.Bindings"]);
        }
        
        /*
         * TODO: it only support one single value, but default is multiple, maybe report issue to SSP team
         * 
         $authsourcesConf["default-sp"]["SingleLogoutServiceBinding"] = $params->get("sp_slo_bindings",null);
        
        if (  is_null($authsourcesConf["default-sp"]["SingleLogoutServiceBinding"]) || in_array("*", $authsourcesConf["default-sp"]["SingleLogoutServiceBinding"])){
            unset($authsourcesConf["default-sp"]["SingleLogoutServiceBinding"]);
        }
        
         * *
         */
        
        
        //SAMLoginControllerAjax::enqueueAjaxMessage("dbg: ".print_r( $authsourcesConf["default-sp"]["acs.Bindings"],true), "info");
    //        $authsourcesConf["default-sp"]["debugall"]=print_r(   $authsourcesConf["default-sp"]["acs.Bindings"],true);
        $authsourcesConf["default-sp"]["isPassive"] = $params->get("sp_isPassive", 0) == 1 ? TRUE : FALSE;
        //sp_acs.Bindings
        $authsourcesConf["default-sp"]["assertion.encryption"] = $params->get("sp_assertion_encryption", 0) == 1 ? TRUE : FALSE;

        $authsourcesConf["default-sp"]["sign.authnrequest"] = $params->get("sp_signassertion", 0) == 1 ? TRUE : FALSE;
        $authsourcesConf["default-sp"]["sign.logout"] = $params->get("sp_signassertion", 0) == 1 ? TRUE : FALSE;

        $authsourcesConf["default-sp"]["redirect.sign"] = $params->get("sp_signassertion", 0) == 1 ? TRUE : FALSE;
        $authsourcesConf["default-sp"]["redirect.validate"] = $params->get("sp_signassertion", 0) == 1 ? TRUE : FALSE;

        $discoType = $params->get("sspas_discotype", "0");
        if ($discoType === "0" || $discoType === 0) {
            $authsourcesConf["default-sp"]["discoURL"] = $params->get("sspas_discourl", "https://discovery.renater.fr/test");
        } else {
            $discoUrl = $params->get("sspas_discotype", null);

            if ($discoUrl == "null") {
                $discoUrl = null;
            }

            $authsourcesConf["default-sp"]["idp"] = null;
            unset($authsourcesConf["default-sp"]["idp"]);

            switch ($discoType) {
                case "discopower":
                    $discoUrl = str_ireplace("http://", "https://", JURI::root()) . "/components/com_samlogin/simplesamlphp/www/module.php/discopower/disco.php";
                    break;
                case "discojuice_standalone":

                    $app = & JApplication::getInstance('site');
                    $router = $app->getRouter('site');
                    $wrong_route = $router->build('index.php?option=com_samlogin&view=discojuice')->toString();
                    $correct_route = preg_replace("|^" . JURI::base(true) . "|", "", $wrong_route);
                    $protocolhost = JURI::getInstance()->getScheme() . '://' . JURI::getInstance()->getHost();
                    $absolute_url = $protocolhost . $correct_route;


                    /* $discojuiceurl=  //replace /administrator
                      str_replace(JURI::base(), JURI::root(),
                      JRoute::_('index.php?option=com_samlogin&view=discojuice')); */

                    $discoUrl = str_ireplace("http://", "https://", $absolute_url);
                    break;
                case "discojuice_embedded":
                    $discoUrl = null;
                    break;
                case "-1": {
                        $idpEntityId = $params->get("sspas_idpentityid", null);
                        $authsourcesConf["default-sp"]["idp"] = $idpEntityId;
                    }
                default:
                    $discoUrl = null;
            }

            $authsourcesConf["default-sp"]["discoURL"] = $discoUrl;
        }
        // die(  $authsourcesConf["default-sp"]["discoURL"]);
        self::saveAuthsourcesConf($authsourcesConf, $app);


        $config = self::getConf();
        $paramsArr = $params->toArray();
        $useMetarefresh = false;

        $sessionStorage = $params->get("ssphp_session_storage", "0");
        if ($sessionStorage == "0") {
            $config["store.type"] = "sql";
            jimport('joomla.database.database');
            jimport('joomla.database.table');

            $conf = JFactory::getConfig();

            $host = $conf->get('host');
            $user = $conf->get('user');
            $password = $conf->get('password');
            $dbname = JFactory::getConfig()->get("db");
            $prefix = $conf->get('dbprefix'); //***Change this if the dbprefix is not the same!***
            $driver = $conf->get('dbtype');

            if (stristr($host, ":")) {
                $port = explode(":", $host);
                $port = $port[1];
            } else {
                $port = 3306;
            }
            //$options    = array ('driver' => $driver, 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix);
            //$db = JDatabase::getInstance($options);
            $config["store.type"] = "sql";
            $config["store.sql.dsn"] = "mysql:host=$host;port=$port;dbname=$dbname";
            $config["store.sql.username"] = $user;
            $config["store.sql.password"] = $password;
            $config["store.sql.prefix"] = $prefix . "samlogin_session";
        } else {
            $config["store.type"] = "phpsession";
        }

        foreach ($paramsArr as $key => $value) {
            if (stripos($key, "ssp_") === 0) {
                $realkey = str_ireplace("ssp_", "", $key);
                $realkey = str_ireplace("__", ".", $realkey);
                switch ($realkey) {
                    case "use_metarefresh":
                        $value = ($value == "1" || $value == 1);
                        if ($value) {
                            $useMetarefresh = true;
                        }
                        break;
                    case "session.cookie.secure":
                        $value = ($value == "1" || $value == 1);
                    case "enable.saml20-idp":
                        $value = ($value == "1" || $value == 1);
                    case "enable.wsfed-sp":
                        $value = ($value == "1" || $value == 1);
                    case "showerrors":
                        $value = ($value == "1" || $value == 1);
                    case "debug":
                        $value = ($value == "1" || $value == 1);
                    case "debug.validatexml":
                        $value = ($value == "1" || $value == 1);
                    default:
                        $config[$realkey] = $value;
                }
            }
        }




        $config["metadata.sources"] = array(array("type" => "flatfile"));
        $configmetarefreshSrcs = array();
        foreach ($paramsArr as $key => $value) {
            if (stripos($key, "sspmeta_") === 0) {
                $realkey = str_ireplace("sspmeta_", "", $key);
                $realkey = str_ireplace("__", ".", $realkey);

                if (!$useMetarefresh) {
                    if (filter_var($value, FILTER_VALIDATE_URL) !== false) {

                        $config["metadata.sources"][] = array("type" => "xml", "url" => $value);
                    } else {
                        $url = trim($value);
                        if (!empty($url)) {
                            //  $app->enqueueMessage("Invalid metadata url: " . $url, "warning");
                            SAMLoginControllerAjax::enqueueAjaxMessage("Invalid metadata url: " . $url, SAMLoginControllerAjax::$AJAX_MESSAGE_WARNING);
                        }
                    }
                } else {
                    if (filter_var($value, FILTER_VALIDATE_URL) !== false) {
                        $configmetarefreshSrcs[] = array("src" => $value);
                    } else {
                        $url = trim($value);
                        if (!empty($url)) {
                            SAMLoginControllerAjax::enqueueAjaxMessage("Invalid metadata url: " . $url, SAMLoginControllerAjax::$AJAX_MESSAGE_WARNING);
                        }
                    }
                }
            }
        }

        //enable also flatfiles
        //  $config["metadata.sources"][]=array("type"=>"flatfile");

        if ($useMetarefresh) {
            $config["metadata.sources"][] = array("type" => "flatfile", "directory" => "metadata/federations");
            self::saveMetarefreshSrcConf($configmetarefreshSrcs, $app,$params);
            $cronConf = array(
                "key" => $params->get("sspcron_secret", "changeme"),
                'allowed_tags' => array('daily', 'hourly', 'frequent'),
                'debug_message' => $params->get("sspcron_debug", false) == 1,
                'sendemail' => $params->get("sspcron_email", false) == 1,
            );
            self::saveCronConf($cronConf, $app,$params);
        }
        $enableWSFED=$params->get("ssp_enable__wsfed-sp",0);
        if ($enableWSFED){
            error_reporting(E_ALL);
             self::saveWSFEDIdPMetadata($app,$params);
             self::saveWSFEDSPMetadata($app,$params);
        }



        return $config;
    }

    static function getCertDirPath() {
        $SSPConf = self::getConf();
        $certdir = $SSPConf["certdir"];

        if (stripos($certdir, "/") === 0) {
            $SSPKeyPath = $certdir;
        } else {
            $SSPKeyPath = JPATH_SITE . "/components/com_samlogin/simplesamlphp/" . $certdir;
        }
        return $SSPKeyPath;
    }

    static function getCertURLPath() {
        $SSPConf = self::getConf();
        $certdir = $SSPConf["certdir"];
        if (stripos($certdir, "/") === 0) {
            $howmanyslashes = JPATH_SITE;
            $slashes = preg_split("\/", $howmanyslashes);
            $backwardTest = "";
            foreach ($slashes as $nomattdir) {
                $backwardTest.="/../";
            }
            $SSPKeyPath = $backwardTest . $certdir;
        } else {
            $SSPKeyPath = "/components/com_samlogin/simplesamlphp/" . $certdir;
        }
        return $SSPKeyPath;
    }

    static function saveCronConf($cronconf, $app,$params) {
        $success = true;
        $SSPConfPath = JPATH_COMPONENT_SITE . "/simplesamlphp/config/";

        $oldSSPConf = file_get_contents($SSPConfPath . "module_cron" . self::$saveConfModeSuffix . ".php");
        $datetimestring = date('j_M_y_H_i_s', time());


        $fwrite = file_put_contents($SSPConfPath . "module_cron.until_" . $datetimestring . self::$saveConfModeSuffix . ".php", $oldSSPConf);
        if ($fwrite == false) {
            SAMLoginControllerAjax::enqueueAjaxMessage("Failed while writing backup config file in $SSPConfPath, please check file permissions "
                    , SAMLoginControllerAjax::$AJAX_MESSAGE_WARNING);
        }
        if (isset($config)) {
            unset($config);
        }
        require($SSPConfPath . "module_cron.php");
        $config = array_merge($config, $cronconf);
        $config["samlogin_lastchanged"] = array("deprecated");
        $newConfFileStr = array_smart_dump($config, "config");

        $fwrite = file_put_contents($SSPConfPath . "module_cron" . self::$saveConfModeSuffix . ".php", "<?php /* This conf file was generated by samlogin for Joomla!, but you can modify it! */\n" . $newConfFileStr);
        if ($fwrite == false) {
            SAMLoginControllerAjax::enqueueAjaxMessage("Failed while writing config file in $SSPConfPath, please check file permissions "
                    , SAMLoginControllerAjax::$AJAX_MESSAGE_DANGER);
            $success = false;
        }
        // $app->enqueueMessage(JText::_('SAMLOGIN_GENCRONSECRET_CONF_OK'));
        if ($success) {
            SAMLoginControllerAjax::enqueueAjaxMessage("module_cron" . self::$saveConfModeSuffix . ".php written"
                    , SAMLoginControllerAjax::$AJAX_MESSAGE_SUCCSS);
        }
        return $success;
    }
    
    static function saveWSFEDSPMetadata($app,$params) {
        $SSPConfPath = JPATH_COMPONENT_SITE . "/simplesamlphp/metadata/";
        $success = true;
      //  $oldSSPConf = file_get_contents($SSPConfPath . "wsfed-sp-hosted" . self::$saveConfModeSuffix . ".php");
        $datetimestring = date('j_M_y_H_i_s', time());
       /* $fwrite = file_put_contents($SSPConfPath . "wsfed-sp-hosted.until_" . $datetimestring . self::$saveConfModeSuffix . ".php", $oldSSPConf);
        if ($fwrite == false) {
            SAMLoginControllerAjax::enqueueAjaxMessage("Failed while writing backup of config file in $SSPConfPath, please check file permissions "
                    , SAMLoginControllerAjax::$AJAX_MESSAGE_WARNING);
        }
        if (isset($config)) {
            unset($config);
        }*/
     //   require($SSPConfPath . "wsfed-sp-hosted" . self::$saveConfModeSuffix . ".php");
        $metadata=array();
        $metadata[$params->get("wsfed_idp_realm_1",JURI::root())] = array(
                'host' => '__DEFAULT__',
        );
        $newConfFileStr = array_smart_dump($metadata, "metadata");

        $fwrite = file_put_contents($SSPConfPath . "wsfed-sp-hosted" . self::$saveConfModeSuffix . ".php", "<?php /* This conf file was generated by samlogin for Joomla!, but you can modify it! */\n" . $newConfFileStr);
        if ($fwrite == false) {
            $success = false;
            SAMLoginControllerAjax::enqueueAjaxMessage("Failed while writing config file in $SSPConfPath, please check file permissions "
                    , SAMLoginControllerAjax::$AJAX_MESSAGE_DANGER);
        }
        //$app->enqueueMessage(JText::_('SAMLOGIN_GENMETARERESH_CONF_OK'));
        if ($success) {
            SAMLoginControllerAjax::enqueueAjaxMessage("wsfed-sp-hosted" . self::$saveConfModeSuffix . ".php written"
                    , SAMLoginControllerAjax::$AJAX_MESSAGE_SUCCSS);
        }
        return $success;
    }
    
     static function saveWSFEDIdPMetadata($app,$params) {
        $SSPConfPath = JPATH_COMPONENT_SITE . "/simplesamlphp/metadata/";
        $success = true;
        //$oldSSPConf = file_get_contents($SSPConfPath . "wsfed-idp-remote" . self::$saveConfModeSuffix . ".php");
        $datetimestring = date('j_M_y_H_i_s', time());
       /* $fwrite = file_put_contents($SSPConfPath . "wsfed-idp-remote.until_" . $datetimestring . self::$saveConfModeSuffix . ".php", $oldSSPConf);
        if ($fwrite == false) {
            SAMLoginControllerAjax::enqueueAjaxMessage("Failed while writing backup of config file in $SSPConfPath, please check file permissions "
                    , SAMLoginControllerAjax::$AJAX_MESSAGE_WARNING);
        }
        if (isset($config)) {
            unset($config);
        }*/
      //  die("test");
        //require($SSPConfPath . "wsfed-idp-remote" . self::$saveConfModeSuffix . ".php");
       // $metadata=array();
        $metadata[$params->get("wsfed_idp_issuer_1","1")]=array(
            "prp" => $params->get("wsfed_idp_endpoint_1","1"),
            "certFingerprint" => $params->get("wsfed_idp_thumbprint_1","1")
        );
        $newConfFileStr = array_smart_dump($metadata, "metadata");

        $fwrite = file_put_contents($SSPConfPath . "wsfed-idp-remote" . self::$saveConfModeSuffix . ".php", "<?php /* This conf file was generated by samlogin for Joomla!, but you can modify it! */\n" . $newConfFileStr);
        if ($fwrite == false) {
            $success = false;
            SAMLoginControllerAjax::enqueueAjaxMessage("Failed while writing config file in $SSPConfPath, please check file permissions "
                    , SAMLoginControllerAjax::$AJAX_MESSAGE_DANGER);
        }
        //$app->enqueueMessage(JText::_('SAMLOGIN_GENMETARERESH_CONF_OK'));
        if ($success) {
            SAMLoginControllerAjax::enqueueAjaxMessage("wsfed-idp-remote" . self::$saveConfModeSuffix . ".php written"
                    , SAMLoginControllerAjax::$AJAX_MESSAGE_SUCCSS);
        }
        return $success;
    }

    static function saveMetarefreshSrcConf($srcArray,$app, $params) {
        $SSPConfPath = JPATH_COMPONENT_SITE . "/simplesamlphp/config/";
        $success = true;
        $oldSSPConf = file_get_contents($SSPConfPath . "config-metarefresh" . self::$saveConfModeSuffix . ".php");
        $datetimestring = date('j_M_y_H_i_s', time());
        $fwrite = file_put_contents($SSPConfPath . "config-metarefresh.until_" . $datetimestring . self::$saveConfModeSuffix . ".php", $oldSSPConf);
        if ($fwrite == false) {
            SAMLoginControllerAjax::enqueueAjaxMessage("Failed while writing backup of config file in $SSPConfPath, please check file permissions "
                    , SAMLoginControllerAjax::$AJAX_MESSAGE_WARNING);
        }
        if (isset($config)) {
            unset($config);
        }
        require($SSPConfPath . "config-metarefresh" . self::$saveConfModeSuffix . ".php");
        $config["sets"]["samlogin"]["sources"] = $srcArray;
        $expiresAfter =  (int)($params->get("metarefresh_expire_after", 60 * 60 * 24 * 31));
        $config["sets"]["samlogin"]["expireAfter"] = $expiresAfter;
        $config["samlogin_lastchanged"] = array("deprecated");
        $newConfFileStr = array_smart_dump($config, "config");

        $fwrite = file_put_contents($SSPConfPath . "config-metarefresh" . self::$saveConfModeSuffix . ".php", "<?php /* This conf file was generated by samlogin for Joomla!, but you can modify it! */\n" . $newConfFileStr);
        if ($fwrite == false) {
            $success = false;
            SAMLoginControllerAjax::enqueueAjaxMessage("Failed while writing config file in $SSPConfPath, please check file permissions "
                    , SAMLoginControllerAjax::$AJAX_MESSAGE_DANGER);
        }
        //$app->enqueueMessage(JText::_('SAMLOGIN_GENMETARERESH_CONF_OK'));
        if ($success) {
            SAMLoginControllerAjax::enqueueAjaxMessage("config-metarefresh" . self::$saveConfModeSuffix . ".php written"
                    , SAMLoginControllerAjax::$AJAX_MESSAGE_SUCCSS);
        }
        return $success;
    }

    static function saveAuthsourcesConf($config, $app, $writeAlsoVirtualFile = false) {
        $success = true;
        $SSPConfPath = JPATH_COMPONENT_SITE . "/simplesamlphp/config/";

        $oldSSPConf = file_get_contents($SSPConfPath . "authsources" . self::$saveConfModeSuffix . ".php");
        $datetimestring = date('j_M_y_H_i_s', time());
        $fwrite = file_put_contents($SSPConfPath . "authsources.until_" . $datetimestring . self::$saveConfModeSuffix . ".php", $oldSSPConf);
        if ($fwrite == false) {
            SAMLoginControllerAjax::enqueueAjaxMessage("Failed while writing backup config file in $SSPConfPath, please check file permissions "
                    , SAMLoginControllerAjax::$AJAX_MESSAGE_WARNING);
        }


        $config["samlogin_lastchanged"] = array("deprecated");
        $newConfFileStr = array_smart_dump($config, "config");

        $fwrite = file_put_contents($SSPConfPath . "authsources" . self::$saveConfModeSuffix . ".php", "<?php /* This conf file was generated by samlogin for Joomla!, but you can modify it! */\n" . $newConfFileStr);
        if ($fwrite == false) {
            $success = false;
            SAMLoginControllerAjax::enqueueAjaxMessage("Failed while writing config file in $SSPConfPath, please check file permissions "
                    , SAMLoginControllerAjax::$AJAX_MESSAGE_DANGER);
        }
        if ($writeAlsoVirtualFile) { //useful to prevent unsync issue when we generate new keys
            $fwrite = JFile::copy($SSPConfPath . "authsources" . self::$saveConfModeSuffix . ".php", $SSPConfPath . "authsources" . self::$SAVECONF_SIMULATE . ".php");

//file_put_contents($SSPConfPath . "authsources" . self::$SAVECONF_SIMULATE . ".php", "<?php /* This conf file was generated by samlogin for Joomla!, but you can modify it! */\n" . $newConfFileStr);
            if ($fwrite == false) {
                $success = false;
                SAMLoginControllerAjax::enqueueAjaxMessage("Failed while writing config file in $SSPConfPath, please check file permissions "
                        , SAMLoginControllerAjax::$AJAX_MESSAGE_DANGER);
            }
        }
        //  $app->enqueueMessage(JText::_('SAMLOGIN_GENAUTHSOURCES_OK'));
        if ($success) {
            SAMLoginControllerAjax::enqueueAjaxMessage("authsources" . self::$saveConfModeSuffix . ".php written"
                    , SAMLoginControllerAjax::$AJAX_MESSAGE_SUCCSS);
        }
        return $success;
    }

    static function getConf($getItFromProduction=true) {
        require_once(JPATH_SITE . "/components/com_samlogin/simplesamlphp/lib/_autoload.php");
        if (isset($config)) {
            unset($config);
        }
      
        if ($getItFromProduction){
                 require(JPATH_SITE . "/components/com_samlogin/simplesamlphp/config/config" . self::$SAVECONF_PRODUCTION . ".php");
        }else{
            require(JPATH_SITE . "/components/com_samlogin/simplesamlphp/config/config" . self::$saveConfModeSuffix . ".php");
        }
        return $config;
    }

    static function saveConf($config, $app) {
        $success = true;
        $SSPConfPath = JPATH_COMPONENT_SITE . "/simplesamlphp/config/";

        $oldSSPConf = file_get_contents($SSPConfPath . "config" . self::$saveConfModeSuffix . ".php");
        $datetimestring = date('j_M_y_H_i_s', time());
        $fwrite = file_put_contents($SSPConfPath . "config.until_" . $datetimestring . self::$saveConfModeSuffix . ".php", $oldSSPConf);
        if ($fwrite == false) {
            SAMLoginControllerAjax::enqueueAjaxMessage("Failed while writing backup config file in $SSPConfPath, please check file permissions "
                    , SAMLoginControllerAjax::$AJAX_MESSAGE_WARNING);
        }
        $config["samlogin_lastchanged"] = array("deprecated");

        $newConfFileStr = array_smart_dump($config, "config");

        $fwrite = file_put_contents($SSPConfPath . "config" . self::$saveConfModeSuffix . ".php", "<?php /* This conf file was generated by samlogin for Joomla!, but you can modify it! */\n" . $newConfFileStr);
        if ($fwrite == false) {
            $success = false;
            SAMLoginControllerAjax::enqueueAjaxMessage("Failed while writing config file in $SSPConfPath, please check file permissions "
                    , SAMLoginControllerAjax::$AJAX_MESSAGE_DANGER);
        }

        // $app->enqueueMessage(JText::_('SAMLOGIN_GENCONF_OK'));
        if ($success) {
            SAMLoginControllerAjax::enqueueAjaxMessage("config" . self::$saveConfModeSuffix . ".php written"
                    , SAMLoginControllerAjax::$AJAX_MESSAGE_SUCCSS);
        }
        return $success;
    }

}

?>
