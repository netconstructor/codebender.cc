<?php

namespace Ace\StaticBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
	public function testAboutAction_Generic()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/static/about');

		$this->assertEquals(1, $crawler->filter('html:contains("we help you write and share code")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("State of the art editor. Awesome compiler.")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Hassle-free. USB cable or the cloud. It doesn\'t matter.")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("From DIY to DIT. Share and Collaborate.")')->count());
		$this->assertGreaterThanOrEqual(3, $crawler->filter('h3')->count());
		$this->assertGreaterThanOrEqual(3, $crawler->filter('h4')->count());
	}

	public function testAboutAction_LoggedIn()
	{
//		$client = static::createClient();
//
//		$crawler = $client->request('GET', '/static/about');
		$this->assertTrue(false);
	}

	public function testAboutAction_Anonymous()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/static/about');

		$this->assertEquals(1, $crawler->filter('html:contains("Sign Up")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Start coding in minutes.")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Hi! Why don\'t you sign up for a codebender account?")')->count());
		$this->assertEquals(1, $crawler->filter('input[value=Register]')->count());
	}

	public function testTechAction()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/static/tech');

		$this->assertEquals(1, $crawler->filter('html:contains("Cloud IDE")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Makers\' Hub")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Documentation and Suggestions")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("under the hood")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Open Source")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Ariadne Bootloader")')->count());
		$this->assertGreaterThanOrEqual(2, $crawler->filter('h1')->count());
		$this->assertGreaterThanOrEqual(11, $crawler->filter('h3')->count());
		$this->assertGreaterThanOrEqual(12, $crawler->filter('h4')->count());
	}

	public function testTutorialsAction()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/static/tutorials');

		$this->assertEquals(1, $crawler->filter('html:contains("Learn how to use codebender")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Walkthrough Video!")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Plugin Tutorial!")')->count());
		$this->assertGreaterThanOrEqual(1, $crawler->filter('h1')->count());
		$this->assertGreaterThanOrEqual(2, $crawler->filter('h3')->count());
	}

	public function testTeamAction()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/static/team');

		$this->assertEquals(1, $crawler->filter('html:contains("Vasilis Georgitzikis")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Stelios Tsampas")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Dimitris Amaxilatis")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Maria Kousta")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Markellos Orfanos")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Dimitris Dimakopoulos")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Dimitrios Christidis")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Alexandros Baltas")')->count());
		$this->assertGreaterThanOrEqual(8, $crawler->filter('h2')->count());
		$this->assertGreaterThanOrEqual(3, $crawler->filter('h1')->count());
	}

	public function testPluginAction()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/static/plugin');

		$this->assertEquals(1, $crawler->filter('html:contains("Firefox")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Google Chrome")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("All Browsers - Windows & Mac")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("The Plugin")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Downloading the correct plugin for your browser or OS!")')->count());
		$this->assertGreaterThanOrEqual(1, $crawler->filter('h1')->count());
		$this->assertGreaterThanOrEqual(3, $crawler->filter('h3')->count());
	}

	public function testPartnerAction_Digispark()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/static/partner/digispark');

		$this->assertEquals(1, $crawler->filter('html:contains("The micro-sized, Arduino enabled, usb development board - cheap enough to leave in any project!")')->count());
	}

	public function testPartnerAction_Arno()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/static/partner/arno');

		$this->assertEquals(1, $crawler->filter('html:contains("Learning the basics of electronics and programming is challenging")')->count());
	}

	public function testPartnerAction_Invalid()
	{
//		$client = static::createClient();
//
//		$crawler = $client->request('GET', '/static/plugin');
		$this->assertTrue(false);
	}

	public function testInfoKarmaAction()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/static/info/karma');

		$this->assertEquals(1, $crawler->filter('html:contains("What is karma?")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Karma in codebender")')->count());
	}
}
