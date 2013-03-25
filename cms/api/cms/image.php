<?php

/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: image.php,v 1.3 2004/11/29 08:20:44 sven_weih Exp $ *
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
  * Create a image from a file. Do not add the file to the object library
  * @param string Path to the source file
  * @param string Description Text for ALT-Tag
  * @param string Copright text for the image
  * @param string Variation-ID of the image
  */
 function createImageFromFile($sourceFile, $alt="", $copyright="", $variation=1, $categoryId=1) {
   global $c, $db;
   $id = nextGUID();
   $info = pathinfo($sourceFile);
   $extension = $info["extension"];
   $extension2 = strtoupper($extension);
   $name = parseSQL($info["basename"]);
   if ($extension2 == "JPG" || $extension2 == "GIF" || $extension2 == "PNG") {
   	 $size = getimagesize($sourceFile);
	 $width = $size[0];
	 $height = $size[1];   	
	 copy($sourceFile, $c["devfilespath"].$id.".".$extension);
	 $thumb = new Img2Thumb($c["devfilespath"].$id.".".$extension, 120, 120, $c["devfilespath"]."t".$id);
   	 $sql = "INSERT INTO pgn_image (FKID, FILENAME, ALT, COPYRIGHT, WIDTH, HEIGHT) VALUES ";
   	 $sql.= "($id, '$id.$extension', '$alt', '$copyright', $width, $height)";   	 
   	 $query = new query($db, $sql);
   	 $query->free();
   	 // Create Library Entry for this image
   	 $cid = nextGUID();
   	 $imageModule = getDBCell("modules", "MODULE_ID", "MODULE_NAME='Image'");
   	 $sql = "INSERT INTO content (CID, MODULE_ID, NAME, CATEGORY_ID, MT_ID) VALUES ";
   	 $sql.="($cid, $imageModule, '$name', $categoryId, 0)";   	 
   	 $query = new query($db, $sql);
   	 $query->free();
   	 $sql = "INSERT INTO content_variations (CID, VARIATION_ID, FK_ID) VALUES ";
   	 $sql.= "($cid, $variation, $id)";
   	 $query = new query($db, $sql);
   	 $query->free();
   	 return $cid;
   } else {
   	 return null;
   }
 }
 
?>