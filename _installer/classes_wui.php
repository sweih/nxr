<?php
/**********************************************************************
 *	phpScriptInstaller
 *	Copyright 2003 Sven Weih (sven@weih.de)
 *
 *	This file is part phpScriptInstaller
 *
 *	phpScriptInstaller is free software; you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation; either version 2 of the License, or
 *	(at your option) any later version.
 *
 *	phpScriptInstaller is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with phpScriptInstaller; if not, write to the Free Software
 *	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 **********************************************************************/
 
  /**
   * Base Class of the installer. Needs to be instanciated to create it.
   */
  class Installer {
  	
    var $css;
    var $pages=null;
    var $active_page;
    var $display_page;
    var $title_html;
    var $title;
    /**
     * Standard constructor
     * @param string Path to CSS-File
     */
    function Installer($css="installer.css") {
    	$this->css = $css;
    }
    
    /**
     * Set a Header in HTML, which will be displayed on every page.
     * Note: You can also use images. Place them with all other resource
     * files in the inst_res folder.
     * @param string HTML-Code of the header
     */
    function setTitleHTML($html) {
      $this->title_html = $html;	
    }
    
    /**
     * Set title of installer in browser headline
     * @param string $title Title to be displayed in the headline
     */
    function setTitle($title) {
      $this->title = $title;	
    }
    
    /**
     * Add a page to the installer. The order of the pages is the order, in which the installer
     * will got through the script. Each page automatically gets its own ID.
     * @param object Page, which is to be added to the script.		
     */
     function addPage(&$page) {
     	$this->pages[count($this->pages)+1] = &$page;
     	$page->parent = &$this; 
     }
     
     /**
      * Start the installation....
      */
     function start() {
     	global $HTTP_POST_VARS, $errors;;
     	// initialize
     	$id =$HTTP_POST_VARS["page"];     	 
        $do = $HTTP_POST_VARS["do"];
        
        // determine page to display and page, which the data needs to processed from.      
        $firstRun = false;
        if ($do=="" || $do=="Back") $firstRun=true;
     	if ($do=="Back") $id--;
     	if ($id == "" || $id==0 || $id=="0") $id = 1;
       
        $this->active_page = $id;
        $this->display_page = $id;
         
        if ($do == "Next") {
          $this->pages[$this->active_page]->check();
          if ($errors=="") { 
            $this->display_page++;	
          }
        }
        	
     	$amountPages = count($this->pages);
     	if ($this->display_page > $amountPages) $this->display_page = $amountPages;
     	if ($this->active_page > $amountPages) $this->active_page = $amountPages;
     	
     	// draw and process.
     	$this->draw_header();
     	$this->pages[$this->active_page]->start($firstRun);
     	$this->draw_footer();
     }
     
    /**
     * Returns the installerpage on a certain position
     * @param integer $pos Position of the page in the installer
     */
    function &getPage($pos) {
     	if (count($this->pages)<$pos) {
     	  return  $this->pages[$pos];
     	} else {
     	  return null;	
     	}
    }
    
    /**
     * Draw the header of the page
     */
    function draw_header() {
    	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">";
	echo "<html>";
	echo "<head>";
	echo '<title>phpScriptInstaller: '.$this->title.'</title>';
	echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">";
	echo '<link href="'.$this->css.'" rel="stylesheet" type="text/css">';
	echo '</head>';
	echo '<body>';
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
  	echo '  <tr>'; 
    	echo '    <td width="50%" class="headline">'.$this->title_html.'</td>';
    	echo '    <td width="50%" class="headline2"><div align="right">Step '.$this->display_page.'/'.count($this->pages).' : '.$this->pages[$this->display_page]->title.'</div></td>';
  	echo '  </tr>';
	echo '</table>';
	echo '<form name="installer" method="post">';
	echo '<br><br><br>';
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
  	echo '  <tr>';
    	echo '    <td width="10%" valign="top">&nbsp;</td>';
    	echo '    <td width="70%" valign="top">';
    	echo '<!-- begin output -->';
    	echo '<div class="pageheader">Step '.$this->display_page.': '.$this->pages[$this->display_page]->title.'</div><br><br>';
    }
    
    /**
     * Draw the footer of the page
     */
    function draw_footer() {
        echo '<br><br>';
        echo '<input type="hidden" name="page" value="'.$this->active_page.'">';
        
        if ($this->display_page >1) echo '<input type="submit" name="do" value="Back">&nbsp;&nbsp;';
        
        if ($this->display_page < count($this->pages)) echo '<input type="submit" name="do" value="Next">&nbsp;&nbsp;';
        echo '</td>';
        echo '<td width="20%" valign="top">&nbsp;</td>';
        echo '</tr>';
	echo '</table>';
	echo '<br><br><br><br><br>';
	echo '</form>';
	echo '<div align="center">Created with <a href="http://pei.sourceforge.net" target="_blank">phpScriptInstaller</a></div>';
	echo '</body>';
	echo '</html>'; 	
    }
}
  
  
/////////////////////////////////////////////////////////////////////////////////////////////
  /**
   * The installtion process is divided into several steps. Each step is equal to one page.
   * A page is the container for all other widgets
   */
  class Page {
  
    var $title;    
    var $widgets=null;
    var $parent;
    
    /**
     * Standard constructor
     * @param string Title of the installer page
     */
    function Page($title) {
    	$this->title = $title;
    }
    
    
    /**
     * Add a Widget to this page
     * @param object $widget The widget, you want to add.
     */
    function addWidget(&$widget) {
    	$this->widgets[count($this->widgets)] = $widget;
    	$widget->parent = &$this;
    }
    
    
    /**
     * Checks, whether all validations to this page are correct or not
     */
    function check() {
      for ($i=0; $i < count($this->widgets); $i++) {
        $this->widgets[$i]->check();
      }	   	 	
    }
    
    /**
     * Start processing the page. Is called internally, so you do not need to call by yourself.
     * @param boolean Flag, if page has already been displayed or not.
     */
    function start($firstRun=false) {
      global $errors, $HTTP_POST_VARS;
      $do = $HTTP_POST_VARS["do"];    
      
      if (! $firstRun) {
       
        // everything is alright.
        if ($errors == "") {
          for ($i=0; $i < count($this->widgets); $i++) {
            $this->widgets[$i]->execute();	
          }
          $this->execute();      
          if ($do=="Next") {
            if ($this->parent->active_page < count($this->parent->pages)) $this->parent->active_page++;
            $this->parent->pages[$this->parent->active_page]->start(true);
            return 0;	
          }
        }

      }
      $this->draw();
    }	
    
    /**
     * This function contains all the logic which is used for 
     * controlling the page-switcher. Overide it for taking
     * control over the installation process.
     */
    function execute() {
      // do somethin here.
    }
    
    /**
     * Drawing the page
     */
    function draw() {
      for ($i=0; $i < count($this->widgets); $i++) {
        $this->widgets[$i]->draw();	
      } 
    }
  }

