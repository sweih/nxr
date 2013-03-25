<?php
   /**********************************************************************
	*	N/X - Web Content Management System
	*	Copyright 2004 Fabian König, fabian@nxsystems.org
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
	 * N/X Image-API
	 * Wrapper-Class to ImageMagick functions
	 **/ 
	class NXImageApi {
		var $sourcefile;
		var $tempfile;
		var $tempfiles = array();
		var $destfile;
		var $width;
		var $height;
		var $bgcolor;
		var $gravity;
		var $font = "Arial";
		var $fontsize = 12;
		var $fontstyle = "";
		var $fillcolor;
		var $border = 10;
		var $debug = false;
		var $suffix;
		var $quality;
		var $workingsuffix="png";
		
		var $errors = false; // if there are errors during processing, this is set to true and prevents execution of commandline code.
		
		/**
		 * NX Image Manipulation API
		 * @param string Source File to apply manipulations on (specify sourcefile, destfile or both !)
		 * @param string Destination file with applied manipulations
		 */
		function NXImageApi($sourcefile="", $destfile="", $debug=false) {
			global $c;
			
			$this->debug = $debug;
			
			if (!$c["useimagemagick"]) {
				nxlog("NX_IMAGE_API", "ImageMagick is disabled in settings.inc.php");
				$this->errors = true;
			}
			
			if (!is_dir($c["imagemagickpath"])) {
				nxlog("NX_IMAGE_API", "The path to your ImageMagick installation seems to be wrong");
				$this->errors = true;
			}
			
			if ($sourcefile == "" && $destfile != "") {
				
				$this->destfile = $destfile;
				$this->width = $width;
				$this->height = $height;
				$this->bgcolor = $bgcolor;
				
				$fileparts = explode(".", basename(strval($destfile)));
				$suffix = $fileparts[count($fileparts) - 1];
				
				$this->tempfile = $c["path"]."cache/nximage/".makeUniqueFilename($c["path"]."cache/nximage/", basename(strval($this->destfile), ".".$suffix), $this->workingsuffix);
				
			} else if ($sourcefile != "") {
			
				if (!file_exists($sourcefile)) {
					nxlog("NX_IMAGE_API", "The specified sourcefile '$sourcefile' does not exist !");
					$this->errors = true;
				} else {
					$this->sourcefile = $sourcefile;
					$this->destfile = (($destfile == "") ? $sourcefile : $destfile);
					
					$fileparts = explode(".", basename(strval($this->destfile)));
					$suffix = $fileparts[count($fileparts) - 1];
					
					$this->tempfile = $c["path"]."cache/nximage/".makeUniqueFilename($c["path"]."cache/nximage/", basename(strval($this->destfile), ".".$suffix), $this->workingsuffix);
					$this->_execute("convert $this->sourcefile $this->tempfile");
				}
				
			} else {
				nxlog("NX_IMAGE_API", "Neither sourcefile nor destfile specified. Need to specify a least one !");
				$this->errors = true;
			}
			
			$this->suffix = $suffix;
			
		}
		
		/**
		 * get path to a new TempFile. This will be automatically deleted when saving.
		 */
		function newTempFile() {
			global $c;
			$fileparts = explode(".", basename(strval($this->tempfile)));
			$suffix = $fileparts[count($fileparts) - 1];
			$filename = $c["path"]."cache/nximage/".makeUniqueFilename($c["path"]."cache/nximage/", basename(strval($this->tempfile), ".".$suffix), $this->workingsuffix);
			array_push($this->tempfiles, $filename);
			copy($this->tempfile, $filename);
			return $filename;
		}
		
		function copy($destpath) {
			return new NXImageApi($this->newTempFile(), $destpath, $this->debug);
		}
		
		function createCanvas($width, $height, $bgcolor) {
			$this->_execute("convert -size ".$width."x".$height." xc:$bgcolor $this->tempfile");
			// doing this twice because of a bug in ImageMagick causing newly created files to contain just rubbish.
			// $this->_execute("convert -size ".$width."x".$height." xc:$bgcolor $this->tempfile");
		}
		
		function floodFill($fillcolor) {
			$this->_execute("convert $this->tempfile -matte -fill $fillcolor -draw \"color 0,0 reset\" $this->tempfile");
		}
		
		function drawText($text, $xpos=0, $ypos=0) {
			$gravity = (isset($this->gravity) ? "-gravity ".$this->gravity : "");
			$fillcolor = (isset($this->fillcolor) ? "-fill ".$this->fillcolor : "");
			
			$command = "convert $this->tempfile $gravity -channel RGBA -font $this->font -pointsize $this->fontsize $fillcolor -draw \"text ".$xpos.",".$ypos." '$text'\" $this->tempfile";
			$this->_execute($command);
		}
		
		/**
		 * resize an image to fit a given size in pixels with constrained proportions
		 * @param integer maximum width in pixels
		 * @param integer maximum height in pixels
		 * @param optional boolean set to true to allow image to grow
		 * @param optional string filter used to resize (grow) the image. Choose from Cubic, Quadratic, Gaussian, Bessel, Hamming, Mitchell, Triangle, Lanczos (default), Sinc, Catrom, Hermite, Hanning, Blackman, Point, Box
		 */
		function resizeAbsolute($maxwidth, $maxheight, $allowgrow=false, $filter="") {
			$grow = ($allowgrow ? "" : ">");
			$filter = (($filter == "") ? "" : "-filter $filter");
			$command = "convert ".$this->_gravity()." $filter -resize \"".intval($maxwidth)."x".intval($maxheight).$grow."\" $this->tempfile $this->tempfile"; 
			$this->_execute($command);
		}
		
		/**
		 * resize an image to a defined width (constrain proportions)
		 * @param integer width
		 * @param optional boolean set to true to allow image to grow
		 * @param optional string filter used to resize (grow) the image. Choose from Cubic, Quadratic, Gaussian, Bessel, Hamming, Mitchell, Triangle, Lanczos (default), Sinc, Catrom, Hermite, Hanning, Blackman, Point, Box
		 */
		function resizeToWidth($width, $allowgrow=false, $filter="") {
			$grow = ($allowgrow ? "" : ">");
			$filter = (($filter == "") ? "" : "-filter $filter");
			$this->_execute("convert ".$this->_gravity()." $filter -resize \"".$width."\" $this->tempfile $this->tempfile");
		}
		
		/**
		 * resize an image to a defined height (constrain proportions)
		 * @param integer height
		 * @param optional boolean set to true to allow image to grow
		 * @param optional string filter used to resize (grow) the image. Choose from Cubic, Quadratic, Gaussian, Bessel, Hamming, Mitchell, Triangle, Lanczos (default), Sinc, Catrom, Hermite, Hanning, Blackman, Point, Box
		 */
		function resizeToHeight($height, $allowgrow=false, $filter="") {
			$grow = ($allowgrow ? "" : ">");
			$filter = (($filter == "") ? "" : "-filter $filter");
			$this->_execute("convert ".$this->_gravity()." $filter -resize \"x".$height."\" $this->tempfile $this->tempfile");			
		}
		
		/**
		 * resize an image to fit an exact area and crop overlapping parts (constrained proportions) 
		 * @param integer exact width in pixels
		 * @param integer exact height in pixels
		 * @param optional boolean set to true to allow image to grow
		 * @param optional string filter used to resize (grow) the image. Choose from Cubic, Quadratic, Gaussian, Bessel, Hamming, Mitchell, Triangle, Lanczos (default), Sinc, Catrom, Hermite, Hanning, Blackman, Point, Box
		 */
		function resizeCrop($width, $height, $allowgrow=false, $filter="") {
			$grow = ($allowgrow ? "" : ">");
			$filter = (($filter == "") ? "" : "-filter $filter");
			if ($allowgrow) {
				if ($this->width() < $width) {

					$this->resizeToWidth($width, true, $filter);

					if ($this->height() < $height) {

						$this->resizeToHeight($height, true, $filter);
					}
				
				} else if ($this->height() < $height) {

					$this->resizeToHeight($height, true, $filter);
				} else if ($this->width() > $width || $this->height() > $height) {

						if (($this->width() * $height / $this->height()) > $width) {
							$this->resizeToHeight($height, true, $filter);
						} else {
							$this->resizeToWidth($width, true, $filter);
							
						}
				}
			} else {
			
			}
			
			// image now has correct minimum measures. go cropping !
			$this->crop($width, $height);
				
		}
		
		/**
		 * resize only content of an image keeping original canvas dimensions. "empty" parts will have $bgcolor. (constrained proportions)
		 * @param integer delta number of pixels to grow or shrink
		 * @param string background-color
		 * @param optional boolean set to true to allow image to grow
		 * @param optional string filter used to resize (grow) the image. Choose from Cubic, Quadratic, Gaussian, Bessel, Hamming, Mitchell, Triangle, Lanczos (default), Sinc, Catrom, Hermite, Hanning, Blackman, Point, Box		 
		 */
		function resizeContent($delta, $bgcolor, $allowgrow=false, $filter="") {
			$tmp = new NXImageApi($this->tempfile, $this->tempfile, $this->debug);
			$this->createCanvas($this->width(), $this->height(), $bgcolor);
			
			$tmp->resizeAbsolute($tmp->width() + $delta, $tmp->height() + $delta, $allowgrow, $filter);
			
			$gravity = (isset($this->gravity)) ? " -gravity $this->gravity " : "";
			
			$this->_execute("composite $gravity -compose Over $tmp->tempfile $this->tempfile $this->tempfile");
			
			$tmp->deltemp();
			
		}
		
		/**
		 * move content of an image
		 * @param string deltaX to move
		 * @param string deltaY to move
		 * @param string background-color
		 */
		function moveContent($deltaX, $deltaY, $bgcolor) {
			$tmp = $this->newTempFile();
			$this->createCanvas($this->width(), $this->height(), $bgcolor);
			$this->_execute("composite -geometry ".$deltaX.$deltaY." $tmp $this->tempfile $this->tempfile");
		}
		
		/**
		 * resize image canvas
		 * @param integer width new canvas width
		 * @param integer height new canvas height
		 * @param string background-color
		 */ 
		function resizeCanvas($width, $height, $bgcolor) {
			$tmp = $this->newTempFile();
			$this->createCanvas($width, $height, $bgcolor);
			$gravity = (isset($this->gravity)) ? " -gravity $this->gravity " : "";
			$this->_execute("composite $gravity -compose Over $tmp $this->tempfile $this->tempfile");
		}
		
		/**
		 * crop image to a given width and height.
		 * @param integer width
		 * @param integer height
		 * @param string x-offset
		 * @param string y-offset
		 */
		function crop($width, $height, $xoff="", $yoff="") {
			$offset = ($xoff.$yoff == "") ? "+0x+0" : $xoff."x".$yoff;
			$this->_execute("convert ".$this->_gravity()." -crop \"".$width."x".$height.$offset."\" ".$this->tempfile." ".$this->tempfile);
		}
		
		/**
		 * resize an image to fit a given percentual size
		 * @param integer x-scaling factor in percent
		 * @param integer (optional) y-scaling factor in percent. If this is 0 (default) the aspect ratio will be maintained
		 * @param string (optional) filter name used to resize (grow) the image. Choose from Cubic, Quadratig, Gaussian, Bessel, Hamming, Mitchell, Triangle, Lanczos (default), Sinc, Catrom, Hermite, Hanning, Blackman, Point, Box
		 */
		function resizeRelative($xscale, $yscale=0, $filter="") {
			$filter = (($filter == "") ? "" : "-filter $filter");
			$xscale = intval($xscale);
			$yscale = intval($yscale);
			if ($yscale == 0)
				$yscale = $xscale;
			$command = "convert  $filter -resize \"".$xscale."%x".$yscale."%".$grow."\" $this->tempfile $this->tempfile";
			$this->_execute($command);
		}
		
		/**
		 * apply an alpha mask on an image.
		 * @param string path to mask image file
		 * @param string background color (transparent default)
		 */
		function alphaMask($path) {
			$gravity = (isset($this->gravity)) ? " -gravity ".$this->gravity : "";

			$this->_execute("composite $gravity -compose CopyOpacity \"$path\" \"$this->tempfile\" \"$this->tempfile\"");
		}
		
		function extractAlpha() {
			$this->_execute("convert   $this->tempfile   -channel matte -separate  +matte $this->tempfile");
		}
		
		function opaquify($bgcolor) {
			$tmp = $this->newTempFile();
			$this->createCanvas($this->width(), $this->height(), $bgcolor);
			$this->_execute("composite $tmp $this->tempfile $this->tempfile");
		}
		
		/**
		 * Smooth out the image and create a fuzz by using gaussian algorithm
		 * You should use blur wherever possible because it is much faster.
		 * Blurs Transparency to greyscale !
		 * @param integer radius
		 * @param double sigma
		 */
		function gaussian($radius, $sigma) {
			$radius = intval($radius);
			$sigma = doubleval($sigma);
			$command = "convert   $this->tempfile -gaussian $radiusx$sigma $this->tempfile";
			$this->_execute($command);
		}
		
		/**
		 * Smooth out the image and create a fuzz by using (gaussian) blur algorithm.
		 * This is faster than gaussian and should be used instead wherever possible.
		 * Blurs Transparency to greyscale !
		 * @param integer radius
		 * @param double sigma
		 */
		function blur($radius, $sigma) {
			$radius = intval($radius);
			$sigma = doubleval($sigma);
			$command = "convert  $this->tempfile -blur ".$radius."x".$sigma." $this->tempfile";
			$this->_execute($command);
		}
		
		/**
		 * Not yet ready for use !!!
		 * Smooth out the image and create a fuzz by using gaussian algorithm and advanced filtering to preserve original colors when blurring
		 * @param integer radius
		 * @param double sigma
		 */
		function __feather($radius, $sigma) {
			global $c;
			
			$fileparts = explode(".", basename(strval($this->tempfile)));
			$suffix = $fileparts[count($fileparts) - 1];
			
			$fade_init = $c["path"]."cache/nximage/".makeUniqueFilename($c["path"]."cache/nximage/", basename(strval($this->tempfile), ".".$suffix), "png");
			$fp[1] = fopen($fade_init, "w");
			$fade_mask = $c["path"]."cache/nximage/".makeUniqueFilename($c["path"]."cache/nximage/", basename(strval($fade_init), ".png"), "png");
			$fp[2] = fopen($fade_mask, "w");
			$fade_work_1a = $c["path"]."cache/nximage/".makeUniqueFilename($c["path"]."cache/nximage/", basename(strval($fade_init), ".png"), "png");
			$fp[3] = fopen($fade_work_1a, "w");
			$fade_work_1b = $c["path"]."cache/nximage/".makeUniqueFilename($c["path"]."cache/nximage/", basename(strval($fade_init), ".png"), "png");
			$fp[4] = fopen($fade_work_1b, "w");
			$fade_work_2 = $c["path"]."cache/nximage/".makeUniqueFilename($c["path"]."cache/nximage/", basename(strval($fade_init), ".png"), "png");
			$fp[5] = fopen($fade_work_2, "w");
			$fadeout = $c["path"]."cache/nximage/".makeUniqueFilename($c["path"]."cache/nximage/", basename(strval($fade_init), ".png"), "png");			
			$fp[6] = fopen($fadeout, "w");
			
			foreach ($fp as $pointer) {
				fclose($pointer);
			}
			
			$this->_execute("convert   $this->tempfile $fade_init");
			
			# extract the mask of the this image
			$this->_execute("convert   $fade_init -channel matte -negate -separate $fade_mask");

			# spread image (border will become 50% transparent)
			$this->_execute("convert   $fade_mask  -gaussian ".$radius."x".$sigma." +matte $fade_work_1a");

			# Use multiply to limit the spread to original boarders of image
			$this->_execute("composite   -compose Multiply $fade_mask $fade_work_1a $fade_work_1b");

			# Now repeat.  Spreading then masking the image mask, a number of times.
			$this->_execute("convert $fade_work_1b -gaussian ".$radius."x".$sigma." +matte $fade_work_2");
			$this->_execute("composite   -compose Multiply $fade_mask $fade_work_2 $fade_work_2");
			
			for ($i=2; $i<($sigma-1); $i++) {
				$this->_execute("convert   $fade_work_2 -gaussian ".$radius."x".($sigma-$i)." +matte $fade_work_2");
				$this->_execute("composite   -compose Multiply $fade_mask $fade_work_2 $fade_work_2");
			}
			
			# When satisfied,  re-add the faded mask into original image
			$this->_execute("composite   -compose CopyOpacity $fade_work_2 $fade_init $fadeout");
			
			$this->_execute("convert   $fadeout $this->tempfile");		
		}
		
		function __landscape() {
			global $c;
			var_dump( exec($c["imagemagickpath"]."identify c:/web/kdlahr/nx2004/newimage.png", $output, $retval) );
			var_dump($output);
			var_dump($retval);
		}
		
		function __portrait() {
			
		}
		
		function width() {
			$width = $this->_execute("identify   -format %w ".$this->tempfile, true);
			return $width[0];
		}
		
		function height() {
			$height = $this->_execute("identify -format %h ".$this->tempfile, true);
			return $height[0];			
		}
		
		/**
		 * write modified tempfile back to destination file
		 */
		function save() {
			// $this->_execute("convert $this->tempfile $this->destfile");
			$this->_execute("convert ".$this->_quality()." $this->tempfile $this->destfile");
			// nxCopy($this->destfile, dirname($this->destfile)."/", basename($this->destfile));
			if ($this->debug)
				echo "NX_IMAGE_API saving: ".$this->tempfile." to ".$this->destfile." -- <br>\n";		
			// doing this twice because of a bug in ImageMagick causing newly created files to contain just rubbish.
			// $this->_execute("convert $this->tempfile $this->destfile");
			$this->deltemp();
		}
		
		/**
		 * delete temporary files
		 */
		function deltemp() {
			if (!$this->debug && !$this->errors) {
				unlink($this->tempfile);
				foreach ($this->tempfiles as $tmpfile) {
					unlink($tmpfile);
				}				
			}
		}
		
		/**
		 * returns gravity string (internally used only)
		 */
		function _gravity() {
			return (isset($this->gravity)) ? " -gravity ".$this->gravity : "";
		}
		
		/**
		 * returns gravity string (internally used only)
		 */
		function _quality() {
			return (isset($this->quality)) ? " -quality ".$this->quality : "";
		}
		
		function _geometry() {
			return (isset($this->geometry)) ? " -geometry ".$this->geometry : "";
		}
		
		/**
		 * execute command... internally used only !
		 * set returnExpected to true if you expect ImageMagick to output something. Otherwise ImageMagick-Output will be logged as error
		 */
		function _execute($command, $returnExpected = false) {
			global $c;

			if ($this->debug)
				echo "NX_IMAGE_API executing: ".$command." -- <br>\n";			
			
/*
			$output = "";	
			$ph = popen($command,"r") or die("unable to run ImageMagick-command");
			while (! feof($ph)) {
				$s = fgets($ph,1048576);
				$output .= $s." | ";
			}
			pclose($ph);	
*/
			exec($c["imagemagickpath"].$command, $returnvalue);
			
			if ($returnExpected) {
				return $returnvalue;
			}
			
			if ((strlen($returnvalue[0]) > 0) && !$returnExpected) {
				
				nxlog("NX_IMAGE_API", "Error during execution of \"".$command."\" said \"".$returnvalue[0]."\"");
			}
		}
		
		
	}
	
?>