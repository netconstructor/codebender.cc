<?php

namespace Ace\GenericBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();        
		$crawler = $client->request('GET', '/');
		
		$this->assertFalse($client->getResponse()->isRedirect());
		
		$this->assertGreaterThan(0, $crawler->filter('html:contains("enter codebender.")')->count());
		
		$client->request('GET', '/list');
		$this->assertTrue($client->getResponse()->isRedirect());
    }
	
	 
	public function testUser()
	{
		$client = static::createClient();        
		
		$crawler = $client->request('GET', '/user/tzikis');
		
		$this->assertGreaterThan(0, $crawler->filter('a:contains("hello!")')->count());
	
	}
	 
	
	public function testProject()
	{
		
	
	
	
	}
	
	public function testLibraries()
	{
		
	
	
	
	}
	

	
}
