In this Skyscrapers profile, please note the following files:

* _init.php
  This file is automatically included before all other template files.
  We use it to initialize some variables and includes. 

* _out.php
  This file is automatically included after all other template files.
  We use it as our main markup file to output everything. 

* includes/skyscrapers.php
  We have placed our common skyscraper finding and rendering functions
  in this file. You will see reference to its functions in most other
  template files here.

This Skyscrapers profile is different from the basic profile in that
we are using our template files to populate variables that will be 
output by _$out.php. Whereas, the basic profile uses head/foot 
include files and each template directly outputs the bodycopy.

These are the variables that each template file populates in this
profile:

* $browserTitle - This is what appears in the <title> tag.
* $headline - The primarily H1 headline of the page.
* $content - The page's main content/bodycopy area. 

The above variables are ultimately output by _out.php. 

