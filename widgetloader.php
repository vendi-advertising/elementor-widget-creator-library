<?php
/*
Plugin Name: CMSE Elementor Widget Developer Library
Plugin URI: http://www.cmsenergizer.com
Description: Develop widgets for Elementor with intuitive markup
Version: 1.0
Author: CMSEnergizer.com
Author URI: http://www.cmsenergizer.com
Copyright 2014 CMSEnergizer.com
https://developers.elementor.com/elementor-controls/
*/

defined('ABSPATH') || exit('CMSEnergizer.com');

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


// load shpaes class
use \Elementor\Shapes;

final class Cmse_Elementor_Widgets 
{
	const VERSION = '1.0.0';
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';
	const MINIMUM_PHP_VERSION = '7.0';

	private static $_instance = null;

	public static function instance()
	{
		if( is_null(self::$_instance) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function __construct()
	{
		add_action('plugins_loaded', function()
		{
			if( $this->is_compatible() )
			{
				add_action('elementor/init', function()
				{
					// Load And Register Widgets Detected
					add_action('elementor/widgets/widgets_registered', function() 
					{
						$mgr = \Elementor\Plugin::instance()->widgets_manager;

						foreach($this->folderlist(WIDGETPATH) as $w) 
						{
							require_once(WIDGETPATH.$w.'/'.$w.'.php');
							$class = '\Cmse_'.$w.'_Widget';
							$mgr->register_widget_type(new $class());
						}
					});
					
					// Add Widget category to the editor slide panels to separate custom widgets
					add_action('elementor/elements/categories_registered', function($cat) 
					{
						$cat->add_category(PANELID,[
						'title'=>PANELCAT,
						'icon'=>PANELICON,
						'active'=>false
						]);
					});
					
					/* register custom controls if exists
					See development docs at https://developers.elementor.com/creating-a-new-control/
					*/
					add_action('elementor/controls/controls_registered', function() {
						$mgr = \Elementor\Plugin::instance()->controls_manager;
						require_once(CUSTOMCTRL.'/control.php');
						$mgr->register_control(\Cmse_FG::FG, new \Cmse_FG());
					});
					
					
					/* add section separator custom shapes
					upload svg files to elementor/assets/shapes
					see elementor/includes/shapes.php
					*/
					add_filter('elementor/shapes/additional_shapes', function($separator)
					{
						$shapes = $this->filelist(COPYDIR.'/','svg');
						$sep=[];
						if( !empty($shapes) ) 
						{
							foreach($shapes as $shape)
							{
								/*
								retain a directory somewhere outside of the Elementor directory where the custom 
								shapes will be held for copy. This is due to the method of folder delete when 
								WordPress updates a plugin. All files are deleted
								*/
								if( !file_exists(SHAPESDIR.'/'.$shape) )
									$this->copyFile(COPYDIR.'/'.$shape, SHAPESDIR.'/'.$shape);

								$shape = str_replace('.svg','',$shape);
								// set selector controls options
								$sep[$shape] = [
								'title'=>ucfirst($shape),
								'has_flip'=>true,
								'has_negative' => true
								];
							}
						}

						$separator = $sep;

						return $separator;
					}); // EOF filter elementor/shapes/additional_shapes


					// add css when in elementor editor for various adjustments if needed
					add_action('elementor/editor/after_enqueue_styles', function() {
						wp_register_style(CSSID, CSSFILEURL,[], self::VERSION);
						wp_enqueue_style(CSSID);
					});

				}); // EOF action elementor/init
			} // EOF is_compatible() check
		}); // EOF action plugins_loaded
	} // EOF __construct()



	## Define Elementor core controls with minimal type ID
	// just for simplification
	protected function ctr()
	{
		$v = (object)[
		'tab'=>\Elementor\Controls_Manager::TAB_CONTENT,
		'tabstyle'=>\Elementor\Controls_Manager::TAB_STYLE,
		'tabadv'=>\Elementor\Controls_Manager::TAB_ADVANCED,
		'text'=>\Elementor\Controls_Manager::TEXT,
		'textarea'=>\Elementor\Controls_Manager::TEXTAREA,
		'rich'=>\Elementor\Controls_Manager::WYSIWYG,
		'list'=>\Elementor\Controls_Manager::SELECT,
		'list2'=>\Elementor\Controls_Manager::SELECT2,
		'img'=>\Elementor\Controls_Manager::MEDIA,
		'date'=>\Elementor\Controls_Manager::DATE_TIME,
		'radio'=>\Elementor\Controls_Manager::SWITCHER,
		'url'=>\Elementor\Controls_Manager::URL,
		'num'=>\Elementor\Controls_Manager::NUMBER,
		'repeat'=>\Elementor\Controls_Manager::REPEATER,
		'color'=>\Elementor\Controls_Manager::COLOR,
		'hidden'=>\Elementor\Controls_Manager::HIDDEN,
		'quad'=>\Elementor\Controls_Manager::DIMENSIONS,
		'code'=>\Elementor\Controls_Manager::CODE,
		'font'=>\Elementor\Controls_Manager::FONT,
		'icon'=>\Elementor\Controls_Manager::ICONS,
		'anim'=>\Elementor\Controls_Manager::ANIMATION,
		'animhover'=>\Elementor\Controls_Manager::HOVER_ANIMATION,
		'slider'=>\Elementor\Controls_Manager::SLIDER,
		'choose'=>\Elementor\Controls_Manager::CHOOSE,
		'readme'=>\Elementor\Controls_Manager::RAW_HTML,
		'gal'=>\Elementor\Controls_Manager::GALLERY,
		'hr'=>\Elementor\Controls_Manager::DIVIDER,
		// control groups
		'bg'=>\Elementor\Group_Control_Background::get_type(),
		'border'=>\Elementor\Group_Control_Border::get_type(),
		'shadow'=>\Elementor\Group_Control_Box_Shadow::get_type(),
		'shadowtxt'=>\Elementor\Group_Control_Text_Shadow::get_type(),
		'typo'=>\Elementor\Group_Control_Typography::get_type(),
		'cssfilter'=>\Elementor\Group_Control_Css_Filter::get_type(),
		'rpt' => new \Elementor\Repeater()
		];

		return $v;
	}

	## Load Fields From XML Markup
	public static function fields($xml,$obj)
	{
		$formfile = simplexml_load_file(WIDGETPATH.$xml.'/'.$xml.'.xml');
		$form = $formfile->attributes();
		$icon = (string)$form->icon;
		$cat = (string)$form->cat;
		$fields= $tabstart= $tabend='';
		$f = $this->ctr();

		foreach($formfile->fields->fieldset as $fieldset)
		{
			$fset = $fieldset->attributes();
			$fsetlbl = (string)$fset->label;
			$fsetid = (string)$fset->id;
			$tabtype = (string)$fset->tab;
			$fsetnote = (isset($fset->note) ? '<div class="tabnote">'.(string)$fset->note.'</div>':null);

			$obj->start_controls_section($fsetid,['label'=>$fsetlbl,'tab'=>$f->$tabtype]);
			if( !empty($fsetnote) ) 
			{
				$obj->add_control($fsetid.'_note',['type'=>$f->readme,'raw'=>$fsetnote]);
				dd_action('elementor/editor/footer', function() use($fsetid) 
				{
					echo '
					<style>
					.elementor-control-'.$fsetid.'_note {
					padding:0 !important; 
					padding-bottom: 8px !important;
					}
					.elementor-control-'.$fsetid.' {
					padding-bottom: 0 !important;
					}
					</style>';
				});
			}

			$i='';
			foreach($fieldset->field as $field) 
			{
				$i++;
				$att = $field->attributes();
				$type = (string)$att->type;
				$name = (string)$att->name;
				$lbl = (isset($att->label) ? (string)$att->label:null);
				$hint = (isset($att->hint) ? (string)$att->hint:null);
				$def = (isset($att->default) ? (string)$att->default:null);
				$gtype = (isset($att->gtype) ? (string)$att->gtype:null);
				$gtypes = (isset($att->gtypes) ? explode(',',(string)$att->gtypes):null);
				$note = (isset($att->note) ? (string)$att->note:null);
				$separator = (isset($att->line) ? (string)$att->line:null);
				$configs = (isset($att->configs) ? (string)$att->configs:null);
				$valueformat = (isset($att->valueformat) ? (string)$att->valueformat:null);
				$lblblock = (isset($att->labelblock) ? true:null);

				// prepare options
				// if the function does not require arguments, set the args="" attribute to any value
				// just to set it
				$options=[];
				if( !empty($att->options) ) 
				{
					$arr = (string)$att->options;
					if( strstr($arr, '::') && !empty($att->args) ) {
						$options = call_user_func($arr,(string)$att->args);
					}else
					if( strstr($arr, '|') ) 
					{
						$elements = explode(',', $arr);
						foreach($elements as $element) {
							list($optval,$optlbl) = explode('|',$element);
							$options[$optval] = $optlbl;
						}
					}
				}

				//selectors
				$selectors=[];
				if( isset($att->selectors) ) {
					list($classes,$cssval) = explode('|',(string)$att->selectors);
					$selectors[$classes] = $cssval;
				}

				//configs
				$config=[];
				if( !empty($configs) ) {
					$parts = explode(',',$configs);
					foreach($parts as $part) {
						list($configindex, $configval) = explode('|',$part);
						$config[$configindex] = $configval;
					}
				}

				// fields
				if( $type == 'controlgroup' ) 
				{
					$obj->add_group_control($f->$gtype,[
					'name'=>$name,
					'types' =>$gtypes,
					'label' =>$lbl,
					'description'=>$note,
					'selectors'=>$selectors,
					'separator'=>$separator
					]);
				}
				else
				if( $type == 'hr' ) {
					$obj->add_control('hr'.$i,['type'=>$f->hr]);
				}
				else
				{
					$obj->add_control($name,[
					'type'=>$f->$type,
					'options'=>$options,
					'default'=>$def,
					'placeholder'=>$hint,
					'label'=>$lbl,
					'description'=>$note,
					'selectors'=>$selectors,
					'separator'=>$separator,
					'picker_options'=>$config,
					'return_value'=>$valueformat,
					'label_block'=>$lblblock
					]);
				}
			}

			$obj->end_controls_section();
		}

		return ['icon'=>$icon,'category'=>$cat];
	}

	## File Scanner
	protected function filelist($path, $filter=null, $getpath=false)
	{
		$files = new \DirectoryIterator($path);
		$filelist=[];
		foreach($files as $file) 
		{
			if( $file->isFile() && !$file->isDot() ) 
			{
				// include only files in $filter 
				// methods: 'css' or 'css|txt' or starting with '^cat' or ending with '$er'
				if( !empty($filter) && !preg_match(chr(1).$filter.chr(1), $file) ) {
					continue;
				}

				$filelist[] = ($getpath == true ? $file->getPath().'/'.$file->getFilename() : $file->getFilename());
			}
		}

		return $filelist;
	}
	
	## Folder Scanner
	protected function folderlist($path, $getpath=false)
	{
		$topdir = new \DirectoryIterator($path);
		$dirs=[];
		foreach($topdir as $dir) {
			if( $dir->isDir() && !$dir->isDot() ) {
				$dirs[] = ($getpath == true ? $dir->getPath().'/'.$dir->getFilename() : $dir->getFilename());
			}
		}

		return $dirs;
	}
	
	## File Copy
	protected function copyFile($src, $dest, $overwrite=true) 
	{
		if( $overwrite == false ) {
			if( file_exists($dest) )
			return;
		}
		
		copy($src, $dest);
	}
	
	
	## Notices and compatibility check
	public function is_compatible() 
	{
		// Check if Elementor installed and activated
		if( !did_action('elementor/loaded') ) 
		{
			add_action('admin_notices', function() 
			{
				if( isset($_GET['activate']) ) 
					unset($_GET['activate']);

				$message = sprintf('%1$s requires %2$s .',
				'<strong>This MU-PLUGIN extension</strong>','<strong><a href="'.admin_url().'plugin-install.php?s=elementor&tab=search&type=term">Elementor plugin installed and activated</a></strong>'
				);

				printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
			});

			return false;
		}

		// Check for required Elementor version
		if( !version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=') ) 
		{
			add_action('admin_notices', function() 
			{
				if( isset($_GET['activate']) ) 
					unset($_GET['activate']);

				$message = sprintf(
				'"%1$s" requires "%2$s" version %3$s or greater.',
				'<strong>Elementor Test Extension</strong>',
				'<strong>Elementor</strong>',
				self::MINIMUM_ELEMENTOR_VERSION
				);

				printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
			});

			return false;
		}

		// Check for required PHP version
		if( version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<') ) 
		{
			add_action('admin_notices', function()
			{
				if( isset($_GET['activate']) ) 
					unset($_GET['activate']);

				$message = sprintf(
				'"%1$s" requires "%2$s" version %3$s or greater.',
				'<strong>Elementor Test Extension</strong>',
				'<strong>PHP</strong>',
				self::MINIMUM_PHP_VERSION
				);

				printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
			});

			return false;
		}

		return true;
	}

}

// initialize
Cmse_Elementor_Widgets::instance();
