# elementor-widget-creator-library
**This is not a plugin** It is for developers who wish to quickly create plugins specifically for Elementor.

This class which serves as a simplified method to easily and quickly create widgets for WordPress page builder plugin called Elementor. Simply create widget form fields using a XML markup instead of writing complex PHP.

## Method
* Place file `widgetloader.php` and `constants.php` in `wp-content/mu-plugins` to autoload the class.
* Place directory `elementor-widgets` in `wp-content`

**Note** if widgets are created for distribution, `widgetloader.php` must be included, so for that purpose it is best to place the file in the plugin package for the purpose of updating accurately, then load it via the plugin pilot file.

That's it! All ready to use.

## Notes
If the directory paths will be changed from the default structure, edit `constants.php` and change the array values accordingly

```php
$const = (object)[
	// set the prefered prefix which displays on the widget button 
	// and title bar of the widget when in edit view
	'widgetprefix'	=>	'CMSE',
	
	// widgets panel section ID, icon and name
	'panelid'		=>	'cmse-widgetcat',
	'panelcat'		=>	'CMSE Widgets',
	'panelicon'		=>	'fa fa-plug',
	
	// text domain for the language ID
	'textdomain'	=>	'plugin-name',
	
	// paths
	'widgetpath'	=>	WP_CONTENT_DIR.'/elementor-widgets/widgets/',
	'customcontrol'	=>	WP_CONTENT_DIR.'/elementor-widgets/controls',
	
	// if custom separator shapes are in use set path to elementor's separator folder
	// and the path to your source where the shapes are stored
	'copydir'		=>	WP_CONTENT_DIR.'/elementor-widgets/svg',
	'shapesdir'		=>	WP_PLUGIN_DIR.'/elementor/assets/shapes',
	'cssfileurl'	=>	get_bloginfo('url').'/wp-content/elementor-widgets/elementor-alt.css',
	'cssid'			=>	'elementor-alt'
	];
```

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
<form icon="eicon-image" cat="basic">
 <fields>
  <fieldset id="animage" tab="tab" note="This is text description of the panel" label="An Image">
   <field 
   type="text" 
   name="thefieldname" 
   label="Image Title" 
   note="Some details about the field" 
   hint="The place holder text" 
   default="A default value" 
   />
   <field 
   type="list" 
   name="thelist" 
   options="{'':'None','choice':'The Choice','newchoice':'New Choice'}" 
   default="choice" 
   label="The List" 
   />
   <field 
   type="textarea" 
   name="thedescription" 
   condition="{'thelist':'newchoice'}" 
   label="Description" 
   labelblock="1" 
   note="Some details about the field" 
   hint="The place holder text" 
   default="A default value" 
   />
  </fieldset>
 </fields>
</form>
```
Elementor icons at https://elementor.github.io/elementor-icons/

![example-xml-field](https://github.com/WebsiteDons/elementor-widget-creator-library/blob/main/newly-added-widget.jpg?raw=true "Example output")

![example-xml-field](https://github.com/WebsiteDons/elementor-widget-creator-library/blob/main/example-xml-field.jpg?raw=true "Example output")

### Comparison to PHP method

To get the same controls result using the PHP methods would be

```php
protected function _register_controls() 
{
	$this->start_controls_section('animage',
	[
	'label'=>'An Image',
	'tab'=>\Elementor\Controls_Manager::TAB_CONTENT
	]);
	
	$this->add_control('thefieldname', 
	[
	'type' => \Elementor\Controls_Manager::TEXT,
	'label' => 'Image Title',
	'description' => 'Some details about the field',
	'placeholder' => 'The place holder text',
	'default' => 'A default value'
	]);
	
	$this->end_controls_section();
}
```

## XML Markup Attributes

**Form attribs**
* icon - required to properly display the widget in the list (see above link for elementor icons)
* cat - required to determine the panel section where the widget will display. 
    * Elementor core panel categories are - basic | pro-elements | general | wordpress | woocommerce-elements
    * Your custom category must be set via constants **PANELID** and **PANELCAT** and **PANELICON**

**Fieldset Attribs**
* id - absolutely required to create unique panel ID
* tab - required to determine the tabbed group //available values -  tab | tabstyle | tabadv
* label - required for the panel title
* note - to add text description immediatedly below the panel handle

**Field Attribs**
* type - the field type
* name - the field name
* label - the field label
* description - text displayed below a field in admininistration
* hint - the placeholder attribute shorthand
* default - a default value set
* labelblock - an instruction to format the field and label vertically instead of default inline
* options - for select fieltype
* args - used in assocition with options if options are from a function
* note - text displayed before a field for various instructions
* min - used for number field
* max - used for number fields
* gtype - used for group controls to define the field type
* gtypes - used for various group controls to add variations
* selectors - used to apply array multiple value to {{WRAPPER}}
* selector - similar to previous but single value

## Field Types
* text
* textarea
* list - standard select field
* list2 - select2 select field with search
* num - number field type
* date - output date picker
* hr - a horizontal ruler for separation
* color - color chooser
* radio - elemetor switcher
* check - standard checkbox
* rich - WYSIWYG editor
* img - image/media selector
* hidden - standard hidden field
* url - elemetor ulr function
* choose - elementor choose switch
* gal - elementor gallery
* anim - elementor animation
* icon - elementor icon selector
* code - code editor
* font - font selector
* repeat - repeater fields
* controlgroup - trigger group controls type
* typo - elementor typography group control
* border - border group control
* shadow - box shadow group controls
* shadowtext - text shadow group controls
* cssfilter - css filter group controls

# Creating a distributable plugin

If creating a distributable plugin, do not place the `widgetloader.php` file in mu-plugins. Follow the below structure. Be sure to set the paths in the constants and load the class via file include within the plugin pilot file. EG: `include_once __DIR__.'/includes/widgetloader.php';`

**Directory structure**

* plugin-name
    * includes
        * copy
            * widget.php
            * widget.xml
            * display.php
        * widgetloader.php 
        * constants.php
    * widgets
        * image
            * image.php
            * image.xml
            * display.php
        * countdown
            * countdown.php
            * countdown.xml
            * display.php 
    * languages
        * plugin-name.pot
        * plugin-name-en_EN.po
        * plugin-name-en_EN.mo
    * plugin-name.php
    
### Coding for plugin pilot

`plugin-name.php`

```php
<?php
/**
Plugin Name: My Great Plugin Name
Plugin URI: https://mysite.com
Description: A package of great Elementor widgets.
Version: 1.0.0
Author: Me
License:     GPLv2 or later
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
Requires at least: 4.9
Requires PHP: 5.2.6
*/

defined('ABSPATH') || exit('A message to bad guys who try to access this file directly');

//load widgetloader class
include_once __DIR__.'/includes/widgetloader.php';

/*
That's it!
No need to register the plugin if no setting options will be applied. 
Add new Elementor widgets to the widget directory and update the version value of the pilot file at the top where Version: 1.0.1
If distributed by WordPress repository, once the updated package is submitted, all users will be alerted
*/
```

### Edit contant array values in below file

---

`includes/constants.php`

---
