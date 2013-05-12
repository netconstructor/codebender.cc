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

	public function testUserAction_UserExists() // Test user page
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/user/tester');

		$this->assertEquals(1, $crawler->filter('html:contains("tester")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("myfirstname")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("mylastname")')->count());

		$matcher = array('id'   => 'user_projects');
		$this->assertTag($matcher, $client->getResponse()->getContent());
	}

	public function testUserAction_UserUnknown() // Test user page
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/user/unknown_user');

		$this->assertEquals(1, $crawler->filter('h3:contains("There is no such user.")')->count());
	}

	public function testUserActionLinksToSketchView_SketchViewWorks() // Test project page
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/user/tester');

		$client->followRedirects();

		$link = $crawler->selectLink("test_project")->link();
		$crawler = $client->click($link);

		$this->assertEquals(1, $crawler->filter('h1:contains("Codebender Project")')->count());
	}

	public function testProjectAction()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/sketch:1');

		$this->assertEquals(1, $crawler->filter('html:contains("a project used to test the search function")')->count());

		//TODO: Use selenium to make sure this works fine.
		$this->markTestIncomplete('Use selenium to make sure this works fine.');
	}

	public function testProjectfilesAction()
	{
		//TODO: Use selenium to make sure this works fine.
		$this->markTestIncomplete('Use selenium to make sure this works fine.');
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

	public function testExampleAction()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/example/01.Basics/Blink/http%3A//libs.codebender.cc/get%3Ffile%3D01.Basics/Blink/Blink.ino');
		$this->assertEquals(1, $crawler->filter('h1:contains("01.Basics : Blink")')->count());
		$this->assertEquals(1, $crawler->filter('h2:contains("Blink.ino")')->count());
	}

	public function testBoardsAction()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/boards');
//		$this->assertEquals(1, $crawler->filter('html:contains("codebender boards")')->count());
//		$this->assertEquals(1, $crawler->filter('html:contains("Request Board")')->count());
//
//		$this->assertEquals(1, $crawler->filter('h4:contains("Arduino Uno")')->count());
//		$this->assertEquals(1, $crawler->filter('h4:contains("Digispark (Tiny Core)")')->count());
//		$this->assertEquals(1, $crawler->filter('h4:contains("Arno")')->count());

		$this->assertEquals(1, $crawler->filter('html:contains("Cannot modify header information")')->count());
		//TODO: Fix header information problem.
		$this->markTestIncomplete('Fix header information problem.');
	}
}
