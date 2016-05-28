<?php
namespace delion\LobiAPI\HttpAPI;

class Header{
	public $Host = '';
	public $Connection = true;
	public $Accept = '';
	public $UserAgent = '';
	public $Referer = '';
	public $AcceptEncoding = '';
	public $AcceptLanguage = '';
	public $Origin = '';

	public function setHost($host){
		$this->Host = $host;
		return $this;
	}
	public function setConnection($connection){
		$this->Connection = $connection;
		return $this;
	}
	public function setAccept($accept){
		$this->Accept = $accept;
		return $this;
	}
	public function setUserAgent($useragent){
		$this->UserAgent = $useragent;
		return $this;
	}
	public function setReferer($referer){
		$this->Referer = $referer;
		return $this;
	}
	public function setAcceptEncoding($encoding){
		$this->AcceptEncoding = $encoding;
		return $this;
	}
	public function setAcceptLanguage($language){
		$this->AcceptLanguage = $language;
		return $this;
	}
	public function setOrigin($origin){
		$this->Origin = $origin;
		return $this;
	}
}