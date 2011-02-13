<?php
ini_set('display_errors', true);
session_start();
define('APP_PATH', '');
define('URL_ROOT', '/forum/');

require_once('system/array.php');
require_once('system/sql.php');
require_once('system/html.php');
require_once('system/http.php');
require_once('system/markup.php');

mysql_connect('xentricsforum.db.5298451.hostedresource.com', 'xentricsforum', 'mmmFFF15');
mysql_select_db('xentricsforum');

define('PAGE_SIZE', 10);
/*END FOF FILE*/