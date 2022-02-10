Track da files module
------------------------
by Federiko_ from Koriolis, drupal.org/node/1142418


Description
-----------

Track da files module enables the possibility to track
how much visitors are viewing files on site.

Every link to a file can be configured to be tracked. 
A record is registered each time a visitor access the file
from this specific link. 

Configure file fields to be tracked in content interface, 
by selecting the corresponding display.

Links to be tracked can also be integrated in templates
or html source of contents, by customizing links. 

This module works with public and private file system. 


Features
-----

- Diferents availables reports : main report, reports by file, reports by user.

- Datas provided : displays count, total ips by file, average ips by file, 
date of last display, users having displayed file, browser used, 
internal or external URI where the link was visited.

- Enable and disable datas which you don’t want to appear in reports

- Export all datas in CSV files

- Clear datas by file or by user. 

- Rules integration

- Colorbox integration (by file, no gallery)


Installation 
------------

 * Copy the module's directory to your modules directory
 and activate the module.
 
 * Install it here : admin/modules
 
 * View reports here : admin/reports/track_da_files
 
 * Configure Track da files module here : admin/config/media/track_da_files


How it works
-----
  
 1. The easiest way to proceed is to activate the counting of displays
 for a specific file field.
 
 For a specific content type, select one of these display formats
 for the field : 
 
 "tdf: Generic File"
 "tdf: Table of files"
 "tdf: Image"
 or if colorbox is enabled "tdf : Colorbox image".     
 
 2. Another way to activate file displays tracking is to adapt your link
 to files in the wysiwyg or in the code source of custom templates 
 and modules.   
       
 * The links should look like this for public files:  
	  
  http://www.domain.com/system/tdf/[file path]/?file=1&type=node&id=457 
	-> In this example we want to track a file related to node 457 : 
	the two last parameters are entity id and entity type 	
  
  If you want to track a file attached to a comment, 
  type will be comment and id the cid of the comment : 
  
  http://www.domain.com/system/tdf/[file path]/?file=1&type=comment&id=186   
  
  This will work too without related content parameters
  (no related content will be recorded) :
  
  http://www.domain.com/system/tdf/[file path]/?file=1 
  
  [file path] have to be replaced with the path to the file
  under the files directory : 
   
    - the filename if you file is put in the root of the files directory.
    
    - docs/[file name] if the file is in the docs directory
    under the files directory

  Example : 
  domain.com/system/tdf/docs/myfile.jpg/?file=1&type=node&id=457 
 
  - Implementation in PHP with Drupal l() function :  
 
  l('Some link text','system/tdf/[file path]', 
  array('query' => array('file' => 1, 'type' => 'node', 'id' => 457)));

  * For private files the links to the file to be tracked
  should look like this : 
 
  http://www.domain.com/system/files/[file path]/?file=1&type=node&id=457
  
  - Implementation in PHP with Drupal l() function :  
  
  l('Some link text','system/files/[file path]',
  array('query' => array('file' => 1, 'type' => 'node','id' => 457)));
