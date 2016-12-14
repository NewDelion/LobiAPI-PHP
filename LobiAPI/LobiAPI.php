<?php
namespace delion\LobiAPI;

use delion\LobiAPI\HttpAPI\Http;
use delion\LobiAPI\HttpAPI\Header;

class LobiAPI{
	private $NetworkAPI = null;

	public function __construct(){
		$this->NetworkAPI = new Http();
	}

	public function Login($mail, $password){
		$header1 = (new Header())
			->setAccept('text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		$source = $this->NetworkAPI->get('https://lobi.co/signin', $header1);
		$csrf_token = Pattern::get_string($source, Pattern::$csrf_token, '"');

		$post_data = sprintf('csrf_token=%s&email=%s&password=%s', $csrf_token, $mail, $password);
		$header2 = (new Header())
			->setAccept('text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		return strpos($this->NetworkAPI->post('https://lobi.co/signin', $post_data, $header2), 'ログインに失敗しました') === false;
	}

	public function TwitterLogin($mail, $password){
		$header1 = (new Header())
			->setAccept('text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		$source = $this->NetworkAPI->get('https://lobi.co/signup/twitter', $header1);
		$authenticity_token = Pattern::get_string($source, Pattern::$authenticity_token, '"');
		$redirect_after_login = Pattern::get_string($source, Pattern::$redirect_after_login, '"');
		$oauth_token = Pattern::get_string($source, Pattern::$oauth_token, '"');

		$post_data = 'authenticity_token=' . $authenticity_token . '&redirect_after_login=' . $redirect_after_login . '&oauth_token=' . $oauth_token . '&session%5Busername_or_email%5D=' . $mail . '&session%5Bpassword%5D=' . $password;
		$header2 = (new Header())
			->setAccept('text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		$source2 = $this->NetworkAPI->post('https://api.twitter.com/oauth/authorize', $post_data, $header2);
		if(strpos($source2, 'Twitterにログイン') !== false)
			return false;

		return strpos($this->NetworkAPI->get(Pattern::get_string($source2, Pattern::$twitter_redirect_to_lobi, '"'), $header1), 'ログインに失敗しました') === false;
	}

	public function GetMe(){
		$header = (new Header())
			->setAccept('application/json, text/plain, */*')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		return json_decode($this->NetworkAPI->get('https://web.lobi.co/api/me?fields=premium', $header), false);
	}

	public function GetPublicGroupList(){
		$header = (new Header())
			->setAccept('application/json, text/plain, */*')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		$result = [];

		$index = 1;
		while(true){
			$pg = json_decode($this->NetworkAPI->get("https://web.lobi.co/api/public_groups?count=1000&page=$index&with_archived=1", $header), false);
			$index++;
			if(count($pg[0]->items) == 0)
				break;
			foreach($pg as $pgbf)
				$result[] = $pg;
		}

		return $result;
	}

	public function GetPrivateGroupList(){
		$header = (new Header())
			->setAccept('application/json, text/plain, */*')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		$result = [];

		$index = 1;
		while(true){
			$pg = json_decode($this->NetworkAPI->get("https://web.lobi.co/api/groups?count=1000&page=$index", $header), false);
			$index++;
			if(count($pg[0]->items) == 0)
				break;
			foreach($pg as $pgbf)
				$result[] = $pg;
		}

		return $result;
	}

	public function GetNotifications(){
		$header = (new Header())
			->setAccept('application/json, text/plain, */*')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		return json_decode($this->NetworkAPI->get('https://web.lobi.co/api/info/notifications?platform=any', $header), false);
	}

	public function GetContacts($uid){
		$header = (new Header())
			->setAccept('application/json, text/plain, */*')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		return json_decode($this->NetworkAPI->get("https://web.lobi.co/api/user/$uid/contacts", $header), false);
	}

	public function GetFollowers($uid){
		$header = (new Header())
			->setAccept('application/json, text/plain, */*')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		return json_decode($this->NetworkAPI->get("https://web.lobi.co/api/user/$uid/followers", $header), false);
	}

	public function GetGroup($uid){
		$header = (new Header())
			->setAccept('application/json, text/plain, */*')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		return json_decode($this->NetworkAPI->get("https://web.lobi.co/api/group/$uid?error_flavor=json2&fields=group_bookmark_info%2Capp_events_info", $header), false);
	}

	public function GetGroupMembersCount($uid){
		$header = (new Header())
			->setAccept('application/json, text/plain, */*')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		$result = json_decode($this->NetworkAPI->get("https://web.lobi.co/api/group/$uid?error_flavor=json2&fields=group_bookmark_info%2Capp_events_info", $header), false);
		if(!isset($result->members_count))
			return 0;
		if($result->members_count == null)
			return 0;
		return $result->members_count;
	}

	public function GetGroupMembers($uid){
		$header = (new Header())
			->setAccept('application/json, text/plain, */*')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		$result = [];
		$next = '0';
		$limit = 10000;
		while($limit-- > 0){
			$g = json_decode($this->NetworkAPI->get("https://web.lobi.co/api/group/$uid?members_cursor=$next", $header), false);
			foreach($g->members as $m)
				$result[] = $m;
			if($g->members_next_cursor == 0)
				break;
			$next = $g->members_next_cursor;
		}

		return $result;
	}

	public function GetThreads($uid, $count = 20){
		$header = (new Header())
			->setAccept('application/json, text/plain, */*')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		return json_decode($this->NetworkAPI->get("https://web.lobi.co/api/group/$uid/chats?count=$count", $header), false);
	}
	
	public function GetReplies($uid,$chatid){
        	$header = (new Header())
           		->setAccept('application/json, text/plain, */*')
          	 	->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
          	 	->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

       		return json_decode($this->NetworkAPI->get("https://web.lobi.co/api/group/$uid/chats/replies?to=$chatid", $header),true);
   	}

	public function Goo($group_id, $chat_id){
		$header = (new Header())
			->setAccept('application/json, text/plain, */*')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		$data = ['test'=>'test_content'];

		$this->NetworkAPI->post('https://web.lobi.co/api/group/$group_id/chats/like', $data, $header);
	}

	public function UnGoo($group_id, $chat_id){
		$header = (new Header())
			->setAccept('application/json, text/plain, */*')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		$data = ['id' => $chat_id];

		$this->NetworkAPI->post("https://web.lobi.co/api/group/$group_id/chats/unlike", $data, $header);
	}

	public function Boo($group_id, $chat_id){
		$header = (new Header())
			->setAccept('application/json, text/plain, */*')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		$data = ['id' => $chat_id];

		$this->NetworkAPI->post("https://web.lobi.co/api/group/$group_id/chats/boo", $data, $header);
	}

	public function UnBoo($group_id, $chat_id){
		$header = (new Header())
			->setAccept('application/json, text/plain, */*')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		$data = ['id' => $chat_id];

		$this->NetworkAPI->post("https://web.lobi.co/api/group/$group_id/chats/unboo", $data, $header);
	}

	public function Follow($user_id){
		$header = (new Header())
			->setAccept('application/json, text/plain, */*')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		$data = ['users' => $user_id];

		$this->NetworkAPI->post("https://web.lobi.co/api/me/contacts", $data, $header);
	}

	public function UnFollow($user_id){
		$header = (new Header())
			->setAccept('application/json, text/plain, */*')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		$data = ['users' => $user_id];

		$this->NetworkAPI->post("https://web.lobi.co/api/me/contacts/remove", $data, $header);
	}

	public function MakeThread($group_id, $message, $shout = false){
		$header = (new Header())
			->setAccept('application/json, text/plain, */*')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		$data = [
			'type' => $shout ? 'shout' : 'normal',
			'lang' => 'ja',
			'message' => $message
		];

		$this->NetworkAPI->post("https://web.lobi.co/api/group/$group_id/chats", $data, $header);
	}

	public function Reply($group_id, $thread_id, $message){
		$header = (new Header())
			->setAccept('application/json, text/plain, */*')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		$data = [
			'type' => 'normal',
			'lang' => 'ja',
			'message' => $message,
			'reply_to' => $thread_id
		];

		$this->NetworkAPI->post("https://web.lobi.co/api/group/$group_id/chats", $data, $header);
	}

	public function MakePrivateGroup($user_id){
		$header = (new Header())
			->setAccept('application/json, text/plain, */*')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		$data = ['user' => $user_id];

		$this->NetworkAPI->post('https://web.lobi.co/api/groups/1on1s', $data, $header);
	}

	public function ChangeProfile($name, $description){
		$header = (new Header())
			->setAccept('application/json, text/plain, */*')
			->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			->setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		$data = [
			'name' => $name,
			'description' => $description
		];

		$this->NetworkAPI->post("https://web.lobi.co/api/me/profile", $data, $header);
	}
}

class Pattern{
	public static $csrf_token = '<input type="hidden" name="csrf_token" value="';
	public static $authenticity_token = '<input name="authenticity_token" type="hidden" value="';
	public static $redirect_after_login = '<input name="redirect_after_login" type="hidden" value="';
	public static $oauth_token = '<input id="oauth_token" name="oauth_token" type="hidden" value="';
	public static $twitter_redirect_to_lobi = '<a class="maintain-context" href="';
	public static function get_string($source, $pattern, $end_pattern){
		$start = strpos($source, $pattern) + strlen($pattern);
		$end = strpos($source, $end_pattern, $start + 1);
		return substr($source, $start, $end - $start);
	}
}
