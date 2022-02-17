<?php
/**
 * Theme tags
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
 */


//----------------------------------------------------------------------
//-- Common tags
//----------------------------------------------------------------------

// Return true if current page need title
if ( !function_exists('academee_need_page_title') ) {
	function academee_need_page_title() {
		return !is_front_page() && apply_filters('academee_filter_need_page_title', true);
	}
}

// Output string with the html layout (if not empty)
// (put it between 'before' and 'after' tags)
// Attention! This string may contain layout formed in any plugin (widgets or shortcodes output) and not require escaping to prevent damage!
if ( !function_exists('academee_show_layout') ) {
	function academee_show_layout($str, $before='', $after='') {
		if (trim($str) != '') {
			printf("%s%s%s", $before, $str, $after);
		}
	}
}

// Return logo images (if set)
if ( !function_exists('academee_get_logo_image') ) {
	function academee_get_logo_image($type='') {
		$logo_image = '';
		if (academee_get_retina_multiplier(2) > 1)
			$logo_image = academee_get_theme_option( 'logo'.(!empty($type) ? '_'.trim($type) : '').'_retina' );
		if (empty($logo_image)) 
			$logo_image = academee_get_theme_option( 'logo'.(!empty($type) ? '_'.trim($type) : '') );
		return $logo_image;
	}
}

// Return header video (if set)
if ( !function_exists('academee_get_header_video') ) {
	function academee_get_header_video() {
		$video = '';
		if (apply_filters('academee_header_video_enable', !wp_is_mobile() && is_front_page())) {
			if (academee_check_theme_option('header_video')) {
				$video = academee_get_theme_option('header_video');
				if ((int) $video > 0) $video = wp_get_attachment_url( $video );
			} else if (function_exists('get_header_video_url')) {
				$video = get_header_video_url();
			}
		}
		return $video;
	}
}


//----------------------------------------------------------------------
//-- Post parts
//----------------------------------------------------------------------

// Show post meta block: post date, author, categories, counters, etc.
if ( !function_exists('academee_show_post_meta') ) {
	function academee_show_post_meta($args=array()) {

		$args = array_merge(array(
			'components' => 'categories,date,author,counters,share,edit',
			'counters' => 'comments',	//comments,views,likes
			'seo' => false,
			'echo' => true
			), $args);

		if (!$args['echo']) ob_start();

		?><div class="post_meta"><?php
			$components = explode(',', $args['components']);
			foreach ($components as $comp) {
				$comp = trim($comp);
				// Post categories
				if ($comp == 'categories') {
					$cats = get_post_type()=='post' ? get_the_category_list(', ') : apply_filters('academee_filter_get_post_categories', '');
					if (!empty($cats)) {
						?>
						<span class="post_meta_item post_categories"><?php academee_show_layout($cats); ?></span>
						<?php
					}

				// Post date
				} else if ($comp == 'date') {
					$dt = apply_filters('academee_filter_get_post_date', academee_get_date());
					if (!empty($dt)) {
						?>
						<span class="post_meta_item post_date<?php if (!empty($args['seo'])) echo ' date updated'; ?>"<?php if (!empty($args['seo'])) echo ' itemprop="datePublished"'; ?>><a href="<?php the_permalink(); ?>"><?php echo esc_html($dt); ?></a></span>
						<?php
					}

				// Post author
				} else if ($comp == 'author') {
					$author_id = get_the_author_meta('ID');
					if (empty($author_id) && !empty($GLOBALS['post']->post_author))
						$author_id = $GLOBALS['post']->post_author;
					if ($author_id > 0) {
						$author_link = get_author_posts_url($author_id);
						$author_name = get_the_author_meta('display_name', $author_id);
						?>
						<a class="post_meta_item post_author" rel="author" href="<?php echo esc_url($author_link); ?>">
							<?php echo esc_html($author_name); ?>
						</a>
						<?php
					}

				// Post counters
				} else if ($comp == 'counters') {
					academee_show_layout(academee_get_post_counters($args['counters']));
	
				// Socials share
				} else if ($comp == 'share') {
					academee_show_share_links(array(
							'type' => 'drop',
							'caption' => esc_html__('Share', 'academee'),
							'before' => '<span class="post_meta_item post_share">',
							'after' => '</span>'
						));

				// Edit page link
				} else if ($comp == 'edit') {
					edit_post_link( esc_html__( 'Edit', 'academee' ), '<span class="post_meta_item post_edit">', '</span>' );
				}
			}
		?></div><!-- .post_meta --><?php
		
		if (!$args['echo']) {
			$rez = ob_get_contents();
			ob_end_clean();
			return $rez;
		} else
			return '';
	}
}

