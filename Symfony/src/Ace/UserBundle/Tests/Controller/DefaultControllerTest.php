<?php

namespace Ace\UserBundle\Tests\Controller;
use Ace\UserBundle\Controller\DefaultController;

class DefaultControllerTest extends \PHPUnit_Framework_TestCase
{
	public function testGetUserAction_UserExists()
	{
		$user = $this->getMockBuilder('Ace\UserBundle\Entity\User')
			->disableOriginalConstructor()
			->getMock();

		$user->expects($this->once())->method('getId')->will($this->returnValue(1));
		$user->expects($this->once())->method('getEmail')->will($this->returnValue("a@fake.email"));
		$user->expects($this->once())->method('getUsername')->will($this->returnValue("iamfake"));
		$user->expects($this->once())->method('getFirstname')->will($this->returnValue("fake"));
		$user->expects($this->once())->method('getLastname')->will($this->returnValue("basterd"));
		$user->expects($this->once())->method('getTwitter')->will($this->returnValue("atwitteraccount"));
		$user->expects($this->once())->method('getKarma')->will($this->returnValue(150));
		$user->expects($this->once())->method('getPoints')->will($this->returnValue(150));
		$user->expects($this->once())->method('getReferrals')->will($this->returnValue(5));
		$user->expects($this->once())->method('getReferrerUsername')->will($this->returnValue(null));
		$user->expects($this->once())->method('getReferralCode')->will($this->returnValue(null));
		$user->expects($this->once())->method('getWalkthroughStatus')->will($this->returnValue(0));

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("findOneByUsername"))
			->getMock();

		$repo->expects($this->once())->method('findOneByUsername')->with($this->equalTo("iamfake"))->will($this->returnValue($user));

		$em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
			->disableOriginalConstructor()
			->getMock();

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('AceUserBundle:User'))->will($this->returnValue($repo));

		$templating = $this->getMockBuilder('Symfony\Bundle\TwigBundle\TwigEngine')
			->setMethods(null)
			->disableOriginalConstructor()
			->getMock();

		$security = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
			->setMethods(null)
			->disableOriginalConstructor()
			->getMock();

		$container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

		$controller = new DefaultController($templating, $security, $em, $container);
		$response = $controller->getUserAction("iamfake");
		$this->assertEquals($response->getContent(), '{"success":true,"id":1,"email":"a@fake.email","username":"iamfake","firstname":"fake","lastname":"basterd","twitter":"atwitteraccount","karma":150,"points":150,"referrals":5,"referrer_username":null,"referral_code":null,"walkthrough_status":0}');
	}

	public function testGetUserAction_NoUser()
	{
		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("findOneByUsername"))
			->getMock();

		$repo->expects($this->once())->method('findOneByUsername')->with($this->equalTo("idontexist"))->will($this->returnValue(null));

		$em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
			->disableOriginalConstructor()
			->getMock();

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('AceUserBundle:User'))->will($this->returnValue($repo));

		$templating = $this->getMockBuilder('Symfony\Bundle\TwigBundle\TwigEngine')
			->setMethods(null)
			->disableOriginalConstructor()
			->getMock();

		$security = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
			->setMethods(null)
			->disableOriginalConstructor()
			->getMock();

		$container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

		$controller = new DefaultController($templating, $security, $em, $container);
		$response = $controller->getUserAction("idontexist");
		$this->assertEquals($response->getContent(), '{"success":false}');
	}
}
