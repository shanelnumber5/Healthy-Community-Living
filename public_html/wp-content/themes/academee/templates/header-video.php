<?php
/**
 * The template to display the background video in the header
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0.14
 */
$academee_header_video = academee_get_header_video();
$academee_embed_video = '';
if (!empty($academee_header_video) && !academee_is_from_uploads($academee_header_video)) {
	if (academee_is_youtube_url($academee_header_video) && preg_match('/[=\/]([^=\/]*)$/', $academee_header_video, $matches) && !empty($matches[1])) {
		?><div id="background_video" data-youtube-code="<?php echo esc_attr($matches[1]); ?>"></div><?php
	} else {
		global $wp_embed;
		if (false && is_object($wp_embed)) {
			$academee_embed_video = do_shortcode($wp_embed->run_shortcode( '[embed]' . trim($academee_header_video) . '[/embed]' ));
			$academee_embed_video = academee_make_video_autoplay($academee_embed_video);
		} else {
			$academee_header_video = str_replace('/watch?v=', '/embed/', $academee_header_video);
			$academee_header_video = academee_add_to_url($academee_header_video, array(
				'feature' => 'oembed',
				'controls' => 0,
				'autoplay' => 1,
				'showinfo' => 0,
				'modestbranding' => 1,
				'wmode' => 'transparent',
				'enablejsapi' => 1,
				'origin' => home_url(),
				'widgetid' => 1
			));
			$academee_embed_video = '<iframe src="' . esc_url($academee_header_video) . '" width="1170" height="658" allowfullscreen="0" frameborder="0"></iframe>';
		}
		?><div id="background_video"><?php academee_show_layout($academee_embed_video); ?></div><?php
	}
}
?>