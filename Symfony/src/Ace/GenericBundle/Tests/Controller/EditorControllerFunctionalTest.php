<?php

namespace Ace\GenericBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class EditorControllerFunctionalTest extends WebTestCase
{
	public function testEditAction()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'tester',
			'PHP_AUTH_PW' => 'testerPASS',
		));

		$crawler = $client->request('GET', '/sketch:1');

		$this->assertEquals(1, $crawler->filter('html:contains("Save")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("test_project.ino")')->count());

		//TODO: Use selenium to make sure this works fine.
		$this->markTestIncomplete('Use selenium to make sure this works fine.');
	}
}
