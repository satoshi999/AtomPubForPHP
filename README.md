# Interchanging AtomPub API for PHP
====

## Overview
The PHP source of interchang PHP with AtomPub API.

## Description
This PHP is source for that interchange between PHP and AtomPub API without mind markup and wsse authorize.  
Modules which to create xml and authorize are implemented in this PHP.  
Just that define parametors you need and generate instance,  
You can interchange data of AtomPub API.  
However you shold be check how is xml of AtomPub API.  
Forther some parametors has to use  that along spec of your blog service.  
So, Confirm the item of below before that implement this PHP.

* End point URL of AtomPub API
* Whether need AtomPub key

## Requirement
* PHP 5.5 or later.
* Available HTTP_Request2
```
pear install HTTP_Request2
```

## Usage
*Donwload AtomPub/AtomPub.php.
*Place the file you want.
*Import the file inside your PHP.
```
require("AtomPub/AtomPub.php");
```

## Classes
### AtomPub_Client
Management informaton for that connect AtomPub API
##### Constructor
| Parametor | Constraint | Data Type | Description | 
|---|---|---|---|
| $endPoint | Requied | String | AtomPub API URL that along require spec of the blog. | 
| $username | Requied | String | username of admin page of the blog. | 
| $password | Requied | String | AtomPub key if need it. Otherwise it's password of admin page of the blog | 
##### Function
| Name | Arg | Data Type| Rerutn |
|---|---|---|---|
| send | $xml | xml created by AtomPub_Message | response of AtomPub API |


### AtomPub_Value
Management markup value of XML
##### Constructor
| Parametor | Constraint | Data Type | Description | 
|---|---|---|---|
| $val | Requied | String | String content you want send. | 
| $type | Requied | String | Markup of xml that will be send to AtomPub API | 

### AtomPub_Message
Created send XML by generate instance with list of AtomPub_Value
##### Constructor
| Parametor | Constraint | Data Type |
|---|---|---|
| $data | Requied | Array of AtomPub_Value |
##### Function
| Name | Arg | Rerutn |
|---|---|---|
| getXml | --- | xml created inside the AtomPub_Message  |


### Sample
```
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
```

## Licence

MIT Licence

## Author

[yorudazzz](https://github.com/yorudazzz)