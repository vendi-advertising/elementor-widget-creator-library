# elementor-widget-creator-library
This class which serves as a simplified method to easily and quickly create widgets for WordPress page builder plugin called Elementor

## Method
Place file `widgetloader.php` in `wp-content/mu-plugins` to autoload the class
Place directory `elementor-widgets` in `wp-content`

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
