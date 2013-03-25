<?php

/**
* phpOpenTracker API - Plot Access Statistics
*
* @author   Sven Weih <sven@nxsystems.org>
*	    initial by Sebastian Bergmann.
*/
class phpOpenTracker_API_plot_nxaccess_statistics extends phpOpenTracker_API_Plugin {
  var $apiCalls = array('nxaccess_statistics');
  var $apiType = 'plot';
  
  
  /**
  * Runs the phpOpenTracker API call.
  *
  * @param  array $parameters
  * @return mixed
  * @access public
  */
  function run($parameters) {
    global $c;
    $parameters['interval']    = isset($parameters['interval'])    ? $parameters['interval']    : false;
    $parameters['month_names'] = isset($parameters['month_names']) ? $parameters['month_names'] : false;
    
    if (!$parameters['month_names']) {
      $parameters['month_names'] = array(
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
      );
    }

    $timestamp = time();

    $steps = array(
      'hour'  =>     3600,
      'day'   =>    86400,
      'week'  =>   604800,
      'month' =>  2592000,
      'year'  => 31536000
    );

    $starttitle = '';
    $endtitle   = '';

    switch ($parameters['interval']) {
      case 'hour': {
        $starthour   = $hour = date('H', $parameters['start']);
        $endhour     =         date('H', $parameters['end']);
        $starttitle .= $starthour . ':00h  ';
        $endtitle   .= $endhour   . ':59h  ';
      }
            
      case 'day': {
        $startday    = $day = date('d', $parameters['start']);
        $endday      =        date('d', $parameters['end']);
        $starttitle .= $startday . '. ';
        $endtitle   .= $endday   . '. ';
      }
      case 'week': {
        $week = date('W', $parameters['start'])+2;
      }
      case 'month': {
        $startmonth  = $month = date('m', $parameters['start']);
        $endmonth    =          date('m', $parameters['end']);
        $starttitle .= $parameters['month_names'][$startmonth-1] . ' ';
        $endtitle   .= $parameters['month_names'][$endmonth-1]   . ' ';
      }

      case 'year': {
        $startyear   = $year = date('Y', $parameters['start']);
        $endyear     =         date('Y', $parameters['end']);
        $starttitle .= $startyear;
        $endtitle   .= $endyear;
      }
      

   }

    $title = $starttitle . '   -   ' . $endtitle.' in '.$parameters['interval']."s.";
    
    $disp = true;

    $correct = 0;
	    if ($parameters['interval'] == 'week') {
	    	$correct = 86400;	
	    }
	    
    for ($start = $parameters['start']; $start < $parameters['end']; $start += $steps[$parameters['interval']]) {
      if ($parameters['interval'] == 'month') {
        $steps['month'] = $steps['day'] * date('t', $_start);
      }
	    
      $end = $start + $steps[$parameters['interval']] - 1;

      if ($start <= $timestamp) {
        $apiCallParameters = array(
          'client_id'   => $parameters['client_id'],
          'start'       => $start+$correct,
          'end'         => $end+$correct,
          'constraints' => $parameters['constraints'],
          'interval' => ''
        );
      
        for ($j = 0; $j < count($parameters["what"]); $j++) {
          $y[$j][] = phpOpenTracker::get(
            array_merge(
              array(
              	'api_call' => $parameters["what"][$j]
              ),
            	$apiCallParameters
            )
          );
        }

       } else {
        for ($j=0; $j < count($parameters["what"]); $j++) {
          $y[$j][] = 0;
        }
      }

      switch ($parameters['interval']) {
        case 'hour': {
          $x_label[] = date('H', mktime($hour, 0, 0, $startmonth, $startday, $startyear)) . ':00';
          $disp = ! $disp;
          $hour++;
        }
        break;

        case 'week':
          $x_label[] = date('W', mktime(0, 0, 0, 1, ($week-2)*7, $startyear));
          $week++;       
        
        break;
        case 'day': {
          $x_label[] = date('d', mktime(0, 0, 0, $startmonth, $day, $startyear));
          $day++;
        }
        break;

        case 'month': {
          $x_label[] = date('m', mktime(0, 0, 0, $month, 1, $startyear));
          $month++;
        }
        break;

        case 'year': {
          $x_label[] = date('Y', mktime(0, 0, 0, 1, 1, $year));
          $year++;
        }
        break;
      }
    }

    if ($y==null) {
    	$apiCallParameters = array(
          'client_id'   => $parameters['client_id'],
          'start'       => $start,
          'end'         => $end,
          'constraints' => $parameters['constraints']
        );
        for ($j = 0; $j < count($parameters["what"]); $j++) {
          $y[$j][] = phpOpenTracker::get(
            array_merge(
              array(
              	'api_call' => $parameters["what"][$j]
              ),
            	$apiCallParameters
            )
          );
        }      
 	$x_label[] = '';  
 	$title = "Total";
    }
    
    if ($parameters['interval'] == 'hour') {
      $angle         = 50;
    } else {
      $angle         = 0;
    }
 
    $x_label = $this->clearLabels($x_label, $parameters);        
    $graph = new Graph($parameters['width'], $parameters['height'], 'auto');

    $graph->img->SetMargin(40, 10, 20, 10);
    $graph->SetScale('textlin');
    $graph->SetMarginColor('white');
    $graph->SetFrame(0);
    $graph->xgrid->Show();
  
    for ($j=0; $j < count($parameters["what"]); $j++) {   
       	$plot[$j] = new BarPlot($y[$j]);
    	$plot[$j]->SetFillColor($parameters["whatcolors"][$j]);
    	$plot[$j]->SetShadow();
    	$plot[$j]->SetWeight(0);
    }
    
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
  
  /**
   * clears the labels to fit the diagram
   * @param mixed labels-array
   * @param mixed parameters array
   */
  function clearLabels($labels, $parameters) {
    global $c;
    $width = $parameters["width"] / count($labels);
    $minwidth=18;
    if (!$c["usettf"] && $parameters['interval'] == 'hour') $minwidth=40;
    if ($width < $minwidth) {   	
      $clearDistance = ceil(count($labels) / ceil($parameters["width"]/$minwidth));
      for ($j=0; $j < count($labels); $j++) {	
    	if (($j % $clearDistance) != 0) $labels[$j] = '';
      }
    }
    return $labels; 	
  }
}
?>
