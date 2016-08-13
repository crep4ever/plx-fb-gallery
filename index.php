<?php

set_include_path(get_include_path() . PATH_SEPARATOR . 'PluXml');

//----------------------------------------------------------------------------
// Fetch Facebook albums and convert them into PluXml articles
//----------------------------------------------------------------------------

require("class.fb.galery.php");

$fb_access_token = "";
$fb_page_id = "160141077367724";
$fb_graph_api = "v2.7";
$ignore_list = ['Timeline Photos', 'Mobile Uploads', 'Cover Photos', 'Profile Pictures', 'Logo du club'];
$galery = new FacebookGallery($fb_page_id, $fb_access_token, $fb_graph_api);
$galery->setIgnoreList($ignore_list);
$articles = $galery->toArticles();

//----------------------------------------------------------------------------
// Init PluXml session to register facebook articles
//----------------------------------------------------------------------------

define('PLX_ROOT', './PluXml/');
define('PLX_CORE', PLX_ROOT.'core/');
include(PLX_ROOT.'config.php');
include(PLX_CORE.'lib/config.php');

session_start();

include(PLX_CORE.'lib/class.plx.timezones.php');
include(PLX_CORE.'lib/class.plx.date.php');
include(PLX_CORE.'lib/class.plx.glob.php');
include(PLX_CORE.'lib/class.plx.token.php');
include(PLX_CORE.'lib/class.plx.plugins.php');
include(PLX_CORE.'lib/class.plx.motor.php');
include(PLX_CORE.'lib/class.plx.admin.php');
include(PLX_CORE.'lib/class.plx.msg.php');

$plxAdmin = plxAdmin::getInstance();

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : $plxAdmin->aConf['default_lang'];
loadLang(PLX_CORE.'lang/'.$lang.'/admin.php');

// Write article as xml file
$count = intval($plxAdmin->nextIdArticle());
foreach ($articles as $article)
{
  $id = str_pad($count++, 4, '0', STR_PAD_LEFT);
  $plxAdmin->editArticle($article->content(), $id);
}

//----------------------------------------------------------------------------
// Redirect to PluXml website
//----------------------------------------------------------------------------

header("Location: ./PluXml/index.php");
exit();
?>
