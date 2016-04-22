<?php
mb_http_output("UTF-8");
mb_internal_encoding("UTF-8");
mb_language('Japanese');

//GLOBAL--------------------------------------------------
//Various of every kindall markups in AtomPub-XML
$GLOBALS["AtomPub_Title"] 	       = "title";
$GLOBALS["AtomPub_Content"]        = "content";
$GLOBALS["AtomPub_Updated"]        = "updated";
$GLOBALS["AtomPub_Published"]      = "published";
$GLOBALS["AtomPub_Edited"]         = "edited";
$GLOBALS["AtomPub_Category"]       = "category";
$GLOBALS["AtomPub_Author"] 	       = "author";
$GLOBALS["AtomPub_Name"] 	       = "name";
$GLOBALS["AtomPub_AppCtrl"]        = "app:control";
$GLOBALS["AtomPub_AppDraft"]       = "app:draft";
$GLOBALS["AtomPub_BlogcmsSource"]  = "blogcms:source";
$GLOBALS["AtomPub_BlogcmsBody"]    = "blogcms:body";
$GLOBALS["AtomPub_BlogcmsMore"]    = "blogcms:more";
$GLOBALS["AtomPub_BlogcmsPrivate"] = "blogcms:private";
$GLOBALS["AtomPub_Category"]       = "category";


//Transform to array from markups
/*
 * Each number express struct of xml
 * 1=Just Scalar,2=Layered structure,3=Other specifications format
 */
$GLOBALS["AtomPub_Types"] = array(
    $GLOBALS['AtomPub_Title']          => 1,
    $GLOBALS['AtomPub_Content']        => 1,
    $GLOBALS['AtomPub_Updated']        => 1,
    $GLOBALS['AtomPub_Published']      => 1,
    $GLOBALS['AtomPub_Edited']         => 1,
    $GLOBALS['AtomPub_Category']       => 1,
    $GLOBALS['AtomPub_Author']         => 2,
    $GLOBALS['AtomPub_Name']           => 1,
    $GLOBALS['AtomPub_AppCtrl']        => 2,
    $GLOBALS['AtomPub_AppDraft']       => 1,
	$GLOBALS["AtomPub_BlogcmsSource"]  => 2,
	$GLOBALS["AtomPub_BlogcmsBody"]    => 1,
	$GLOBALS["AtomPub_BlogcmsMore"]    => 1,
	$GLOBALS["AtomPub_BlogcmsPrivate"] => 1,
	$GLOBALS["AtomPub_Category"]       => 3
);

