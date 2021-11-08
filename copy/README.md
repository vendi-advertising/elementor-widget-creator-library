These files are the widget auto-builders. An empty folder can be placed in the widget dirctory and the library function will create the required files and you just need to edit the form controls with the XML file and the front end output file `display.php`

### Snippet of the auto-generation process

```php
// Load And Register Widgets Detected
add_action('elementor/widgets/widgets_registered', function() 
{
	// load global constants variable
	global $cmse;
	
	$mgr = \Elementor\Plugin::instance()->widgets_manager;

	foreach($this->folderlist($cmse('widgetpath')) as $w) 
	{
		//make widget pilot file if not exists
		if( !file_exists($cmse('widgetpath').$w.'/'.$w.'.php') ) {
			$getfile = file_get_contents(__DIR__.'/copy/widget.php');
			$getfile = str_replace('widgetname', $w, $getfile);
			file_put_contents($cmse('widgetpath').$w.'/'.$w.'.php', $getfile);
		}
		// make XML file if not exists
		if( !file_exists($cmse('widgetpath').$w.'/'.$w.'.xml') ) {
			$getfile = file_get_contents(__DIR__.'/copy/widget.xml');
			$getfile = str_replace('basic', $cmse('panelid'), $getfile);
			file_put_contents($cmse('widgetpath').$w.'/'.$w.'.xml', $getfile);
		}
		// make display file if not exists
		if( !file_exists($cmse('widgetpath').$w.'/display.php') ) {
			$getfile = file_get_contents(__DIR__.'/copy/display.php');
			$getfile = str_replace('widgetname', $cmse('panelid'), $getfile);
			file_put_contents($cmse('widgetpath').$w.'/display.php', $getfile);
		}
		require_once($cmse('widgetpath').$w.'/'.$w.'.php');
		$class = '\Cmse_'.$w.'_Widget';
		$mgr->register_widget_type(new $class());
	}
});
```

## Note

Widget folder names **must** 
* be lowercase
* not include spaces
* not include special characters except underscore
