<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter Google Maps API V3 Class
 *
 * Displays a Google Map
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		BIOSTALL (Steve Marks)
 * @link		http://biostall.com
 */
 
class Googlemaps {

	var $center						= "37.4419, -122.1419";
	var $disableDefaultUI			= FALSE;
	var $disableMapTypeControl		= FALSE;
	var $disableNavigationControl	= FALSE;
	var $disableScaleControl		= FALSE;
	var $disableDoubleClickZoom		= FALSE;
	var $draggable					= TRUE;
	var $draggableCursor			= '';
	var $draggingCursor				= '';
	var $navigationControlPosition	= '';
	var $keyboardShortcuts			= TRUE;
	var $jsfile						= '';
	var $map_div_id					= "map_canvas";
	var $map_height					= "450px";
	var $map_name					= "map";
	var $map_type					= "ROADMAP";
	var $map_width					= "100%";
	var $mapTypeControlPosition		= '';
	var $onclick					= '';
	var $region						= '';
	var $scaleControlPosition		= '';
	var $scrollwheel				= TRUE;
	var $sensor						= FALSE;
	var	$version					= "3";
	var $zoom						= 13;
	
	var	$markers					= array();	
	var	$polylines					= array();
	var	$polygons					= array();
	
	function Googlemaps($config = array())
	{
		if (count($config) > 0)
		{
			$this->initialize($config);
		}

		log_message('debug', "Google Maps Class Initialized");
	}