// Show post featured block: image, video, audio, etc.
if ( !function_exists('academee_show_post_featured') ) {
	function academee_show_post_featured($args=array()) {

		$args = array_merge(array(
			'hover' => academee_get_theme_option('image_hover'),	// Hover effect
			'class' => '',									// Additional Class for featured block
			'post_info' => '',								// Additional layout after hover
			'thumb_bg' => false,							// Put thumb image as block background or as separate tag
			'thumb_size' => '',								// Image size
			'thumb_only' => false,							// Display only thumb (without post formats)
			'show_no_image' => false,						// Display 'no-image.jpg' if post haven't thumbnail
			'seo' => academee_is_on(academee_get_theme_option('seo_snippets')),
			'singular' => is_singular()						// Current page is singular (true) or blog/shortcode (false)
			), $args);

		if ( post_password_required() ) return;

		$thumb_size = !empty($args['thumb_size']) ? $args['thumb_size'] : academee_get_thumb_size(is_attachment() ? 'full' : (is_single() ? 'huge' : 'big'));
		$post_format = str_replace('post-format-', '', get_post_format());
		$no_image = !empty($args['show_no_image']) ? academee_get_no_image() : '';
		if ($args['thumb_bg']) {
			if (has_post_thumbnail()) {
				$image = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), $thumb_size );
				$image = $image[0];
			} else if ($post_format == 'image') {
				$image = academee_get_post_image();
				if (!empty($image)) 
					$image = academee_add_thumb_size($image, $thumb_size);
			}
			if (empty($image))
				$image = $no_image;
			if (!empty($image))
				$args['class'] .= ($args['class'] ? ' ' : '') . 'post_featured_bg' . ' ' . academee_add_inline_css_class('background-image: url('.esc_url($image).');');
		}

		if ( $args['singular'] ) {
			
			if ( is_attachment() ) {
				?>
				<div class="post_featured post_attachment<?php if ($args['class']) echo ' '.esc_attr($args['class']); ?>">

					<?php if (!$args['thumb_bg']) echo wp_get_attachment_image( get_the_ID(), $thumb_size ); ?>

					<nav id="image-navigation" class="navigation image-navigation">
						<div class="nav-previous"><?php previous_image_link( false, '' ); ?></div>
						<div class="nav-next"><?php next_image_link( false, '' ); ?></div>
					</nav><!-- .image-navigation -->
				
				</div><!-- .post_featured -->
				
				<?php
				if ( has_excerpt() ) {
					?><div class="entry-caption"><?php the_excerpt(); ?></div><!-- .entry-caption --><?php
				}
	
			} else if ( has_post_thumbnail() || !empty($args['show_no_image']) ) {

				?>
				<div class="post_featured<?php if ($args['class']) echo ' '.esc_attr($args['class']); ?>"<?php if ($args['seo']) echo ' itemscope itemprop="image" itemtype="//schema.org/ImageObject"'; ?>>
					<?php
					if (has_post_thumbnail() && $args['seo']) {
						$academee_attr = academee_getimagesize( wp_get_attachment_url( get_post_thumbnail_id() ) );
						?>
						<meta itemprop="width" content="<?php echo esc_attr($academee_attr[0]); ?>">
						<meta itemprop="height" content="<?php echo esc_attr($academee_attr[1]); ?>">
						<?php
					}
					if (!$args['thumb_bg']) {
						if ( has_post_thumbnail() ) {
							the_post_thumbnail( $thumb_size, array(
								'alt' => the_title_attribute( array( 'echo' => false ) ),
								'itemprop' => 'url'
								)
							);
						} else if (!empty($no_image)) {
							?><img<?php if ($args['seo']) echo ' itemprop="url"'; ?> src="<?php echo esc_url($no_image); ?>" alt="<?php the_title_attribute(); ?>"><?php
						}
					}
					?>
				</div><!-- .post_featured -->
				<?php

			}
	
		} else {
	
			if (empty($post_format)) $post_format='standard';
			$has_thumb = has_post_thumbnail();
			$post_info = !empty($args['post_info']) ? $args['post_info'] : '';

			if ($has_thumb 
				|| !empty($args['show_no_image']) 
				|| (!$args['thumb_only'] && in_array($post_format, array('gallery', 'image', 'audio', 'video')))) {
				?><div class="post_featured <?php
							echo (!empty($has_thumb) || $post_format == 'image' || !empty($args['show_no_image']) 
									? ('with_thumb' . ($args['thumb_only'] 
														|| !in_array($post_format, array('audio', 'video', 'gallery')) 
														|| ($post_format=='gallery' && ($has_thumb || $args['thumb_bg']))
															? ' hover_'.esc_attr($args['hover'])
															: (in_array($post_format, array('video')) ? ' hover_play' : '')
														)
										)
									: 'without_thumb')
									. (!empty($args['class']) ? ' '.esc_attr($args['class']) : '');
								?>"><?php 

				// Put the thumb or gallery or image or video from the post
				if ( $args['thumb_bg'] ) {
					if (!empty($args['hover'])) {
						?><div class="mask"></div><?php
					}
					if (!in_array($post_format, array('audio', 'video'))) {
						academee_hovers_add_icons($args['hover']);
					}

				} else if ( $has_thumb ) {
					the_post_thumbnail( $thumb_size, array( 'alt' => the_title_attribute( array( 'echo' => false ) ) ) );
					if (!empty($args['hover'])) {
						?><div class="mask"></div><?php
					}
					if ($args['thumb_only'] || !in_array($post_format, array('audio', 'video'))) {
						academee_hovers_add_icons($args['hover']);
					}
	
				} else if ($post_format == 'gallery' && !$args['thumb_only']) {

					if (($output=academee_get_slider_layout(array('thumb_size'=>$thumb_size, 'controls'=>'yes', 'pagination'=>'yes'))) != '')
						academee_show_layout($output);
	
				} else if ($post_format == 'image') {
					$image = academee_get_post_image();
					if (!empty($image)) {
						$image = academee_add_thumb_size($image, $thumb_size);
						?><img src="<?php echo esc_url($image); ?>" alt="<?php the_title_attribute(); ?>"><?php
						if (!empty($args['hover'])) {
							?><div class="mask"></div><?php 
						}
						academee_hovers_add_icons($args['hover'], array('image' => $image));
					}
				} else if (!empty($args['show_no_image']) && !empty($no_image)) {
					?><img src="<?php echo esc_url($no_image); ?>" alt="<?php the_title_attribute(); ?>"><?php
					if (!empty($args['hover'])) {
						?><div class="mask"></div><?php 
					}
					academee_hovers_add_icons($args['hover']);
				}
				
				// Add audio and video
				if (!$args['thumb_only'] && ($post_format == 'video' || $post_format == 'audio')) {

					$post_content = '';	

					// Put video under the thumb
					if ($post_format == 'video') {
						$video = academee_get_post_video($post_content, false);
						if (empty($video))
							$video = academee_get_post_iframe($post_content, false);
						if (empty($video)) {
							// Only get video from the content if a playlist isn't present.
							$post_content = apply_filters( 'the_content', get_the_content() );
							if ( false === strpos( $post_content, 'wp-playlist-script' ) ) {
								$videos = get_media_embedded_in_content( $post_content, array( 'video', 'object', 'embed', 'iframe' ) );
								if (!empty($videos) && is_array($videos)) {
									$video = academee_array_get_first($videos, false);
								}
							}
						}
						if (!empty($video)) {
							if ( $has_thumb ) {
								$video = academee_make_video_autoplay($video);
								?><div class="post_video_hover" data-video="<?php echo esc_attr($video); ?>"></div><?php
							}
							?><div class="post_video video_frame"><?php
								if ( !$has_thumb ) {
									academee_show_layout($video);
								}
							?></div><?php
						}

					// Put audio over the thumb
					} else if ($post_format == 'audio') {
						$audio = academee_get_post_audio($post_content, false);
						if (empty($audio))
							$audio = academee_get_post_iframe($post_content, false);
						if (empty($audio)) {
							// Only get video from the content if a playlist isn't present.
							$post_content = apply_filters( 'the_content', get_the_content() );
							if ( false === strpos( $post_content, 'wp-playlist-script' ) ) {
								$audios = get_media_embedded_in_content( $post_content, array( 'audio' ) );
								if (!empty($audios) && is_array($audios)) {
									$audio = academee_array_get_first($audios, false);
								}
							}
						}
						if (!empty($audio)) {
							?><div class="post_audio<?php if (strpos($audio, 'soundcloud')!==false) echo ' with_iframe'; ?>"><?php
								// Add author and title
								$media_author = academee_get_theme_option('media_author', '', false, get_the_ID());
								$media_title = academee_get_theme_option('media_title', '', false, get_the_ID());
								if ( !empty($media_author) && !academee_is_inherit($media_author) ) {
									?><div class="post_audio_author"><?php academee_show_layout($media_author); ?></div><?php
								}
								if ( !empty($media_title) && !academee_is_inherit($media_title) ) {
									?><h5 class="post_audio_title"><?php academee_show_layout($media_title); ?></h5><?php
								}
								// Display audio
								academee_show_layout($audio);
							?></div><?php
						}
					}
				}
				
				// Put optional info block over the thumb
				academee_show_layout($post_info);
				?></div><?php
			} else {
				// Put optional info block over the thumb
				academee_show_layout($post_info);
			}
		}
	}
}


