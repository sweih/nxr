<?php

 /**
  * Office Import functions
  * @package Tools
  * @subpackage Import
  */
  
/**********************************************************************
 *	Parts of this file have been developed by
 * 	Robert Lemke <rl@robertlemke.com>
 * 	Kasper Skårhøj <kasper@typo3.com>
 *	for Typo3 (www.typo3.com)
 *	These parts of sourcecode are Copyright (C) 2003 by Robert Lemke or Kaspar Skårhøj
 *	and were released under GNU GPL.
 *
 *	N/X - Web Content Management System
 *
 *	Copyright 2004 Sven Weih
 *
 *      $Id: class.openoffice.php,v 1.2 2004/04/02 19:41:40 sven_weih Exp $ *
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

// Original copyright notice for typo3 script.
/***************************************************************
*  Copyright notice
*  
*  (c) 2003 Robert Lemke (rl@robertlemke.com)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is 
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
* 
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
* 
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


  /**
   * Class for importing Open Office files to N/X
   * Some Ideas and parts of the source are based on the Typo3 Office Import Plugin which
   * was developed by Robert Lemke and Kasper Skårhøj. Refer the typo3 extension for copyright information.
   */
   class ImportOpenOffice extends FileManipulation {
   
     var $type;
     var $mapOOtoCommon = array(
			// Bodytext formats
		"Text body" => 'paragraph',							// "Text body"
		"Text body indent" => 'indented',					// "Text body indent"
		"Heading" => 'heading1',								// "Heading"
		"Preformatted Text" => 'preformatted',				// "Preformatted Text" (HTML-menu)
		"First line indent" => 'firstLineIndent',			// "First line indent"
		"Hanging indent" => 'hangingIndent',				// "Hanging indent"
		"Salutation" => 'paragraph',							// "Complimentary close"
		"List Indent" => 'paragraph',							// "List Indent"
		"Marginalia" => 'paragraph',							// "Marginalia"
		"Signature" => 'paragraph',							// "Signature"
		"Standard" => 'paragraph',								// "Default"

			// Headers:
		"Heading 1" => 'heading1',								// Heading 1
		"Heading 2" => 'heading2',								// Heading 2
		"Heading 3" => 'heading3',								// Heading 3
		"Heading 4" => 'heading4',								// Heading 4
		"Heading 5" => 'heading5',								// Heading 5
		"Heading 6" => 'heading6',								// Heading 6
		"Heading 7" => 'heading7',								// Heading 7
		"Heading 8" => 'heading8',								// Heading 8
		"Heading 9" => 'heading9',								// Heading 9
		"Heading 10" => 'heading10',							// Heading 10

			// DEFAULT (non-rendered)
		"_default" => 'paragraph',								// [everything else...]
	);
	
	var $officeConf = array (
		'imageCObject_scaledImage.' => array (
			'file.'=> array (
				'width' => 100,
				'import' => array (
					'current' => 1,
				),
			),
			'imageLinkWrap' => 1,
			'imageLinkWrap.' => array (
				'width' => 800,
				'JSwindow' => 1,
				'enable' => 1,
			),					
			'wrap' => '<div style=>"text-align:center; margin-bottom: 10px;"> | </div>',
		),
		'tagWraps.' => array (
			'heading1' => '<h1> | </h1>',
			'heading2' => '<h2> | </h2>',
			'heading3' => '<h3> | </h3>',
			'heading4' => '<h4> | </h4>',
			'heading5' => '<h5> | </h5>',
			'paragraph' => '<p> | </p>',
			'bold' => '<strong> | </strong>',
			'italic' => '<em> | </em>',
			'underlined' => '<span style="text-decoration: underline;"> | </span>',
			'unorderedlist' => '<ul> | </ul>',
			'listitem' => '<li> | </li>',
			'superscript' => '<sup> | </sup>',
			'subscript' => '<sub> | </sub>',
			'preformatted' => '<pre> | </pre>',
			'indented' => '<blockquote> | </blockquote>',
			'firstLineIndent' => '<p> | </p>',
		),
		'parseOptions.' => array (
			'renderMicrosoftSmartTags' => 1,
			'renderColors' => 1,
			'renderBackgroundColors' => 1,
			'renderFontFaces' => 1,
		)
	);
	
	
	var $designConf = array(
		'tableParams' => 'cellspacing="0"'
	);
	
	var $cssBaseClass = "";	
	var $officeBody=array();
	var $officeStyles=array();
	var $parsedStyles=array();	
	var $unzipObj;
	var $tmpFolder="";
     
     /**
      * Standard constructor
      * @param string Path to file on harddisk.
      */
     function ImportOpenOffice($filename) {
       FileManipulation::FileManipulation($filename);	
       $this->setFileType();
     }
     
     /**
      * Return the office document as a html string
      */
     function getParsedContent() {
     	if ($this->type == "oowrite") {
     		return $this->parseOOWriter();
     	} else if ($this->type == "oocalc") {
	  	return $this->parseOOCalc();     		
     	}
     	return "";
     }
     
     
     
     /**
      * parse document as OpenOffice Writer
      */
     function parseOOWriter() {
     		// unzip the file.
     		global $c;
     		mt_srand ((double)microtime()*1000000);
  	        $this->tmpFolder =  $c['path']."cache/".md5(uniqid(mt_rand()));
  	        mkdir($this->tmpFolder);	        		
     		nxunzip($this->filename, $this->tmpFolder."/");
     		$this->filename = $this->tmpFolder."/content.xml";
     		$this->loadFile();
		if ($this->filecontent)	{
			$vals=array();
			$index=array();
			$p = xml_parser_create();
			xml_parse_into_struct($p,$this->filecontent,$vals,$index);
			xml_parser_free($p);
	
			$this->officeStyles = $this->indentSubTagsRec(array_slice($vals,$index['OFFICE:AUTOMATIC-STYLES'][0]+1,$index['OFFICE:AUTOMATIC-STYLES'][1]-$index['OFFICE:AUTOMATIC-STYLES'][0]-1),2);
			$this->prepareStyles();
			$this->officeBody = array_slice($vals,$index['OFFICE:BODY'][0]+1,$index['OFFICE:BODY'][1]-$index['OFFICE:BODY'][0]-1);
			$this->officeBody = $this->indentSubTagsRec($this->officeBody,1);
			$res = $this->renderOOBody($this->officeBody);
			nxRMDir($this->tmpFolder);	
			return implode(chr(10),$res);
		} 	
     }
     
     
     /**
      * parse document as OpenOffice Calc
      */
     function parseOOCalc() {
     	
     }
     
     /**
      * Checks, if the file is valid excel or word file. Sets type-class-variable
      */
      function setFileType() {
      	$this->filetype == "oowrite";
      	/**
      	if ($this->extension == 'sxw')  {
	  $this->type = "oowrite";
	} elseif ($this->extension == 'sxc')	{
	  $this->type = "oocalc";
	} else {
	  $this->type = "undefined";
	}*/
      }
      
      	/**
	 * This prepares the automatic styles from the document
	 * 
	 * @return	[type]		...
	 */
	function prepareStyles()	{
		reset($this->officeStyles);
		while(list($k,$v)=each($this->officeStyles))	{
			$v['_wrap']=array();
			if ($v['attributes']['STYLE:PARENT-STYLE-NAME'])	{
				$v['_stylepointer']=$v['attributes']['STYLE:PARENT-STYLE-NAME'];
				$v['_wrap']=explode ('|',$this->officeConf['tagWraps.'][$this->mapOOtoCommon[$v['_stylepointer']]]);
					// No matching style was found in the mapOOtoCommon array, so try to apply a custom style:
				if (count ($v['_wrap']) < 2) {
					$v['_wrap']=explode ('|',$this->officeConf['tagWraps.'][strtolower($v['_stylepointer'])]);
				}
				if (count ($v['_wrap']) < 2) {
				$v['_wrap']=explode ('|',$this->officeConf['tagWraps.'][$this->mapOOtoCommon['_default']]);
				}
				
			}
			if ($v['subTags'][0]['tag']=='STYLE:PROPERTIES')	{
				$styleProp = $v['subTags'][0]['attributes'];
				$cssP=array();
					
					
				// ***********************
				// HERE we try to use regular HTML B/I/U tags for bold, italic and underline. Alternatively these could be rendered with style='' attributes OR with strong/em
				// ***********************
				
					// Bold:
				if ($styleProp['FO:FONT-WEIGHT'])	{
					if ($styleProp['FO:FONT-WEIGHT']=='bold')	{
						$v['_wrap'][0].= $this->getWrapPart ($this->officeConf['tagWraps.']['bold'],0);
						$v['_wrap'][1]= $this->getWrapPart ($this->officeConf['tagWraps.']['bold'],1).$v['_wrap'][1];
					} else {
						$cssP[]='font-style: '.$styleProp['FO:FONT-WEIGHT'].';';
					}
				}
					// Italic:
				if ($styleProp['FO:FONT-STYLE'])	{
					if ($styleProp['FO:FONT-STYLE']=='italic')	{
						$v['_wrap'][0].=$this->getWrapPart ($this->officeConf['tagWraps.']['italic'],0);
						$v['_wrap'][1]=$this->getWrapPart ($this->officeConf['tagWraps.']['italic'],1).$v['_wrap'][1];
					} else {
						$cssP[]='font-style: '.$styleProp['FO:FONT-STYLE'].';';
					}
				}
					// Underline:
				if ($styleProp['STYLE:TEXT-UNDERLINE'])	{
					if ($styleProp['STYLE:TEXT-UNDERLINE']=='single')	{
						$v['_wrap'][0].=$this->getWrapPart ($this->officeConf['tagWraps.']['underlined'],0);
						$v['_wrap'][1]=$this->getWrapPart ($this->officeConf['tagWraps.']['underlined'],1).$v['_wrap'][1];
					} else {
						$cssP[]='text-decoration: '.$styleProp['STYLE:TEXT-UNDERLINE'].';';
					}
				}


				// ***********************				
				// style='' attributes
				// ***********************
					// Background color
				if ($styleProp['STYLE:TEXT-BACKGROUND-COLOR'])	{
					$cssP[]='background-color: '.$styleProp['STYLE:TEXT-BACKGROUND-COLOR'].';';
				}
					// Background color
				if ($styleProp['FO:BACKGROUND-COLOR'])	{
					$cssP[]='background-color: '.$styleProp['FO:BACKGROUND-COLOR'].';';
				}
					// color
				if ($styleProp['FO:COLOR'])	{
					$cssP[]='color: '.$styleProp['FO:COLOR'].';';
				}

				if (count($cssP))	{
					$v['_wrap'][0].='<span style="'.implode('',$cssP).'">';
					$v['_wrap'][1]='</span>'.$v['_wrap'][1];
				}
			}
			$this->parsedStyles[$v['attributes']['STYLE:NAME']]	= $v;
		}
	}
	
	/**
	 * Traversing the bodyArray and outputting all known elements
	 * 
	 * @param	[type]		$bodyArray: ...
	 * @return	[type]		...
	 */
	function renderOOBody($bodyArray)	{
		reset($bodyArray);
		$HTML_code=array();
		while(list($k,$v)=each($bodyArray))	{			
			switch((string)$v['tag'])	{
				case 'TEXT:P':
				case 'TEXT:H':
					$sN = $v['attributes']['TEXT:STYLE-NAME'];
 					if (stristr(',P,', ','.substr($sN,0,1).',') && (! is_int(substr($sN,1)))) { 						
 						$wrap = $this->parsedStyles[$sN]['_wrap']; 						
					} else {						
						if ($this->mapOOtoCommon[$sN] != "") { 					
							$wrap = explode ('|',$this->officeConf['tagWraps.'][$this->mapOOtoCommon[$sN]]);
							
						} else {		// there is no style which could be mapped ...
							
							if ($this->officeConf['tagWraps.'][strtolower($sN)]) {
								$wrap = explode ('|',$this->officeConf['tagWraps.'][strtolower($sN)]);
							} else {		// no, but really no matching style found. So apply the default one.
								$wrap = explode ('|',$this->officeConf['tagWraps.'][$this->mapOOtoCommon['_default']]);
							}
						}
					}
					if (count($wrap)>1)	{
						$this->chr10BR=$wrap[2];
						$HTML_code[]=$wrap[0].$this->getParagraphContent($v).$wrap[1];
						$this->chr10BR=0;
					} else $this->noProcessing($v);
				break;
				case 'TEXT:UNORDERED-LIST':
				case 'TEXT:ORDERED-LIST':
					if (is_array($v['subTags']))	{
						$tempArr=array();
						$listItems=$this->indentSubTagsRec($v['subTags'],2);
						
						reset($listItems);
						while(list($kk,$vv)=each($listItems))	{
							if ($vv['tag']=='TEXT:LIST-ITEM' && is_array($vv['subTags']))	{
								$tempArr[]='<li>'.implode(chr(10),$this->renderOOBody($vv['subTags'])).'</li>';
							} elseif ($vv['tag']=='TEXT:LIST-HEADER' && is_array($vv['subTags'])) {
								$tempArr[]=implode(chr(10),$this->renderOOBody($vv['subTags']));
							} else $this->noProcessing($vv);
						}
						$lT = $v['tag']=='TEXT:ORDERED-LIST' ? 'ol' : 'ul';
						$HTML_code[]='<'.$lT.'>'.implode(chr(10),$tempArr).'</'.$lT.'>';
					} else $this->noProcessing($v);
				break;
				case 'TABLE:TABLE':
				case 'TABLE:SUB-TABLE':
					if (is_array($v['subTags']))	{
						$tableItems=$this->indentSubTagsRec($v['subTags'],1);

						$tableRows=array();
						$tableHeadRows=array();
						
						$columnCount=0;
						reset($tableItems);					
						while(list($kk,$vv)=each($tableItems))	{
							if ($vv['tag']=='TABLE:TABLE-COLUMN')	$columnCount++;
							if ($vv['tag']=='TABLE:TABLE-HEADER-ROWS')	{
								$HRrows = $this->indentSubTagsRec($vv['subTags'],1);
								reset($HRrows);
								while(list(,$vvv)=each($HRrows))	{
									$tableHeadRows[]='<tr>'.$this->tableRow($vvv['subTags'],'th').'</tr>';
								}
							}
							if ($vv['tag']=='TABLE:TABLE-ROW')	{
								$tableRows[]='<tr>'.$this->tableRow($vv['subTags']).'</tr>';
							}
						}						
						$HTML_code[]='<table '.$this->designConf['tableParams'].'>'.
							(count($tableHeadRows) ? '<thead>'.implode(chr(10),$tableHeadRows).'</thead>' : '').
							'<tbody>'.implode(chr(10),$tableRows).'</tbody>'.
							'</table>';
					} else $this->noProcessing($v);

				break;
					// Non-rendered / processed elements:
				case 'TEXT:SEQUENCE-DECLS':
				case 'TEXT:TABLE-OF-CONTENT':
				break;
				
				default:
					$HTML_code[]='<p>NOT RENDERED: <em>'.$v['tag'].'</em></p>';
				break;
			}
		}
		return $HTML_code;
	}
	
	/**
	 * Processing of table rows
	 * 
	 * @param	[type]		$subTags: ...
	 * @param	[type]		$tagName: ...
	 * @return	[type]		...
	 */
	function tableRow($subTags,$tagName='td')	{
		$cells = $this->indentSubTagsRec($subTags,2);
		reset($cells);
		$cellOutput=array();
		while(list($k,$v)=each($cells))	{
			if ($v['tag']!='TABLE:COVERED-TABLE-CELL')	{
				$content = implode(chr(10),$this->renderOOBody($v['subTags']));
				$cellOutput[]='<'.$tagName.($v['attributes']['TABLE:NUMBER-COLUMNS-SPANNED']>1?' colspan="'.$v['attributes']['TABLE:NUMBER-COLUMNS-SPANNED'].'"':'').'>'.
					$content.
					'</'.$tagName.'>';
			}
		}
		return implode('',$cellOutput);
	}	

	/**
	 * This processed the content inside a paragraph or header.
	 * 
	 * @param	[type]		$v: ...
	 * @return	[type]		...
	 */
	function getParagraphContent($v)	{
		$content='';
		$content.=$this->pValue($v['value']);

		if (is_array($v['subTags']))	{
			$v['subTags'] = $this->indentSubTags($v['subTags']);

			reset($v['subTags']);
			while(list(,$subV)=each($v['subTags']))	{
				switch($subV['tag'])	{
						// Paragraph/Headers
					case 'TEXT:P':
					case 'TEXT:H':

						// Fields:
					case 'TEXT:AUTHOR-NAME':
					case 'TEXT:TITLE':
					case 'TEXT:USER-DEFINED':
						if (strstr(',complete,cdata,',','.$subV['type'].','))	{
							$content.=$this->pValue($subV['value']);
						} else $this->noProcessing($subV);
					break;
					case 'TEXT:S':
						// Extra SPACE!
						if ($subV['attributes']['TEXT:C'] > 1 && $subV['attributes']['TEXT:C'] < 2000000000) {
							$cc = $subV['attributes']['TEXT:C'];
						} else {						
							$cc = 1;
						}
						for ($a=0; $a<$cc; $a++)	{
							$content.='&nbsp;';
						}
					break;
					case 'TEXT:TAB-STOP':
						$content.=$this->chr10BR?chr(9):'&nbsp;&nbsp;&nbsp;&nbsp;';
					break;
					case 'TEXT:LINE-BREAK':
						$content.=$this->chr10BR?chr(10):'<br />';
					break;
					case 'TEXT:SPAN':					
					if (strstr(',complete,cdata,',','.$subV['type'].','))	{
							$content.=$this->spanFormat($this->getParagraphContent($subV),$subV['attributes']['TEXT:STYLE-NAME']);
						} else $this->noProcessing($subV);
					break;
					case 'TEXT:A':
						if (strstr(',complete,cdata,',','.$subV['type'].','))	{
							$content.='<a href="'.$subV['attributes']['XLINK:HREF'].'">'.$this->getParagraphContent($subV).'</a>';
						} else $this->noProcessing($subV);
					break;
					case 'DRAW:IMAGE':
						if ($subV['attributes']['XLINK:HREF'])	{
							$fI = pathinfo($subV['attributes']['XLINK:HREF']);														
							$imageId = createImageFromFile($this->tmpFolder."/Pictures/".$fI["basename"]);
							$content.="{NX:IMAGE ID=\"$imageId\"}";						
						}
					break;
					case 'DRAW:TEXT-BOX';
						$content.='<table border="0" cellpadding="0" cellspacing="0"><tr><td>'.implode('',$this->renderOOBody($this->indentSubTags($subV['subTags']))).'</td></tr></table>';
					break;
						// Nothing happens:
					case 'TEXT:SEQUENCE':
					case 'OFFICE:ANNOTATION':
					break;
					default:
						$this->noProcessing($subV);
					break;
				}
			}
		}
		if (!strcmp(trim(strip_tags($content,'<img>')),''))	$content='&nbsp;';
		return $content;
	}
	
	/**
	 * Wrapping spans in the code they need.
	 * 
	 * @param	[type]		$value: ...
	 * @param	[type]		$style: ...
	 * @return	[type]		...
	 */
	function spanFormat($value,$style)	{
		$wrap = $this->parsedStyles[$style]['_wrap'];
		if (is_array($wrap) && count($wrap)>1)	{
			return $wrap[0].$value.$wrap[1];
		} else {
			$this->noProcessing(array('STYLE'=>$style));
			return $value;
		}
	}
	
	/**
	 * @param	[type]		$v: ...
	 * @return	[type]		...
	 */
	function noProcessing($v)	{
	}
     
   
   }
   
?>