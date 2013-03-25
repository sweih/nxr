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
 *      $Id: class.mso2003.php,v 1.2 2004/04/02 19:41:40 sven_weih Exp $ *
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
   * Class for importing microsoft word or excel files to N/X
   * Some Ideas and parts of the source are based on the Typo3 Office Import Plugin which
   * was developed by Robert Lemke and Kasper Skårhøj. Refer the typo3 extension for copyright information.
   */
   class ImportMSOffice extends FileManipulation {
   
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
     
     	var $type;	
     	var $currentListLevel = 0;		// used for determining the level of ordered and unordered lists
	var $wordWraps = array ();		//	used for wrapping paragraphs and such by several functions
	var $dontAddPTags = 0;			// used by wordRenderParagraph
	var $colsCount = 0;				// used by Excel rendering function
	var $rowsCount = 0;
	var $cssBaseClass = "";	
   	  
     /**
      * Standard constructor
      * @param string Path to file on harddisk.
      */
     function ImportMSOffice($filename) {
     	FileManipulation::FileManipulation($filename);
     	$this->setFileType();     	
     }	
     
     /**
      * Return the office document as a html string
      */
     function getParsedContent() {    	
     	if ($this->type == "word") {
       	  return $this->parseWord2003();	
     	} else if ($this->type == "excel") {
     	  return $this->parseExcel2003();	
     	}
     	return "";
     }
          
     /**
      * Parse the file as MS Word 2003 file and return the content as html string
      * @see getParsedContent
      */
     function parseWord2003() {     	
     	$p = xml_parser_create();
	$vals=array();
	$index=array();
	xml_parse_into_struct($p, $this->filecontent, $vals, $index);
	xml_parser_free($p);
	$explodedSubsection = $this->indentSubTagsRec(array_slice($vals,$index['WX:SECT'][0]+1,$index['WX:SECT'][1]-$index['WX:SECT'][0]-1),999);		
	return $this->wordTraverseSection ($explodedSubsection);
     }
    
       
     
     
     /**
      * Parse the file as MS Excel 2003 file and return the content as html string
      * @see getParsedContent
      */
     function parseExcel2003() {
    	$p = xml_parser_create();
	$vals=array();
	$index=array();
	xml_parse_into_struct($p, $this->filecontent, $vals, $index);
	xml_parser_free($p);
	$explodedSubsection = $this->indentSubTagsRec(array_slice($vals,$index['WX:SECT'][0]+1,$index['WX:SECT'][1]-$index['WX:SECT'][0]-1),999);
	return $this->ExcelTraverseSection($explodedSubsection);		
     }
     
     	// --- WORD FUNCTIONS BELOW ------------------------------------------------------------------------------------

	/**
	 * Traverse the sections. Sub sections lets us go one level deeper and traverse again, paragraphs etc. are
	 * handle by sub functions
	 * 
	 * @param	[array]		$explodedSubsection: Array containing subTags
	 * @return	[string]		HTML content
	 */
	function wordTraverseSection($explodedSubsection)	{
		foreach($explodedSubsection as $value)	{
			$this->wraps = array ();	// clear previous wraps;			
			switch($value['tag'])	{
				case 'WX:SUB-SECTION':	
					$content.=$this->wordTraverseSection($value['subTags']);
				break;			
				case 'W:P':
					$this->dontAddPTags = 0;
					$content.= $this->wordRenderParagraph($value['subTags']);
				break;
				case 'AML:ANNOTATION':
					if ($value['attributes']['W:TYPE'] == 'Word.Bookmark.Start') {
						$content.= '<a name="officeimport'.ereg_replace ("[^a-z:._-]","",strtolower($value['attributes']['W:NAME'])).'"></a>';
					}
				break;
				case 'W:TBL':	
					$content.=$this->wordRenderTable($value['subTags']);
				break;
			}
		}		
		return $content;
	}

	/**
	 * Renders a paragraph
	 * 
	 * @param	[array]		$pArray: subparts
	 * @param	[type]		$tempDontAddPTags: ...
	 * @return	[string]		rendered HTML output
	 */
	function wordRenderParagraph($pArray, $tempDontAddPTags=0)	{
		if (is_array ($pArray)) {
			foreach ($pArray as $paragraph) {
				switch ($paragraph['tag']) {
					case 'W:PPR':
							// PPR mostly contains things elements like lists and such, no actual content.
							//	That's why only wraps are returned
						$this->wraps = $this->wordRenderPPR ($paragraph);						
					break;
					case 'W:R' :
						$content .= $this->wordRenderR ($paragraph);
					break;	
					case 'ST1:CITY' :	
							// Microsoft Smarttag for identifying cities etc.
					   $x = $this->traverseToTag ($paragraph['subTags'], 'W:T');
						if ($this->officeConf['parseOptions.']['renderMicrosoftSmartTags']) {
							$x = '<span style="smarttag-city">'.$x.'</span>';
						}
						$content .= $x;
					break;
					case 'ST1:STREET' :	
							// Microsoft Smarttag for identifying cities etc.
					   $x = $this->traverseToTag ($paragraph['subTags'], 'W:T');
						if ($this->officeConf['parseOptions.']['renderMicrosoftSmartTags']) {
							$x = '<span style="smarttag-street">'.$x.'</span>';
						}
						$content .= $x;
					break;
					case 'W:HLINK' :	
							// A hyperlink or bookmark within the document
						if ($paragraph['attributes']['W:DEST']) {
								$href = $paragraph['attributes']['W:DEST'];
								$value = $this->wordRenderParagraph ($paragraph['subTags'],1);
								$target = $this->officeConf['parseOptions.']['extLinksTarget'] ? ' target="'.$this->officeConf['parseOptions.']['extLinksTarget'].'"' : '';
								$content .= '<a href="'.$href.'"'.$target.'>'.$value.'</a>';
						} elseif ($paragraph['attributes']['W:BOOKMARK']) {
								$href = '#officeimport'.ereg_replace ("[^a-z:._-]","",strtolower($paragraph['attributes']['W:BOOKMARK']));
								$value = $this->wordRenderParagraph ($paragraph['subTags'],1);
								$target = $this->officeConf['parseOptions.']['intLinksTarget'] ? ' target="'.$this->officeConf['parseOptions.']['extLinksTarget'].'"' :'';
								$content .= '<a href="'.$href.'"'.$target.'>'.$value.'</a>';
						}
					break;
					default:
				}	
			}
		}

		$content = $this->wraps['pPrepend'].$content.$this->wraps['pAppend'];
		if ((!$this->dontAddPTags) && (!$tempDontAddPTags)) { $content = $this->wrap ($content, '<p'.($this->wraps['pAddParams'] ? ' '.$this->wraps['pAddParams'] : '') .'>|</p>'."\n"); }
		return $content;
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$paragraph: ...
	 * @return	[type]		...
	 */
	function wordRenderPPR ($paragraph) {

		foreach ($paragraph['subTags'] as $subTag) {
			switch ($subTag['tag']) {
				case 'W:PSTYLE' :	// Now there comes some style information (like header, custom styles etc.)
					$wraps['pPrepend'] .= $this->getWrapPart($this->officeConf['tagWraps.'][strtolower ($subTag['attributes']['W:VAL'])],0);
					$wraps['pAppend']  = $wraps['pAppend'].$this->getWrapPart($this->officeConf['tagWraps.'][strtolower ($subTag['attributes']['W:VAL'])],1);
				break;
				case 'W:JC' : // Horizontal alignment
					$wraps['pAddParams'] .= 'style="text-align:'.$subTag['attributes']['W:VAL'].'" ';
				break;
				case 'W:LISTPR' : // A List
					foreach ($subTag['subTags'] as $subTagL2) {
						switch ($subTagL2['tag']) {
							case 'W:ILVL': 
									// The level didn't change, we only need to output the LI tag
								if ($subTagL2['attributes']['W:VAL'] == $this->currentListLevel) {
									$wraps['pPrepend'] .= $this->getWrapPart ($this->officeConf['tagWraps.']['listitem'],0);
									$wraps['pAppend'] = $wraps['pAppend'].$this->getWrapPart ($this->officeConf['tagWraps.']['listitem'],1);
								}
									// One level down: Open a new UL tag
								if ($subTagL2['attributes']['W:VAL'] > $this->currentListLevel) {
									$wraps['pPrepend'] .= $this->getWrapPart ($this->officeConf['tagWraps.']['unorderedlist'],0);
									$wraps['pPrepend'] .= $this->getWrapPart ($this->officeConf['tagWraps.']['listitem'],0);
									$wraps['pAppend'] = $wraps['pAppend'].$this->getWrapPart ($this->officeConf['tagWraps.']['listitem'],1);
									$this->dontAddPTags = 1;
								}
									// One level up: Close an UL tag
								if ($subTagL2['attributes']['W:VAL'] < $this->currentListLevel) {
									$wraps['pPrepend'] .= $this->getWrapPart ($this->officeConf['tagWraps.']['listitem'],0);
									$wraps['pAppend'] = $wraps['pAppend'].$this->getWrapPart ($this->officeConf['tagWraps.']['listitem'],1);
									$wraps['pAppend'] = $wraps['pAppend'].$this->getWrapPart ($this->officeConf['tagWraps.']['unorderedlist'],1);
									$this->dontAddPTags = 1;
								}
								$this->currentListLevel = $subTagL2['attributes']['W:VAL'];
							break;
							case 'WX:T':	// defines the character used as a bullet sign
								// not parsed yet
							break;
							default:
						}	
					}
				break;
				case 'W:IND' : // This creates an indent
					$wraps['pPrepend'] .= $this->getWrapPart($this->officeConf['tagWraps.']['indented'],0);
					$wraps['pAppend']  = $wraps['pAppend'].$this->getWrapPart($this->officeConf['tagWraps.']['indented'],1);
				break;
				case 'W:TABS' : // Defines tabulators
					// not parsed yet
				break;
				default:
			}
		}
		return $wraps;
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$paragraph: ...
	 * @return	[type]		...
	 */
	function wordRenderR ($paragraph) {
		foreach ($paragraph['subTags'] as $subTag) {
			$textElement = '';
			$textELWraps ['prepend'] = $textELWraps ['prependNext'];
			$textELWraps ['append'] =  $textELWraps ['appendNext'];
			$textELWraps ['prependNext'] = '';
			$textELWraps ['appendNext'] = '';
			switch ($subTag['tag']) {
				case 'W:T' :	// This is the actual bodytext
					$textElement = $this->pValue ($subTag['value']);
				break;
				case 'W:BR' :	// A line break
					$textElement = '<br />';
				break;
				case 'W:RPR' :
					$textELWraps = $this->wordRenderRPR ($subTag['subTags'], $textELWraps);
				break;
				case 'W:SYM' :	// Some symbol like Wingdings etc.
					// don't want to parse them yet
				break;
				case 'W:FOOTNOTEREF' :
				case 'W:FOOTNOTE' :
					// not parsed yet
				break;
				case 'W:PICT' :
					foreach($subTag['subTags'] as $subTagL2)	{
						switch ($subTagL2['tag']) {
							case 'V:GROUP':	// textbox or sth. similar
								//not parsed yet
							break;
							case 'W:BINDATA':	// picture
								$textElement = $this->renderImage($subTagL2);
							break;
						}
					}
				break;
				default:
					
			}
			if ($textELWraps ['styles']) {
				$textELWraps['prepend'] .= '<span style="'.$textELWraps['styles'].'">';
				$textELWraps['append'] = $textELWraps['append'] .'</span>';	
			}
			$content .= $textELWraps ['prepend'].$textElement.$textELWraps ['append'];
		}
		return $content;
	}
	
	/**
	 * **************************************************
	 * Renders the RPR subsections.
	 * NOTE that some parameters are passed by reference!
	 * 
	 * @param	[array]		$pArray: subparts array
	 * @param	[array]		$textELWraps: array of appending and prepending string. BY REFERENCE
	 * @param	[strin]		$wraps: string of parameters being added to the <p> tag. BY REFERENCE
	 * @return	[type]		nothing.
	 */
	function wordRenderRPR ($pArray, $textELWraps) {
		foreach ($pArray as $subTag) {
			switch ($subTag['tag']) {
				case 'W:I': // ITALIC
					$textELWraps ['prependNext'] .= $this->getWrapPart ($this->officeConf['tagWraps.']['italic'],0);
					$textELWraps ['appendNext']  = $textELWraps ['appendNext'].$this->getWrapPart ($this->officeConf['tagWraps.']['italic'],1);
				break;
				case 'W:B': // BOLD
					$textELWraps ['prependNext'] .= $this->getWrapPart ($this->officeConf['tagWraps.']['bold'],0);
					$textELWraps ['appendNext']  = $textELWraps ['appendNext'].$this->getWrapPart ($this->officeConf['tagWraps.']['bold'],1);
				break;
				case 'W:U': // UNDERLINED
					$textELWraps ['prependNext'] .= $this->getWrapPart ($this->officeConf['tagWraps.']['underlined'],0);
					$textELWraps ['appendNext']  = $textELWraps ['appendNext'].$this->getWrapPart ($this->officeConf['tagWraps.']['underlined'],1);
				break;
				case 'W:VERTALIGN':
					if ($subTag['attributes']['W:VAL'] == 'superscript') {
						$textELWraps ['prependNext'] .= $this->getWrapPart ($this->officeConf['tagWraps.']['superscript'],0);
						$textELWraps ['appendNext'] = $textELWraps ['appendNext'].$this->getWrapPart ($this->officeConf['tagWraps.']['superscript'],1);
					}
					if ($subTag['attributes']['W:VAL'] == 'subscript') {
						$textELWraps ['prependNext'] .= $this->getWrapPart ($this->officeConf['tagWraps.']['subscript'],0);
						$textELWraps ['appendNext']  = $textELWraps ['appendNext'].$this->getWrapPart ($this->officeConf['tagWraps.']['subscript'],1);
					}
				break;
				case 'W:COLOR':
					if ($this->officeConf['parseOptions.']['renderColors']) {
						$textELWraps ['styles'] .= 'color:#'.$subTag['attributes']['W:VAL'].' ';
					}
				break;
				case 'W:LANG': // Defines the language:
					$this->wraps['pAddParams'] .= 'lang="'.$subTag['attributes']['W:VAL'].'" ';
				break;
				case 'W:RSTYLE': // Defines a special style:	
					$textELWraps['prepend'] .= $this->getWrapPart($this->officeConf['tagWraps.'][strtolower ($subTag['attributes']['W:VAL'])],0);
					$textELWraps['append']  .= $this->getWrapPart($this->officeConf['tagWraps.'][strtolower ($subTag['attributes']['W:VAL'])],1);
				break;
				case 'R:FONTS': // Applies a certain font-face:
					if ($this->officeConf['parseOptions.']['renderFonts']) {
						$textELWraps ['styles'] .= 'font-face: '.$subTag['W:ASCII'].' ';
					}
				break;
				case 'W:SZ': // Applies a font size:
					if ($this->officeConf['parseOptions.']['renderFonts']) {
						$textELWraps ['styles'] .= 'font-size: '.$subTag['W:ASCII'].' ';
					}				
				break;
				default:
			}
		}
		return $textELWraps;				
	}

	/**
	 * Renders a table
	 * 
	 * @param	[array]		$tArray: subtags for the table
	 * @return	[string]		rendered HTML output
	 */
	function wordRenderTable($tArray)	{
		foreach($tArray as $subTag)	{
			if ($subTag['tag']=='W:TR')	{
				$rowCells='';
				foreach($subTag['subTags'] as $subTagL2)	{
					$tdParams = '';
					if ($subTagL2['tag']=='W:TC')	{
						foreach ($subTagL2['subTags'] as $subTagL3) {
							if ($subTagL3['tag'] == 'W:TCPR') {
								foreach ($subTagL3['subTags'] as $subTagL4) {
									if ($subTagL4['tag'] == 'W:GRIDSPAN') {
										$tdParams .= ' colspan="'.$subTagL4['attributes']['W:VAL'].'"';
									}
								}
							}
						}						
						$cellContent=$this->wordTraverseSection($subTagL2['subTags']);
						$rowCells.='<td'.$tdParams.'>'.$cellContent.'</td>';
					}
				}
				$allRows.='<tr>'.$rowCells.'</tr>';
			}
		}		
		return '<table cellspacing="0" class="'.$this->cssBaseClass.'">'.$allRows.'</table>';
	}
	
		// --- EXCEL FUNCTIONS BELOW ------------------------------------------------------------------------------------

	/**
	 * Traverse the sections. Sub sections lets us go one level deeper and traverse again, cells etc. are
	 * handled by sub functions
	 * 
	 * @param	[array]		$explodedSubsection: Array containing subTags
	 * @return	[string]		HTML content
	 */
	function excelTraverseSection($explodedSubsection)	{
		$renderedWorkSheet = 0;
		foreach($explodedSubsection as $value)	{
			switch ($value['tag']) {
				case 'DOCUMENTPROPERTIES':
				case 'EXCELWORKBOOK':
					// not used yet
				break;
				case 'WORKSHEET':
						// We only want the first worksheet in the file to be rendered:
					if (!$renderedWorkSheet) {
						$rows = $this->excelRenderWorkSheet ($value['subTags']);
						$renderedWorkSheet = 1;
					}
 				break;
				case 'STYLES':
					$styles = $this->excelGetStyles ($value['subTags']);
				break;
				default:
			}
		}
		
			// Now render the table:

		foreach ($rows as $row) {
			$colSpan = $this->colsCount-count ($row);
			$colNr = 0;
			$rowContent = '';
			foreach ($row as $cell) {				
				$colNr ++;
				if (!$cell['data']) { $cell['data'] = '&nbsp;'; }
					// Apply style for table cell if wished / neccesary
				$styleCode = $styles[$cell['styleID']] ? ' style="'.$styles[$cell['styleID']].'"' : '';
				if ($colNr == count($row)) {
					$rowContent .= '<td colspan="'.$colSpan.'"'.$styleCode.'>'.$cell['data']."</td>\n";
				} else {
					$rowContent .= '<td'.$styleCode.'>'.$cell['data']."</td>\n";
				}
			}
			$content.='<tr>'.$rowContent.'</tr>';
		}
		$content = '<table cellspacing="0" class="'.$this->cssBaseClass.'">'.$content.'</table>';
		return $content;
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$wArray: ...
	 * @return	[type]		...
	 */
	function excelRenderWorkSheet ($wArray) {

		$rows = array ();		
			// First build the datastructure and style structure of the table
		foreach ($wArray as $subTag) {
			switch ($subTag['tag']) {
				case 'TABLE':
					$this->colsCount = $subTag['attributes']['SS:EXPANDEDCOLUMNCOUNT'];
					$this->rowsCount = $subTag['attributes']['SS:EXPANDEDROWCOUNT'];
					foreach ($subTag['subTags'] as $subTagL2) {
						switch ($subTagL2['tag']) {
							case 'COLUMN':
								$columns[] = array ('width' => $subTagL2['attributes']['SS:WIDTH']);
							break;
							case 'ROW':
								$cells = array ();
									foreach ($subTagL2['subTags'] as $subTagL3) {
										switch ($subTagL3['tag']) {
											case 'CELL':
												$data = array ();
												if (is_array($subTagL3['subTags'])) {
												  foreach ($subTagL3['subTags'] as $subTagL4) {
													  switch ($subTagL4['tag']) {
														  case 'DATA':
														  	$data[] = $subTagL4['value'];
														  break;	
													  }	
												  }
												} 
												$cells[] = array (
													'styleID' => $subTagL3['attributes']['SS:STYLEID'],
													'data' => $data[0],							// We only take the first entry
												);
											break;
										}	
									}
								$rows[] = $cells;
							break;
						}
					}
				break;
			}			
		}
			
		return $rows;
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$sArray: ...
	 * @return	[type]		...
	 */
	function excelGetStyles ($sArray) {
			// traverse the different style-sets
		foreach ($sArray as $subTag) {
			if ($subTag['tag'] == 'STYLE') {
				$styleID = $subTag['attributes']['SS:ID'];
				foreach ($subTag['subTags'] as $subTagL2) {
					switch ($subTagL2['tag']) {
						case 'ALIGNMENT':
							if ($subTagL2['attributes']['SS:VERTICAL']) {
								$styles[$styleID] .= 'vertical-align:'.strtolower ($subTagL2['attributes']['SS:VERTICAL']).'; ';
							}
							if ($subTagL2['attributes']['SS:HORIZONTAL']) {	// NOT TESTED YET
								$styles[$styleID] .= 'vertical-align:'.strtolower ($subTagL2['attributes']['SS:VERTICAL']).'; ';
							}
						break;
						case 'FONT':
							// not parsed yet
						break;
						case 'INTERIOR':
							if ($subTagL2['attributes']['SS:COLOR'] && $this->officeConf['parseOptions.']['renderBackgroundColors']) {
								$styles[$styleID] .= 'background-color:'.$subTagL2['attributes']['SS:COLOR'].'; ';
							}
						break;
					}
				}
			}
		}
		return $styles;
	}
	
	
	/**
	 * Takes care of images. The Images are not really rendered within this function! By default
	 * it will simply return [IMAGE]! If you want to output the real image, you will have to
	 * provide a userfunction. You can pass the userfunction's name by the general TS configuration
	 * of this extension.
	 * 
	 * 	Example:
	 * 		userFunctions.renderImage = tx_rlmpofficeimport_pi1->renderImage
	 * 
	 * @param	[array]		$iArray: subtags for the image section
	 * @return	[string]		[IMAGE]
	 */
	function renderImage($binDataConf)	{
	  global $c;
	  $pathinformation = pathinfo($binDataConf['attributes']['W:NAME']);
	  $baseName = $pathinformation['basename'];
	  $extension = strtolower($pathinformation['extension']);
	  $binary = $binDataConf['value'];	  
	  if (strstr('gif,jpeg,jpg,png', $extension))	{
	    mt_srand ((double)microtime()*1000000);
  	    $fileName =  $c["path"]."cache/".md5(uniqid(mt_rand())).".".$extension;
  	    if (!@is_file($fileName))	{
		if($fd = fopen($fileName,'wb'))	{
		  fwrite( $fd, base64_decode($binary));
		  fclose( $fd );
		}
	    }	    
	  }	
	 $imageId = createImageFromFile($fileName);	 
	 unlink($fileName);
	 return "{NX:IMAGE ID=\"$imageId\"}";							 
	}

     
     /**
      * Checks, if the file is valid excel or word file. Sets type-class-variable
      */
      function setFileType() {
      	$firstFewBytes = substr($this->filecontent,0,200);
      	if (($this->extension == 'xml' || $this->extension=="tmp") && strstr($firstFewBytes,'<?mso-application progid="Word.Document"?>'))  {
	  $this->type = "word";
	} elseif (($this->extension == 'xml' || $this->extension=="tmp") && strstr($firstFewBytes,'<?mso-application progid="Excel.Sheet"?>'))	{
	  $this->type = "excel";
	} else {
	  $this->type = "undefined";
	}
      }
     
}


?>