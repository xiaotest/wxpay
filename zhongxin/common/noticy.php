<?php
require_once dirname(__FILE__).'/log.php';
$logHandler = new CLogFileHandler("../logs/" . date('Y-m-d') . '.log');
$log = Log::Init($logHandler, 15);

function decodeUnicode($str)
{
    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
        create_function(
            '$matches',
            'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
        ),
        $str);
}

$postStr = $_POST;
if (sizeof($_POST) == 0) {
    $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
} else {
    $postStr = $_POST;
}
Log::DEBUG('data'.json_encode($postStr));

