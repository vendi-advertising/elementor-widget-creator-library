<?php
/*
Plugin Name: CMSE Elementor Widget Developer Library
Plugin URI: https://github.com/WebsiteDons/elementor-widget-creator-library
Description: Develop widgets for Elementor with intuitive markup
Version: 1.0.1
Author: CMSEnergizer.com
Author URI: https://github.com/WebsiteDons/elementor-widget-creator-library
Copyright 2014 CMSEnergizer.com
https://developers.elementor.com/elementor-controls/
*/

defined('ABSPATH') || exit('CMSEnergizer.com');

/*
Edit the constants as need to change paths and othe text values
*/
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
// set the prefered prefix which displays on the widget button
define('WIDGETPREFIX', 'CMSE');


// load shpaes class
use \Elementor\Shapes;


final class Cmse_Elementor_Widgets 
{
	const VERSION = '1.0.1';
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
	protected static function ctr()
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
		$formfile = simplexml_load_file(WIDGETPATH.'/'.$xml.'/'.$xml.'.xml');
		$form = $formfile->attributes();
		$icon = (string)$form->icon;
		$cat = (string)$form->cat;
		$fields= $tabstart= $tabend='';
		$f = self::ctr();
		$rpt = new \Elementor\Repeater();
		
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
				add_action('elementor/editor/footer', function() use($fsetid) 
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

			$i=null;
			foreach($fieldset->field as $field) 
			{
				$i++;
				$att = $field->attributes();
				$fv = self::fieldval($att);
				$type = (string)$att->type;
				$gtype = $fv->gtype;
				
				
				## Do Controls
				// group controls
				if( $type == 'controlgroup' ) 
				{
					self::printFieldGroup($fv,$type,$gtype,$obj,$f);
				}
				else
				// horizontal line
				if( $type == 'hr' ) {
					$obj->add_control('hr'.$i,['type'=>$f->hr]);
				}
				else
				// repeat fields
				if( $type == 'repeat' ) 
				{
					self::repeat($field->repeat, $rpt, $f);
					
					$titlefield = (isset($att->titlefield) ? '{{{'.(string)$att->titlefield.'}}}':null);
					$default = [];
					
					$obj->add_control($fv->name, [
					'type'=>$f->repeat,
					'fields'=>$rpt->get_controls(),
					'title_field'=>$titlefield,
					'show_label'=>$fv->showlabel,
					'label'=>$fv->lbl,
					'prevent_empty'=>false
					]);
				}
				else
				// standard controls
				{
					self::printFieldStandard($fv,$type,$gtype,$obj,$f);
				}
			}
			
			$obj->end_controls_section();
		}
		
