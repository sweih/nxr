<?php

/**
* N/X API to Google Maps 
* Uses Google Maps API 2.0 to create customizable maps
* that can be embedded on your website
*
*    Copyright (C) 2006  Sven Weih <sven@nxsystems.org>
*
*    This program is free software; you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation; either version 2 of the License, or
*    (at your option) any later version.
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with this program; if not, write to the Free Software
*    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA 
*/



/**
 * Allowed Controls:
 * GLargeMapControl - a large pan/zoom control used on Google Maps. Appears in the top left corner of the map.
 * GSmallMapControl - a smaller pan/zoom control used on Google Maps. Appears in the top left corner of the map.
 * GSmallZoomControl - a small zoom control (no panning controls) used in the small map blowup windows used to display driving directions steps on Google Maps.
 * GScaleControl - a map scale
 * GMapTypeControl - buttons that let the user toggle between map types (such as Map and Satellite)
 * GOverviewMapControl - a collapsible overview map in the corner of the screen
 */
 
  define(GoogleMapsKey, '<your api key here>'); 
  
  // Serverside geocoding
  define(GeoCoderURI  , 'http://maps.google.com/maps/geo');
  
  // Use proxy-server for http-requests?
  define( UseProxy, false);
  define( ProxyServer, '127.0.0.1');
  define( ProxyPort, '3128');

  define( GLargeMapControl 		, 'GLargeMapControl()');
  define( GSmallMapControl 		,	 'GSmallMapControl()');
  define( GSmallZoomControl 	, 'GSmallZoomControl()');
  define( GScaleControl     	, 'GSCALEControl()');
  define( GMapTypeControl   	, 'GMapTypeControl()');
  define( GOverviewMapControl , 'GOverviewMapControl()');
  
  // viewtypes  
  define (VTSatellite			,'Satellite');
  define (VTMap						,'Map');
  define (VTHybrid				,'Hybrid');

/**
 * API-Class for accessing Google Maps 
 */
class NXGoogleMapsAPI {

  // The Google Maps API Key
  var $apiKey;
  
  // Width and Height of the Control
  var $width;
  var $height;
  
  // GoogleMaps output div id
  var $divId;
  
  // ZoomFactor
  var $zoomFactor;
  
  // Map Center Coords
  var $centerX;
  var $centerY;
  
  // DragMarker
  var $dragX;
  var $dragY;
  
  // Address Array
  var $addresses;
  
  // GeoPoint Array
  var $geopoints;
  
  // Arrays with the controls that will be displayed
  var $controls;
  
  // View Mode
  var $viewMode;

  /**
   * Constructor
   *
   * @param string $apiKey The Google Maps API-Key for your domain.
   */
  function NXGoogleMapsAPI($apiKey="") {
    $this->apiKey = $apiKey;
    if ($this->apiKey == "") 
      $this->apiKey = GoogleMapsKey;
    $this->_initialize();
  }
  
  
  /**
   * Add an address-marker to the map. The address is resolved by the webbrowser.
   * with the Google Geocoder.
   *
   * @param string address which should be add. test with google maps
   * @param string HTML-Code which will be displayed when the user clicks the address
   * @param boolean Set the Center to this point(true) or not (false)
   */
  function addAddress($address, $htmlinfo, $setCenter=true) {
    $ar = array(addSlashes($address), addSlashes($htmlinfo), $setCenter);
    array_push($this->addresses, $ar);	
  }
  
