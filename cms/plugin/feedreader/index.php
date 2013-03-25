<?php
// Start counting time for the page load
$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];

// Include SimplePie
// Located in the parent directory
include_once($c["path"].'plugin/feedreader/simplepie.inc');
include_once($c["path"].'plugin/feedreader/idn/idna_convert.class.php');

// Create a new instance of the SimplePie object
$feed = new SimplePie();

// Set these Configuration Options
$feed->strip_ads(true);
$feed->feed_url($url);


// Allow us to change the input encoding from the URL string if we want to. (optional)
if (!empty($_GET['input'])) {
	$feed->input_encoding($_GET['input']);
}

// Allow us to snap into IHBB mode.
if (!empty($_GET['image'])) {
	$feed->bypass_image_hotlink('i');
	$feed->bypass_image_hotlink_page('./ihbb.php');
}

// Initialize the whole SimplePie object.  Read the feed, process it, parse it, cache it, and 
// all that other good stuff.  The feed's information will not be available to SimplePie before 
// this is called.
$feed->init();

// We'll make sure that the right content type and character encoding gets set automatically.
// This function will grab the proper character encoding, as well as set the content type to text/html.
$feed->handle_content_type();

// When we end our PHP block, we want to make sure our DOCTYPE is on the top line to make 
// sure that the browser snaps into Standards Mode.
$out = '';
if ($feed->data):

				
				foreach($feed->get_items() as $item):
					$out.='<div class="chunk"><h2>'	;
				     if ($item->get_permalink()) $out.='<a href="' . $item->get_permalink() . '">'; 
				     $out.= $item->get_title(); 
				     if ($item->get_permalink()) 
				       $out.= '</a>&nbsp;<span class="footnote">'.$item->get_date('j M Y').'</span>';
				      $out.='</h2>';
					 $out.= $item->get_description();

						
						// Check for enclosures.  If an item has any, set the first one to the $enclosure variable.
						if ($enclosure = $item->get_enclosure(0)) {

							// Use the embed() method to embed the enclosure into the page inline.
							$out.= '<div align="center">';
							$out.= '<p>' . $enclosure->embed(array(
								'audio' => './for_the_demo/place_audio.png',
								'video' => './for_the_demo/place_video.png',
								'alt' => '<img src="./for_the_demo/mini_podcast.png" class="download" border="0" title="Download the Podcast (' . $enclosure->get_extension() . '; ' . $enclosure->get_size() . ' MB)" />',
								'altclass' => 'download'
							)) . '</p>';
							$out.= '<p class="footnote" align="center">(' . $enclosure->get_type() . '; ' . $enclosure->get_size() . ' MB)</p>';
							$out.= '</div>';
						}
	

	
						$out.= '<p class="footnote" align="center"><a href="'.$item->add_to_blinklist().'" title="Add post to Blinklist">Blinklist</a> | <a href="'.$item->add_to_delicious().'" title="Add post to del.icio.us">Del.icio.us</a> | <a href="'. $item->add_to_digg().'" title="Digg this!">Digg</a> | <a href="'. $item->add_to_furl().'" title="Add post to Furl">Furl</a> | <a href="'.$item->add_to_magnolia().'" title="Add post to Ma.gnolia">Ma.gnolia</a> | <a href="'.$item->add_to_newsvine().'" title="Add post to Newsvine">Newsvine</a> | <a href="'. $item->add_to_spurl().'" title="Add post to Spurl">Spurl</a> | <a href="'. $item->search_technorati().'" title="Who s linking to this post?">Technorati</a></p>';
						$out.='</div><br><br>';

			 endforeach;		
			endif;