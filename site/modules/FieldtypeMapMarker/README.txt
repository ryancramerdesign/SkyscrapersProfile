FieldtypeMapMarker Module for ProcessWire 2.1+
==============================================

This Fieldtype for ProcessWire 2.1+ holds an address or location name, and automatically
geocodes the address to latitude/longitude using Google Maps API. 

This Fieldtype was created to serve as an example of creating a custom Fieldtype and 
Inputfield that contains multiple pieces of data. 

This Fieldtype has a support forum and the most up-to-date instructions at:
http://processwire.com/talk/index.php/topic,752.0.html


HOW TO USE
----------
To use, install FieldtypeMapMarker. Then create a new field that uses it. Add that field
to a template and edit a page using that template. Enter an address, place or location of
any sort into the 'Address' field, for example, Google Maps will geocode any of these:

- 125 E. Court Square, Decatur, GA 30030
- Atlanta, GA
- Disney World

Hit save, and the address will be converted into latitude/longitude coordinates. The 
field will also show a map of the location once it has the coordinates. 

On the front end, you can utilize this data for your own Google Maps (or anything else
that you might need latitude/longitude for). 

Lets assume that your field is called 'marker'. Here is how you would access the
components of it from the API:

echo $page->marker->address; 	// outputs the address you entered
echo $page->marker->lat; 	// outputs the latitude
echo $page->marker->lng; 	// outputs the longitude


PLANNED IMPROVEMENTS
--------------------

1. Automatic determination of the appropriate Google Maps zoom setting to accompany the
   latitude and longitude. 

2. The option to automatically output a map of the location on the front-end. This would
   be for those that want instant output rather than to use it for their own output.

3. The ability to drag the marker and update the lat/lng automatically.

4. The ability to perform a reverse geocode. 


Copyright 2011 by Ryan Cramer 

