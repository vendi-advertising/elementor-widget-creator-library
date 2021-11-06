# elementor-widget-creator-library
**This is not a plugin** It is for developers who wish to quickly create plugins specifically for Elementor.

This class which serves as a simplified method to easily and quickly create widgets for WordPress page builder plugin called Elementor. Simply create widget form fields using a XML markup instead of writing complex PHP.

## Method
* Place file `widgetloader.php` in `wp-content/mu-plugins` to autoload the class.
* Place directory `elementor-widgets` in `wp-content`

**Note** if widgets are created for distribution, `widgetloader.php` must be included, so for that purpose it is best to place the file in the plugin package for the purpose of updating accurately, then load it via the plugin pilot file.

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

## XML Markup Attributes

**Form attribs**
* icon - required to properly display the widget in the list (see above link for elementor icons)
* cat - required to determine the panel section where the widget will display

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
