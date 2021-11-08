<?php
/**
* your copyright and license data here
*/
defined('ABSPATH') || exit('CMSEnergizer.com');

/*
only need to change the middle string of the class name
eg: Cmse_image_Widget 
Cmse_myNewName_Widget
*/

class Cmse_image_Widget extends \Elementor\Widget_Base
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
		include __DIR__.'/display.php';
	}
	
	protected function content_template() {
	}
}
