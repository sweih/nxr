<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2006 Sven Weih
	 *
	 *	This file is part of N/X.
	 *	The initial has been setup as a small diploma thesis (Studienarbeit) at the FZI.
	 *	It was be coached by Prof. Werner Zorn and Dipl.-Inform Thomas Gauweiler.
	 *
	 *	N/X is free software; you can redistribute it and/or modify
	 *	it under the terms of the GNU General Public License as published by
	 *	the Free Software Foundation; either version 2 of the License, or
	 *	(at your option) any later version.
	 *
	 *	N/X is distributed in the hope that it will be useful,
	 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
	 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 *	GNU General Public License for more details.
	 *
	 *	You should have received a copy of the GNU General Public License
	 *	along with N/X; if not, write to the Free Software
	 *	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	 **********************************************************************/

	/**
	 * Captcha PlugIn
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnCaptcha extends Plugin {

		
        
		var $pluginType = 3; //CDS-API-Extension
        var $helpfile = "captcha/plugin_captcha.pdf";
		/**
		   * This function is used for drawing the html-code out to the templates.
		   * It just returns the code
		   * @param 		string	Optional parameters for the draw-function. There are none supported.
		   * @return		string	HTML-CODE to be written into the template.
		   */
		function draw($param = "") {
			return new NXCaptcha();
		}
	

		/**
		   * Specifies information for installation and deinstallation of the plugin.
		   */
		function registration() {
			global $auth;	
			if ($auth->checkPermission("ADMINISTRATOR")) {
				Plugin::registration();
				$this->name = "Captcha";
				$this->description = "CDS-API-Extension for creating Captchas";
				$this->version = 1;
				$mtid = nextGUID(); // getting next GUID.
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE, MODULE_TYPE_ID) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source', 3)");
			}
		}
	}
	
  
  /**
   * Class used to create and validate captchas.
   *
   */
  class NXCaptcha {
  
  	/**
  	 * Draw the captcha and the input.
  	 *
  	 */
  	function get($inputTitle="Validation Code:", $tableWidth="100%", $titleWidth="30%", $formWidth="70%") { 	  
  	  $captcha = $this->_getCaptcha();
  	  $output = '<table border="0" cellspacing="0" cellpadding="2" width="'.$tableWidth.'">';
  	  $output.= '<tr><td width="'.$titleWidth.'">&nbsp;</td><td width="'.$captchaWidth.'">'.$captcha->display_captcha().'</td></tr>';
  	  $output.= '<tr><td>'.$inputTitle.'</td><td><input type="text" name="private_key" maxlen="5"></td></tr></table>';
  	  return $output;  	  
  	}
  	
  	/**
  	 * Check, whether the public and the private key matches.
  	 *
  	 */
  	function validate() {
  	  $captcha = $this->_getCaptcha();
  	  $private = value("private_key");
  	  $public  = value("public_key");
  	  return $captcha->check_captcha($public, $private);  		
  	}
  	
  	/**
  	 * Create a capture object
  	 *
  	 * @return Captcha Object
  	 */
  	function _getCaptcha() {
  	  global $c;
  	  require_once($c["path"]. 'plugin/captcha/hn_captcha.class.php');
  	  $CAPTCHA_INIT = array(
        'tempfolder'     => $c['tmpcachepath'],      // string: absolute path (with trailing slash!) to a writeable tempfolder which is also accessible via HTTP!
  	    'TTF_folder'     => $c["path"].'plugin/captcha/', // string: absolute path (with trailing slash!) to folder which contains your TrueType-Fontfiles.            
	    'TTF_RANGE'      => array('geeker.ttf'),

            'chars'          => 5,       // integer: number of chars to use for ID
            'minsize'        => 20,      // integer: minimal size of chars
            'maxsize'        => 30,      // integer: maximal size of chars
            'maxrotation'    => 25,      // integer: define the maximal angle for char-rotation, good results are between 0 and 30

            'noise'          => FALSE,    // boolean: TRUE = noisy chars | FALSE = grid
            'websafecolors'  => FALSE,   // boolean
            'refreshlink'    => TRUE,    // boolean
            'lang'           => 'en',    // string:  ['en'|'de']
            'maxtry'         => 3,       // integer: [1-9]            
            'secretstring'   => 'A very, very scrt string which is used to generate a md5-key!',
            'secretposition' => 13,      // integer: [1-32]
            'debug'          => FALSE
		);
		$captcha = new hn_captcha($CAPTCHA_INIT);
		return $captcha;  		  		
  	}
  	
  	
  }

?>