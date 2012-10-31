<?php

namespace Ace\GenericBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class DefaultControllerTest extends WebTestCase
{
    public function testIndex()   // Test homepage and redirection bug
    {
        $client = static::createClient();        
		$crawler = $client->request('GET', '/');
		
		$this->assertFalse($client->getResponse()->isRedirect());
		
		$this->assertGreaterThan(0, $crawler->filter('html:contains("enter codebender.")')->count());
		
		$client->request('GET', '/list');
		$this->assertTrue($client->getResponse()->isRedirect('/'));
    }
	
	 
	public function testUser()  // Test user page
	{
		$client = static::createClient();        
		
		$crawler = $client->request('GET', '/user/tzikis');
		
		$this->assertGreaterThan(0, $crawler->filter('html:contains("tzikis (Georgitzikis Vasilis)")')->count());
		
		$matcher = array('id'   => 'user_projects');
		$this->assertTag($matcher, $client->getResponse()->getContent()); 
		
		/* $user = $this->getDoctrine()->getRepository('AceUserBundle:User')->findOneByUsername('tzikis');
		
		$result=file_get_contents("http://api.twitter.com/1/statuses/user_timeline/{$user->getTwitter()}.json");
		if ( $result != false ) {
			$tweet=json_decode($result); // get tweets and decode them into a variable
			$lastTweet = $tweet[0]->text; // show latest tweet
		} else {
			$lastTweet=0;
		}
		
		echo $lastTweet; */
	
	}
	 
	
	public function testProject() // Test project page
	{
		$client = static::createClient();        
		
		$crawler = $client->request('GET', '/user/tzikis');
		
		$client->followRedirects();
		
		$link = $crawler->filter('#user_projects')->children()->eq(1)->children()->children()->children()->link();
		$crawler = $client->click($link); 		
			
		$matcher = array('id'   => 'code-container');
		$this->assertTag($matcher, $client->getResponse()->getContent()); 
		
	
	}
	
	public function testLibraries()
	{
		
	
	
	
	}
	

	
}