	function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}
	}
	
	function add_marker($params = array())
	{
		
		$marker = array();
		
		$marker['position'] = '';
		$marker['infowindow_content'] = '';
		$marker['clickable'] = TRUE;
		$marker['cursor'] = '';
		$marker['draggable'] = FALSE;
		$marker['flat'] = FALSE;
		$marker['icon'] = '';
		$marker['shadow'] = '';
		$marker['title'] = '';
		$marker['visible'] = TRUE;
		$marker['zIndex'] = '';
		
		$marker_output = '';
		
		foreach ($params as $key => $value) {
		
			if (isset($marker[$key])) {
			
				$marker[$key] = $value;
				
			}
			
		}
		
		if ($marker['position']!="") {
			if ($this->is_lat_long($marker['position'])) {
				$marker_output .= '
			var myLatlng = new google.maps.LatLng('.$marker['position'].');
			';
			}else{
				$lat_long = $this->get_lat_long_from_address($marker['position']);
				$marker_output .= '
			var myLatlng = new google.maps.LatLng('.$lat_long[0].', '.$lat_long[1].');';
			}
		}
		
		$marker_output .= '		
			var marker = new google.maps.Marker({
				position: myLatlng, 
				map: '.$this->map_name;
		if (!$marker['clickable']) {
			$marker_output .= ',
				clickable: false';
		}
		if ($marker['cursor']!="") {
			$marker_output .= ',
				cursor: "'.$marker['cursor'].'"';
		}
		if ($marker['draggable']) {
			$marker_output .= ',
				draggable: true';
		}
		if ($marker['flat']) {
			$marker_output .= ',
				flat: true';
		}
		if ($marker['icon']!="") {
			$marker_output .= ',
				icon: "'.$marker['icon'].'"';
		}
		if ($marker['shadow']!="") {
			$marker_output .= ',
				shadow: "'.$marker['shadow'].'"';
		}
		if ($marker['title']!="") {
			$marker_output .= ',
				title: "'.$marker['title'].'"';
		}
		if (!$marker['visible']) {
			$marker_output .= ',
				visible: false';
		}
		if ($marker['zIndex']!="" && is_numeric($marker['zIndex'])) {
			$marker_output .= ',
				zIndex: '.$marker['zIndex'];
		}
		$marker_output .= '		
			});		';
		
		if ($marker['infowindow_content']!="") {
			$marker_output .= '
			marker.set("content", "'.$marker['infowindow_content'].'");
			
			google.maps.event.addListener(marker, "click", function() {
				iw.setContent(this.get("content"));
				iw.open('.$this->map_name.', this);
			});
			';
		}
		
		$marker_output .= '
			markers.push(marker);
			lat_longs.push(marker.getPosition());
		';
		
		array_push($this->markers, $marker_output);
	
	}
	
	function add_polyline($params = array())
	{
		
		$polyline = array();
		
		$polyline['points'] = array();
		$polyline['strokeColor'] = '#FF0000';
		$polyline['strokeOpactiy'] = '1.0';
		$polyline['strokeWeight'] = '2';
	
		$polyline_output = '';
		
		foreach ($params as $key => $value) {
		
			if (isset($polyline[$key])) {
			
				$polyline[$key] = $value;
				
			}
			
		}
		
		if (count($polyline['points'])) {

			$polyline_output .= '
				var polyline_plan_'.count($this->polylines).' = [';
			$i=0;
			$lat_long_output = '';
			foreach ($polyline['points'] as $point) {
				if ($i>0) { $polyline_output .= ','; }
				$lat_long_to_push = '';
				if ($this->is_lat_long($point)) {
					$lat_long_to_push = $point;
					$polyline_output .= '
					new google.maps.LatLng('.$point.')
					';
				}else{
					$lat_long = $this->get_lat_long_from_address($point);
					$polyline_output .= '
					new google.maps.LatLng('.$lat_long[0].', '.$lat_long[1].')';
					$lat_long_to_push = $lat_long[0].', '.$lat_long[1];
				}
				$lat_long_output .= '
					lat_longs.push(new google.maps.LatLng('.$lat_long_to_push.'));
				';
				$i++;
			}
			$polyline_output .= '];';
			
			$polyline_output .= $lat_long_output;
			
			$polyline_output .= '
				var polyline_'.count($this->polylines).' = new google.maps.Polyline({
    				path: polyline_plan_'.count($this->polylines).',
    				strokeColor: "'.$polyline['strokeColor'].'",
    				strokeOpacity: '.$polyline['strokeOpactiy'].',
    				strokeWeight: '.$polyline['strokeWeight'].'
 				});
				
				polyline_'.count($this->polylines).'.setMap('.$this->map_name.');

			';
		
			array_push($this->polylines, $polyline_output);
			
		}
	
	}
	
	function add_polygon($params = array())
	{
		
		$polygon = array();
		
		$polygon['points'] = array();
		$polygon['strokeColor'] = '#FF0000';
		$polygon['strokeOpactiy'] = '0.8';
		$polygon['strokeWeight'] = '2';
		$polygon['fillColor'] = '#FF0000';
		$polygon['fillOpacity'] = '0.3';
	
		$polygon_output = '';
		
		foreach ($params as $key => $value) {
		
			if (isset($polygon[$key])) {
			
				$polygon[$key] = $value;
				
			}
			
		}
		
		if (count($polygon['points'])) {

			$polygon_output .= '
				var polygon_plan_'.count($this->polygons).' = [';
			$i=0;
			$lat_long_output = '';
			foreach ($polygon['points'] as $point) {
				if ($i>0) { $polygon_output .= ','; }
				$lat_long_to_push = '';
				if ($this->is_lat_long($point)) {
					$lat_long_to_push = $point;
					$polygon_output .= '
					new google.maps.LatLng('.$point.')
					';
				}else{
					$lat_long = $this->get_lat_long_from_address($point);
					$polygon_output .= '
					new google.maps.LatLng('.$lat_long[0].', '.$lat_long[1].')';
					$lat_long_to_push = $lat_long[0].', '.$lat_long[1];
				}
				$lat_long_output .= '
					lat_longs.push(new google.maps.LatLng('.$lat_long_to_push.'));
				';
				$i++;
			}
			$polygon_output .= '];';
			
			$polygon_output .= $lat_long_output;
			
			$polygon_output .= '
				var polygon_'.count($this->polygons).' = new google.maps.Polygon({
    				path: polygon_plan_'.count($this->polygons).',
    				strokeColor: "'.$polygon['strokeColor'].'",
    				strokeOpacity: '.$polygon['strokeOpactiy'].',
    				strokeWeight: '.$polygon['strokeWeight'].',
					fillColor: "'.$polygon['fillColor'].'",
					fillOpacity: '.$polygon['fillOpacity'].'
 				});
				
				polygon_'.count($this->polygons).'.setMap('.$this->map_name.');

			';
		
			array_push($this->polygons, $polygon_output);
			
		}
	
	}
	
	function create_map()
	{
	
		$this->output_js = '';
		$this->output_js_contents = '';
		$this->output_html = '';
		
		if ($this->sensor) { $this->sensor = "true"; }else{ $this->sensor = "false"; }
		
		$this->output_js .= '
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor='.$this->sensor;
		if ($this->region!="" && strlen($this->region)==2) { $this->output_js .= '&region='.strtoupper($this->region); }
		$this->output_js .= '"></script>';
		if ($this->jsfile=="") {
			$this->output_js .= '
			<script type="text/javascript">
			//<![CDATA[
			';
		}

		$this->output_js_contents .= '
			var '.$this->map_name.'; // Global declaration of the map
			var iw = new google.maps.InfoWindow(); // Global declaration of the infowindow
			var lat_longs = new Array();
			var markers = new Array();
		
		  	function initialize() {
				
				';
				
		if ($this->is_lat_long($this->center)) { // if centering the map on a lat/long
			$this->output_js_contents .= 'var myLatlng = new google.maps.LatLng('.$this->center.');';
		}else{  // if centering the map on an address
			$lat_long = $this->get_lat_long_from_address($this->center);
			$this->output_js_contents .= 'var myLatlng = new google.maps.LatLng('.$lat_long[0].', '.$lat_long[1].');';
		}
		
		$this->output_js_contents .= '
				var myOptions = {
			  		';
		if ($this->zoom=="auto") { $this->output_js_contents .= 'zoom: 13,'; }else{ $this->output_js_contents .= 'zoom: '.$this->zoom.','; }
		$this->output_js_contents .= '
					center: myLatlng,
			  		mapTypeId: google.maps.MapTypeId.'.$this->map_type;
		if ($this->disableDefaultUI) {
			$this->output_js_contents .= ',
					disableDefaultUI: true';
		}
		if ($this->disableMapTypeControl) {
			$this->output_js_contents .= ',
					mapTypeControl: false';
		}
		if ($this->disableNavigationControl) {
			$this->output_js_contents .= ',
					navigationControl: false';
		}
		if ($this->disableScaleControl) {
			$this->output_js_contents .= ',
					scaleControl: false';
		}
		if ($this->disableDoubleClickZoom) {
			$this->output_js_contents .= ',
					disableDoubleClickZoom: true';
		}
		if (!$this->draggable) {
			$this->output_js_contents .= ',
					draggable: false';
		}
		if ($this->draggableCursor!="") {
			$this->output_js_contents .= ',
					draggableCursor: "'.$this->draggableCursor.'"';
		}
		if ($this->draggingCursor!="") {
			$this->output_js_contents .= ',
					draggingCursor: "'.$this->draggingCursor.'"';
		}
		if (!$this->keyboardShortcuts) {
			$this->output_js_contents .= ',
					keyboardShortcuts: false';
		}
		if ($this->mapTypeControlPosition!="") {
			$this->output_js_contents .= ',
					mapTypeControlOptions: {position: google.maps.ControlPosition.'.strtoupper($this->mapTypeControlPosition).'}';
		}
		if ($this->navigationControlPosition!="") {
			$this->output_js_contents .= ',
					navigationControlOptions: {position: google.maps.ControlPosition.'.strtoupper($this->navigationControlPosition).'}';
		}
		if ($this->scaleControlPosition!="") {
			$this->output_js_contents .= ',
					scaleControlOptions: {position: google.maps.ControlPosition.'.strtoupper($this->scaleControlPosition).'}';
		}
		if (!$this->scrollwheel) {
			$this->output_js_contents .= ',
					scrollwheel: false';
		}
		$this->output_js_contents .= '}
				'.$this->map_name.' = new google.maps.Map(document.getElementById("'.$this->map_div_id.'"), myOptions);
				';
		
		if ($this->onclick!="") { 
			$this->output_js_contents .= 'google.maps.event.addListener(map, "click", function(event) {
    			'.$this->onclick.'
  			});
			';
		}
		
		// add markers
		if (count($this->markers)) {
			foreach ($this->markers as $marker) {
				$this->output_js_contents .= $marker;
			}
		}	
		//
		
		// add polylines
		if (count($this->polylines)) {
			foreach ($this->polylines as $polyline) {
				$this->output_js_contents .= $polyline;
			}
		}	
		//
		
		// add polygons
		if (count($this->polygons)) {
			foreach ($this->polygons as $polygon) {
				$this->output_js_contents .= $polygon;
			}
		}	
		//
		
		if ($this->zoom=="auto") { 
			$this->output_js_contents .= '
			var bounds = new google.maps.LatLngBounds();
			for (var i=0; i<lat_longs.length; i++) {
				bounds.extend(lat_longs[i]);
			}
			'.$this->map_name.'.fitBounds(bounds);
			';
		}
		
		$this->output_js_contents .= '
			
			}
		
		';
		
		$this->output_js_contents .= '
		  	window.onload = initialize;
		';
		
		if ($this->jsfile=="") { 
			$this->output_js .= $this->output_js_contents; 
		}else{ // if needs writing to external js file
			if (!$handle = fopen($this->jsfile, "w")) {
				$this->output_js .= $this->output_js_contents; 
			}else{
				if (!fwrite($handle, $this->output_js_contents)) {
					$this->output_js .= $this->output_js_contents; 
				}else{
					$this->output_js .= '
					<script src="'.$this->jsfile.'" type="text/javascript"></script>';
				}
			}	
		}
		
		if ($this->jsfile=="") { 
			$this->output_js .= '
			//]]>
			</script>';
		}
		
		
		
		// set height and width
		if (is_numeric($this->map_width)) { // if no width type set
			$this->map_width = $this->map_width.'px';
		}
		if (is_numeric($this->map_height)) { // if no height type set
			$this->map_height = $this->map_height.'px';
		}
		//
		
		$this->output_html .= '<div id="'.$this->map_div_id.'" style="width:'.$this->map_width.'; height:'.$this->map_height.';"></div>';
		
		return array('js'=>$this->output_js, 'html'=>$this->output_html);
	
	}
	
	function is_lat_long($input)
	{
		
		$input = str_replace(", ", ",", $input);
		$input = explode(",", $input);
		if (count($input)==2) {
		
			if (is_numeric($input[0]) && is_numeric($input[1])) { // is a lat long
				return true;
			}else{ // not a lat long - incorrect values
				return false;
			}
		
		}else{ // not a lat long - too many parts
			return false;
		}
		
	}
	
	function get_lat_long_from_address($address)
	{
		
		$lat = 0;
		$lng = 0;
		
		$data_location = "http://maps.google.com/maps/api/geocode/json?address=".str_replace(" ", "+", $address)."&sensor=".$this->sensor;
		if ($this->region!="" && strlen($this->region)==2) { $data_location .= "&region=".$this->region; }
		$data = file_get_contents($data_location);
		
		$data = json_decode($data);
		
		if ($data->status=="OK") {
			
			$lat = $data->results[0]->geometry->location->lat;
			$lng = $data->results[0]->geometry->location->lng;
			
		}
		
		return array($lat, $lng);
		
	}
	
}

?>