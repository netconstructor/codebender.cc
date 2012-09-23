<?php

namespace Ace\UtilitiesBundle\Handler;
 
class DefaultHandler
{
	public function get_data($url, $var, $value)
	{
		$ch = curl_init();
		$timeout = 10;
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);

		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$var.'='.$value);

		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

}






