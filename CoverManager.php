<?php

	require_once "config.php";
	require_once "./vendor/autoload.php";

	use VK\Client\Enums\VKLanguage;
	use VK\Client\VKApiClient;


	class CoverManager
	{

		private $mysql;
		private $tempImagePath = "temp.jpg";

		public function __construct()
		{
			$this->mysql = new mysqli(Config::$dbhost, Config::$dbuser, Config::$dbpass, Config::$dbname);
		}

		public function __destruct()
		{
			$this->mysql->close();
		}

		public function GenerateCover($coverTemplte)
		{
			$img = new Imagick($coverTemplte);
			$draw = new ImagickDraw();

			$draw->setFillColor('white');
			$draw->setFontFamily('Bookman-DemiItalic');
			$draw->setFontSize(60);

			$query = $this->mysql->query("SELECT * FROM settings WHERE id=1");
			$result = $query->fetch_assoc();

			$img->annotateImage($draw, 390, 235, -5, strval($result['comments']));
			$img->annotateImage($draw, 1190, 165, -5, strval($result['smartphones']));

			file_put_contents ($this->tempImagePath, $img);
		}

		public function SetupCover()
		{

			$post_data = array('photo' => new CURLFile($this->tempImagePath));

			$vk = new VKApiClient('5.50', VKLanguage::RUSSIAN);
			$url = $vk->photos()->getOwnerCoverPhotoUploadServer(Config::$vk_token, [
				'group_id' => 184525610,
				'crop_x2' => 1590,
				'crop_y2' => 400
			]);

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url['upload_url']);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
			$result = json_decode(curl_exec($curl), true);

			$vk->photos()->saveOwnerCoverPhoto(Config::$vk_token, [
				'hash' => $result['hash'],
				'photo' => $result['photo']
			]);

		}

		public function ChangeSmartphones($count)
		{
			$this->mysql->query("UPDATE settings SET smartphones=$count");
		}

	}
	

?>