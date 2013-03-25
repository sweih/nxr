<?
	/**
	 * @package CDS
	 */

/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih
	 *	This file is part of N/X.
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
	  * This class contains functions for drawing DHTML components.
	  * Access this class with $cds->layout->dhtml
	  */
	 class DHTMLLayout {
		var $parent;
		
		/**
		* Standard constructor.
		*/
		function DHTMLLayout(&$parent) { 
			$this->parent = &$parent; 
		
		}
		
		/**
		 * Paints an mouseoverimage. Requires a field of 2 images called with ALL-Parameter in getField.
		 * @param imageField
		 */
		function imageMouseOver($imageField) {		  
			if (count($imageField) != 2) {
		  	 return "Error:The field has not cardinality 2.";
		  }
		  if ($imageField[0]["width"] == "") {
		  	 return "Error: Not a valid image field. Use ALL-Attribute with getField method!";
		  }
		  $omo = "onMouseOver='this.src=\"".$imageField[1]["path"]."\";'";
		  $omu = "onMouseOut='this.src=\"".$imageField[0]["path"]."\";'";
		  $tag = '<img src="'.$imageField[0]["path"].'" width="'.$imageField[0]["width"].'" height="'.$imageField[0]["height"].'" border="0" '.$omu.' '.$omo.' >';
		  return $tag;
			
		}
		
		/**
		 * Paints an marquee
		 * @param string Text to show on marquee
		 * @param integer width
		 * @param integer height
		 * @param string color
		 * @param integer speed 1-10
		 */
		function marquee($payload="enter content", $width="300px", $height="20px", $bgcolor="#ffffff", $speed=3) {
			
		?>	
			<script language="JavaScript1.2">
			/*
			Cross browser Marquee script- � Dynamic Drive (www.dynamicdrive.com)
			For full source code, 100's more DHTML scripts, and Terms Of Use, visit http://www.dynamicdrive.com
			Credit MUST stay intact
			*/
			var marqueewidth="300px"
			var marqueeheight="25px"
			var marqueespeed=2
			var marqueebgcolor="#DEFDD9"
			var pauseit=1
			var marqueecontent='<nobr><font face="Arial">Thank you for visiting <a href="http://www.dynamicdrive.com">Dynamic Drive.</a> If you find this script useful, please consider linking to us by <a href="../link.htm">click here.</a> Enjoy your stay!</font></nobr>'
			marqueespeed=(document.all)? marqueespeed : Math.max(1, marqueespeed-1) //slow speed down by 1 for NS
			var copyspeed=marqueespeed
			var pausespeed=(pauseit==0)? copyspeed: 0
			var iedom=document.all||document.getElementById
			if (iedom)
				document.write('<span id="temp" style="visibility:hidden;position:absolute;top:-100px;left:-9000px">'+marqueecontent+'</span>')
			var actualwidth=''
			var cross_marquee, ns_marquee

			function populate(){
				if (iedom){
					cross_marquee=document.getElementById? document.getElementById("iemarquee") : document.all.iemarquee
					cross_marquee.style.left=parseInt(marqueewidth)+8+"px"
					cross_marquee.innerHTML=marqueecontent
					actualwidth=document.all? temp.offsetWidth : document.getElementById("temp").offsetWidth
				} else if (document.layers){
					ns_marquee=document.ns_marquee.document.ns_marquee2
					ns_marquee.left=parseInt(marqueewidth)+8
					ns_marquee.document.write(marqueecontent)
					ns_marquee.document.close()
					actualwidth=ns_marquee.document.width
				}
				lefttime=setInterval("scrollmarquee()",20)
			}
			window.onload=populate

			function scrollmarquee(){
				if (iedom){
					if (parseInt(cross_marquee.style.left)>(actualwidth*(-1)+8))
						cross_marquee.style.left=parseInt(cross_marquee.style.left)-copyspeed+"px"
					else
						cross_marquee.style.left=parseInt(marqueewidth)+8+"px"
					}	else if (document.layers){
						if (ns_marquee.left>(actualwidth*(-1)+8))
							ns_marquee.left-=copyspeed
						else
							ns_marquee.left=parseInt(marqueewidth)+8
					}
				}
				if (iedom||document.layers){
				with (document){
					document.write('<table border="0" cellspacing="0" cellpadding="0"><td>')
					if (iedom){
						write('<div style="position:relative;width:'+marqueewidth+';height:'+marqueeheight+';overflow:hidden">')
						write('<div style="position:absolute;width:'+marqueewidth+';height:'+marqueeheight+';background-color:'+marqueebgcolor+'" onMouseover="copyspeed=pausespeed" onMouseout="copyspeed=marqueespeed">')
						write('<div id="iemarquee" style="position:absolute;left:0px;top:0px"></div>')
						write('</div></div>')
					} else if (document.layers){
						write('<ilayer width='+marqueewidth+' height='+marqueeheight+' name="ns_marquee" bgColor='+marqueebgcolor+'>')
						write('<layer name="ns_marquee2" left=0 top=0 onMouseover="copyspeed=pausespeed" onMouseout="copyspeed=marqueespeed"></layer>')
						write('</ilayer>')
					}
				document.write('</td></table>')
			}
		}
		</script>	
		
		<?PHP
		}
	 }
?>