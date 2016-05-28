<?php
namespace delion\APISample;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use delion\APISample\LobiAPI;//ネームスペースは各自修正

class Main extends PluginBase{
	function onEnable(){
		$api = new LobiAPI();
		$mail = 'メールを入力';
		$password = 'パスワードを入力';
		$name_after = '変更後の名前を入力';
		$description_after = '変更後のプロフィール文を入力';

		if($api->Login($mail, $password)){//twitter認証の場合はTwitterLoginを使用する
			$this->getLogger()->info(TextFormat::AQUA.'ログイン成功');
		}
		else{
			$this->getLogger()->info(TextFormat::RED.'ログイン失敗');
		}

		$api->ChangeProfile($name_after, $description_after);
		$this->getLogger()->info(TextFormat::YELLOW.'プロフィールを変更しました。');
	}
}