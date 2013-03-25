<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2004 Sven Weih
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
	 * draws a file-upload-field escecially for uploading text content	 
	 * @package DatabaseConnectedInput
	 */
	class TextUpload {
		var $v_label;

		var $v_wuiobject;
		var $fileId;
		var $label;
		var $suffix = "";
		var $name;
		var $fkid;
		var $filename;
		var $id;

		/**
		  * standard constructor
		  * @param string Text that is to be shown as description or label with your object.
		  * @param integer ID, that corresponds to the image file. the ending of the uploaded
		  * file will be detected and addes automatically. No manipulation to the database is 
		  * done.
		  */
		function TextUpload($label, $file_id) {
			global $specialID;

			$this->fileId = $file_id;
			$this->label = $label;
			$this->fkid = $file_id;
			$this->name = "text" . $specialID;
			$this->v_label = new Label("lbl", $label, "standard");
			$this->v_label->text = "<span class=\"standardlight\">$label</span>"; // set text light.
			$this->v_wuiobject = new Filebox($this->name, "standard", "image/*");
			$this->v_wuiobject->additionalAttribute = "onChange='preview".$this->fkid."();'";
			$this->id = $specialID;
		}

	
		/**
		 * checks, whether the uploaded file is an textfile or not
		 */
		function check() {
			// check if right file type;
			//global $image, $image_name;
			if (isset($_FILES[$this->name])) {
				$image_name = $_FILES[$this->name]['name'];

				if ($image_name != "") {
					$fileparts = explode(".", $image_name);
					$suffix = strtolower($fileparts[(count($fileparts) - 1)]);
					if ($suffix != "sxw" && $suffix != "xml" && $suffix != "txt" && $suffix != "html" && $suffix != "htm") {
						$this->addError("WRONGFILETYPE", "The file type you selected for upload is wrong!");
					}
					$this->suffix = $suffix;
				}
			}
		}
		/**
		 * Extract the text content and write it to the text-plugin.
		 */
		function process() {
			global $c;
			if ($this->suffix == "xml") {
			  $parser = new ImportMSOffice($_FILES[$this->name]['tmp_name']);
			} else if ($this->suffix == "sxw") {
			  $parser = new ImportOpenOffice($_FILES[$this->name]['tmp_name']);
			} else if ($this->suffix == "html") {
			  $parser = new ImportHTML($_FILES[$this->name]['tmp_name']);
			} else if ($this->suffix == "txt") {
			  $parser = new ImportText($_FILES[$this->name]['tmp_name']);
			}
			
			if (is_object($parser)) {
			  $content = $parser->getParsedContent();	
			  addUpdate("pgn_text", "CONTENT", $content, "FKID = ".$this->fileId, "TEXT");
			  $c["pgn_text_CONTENT".$this->fileId] = $content;
			}	
		}
		
		/** For Down-Grade-Compatibility only **/
		function proccess() { $this->process(); }

		/**
		 * Add an errormessage to the global error-String.
		 * Changes also the colors of the DBO so that users can find the wrong input easier.
		 * @param string errortext, to be added to the global error-string. for debugging only.
		 * @param string text, to be displayed in the DBO for telling the user, what he did wrong.
		 */
		function addError($error, $text) {
			global $errors;

			$errors .= "-" . $error;
			$this->v_label = new Label("lbl_" . $this->column, $this->label . "<br><span style=\"font-weight:normal;\">" . $text."</span>", "error");
			$this->v_wuiobject->style = "error";
			$this->std_style = "error";
		}

		/**
		 * Draw the upload-box
		 */
		function draw() {
			global $c;
			$this->v_label->draw();
			$this->v_wuiobject->draw();			
			return 2;
		}
	}
?>