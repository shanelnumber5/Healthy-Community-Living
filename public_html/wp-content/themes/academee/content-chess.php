<?php
/**
 * The Classic template to display the content
 *
 * Used for index/archive/search.
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
 */

$academee_blog_style = explode('_', academee_get_theme_option('blog_style'));
$academee_columns = empty($academee_blog_style[1]) ? 1 : max(1, $academee_blog_style[1]);
$academee_expanded = !academee_sidebar_present() && academee_is_on(academee_get_theme_option('expand_content'));
$academee_post_format = get_post_format();
$academee_post_format = empty($academee_post_format) ? 'standard' : str_replace('post-format-', '', $academee_post_format);
$academee_animation = academee_get_theme_option('blog_animation');

?><article id="post-<?php the_ID(); ?>" 
	<?php post_class( 'post_item post_layout_chess post_layout_chess_'.esc_attr($academee_columns).' post_format_'.esc_attr($academee_post_format) ); ?>
	<?php echo (!academee_is_off($academee_animation) ? ' data-animation="'.esc_attr(academee_get_animation_classes($academee_animation)).'"' : ''); ?>>

	<?php
	// Add anchor
	if ($academee_columns == 1 && shortcode_exists('trx_sc_anchor')) {
		echo do_shortcode('[trx_sc_anchor id="post_'.esc_attr(get_the_ID()).'" title="'.the_title_attribute( array( 'echo' => false ) ).'"]');
	}

	// Sticky label
	if ( is_sticky() && !is_paged() ) {
		?><span class="post_label label_sticky"></span><?php
	}

	// Featured image
	academee_show_post_featured( array(
											'class' => $academee_columns == 1 ? 'trx-stretch-height' : '',
											'show_no_image' => true,
											'thumb_bg' => true,
											'thumb_size' => academee_get_thumb_size(
																	strpos(academee_get_theme_option('body_style'), 'full')!==false
																		? ( $academee_columns > 1 ? 'huge' : 'original' )
																		: (	$academee_columns > 2 ? 'big' : 'huge')
																	)
											) 
										);

	?><div class="post_inner"><div class="post_inner_content"><?php 

		?><div class="post_header entry-header"><?php 
			do_action('academee_action_before_post_title'); 

			// Post title
			the_title( sprintf( '<h3 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' );
			
			do_action('academee_action_before_post_meta'); 

			// Post meta
			$academee_post_meta = academee_show_post_meta(apply_filters('academee_filter_post_meta_args', array(
									'components' => 'categories,date'.($academee_columns < 3 ? ',counters' : '').($academee_columns == 1 ? ',edit' : ''),
									'counters' => 'comments',
									'seo' => false,
									'echo' => false
									), $academee_blog_style[0], $academee_columns)
								);
			academee_show_layout($academee_post_meta);
		?></div><!-- .entry-header -->
	
		<div class="post_content entry-content">
			<div class="post_content_inner">
				<?php
				$academee_show_learn_more = !in_array($academee_post_format, array('link', 'aside', 'status', 'quote'));
				if (has_excerpt()) {
					the_excerpt();
				} else if (strpos(get_the_content('!--more'), '!--more')!==false) {
					the_content( '' );
				} else if (in_array($academee_post_format, array('link', 'aside', 'status'))) {
					the_content();
				} else if ($academee_post_format == 'quote') {
					if (($quote = academee_get_tag(get_the_content(), '<blockquote>', '</blockquote>'))!='')
						academee_show_layout(wpautop($quote));
					else
						the_excerpt();
				} else if (substr(get_the_content(), 0, 1)!='[') {
					the_excerpt();
				}
				?>
			</div>
			<?php
			// Post meta
			if (in_array($academee_post_format, array('link', 'aside', 'status', 'quote'))) {
				academee_show_layout($academee_post_meta);
			}
			// More button
			if ( $academee_show_learn_more ) {
				?><p><a class="more-link" href="<?php the_permalink(); ?>"><?php esc_html_e('Read more', 'academee'); ?></a></p><?php
			}
			?>
		</div><!-- .entry-content -->

	</div></div><!-- .post_inner -->

</article>