  /**
   * Invokes the google geocoder to get the coordinates of the given address
   *
   * @param string $address Address you want to get the longitude and the lattitude of
   * @return array format $array['longitude'], $array['lattitude'];
   */
  function geoCodeAddress($address) {
    // initialization
    $uri = GeoCoderURI.'?q='.urlencode($address).'&output=csv&key='.$this->apiKey;
    $response = '0,0,0,0';
  	if (UseProxy) { 	
  	  $errno="";
  		$errstr="";
  		$file = fsockopen(ProxyServer, ProxyPort, &$errno, &$errstr,30); 
  		if( !$file ) {
   			fclose($file);    			
 		  } else { 
   			$response = '';
   			fputs($file,"GET $uri HTTP/1.0\n\n"); 
   			while (!feof($file)) 
   			{
     				$response.=fread($file,4096);
   			}
   			fclose($file);
  		}  		
  		$response = substr($response,strpos($response,"\r\n\r\n")+4)  		;  		
  	} else {
  	  $response = file_get_contents($uri);
  	}
  	
  	// compile result array
  	$result = array('longitude' =>0, 'latitude' => 0);
  	$tmpArray = @explode(',', $response);  	
  	if (is_array($tmpArray)) {
  		$result['latitude'] = $tmpArray[2];
  		$result['longitude'] = $tmpArray[3];
  	}
  	return $result;  	
  }
  
  /**
   * Add a dragable marker to the map. Only one Drag-Marker is allowed!
   *
   * @param integer $longitude Longitude of the point
   * @param integer $latitude  Lattitude of the point
   */
  function addDragMarker($longitude, $latitude) {
    $this->dragX = $longitude;
    $this->dragY = $latitude;	
  }
  
  /**
   * Add a geopoint to the map. 
   *
   * @param integer Longitude of the point
   * @param integer Latitude of the point
   * @param string HTML-Code which will be displayed when the user clicks the address
   * @param boolean Set the Center to this point(true) or not (false)
   */  
  function addGeoPoint($longitude, $latitude, $htmlinfo, $setCenter) {
    $ar = array($latitude, $longitude, addSlashes($htmlinfo), $setCenter);
    array_push($this->geopoints, $ar);	
  }
  
  /**
   * Adds a control to the map
   *
   * @param control Control-Type. Allowed are the constants 
   * GLargeMapControl - a large pan/zoom control used on Google Maps. Appears in the top left corner of the map.
   * GSmallMapControl - a smaller pan/zoom control used on Google Maps. Appears in the top left corner of the map.
   * GSmallZoomControl - a small zoom control (no panning controls) used in the small map blowup windows used to display driving directions steps on Google Maps.
   * GScaleControl - a map scale
   * GMapTypeControl - buttons that let the user toggle between map types (such as Map and Satellite)
   * GOverviewMapControl - a collapsible overview map in the corner of the screen
   *      
   */      
  function addControl($control) {
  	array_push($this->controls, $control);
  }
  
  /**
   * Set the ZoomFactor
   * The ZoomFactor is a value between 0 and 17
   *
   * @param integer $zoomFactor Value of the Zoom-Factor
   */
  function setZoomFactor($zoomFactor) {
  	 if ($zoomFactor > -1 && $zoomFactor < 18) {
  	   $this->zoomFactor = $zoomFactor;
  	 }
  }
  
  /**
   * Set the map Mode. Allowed options are:
   *
   * @param string $mode		VTMap - View as Map
   *												VTSatellite - View as Satellite 
   *												VTHybrid - View as Hybrid mode (mixed Sat and Map)
   */
  function setMapType($mode="Map") {
  	  $this->viewMode = $mode;
  	  $this->addControl(GMapTypeControl);
  }

  /**
   * Set the width of the map
   *
   * @param integer $width The Height in pixels
   */
  function setWidth($width) {
    $this->width = $width;
  }
  
  /**
   * Set the height of the map
   *
   * @param integer $height The Height in pixels
   */
  function setHeight($height) {
  	  $this->height = $height;
  }
  
