<?
	require_once "../../config.inc.php";
	require_once $c["path"]."modules/stats/phpOpenTracker.php";



// average clickstream


$documents = phpOpenTracker::get(
array(
'api_call' => 'page_impressions',
'range' => 'current_month',
'constraints' => array('document' => 100104)
)
);
var_dump($documents);



/*
$a =phpOpenTracker::get(
array(
'api_call' => 'visits',
'range' => 'current_month',
'interval' => '86400'

)
);
var_dump($a);*/




//$stats = new Stats1($a, $a);
//$stats->printStats();

/**

phpOpenTracker::plot(
array(
'api_call' => 'nxweekdays',
'what' => 'pi',
'start' => 0,
'end' => 9999999999
));
*/


/*
phpOpenTracker::plot(
array(
'api_call' => 'nxtop',
'what' => 'operating_system',
'limit' => 5,
'range' => 'total',
'width' => '400',
'height' => '300'
)
);
*/

/**
$a = phpOpenTracker::get( array(
	'api_call' => 'nxone_time_visitors',
	'range' => 'current_year'));
	var_dump($a);
*/
/**
$a = phpOpenTracker::get( array(
	'api_call' => 'top_paths',
	'range' => 'current_year',
	'limit' => '10',
	'result_format' => 'array'));
	var_dump($a);	
*/	
/*
phpOpenTracker::plot( array(
	'api_call' => 'nxaccess_statistics',
	'range' => 'current_year',
	'interval' => 'week',
	'width' => '500',
	'height' => '200',
	'what' => array('nxavg_visits'),
	'whatcolors' => array(__GREEN, __ORANGE)
	));
*/

// visitors and page_impressions
/**
phpOpenTracker::plot( array(
	'api_call' => 'nxaccess_statistics',
	'range' => 'current_month',
	'interval' => 'day',
	'month_names' => array(
	'Januar', 'Februar', 'März',
	'April', 'Mai', 'Juni',
	'Juli', 'August', 'September',
	'Oktober', 'November', 'Dezember'
	),
	'width' => '600',
	'height' => '300',
	'what' => array('page_impressions', 'visits'),
	'whatcolors' => array(__GREEN, __BLUE)
	));*/
?>