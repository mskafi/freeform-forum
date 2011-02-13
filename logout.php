<?php
require_once('boot.php');

session_start();
session_destroy();
redirect('index.php');
/*END OF FILE*/