// Return path to the 'no-image'
if ( !function_exists('academee_get_no_image') ) {
	function academee_get_no_image($no_image='') {
		static $img = '';
		if (empty($img)) {
			$img = academee_get_theme_option( 'no_image' );
			if (empty($img)) $img = academee_get_file_url('images/no-image.jpg');
		}
		if (!empty($img)) $no_image = $img;
		return $no_image;
	}
}


// Add featured image as background image to post navigation elements.
if ( !function_exists('academee_add_bg_in_post_nav') ) {
	function academee_add_bg_in_post_nav() {
		if ( ! is_single() ) return;
	
		$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
		$next     = get_adjacent_post( false, '', false );
		$css      = '';
		$noimg    = academee_get_no_image();
		
		if ( is_attachment() && $previous->post_type == 'attachment' ) return;
	
		if ( $previous ) {
			if ( has_post_thumbnail( $previous->ID ) ) {
				$img = wp_get_attachment_image_src( get_post_thumbnail_id( $previous->ID ), academee_get_thumb_size('med') );
				$img = $img[0];
			} else
				$img = $noimg;
			if ( !empty($img) )
				$css .= '.post-navigation .nav-previous a .nav-arrow { background-image: url(' . esc_url( $img ) . '); }';
			else
				$css .= '.post-navigation .nav-previous a .nav-arrow { background-color: rgba(128,128,128,0.05); border-color:rgba(128,128,128,0.1); }';
		}
	
		if ( $next ) {
			if ( has_post_thumbnail( $next->ID ) ) {
				$img = wp_get_attachment_image_src( get_post_thumbnail_id( $next->ID ), academee_get_thumb_size('med') );
				$img = $img[0];
			} else
				$img = $noimg;
			if ( !empty($img) )
				$css .= '.post-navigation .nav-next a .nav-arrow { background-image: url(' . esc_url( $img ) . '); }';
			else
				$css .= '.post-navigation .nav-next a .nav-arrow { background-color: rgba(128,128,128,0.05); border-color:rgba(128,128,128,0.1); }';
		}
	
		wp_add_inline_style( 'academee-main', $css );
	}
}

