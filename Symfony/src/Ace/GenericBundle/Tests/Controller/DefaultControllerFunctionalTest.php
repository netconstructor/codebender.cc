<?php

namespace Ace\GenericBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class DefaultControllerFunctionalTest extends WebTestCase
{
	public function testIndexAction_Anonymous() // Test homepage and redirection
	{
		$client = static::createClient();
		$crawler = $client->request('GET', '/');

		$this->assertEquals(1, $crawler->filter('html:contains("code fast. code easy. codebender")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("online development & collaboration ")')->count());
	}

	public function testIndexAction_LoggedIn() // Test homepage redirection for logged in users
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'tester',
			'PHP_AUTH_PW' => 'testerPASS',
		));

		$crawler = $client->request('GET', '/');

		$this->assertEquals(1, $crawler->filter('h2:contains("Hello tester!")')->count());
		$this->assertEquals(1, $crawler->filter('h3:contains("New Project")')->count());
	}

	public function testUserAction() // Test user page
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/user/tester');

		$this->assertEquals(1, $crawler->filter('html:contains("tester")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("myfirstname")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("mylastname")')->count());

		$matcher = array('id'   => 'user_projects');
		$this->assertTag($matcher, $client->getResponse()->getContent());
	}

	public function testUserActionLinksToSketchView_SketchViewWorks() // Test project page
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/user/tester');

		$client->followRedirects();

		$link = $crawler->filter('#user_projects')->children()->eq(1)->children()->children()->children()->link();
		$crawler = $client->click($link);

//		$matcher = array('id'   => 'code-container');
//		$this->assertTag($matcher, $client->getResponse()->getContent());
		$this->assertEquals(1, $crawler->filter('h2:contains("Codebender Project")')->count());
	}

	public function testLibraries()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/libraries');
		$this->assertEquals(1, $crawler->filter('html:contains("codebender libraries")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Request Library")')->count());

		$this->assertEquals(1, $crawler->filter('h2:contains("Examples")')->count());
		$this->assertEquals(1, $crawler->filter('h2:contains("Builtin Libraries")')->count());
		$this->assertEquals(1, $crawler->filter('h2:contains("External Libraries")')->count());

		$this->assertEquals(1, $crawler->filter('html:contains("01.Basics")')->count());
	}

	public function testFunctionalTested()
	{
		$this->markTestIncomplete('Not functional tested yet.');
	}
}
