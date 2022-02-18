<?php
/**
 * The style "default" of the Widget "Flickr"
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.10
 */

$args = get_query_var('trx_addons_args_widget_flickr');
extract($args);
		
// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
trx_addons_show_layout($title, $before_title, $after_title);
	
// Widget body
?><div class="flickr_images"><?php
	if ($flickr_count <= 10) {
		// Old method - up to 10 images
		$size = 's';
		?>
		<script type="text/javascript" src="<?php echo esc_attr(trx_addons_get_protocol()); ?>://www.flickr.com/badge_code_v2.gne?count=<?php echo (int) $flickr_count; ?>&amp;display=random&amp;flickr_display=random&amp;size=<?php echo urlencode($size); ?>&amp;layout=x&amp;source=user&amp;user=<?php echo urlencode($flickr_username); ?>"></script>
		<?php
	} else {
		// New method > 10 images
		$size = 'square';
		?>
		<script type="text/javascript" src="<?php echo esc_attr(trx_addons_get_protocol()); ?>://www.flickr.com/badge_code.gne?count=<?php echo (int) $flickr_count; ?>&amp;display=random&amp;flickr_display=random&amp;size=<?php echo urlencode($size); ?>&amp;layout=x&amp;source=user&amp;nsid=<?php echo urlencode($flickr_username); ?>&amp;raw=1"></script>
		<?php
	}
?></div><?php	

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
?>