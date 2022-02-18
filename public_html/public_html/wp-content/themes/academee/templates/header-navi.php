<?php
/**
 * The template to display the main menu
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
 */


$academee_header_top_text_phone = academee_get_theme_option('header_top_text_phone');
$academee_header_top_text_mail = academee_get_theme_option('header_top_text_mail');

if (academee_exists_trx_addons()){
?>
<div class="top_panel_default_top">
	<div class="content_wrap top_panel_default_header">
		<div class="columns_wrap">
			<div class="header_top_text_phone top_panel_default_left sc_layouts_column sc_layouts_column_align_left sc_layouts_column_icons_position_left">
				<?php
				academee_show_layout($academee_header_top_text_phone)
				?>
			</div>
			<div class="header_top_text_mail top_panel_default_left sc_layouts_column sc_layouts_column_align_left sc_layouts_column_icons_position_left">
				<?php
				academee_show_layout($academee_header_top_text_mail)
				?>
			</div>
		</div>
	</div>
</div>

<?php } ?>

<div class="top_panel_navi sc_layouts_row sc_layouts_row_type_compact sc_layouts_row_fixed sc_layouts_row_delimiter
			scheme_<?php echo esc_attr(academee_is_inherit(academee_get_theme_option('menu_scheme')) 
												? (academee_is_inherit(academee_get_theme_option('header_scheme')) 
													? academee_get_theme_option('color_scheme') 
													: academee_get_theme_option('header_scheme')) 
												: academee_get_theme_option('menu_scheme')); ?>">
	<div class="content_wrap">
		<div class="columns_wrap">
			<div class="sc_layouts_column sc_layouts_column_align_left sc_layouts_column_icons_position_left column-1_4">
				<?php
				// Logo
				?><div class="sc_layouts_item"><?php
					get_template_part( 'templates/header-logo' );
				?></div>
			</div><?php
			
			// Attention! Don't place any spaces between columns!
			?><div class="sc_layouts_column sc_layouts_column_align_right sc_layouts_column_icons_position_left column-3_4">
				<div class="sc_layouts_item">
					<?php
					// Main menu
					$academee_menu_main = academee_get_nav_menu(array(
						'location' => 'menu_main', 
						'class' => 'sc_layouts_menu sc_layouts_menu_default sc_layouts_hide_on_mobile'
						)
					);
					if (empty($academee_menu_main)) {
						$academee_menu_main = academee_get_nav_menu(array(
							'class' => 'sc_layouts_menu sc_layouts_menu_default sc_layouts_hide_on_mobile'
							)
						);
					}
					academee_show_layout($academee_menu_main);
					// Mobile menu button
					?>
					<div class="sc_layouts_iconed_text sc_layouts_menu_mobile_button">
						<a class="sc_layouts_item_link sc_layouts_iconed_text_link" href="#">
							<span class="sc_layouts_item_icon sc_layouts_iconed_text_icon trx_addons_icon-menu"></span>
						</a>
					</div>
				</div><?php
			
				// Attention! Don't place any spaces between layouts items!
				?>
				<div class="sc_layouts_item">
					<?php
					// Display search field
					do_action('academee_action_search', 'fullscreen', 'header_search', false);
					?>
				</div>			
			</div>
		</div><!-- /.sc_layouts_row -->
	</div><!-- /.content_wrap -->
</div><!-- /.top_panel_navi -->