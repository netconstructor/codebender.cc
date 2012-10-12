<?php

namespace Ace\BlogBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testBlog()   // Test wether each page has 5 posts or less.
    {
        $client = static::createClient();
		
		$crawler = $client->request('GET', '/blog');
			$pages = $crawler->filter('.pagination')->children()->children()->count();
			//echo $pages;

		for($i = 1;$i <= $pages-2;$i++){
        $crawler = $client->request('GET', '/blog/'.$i);        
		$this->assertLessThanOrEqual(5, ($crawler->filter('#posts')->children()->count() - 1)); }
    }
}
