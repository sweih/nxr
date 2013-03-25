<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih (sven@nxsystems.org), Fabian Koenig (fabian@nxsystems.org)
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
* @package DatabaseConnectedInput
*/

	/**
	 * draws a file-upload-field escecially for uploading images to the system and provides 
	 * all mechanisms for upload the image.
	 * @package DatabaseConnectedInput
	 */
	class ImageUpload extends WUIInterface {
		var $v_label;

		var $v_wuiobject;
		var $fileId;
		var $label;
		var $suffix = "";
		var $name;
		var $fkid;
		var $width;
		var $height;
		var $filename;
		var $id;
		var $pgnref;

		/**
		  * standard constructor
		  * @param string Text that is to be shown as description or label with your object.
		  * @param integer ID, that corresponds to the image file. the ending of the uploaded
		  * file will be detected and addes automatically. No manipulation to the database is 
		  * done.
		  */
		function ImageUpload($label, $file_id, &$pgnref) {
			global $specialID;

			$this->pgnref = &$pgnref;
			
			$this->fileId = $file_id;
			$this->label = $label;
			$this->fkid = $file_id;
			$this->name = "image" . $specialID;
			$this->v_label = new Label("lbl", $label, "standard");
			$this->v_label->text = "<span class=\"standardlight\">$label</span>"; // set text light.
			$this->v_wuiobject = new Filebox($this->name, "standard", "image/*");
			$this->v_wuiobject->additionalAttribute = "onChange='preview".$this->fkid."();'";
			$this->id = $specialID;
		}

	
		/**
		 * checks, whether the uploaded file is an image or not.
		 */
		function check() {
			// check if right file type;
			//global $image, $image_name;
			if (isset($_FILES[$this->name])) {
				$image_name = $_FILES[$this->name]['name'];

				if ($image_name != "") {
					$fileparts = explode(".", $image_name);

					$suffix = strtolower($fileparts[(count($fileparts) - 1)]);

					if ($suffix != "png" && $suffix != "gif" && $suffix != "jpg") {
						$this->addError("WRONGFILETYPE", "The file type you selected for upload is wrong!");
					}
					$this->suffix = $suffix;
				}
			}
		}
		/**
		 * determine the size of the image. write size to the database.
		 * copy file to local folder
		 */
		function process() {
			global $c;

			
			$delete = $_POST["REMOVE".$this->fkid];
			if ($delete) {
			  addUpdate("pgn_image", "FILENAME", "", "FKID = ".$this->fileId, "TEXT");
			  addUpdate("pgn_image", "WIDTH", 0, "FKID = ".$this->fileId, "NUMBER");
			  addUpdate("pgn_image", "HEIGHT", 0, "FKID = ".$this->fileId, "NUMBER");	
			} else {	
				// check, if upload.
				if ($this->suffix != "") {
					$image = $_FILES[$this->name]['tmp_name'];
	
					$image_name = $_FILES[$this->name]['name'];
	
					//determine image size
					$size = getimagesize($image);
					$width = $size[0];
					$height = $size[1];
					$oldfilename = getDBCell("pgn_image", "FILENAME", "FKID = " . $this->fileId);
					$filename = $this->fileId . "." . $this->suffix;
					// updating the database
					addUpdate("pgn_image", "FILENAME", $filename, "FKID = " . $this->fileId, "TEXT");
					addUpdate("pgn_image", "WIDTH", $width, "FKID = " . $this->fileId, "NUMBER");
					addUpdate("pgn_image", "HEIGHT", $height, "FKID = " . $this->fileId, "NUMBER");
	
					// loading up new item.
					if ($oldfile != "")
						unlink ($c["devfilespath"] . $oldfilename);
	
					move_uploaded_file($image, $c["devfilespath"] . $filename);
					
					nxCopy($c["devfilespath"] . $filename, $c["devfilespath"], "orig_".$filename);
	
					// setting variables for further filtering
					$this->filename = $filename;
					$this->width = $width;
					$this->height = $height;
		
					// try creating thumbnails
					if ($c["useimagemagick"]) {
						$thumb = new NXImageApi($c["devfilespath"].$filename, $c["devfilespath"]."t".$filename);
						$thumb->resizeAbsolute(120, 120);
						$thumb->gravity = "Center";
						$thumb->drawText("Preview");
						$thumb->save();
						
						$this->applyAutoMod($filename);
					} else {
						$thumb = new Img2Thumb($c["devfilespath"].$filename, 120, 120, $c["devfilespath"]."t".$filename);
					}
					
					//determine image size
					$size = getimagesize($c["devfilespath"] . $filename);
					$width = $size[0];
					$height = $size[1];
					$oldfilename = getDBCell("pgn_image", "FILENAME", "FKID = " . $this->fileId);
					$filename = $this->fileId . "." . $this->suffix;
					// updating the database
					addUpdate("pgn_image", "FILENAME", $filename, "FKID = " . $this->fileId, "TEXT");
					addUpdate("pgn_image", "WIDTH", $width, "FKID = " . $this->fileId, "NUMBER");
					addUpdate("pgn_image", "HEIGHT", $height, "FKID = " . $this->fileId, "NUMBER");					
					
				}
			}
		}
		/** For Down-Grade-Compatibility only **/
		function proccess() { $this->process(); }

		/**
		 * apply automatic modifications of the uploaded images as specified in a ClusterTemplateItem.
		 */
		function applyAutoMod($filename) {
			global $c;
			$maxwidth = $this->pgnref->getOption("TEXT1");
			$maxheight = $this->pgnref->getOption("TEXT2");
			
			if (($maxwidth != "") || ($maxheight != "")) {
				$imageapi = new NXImageApi($c["devfilespath"]."orig_".$filename, $c["devfilespath"].$filename);
				if (stristr($maxwidth, "%") && stristr($maxheight, "%")) {
					$imageapi->resizeRelative($maxwidth, $maxheight);
				} else if (is_numeric($maxwidth) && is_numeric($maxwidth)) {
					$imageapi->resizeAbsolute($maxwidth, $maxheight);
				}
				$imageapi->save();
			}
		}
		
		
		/**
		 * Add an errormessage to the global error-String.
		 * Changes also the colors of the DBO so that users can find the wrong input easier.
		 * @param string errortext, to be added to the global error-string. for debugging only.
		 * @param string text, to be displayed in the DBO for telling the user, what he did wrong.
		 */
		function addError($error, $text) {
			global $errors;

			$errors .= "-" . $error;
			$this->v_label = new Label("lbl_" . $this->column, $this->label . "<br><span style=\"font-weight:normal;\">" . $text . "</span>", "error");
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
			$currentimage = $c["devfilesdocroot"];
			if ($this->fkid != "") {
				$currentimage = $c["devfilesdocroot"].getDBCell("pgn_image", "FILENAME", "FKID = ".$this->fkid);
				$filename = getDBCell("pgn_image", "FILENAME", "FKID = ".$this->fkid);
			}
			if ($currentimage == $c["devfilesdocroot"])
				$currentimage = $c["host"].$c["docroot"]."img/ptrans.gif";
			echo "<tr><td colspan=\"2\" class=\"standardlight\" align=\"center\"><img id=\"prv".$this->fkid."\" src=\"".$currentimage."\"></td></tr>";
			echo '<script language="JavaScript">';
			echo '  function preview'.$this->fkid.'() {';
			echo '    var target = document.form1.'.$this->name.'.value;';
			echo '	  if (target == "") target = "'.$c["devfilesdocroot"].$filename.'";';
			echo '    if (document.getElementById("REMOVE'.$this->fkid.'").checked) target = "'.$c["host"].$c["docroot"].'img/ptrans.gif";';
			echo '    document.getElementById("prv'.$this->fkid.'").src = target;';
			echo ' }';
			echo '</script>';
			return 2;
		}
	}
?>