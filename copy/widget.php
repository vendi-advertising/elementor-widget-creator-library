<?php
defined('ABSPATH') || exit('CMSEnergizer.com');

/*
Elementor widget: widgetname
*/

class Cmse_widgetname_Widget extends \Elementor\Widget_Base
{
	public function get_name() {
		$name = str_replace(['Cmse_','_Widget'],'',__CLASS__);
		return strtolower($name);
	}
	
	public function get_title() {
		global $cmse;
		return $cmse('widgetprefix').' '.$this->get_name();
	}
	
	public function get_icon() {
		return $this->_register_controls()->icon;
	}
	
	public function get_categories() {
		return [$this->_register_controls()->category];
	}
	
	protected function _register_controls() {
		$icon = Cmse_Elementor_Widgets::fields($this->get_name(),$this);
		
		return (object)$icon;
	}
	
	// front end output
	protected function render() {
		$v = (object)$this->get_settings_for_display();
		if( file_exists(__DIR__.'/display.php') ) {
			include __DIR__.'/display.php';
		}else{
			echo 'The display file is missing. Check the widget folder for file display.php at <pre>'.__DIR__.'</pre>';
		}
	}
	
	protected function content_template() {
	}
}