//Class from here-----------------------------------------------------------

	/*
	 * Keep api info
	 * Send to AtomPub by call send func
	 */
	class AtomPub_Client extends AtomPub_Base{
		var $url = "";
		var $wsse = "";
		var $debug = false;	//true=print send xml;

		function AtomPub_Client($endPoint,$id,$key){
			if($endPoint && $id && $key){
				$this->url = $endPoint;
				$this->generateWsseKey($id,$key);
			}else{
				$this->raiseError("InValid Api Info");
			}
		}

		private function generateWsseKey($id,$key){
			$created = date('Y-m-d\TH:i:s\Z');
			$nonce = pack('H*', sha1(md5(time())));
			$pass_digest = base64_encode(pack('H*', sha1($nonce.$created.$key)));
			$this->wsse = 'UsernameToken Username="'.$id.'", '.
					'PasswordDigest="'.$pass_digest.'", '.
					'Nonce="'.base64_encode($nonce).'", '.
					'Created="'.$created.'"';
		}

		function setDebug($isDebug){
			$this->debug = $isDebug;
		}

		function send($xml){
		    require_once 'HTTP/Request2.php';
			$headers =array(
			  'X-WSSE: ' . $this->wsse,
			  'Expect:'
			);
			if($this->debug){
				echo $xml;
			}
		    try{
				$req = new HTTP_Request2();
				$req->setUrl($this->url);
				$req->setMethod(HTTP_Request2::METHOD_POST);
				$req->setHeader($headers);
				$req->setBody($xml);
				$response = $req->send();
				return $response;
		    } catch (HTTP_Request2_Exception $e) {
		        return $e->getMessage();
		    } catch (Exception $e) {
		        return $e->getMessage();
		    }

		}
	}

	/*
	 * Keep markup value
	 */
	class AtomPub_Value extends AtomPub_Base{
		var $myName = "";
		var $myValue = "";
		var $myType = 0;

		function AtomPub_Value($val = -1,$type = ""){
			//Initialize
			$this->myName = "";
			$this->myValue = "";
			$this->myType = 0;
			if($val || $type != ""){
				if($type == ""){
					$type = "string";
				}
			}
			if(!array_key_exists($type,$GLOBALS["AtomPub_Types"])){
				$this->raiseError("Could not find type");
			}else{
				$this->appendValue($val,$type);
			}
		}

		private function appendValue($val,$type){
			if($this->myType != 0){
				$this->raiseError("Already initialized as a [".$this->kindOf()."]");
				return 0;
			}
			$typeof = $GLOBALS['AtomPub_Types'][$type];
			$this->myValue = $val;
			$this->myName = $type;
			$this->myType = $typeof;
		}
	}

	/*
	 * Generate send xml and keep
	 */
	class AtomPub_Message extends AtomPub_Base{
		var $myXml = "";
		function AtomPub_Message($data){
			$this->myXml .= $this->xml_header();
			foreach($data as $_AtomPub_Value){
				if(is_object(@$_AtomPub_Value) && get_class($_AtomPub_Value) == "AtomPub_Value"){
					if($_AtomPub_Value->myType == 1){
						$this->myXml .= $this->generateScalarXml($_AtomPub_Value->myName,$_AtomPub_Value->myValue);
					}else if($_AtomPub_Value->myType == 2){
						$this->myXml .= $this->generateStructXml($_AtomPub_Value);
					}else if($_AtomPub_Value->myType == 3){
						$this->myXml .= $this->generateCategoryXml($_AtomPub_Value->myValue);
					}
					if($_AtomPub_Value !== end($data)){
						$this->myXml .= "\n";
					}
				}else{
					$this->raiseError("Not a AtomPub_Value");
					break;
				}
			}
			$this->myXml .= $this->xml_footer();
		}

		private function generateScalarXml($name,$value){
			$xml = "";
			$xml = "<".$name.">".$value."</".$name.">";
			return $xml;
		}

		//Category is specifications format(<category term='category_name'>)
		private function generateCategoryXml($value){
			$xml = "";
			$xml = '<category term="'.$value.'" />';
			return $xml;
		}

		private function generateStructXml($struct){
			$xml = "";
			$xml = "<".$struct->myName.">";
			foreach($struct->myValue as $value){
				$xml .= $this->generateScalarXml($value->myName,$value->myValue);
			}
			$xml .= "</".$struct->myName.">";
			return $xml;
		}

		private function xml_header(){
			$header = '<?xml version="1.0" encoding="utf-8"?>'.
					  '	<entry xmlns="http://www.w3.org/2005/Atom"'.
					  '		xmlns:app="http://www.w3.org/2007/app"'.
					  '		xmlns:blogcms="http://blogcms.jp/-/spec/atompub/1.0/">';
			return $header;
		}

		private function xml_footer(){
			$footer = "</entry>";
			return $footer;
		}

		function getXml(){
			return $this->myXml;
		}
	}

	/*
	 * Error handling
	 * Throw Exception with Class Name
	 */
	class AtomPub_Base{
		function raiseError($msg){
			if(is_object(@$this)){
				throw new Exception(get_class($this).": ".$msg);
			}else{
				throw new Exception("In Unknown Class: ".$msg);
			}
		}
	}
?>