/////////////////////////////////////////////////////////////////////////////////////////////

  /**
    * Container, which contains other widgets. Use to create config-Files with several entries.
    */
 class Container {
      var $widgets = null;	
      var $errorstring = "";
      
    function Container() {}
    
    /**
     * Add a Widget to this container
     * @param object $widget The widget, you want to add.
     */
    function addWidget(&$widget) {
    	$this->widgets[count($this->widgets)] = $widget;
    }
    
    /**
     * Drawing the page
     */
    function draw() {
      for ($i=0; $i < count($this->widgets); $i++) {
        $this->widgets[$i]->draw();	
      } 
    }
    
    /**
     * Checks, whether all validations to this page are correct or not
     */
    function check() {
      for ($i=0; $i < count($this->widgets); $i++) {
        $this->widgets[$i]->check();
      }	   	 	
    }
    
    /**
     * Format an errorstring;
     */   
    function prepareErrorString() {
      if ($this->errorstring != "") {
    	return '<div class="error">'.$this->errorstring.'</div><br>';
      } else {
        return '';
      } 	
    }	
	
}


/////////////////////////////////////////////////////////////////////////////////////////////

  class Widget {
  	
    var $parent=null;	
    var $errorstring='';
    
    /**
     * Standard constructor
     */
    function Widget() {    }
  
    /**
     * Draw the widget
     */
    function draw() {    }
  
    /**
     * Check for correctness
     */
    function check() {    }
    
    /**
     * Do some action here
     */
    function execute() {   }
    
    /**
     * Format an errorstring;
     */   
    function prepareErrorString() {
      if ($this->errorstring != "") {
    	return '<div class="error">'.$this->errorstring.'</div><br>';
      } else {
        return '';
      } 	
    }
  }



