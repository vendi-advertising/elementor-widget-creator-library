<?php
defined('ABSPATH') || exit('CMSEnergizer.com');

/*
Elementor Widget: widgetname
Developer Guide: https://github.com/WebsiteDons/elementor-widget-creator-library
*/

?>
<div class="myclass">
    <h2><?php esc_html_e($v->newwidgettext); ?></h2>
    <?php echo wp_kses_post($v->newwidgettextarea); ?>
    <?php if (!empty($v->newwidgetlink['url'])): ?>
        <p><a
                    style="display:inline-block;background:#fff;padding:4px 10px;color:#222;border-radius:5px;line-height:normal;font-size:12px;"
                    href="<?php esc_attr_e($v->newwidgetlink['url']) ?>"
                <?php if (!empty($v->newwidgetlink['is_external'])): ?>
                    target="_blank"
                <?php endif; ?>
            >See developer guide</a></p>
    <?php endif; ?>
</div>