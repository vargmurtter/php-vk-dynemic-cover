<?php
	
	require_once "./vendor/autoload.php";
	require_once "CoverManager.php";

	use VK\Client\Enums\VKLanguage;
	use VK\Client\VKApiClient;

	ini_set('display_errors', 1);
	ini_set("error_log", "/mnt/c/lamp/php-error.log");
	error_reporting(E_ALL);

	const VK_TOKEN = "1b2d86e4558c3158e4908b39af810001f42ab70311fa23f2bd8d15fed57345ac2c51e9570c7dd97c85c75";

	$vk = new VKApiClient('5.50', VKLanguage::RUSSIAN);
	
	$json = file_get_contents("php://input");

	$data = json_decode($json, true);

	if($data['type'] === "message_new"){

		$message = $data['object'];

		$userId = $message['user_id'];
		$body = $message['body'];

		$answer = "";
		if (ctype_digit(trim($body))){

			$cm = new CoverManager();
			$cm->ChangeSmartphones( (int)$body );
			$cm->GenerateCover("_preview.jpg");
			$cm->SetupCover();

			$answer = "Оки, обложка изменена :)";
		}else{
			$answer = "Напиши целое число плез...";
		}

		$response = $vk->messages()->send(VK_TOKEN, [
			'peer_id' => $userId,
			'message' => $answer
		]);
	}

	echo "f94f9275";

	
?>