# elementor-widget-creator-library
**This is not a plugin** It is for developers who wish to quickly create plugins specifically for Elementor.

This class which serves as a simplified method to easily and quickly create widgets for WordPress page builder plugin called Elementor. Simply create widget form fields using a XML markup instead of writing complex PHP.

## Method
* Place file `widgetloader.php` in `wp-content/mu-plugins` to autoload the class.
* Place directory `elementor-widgets` in `wp-content`

That's it! All ready to use.

## Notes
If the directory paths will be changed from the default structure, edit `widgetloader.php` and change the `define()` constant values accordingly

```
define('CSSFILEURL', get_bloginfo('url').'/wp-content/elementor-widgets/elementor-alt.css');
define('CSSID', 'elementor-alt');
define('WIDGETPATH', WP_CONTENT_DIR.'/elementor-widgets/widgets/');
define('CUSTOMCTRL', WP_CONTENT_DIR.'/elementor-widgets/controls');
define('EMENTOR', WP_PLUGIN_DIR.'/elementor');
define('SHAPESDIR', EMENTOR.'/assets/shapes');
define('COPYDIR', WP_CONTENT_DIR.'/elementor-widgets/svg');
define('PANELID', 'cmse-widgetcat');
define('PANELCAT', 'CMSE Widgets'); 
define('PANELICON', 'fa fa-plug');
```

See widget development documentation at https://developers.elementor.com/elementor-controls/

## Creating Widgets
Add widgets to `wp-content/elementor-widgets/widgets` If a prefereed location is desired, be sure to set the path in the above noted constants

Each widget folder will consist of 2 files with exact matching names of the folder and one display ie: 
* image
 * image.php
 * image.xml
 * display.php
 
 See the included demo files `elementor-widgets/widgets/image`
 
 ## XML Structure
 
```xml
<?xml version="1.0" encoding="utf-8"?>
<form icon="eicon-countdown" cat="basic">
	<fields>
		<fieldset id="cmse_countdown" tab="tab" label="Clock Settings">
  </fieldset>
 </fields>
</form>
```
 
 
