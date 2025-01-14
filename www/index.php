<?php
ob_start();
ob_clean();

$is404 = true;

session_start();
define("D_PATH", dirname(dirname(__FILE__)));
CONST APP_PATH = D_PATH."/v1";
include D_PATH."/.env/config.php";

const ACCEPTED_HEADERS = ["app.project.com"];

#load routes
require APP_PATH."/models/model.php";
require APP_PATH."/controllers/controller.php";
require APP_PATH."/routes/router.php";


if($is404 == true){
  include APP_PATH."/views/404.php";
}

 ?>
