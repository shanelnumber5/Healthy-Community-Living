<?php
/**
 * The template to display the copyright info in the footer
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0.10
 */

// Copyright area
$academee_footer_scheme =  academee_is_inherit(academee_get_theme_option('footer_scheme')) ? academee_get_theme_option('color_scheme') : academee_get_theme_option('footer_scheme');
$academee_copyright_scheme = academee_is_inherit(academee_get_theme_option('copyright_scheme')) ? $academee_footer_scheme : academee_get_theme_option('copyright_scheme');
?> 
<div class="footer_copyright_wrap scheme_<?php echo esc_attr($academee_copyright_scheme); ?>">
	<div class="footer_copyright_inner">
		<div class="content_wrap">
			<div class="copyright_text"><?php
				// Replace {{...}} and [[...]] on the <i>...</i> and <b>...</b>
				$academee_copyright = academee_prepare_macros(academee_get_theme_option('copyright'));
				if (!empty($academee_copyright)) {
					// Replace {date_format} on the current date in the specified format
					if (preg_match("/(\\{[\\w\\d\\\\\\-\\:]*\\})/", $academee_copyright, $academee_matches)) {
						$academee_copyright = str_replace($academee_matches[1], date(str_replace(array('{', '}'), '', $academee_matches[1])), $academee_copyright);
					}
					// Display copyright
					echo wp_kses_data(nl2br($academee_copyright));
				}
			?></div>
		</div>
	</div>
</div>
