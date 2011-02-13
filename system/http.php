<?php
function redirect($path){
	header('Location: http://'.$_SERVER['SERVER_NAME'].URL_ROOT."$path");
}