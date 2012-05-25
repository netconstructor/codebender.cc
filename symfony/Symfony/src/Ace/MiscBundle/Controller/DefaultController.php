<?php

namespace Ace\MiscBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class developer
{
	public $name;
	public $image;
	public $description;
	function __construct($name, $subtext, $image, $description)
	{
		$this->name = $name;
		$this->subtext = $subtext;
		$this->image = $image;
		$this->description = $description;
	}
}

class post
{
	public $title;
	public $description;
	function __construct($title, $description)
	{
		$this->title = $title;
		$this->description = $description;
	}
}

class DefaultController extends Controller
{

    public function aboutAction()
    {
        return $this->render('AceMiscBundle:Default:about.html.twig');
    }

    public function teamAction()
    {
                $tzikis_name = "Vasilis Georgitzikis";
                $tzikis_title = "teh lead";
                $tzikis_avatar = "http://www.gravatar.com/avatar/1a6a5289ac4473b5731fa9d9a3032828?s=260";
                $tzikis_desc = "I am a student at the Computer Engineering and Informatics Department of the University of Patras, Greece, a researcher at the Research Academic Computer Technology Institute, and an Arduino and iPhone/OSX/Cocoa developer. Basically, just a geek who likes building stuff, which is what started codebender in the first place.";
		$tzikis = new developer($tzikis_name, $tzikis_title, $tzikis_avatar, $tzikis_desc);

		$tsampas_name = "Stelios Tsampas";
		$tsampas_title = "teh crazor";
                $tsampas_avatar = "http://secure.gravatar.com/avatar/a5eb2b494a07a39ab0eef0d10aa86c84?s=260";
                $tsampas_desc="Yet another student at CEID. My task is to make sure to bring crazy ideas to the table and let others assess their value. I'm also responsible for the Arduino Ethernet TFTP bootloader, the only crazy idea that didn't originate from me.";
		$tsampas = new developer($tsampas_name, $tsampas_title, $tsampas_avatar, $tsampas_desc);

		$amaxilatis_name = "Dimitris Amaxilatis";
		$amaxilatis_title = "teh code monkey";
		$amaxilatis_avatar = "http://codebender.cc/images/amaxilatis.jpg";
		$amaxilatis_desc = "Master Student at the Computer Engineering and Informatics Department of the University of Patras, Greece. Researcher at  the Research Unit 1 of Computer Technology Institute & Press (Diophantus) in the fields of Distributed Systems and Wireless Sensor Networks.";
		$amaxilatis = new developer($amaxilatis_name, $amaxilatis_title, $amaxilatis_avatar, $amaxilatis_desc);

		$kousta_name = "Maria Kousta";
		$kousta_title = "teh lady";
		$kousta_avatar = "http://codebender.cc/images/kousta.png";
		$kousta_desc = "A CEID graduate. My task is to develop the various parts of the site besides the core 'code and compile' page that make it a truly social-building website.";
		$kousta = new developer($kousta_name, $kousta_title, $kousta_avatar, $kousta_desc);

		$orfanos_name = "Markellos Orfanos";
		$orfanos_title = "teh fireman";
                $orfanos_avatar = "http://codebender.cc/images/orfanos.jpg";
                $orfanos_desc = "I am also (not for long I hope) a student at the Computer Engineering & Informatics Department and probably the most important person in the team. My task? Make sure everyone keeps calm and the team is having fun. And yes, I'm the one who developed our wonderful options page. Apart from that, I'm trying to graduate and some time in the future to become a full blown Gentoo developer.";
		$orfanos = new developer($orfanos_name, $orfanos_title, $orfanos_avatar, $orfanos_desc);

		$developers = array($tzikis, $tsampas, $amaxilatis, $kousta, $orfanos);
        return $this->render('AceMiscBundle:Default:team.html.twig', array("developers" => $developers));
    }
    public function blogAction()
    {
		// $text1 = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum varius nisi blandit leo tempor feugiat. Vestibulum semper elementum sem at convallis. Fusce ac cursus est. Aliquam adipiscing tristique sapien id venenatis. Ut vel tincidunt ligula. Etiam interdum sollicitudin nisl, vel pulvinar urna suscipit sed. Duis laoreet, est eget tristique pellentesque, metus risus luctus augue, in luctus risus mi sit amet nibh. Morbi rhoncus erat eget mauris viverra vulputate.
		// Cras pretium aliquam urna. Donec adipiscing vestibulum nisl non eleifend. Sed ultricies tincidunt turpis, sed ornare odio luctus in. Cras lacinia, quam vitae tincidunt volutpat, elit lacus aliquet turpis, nec aliquam dui tellus eget purus. Praesent bibendum enim quis urna porttitor at pretium odio feugiat. Nam ut nulla tellus, rutrum semper ipsum. Morbi laoreet consequat arcu nec pharetra. Nullam fermentum porta fringilla. Proin risus nisi, pellentesque sit amet vulputate vel, malesuada sed mauris. Sed in purus ligula. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Quisque risus tortor, porttitor eget vulputate a, mattis sed augue. Integer venenatis venenatis vestibulum. Proin commodo sem vel lectus aliquam sed bibendum odio gravida. Fusce eu interdum libero.
		// Suspendisse vel arcu elit. Vestibulum fermentum, nulla a sollicitudin eleifend, lacus lorem laoreet arcu, porta consequat erat elit in urna. Vestibulum nec felis ut nunc aliquet pharetra. Mauris nec mi sit amet lectus tincidunt dapibus. Proin hendrerit ornare quam, sed consectetur risus accumsan nec. Etiam non mi eu dui semper suscipit at tincidunt neque. Quisque luctus, odio ut condimentum euismod, nulla ligula vulputate purus, ut scelerisque est nisl ut erat. Duis vel quam eu est interdum cursus. Quisque non diam diam. Quisque laoreet nunc pellentesque risus pulvinar non eleifend ante auctor. Quisque eu erat nisi, et scelerisque augue. Aenean imperdiet metus at nisl vestibulum scelerisque. Sed ultricies viverra consequat. Integer in lectus dapibus ligula ultrices eleifend ac a libero.";
		$text2 = "This is codebender calling.
		And we are a-live! 
		Given this is our first post, we are all very excited to reach a point were we are live, stable, and feature-rich. At the moment, we are still working on fixing our alpha-testing bugs and completing our feature set, but we are very pleased with everything so far.
		As far as our main functionality is concerned, we are succesfully compiling and flashing, and all that's left is to complete our functionality for all cases. For example, not all of the Arduino's bundled libraries compile at the moment and the uploader doesn't work on Windows. This is, of course, our main focus for the near future. We hope to be ready for beta testing within a few weeks, so be sure to check often for updates and news.";
		$text2 = str_replace("\n", "</p>\n<p>", $text2);
		// $text1 = str_replace("\n", "</p>\n<p>", $text1);
 		// $first = new post("Sample blog post", $text1);
		$second = new post("Hello, world!", $text2);
		$posts = array(/* $first, */$second);
       return $this->render('AceMiscBundle:Default:blog.html.twig', array("posts" => $posts));
    }

    public function blog_newAction()
    {
		$text2 = "This is codebender calling.
		And we are a-live! 
		Given this is our first post, we are all very excited to reach a point were we are live, stable, and feature-rich. At the moment, we are still working on fixing our alpha-testing bugs and completing our feature set, but we are very pleased with everything so far.
		As far as our main functionality is concerned, we are succesfully compiling and flashing, and all that's left is to complete our functionality for all cases. For example, not all of the Arduino's bundled libraries compile at the moment and the uploader doesn't work on Windows. This is, of course, our main focus for the near future. We hope to be ready for beta testing within a few weeks, so be sure to check often for updates and news.";
		$second = new post("Hello, world!", $text2);
		$posts = array($second);
       return $this->render('AceMiscBundle:Default:blog_new.html.twig', array("posts" => $posts));
    }

    public function tutorialsAction()
    {
        return $this->render('AceMiscBundle:Default:tutorials.html.twig');
    }
}
