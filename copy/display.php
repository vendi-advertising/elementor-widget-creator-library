<?php
defined('ABSPATH') || exit('CMSEnergizer.com');

/*
Elementor Widget: widgetname
Developer Guide: https://github.com/WebsiteDons/elementor-widget-creator-library
*/

?>
<div class="myclass">
<h2><?php echo $v->newwidgettext; ?></h2>
<?php echo $v->newwidgettextarea; ?>
<?php echo (!empty($v->newwidgetlink['url']) ? 
'<p><a style="display:inline-block;background:#fff;padding:4px 10px;color:#222;border-radius:5px;line-height:normal;font-size:12px;" href="'.$v->newwidgetlink['url'].'"'.(!empty($v->newwidgetlink['is_external']) ? ' target="_blank"':'').'>See developer guide</a></p>'
:''); ?>
</div>