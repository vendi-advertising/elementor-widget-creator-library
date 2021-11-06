<?php
/**
* your copyright and license data here
*/
defined('ABSPATH') || exit('CMSEnergizer.com');

/*
Values are stored in variable $v just for lesser character. Change as suitable
std class variables are the control field names except when group controls are used
group controls have appended character with underscores EG: _font_size or _font_weight when using typography
repeatable fields must be looped to define output
*/
?>

<div class="myclass">
<h2><?php echo $v->imagetxt; ?></h2>
<?php echo $v->imagetext; ?>
</div>