// Show related posts
if ( !function_exists('academee_show_related_posts') ) {
	function academee_show_related_posts($args=array(), $style=1, $title='') {
		$args = array_merge(array(
			'ignore_sticky_posts' => true,
			'posts_per_page' => 2,
			'columns' => 0,
			'orderby' => 'rand',
			'order' => 'DESC',
			'post_type' => 'post',
			'post_status' => 'publish',
			'post__not_in' => array(),
			'category__in' => array()
			), $args);
		
		$args['post__not_in'][] = get_the_ID();
		
		if (empty($args['columns'])) $args['columns'] = $args['posts_per_page'];
		
		if (empty($args['category__in']) || is_array($args['category__in']) && count($args['category__in']) == 0) {
			$post_categories_ids = array();
			$post_cats = get_the_category(get_the_ID());
			if (is_array($post_cats) && !empty($post_cats)) {
				foreach ($post_cats as $cat) {
					$post_categories_ids[] = $cat->cat_ID;
				}
			}
			$args['category__in'] = $post_categories_ids;
		}
		
		$query = new WP_Query( $args );

		if ($query->found_posts > 0) {
			?>
			<section class="related_wrap">
				<h3 class="section_title related_wrap_title"><?php
					if (!empty($title))
						echo esc_html($title);
					else
						esc_html_e('You May Also Like', 'academee');
				?></h3>
				<div class="columns_wrap posts_container<?php if ($args['columns'] < $args['posts_per_page']) echo ' columns_padding_bottom'; ?>">
					<?php
					while ( $query->have_posts() ) { $query->the_post();
						?><div class="column-1_<?php echo intval(max(1, min(4, $args['columns']))); ?>"><?php
							 get_template_part('templates/related-posts', $style);
						?></div><?php
					}
					wp_reset_postdata();
					?>
				</div>
			</section>
		<?php
		}
	}
}


