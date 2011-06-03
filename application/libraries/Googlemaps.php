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
 * @docs		http://biostall.com/wp-content/uploads/2010/07/Google_Maps_V3_API_Documentation.pdf
 */
 
class Googlemaps {
	
	var $adsense					= FALSE; 					// Whether Google Adsense For Content should be enabled
	var $adsenseChannelNumber		= ''; 						// The Adsense channel number for tracking the performance of this AdUnit
	var $adsenseFormat				= 'HALF_BANNER';			// The format of the AdUnit
	var $adsensePosition			= 'TOP_CENTER';				// The position of the AdUnit
	var $adsensePublisherID			= '';						// Your Google AdSense publisher ID
	var $center						= "37.4419, -122.1419";		// Sets the default center location (lat/long co-ordinate or address) of the map
	var $disableDefaultUI			= FALSE;					// If set to TRUE will hide the default controls (ie. zoom, scale etc)
	var $disableMapTypeControl		= FALSE;					// If set to TRUE will hide the MapType control (ie. Map, Satellite, Hybrid, Terrain)
	var $disableNavigationControl	= FALSE;					// If set to TRUE will hide the Navigation control (ie. zoom in/out, pan)
	var $disableScaleControl		= FALSE;					// If set to TRUE will hide the Scale control
	var $disableDoubleClickZoom		= FALSE;					// If set to TRUE will disable zooming when a double click occurs
	var $draggable					= TRUE;						// If set to FALSE will prevent the map from being dragged around
	var $draggableCursor			= '';						// The name or url of the cursor to display on a draggable object
	var $draggingCursor				= '';						// The name or url of the cursor to display when an object is being dragged
	var $navigationControlPosition	= '';						// The position of the Navigation control
	var $keyboardShortcuts			= TRUE;						// If set to FALSE will disable to map being controlled via the keyboard
	var $jsfile						= '';						// Set this to the path of an external JS file if you wish the JavaScript to be placed in a file rather than output directly into the <head></head> section. The library will try to create the file if it does not exist already. Please ensure the destination file is writeable
	var $map_div_id					= "map_canvas";				// The ID of the <div></div> that is output which contains the map
	var $map_height					= "450px";					// The height of the map container. Any units (ie 'px') can be used. If no units are provided 'px' will be presumed
	var $map_name					= "map";					// The JS reference to the map. Currently not used but to be used in the future when multiple maps are supported
	var $map_type					= "ROADMAP";				// The default MapType
	var $map_width					= "100%";					// The width of the map container. Any units (ie 'px') can be used. If no units are provided 'px' will be presumed
	var $mapTypeControlPosition		= '';						// The position of the MapType control
	var $onclick					= '';						// The JavaScript action to perform when the map is clicked
	var $region						= '';						// Country code top-level domain (eg "uk") within which to search. Useful if supplying addresses rather than lat/longs
	var $scaleControlPosition		= '';						// The position of the Scale control
	var $scrollwheel				= TRUE;						// If set to FALSE will disable zooming by scrolling of the mouse wheel
	var $sensor						= FALSE;					// Set to TRUE if being used on a device that can detect a users location
	var	$version					= "3";						// Version of the API being used. Not currently used in the libraryh
	var $zoom						= 13;						// The default zoom level of the map. If set to "auto" will autozoom/center to fit in all visible markers. If "auto", also overrides the $center parameter
	
	var	$markers					= array();					// An array used by the library to store the markers as they are produced
	var	$polylines					= array();					// An array used by the library to store the polylines as they are produced
	var	$polygons					= array();					// An array used by the library to store the polygons as they are produced
	var	$circles					= array();					// An array used by the library to store the circles as they are produced
	
