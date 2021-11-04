<?php
defined('ABSPATH') || exit('CMSEnergizer.com');

/*
this is just an example
see the development docs at https://developers.elementor.com/elementor-controls/
*/

class Cmse_FG extends \Elementor\Control_Base_Multiple
{
	const FG = 'quad';
	
	public function get_type() {
		return self::FG;
	}
	
	
	
	public function content_template() 
	{
		
	}
}
