<?php
//
// +---------------------------------------------------------------------+
// | phpOpenTracker - The Website Traffic and Visitor Analysis Solution  |
// +---------------------------------------------------------------------+
// | Copyright (c) 2000-2003 Sebastian Bergmann. All rights reserved.    |
// +---------------------------------------------------------------------+
// | This source file is subject to the phpOpenTracker Software License, |
// | Version 1.0, that is bundled with this package in the file LICENSE. |
// | If you did not receive a copy of this file, you may either read the |
// | license online at http://phpOpenTracker.de/license/1_0.txt, or send |
// | a note to license@phpOpenTracker.de, so we can mail you a copy.     |
// +---------------------------------------------------------------------+
//

/**
* phpOpenTracker API - Plot Top
*
* @author Sven Weih <sven@weih.de>  
*  	  initial Sebastian Bergmann <sebastian@phpOpenTracker.de>
*/
class phpOpenTracker_API_plot_nxhoursdays extends phpOpenTracker_API_Plugin {

  var $apiCalls = array('nxhours', 'nxweekdays');
  var $apiType = 'plot';

  /**
  * Runs the phpOpenTracker API call.
  *
  * @param  array $parameters
  * @return mixed
  * @access public
  */
  function run($parameters) {
    global $lang, $c;
    $parameters['result_format'] = 'separate_result_arrays';

    
    // hour or weeday analysis
    $apc = "weekdays";
    if ($parameters['api_call'] == "nxhours") $apc = "hours";
    
    $apicall=array(
    	'api_call' => $apc,
    	'what' => $parameters["what"],
        'start' => $parameters["start"],
        'end' => $parameters["end"]      
    );

    $queryValues = phpOpenTracker::get($apicall);

    for ($i=0; $i < count($queryValues); $i++) {
      $y[$i] = $queryValues[$i]["value"];	
    }
    
    $title = 'Analyse ';

    switch ($parameters['what']) {
      case 'visits': {
        $title .= 'Visits';
      }
      break;

      case 'pi': {
        $title .= 'Page Impressions';
      }
      break;

      
      case 'avg_clickstream': {
        $title .= 'Average Clickstream';
      }
      break;

      case 'avg_time': {
        $title .= 'Average Online Time';
      }
      break;
    }


   
    if ($apc == "hours") {
    	for ($i=0; $i < 24; $i++) {	
    		$x_label[$i] = sprintf("%02d", $i); 
	}
	    $angle         = 50;
    } else {
    	$x_label = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");	
    	    $angle         = 30;
    }
    
    $graph = new Graph($parameters['width'], $parameters['height'], 'auto');

    $graph->img->SetMargin(40, 10, 20, 50);
    $graph->SetScale('textlin');
    $graph->SetMarginColor('white');
    $graph->SetFrame(0);
    $graph->xgrid->Show();
  
    $plot[0] = new BarPlot($y);
    $plot[0]->SetFillColor(__RED);
    $plot[0]->SetShadow();
    $plot[0]->SetWeight(0);
    
    
    $gbarplot = new GroupBarPlot($plot);
    $gbarplot->SetWidth(0.6);
    $graph->add($gbarplot);
    
    $graph->xaxis->SetTickLabels($x_label);
    if ($c["usettf"]) $graph->xaxis->SetLabelAngle($angle);
    if ($c["usettf"]) $graph->xaxis->SetFont(FF_ARIAL , FS_NORMAL, 8);
    if ($c["usettf"]) $graph->xaxis->title->SetFont(FF_ARIAL, FS_NORMAL, 8);

    $graph->yaxis->SetColor('black');
    if ($c["usettf"]) $graph->yaxis->SetFont(FF_ARIAL, FS_NORMAL, 8);
    if ($c["usettf"]) $graph->yaxis->title->SetFont(FF_ARIAL, FS_NORMAL, 8);
    
    $graph->title->Set($title);
    if ($c["usettf"]) $graph->title->SetFont(FF_ARIAL, FS_NORMAL, 8);
    $graph->img->SetAntiAliasing("white");
    
    $graph->Stroke();
  }
}

?>