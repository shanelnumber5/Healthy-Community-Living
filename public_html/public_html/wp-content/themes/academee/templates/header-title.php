<?php
/**
 * The template to display the page title and breadcrumbs
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
 */

// Page (category, tag, archive, author) title

if ( academee_need_page_title() ) {
	academee_sc_layouts_showed('title', true);
	academee_sc_layouts_showed('postmeta', true);
	?>
	<div class="top_panel_title sc_layouts_row sc_layouts_row_type_normal">
		<div class="content_wrap">
			<div class="sc_layouts_column sc_layouts_column_align_center">
				<div class="sc_layouts_item">
					<div class="sc_layouts_title sc_align_center">
						<?php
						// Post meta on the single post
						if ( is_single() )  {
							?><div class="sc_layouts_title_meta"><?php
								academee_show_post_meta(apply_filters('academee_filter_post_meta_args', array(
									'components' => 'categories,date,counters,edit',
									'counters' => 'views,comments,likes',
									'seo' => true
									), 'header', 1)
								);
							?></div><?php
						}
						
						// Blog/Post title
						?><div class="sc_layouts_title_title"><?php
							$academee_blog_title = academee_get_blog_title();
							$academee_blog_title_text = $academee_blog_title_class = $academee_blog_title_link = $academee_blog_title_link_text = '';
							if (is_array($academee_blog_title)) {
								$academee_blog_title_text = $academee_blog_title['text'];
								$academee_blog_title_class = !empty($academee_blog_title['class']) ? ' '.$academee_blog_title['class'] : '';
								$academee_blog_title_link = !empty($academee_blog_title['link']) ? $academee_blog_title['link'] : '';
								$academee_blog_title_link_text = !empty($academee_blog_title['link_text']) ? $academee_blog_title['link_text'] : '';
							} else
								$academee_blog_title_text = $academee_blog_title;
							?>
							<h1 class="sc_layouts_title_caption<?php echo esc_attr($academee_blog_title_class); ?>"><?php
								$academee_top_icon = academee_get_category_icon();
								if (!empty($academee_top_icon)) {
									$academee_attr = academee_getimagesize($academee_top_icon);
									?><img src="<?php echo esc_url($academee_top_icon); ?>" alt="<?php echo esc_attr(basename($academee_top_icon)); ?>" <?php if (!empty($academee_attr[3])) academee_show_layout($academee_attr[3]);?>><?php
								}
								echo wp_kses($academee_blog_title_text, 'academee_kses_content');
							?></h1>
							<?php
							if (!empty($academee_blog_title_link) && !empty($academee_blog_title_link_text)) {
								?><a href="<?php echo esc_url($academee_blog_title_link); ?>" class="theme_button theme_button_small sc_layouts_title_link"><?php echo esc_html($academee_blog_title_link_text); ?></a><?php
							}
							
							// Category/Tag description
							if ( is_category() || is_tag() || is_tax() ) 
								the_archive_description( '<div class="sc_layouts_title_description">', '</div>' );
		
						?></div><?php
	
						// Breadcrumbs
						?><div class="sc_layouts_title_breadcrumbs"><?php
							do_action( 'academee_action_breadcrumbs');
						?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
?>