	var $directions					= FALSE;					// Whether or not the map will be used to show directions
	var $directionsStart			= "";						// The starting location (lat/long co-ordinate or address) of the directions
	var $directionsEnd				= "";						// The destination point (lat/long co-ordinate or address) of the directions
	var $directionsDivID			= "";						// An element's ID on the page where textual directions will be output to. Leave blank if not required
	var $directionsMode				= "DRIVING"; 				// DRIVING, WALKING or BICYCLING (US Only) - The vehicle/mode of transport to show directions for
	var $directionsAvoidTolls		= FALSE;					// Whether or not directions should avoid tolls
	var $directionsAvoidHighways	= FALSE;					// Whether or not directions should avoid highways
	
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
		
		if ($this->sensor) { $this->sensor = "true"; }else{ $this->sensor = "false"; }
		
	}
	
	function add_marker($params = array())
	{
		
		$marker = array();
		
		$marker['position'] = '';								// The position (lat/long co-ordinate or address) at which the marker will appear
		$marker['infowindow_content'] = '';						// If not blank, creates an infowindow (aka bubble) with the content provided. Can be plain text or HTML
		$marker['clickable'] = TRUE;							// Defines if the marker is clickable
		$marker['cursor'] = '';									// The name or url of the cursor to display on hover
		$marker['draggable'] = FALSE;							// Defines if the marker is draggable
		$marker['flat'] = FALSE;								// If set to TRUE will not display a shadow beneath the icon
		$marker['icon'] = '';									// The name or url of the icon to use for the marker
		$marker['animation'] = ''; 								// blank, 'DROP' or 'BOUNCE'
		$marker['onclick'] = '';								// JavaScript performed when a marker is clicked
		$marker['ondblclick'] = '';								// JavaScript performed when a marker is double-clicked
		$marker['ondrag'] = '';									// JavaScript repeatedly performed while the marker is being dragged
		$marker['ondragstart'] = '';							// JavaScript performed when a marker is started to be dragged
		$marker['ondragend'] = '';								// JavaScript performed when a draggable marker is dropped
		$marker['onmousedown'] = '';							// JavaScript performed when a mousedown event occurs on a marker
		$marker['onmouseout'] = '';								// JavaScript performed when the mouse leaves the area of the marker icon
		$marker['onmouseover'] = '';							// JavaScript performed when the mouse enters the area of the marker icon
		$marker['onmouseup'] = '';								// JavaScript performed when a mouseup event occurs on a marker
		$marker['onrightclick'] = '';							// JavaScript performed when a right-click occurs on a marker
		$marker['raiseondrag'] = TRUE;							// If FALSE, disables the raising and lowering of the icon when a marker is being dragged
		$marker['shadow'] = '';									// The name or url of the icon’s shadow
		$marker['title'] = '';									// The tooltip text to show on hover
		$marker['visible'] = TRUE;								// Defines if the marker is visible by default
		$marker['zIndex'] = '';									// The zIndex of the marker. If two markers overlap, the marker with the higher zIndex will appear on top
		
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
		if (!$marker['raiseondrag']) {
			$marker_output .= ',
				raiseOnDrag: false';
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
		if ($marker['animation']!="" && (strtoupper($marker['animation'])=="DROP" || strtoupper($marker['animation']=="BOUNCE"))) {
			$marker_output .= ',
				animation:  google.maps.Animation.'.strtoupper($marker['animation']);
		}
		$marker_output .= '		
			});		';
		
		if ($marker['infowindow_content']!="") {
			$marker_output .= '
			marker.set("content", "'.$marker['infowindow_content'].'");
			
			google.maps.event.addListener(marker, "click", function() {
				iw.setContent(this.get("content"));
				iw.open('.$this->map_name.', this);
			';
			if ($marker['onclick']!="") { $marker_output .= $marker['onclick'].'
			'; }
			$marker_output .= '
			});
			';
		}else{
			if ($marker['onclick']!="") { 
				$marker_output .= '
				google.maps.event.addListener(marker, "click", function() {
					'.$marker['onclick'].'
				});
				';
			}
		}
		
		if ($marker['ondblclick']!="") { 
			$marker_output .= '
			google.maps.event.addListener(marker, "dblclick", function() {
				'.$marker['ondblclick'].'
			});
			';
		}
		if ($marker['onmousedown']!="") { 
			$marker_output .= '
			google.maps.event.addListener(marker, "mousedown", function() {
				'.$marker['onmousedown'].'
			});
			';
		}
		if ($marker['onmouseout']!="") { 
			$marker_output .= '
			google.maps.event.addListener(marker, "mouseout", function() {
				'.$marker['onmouseout'].'
			});
			';
		}
		if ($marker['onmouseover']!="") { 
			$marker_output .= '
			google.maps.event.addListener(marker, "mouseover", function() {
				'.$marker['onmouseover'].'
			});
			';
		}
		if ($marker['onmouseup']!="") { 
			$marker_output .= '
			google.maps.event.addListener(marker, "mouseup", function() {
				'.$marker['onmouseup'].'
			});
			';
		}
		if ($marker['onrightclick']!="") { 
			$marker_output .= '
			google.maps.event.addListener(marker, "rightclick", function() {
				'.$marker['onrightclick'].'
			});
			';
		}
		
		if ($marker['draggable']) {
			if ($marker['ondrag']!="") { 
				$marker_output .= '
				google.maps.event.addListener(marker, "drag", function() {
					'.$marker['ondrag'].'
				});
				';
			}
			if ($marker['ondragend']!="") { 
				$marker_output .= '
				google.maps.event.addListener(marker, "dragend", function() {
					'.$marker['ondragend'].'
				});
				';
			}
			if ($marker['ondragstart']!="") { 
				$marker_output .= '
				google.maps.event.addListener(marker, "dragstart", function() {
					'.$marker['ondragstart'].'
				});
				';
			}
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
		
		$polyline['points'] = array();							// An array of latitude/longitude coordinates OR addresses, or a mixture of both. If an address is supplied the Google geocoding service will be used to return a lat/long.
		$polyline['clickable'] = TRUE;							// Defines if the polyline is clickable
		$polyline['strokeColor'] = '#FF0000';					// The hex value of the polylines color
		$polyline['strokeOpacity'] = '1.0';						// The opacity of the polyline. 0 to 1.0
		$polyline['strokeWeight'] = '2';						// The thickness of the polyline
		$polyline['onclick'] = '';								// JavaScript performed when a polyline is clicked
		$polyline['ondblclick'] = '';							// JavaScript performed when a polyline is double-clicked
		$polyline['onmousedown'] = '';							// JavaScript performed when a mousedown event occurs on a polyline
		$polyline['onmousemove'] = '';							// JavaScript performed when the mouse moves in the area of the polyline
		$polyline['onmouseout'] = '';							// JavaScript performed when the mouse leaves the area of the polyline
		$polyline['onmouseover'] = '';							// JavaScript performed when the mouse enters the area of the polyline
		$polyline['onmouseup'] = '';							// JavaScript performed when a mouseup event occurs on a polyline
		$polyline['onrightclick'] = '';							// JavaScript performed when a right-click occurs on a polyline
		$polyline['zIndex'] = '';								// The zIndex of the polyline. If two polylines overlap, the polyline with the higher zIndex will appear on top
		
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
    				strokeOpacity: '.$polyline['strokeOpacity'].',
    				strokeWeight: '.$polyline['strokeWeight'];
			if (!$polyline['clickable']) {
				$polyline_output .= ',
					clickable: false';
			}
			if ($polyline['zIndex']!="" && is_numeric($polyline['zIndex'])) {
				$polyline_output .= ',
					zIndex: '.$polyline['zIndex'];
			}
 			$polyline_output .= '
				});
				
				polyline_'.count($this->polylines).'.setMap('.$this->map_name.');

			';
			
			if ($polyline['onclick']!="") { 
				$polyline_output .= '
				google.maps.event.addListener(polyline_'.count($this->polylines).', "click", function() {
					'.$polyline['onclick'].'
				});
				';
			}
			if ($polyline['ondblclick']!="") { 
				$polyline_output .= '
				google.maps.event.addListener(polyline_'.count($this->polylines).', "dblclick", function() {
					'.$polyline['ondblclick'].'
				});
				';
			}
			if ($polyline['onmousedown']!="") { 
				$polyline_output .= '
				google.maps.event.addListener(polyline_'.count($this->polylines).', "mousedown", function() {
					'.$polyline['onmousedown'].'
				});
				';
			}
			if ($polyline['onmousemove']!="") { 
				$polyline_output .= '
				google.maps.event.addListener(polyline_'.count($this->polylines).', "mousemove", function() {
					'.$polyline['onmousemove'].'
				});
				';
			}
			if ($polyline['onmouseout']!="") { 
				$polyline_output .= '
				google.maps.event.addListener(polyline_'.count($this->polylines).', "mouseout", function() {
					'.$polyline['onmouseout'].'
				});
				';
			}
			if ($polyline['onmouseover']!="") { 
				$polyline_output .= '
				google.maps.event.addListener(polyline_'.count($this->polylines).', "mouseover", function() {
					'.$polyline['onmouseover'].'
				});
				';
			}
			if ($polyline['onmouseup']!="") { 
				$polyline_output .= '
				google.maps.event.addListener(polyline_'.count($this->polylines).', "mouseup", function() {
					'.$polyline['onmouseup'].'
				});
				';
			}
			if ($polyline['onrightclick']!="") { 
				$polyline_output .= '
				google.maps.event.addListener(polyline_'.count($this->polylines).', "rightclick", function() {
					'.$polyline['onrightclick'].'
				});
				';
			}
		
			array_push($this->polylines, $polyline_output);
			
		}
	
	}
	
	function add_polygon($params = array())
	{
		
		$polygon = array();
		
		$polygon['points'] = array();							// The positions (latitude/longitude coordinates OR addresses) at which the polygon points will appear. NOTE: The first and last elements of the array must be the same
		$polygon['clickable'] = TRUE;							// Defines if the polygon is clickable
		$polygon['strokeColor'] = '#FF0000';					// The hex value of the polygons border color
		$polygon['strokeOpacity'] = '0.8';						// The opacity of the polygon border. 0 to 1.0
		$polygon['strokeWeight'] = '2';							// The thickness of the polygon border
		$polygon['fillColor'] = '#FF0000';						// The hex value of the polygons fill color
		$polygon['fillOpacity'] = '0.3';						// The opacity of the polygons fill
		$polygon['onclick'] = '';								// JavaScript performed when a polygon is clicked
		$polygon['ondblclick'] = '';							// JavaScript performed when a polygon is double-clicked
		$polygon['onmousedown'] = '';							// JavaScript performed when a mousedown event occurs on a polygon
		$polygon['onmousemove'] = '';							// JavaScript performed when the mouse moves in the area of the polygon
		$polygon['onmouseout'] = '';							// JavaScript performed when the mouse leaves the area of the polygon
		$polygon['onmouseover'] = '';							// JavaScript performed when the mouse enters the area of the polygon
		$polygon['onmouseup'] = '';								// JavaScript performed when a mouseup event occurs on a polygon
		$polygon['onrightclick'] = '';							// JavaScript performed when a right-click occurs on a polygon
		$polygon['zIndex'] = '';								// The zIndex of the polygon. If two polygons overlap, the polygon with the higher zIndex will appear on top
		
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
    				strokeOpacity: '.$polygon['strokeOpacity'].',
    				strokeWeight: '.$polygon['strokeWeight'].',
					fillColor: "'.$polygon['fillColor'].'",
					fillOpacity: '.$polygon['fillOpacity'];
			if (!$polygon['clickable']) {
				$polygon_output .= ',
					clickable: false';
			}
			if ($polygon['zIndex']!="" && is_numeric($polygon['zIndex'])) {
				$polygon_output .= ',
					zIndex: '.$polygon['zIndex'];
			}
 			$polygon_output .= '
				});
				
				polygon_'.count($this->polygons).'.setMap('.$this->map_name.');

			';
			
			if ($polygon['onclick']!="") { 
				$polygon_output .= '
				google.maps.event.addListener(polygon_'.count($this->polygons).', "click", function() {
					'.$polygon['onclick'].'
				});
				';
			}
			if ($polygon['ondblclick']!="") { 
				$polygon_output .= '
				google.maps.event.addListener(polygon_'.count($this->polygons).', "dblclick", function() {
					'.$polygon['ondblclick'].'
				});
				';
			}
			if ($polygon['onmousedown']!="") { 
				$polygon_output .= '
				google.maps.event.addListener(polygon_'.count($this->polygons).', "mousedown", function() {
					'.$polygon['onmousedown'].'
				});
				';
			}
			if ($polygon['onmousemove']!="") { 
				$polygon_output .= '
				google.maps.event.addListener(polygon_'.count($this->polygons).', "mousemove", function() {
					'.$polygon['onmousemove'].'
				});
				';
			}
			if ($polygon['onmouseout']!="") { 
				$polygon_output .= '
				google.maps.event.addListener(polygon_'.count($this->polygons).', "mouseout", function() {
					'.$polygon['onmouseout'].'
				});
				';
			}
			if ($polygon['onmouseover']!="") { 
				$polygon_output .= '
				google.maps.event.addListener(polygon_'.count($this->polygons).', "mouseover", function() {
					'.$polygon['onmouseover'].'
				});
				';
			}
			if ($polygon['onmouseup']!="") { 
				$polygon_output .= '
				google.maps.event.addListener(polygon_'.count($this->polygons).', "mouseup", function() {
					'.$polygon['onmouseup'].'
				});
				';
			}
			if ($polygon['onrightclick']!="") { 
				$polygon_output .= '
				google.maps.event.addListener(polygon_'.count($this->polygons).', "rightclick", function() {
					'.$polygon['onrightclick'].'
				});
				';
			}
			
			array_push($this->polygons, $polygon_output);
			
		}
	
	}
	
	function add_circle($params = array())
	{
		
		$circle = array();
		
		$circle['center'] = '';									// The center position (latitude/longitude coordinate OR addresse) at which the circle will appear
		$circle['clickable'] = TRUE;							// Defines if the circle is clickable
		$circle['radius'] = 0;									// The circle radius (in metres)
		$circle['strokeColor'] = '0.8';							// The hex value of the circles border color
		$circle['strokeOpacity'] = '0.8';						// The opacity of the circle border
		$circle['strokeWeight'] = '2';							// The thickness of the circle border
		$circle['fillColor'] = '#FF0000';						// The hex value of the circles fill color
		$circle['fillOpacity'] = '0.3';							// The opacity of the circles fill
		$circle['onclick'] = '';								// JavaScript performed when a circle is clicked
		$circle['ondblclick'] = '';								// JavaScript performed when a circle is double-clicked
		$circle['onmousedown'] = '';							// JavaScript performed when a mousedown event occurs on a circle
		$circle['onmousemove'] = '';							// JavaScript performed when the mouse moves in the area of the circle
		$circle['onmouseout'] = '';								// JavaScript performed when the mouse leaves the area of the circle
		$circle['onmouseover'] = '';							// JavaScript performed when the mouse enters the area of the circle
		$circle['onmouseup'] = '';								// JavaScript performed when a mouseup event occurs on a circle
		$circle['onrightclick'] = '';							// JavaScript performed when a right-click occurs on a circle
		$circle['zIndex'] = '';									// The zIndex of the circle. If two circles overlap, the circle with the higher zIndex will appear on top
		
		$circle_output = '';
		
		foreach ($params as $key => $value) {
		
			if (isset($circle[$key])) {
			
				$circle[$key] = $value;
				
			}
			
		}
		
		if ($circle['radius']>0 && $circle['center']!="") {
			
			$lat_long_to_push = '';
			if ($this->is_lat_long($circle['center'])) {
				$lat_long_to_push = $circle['center'];
				$circle_output = '
				var circleCenter = new google.maps.LatLng('.$circle['center'].')
				';
			}else{
				$lat_long = $this->get_lat_long_from_address($circle['center']);
				$circle_output = '
				var circleCenter = new google.maps.LatLng('.$lat_long[0].', '.$lat_long[1].')';
				$lat_long_to_push = $lat_long[0].', '.$lat_long[1];
			}
			$circle_output .= '
				lat_longs.push(new google.maps.LatLng('.$lat_long_to_push.'));
			';
			
			$circle_output .= '
				var circleOptions = {
					strokeColor: "'.$circle['strokeColor'].'",
					strokeOpacity: '.$circle['strokeOpacity'].',
					strokeWeight: '.$circle['strokeWeight'].',
					fillColor: "'.$circle['fillColor'].'",
					fillOpacity: '.$circle['fillOpacity'].',
					map: '.$this->map_name.',
					center: circleCenter,
					radius: '.$circle['radius'];
			if (!$circle['clickable']) {
				$circle_output .= ',
					clickable: false';
			}
			if ($circle['zIndex']!="" && is_numeric($circle['zIndex'])) {
				$circle_output .= ',
					zIndex: '.$circle['zIndex'];
			}
 			$circle_output .= '
				};
				var circle_'.count($this->circles).' = new google.maps.Circle(circleOptions);
			';
			
			if ($circle['onclick']!="") { 
				$circle_output .= '
				google.maps.event.addListener(circle_'.count($this->circles).', "click", function() {
					'.$circle['onclick'].'
				});
				';
			}
			if ($circle['ondblclick']!="") { 
				$circle_output .= '
				google.maps.event.addListener(circle_'.count($this->circles).', "dblclick", function() {
					'.$circle['ondblclick'].'
				});
				';
			}
			if ($circle['onmousedown']!="") { 
				$circle_output .= '
				google.maps.event.addListener(circle_'.count($this->circles).', "mousedown", function() {
					'.$circle['onmousedown'].'
				});
				';
			}
			if ($circle['onmousemove']!="") { 
				$circle_output .= '
				google.maps.event.addListener(circle_'.count($this->circles).', "mousemove", function() {
					'.$circle['onmousemove'].'
				});
				';
			}
			if ($circle['onmouseout']!="") { 
				$circle_output .= '
				google.maps.event.addListener(circle_'.count($this->circles).', "mouseout", function() {
					'.$circle['onmouseout'].'
				});
				';
			}
			if ($circle['onmouseover']!="") { 
				$circle_output .= '
				google.maps.event.addListener(circle_'.count($this->circles).', "mouseover", function() {
					'.$circle['onmouseover'].'
				});
				';
			}
			if ($circle['onmouseup']!="") { 
				$circle_output .= '
				google.maps.event.addListener(circle_'.count($this->circles).', "mouseup", function() {
					'.$circle['onmouseup'].'
				});
				';
			}
			if ($circle['onrightclick']!="") { 
				$circle_output .= '
				google.maps.event.addListener(circle_'.count($this->circles).', "rightclick", function() {
					'.$circle['onrightclick'].'
				});
				';
			}
			
			array_push($this->circles, $circle_output);
			
		}
	
	}
	
	function create_map()
	{
	
		$this->output_js = '';
		$this->output_js_contents = '';
		$this->output_html = '';
		
		$this->output_js .= '
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor='.$this->sensor;
		if ($this->region!="" && strlen($this->region)==2) { $this->output_js .= '&region='.strtoupper($this->region); }
		if ($this->adsense!="") { $this->output_js .= '&libraries=adsense'; }
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
			';
		if ($this->directions) { 
			$this->output_js_contents .= 'var directionsDisplay = new google.maps.DirectionsRenderer();
			var directionsService = new google.maps.DirectionsService();
			';
		}
		if ($this->adsense) { 
			$this->output_js_contents .= 'var adUnit;
			'; 
		}
		
		$this->output_js_contents .= 'function initialize() {
				
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
		if ($this->directions) {
			$this->output_js_contents .= 'directionsDisplay.setMap('.$this->map_name.');
			';
			if ($this->directionsDivID!="") {
				$this->output_js_contents .= 'directionsDisplay.setPanel(document.getElementById("'.$this->directionsDivID.'"));
			';
			}
		}
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
		
		// add circles
		if (count($this->circles)) {
			foreach ($this->circles as $circle) {
				$this->output_js_contents .= $circle;
			}
		}	
		//
		
		if ($this->zoom=="auto") { 
			$this->output_js_contents .= '
			var bounds = new google.maps.LatLngBounds();
			if (lat_longs.length>0) {
				for (var i=0; i<lat_longs.length; i++) {
					bounds.extend(lat_longs[i]);
				}
				'.$this->map_name.'.fitBounds(bounds);
			}
			';
		}
		
		if ($this->adsense) { 
			$this->output_js_contents .= '
			var adUnitDiv = document.createElement("div");

		    // Note: replace the publisher ID noted here with your own
		    // publisher ID.
		    var adUnitOptions = {
		    	format: google.maps.adsense.AdFormat.'.$this->adsenseFormat.',
		    	position: google.maps.ControlPosition.'.$this->adsensePosition.',
		    	publisherId: "'.$this->adsensePublisherID.'",
		    	';
		    if ($this->adsenseChannelNumber!="") { $this->output_js_contents .= 'channelNumber: "'.$this->adsenseChannelNumber.'",
		    	'; }
		    $this->output_js_contents .= 'map: map,
		    	visible: true
		    };
		    adUnit = new google.maps.adsense.AdUnit(adUnitDiv, adUnitOptions);
		    ';
		}
		
		if ($this->directions && $this->directionsStart!="" && $this->directionsEnd!="") {
			$this->output_js_contents .= '
				calcRoute(\''.$this->directionsStart.'\', \''.$this->directionsEnd.'\');
			';
		}
		
		$this->output_js_contents .= '
			
			}
		
		';
		
		if ($this->directions) {
			
			$this->output_js_contents .= 'function calcRoute(start, end) {
			var request = {
			    	origin:start,
			    	destination:end,
			    	travelMode: google.maps.TravelMode.'.$this->directionsMode.'
			    	';
			if ($this->region!="" && strlen($this->region)==2) { 
				$this->output_js_contents .= ',region: '.strtoupper($this->region).'
					'; 
			}
			if ($this->directionsAvoidTolls) { 
				$this->output_js_contents .= ',avoidTolls: true
					'; 
			}
			if ($this->directionsAvoidHighways) { 
				$this->output_js_contents .= ',avoidHighways: true
					'; 
			}
			
			$this->output_js_contents .= '
			};
			  	directionsService.route(request, function(response, status) {
			    	if (status == google.maps.DirectionsStatus.OK) {
			      		directionsDisplay.setDirections(response);
			    	}else{
			    		switch (status) { 	
			    			case "NOT_FOUND": { alert("Either the start location or destination were not recognised"); break }
			    			case "ZERO_RESULTS": { alert("No route could be found between the start location and destination"); break }
			    			case "MAX_WAYPOINTS_EXCEEDED": { alert("Maximum waypoints exceeded. Maximum of 8 allowed"); break }
			    			case "INVALID_REQUEST": { alert("Invalid request made for obtaining directions"); break }
			    			case "OVER_QUERY_LIMIT": { alert("This webpage has sent too many requests recently. Please try again later"); break }
			    			case "REQUEST_DENIED": { alert("This webpage is not allowed to request directions"); break }
			    			case "UNKNOWN_ERROR": { alert("Unknown error with the server. Please try again later"); break }
			    		}
			    	}
			  	});
			}
			';
			
		}
		
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