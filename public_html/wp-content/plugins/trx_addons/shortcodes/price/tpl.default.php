<?php
/**
 * The style "default" of the Price block
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

$args = get_query_var('trx_addons_args_sc_price');
?><div <?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?> 
	class="sc_price sc_price_<?php
			echo esc_attr($args['type']);
			if (!empty($args['class'])) echo ' '.esc_attr($args['class']);
	?>"<?php
	if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
?>><?php

	// Label
	if (!empty($args['label'])) {
		?><div class="sc_price_label"><?php echo esc_html($args['label']); ?></div><?php
	}

	// Show image
	if (!empty($args['image'])) {
		if (!empty($args['link'])) {
			?><a href="<?php echo esc_url($args['link']); ?>" class="sc_price_image"><?php
		} else {
			?><div class="sc_price_image"><?php
		}
		$args['image'] = trx_addons_get_attachment_url($args['image'], trx_addons_get_thumb_size('medium'));
		$attr = trx_addons_getimagesize($args['image']);
		?><img src="<?php echo esc_url($args['image']); ?>" alt=""<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>><?php
		if (!empty($args['link'])) {
			?></a><?php
		} else {
			?></div><?php
		}

	// Show icon
	} else if (!empty($args['icon'])) {
		if (!empty($args['link'])) {
			?><a href="<?php echo esc_url($args['link']); ?>" class="sc_price_icon"><?php
		} else {
			?><div class="sc_price_icon"><?php
		}
		?><span class="<?php echo esc_attr($args['icon']); ?>"></span><?php
		if (!empty($args['link'])) {
			?></a><?php
		} else {
			?></div><?php
		}
	}

	?><div class="sc_price_info"><?php

		if (!empty($args['subtitle'])) {
			?><div class="sc_price_subtitle"><?php echo esc_html($args['subtitle']); ?></div><?php
		}
		if (!empty($args['title'])) {
			?><div class="sc_price_title"><?php
				if (!empty($args['link'])) {
					?><a href="<?php echo esc_url($args['link']); ?>"><?php
				} 
				echo esc_html($args['title']); 
				if (!empty($args['link'])) {
					?></a><?php
				} 
			?></div><?php
		}
		if (!empty($args['description'])) {
			?><div class="sc_price_description"><?php echo wp_kses_post(trx_addons_parse_codes($args['description'])); ?></div><?php
		}
		if (!empty($args['price'])) {
			$parts = explode('.', trx_addons_parse_codes($args['price']));
			?><div class="sc_price_price"><?php
				if (!empty($args['currency'])) {
					?><span class="sc_price_currency"><?php echo esc_html($args['currency']); ?></span><?php
				}
				?><span class="sc_price_value"><?php echo wp_kses_post($parts[0]); ?></span><?php
				if (count($parts) > 1 && $parts[1]!='') {
					?><span class="sc_price_decimals"><?php echo wp_kses_post($parts[1]); ?></span><?php
				}
            if ( count($parts) > 1 && isset($parts[2]) && $parts[2]!='') {
                ?><span class="sc_price_go"><?php echo wp_kses_post($parts[2]); ?></span><?php
            }
			?></div><?php
		}
		if (!empty($args['content'])) {
			?><div class="sc_price_details"><?php trx_addons_show_layout($args['content']); ?></div><?php
		}

	if (!empty($args['link']) && !empty($args['link_text'])) {
		?><a href="<?php echo esc_url($args['link']); ?>" class="sc_price_link"><?php echo esc_html($args['link_text']); ?></a><?php
	}

	?></div>
</div>