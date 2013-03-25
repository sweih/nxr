<?php

	/**
	* Project:		Google Site Map Generator
	*				
	* File:			GoogleSiteMap.php
	*
	* Copyright (C) 2006 DOUSSAU Alban
	* 
	*
	* @link http://www.dodecagone.com
	* @author DOUSSAU ALban <myalban at gmail dot com>
	* @version 1.0 29/05/2006
	*/
	
	
class GoogleSiteMap {


	var $flux;			//XML String
	var $nl;			//New Line
	var $listfreq;		//Frequency List

	// Constructor function
	function GoogleSiteMap(){
		$this->Ini();
		$this->StartMap();
	}

	//------------------------------------------------------------
	//--------------------------PUBLIC FUNCTIONS------------------
	//------------------------------------------------------------
	
	
	// Add an url to the XML String
	// This function support string or array parameters but it's easy to extend it !
	function AddURL($o){
		
		$type=gettype($o);
		//echo $type;
		switch ($type){
			case "string":
				$this->AddStringURL($o);
				break;
			case "array":
				$this->AddArrayURL($o);
				break;
			default:
				break;
		}
		
	}
	
	// Add a bunch of urls to the XML String
	// This function array parameters but it's easy to extend it !
	
	function AddURLs($o){
		$type=gettype($o);
		//echo $type;
		switch ($type){
			case "array":
				for ($i=0;$i<count($o);$i++){
					$this->AddURL($o[$i]);
				}
				break;
			default:
				break;
		}
	}
	
	// Close the XML String and return the XML String
	function CloseMap(){
		$this->flux.="</urlset>";
		$this->GetFlux();
	}
	
	// View the XML String in IE ( Or Firefox,....)
	function ViewMap(){
		header("Pragma: no-cache");
		header("Content-Type: text/xml;charset=UTF-8");
		echo $this->flux;
	}
	
	// Get the XML String
	function GetFlux(){
		return $this->flux;
	}
	
	//write the XML STRING in a specified file
	function WriteFlux($file){
	
		$this->MakerFile($file, $this->flux);
	
	}
	
	//------------------------------------------------------------
	//--------------------------PRIVATE FUNCTIONS-----------------
	//------------------------------------------------------------
	
	
	// Initialize
	function Ini(){
		$this->flux="";
		$this->nl="\n";
		$this->listfreq=array("always","hourly","daily","weekly","monthly","yearly","never");
		
	}
	// Start the XML String
	function StartMap(){
		$this->flux.="<?xml version=\"1.0\" encoding=\"UTF-8\"?>".$this->nl;
		$this->flux.="<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/sitemap.xsd\">".$this->nl;
	}
	
	// Add a simple url location to the XML String
	function AddStringURL($s){
		$this->flux.="<url>".$this->nl;
		$this->flux.="<loc>".$this->CleanURL($s)."</loc>".$this->nl;
		$this->flux.="</url>".$this->nl;
	}
	// Add an url location with full option to the XML String
	function AddArrayURL($a){
		$this->flux.="<url>".$this->nl;
		while (list($k, $v) = each($a)) {
					switch($k) {
						case "loc":
							$v=$this->CleanURL($v);
							break;
						case "lastmod":
							$v=($v=="")?date("c"):$v;
							break;
						case "changefreq":
							$v=(in_array($v,$this->listfreq))?$v:"monthly";
							break;
						case "priority":
							$v=($v==""||$v>1||$v<0)?"0.5":number_format($v,1);
							break;
					}
							
					$this->flux.="<".$k.">".$v."</".$k.">".$this->nl;
		}
		$this->flux.="</url>".$this->nl;
	
	}
	
	
	// This function make a clean url with HTML ENTITIES end UTF-8 conversions
	function CleanURL($s){
		$s=htmlentities($s,ENT_QUOTES,"UTF-8");
		$s=utf8_encode($s);
		return $s;
	}
	
	//this function write a string to a file
	
	function MakerFile($file, $string) {
	   $f=fopen($file, 'w+');
	   ftruncate($f, 0);
	   fwrite($f, $string);
	   fclose($f);
	}

	
}
	
?>