		return ['icon'=>$icon,'category'=>$cat];
	}
	
	
	
	// Repeater fields process
	protected static function repeat($fields,$obj, $f)
	{
		$i=null;
		foreach($fields as $atts) 
		{
			$i++;
			$att = $atts->attributes();
			$fv = self::fieldval($att);
			$type = (string)$att->type;
			$gtype = $fv->gtype;
			
			if( $type == 'controlgroup' ) 
			{
				self::printFieldGroup($fv,$type,$gtype,$obj,$f);
			}
			else
			if( $type == 'hr' ) {
				$obj->add_control('hr'.$i,['type'=>$f->hr]);
			}
			else
			{
				self::printFieldStandard($fv,$type,$gtype,$obj,$f);
			}
		}
	}
	
	
	// standard controls printer
	protected static function printFieldStandard($fv, $type, $gtype, $obj, $f) 
	{
		$obj->add_control($fv->name,[
		'type'=>$f->$type,
		'options'=>$fv->options,
		'default'=>$fv->def,
		'placeholder'=>$fv->hint,
		'label'=>$fv->lbl,
		'description'=>$fv->note,
		'selectors'=>$fv->selectors,
		'separator'=>$fv->separator,
		'picker_options'=>$fv->config,
		'return_value'=>$fv->valueformat,
		'label_block'=>$fv->lblblock,
		'label_on'=>$fv->labelon,
		'label_off'=>$fv->labeloff,
		'conditions'=>$fv->condition,
		'rows'=>$fv->rows,
		'show_label'=>$fv->showlabel,
		'show_external'=>$fv->extlink,
		]);
	}
	
	// group controls printer
	protected static function printFieldGroup($fv, $type, $gtype, $obj, $f) 
	{
		$obj->add_group_control($f->$gtype,[
		'name'=>$fv->name,
		'types' =>$fv->gtypes,
		'label' =>$fv->lbl,
		'description'=>$fv->note,
		'selectors'=>$fv->selectors,
		'separator'=>$fv->separator
		]);
	}
	
	// values processor
	protected static function fieldVal($att)
	{
		$def=null;
		if( isset($att->default) ) 
		{
			if( (string)$att->type == 'url' ) 
			{
				$def = self::stringToArray((string)$att->default);
			}else{
				$def = (string)$att->default;
			}
		}
		
		$options='';
		if( !empty($att->options) ) 
		{
			$options = self::options((string)$att->options);
		}
		
		//selectors
		$selectors=[];
		if( isset($att->selectors) ) {
			list($classes,$cssval) = explode('|',(string)$att->selectors);
			$selectors[$classes] = $cssval;
		}
		
		//configs
		$config=[];
		if( !empty($fv->configs) ) {
			$parts = explode(',',$fv->configs);
			foreach($parts as $part) {
				list($configindex, $configval) = explode('|',$part);
				$config[$configindex] = $configval;
			}
		}
		
		// condition handler
		$condition=null;
		if( isset($att->condition) )
		{
			$condition = self::condition((string)$att->condition);
		}
			
		$vals = (object)[
		'type'=> (string)$att->type,
		'name'=> (string)$att->name,
		'lbl'=> (isset($att->label) ? (string)$att->label:null),
		'hint'=> (isset($att->hint) ? (string)$att->hint:null),
		'gtype'=> (isset($att->gtype) ? (string)$att->gtype:null),
		'gtypes'=> (isset($att->gtypes) ? explode(',',(string)$att->gtypes):null),
		'note'=> (isset($att->note) ? (string)$att->note:null),
		'separator'=> (isset($att->line) ? (string)$att->line:null),
		'configs'=> (isset($att->configs) ? (string)$att->configs:null),
		'valueformat'=> (isset($att->valueformat) ? (string)$att->valueformat:1),
		'lblblock'=> (isset($att->labelblock) || $att->type == 'url' ? true:null),
		'labelon'=> (isset($att->labelon) ? (string)$att->labelon:'Yes'),
		'labeloff'=> (isset($att->labeloff) ? (string)$att->labeloff:'No'),
		'rows'=> (isset($att->rows) ? (string)$att->rows:null),
		'showlabel'=> (isset($att->showlabel) ? false:true),
		'extlink'=> (isset($att->extlink) ? false:true),
		'options'=>$options,
		'selectors'=>$selectors,
		'config'=>$config,
		'def'=>$def,
		'condition'=>$condition,
		];
		
		return $vals;
	}
	
	// string condition="{'texton':'1'}"
	// converted array([texton]=>1)
	protected static function stringToArray($val) 
	{
		$arr = str_replace('\'','"',$val);
		$value = (array)json_decode($arr);
		
		return $value;
	}
	
	protected static function condition($condition)
	{
		$arr = self::stringToArray($condition);
		$field = array_keys($arr);
		$check = array_values($arr);
		
		$cond=[];$conditions=[];
		if( isset($check[0]) && isset($field[0]) ) 
		{
			$checks = explode('|',(string)$check[0]);
			foreach($checks as $chk) {
				$cond[] = ['name'=>$field[0],'value'=>$chk];
			}
		}
		
		$conditions['terms'][] = ['relation'=>'or','terms'=>$cond];
		
		return $conditions;
	}
	
	protected static function options($vals) 
	{
		$arr = self::stringToArray($vals);
		$options=[];$grp=[]; $out=null;
		foreach($arr as $k=>$v) 
		{
			if( strstr($vals,'::') ) {
				if( count($arr) > 1 ) {
					$grp[] = call_user_func($k,$v);
				}else{
					$options = call_user_func($k,$v);
				}
			}else{
				$options[$k] = $v;
			}
		}
		
		if( !empty($grp) ) {
			$out = array_merge(...$grp);
			
		}else{
			$out = $options;
		}
		
		return $out;
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