/////////////////////////////////////////////////////////////////////////////////////////////  
   /**
    * Container, which contains other widgets. Use to create config-Files with several entries.
    */
    class ConfigFileParser extends Container {
    
      var $sourcefiles = null;
      var $destinationfiles = null;
      var $widgets = null;
      
    /**
     * Standard constructor
     */
     function ConfigFileParser() {}
     
     /**
      * Add a file, that is to be parsed, if all inputs are correct!
      * @param string $source Sourcefile, which is to be parsed
      * @param string $destination Filename, where the configfile is to be stored.
      */
     function addFileToParse($source, $destination) {
       $this->sourcefiles[count($this->sourcefiles)] = $source;	
       $this->destinationfiles[count($this->destinationfiles)] = $destination;	
     }
     
    
    /**
     * Write the configuartions here.
     */
    function execute() {   
      for ($i=0; $i < count($this->sourcefiles); $i++) {
      	// load the sourcefile....
      	$input = "";
      	$file = @fopen($this->sourcefiles[$i], 'r');
        while($line=@fgets($file,200)){
          $input.= $line;
        }
        @fclose($file);
        // parsing....
        // search for all child-widgets with parseNames.
        for ($j=0; $j < count($this->widgets); $j++) {
          if ( $this->widgets[$j]->parseName != "") {
            // replace placeholders...
            $input = str_replace('%'.$this->widgets[$j]->parseName.'%', $this->widgets[$j]->value, $input);         
          }	
        }
        
        // save the file....
        if ($handle = @fopen($this->destinationfiles[$i], 'w')) {
		@fwrite($handle, $input );
		@fclose($handle);
	} else {
	  $errors.="-COULD_NOT_WRITE_TO_DISK";
	  $this->errortext="Could not access the filesystem for writing.";	
	}
      }
    }
    	
    	
    }


/////////////////////////////////////////////////////////////////////////////////////////////  
  
  /**
   * Base class of all widgets that are using common validation and inputfields
   */
  class IWidget extends Widget {
  
    var $label;
    var $parseName;
    var $value;
    var $validation;
    var $style;
    /**
     * Class constructor
     * @param string $label Text that will be displayed 
     * @param string $parseName Name of the placeholder, that will be parsed with the value
     * @param string $value Standard value of the input
     * @param string $validation Validate the input filed with this functions (MANDATORY)
     * @param string $style Style of the input
     */
    function IWidget($label, $parseName, $value="", $validation="MANDATORY", $style="") {
      global $HTTP_POST_VARS;
      $this->label = $label;
      $this->parseName = $parseName;
      $this->value = $value;
      $this->validation = strtoupper($validation);
      $this->style = $style;
      if (isset($HTTP_POST_VARS[$this->parseName])) $this->value=$HTTP_POST_VARS[$this->parseName];
    } 
    
    /**
     * Executes the validators given in the object definition.
     */
    function check() {
    	global $errors;
    	if (stristr('MANDATORY', $this->validation)) {
    	  if ($this->value == "") {
    	    $errors.= "-".$this->parseName."_MANDATORY";
    	    $this->errorstring = "This field cannot be empty.";	
    	  }	
    	}
    }
    
   /**
    * Draw the IWidget
    */
   function draw() {
      echo '<table width="100%" cellpadding="2" cellspacing="0">';
      echo '<tr>';
      if ($this->style != "") {
      	echo '<td width="30%" valign="top" class="'.$this->style.'">';
      } else {
        echo '<td width="30%" valign="top">';	
      } 
      echo $this->label;
      if ($this->errorstring != "") {
      	echo "<br>";
      	echo $this->prepareErrorString();
      }
      echo '</td>';
      echo '<td width="70%" valign="top">';
      echo $this->draw_input();
      echo '</td></tr>';
      echo '</table>';
   }
   
   /**
    * Draw the input-tags; virtual
    */
    function draw_input() {  }
   
  	
  }
///////////////////////////////////////////////////////////////////////////////////////////// 

	/**
	 * Draws a simple Text-Input field
	 */
	class TextInput extends IWidget {
			
		/**
		 * Draw the text-input-field.
		 */
		function draw_input() {
		  echo '<input type="text" name="'.$this->parseName.'" value="'.$this->value.'" style="width:300px;">';	
		}	
	}
	
///////////////////////////////////////////////////////////////////////////////////////////// 

	/**
	 * Holds a value from a page before.
	 */
	class Retain extends IWidget {
			
		/**
		 * Standard constructor
		 * @param string $parsename Name of value to hold
		 */
		 function Retain($parsename) {
		   global $HTTP_POST_VARS;
		   IWidget::IWidget("", $parsename, "", "");
		   $this->value = $HTTP_POST_VARS[$this->parseName];	
		 }
		 
		/**
		 * Nothing to check
		 */
		function check() {}
		
		/**
		 * Draw the text-input-field.
		 */
		function draw_input() {
		   echo '<input type="hidden" name="'.$this->parseName.'" value="'.$this->value.'">';	
		}	
	}

