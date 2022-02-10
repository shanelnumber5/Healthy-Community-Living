<?php
/**
 * Template part for displaying entry content.
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

<?php do_action( 'sinatra_before_entry_content' ); ?>
<div class="entry-content si-entry"<?php sinatra_schema_markup( 'text' ); ?>>
	<?php the_content(); ?>
</div>

<?php sinatra_link_pages(); ?>

<?php do_action( 'sinatra_after_entry_content' ); ?>