  /**
   * Center the map to the coordinates
   *
   * @param integer $x Longitude
   * @param integer $y Latitude
   */
  function setCenter($x, $y) {
  	$this->centerX = $x;
  	$this->centerY = $y;
  }
  
  
  /**
   * Returns the HTML-Code, which must be placed within the <HEAD>-Tags of your page.
   *
   * @returns string The Code for the <Head>-Tag
   */
  function getHeadCode() {
  	$out = '<style type="text/css">
   v\:* {
       behavior:url(#default#VML);
   }
</style>
<script src="http://maps.google.com/maps?file=api&v=2&key='.$this->apiKey.'"  type="text/javascript"></script>';
   $out.= $this->_getGMapInitCode();
   return $out;
  }
  
  /**
   * Get the BodyCode and draw the map.
   *
   * @returns string Returns the code which is to be placed wight the <body>-tags.
   */
  function getBodyCode() {
  	$out = '<div id="'.$this->divId.'" style="width:'.$this->width.'px;height:'.$this->height.'px;"></div>';  	
  	return $out;
  }
  
  /**
   * Get the code, which must be passed to the <body>-attribute onLoad.
   *
   * @returns string The onload Code
   */
  function getOnLoadCode() {
  	$out = "initNXGMap(document.getElementById('$this->divId'));";
  	return $out;
  }
  
  
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////// Internal functions /////////////////////////////////////////////////////////////////////////////////  
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
  
  /** 
   * Compiles the Javascript to initialize the map.
   * Is automatically called, so do not call yourself.
   */
  function _getGMapInitCode() {
  	$out = '  <script type="text/javascript">
  //<![CDATA[
  var map      = null;
  var geocoder = null;
  var center   = null;
  var updateX  = null;
  var updateY  = null;
  var marker   = null;';
      
			 // Add Geopoints Array
			 $out.="\n";
      if ( count($this->geopoints) > 0 ) {      	
      	$out.= '  var geopoints = new Array(';
      	for ($i=0; $i < count($this->geopoints); $i++) {
      	  	$out.= ' new Array('.$this->geopoints[$i][0].','.$this->geopoints[$i][1].' ,"'.$this->geopoints[$i][2].'", ';
      	  	// move to this address?
      	  	if ($this->geopoints[$i][3]) {
      	  		$out.='true';
      	  	} else {
      	  		$out.='false';
      	  	}
      	  	$out.=')';
      	  	if ($i < (count($this->geopoints)-1)) $out.=', ';
      	}
      	$out.=");\n";
      } else {
      	  $out.="  var geopoints = new Array();\n";
      }
      
			 // Add Addresses Array            
      if ( count($this->addresses) > 0 ) {      	
      	$out.= '  var addresses = new Array(';
      	for ($i=0; $i < count($this->addresses); $i++) {
      	  	$out.= ' new Array("'.$this->addresses[$i][0].'", "'.$this->addresses[$i][1].'", ';
      	  	// move to this address?
      	  	if ($this->addresses[$i][2]) {
      	  		$out.='true';
      	  	} else {
      	  		$out.='false';
      	  	}
      	  	$out.=')';
      	  	if ($i < (count($this->addresses)-1)) $out.=', ';
      	}
      	$out.=");\n";
      } else {
      	  $out.="  var addresses = new Array();\n";
      }
    	$out.="\n";
    	
    	// Draw standard js-functions and initialization code.
    	$out.='  function showAddresses() {
    for (i=0; i < addresses.length; i++) {
      showAddress(addresses[i][0], addresses[i][1], addresses[i][2]);
    }	
  }
    	
  function showAddress(address, htmlInfo, moveToPoint) {
   if (geocoder) {
     geocoder.getLatLng(
       address,
       function(point) {
         if (!point) {
           alert("Location not found:" + address);
         } else {              
           if (moveToPoint) {
             map.setCenter(point, '.$this->zoomFactor.');
           }
           var marker = new GMarker(point);
           map.addOverlay(marker);
           if (htmlInfo != "") {
             GEvent.addListener(marker, "click", function() {
                marker.openInfoWindowHtml(htmlInfo);
             });              
           }
         }
       }
     );
    }
  }

  function showGeopoints() {
    for (i=0; i < geopoints.length; i++) {
 	    	showGeopoint(geopoints[i][0], geopoints[i][1], geopoints[i][2], geopoints[i][3]);
    }	
  }

  function showGeopoint(longitude, latitude, htmlInfo, moveToPoint) {
    if (moveToPoint) {
      map.setCenter(new GLatLng(longitude, latitude), '.$this->zoomFactor.');
    }
    var marker = new GMarker(new GLatLng(longitude, latitude));
    map.addOverlay(marker);
    if (htmlInfo != "") {
      GEvent.addListener(marker, "click", function() {
        marker.openInfoWindowHtml(htmlInfo);
      });              
       }   
  }

  function moveToGeopoint(index) {
	  map.panTo(new GLatLng(geopoints[index][0], geopoints[index][1]));
  }

  function moveToAddress(index) {
    moveToAddressEx(addresses[index][0]); 
  }

  function moveToAddressEx(addressString) {
    if (geocoder) { 
     geocoder.getLatLng(
       addressString,
       function(point) {       
         if (!point) {
           alert("Location not found:" + addressString);
         } else {                                    
            center = point;
            map.panTo(point);           
         }
       });    
    }
  }

  function moveToAddressDMarker(addressString) {
    if (geocoder) { 
     geocoder.getLatLng(
       addressString,
       function(point) {       
         if (!point) {
           alert("Location not found:" + addressString);
         } else {                                    
            center = point;
            setZoomFactor(14);
            map.panTo(point);  
            addDragableMarker();         
         }
       });    
    }
  }

  function setZoomFactor(factor) {
	    map.setZoom(factor);
  }

  function addDragableMarker() {
    if (!marker) {
      marker = new GMarker(center, {draggable: true});
      map.addOverlay(marker);
       
      GEvent.addListener(marker, "dragend", function() {      
        var tpoint =  marker.getPoint();      
        document.getElementById(updateX).value = tpoint.lat();
        document.getElementById(updateY).value = tpoint.lng();              
      });
    } else {
  	  marker.setPoint(center);  	 
    }  
    var tpoint =  marker.getPoint();      
    document.getElementById(updateX).value = tpoint.lat();
    document.getElementById(updateY).value = tpoint.lng();              
  }
    	
  function initNXGMap(mapElement) {
    if (GBrowserIsCompatible()) {
      map = new GMap2(mapElement);        
      geocoder = new GClientGeocoder();'."\n";
      
     
      // Add controls to the map          
      if (count($this->controls) > 0) {
        for ($i=0; $i<count($this->controls); $i++) {
          $out.="      map.addControl(new ".$this->controls[$i].");\n";
        }
      }
      
      // Center the map
      if (($this->centerX != -1000) && ($this->centerY != -1000)) {      	
      	$out.= '      map.setCenter(new GLatLng('.$this->centerX.', '.$this->centerY.'), '.$this->zoomFactor.');'."\n";      	
      }
      
            // Set the viewmode;
      if (strtoupper($this->viewMode) == "SAT") {
        $out.="      map.setMapType(G_SATELLITE_TYPE);\n";	
      } else if (strtoupper($this->viewMode) == "HYBRID") {
      	$out.="      map.setMapType(G_HYBRID_TYPE);\n";	
      }          
      
      
      $out.='      updateX="coordX"; updateY="coordY";'."\n";
      
      // Draw Dragmarker
      if (($this->dragX != 1000) && ($this->dragY != -1000)) {
      	$out.='
      	  center = new GLatLng('.$this->dragY.','.$this->dragX.');
      	  map.setCenter(center, '.$this->zoomFactor.');
      	  marker = new GMarker(center, {draggable: true});
    			map.addOverlay(marker);
       
    			GEvent.addListener(marker, "dragend", function() {      
      			var tpoint =  marker.getPoint();      
      			document.getElementById(updateX).value = tpoint.lat();
      			document.getElementById(updateY).value = tpoint.lng();              
  				});'."\n";
      }
      
      // Add AddressPoints
			$out.="      showAddresses();\n";  
			// Add GeoPoints
			$out.="      showGeopoints();\n";
           
     $out.='
    }
  }
  //]]>
</script>';
  	return $out;
  }
  
  /**
   * Initializes the standard values of the class. 
   * Is automatically called by the constructor.
   */
  function _initialize() {
  	$this->width 			= 800;
  	$this->height 		= 600;
  	$this->divId    	= 'map';
  	$this->zoomFactor = 14;
  	$this->centerX    = -1000;
  	$this->centerY    = -1000;
  	$this->dragX			= -1000;
  	$this->dragY			= -1000;
  	$this->viewMode   = "Map";
  	$this->addresses  = array();
  	$this->geopoints  = array();
  	$this->controls   = array();
  }

}

?>