///////////////////////////////////////////////////////////////////////////////////////////// 

  /**
   * Draws a simple Text-Input field
   */
   class Path extends TextInput {
    
    var $addition;
    /**
     * Class constructor
     * @param string $label Text that will be displayed 
     * @param string $parseName Name of the placeholder, that will be parsed with the value
     * @param string $addition Path to add, without leading slash!
     */
    function Path($label, $parseName, $addition ) {
      global $HTTP_SERVER_VARS;
      $value = $this->realpath2('./install.php');
      $value = str_replace("\\\\", "/", $value);
      $value = str_replace('\install.php', $addition, $value);
      $value = str_replace('/install.php', $addition, $value);
      IWidget::IWidget($label, $parseName, $value);
    } 
    
    function realpath2($path)
    {
    	////check if realpath is working
    	if (strlen(realpath($path))>0)
    	return realpath($path);

    	///if its not working use another method///
    	$p=getenv("PATH_TRANSLATED");
    	$p=str_replace("\\","/",$p);
    	$p=str_replace(basename(getenv("PATH_INFO")),"",$p);
    	$p.="/";
    	if ($path==".")
    	return $p;
    	//now check for back directory//
    	$p=$p.$path;



    	$dirs=split("/",$p);
    	foreach($dirs as $k => $v)
    	{

    		if ($v=="..")
    		{
    			$dirs[$k]="";
    			$dirs[$k-2]="";
    		}
    	}
    	$p="";
    	foreach($dirs as $k => $v)
    	{
    		if (strlen($v)>0)
    		$p.=$v."/";
    	}
    	$p=substr($p,0,strlen($p)-1);


    	if (is_dir($p))
    	return $p;
    	if (is_file($p))
    	return $p;


    	return false;

    }
		
			
  }

///////////////////////////////////////////////////////////////////////////////////////////// 

  /**
   * Draws a simple Text-Input field
   */
   class Docroot extends TextInput {
    
    var $addition;
    /**
     * Class constructor
     * @param string $label Text that will be displayed 
     * @param string $parseName Name of the placeholder, that will be parsed with the value
     * @param string $addition Path to add, without leading slash!
     */
    function Docroot($label, $parseName, $addition ) {
      $value = $_SERVER['REQUEST_URI'];
      $value = str_replace("\\", "/", $value);
      $value = str_replace('install.php', $addition, $value);
      IWidget::IWidget($label, $parseName, $value);
      
    } 		
			
  }
///////////////////////////////////////////////////////////////////////////////////////////// 

  /**
   * Draws a simple Text-Input field
   */
   class WebServer extends TextInput {
    
    var $addition;
    /**
     * Class constructor
     * @param string $label Text that will be displayed 
     * @param string $parseName Name of the placeholder, that will be parsed with the value
     */
    function WebServer($label, $parseName) {
      global $HTTP_SERVER_VARS;
      $value = $HTTP_SERVER_VARS["SERVER_NAME"];
      IWidget::IWidget($label, $parseName, "http://".$value);
    } 		
			
  }

///////////////////////////////////////////////////////////////////////////////////////////// 
/**
 * Draws a SelectBox with predefined values array.
 */
class SelectBox extends IWidget {
			
    var $values = null;
    
    /**
     * Class constructor
     * @param string $label Text that will be displayed 
     * @param string $parseName Name of the placeholder, that will be parsed with the value
     * @param string $values Array with names and values, e.g. (1 => ("name" => "Name", "value" => "Value"),...)
     * @param string $validation Validate the input filed with this functions (MANDATORY)
     * @param string $style Style of the input
     */
    function SelectBox($label, $parseName, $value="", $values=null, $style="") {
      $this->values = $values;
      IWidget::IWidget($label, $parseName, $value, "novalidation", $style);
    }
		
    /**
    * Draw the Selectbox
    */
    function draw_input() {
	  echo '<select name="'.$this->parseName.'" size="1">';
	  for ($i=0; $i < count($this->values); $i++) {
	    echo '<option value="'.$this->values[$i]["value"].'"';
	    if ($this->value == $this->values[$i]["value"]) echo ' selected';
	    echo '>'.$this->values[$i]["name"].'</option>';	
	  }
	  echo '</select><br>';
    }	
}
	
///////////////////////////////////////////////////////////////////////////////////////////// 

	/**
	 * Draws a simple TextArea-Input field
	 */
	class TextArea extends IWidget {
			
		var $rows = 10;
		/**
		 * Draw the text-input-field.
		 */
		function draw_input() {
		  echo '<textarea name="'.$this->parseName.'" style="width:300px;" rows="'.$this->rows.'">'.$this->value.'</textarea>';	
		}	
	}
