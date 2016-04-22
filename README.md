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


## Licence

MIT Licence

## Author

[yorudazzz](https://github.com/yorudazzz)