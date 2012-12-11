<?php

namespace Ace\UtilitiesBundle\Tests\Handler;

use Ace\UtilitiesBundle\Handler\DefaultHandler;

class DefaultHandlerTest extends \PHPUnit_Framework_TestCase

{
	public function get_dataTest($url, $var, $value)
	{
		$this->assertTrue(FALSE);
//		$ch = curl_init();
//		$timeout = 10;
//		curl_setopt($ch,CURLOPT_URL,$url);
//		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
//		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
//
//		curl_setopt($ch,CURLOPT_POST,1);
//		curl_setopt($ch,CURLOPT_POSTFIELDS,$var.'='.$value);
//
//		$data = curl_exec($ch);
//		curl_close($ch);
//		return $data;
	}

	public function getTest($url)
	{
		$this->assertTrue(FALSE);
//		$ch = curl_init();
//		$timeout = 10;
//		curl_setopt($ch,CURLOPT_URL,$url);
//		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
//		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
//
//		$data = curl_exec($ch);
//		curl_close($ch);
//		return $data;
	}

	public function json_requestTest($url, $data)
	{
		$this->assertTrue(FALSE);
//		$ch = curl_init();
//		$timeout = 10;
//		curl_setopt($ch,CURLOPT_URL,$url);
//		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
//		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
//		curl_setopt($ch,CURLOPT_POST,1);
//		curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);
//		$data = curl_exec($ch);
//		curl_close($ch);
//		return $data;
	}

	public function default_textTest()
	{
		$this->assertTrue(FALSE);
//		$file = fopen($this::directory.$this::default_file, 'r');
//		$value = fread($file, filesize($this::directory.$this::default_file));
//		fclose($file);
//
//		return $value;
	}

	public function get_gravatarTest( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() )
	{
		$this->assertTrue(FALSE);
//		$url = 'http://www.gravatar.com/avatar/';
//		$url .= md5( strtolower( trim( $email ) ) );
//		$url .= "?s=$s&d=$d&r=$r";
//		if ( $img ) {
//			$url = '<img src="' . $url . '"';
//			foreach ( $atts as $key => $val ) $url .= ' ' . $key . '="' . $val . '"';
//			$url .= ' />';
//		}
//		return $url;
	}
}


