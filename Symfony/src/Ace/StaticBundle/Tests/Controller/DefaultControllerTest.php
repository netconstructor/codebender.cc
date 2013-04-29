<?php

namespace Ace\StaticBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testPluginAction()
    {
//        $client = static::createClient();
//
//        $crawler = $client->request('GET', '/static/plugin');
//
//        $this->assertGreaterThan(0, $crawler->filter('html:contains("Firefox")')->count());
//        $this->assertGreaterThan(0, $crawler->filter('html:contains("Google Chrome")')->count());
//        $this->assertGreaterThan(0, $crawler->filter('html:contains("All Browsers - Windows & Mac")')->count());
		$this->assertTrue(false);
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
}
