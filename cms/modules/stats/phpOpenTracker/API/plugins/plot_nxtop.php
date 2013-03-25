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
class phpOpenTracker_API_plot_nxtop extends phpOpenTracker_API_Plugin {

  var $apiCalls = array('nxtop');
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
    $parameters['api_call']      = 'top';
    $parameters['result_format'] = 'separate_result_arrays';

    $apicall=array(
    	'api_call' => 'top',
    	'result_format' => 'separate_result_arrays',
        'what' => $parameters["what"],
        'start' => $parameters["start"],
        'end' => $parameters["end"],
        'client_id' => 1,
        'limit' => $parameters["limit"]
        
    );
    
    list($names, $values, $percent, $total) = phpOpenTracker::get($apicall);
	
    $percent_others = 100.0;
    $values_others = 0;
    for ($j=0; $j < $parameters['limit']; $j++) {
      $percent_others = $percent_others - $percent[$j];	
      $values_others = $values_others + $values[$j];
      if (is_numeric($names[$j])) $names[$j] = resolvePage($names[$j]);
    }
    
    array_push($percent, $percent_others);
    array_push($names, $lang->get("others", "Others"));
    array_push($values, ($values_others * $percent_others/100));

    $title = 'Top ' . $parameters['limit'] . ' ';

    switch ($parameters['what']) {
      case 'document': {
        $title .= 'Pages';
      }
      break;

      case 'entry_document': {
        $title .= 'Entry Pages';
      }
      break;

      case 'exit_document': {
        $title .= 'Exit Pages';
      }
      break;

      case 'exit_target': {
        $title .= 'Exit Targets';
      }
      break;

      case 'host': {
        $title .= 'Hosts';
      }
      break;

      case 'operating_system': {
        $title .= 'Operating Systems';
      }
      break;

      case 'referer': {
        $title .= 'Referers';
      }
      break;

      case 'user_agent': {
        $title .= 'Browsers';
      }
      break;
    }

    $title .= " (Total: $total)";

    for ($i = 0, $numValues = sizeof($values); $i < $numValues; $i++) {
      $legend[$i] = sprintf(
        '%s (%s, %s%%%%)',

        $names[$i],
        $values[$i],
        $percent[$i]
      );
    }

    $graph = new PieGraph($parameters['width'], $parameters['height'], 'auto');
   
    $graph->title->Set($title);
    if ($c["usettf"]) $graph->title->SetFont(FF_ARIAL, FS_NORMAL, 8);
    $graph->title->SetColor('black');
    $graph->legend->Pos(0.5, 0.60, "center", "top");
    $graph->legend->SetFillColor("white");
    $graph->legend->SetShadow(0);
   
    
    //$graph->legend->SetFrame(0);
    if ($c["usettf"]) $graph->legend->SetFont(FF_ARIAL, FS_NORMAL, 8);
    $graph->SetFrame(0);
    $plot = new PiePlot3d($percent);
    $plot->SetTheme('nx');
    $plot->SetCenter(0.5, 0.3);
    $plot->SetAngle(45);
    if ($c["usettf"]) $plot->value->SetFont(FF_ARIAL, FS_NORMAL, 8);
    $plot->SetLegends($legend);

    $graph->Add($plot);
    $graph->img->SetAntiAliasing("white");
    $graph->Stroke();
  }
}
?>