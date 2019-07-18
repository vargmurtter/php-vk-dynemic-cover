<?php

ini_set('display_errors', 1);
ini_set("error_log", "/mnt/c/lamp/php-error.log");
error_reporting(E_ALL);

require_once "config.php";
require_once "./vendor/autoload.php";
require_once "CoverManager.php";

use VK\Client\Enums\VKLanguage;
use VK\Client\VKApiClient;

const SERVICE_KEY = "7218fb167218fb167218fb164d72734545772187218fb162f37a06544d3b9bd49c6b384";
$mysql = new mysqli(Config::$dbhost, Config::$dbuser, Config::$dbpass, Config::$dbname);



$vk = new VKApiClient('5.50', VKLanguage::RUSSIAN);
$result = $vk->wall()->getById(SERVICE_KEY, [
	'posts' => '-184525610_4'
]);

$commentsCount = $result[0]["comments"]["count"];

$mysql->query("UPDATE settings SET comments=$commentsCount");

$cm = new CoverManager();
$cm->GenerateCover("_preview.jpg");
$cm->SetupCover();

?>