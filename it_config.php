<?php
ini_set('include_path','/var/www/sarotam_staging/home/'.PATH_SEPARATOR.'/var/www/sarotam_staging/home/Classes/'.PATH_SEPARATOR.ini_get('include_path'));
define("DEF_SITEURL", "http://localhost/sarotam_staging/home/");
define("DB_SERVER", "localhost");
define("DB_NME","sarotam_staging");
define("DB_USR", "dbusr");
define("DB_PWD", "dbpass");

//define("DEF_SMSURL","http://www.myvaluefirst.com/smpp/sendsms?username=%u&password=%p&to=%tcust&udh=%h&from=%frm&text=%txtmsg&dlr-url=%durl");
define("DEF_SMSURL","http://www.myvaluefirst.com/smpp/sendsms?");
define("DEF_ISSUE_URL","http://192.168.0.106/site_new/home/signon.php");
//define("DEF_ISSUE_URL","http://tracker.intouchrewards.com/signon.php");
define("DEF_ISSUE_AUTHKEY","384b184f0b3ddbbd1519add74c57aec2");
define("DEF_ISSUE_PROJECTID","99");

# log msg types
define("LOG_MSGTYPE_ERROR", 1);
define("LOG_MSGTYPE_WARNING", 2);
define("LOG_MSGTYPE_DEBUG", 3);
define("LOG_MSGTYPE_REPLY", 4);
define("LOG_MSGTYPE_TRIAL", 5);
define("LOG_MSGTYPE_INFO", 6);
define("LOG_MSGTYPE_EXCEPTION", 7);

define("DEF_INTOUCH_STARTNO", 1090808);
define("DEF_SMS_PHONENO", "09220092200");
define("DEF_VCODE_OFFSET", 50000);

define("config_disable_email",true);
define("config_disable_sms",true);

if (!defined("DEF_PAGE_TITLE")) define("DEF_PAGE_TITLE", "IPN");
if (!defined("DEF_PAGE_KEYWORDS")) define("DEF_PAGE_KEYWORDS", "IntouchRewards.com,WeikField Kiosks");
if (!defined("DEF_PAGE_DESCRIPTION")) define("DEF_PAGE_DESCRIPTION", "Portal for WeikField Kiosks");

$gConfig = array(
'checkin_repeat_hours' => 5,
);

define("DEF_DEBUG", true);

if (DEF_DEBUG) {
error_reporting(E_ALL);
ini_set('display_errors', '1');
}

$config_disable_email = "false";
$config_disable_sms = "true";
/*define("STATUS_NOT_PROCESSED", 0);
define("STATUS_SUCCESS", 1);
define("STATUS_ERROR", 2);
define("STATUS_REPRINT", 3);
define("STATUS_REPROCESS", 4);
define("STATUS_DEALER_NOT_RESOLVED", 11);
define("STATUS_ITEMS_NOT_RESOLVED", 12);
$STATUS = array (
	STATUS_NOT_PROCESSED => array("Not Processed", "Process All", "admin/invoices/processall"),
	STATUS_SUCCESS => array("Processed", "Run Again", "admin/invoices/processall/status=".STATUS_SUCCESS."/"),
	STATUS_ERROR => array("Error Processing", "Process Again", "admin/invoices/processall/status=".STATUS_ERROR."/"),
	STATUS_REPRINT => array("Reprint"),
	STATUS_REPROCESS => array("Reprocess", "Run", "admin/invoices/processall/status=".STATUS_REPROCESS."/"),
	STATUS_DEALER_NOT_RESOLVED => array("Dealer Not Resolved", "Resolve Dealers", "admin/dealers/resolve/"),
	STATUS_ITEMS_NOT_RESOLVED => array("Items Not Resolved", "Resolve Items", "admin/items/resolve/"),
);

function getStatusMsg($status) {
	global $STATUS;
	return $STATUS[$status][0];
}
*/

function logthis($msg) {
    error_log(date("H:i:s").":$msg\n", 3, "/tmp/weikfield_kiosks.log");
}
?>
