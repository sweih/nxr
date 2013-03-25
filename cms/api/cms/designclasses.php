<?php
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002- 2006 Sven Weih, FZI Research Center for Information Technologies
	 *	www.fzi.de
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
	 * Returns a list of all design class in a folder
	 *
	 * @param string $folder folder where to start the search.	 
	 */
	function getDCFileList ($folder) {
		global $c, $lang;
		$ret = array ();
		$ret[] = array($lang->get('no_design', 'No design selected'), '');
		$dirhandle = opendir($c["basepath"].$folder.'/');
		while (false !== ($fname = readdir ($dirhandle))):
		  $pgn_fname = $fname."/dc_".$fname.".php";
		  if (is_dir ($c["basepath"].$folder.'/'.$fname) && file_exists($c["basepath"].$folder.'/'.$pgn_fname)):		
		    $ref = createDCRef($c["basepath"].$folder.'/'.$pgn_fname);
		    $ret[] = array($ref->getName(), $pgn_fname );
		    unset($ref);
		  endif;
		endwhile;
		closedir($dirhandle);
		return $ret;
	}
	
	/**
	 * Creates an instance of the class which is located with the classfile
	 *
	 * @param string $classFile Path to the classfile.
	 */
	function createDCRef($classFile) {
		$fcontent = join ('', file ($classFile));		
		$cpos = strpos($fcontent, "class ");
		$epos = strpos($fcontent, "extends AbstractDesign");
		if ($cpos==0 || $epos==0) :
			return false;
		endif;		
		$classname = trim(substr($fcontent, $cpos+6, $epos-$cpos-6));			
		@require_once $classFile;
		$ref = new $classname(); 
		return $ref;
	}

?>