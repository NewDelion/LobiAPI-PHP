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

		return $this->NetworkAPI->post('https://lobi.co/signin', $post_data, $header2);
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