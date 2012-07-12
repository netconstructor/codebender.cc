This repository is part of the [codebender.cc](http://www.codebender.cc) maker and artist web platform.

## And what's that?

codebender comes to fill the need for reliable and easy to use tools for makers. A need that from our own experience could not be totally fulfilled by any of the existing solutions. Things like installing libraries, updating the software or installing the IDE can be quite a painful process.

In addition to the above, the limited features provided (e.g. insufficient highlighting, indentation and autocompletion) got us starting building codebender, a completely web-based IDE, that requires no installation and offers a great code editor. It also stores your sketches on the cloud.

That way, you can still access your sketches safely even if your laptop is stolen or your hard drive fails! codebender also takes care of compilation, giving you extremely descriptive warnings on terrible code. On top of that, when you are done, you can upload your code to your Arduino straight from the browser without installing anything.

Currently codebender.cc is running its beta and we are trying to fix issues that may (will) come up so that we can launch and offer our services to everyone!
If you like what we do you can also support our campaign on [indiegogo](http://www.indiegogo.com/codebender) to also get early access to codebender! 

## Features offered by codebender

* Code Editor
codebender uses Ace, an awesome web code editor written in pure HTML5 and Javascript, which is the best thing ever since sliced bread, at least as far as code editors go. Use it now and enjoy indentation that just works™, great syntax highlighting, auto-completion, highlighting of matching parentheses and brackets and highlighting of a selected keyword. Oh, and of course, you can't call it an editor if it doesn't have Vim and EMACS keybindings. Built-in.

* Advanced Code Analysis
codebender uses Clang, a state-of-the-art C++ code analyzer and compiler to perform syntax analysis in your programs, and give you precise and accurate information on your bugs. Using codebender's improved reporting on the faulty lines of code, you can find and squash bugs in no time. Using codebender, you can now get more precise reports on your errors, and better suggestions on how to fix them.

* Compilation
Compile your code, on the cloud. With codebender, you can compile your code on our servers, saving you the hassle of downloading and installing all the necessary tools to build your sketches. No more upgrading to newer versions and managing incompatible library versions. This also improves compilation times in old machines and simplifies syntax error checks.

* Built-in Libraries
Never mind about finding, installing, and updating external libraries anymore. With codebender, you only need to include the library you want to use, and we automatically compile it with your code. And we also take care of updating them when a new version is released. All just for you. Stop worrying about libraries, and start coding your Arduino. That autonomous space BBQ laser grill isn't going to build itself, you know.

* Documentation and Suggestions
codebender makes finding the documentation for that one function you are looking for as easy as it can get. Just select it, press Ctrl-Space, and you will be take to its arduino.cc Reference page. And it gets better. We are actively documenting Arduino's codebase, in order to provide a better reference, as well as suggestions and auto-completion in the near future.

* Cloud Architecture
codebender is built from the ground up with a Cloud perspective. We use the best engineering practices to be scalable, fast, responsive, stable, and safe. We use top notch technology and the best infrastructure, to make sure that your data is safe and our website runs smoothly 24/7. We will be online whenever you need us, and you will never have to back up your sketches, or worry about losing your data due to hardware faults again.

* Flashing over USB
Upload your code to your Arduino, straight from your browser. No inconvenient software installations needed to flash your sketch on your Arduino board. All you need to do is run our Java applet straight from your browser, and you gain immediate access to your Serial ports to Upload your sketch, or open the Serial Monitor to communicate with your Arduino.

* Flashing over TFTP
Still using your USB port to flash your new sketches on your Arduino? Come on, that is sooo 2010. Using our open-source TFTP bootloader for Arduino Ethernet and Arduinos with Ethernet Shield, you can upload your sketch to any internet-connected Arduino, anywhere in the world, straight from codebender. Add your Ethernet-equipped Arduino to your list and you are set.

* Cross-Platform Design
codebender loves all operating systems, and we love everyone. That's why we support all major platforms (OS X, Windows, Linux) in codebender. And since we love everyone, we also support all major browsers. codebender will look great, wether you use Firefox, Chrome, Internet Explorer (not tested) or Opera (not tested).

## Interested in more technical stuff?

* HTML5
codebender is built using HTML5 technologies to make the most out of the web.
* CSS3
Beautiful transitions and styling brought to you using CSS3.

* ACE
codebender uses Ace, the state-of-the-art, open-source javascript editor, to provide a great coding environment.

* Bootstrap
We are using Twitter's Bootstrap for our design.

* Symfony
codebender is built on Symfony 2, the awesome PHP framework that helps us make codebender scalable, fast, and maintainable.

* MySQL
MySQL may not be the newest kid on the block, but it sure is well-known and stable. That's why codebender uses MySQL to manage your projects and settings.

* MongoDB
Unlike MySQL, MongoDB is extremely scalable. This is why we're using MongoDB to save your projects. To make sure that they will stay safe, backed up, and online whenever you need them, no matter what.

* PHP Fog & AWS
codebender is hosted on PHP Fog, the great PaaS built for PHP applications to ensure scalability and great uptime. We are also using Amazon Web Services for the compiler, to make sure it is just as fast, responsive, and stable, 24/7.

* Arduino
Besides using and targeting the Arduino platform, we are actively involved in advancing the platform. We have contributed in the TFTP bootloader, as well as various libraries, such as the GNTP library, the FIO Xbee Config Tool, and more.

* TFTP Bootloader
We are proudly some of the guys behind the TFTP bootloader, which we also support for remote programming. One of our developers built upon the work done by the Arduino team and completed it. That's why we are the first ones to use it to remotely flash an Arduino, anywhere in the world.

* Clang
We are using Clang, the awesome C/C++/Obj-C compiler, to perform syntax analysis on your Arduino sketches. By using Clang, we can give you much more refined and precise error reporting, which helps you squash bugs in no time.

* Open Source - Github
We are completely and utterly open source, and we strongly believe in it, which is why we support only open-source sketches at launch. You can always check our code on GitHub. Got a patch? Please send it our way!

## And what's the status?
