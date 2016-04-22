<?php
	mb_http_output("UTF-8");
	mb_internal_encoding("UTF-8");
	mb_language('Japanese');

	require("AtomPub/AtomPub.php");

	try{

		/*
		 * This source is for new entry to blog.
		 * This is example code so,actually each params($username,$password,$endPoint) has to along requite spec of your blog.
		 * Specially, $password will be password of blog's admin page or it is also will be api key.
		 */

		$username = "username";
		$password = "password";
		$endPoint = "http://MEMBER_PROVIDER.blogcms.jp/atom/entry";
		$title = "test-title";
		$content = "test-main-content";
		$draft = "yes";
		$year = "2016";
		$month = "2";
		$day = "28";
		$hour = "8";
		$minute = "49";
		$second = "34";
		
		//Client
		$client = new AtomPub_Client($endPoint,$username,$password);

		//Title
		$title = new AtomPub_Value($title,"title");

		//The time
		$date = sprintf("%04d%02d%02d\t%02d:%02d:%02d",$year,$month,$day,$hour,$minute,$second);
		$updated = new AtomPub_Value($date,"updated");
		$published = new AtomPub_Value($date,"published");
		$edited = new AtomPub_Value($date,"edited");

		//Author
		$name = new AtomPub_Value("author_name","name");
		$author = new AtomPub_Value(array($name),"author");
		//Content
		$content_data = new AtomPub_Value($content,"content");

		//Draft
		$appDraft = new AtomPub_Value($draft,"app:draft");
		$appCtrl = new AtomPub_Value(array($appDraft),"app:control");

		//Create Message
		$data = array($title,$updated,$published,$edited,$author,$content_data,$appCtrl);
		$message = new AtomPub_Message($data);
		$xml = $message->getXml();

		//Sending
		$res = $client->send($xml);
		echo $res->getStatus();
	}catch(Exceptin $e){
		echo $e->getMessage();
	}
?>