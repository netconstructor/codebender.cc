<?php

namespace Ace\UtilitiesBundle\Handler;
 
class DefaultHandler
{
	const default_file = "default_text.txt";
	const directory = "../../";
	
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
	
	public function get($url)
	{
		$ch = curl_init();
		$timeout = 10;
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);

		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	public function json_request($url, $data)
	{
		$ch = curl_init();
		$timeout = 10;
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	public function default_text()
	{
		$file = fopen($this::directory.$this::default_file, 'r');
		$value = fread($file, filesize($this::directory.$this::default_file));
		fclose($file);
		
		return $value;
	}
	
	/**
		* Get either a Gravatar URL or complete image tag for a specified email address.
		*
		* @param string $email The email address
		* @param string $s Size in pixels, defaults to 80px [ 1 - 512 ]
		* @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
		* @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
		* @param boole $img True to return a complete IMG tag False for just the URL
		* @param array $atts Optional, additional key/value attributes to include in the IMG tag
		* @return String containing either just a URL or a complete image tag
		* @source http://gravatar.com/site/implement/images/php/
	*/
	public function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() )
	{
		$url = 'http://www.gravatar.com/avatar/';
		$url .= md5( strtolower( trim( $email ) ) );
		$url .= "?s=$s&d=$d&r=$r";
		if ( $img ) {
			$url = '<img src="' . $url . '"';
			foreach ( $atts as $key => $val ) $url .= ' ' . $key . '="' . $val . '"';
			$url .= ' />';
		}
		return $url;
	}
	
		public function get_boards()
		{
			$boards = array();
			$boards[] = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}';
			$boards[] = '{"name":"Arduino Duemilanove w/ ATmega328","upload":{"protocol":"arduino","maximum_size":"30720","speed":"57600"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xda","extended_fuses":"0x05","path":"atmega","file":"ATmegaBOOT_168_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}';
			$boards[] = '{"name":"Arduino Diecimila or Duemilanove w/ ATmega168","upload":{"protocol":"arduino","maximum_size":"14336","speed":"19200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xdd","extended_fuses":"0x00","path":"atmega","file":"ATmegaBOOT_168_diecimila.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega168","f_cpu":"16000000L","core":"arduino","variant":"standard"}}';
			$boards[] = '{"name":"Arduino Nano w/ ATmega328","upload":{"protocol":"arduino","maximum_size":"30720","speed":"57600"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xda","extended_fuses":"0x05","path":"atmega","file":"ATmegaBOOT_168_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"eightanaloginputs"}}';
			$boards[] = '{"name":"Arduino Nano w/ ATmega168","upload":{"protocol":"arduino","maximum_size":"14336","speed":"19200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xdd","extended_fuses":"0x00","path":"atmega","file":"ATmegaBOOT_168_diecimila.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega168","f_cpu":"16000000L","core":"arduino","variant":"eightanaloginputs"}}';
			$boards[] = '{"name":"Arduino Mega 2560 or Mega ADK","upload":{"protocol":"stk500v2","maximum_size":"258048","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xd8","extended_fuses":"0xfd","path":"stk500v2","file":"stk500boot_v2_mega2560.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega2560","f_cpu":"16000000L","core":"arduino","variant":"mega"}}';
			$boards[] = '{"name":"Arduino Mega (ATmega1280)","upload":{"protocol":"arduino","maximum_size":"126976","speed":"57600"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xda","extended_fuses":"0xf5","path":"atmega","file":"ATmegaBOOT_168_atmega1280.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega1280","f_cpu":"16000000L","core":"arduino","variant":"mega"}}';
			$boards[] = '{"name":"Arduino Leonardo","upload":{"protocol":"arduino","maximum_size":"28672","speed":"1200"},"bootloader":{"low_fuses":"0xde","high_fuses":"0xd8","extended_fuses":"0xcb","path":"diskloader","file":"DiskLoader-Leonardo.hex","unlock_bits":"0x3F","lock_bits":"0x2F"},"build":{"mcu":"atmega32u4","f_cpu":"16000000L","core":"arduino","variant":"leonardo"}}';
			$boards[] = '{"name":"Arduino Micro","upload":{"protocol":"arduino","maximum_size":"30720","speed":"1200"},"bootloader":{"low_fuses":"0xde","high_fuses":"0xda","extended_fuses":"0xcb","path":"diskloader","file":"DiskLoader-Micro.hex","unlock_bits":"0x3F","lock_bits":"0x2F"},"build":{"mcu":"atmega32u4","f_cpu":"16000000L","core":"arduino","variant":"micro"}}';
			$boards[] = '{"name":"Arduino Mini w/ ATmega328","upload":{"protocol":"stk500","maximum_size":"28672","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xd8","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328-Mini.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"eightanaloginputs"}}';
			$boards[] = '{"name":"Arduino Mini w/ ATmega168","upload":{"protocol":"arduino","maximum_size":"14336","speed":"19200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xdd","extended_fuses":"0x00","path":"atmega","file":"ATmegaBOOT_168_ng.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega168","f_cpu":"16000000L","core":"arduino","variant":"eightanaloginputs"}}';
			$boards[] = '{"name":"Arduino Ethernet","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}';
			$boards[] = '{"name":"Arduino Fio","upload":{"protocol":"arduino","maximum_size":"30720","speed":"57600"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xda","extended_fuses":"0x05","path":"atmega","file":"ATmegaBOOT_168_atmega328_pro_8MHz.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"8000000L","core":"arduino","variant":"eightanaloginputs"}}';

	/*
			$bt328 = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}';
			$bt = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}';
			$lilypad328 = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}';
			$lilypad = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}';
			$pro5v328 = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}';
			$pro5v = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}';
			$pro328 = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}';
			$pro = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}';
			$atmega168 = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}';
			$atmega8 = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}';
			$atmega644 = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}';
			$atmega12848m = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}';
	*/

			foreach($boards as $key => $board)
			{
				$boards[$key] = json_decode($board, true);
				// if($board["name"] == $boardname)
				// {
				// 	$response = array("success" => true, "board" => $board);
				// 	return new Response(json_encode($response));
				// }
			}
			return json_encode($boards);
		}

}






