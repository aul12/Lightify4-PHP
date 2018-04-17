<?php
require_once "lightify.php";
require_once "config.php";

session_start();

assert(isset($_GET["code"]));

$code = $_GET["code"];

$connection = new LightifyConnection(clientId,clientSecret, redirect);
$connection->generateToken($code);

$connection->setDevice($_SESSION["deviceId"], $_SESSION["status"]);

//"201409345-d01"