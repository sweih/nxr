<?
  require_once "nxheader.inc.php";
  
  // For details of the Calendarobject look at the pgn_calendar.php and see the functions
  // or read the api-documentation.
  
  echo "<h1> Events on selected day</h1>";
  // get Calendar plugin
  $calendar = $cds->plugins->getApi("calendar", array("calendar" => "Happenings"));
  
  // get Calendarbox with month april and year 2005
  $calendarObject =  $calendar->getCalendarObject(4,2005);
  // draw Calendarobject
  echo $calendarObject->draw(); 
  
  // automtically get events of the selected day in the calendarObject, default is current day
  $selectedEvents = $calendar->dispatchCalendar();
  // draw the event data of the selected day.
  echo $calendar->drawEventData($selectedEvents);
  
  
  echo "<br><br><h1>Event overview</h1>";
  
  //function getEvents($categories = "ALL", $startdate="", $enddate="", $starttime="", $endtime="", $limit=20)
  $events = $calendar->getEvents("ALL", "2005-01-01", "2005-12-12", "", "", 20);
  for ($i=0; $i < count($events); $i++) {
    $event = $events[$i];
    echo "<b>".$event["STARTDATE"]." - ".$event["ENDDATE"].": ".$event["TITLE"]."</b><br><br>";
    echo $event["DESCRIPTION"];
    echo "<img src='" . $event["IMAGE"] . "'>";
    echo "<br><br>"; 	  	
  }
    
  require_once "nxfooter.inc.php";



?>