/////////////////////////////////////////////////////////////////////////////////////////////  
	/**
	 * Draws a password input field
	 */
	 class PasswordInput extends IWidget {
	
		var $value1;
		/**
     		* Class constructor
     		* @param string $label Text that will be displayed 
     		* @param string $parseName Name of the placeholder, that will be parsed with the value
     		* @param string $value Standard value of the input
     		* @param string $validation Validate the input filed with this functions (MANDATORY)
     		* @param string $style Style of the input
     		*/
    		function PasswordInput($label, $parseName, $value="", $style="") {
      			global $HTTP_POST_VARS;
    			IWidget::IWidget($label, $parseName, $value, "", $style);
    			$this->value1 = $value;
      			if (isset($HTTP_POST_VARS[$this->parseName.'1'])) $this->value=$HTTP_POST_VARS[$this->parseName.'1'];
      			if (isset($HTTP_POST_VARS[$this->parseName])) $this->value1=$HTTP_POST_VARS[$this->parseName];
    		} 
    
	 	
	 	/**
     		* Executes the validators given in the object definition.
     		*/
    		function check() {
    			global $errors;
    			if ($this->value != $this->value1) {
    	    			$errors.= "-".$this->parseName."_MANDATORY";
    	    			$this->errorstring = "The passwords do not match.";		
    			}
    		}
	 	
	 	/**
	 	 * Draw the password-Input
	 	 */
	 	function draw_input() {
	 	  	 	  
	 	  echo '<input type="password" name="'.$this->parseName.'" value="'.$this->value.'" style="width:150px;"><br>';	
	 	  echo '<input type="password" name="'.$this->parseName.'1" value="'.$this->value.'" style="width:150px;">';
	 	 	
	 	}
	 		
	 	
	}

/////////////////////////////////////////////////////////////////////////////////////////////  
  class LicenseAgreement extends Widget {
  
    var $filename;
    
    /**
     * Standard constructor
     * @param string $filename Filename of the License, relative to install.php
     */
    function LicenseAgreement($filename="license") {
      $this->filename = $filename;	
    }
    
    /**
     * Draw the widget
     */
    function draw() {
      // load license from file
      $license = "";
      $file = @fopen($this->filename, 'r');
      while($line=@fgets($file,200)){
        $license.= $line;
      }
      @fclose($file);  
      
      // display license
      echo '<textarea rows="20" style="width:100%;">'.$license.'</textarea>';	
      echo '<br><br>';
      echo $this->prepareErrorString();
      echo '<input type="radio" name="license" value="no">No, I do not accept this license or I did not understand it.<br>';
      echo '<input type="radio" name="license" value="yes">Yes, I understand and accept this license.<br>';
    }
    
    /**
     * Check, whether yes is set or not.
     */
    function check() {
      global $errors, $HTTP_POST_VARS;
      if ($HTTP_POST_VARS["license"] != "yes") {
      	$this->errorstring = "You must accept the license, if you want to install the program.";
      	$errors.= "-LICENSE_NOT_ACCEPTED";
      }
    }
  	
  }
/////////////////////////////////////////////////////////////////////////////////////////////  
  class Label extends Widget {
  
   var $text;
   var $style;
   
   /**
    * Standard constructor
    * @param string $text Text to display
    * @param string $style CSS-Style to use for display
    */
    function Label($text="", $style="") {
      $this->text = $text;
      $this->style = $style;	
    }	
    
    function draw() {
      if ($this->style != "") echo '<div class="'.$this->style.'">';
      echo $this->text."<br>";
      if ($this->style != "") echo '</div>';
    }
  }
/////////////////////////////////////////////////////////////////////////////////////////////
   class Information extends Widget {
  
    var $dtext;
    
    /**
     * Standard constructor
     * @param string $dtext Text to display. You must choosee text or a filename!
     * @param string $filename Filename of the License, relative to install.php
     */
    function Information($dtext="", $filename="") {
      $this->dtext=$dtext;
      if ($filename != "") {
        $file = @fopen($filename, 'r');
        while($line=@fgets($file,200)){
          $this->dtext.= $line;
        }
        @fclose($file);
        // apply some formatting...
        $this->dtext = nl2br($this->dtext);
        $this->dtext = str_replace('//b/', '</b>', $this->dtext);  	
        $this->dtext = str_replace('/b/', '<b>', $this->dtext);  	
        $this->dtext = str_replace('//t/', '</div>', $this->dtext);  	
        $this->dtext = str_replace('/t/', '<div class="pageheader">', $this->dtext);  	
      }
      
    }
    
    /**
     * Draw the widget
     */
    function draw() {
	echo $this->dtext;
    }
    	
  }
?>