// Show portfolio posts
if ( !function_exists('academee_show_portfolio_posts') ) {
	function academee_show_portfolio_posts($args=array()) {
		$args = array_merge(array(
			'cat' => 0,
			'parent_cat' => 0,
			'taxonomy' => 'category',
			'post_type' => 'post',
			'page' => 1,
			'sticky' => false,
			'blog_style' => '',
			'echo' => true
			), $args);

		$blog_style = explode('_', empty($args['blog_style']) ? academee_get_theme_option('blog_style') : $args['blog_style']);
		$style = $blog_style[0];
		$columns = empty($blog_style[1]) ? 2 : max(2, $blog_style[1]);

		if ( !$args['echo'] ) {
			ob_start();

			$q_args = array(
				'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') 
										? array('publish', 'private') 
										: 'publish'
			);
			$q_args = academee_query_add_posts_and_cats($q_args, '', $args['post_type'], $args['cat'], $args['taxonomy']);
			if ($args['page'] > 1) {
				$q_args['paged'] = $args['page'];
				$q_args['ignore_sticky_posts'] = true;
			}
			$ppp = academee_get_theme_option('posts_per_page');
			if ((int) $ppp != 0)
				$q_args['posts_per_page'] = (int) $ppp;

			query_posts( $q_args );
		}

		// Show posts
		$class = sprintf('portfolio_wrap posts_container portfolio_%s', $columns)
				. ($style!='portfolio' ? sprintf(' %s_wrap %s_%s', $style, $style, $columns) : '');
		if ($args['sticky']) {
			?><div class="columns_wrap sticky_wrap"><?php	
		} else {
			?><div class="<?php echo esc_attr($class); ?>"><?php	
		}
	
		while ( have_posts() ) { the_post(); 
			if ($args['sticky'] && !is_sticky()) {
				$args['sticky'] = false;
				?></div><div class="<?php echo esc_attr($class); ?>"><?php
			}
			get_template_part( 'content', $args['sticky'] && is_sticky() ? 'sticky' : ($style == 'gallery' ? 'portfolio-gallery' : $style) );
		}
		
		?></div><?php
	
		academee_show_pagination();
		
		if (!$args['echo']) {
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}
	}
}

