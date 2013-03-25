<?
/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2002 Sven Weih, Fabian König
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
  * draws a file-upload-field escecially for uploading media files to the system and provides 
  * all mechanisms for upload the media file.
  * @package DatabaseConnectedInput
  */
	class MediaUpload  {
	
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

 	/**
 	 * standard constructor
 	 * @param string Text that is to be shown as description or label with your object.
 	 * @param integer ID, that corresponds to the image file. the ending of the uploaded
 	 * file will be detected and addes automatically. No manipulation to the database is 
 	 * done.
 	 */
 	function MediaUpload($label, $file_id) {
		global $specialID;
		$this->fileId = $file_id;
		$this->label = $label;
		$this->fkid = $file_id;
		
		$this->name = "image".$specialID;
		$this->v_label = new Label("lbl", $label, "standard");
 		$this->v_label->text = "<span class=\"lighttext\">$label</span>"; // set text light.
		$this->v_wuiobject = new Filebox ($this->name, "standard", "image/*");
	}
	

	
	/**
 	 * Calculates the new size of an image to resize it proportional
 	 * @param integer Maximum width of the thumbnail
 	 * @param integer Maximum height of the thumbnail
 	 * @returns array["WIDTH"], array["HEIGHT"]
 	 */
 	function scale($XScale=300, $YScale=200) {
 	 	$scale=1;
 	 	$dwidth =  $this->width;
 	 	$dheight =  $this->height;
 	 	if ($dwidth > $XScale || $dheight>$YScale) {
			$scale_w = $dwidth / $XScale;
			$scale_h = $dheight / $YScale;
			$scale = max($scale_w, $scale_h);
			//scale down
			$dwidth = floor($dwidth / $scale);
			$dheight = floor($dheight / $scale);
 	 	}
 		$res["WIDTH"] = $dwidth;
 		$res["HEIGHT"] = $dheight;
 		return $res;
 	}
 	

	/**
	 * checks, whether the uploaded file is an image or not.
	 */
	function check() {
		global $HTTP_POST_FILES;
		// check if right file type;
		//global $image, $image_name;
		
		
		if (isset($HTTP_POST_FILES[$this->name])) {
			$image_name = $HTTP_POST_FILES[$this->name]['name'];
			//$image_name = trim($image_name);
			//ereg_replace("\"", "", $image_name);
			//$image = trim($image);
			//ereg_replace("\"", "", $image);
			if ($image_name != "") {
				$fileparts = explode(".", $image_name);
				$suffix = strtolower($fileparts[(count($fileparts)-1)]);

				// Add allowed file suffixes here
				$suffixes_allowed = array("swf","pdf","avi","mp3","rm","mov");

				if (!in_array($suffix, $suffixes_allowed)) {
					$this->addError("WRONGFILETYPE", "The file type you selected for upload is not supported!"); 
				}
				$this->suffix = $suffix;
			}
		}
	}
	
	/**
	 * for downgrad-compatibility only.
	 */
	function proccess() {
		$this->process();
	}
	
	/**
	 * determine the size of the image. write size to the database.
	 * copy file to local folder
	 */
	function process() {
		global $c, $HTTP_POST_FILES, $HTTP_POST_VARS;
		// check, if upload.
		$delete = $HTTP_POST_VARS["REMOVE"];
		if ($delete) {
		  addUpdate("pgn_media", "FILENAME", "", "FKID = ".$this->fileId, "TEXT");
		  addUpdate("pgn_media", "WIDTH", -1, "FKID = ".$this->fileId, "NUMBER");
		  addUpdate("pgn_media", "HEIGHT", -1, "FKID = ".$this->fileId, "NUMBER");		
		} else {
		  switch ($this->suffix) {

			case "swf":
				$image = $HTTP_POST_FILES[$this->name]['tmp_name'];
				$image_name = $HTTP_POST_FILES[$this->name]['name'];
				
				//determine image size
				$size = getimagesize($image);
				$width = $size[0];
				$height= $size[1];
				$oldfilename = getDBCell("pgn_media", "FILENAME", "FKID = ".$this->fileId);
				$filename = $this->fileId.".".$this->suffix;
				// updating the database
								
				addRawSQL( "UPDATE pgn_media SET FILENAME = '$filename', WIDTH=$width, HEIGHT=$height WHERE FKID = ".$this->fileId );
							

/*				
				// loading up new item.
				if ($oldfile !="") unlink($c["uploadpath"].$oldfilename);
				copy($image, $c["uploadpath"].$filename);
*/

				// loading up new item.
				if ($oldfile != "")
					unlink ($c["devfilespath"] . $oldfilename);

				move_uploaded_file($image, $c["devfilespath"] . $filename);
				
				
				// setting variables for further filtering
				$this->filename = $filename;
				$this->width = $width;
				$this->height = $height;
				
				// create a thumbnail
				//$this->createThumbnail();
				//$this->createGrayCopy("t".$this->filename);
			
				break;

			case "pdf":
			case "rm":
			case "avi":
			case "mp3":
			case "mov":
				$image = $HTTP_POST_FILES[$this->name]['tmp_name'];
				$image_name = $HTTP_POST_FILES[$this->name]['name'];
				
				$oldfilename = getDBCell("pgn_media", "FILENAME", "FKID = ".$this->fileId);
				$filename = $this->fileId.".".$this->suffix;

				// updating the database
				addUpdate("pgn_media", "FILENAME", $filename, "FKID = ".$this->fileId, "TEXT");
				addUpdate("pgn_media", "WIDTH", -1, "FKID = ".$this->fileId, "NUMBER");
				addUpdate("pgn_media", "HEIGHT", -1, "FKID = ".$this->fileId, "NUMBER");
							
/*
				// loading up new item.
				if ($oldfile !="") unlink($c["uploadpath"].$oldfilename);
				copy($image, $c["uploadpath"].$filename);
*/

				// loading up new item.
				if ($oldfile != "")
					unlink ($c["devfilespath"] . $oldfilename);

				move_uploaded_file($image, $c["devfilespath"] . $filename);
											
				// setting variables for further filtering
				$this->filename = $filename;
				$this->width = $width;
				$this->height = $height;
			
				break;

			default:
		}
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
			$errors.="-".$error;
			$this->v_label = new Label("lbl_".$this->column, $this->label."<br><span class=\"lighttext\">".$text."</span>", "error");
			$this->v_wuiobject->style = "error";
			$this->std_style = "error";
	}


	/**
	 * Draw the upload-box
	 */
	function draw() {
		$this->v_label->draw();
		$this->v_wuiobject->draw();
		return 2;
	}

}
 ?>