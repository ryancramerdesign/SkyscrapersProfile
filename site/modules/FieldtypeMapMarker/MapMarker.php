<?php

/**
 * Class to hold an address and geocode it to latitude/longitude
 *
 */
class MapMarker extends WireData {

	const statusNoGeocode = -100;

	protected $geocodeStatuses = array(

		0 => 'N/A',
		1 => 'OK',
		2 => 'OK_ROOFTOP', 
		3 => 'OK_RANGE_INTERPOLATED',
		4 => 'OK_GEOMETRIC_CENTER',
		5 => 'OK_APPROXIMATE',

		-1 => 'UNKNOWN',
		-2 => 'ZERO_RESULTS',
		-3 => 'OVER_QUERY_LIMIT',
		-4 => 'REQUEST_DENIED',
		-5 => 'INVALID_REQUEST',

		-100 => 'Geocode OFF', // RCD

		);

	protected $geocodedAddress = '';

	public function __construct() {
		$this->set('lat', '');
		$this->set('lng', '');
		$this->set('address', ''); 
		$this->set('status', 0); 
	}

	public function set($key, $value) {

		if($key == 'lat' || $key == 'lng') {
			// if value isn't numeric, then it's not valid: make it blank
			if(strpos($value, ',') !== false) $value = str_replace(',', '.', $value); // convert 123,456 to 123.456
			if(!is_numeric($value)) $value = '';	

		} else if($key == 'address') {
			$value = wire('sanitizer')->text($value);

		} else if($key == 'status') { 
			$value = (int) $value; 
			if(!isset($this->geocodeStatuses[$value])) $value = -1; // -1 = unknown
		}

		return parent::set($key, $value);
	}

	public function get($key) {
		if($key == 'statusString') return str_replace('_', ' ', $this->geocodeStatuses[$this->status]); 
		return parent::get($key);
	}

	public function geocode() {

		// check if address was already geocoded
		if($this->geocodedAddress == $this->address) return $this->status; 
		$this->geocodedAddress = $this->address;

		if(!ini_get('allow_url_fopen')) {
			$this->error("Geocode is not supported because 'allow_url_fopen' is disabled in PHP"); 
			return 0;
		}

		$url = "http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=" . urlencode($this->address);
		$json = file_get_contents($url);
		$json = json_decode($json, true);

		if(empty($json['status']) || $json['status'] != 'OK') {
			$this->error("Error geocoding address");
			if(isset($json['status'])) $this->status = (int) array_search($json['status'], $this->geocodeStatuses);
				else $this->status = -1; 
			$this->lat = 0;
			$this->lng = 0;
			return $this->status; 
		}

		$geometry = $json['results'][0]['geometry'];
		$location = $geometry['location'];
		$locationType = $geometry['location_type'];

		$this->lat = $location['lat'];
		$this->lng = $location['lng'];

		$statusString = $json['status'] . '_' . $locationType; 
		$status = array_search($statusString, $this->geocodeStatuses); 
		if($status === false) $status = 1; // OK	

		$this->status = $status; 
		$this->message("Geocode {$this->statusString}: '{$this->address}'"); 

		return $this->status; 
	}

	/**
	 * If accessed as a string, then just output the lat, lng coordinates
	 *
	 */
	public function __toString() {
		return "{$this->address} ({$this->lat}, {$this->lng}) [{$this->statusString}]";
	}

}



