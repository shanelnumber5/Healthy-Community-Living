<?php
/**
 * Template part for displaying entry header.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php do_action( 'sinatra_before_entry_header' ); ?>
<header class="entry-header">

	<?php
	$sinatra_tag = is_single( get_the_ID() ) && ! sinatra_page_header_has_title() ? 'h1' : 'h2';
	$sinatra_tag = apply_filters( 'sinatra_entry_header_tag', $sinatra_tag );

	$sinatra_title_string = '%2$s%1$s';

	if ( 'link' === get_post_format() ) {
		$sinatra_title_string = '<a href="%3$s" title="%3$s" rel="bookmark">%2$s%1$s</a>';
	} elseif ( ! is_single( get_the_ID() ) ) {
		$sinatra_title_string = '<a href="%3$s" title="%4$s" rel="bookmark">%2$s%1$s</a>';
	}

	$sinatra_title_icon = apply_filters( 'sinatra_post_title_icon', '' );
	$sinatra_title_icon = sinatra()->icons->get_svg( $sinatra_title_icon );
	?>

	<<?php echo tag_escape( $sinatra_tag ); ?> class="entry-title"<?php sinatra_schema_markup( 'headline' ); ?>>
		<?php
		echo sprintf(
			wp_kses_post( $sinatra_title_string ),
			wp_kses_post( get_the_title() ),
			wp_kses_post( $sinatra_title_icon ),
			esc_url( sinatra_entry_get_permalink() ),
			the_title_attribute( array( 'echo' => false ) )
		);
		?>
	</<?php echo tag_escape( $sinatra_tag ); ?>>

</header>
<?php do_action( 'sinatra_after_entry_header' ); ?>
