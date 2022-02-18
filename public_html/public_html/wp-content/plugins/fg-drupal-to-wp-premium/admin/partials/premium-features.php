			<tr>
				<th scope="row"><?php _e('Custom post types format:', 'fgd2wpp'); ?></th>
				<td>
					<input id="cpt_format_acf" name="cpt_format" type="radio" value="acf" <?php checked($data['cpt_format'], 'acf'); ?> /> <label for="cpt_format_acf" ?><?php _e('ACF + CPT UI', 'fgd2wpp'); ?></label>&nbsp;&nbsp;<small><?php printf(__('The <a href="%s" target="_blank">ACF plugin</a> and the <a href="%s" target="_blank">CPT UI plugin</a> are required.', 'fgd2wpp'), 'https://wordpress.org/plugins/advanced-custom-fields/', 'https://wordpress.org/plugins/custom-post-type-ui/'); ?></small><br />
					<input id="cpt_format_toolset" name="cpt_format" type="radio" value="toolset" <?php checked($data['cpt_format'], 'toolset'); ?> /> <label for="cpt_format_toolset" ?><?php _e('Toolset Types', 'fgd2wpp'); ?></label>&nbsp;&nbsp;<small><?php printf(__('The <a href="%s" target="_blank">Toolset Types plugin</a> is required.', 'fgd2wpp'), 'https://www.fredericgilles.net/toolset-types'); ?></small>
				</td>
			</tr>
			
			<tr>
				<th scope="row"><?php _e('Users:', 'fgd2wpp'); ?></th>
				<td>
					<input id="unicode_usernames" name="unicode_usernames" type="checkbox" value="1" <?php checked($data['unicode_usernames'], 1); ?> /> <label for="unicode_usernames" ><?php _e("Allow Unicode characters in the usernames", 'fgd2wpp'); ?></label>
				</td>
			</tr>
			
			<tr>
				<th scope="row"><?php _e('SEO:', 'fgd2wpp'); ?></th>
				<td>
					<input id="url_redirect" name="url_redirect" type="checkbox" value="1" <?php checked($data['url_redirect'], 1); ?> /> <label for="url_redirect" ><?php _e("Redirect the Drupal URLs", 'fgd2wpp'); ?></label>
				</td>
			</tr>
			
			<tr>
				<th scope="row"><?php _e('Partial import:', 'fgd2wpp'); ?></th>
				<td>
					<div id="partial_import_toggle"><?php _e('expand / collapse', 'fgd2wpp'); ?></div>
					<div id="partial_import">
					<input id="skip_taxonomies" name="skip_taxonomies" type="checkbox" value="1" <?php checked($data['skip_taxonomies'], 1); ?> /> <label for="skip_taxonomies" ><?php _e('Don\'t import the taxonomies', 'fgd2wpp'); ?></label>
					<br />
					<input id="skip_nodes" name="skip_nodes" type="checkbox" value="1" <?php checked($data['skip_nodes'], 1); ?> /> <label for="skip_nodes" ><?php _e('Don\'t import the nodes', 'fgd2wpp'); ?></label>
					<br />
					<div id="skip_nodes_box">
						<small><a href="#" id="toggle_node_types"><?php _e('Select / Deselect all', 'fgd2wpp'); ?></a></small><br />
						<div id="partial_import_nodes"><?php echo $data['partial_import_nodes']; ?></div>
					</div>
					<input id="skip_users" name="skip_users" type="checkbox" value="1" <?php checked($data['skip_users'], 1); ?> /> <label for="skip_users" ><?php _e('Don\'t import the users', 'fgd2wpp'); ?></label>
					<br />
					<input id="skip_menus" name="skip_menus" type="checkbox" value="1" <?php checked($data['skip_menus'], 1); ?> /> <label for="skip_menus" ><?php _e('Don\'t import the menus', 'fgd2wpp'); ?></label>
					<br />
					<input id="skip_comments" name="skip_comments" type="checkbox" value="1" <?php checked($data['skip_comments'], 1); ?> /> <label for="skip_comments" ><?php _e('Don\'t import the comments', 'fgd2wpp'); ?></label>
					<br />
					<input id="skip_blocks" name="skip_blocks" type="checkbox" value="1" <?php checked($data['skip_blocks'], 1); ?> /> <label for="skip_blocks" ><?php _e('Don\'t import the blocks', 'fgd2wpp'); ?></label>
					<br />
					<input id="skip_redirects" name="skip_redirects" type="checkbox" value="1" <?php checked($data['skip_redirects'], 1); ?> /> <label for="skip_redirects" ><?php _e('Don\'t import the redirects', 'fgd2wpp'); ?></label>
					<?php do_action('fgd2wp_post_display_partial_import_options', $data); ?>
					</div>
				</td>
			</tr>
