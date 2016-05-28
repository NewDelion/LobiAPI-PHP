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
		$header1 = new Header()
			.setHost('lobi.co')
			.setConnection(true)
			.setAccept('text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8')
			.setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36')
			.setAcceptLanguage('ja,en-US;q=0.8,en;q=0.6');

		$source = $this->NetworkAPI->get('https://lobi.co/signin', $header1);
		echo $source;
	}
}