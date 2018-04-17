<?php
require_once "lightify.php";
require_once "config.php";

session_start();
if(isset($_GET["status"])) {
    $_SESSION["status"] =  $_GET["status"];
} else {
    $_SESSION["status"] = 0;
}

if(isset($_GET["deviceId"])) {
    $_SESSION["deviceId"] = $_GET["deviceId"];
    $redirectUrl = LightifyConnection::getLoginUrl(clientId, redirect);
    header("Location: ".$redirectUrl);
} else {
    echo "Missing GET-Parameter deviceId";
}