// AJAX handler for the academee_ajax_get_posts action
if ( !function_exists( 'academee_ajax_get_posts_callback' ) ) {
	add_action('wp_ajax_academee_ajax_get_posts',			'academee_ajax_get_posts_callback');
	add_action('wp_ajax_nopriv_academee_ajax_get_posts',	'academee_ajax_get_posts_callback');
	function academee_ajax_get_posts_callback() {
		if ( !wp_verify_nonce( academee_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
			wp_die();
	
		$id = !empty($_REQUEST['blog_template']) ? $_REQUEST['blog_template'] : 0;
		if ($id > 0) {
			academee_storage_set('blog_archive', true);
			academee_storage_set('blog_mode', 'blog');
			academee_storage_set('options_meta', get_post_meta($id, 'academee_options', true));
		}

		$response = array(
			'error'=>'', 
			'data' => academee_show_portfolio_posts(array(
							'cat' => (int) $_REQUEST['cat'],
							'parent_cat' => (int) $_REQUEST['parent_cat'],
							'page' => (int) $_REQUEST['page'],
							'post_type' => trim($_REQUEST['post_type']),
							'taxonomy' => trim($_REQUEST['taxonomy']),
							'blog_style' => trim($_REQUEST['blog_style']),
							'echo' => false
							)
						)
		);

		if (empty($response['data'])) {
			$response['error'] = esc_html__('Sorry, but nothing matched your search criteria.', 'academee');
		}
		echo json_encode($response);
		wp_die();
	}
}


// Show pagination
if ( !function_exists('academee_show_pagination') ) {
	function academee_show_pagination() {
		global $wp_query;
		// Pagination
		$pagination = academee_get_theme_option('blog_pagination');
		if ($pagination == 'pages') {
			the_posts_pagination( array(
				'mid_size'  => 2,
				'prev_text' => esc_html__( '<', 'academee' ),
				'next_text' => esc_html__( '>', 'academee' ),
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__( 'Page', 'academee' ) . ' </span>',
			) );
		} else if ($pagination == 'more' || $pagination == 'infinite') {
			$page_number = get_query_var('paged') ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page') : 1);
			if ($page_number < $wp_query->max_num_pages) {
				?>
				<div class="nav-links-more<?php if ($pagination == 'infinite') echo ' nav-links-infinite'; ?>">
					<a class="nav-load-more" href="#" 
						data-page="<?php echo esc_attr($page_number); ?>" 
						data-max-page="<?php echo esc_attr($wp_query->max_num_pages); ?>"
						><span><?php esc_html_e('Load more posts', 'academee'); ?></span></a>
				</div>
				<?php
			}
		} else if ($pagination == 'links') {
			?>
			<div class="nav-links-old">
				<span class="nav-prev"><?php previous_posts_link( is_search() ? esc_html__('Previous posts', 'academee') : esc_html__('Newest posts', 'academee') ); ?></span>
				<span class="nav-next"><?php next_posts_link( is_search() ? esc_html__('Next posts', 'academee') : esc_html__('Older posts', 'academee'), $wp_query->max_num_pages ); ?></span>
			</div>
			<?php
		}
	}
}

// Return template for the single field in the comments
if ( ! function_exists( 'academee_single_comments_field' ) ) {
    function academee_single_comments_field( $args ) {
        $path_height = 'path' == $args['form_style']
            ? ( 'text' == $args['field_type'] ? 75 : 190 )
            : 0;
        $html = '<div class="comments_field comments_' . esc_attr( $args['field_name'] ) . '">'
            . ( 'default' == $args['form_style'] && 'checkbox' != $args['field_type']
                ? '<label for="' . esc_attr( $args['field_name'] ) . '" class="' . esc_attr( $args['field_req'] ? 'required' : 'optional' ) . '">' . esc_html( $args['field_title'] ) . '</label>'
                : ''
            )
            . '<span class="sc_form_field_wrap">';
        if ( 'text' == $args['field_type'] ) {
            $html .= '<input id="' . esc_attr( $args['field_name'] ) . '" name="' . esc_attr( $args['field_name'] ) . '" type="text"' . ( 'default' == $args['form_style'] ? ' placeholder="' . esc_attr( $args['field_placeholder'] ) . ( $args['field_req'] ? ' *' : '' ) . '"' : '' ) . ' value="' . esc_attr( $args['field_value'] ) . '"' . ( $args['field_req'] ? ' aria-required="true"' : '' ) . ' />';
        } elseif ( 'checkbox' == $args['field_type'] ) {
            $html .= '<input id="' . esc_attr( $args['field_name'] ) . '" name="' . esc_attr( $args['field_name'] ) . '" type="checkbox" value="' . esc_attr( $args['field_value'] ) . '"' . ( $args['field_req'] ? ' aria-required="true"' : '' ) . ' />'
                . ' <label for="' . esc_attr( $args['field_name'] ) . '" class="' . esc_attr( $args['field_req'] ? 'required' : 'optional' ) . '">' . wp_kses( $args['field_title'], 'academee_kses_content' ) . '</label>';
        } else {
            $html .= '<textarea id="' . esc_attr( $args['field_name'] ) . '" name="' . esc_attr( $args['field_name'] ) . '"' . ( 'default' == $args['form_style'] ? ' placeholder="' . esc_attr( $args['field_placeholder'] ) . ( $args['field_req'] ? ' *' : '' ) . '"' : '' ) . ( $args['field_req'] ? ' aria-required="true"' : '' ) . '></textarea>';
        }
        if ( 'default' != $args['form_style'] ) {
            $html .= '<span class="sc_form_field_hover">'
                . ( 'path' == $args['form_style']
                    ? '<svg class="sc_form_field_graphic" preserveAspectRatio="none" viewBox="0 0 520 ' . intval( $path_height ) . '" height="100%" width="100%"><path d="m0,0l520,0l0,' . intval( $path_height ) . 'l-520,0l0,-' . intval( $path_height ) . 'z"></svg>'
                    : ''
                )
                . ( 'iconed' == $args['form_style']
                    ? '<i class="sc_form_field_icon ' . esc_attr( $args['field_icon'] ) . '"></i>'
                    : ''
                )
                . '<span class="sc_form_field_content" data-content="' . esc_attr( $args['field_title'] ) . '">' . wp_kses( $args['field_title'], 'academee_kses_content' ) . '</span>'
                . '</span>';
        }
        $html .= '</span></div>';
        return $html;
    }
}

?>