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
			$boards[] = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}, "description":"The Uno is the latest in a series of USB Arduino boards, and the reference model for the Arduino platform. It has 14 digital input/output pins (of which 6 can be used as PWM outputs), 6 analog inputs, a 16 MHz ceramic resonator, a USB connection, a power jack, an ICSP header, and a reset button. It does not use the FTDI USB-to-serial driver chip. Instead, it features the Atmega16U2 (Atmega8U2 up to version R2) programmed as a USB-to-serial converter."}';
			$boards[] = '{"name":"Arduino Duemilanove w/ ATmega328","upload":{"protocol":"arduino","maximum_size":"30720","speed":"57600"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xda","extended_fuses":"0x05","path":"atmega","file":"ATmegaBOOT_168_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}, "description":"Around March 1st, 2009, the Duemilanove started to ship with the ATmega328p instead of the ATmega168. The ATmega328 has 32 KB, (also with 2 KB used for the bootloader)."}';
			$boards[] = '{"name":"Arduino Diecimila or Duemilanove w/ ATmega168","upload":{"protocol":"arduino","maximum_size":"14336","speed":"19200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xdd","extended_fuses":"0x00","path":"atmega","file":"ATmegaBOOT_168_diecimila.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega168","f_cpu":"16000000L","core":"arduino","variant":"standard"}, "description":"The Duemilanove automatically selects the appropriate power supply (USB or external power), eliminating the need for the power selection jumper found on previous boards. It also adds an easiest to cut trace for disabling the auto-reset, along with a solder jumper for re-enabling it."}';
			$boards[] = '{"name":"Arduino Nano w/ ATmega328","upload":{"protocol":"arduino","maximum_size":"30720","speed":"57600"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xda","extended_fuses":"0x05","path":"atmega","file":"ATmegaBOOT_168_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"eightanaloginputs"}, "description":"The Arduino Nano is an all-in-one, compact design for use in breadboards. Version 3.0 has an ATmega328."}';
			$boards[] = '{"name":"Arduino Nano w/ ATmega168","upload":{"protocol":"arduino","maximum_size":"14336","speed":"19200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xdd","extended_fuses":"0x00","path":"atmega","file":"ATmegaBOOT_168_diecimila.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega168","f_cpu":"16000000L","core":"arduino","variant":"eightanaloginputs"}, "description":"Older Arduino Nano with ATmega168 instead of the newer ATmega328."}';
			$boards[] = '{"name":"Arduino Mega 2560 or Mega ADK","upload":{"protocol":"stk500v2","maximum_size":"258048","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xd8","extended_fuses":"0xfd","path":"stk500v2","file":"stk500boot_v2_mega2560.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega2560","f_cpu":"16000000L","core":"arduino","variant":"mega"}, "description":"The Mega 2560 is an update to the Arduino Mega, which it replaces. It features the Atmega2560, which has twice the memory, and uses the ATMega 8U2 for USB-to-serial communication."}';
			$boards[] = '{"name":"Arduino Mega (ATmega1280)","upload":{"protocol":"arduino","maximum_size":"126976","speed":"57600"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xda","extended_fuses":"0xf5","path":"atmega","file":"ATmegaBOOT_168_atmega1280.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega1280","f_cpu":"16000000L","core":"arduino","variant":"mega"}, "description":"A larger, more powerful Arduino board. Has extra digital pins, PWM pins, analog inputs, serial ports, etc. The original Arduino Mega has an ATmega1280 and an FTDI USB-to-serial chip."}';
			$boards[] = '{"name":"Arduino Leonardo","upload":{"protocol":"avr109","maximum_size":"28672","speed":"57600", "disable_flushing":"true"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xd8","extended_fuses":"0xcb","path":"caterina","file":"Caterina-Leonardo.hex","unlock_bits":"0x3F","lock_bits":"0x2F"},"build":{"vid":"0x2341","pid":"0x8036","mcu":"atmega32u4","f_cpu":"16000000L","core":"arduino","variant":"leonardo"}, "description":"The Leonardo differs from all preceding boards in that the ATmega32u4 has built-in USB communication, eliminating the need for a secondary processor. This allows the Leonardo to appear to a connected computer as a mouse and keyboard, in addition to a virtual (CDC) serial / COM port."}';
			$boards[] = '{"name":"Arduino Mini w/ ATmega328","upload":{"protocol":"stk500","maximum_size":"28672","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xd8","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328-Mini.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"eightanaloginputs"}, "description":"The Mini is a compact Arduino board, intended for use on breadboards and when space is at a premium. This version has an ATmega328."}';
			$boards[] = '{"name":"Arduino Mini w/ ATmega168","upload":{"protocol":"arduino","maximum_size":"14336","speed":"19200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xdd","extended_fuses":"0x00","path":"atmega","file":"ATmegaBOOT_168_ng.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega168","f_cpu":"16000000L","core":"arduino","variant":"eightanaloginputs"}, "description":"Older Arduino Mini version with the ATmega168 microcontroller."}';
			$boards[] = '{"name":"Arduino Ethernet","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}, "description":"The Ethernet differs from other boards in that it does not have an onboard USB-to-serial driver chip, but has a Wiznet Ethernet interface. This is the same interface found on the Ethernet shield."}';
			$boards[] = '{"name":"Arduino Fio","upload":{"protocol":"arduino","maximum_size":"30720","speed":"57600"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xda","extended_fuses":"0x05","path":"atmega","file":"ATmegaBOOT_168_atmega328_pro_8MHz.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"8000000L","core":"arduino","variant":"eightanaloginputs"}, "description":"An Arduino intended for use as a wireless node. Has a header for an XBee radio, a connector for a LiPo battery, and a battery charging circuit."}';

	/*
			$bt328 = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}, "description":""}';
			$bt = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}, "description":""}';
			$lilypad328 = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}, "description":""}';
			$lilypad = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}, "description":""}';
			$pro5v328 = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}, "description":""}';
			$pro5v = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}, "description":""}';
			$pro328 = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}, "description":""}';
			$pro = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}, "description":""}';
			$atmega168 = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}, "description":""}';
			$atmega8 = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}, "description":""}';
			$atmega644 = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}, "description":""}';
			$atmega12848m = '{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}, "description":""}';
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






