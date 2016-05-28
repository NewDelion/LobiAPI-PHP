<?php
namespace delion\LobiAPI\HttpAPI;

class Http{
	public $cookie_path;

	public function __construct($cookie_file_path = ''){
		$path = ($cookie_file_path == '' ? 'cookie.txt' : $cookie_file_path);
		if(file_exists($path))
			unlink($path);
		touch($path);
		$this->cookie_path = $path;
	}

	public function get($url, $header){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$req_header = [];
		if($header->Host != '')
			$req_header[] = 'Host: ' . $header->Host;
		if($header->Connection)
			$req_header[] = 'Connection: keep-alive';
		if($header->Accept != '')
			$req_header[] = 'Accept: ' . $header->Accept;
		if($header->UserAgent != '')
			$req_header[] = 'User-Agent: ' . $header->UserAgent;
		if($header->Referer != '')
			$req_header[] = 'Referer: ' . $header->Referer;
		if($header->AcceptEncoding != '')
			$req_header[] = 'Accept-Encoding: ' . $header->AcceptEncoding;
		if($header->AcceptLanguage != '')
			$req_header[] = 'Accept-Language: ' . $header->AcceptLanguage;
		if(count($req_header) > 0)
			curl_setopt($ch, CURLOPT_HTTPHEADER, $req_header);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_path);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_path);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	public function post($url, $data, $header){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$req_header = [];
		if($header->Host != '')
			$req_header[] = 'Host: ' . $header->Host;
		if($header->Connection)
			$req_header[] = 'Connection: keep-alive';
		if($header->Accept != '')
			$req_header[] = 'Accept: ' . $header->Accept;
		if($header->Origin != '')
			$req_header[] = 'Origin: ' . $header->Origin;
		if($header->UserAgent != '')
			$req_header[] = 'User-Agent: ' . $header->UserAgent;
		if($header->Referer != '')
			$req_header[] = 'Referer: ' . $header->Referer;
		if($header->AcceptEncoding != '')
			$req_header[] = 'Accept-Encoding: ' . $header->AcceptEncoding;
		if($header->AcceptLanguage != '')
			$req_header[] = 'Accept-Language: ' . $header->AcceptLanguage;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $req_header);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_path);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_path);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
}