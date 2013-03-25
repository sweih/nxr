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
  * Creates databases and structures within it
  */
 class CreateMySQL extends Container {
 
  var $dump;
  var $db="";
  var $dbuser="";
  var $dbpasswd="";
  var $dbserver="";
  var $dblink;
  var $messages="";
  
  /**
   * Standard constructor
   */
   function CreateMySQL($dump="dump.sql") {
   	global $HTTP_POST_VARS;
   	$this->dump = $dump;
   	$this->addWidget(new Label("We will setup the database now. Please note, that you can cause damage to your database-server here, when entering wrong information! The installation script will check, if the database you defined already exists. If not, it tries to create it.<br><br>"));
   	$this->addWidget(new TextInput("Database Server", "DBSERVER"));
   	$this->addWidget(new TextInput("Database", "DB"));
   	$this->addWidget(new TextInput("Database User", "DBUSER"));
   	$this->addWidget(new PasswordInput("Password", "DBPASSWD"));  	
   	if (isset($HTTP_POST_VARS["DB"])) $this->db=$HTTP_POST_VARS["DB"];
   	if (isset($HTTP_POST_VARS["DBSERVER"])) $this->dbserver=$HTTP_POST_VARS["DBSERVER"];
   	if (isset($HTTP_POST_VARS["DBUSER"])) $this->dbuser=$HTTP_POST_VARS["DBUSER"];
   	if (isset($HTTP_POST_VARS["DBPASSWD"])) $this->dbpasswd=$HTTP_POST_VARS["DBPASSWD"];
   }
   
   
   /**
    * Create the database.
    */
   function execute() {
    
   }
   
   function draw() {
      echo $this->prepareErrorstring();
      echo "<br>";
      Container::draw();  	
   }		
 
   function check() {
     global $errors;
     Container::check();
     if ($errors == "") $this->checkDBServer();
     if ($errors == "") $this->assureDB();
     if ($errors == "") $this->readDump();	
   }
   
   
   
   /**
    * Create the structures from dump-file
    */
   function readDump() {
    global $errors;
    $text="";
    $file = @fopen($this->dump, 'r');
    while($line=@fgets($file,200)){
          $text.= $line;
    }
    @fclose($file);	
    
    $ret = null;
    $this->splitSQLFile($ret, $text, 0);
    for ($i=0; $i<count($ret); $i++) {
      @mysql_query($ret[$i]);
      if (mysql_error() != "") {
        $errors.="-COULD_NOT_CREATE_DUMP";
      }
    }
    if ($errors != "") $this->errorstring.="The dump was not written to the database successfully. Please check, if the db was empty!";	   
   }
   
   
   /**
    * Make sure, that db exists or create it
    */   
   function assureDB() {
     global $db;
     if (mysql_select_db($this->db)) {
     	return true;
     } else {
       mysql_query("Create database " . $this->db, $this->dblink);
       mysql_select_db($this->db);	
        return true;       	
     } 	
   }
   
   /**
    * Checks, if servername, password and username are correct
    */
   function checkDBServer() {
     global $errors;
     $this->dblink="";
     $this->dblink = @mysql_connect($this->dbserver, $this->dbuser, $this->dbpasswd); 
     if ($this->dblink ==0) {    
     	$this->errorstring.="Could not connect to database!<br>";
     	$errors.="-COULD_NOT_CONNECT_TO_DB";
     	return false;
     }
     return true;  	
   }
   
   
   
   
/**
 * Removes comment lines and splits up large sql files into individual queries
 * Based on phpMyAdmin 2.3.0 Read Copyright at phpmyadmin.net for reuse.
 * @param   array    the splitted sql commands
 * @param   string   the sql commands
 * @param   integer  the MySQL release number (because certains php3 versions
 *                   can't get the value of a constant from within a function)
 * @return  boolean  always true
 * @access  public
 */
function splitSqlFile(&$ret, $sql, $release)
{
    $sql          = trim($sql);
    $sql_len      = strlen($sql);
    $char         = '';
    $string_start = '';
    $in_string    = FALSE;
    $time0        = time();

    for ($i = 0; $i < $sql_len; ++$i) {
        $char = $sql[$i];

        if ($in_string) {
            for (;;) {
                $i         = strpos($sql, $string_start, $i);
                if (!$i) {
                    $ret[] = $sql;
                    return TRUE;
                }
                else if ($string_start == '`' || $sql[$i-1] != '\\') {
                    $string_start      = '';
                    $in_string         = FALSE;
                    break;
                }
                else {
                    $j                     = 2;
                    $escaped_backslash     = FALSE;
                    while ($i-$j > 0 && $sql[$i-$j] == '\\') {
                        $escaped_backslash = !$escaped_backslash;
                        $j++;
                    }
                    if ($escaped_backslash) {
                        $string_start  = '';
                        $in_string     = FALSE;
                        break;
                    }
                    else {
                        $i++;
                    }
                } 
            } 
        } 
        else if ($char == ';') {
            
            $ret[]      = substr($sql, 0, $i);
            $sql        = ltrim(substr($sql, min($i + 1, $sql_len)));
            $sql_len    = strlen($sql);
            if ($sql_len) {
                $i      = -1;
            } else {
               return TRUE;
            }
        } 

        
        else if (($char == '"') || ($char == '\'') || ($char == '`')) {
            $in_string    = TRUE;
            $string_start = $char;
        } 

        else if ($char == '#'
                 || ($char == ' ' && $i > 1 && $sql[$i-2] . $sql[$i-1] == '--')) {
            
            $start_of_comment = (($sql[$i] == '#') ? $i : $i-2);
            $end_of_comment   = (strpos(' ' . $sql, "\012", $i+2))
                              ? strpos(' ' . $sql, "\012", $i+2)
                              : strpos(' ' . $sql, "\015", $i+2);
            if (!$end_of_comment) {
                if ($start_of_comment > 0) {
                    $ret[]    = trim(substr($sql, 0, $start_of_comment));
                }
                return TRUE;
            } else {
                $sql          = substr($sql, 0, $start_of_comment)
                              . ltrim(substr($sql, $end_of_comment));
                $sql_len      = strlen($sql);
                $i--;
            } 
        } 

        
        else if ($release < 32270
                 && ($char == '!' && $i > 1  && $sql[$i-2] . $sql[$i-1] == '/*')) {
            $sql[$i] = ' ';
        } 

        $time1     = time();
        if ($time1 >= $time0 + 30) {
            $time0 = $time1;
            header('X-pmaPing: Pong');
        }
    }

      if (!empty($sql) && ereg('[^[:space:]]+', $sql)) {
        $ret[] = $sql;
    }
    return TRUE;
} 	
 	
 }
 
 ?>