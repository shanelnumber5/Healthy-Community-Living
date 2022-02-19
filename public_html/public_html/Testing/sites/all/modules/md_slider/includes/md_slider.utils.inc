<?php
/**
 * @file: md_slider.utils.inc
 * User: Duy
 * Date: 2/5/13
 * Time: 6:11 PM
 */

/**
 * Get video data
 */
function get_video_data($type, $fid) {
    switch ($type) {
        case 'youtube':
            $output['type'] = 'youtube';
            $output['fid'] = $fid;
            $url = "http://www.youtube.com/oembed?url=https://youtu.be/{$fid}&format=json";
            $data = drupal_http_request($url);
            if ($data->code == '200') {
                $output['data'] = json_decode($data->data);
            }
            break;

        case 'vimeo':
            $output['type'] = 'vimeo';
            $url = "http://vimeo.com/api/v2/video/{$fid}.php";
            $data = drupal_http_request($url);
            if ($data->code == '200') {
                $info = unserialize($data->data);
                $output['data'] = (object)$info[0];
            }
            break;
    };

    return $output;
}

/**
 * Convert hex to rgb value
 */
function hex_to_rgb($hex) {
    $hex = str_replace("#", "", $hex);

    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    return array($r, $g, $b);
}

/**
 * Analyse google link to get font information
 */
function md_slider_process_google_web_font($fonts) {
    preg_match('/([^\?]+)(\?family=)?([^&\']+)/i', $fonts, $matches);
    $gfonts = explode("|", $matches[3]);

    for ($i = 0; $i < count($gfonts); $i++) {
        $gfontsdetail = explode(":", $gfonts[$i]);
        $gfontname = str_replace("+", " ", $gfontsdetail['0']);
        $gfontweigth = "";
        if (array_key_exists('1', $gfontsdetail)) {
            $gfontweigth = $gfontsdetail['1'];
        }
        $fontvars[] = array(
            'CSS' => $gfontname,
            'Weight' => $gfontweigth,
        );
    }

    return $fontvars;
}

/**
 * Process style for layer
 */
function process_layer_style($layer) {
    $output = array();
    if (isset($layer->backgroundcolor)) {
        if (isset($layer->backgroundtransparent)) {
            $rgb = hex_to_rgb($layer->backgroundcolor);
            $alpha = $layer->backgroundtransparent / 100;
            $output[] = "background: rgb({$rgb[0]}, {$rgb[1]}, {$rgb[2]});background: rgba({$rgb[0]}, {$rgb[1]}, {$rgb[2]}, {$alpha});\n";
        } else {
            if (is_string($layer->backgroundcolor)) {
                $output[] = "background: {$layer->backgroundcolor};\n";
            } else {
                $output[] = "background: #000;\n";
            }
        }
    }

    // Generate css for z-index of layers
    if (!isset($layer->link) || !is_array($layer->link))
        $output[] = "z-index: {$layer->zindex} !important;";

    # Process style for layer border
    if (isset($layer->borderposition) && isset($layer->borderwidth) && $layer->borderposition > 0 && $layer->borderwidth > 0) {
        $color = (isset($layer->bordercolor)) ? $layer->bordercolor : "#000";
        $border_style = (isset($layer->borderstyle)) ? $layer->borderstyle : "solid";

        # Process border layer position
        $positions = process_layer_border_position($layer->borderposition);
        if (count($positions) == 4) {
            $output[] = "border: {$layer->borderwidth}px {$border_style} {$color};\n";
        } else {
            foreach ($positions as $position) {
                $output[] = "border-{$position}: {$layer->borderwidth}px {$border_style} {$color};\n";
            }
        }
    }

    if (isset($layer->bordertopleftradius) && $layer->bordertopleftradius > 0) {
        $output[] = "-webkit-border-top-left-radius: {$layer->bordertopleftradius}px; -moz-border-radius-topleft: {$layer->bordertopleftradius}px; border-top-left-radius: {$layer->bordertopleftradius}px;\n";
    }

    if (isset($layer->bordertoprightradius) && $layer->bordertoprightradius > 0) {
        $output[] = "-webkit-border-top-right-radius: {$layer->bordertoprightradius}px; -moz-border-radius-topright: {$layer->bordertoprightradius}px; border-top-right-radius: {$layer->bordertoprightradius}px;\n";
    }

    if (isset($layer->borderbottomleftradius) && $layer->borderbottomleftradius > 0) {
        $output[] = "-webkit-border-bottom-left-radius: {$layer->borderbottomleftradius}px; -moz-border-radius-bottomleft: {$layer->borderbottomleftradius}px; border-bottom-left-radius: {$layer->borderbottomleftradius}px;\n";
    }

    if (isset($layer->borderbottomrightradius) && $layer->borderbottomrightradius > 0) {
        $output[] = "-webkit-border-bottom-right-radius: {$layer->borderbottomrightradius}px; -moz-border-radius-bottomright: {$layer->borderbottomrightradius}px; border-bottom-right-radius: {$layer->borderbottomrightradius}px;\n";
    }

    // Process for padding settings
    if (isset($layer->paddingtop) && $layer->paddingtop > 0)
      $output[] = "padding-top: {$layer->paddingtop}px;\n";

    if (isset($layer->paddingright) && $layer->paddingright > 0)
      $output[] = "padding-right: {$layer->paddingright}px;\n";

    if (isset($layer->paddingbottom) && $layer->paddingbottom > 0)
      $output[] = "padding-bottom: {$layer->paddingbottom}px;\n";

    if (isset($layer->paddingleft) && $layer->paddingleft > 0)
      $output[] = "padding-left: {$layer->paddingleft}px;\n";

    # Process styles for text layer
    if ($layer->type == 'text') {
        if (isset($layer->color) && $layer->color != '') {
            if ($layer->color == '0')
                $output[] = "color: #000 !important;\n";
            else
                $output[] = "color: {$layer->color} !important;\n";
        }

        if (isset($layer->textalign) && $layer->textalign != '') {
            $output[] = "text-align: {$layer->textalign};\n";
        }

        if (isset($layer->fontsize) && is_numeric($layer->fontsize)) {
            $font_size = $layer->fontsize / 12;
            $output[] = "font-size: {$font_size}em;\n";
        }

        if (isset($layer->fontweight) && $layer->fontweight != '') {
            if (is_numeric($layer->fontweight) === FALSE) {
                $font_w = substr($layer->fontweight, 0, 3);
                $font_s = substr($layer->fontweight, 3);
                $output[] = "font-weight: {$font_w};\n";
                $output[] = "font-style: {$font_s};\n";
            } else
                $output[] = "font-weight: {$layer->fontweight};\n";
        }

        if (isset($layer->fontfamily) && $layer->fontfamily != '') {
            $output[] = "font-family: \"{$layer->fontfamily}\";\n";
        }

        if (isset($layer->textdecoration) && $layer->textdecoration != '') {
            $output[] = "text-decoration: {$layer->textdecoration};\n";
        }

        if (isset($layer->texttransform) && $layer->texttransform != '') {
            $output[] = "text-transform: {$layer->texttransform};\n";
        }
    }

    if (count($output))
        return " {\n" . implode(' ', $output) . "}";
    else
        return "";
}

/**
 * Process border layer position
 */
function process_layer_border_position($border_position) {
    $border_pos = array();

    if ($border_position & 1)
        $border_pos[] = "top";

    if ($border_position & 2)
        $border_pos[] = "right";

    if ($border_position & 4)
        $border_pos[] = "bottom";

    if ($border_position & 8)
        $border_pos[] = "left";

    return $border_pos;
}

/**
 * Generate css class for layer link
 */
function md_slider_generate_layer_link_css($link) {
    $output = array();

    if ($link['color'] != '') {
        if ($link['color'] == '0')
            $output[] = "color: #000 !important;\n";
        else
            $output[] = "color: {$link['color']} !important;\n";
    }

    if ($link['background'] != '') {
        if ($link['background'] == '0')
            $output[] = "background-color: #000;\n";
        else
            $output[] = "background-color: {$link['background']};\n";
    }

    if ($link['transparent'] != '' && is_numeric($link['transparent'])) {
        $opacity = $link['transparent'] / 100;
        $output[] = "-ms-filter:'progid:DXImageTransform.Microsoft.Alpha(Opacity={$link['transparent']})'; filter: alpha(opacity={$link['transparent']}); opacity: {$opacity};";
    }

    if ($link['border'] != '') {
        if ($link['border'] == '0')
            $output[] = "border-color: #000;\n";
        else
            $output[] = "border-color: {$link['border']};\n";
    }

    if (count($output))
        return "{\n" . implode('', $output) . "}";
    else
        return "";
}

/**
 * Generate video embeded link
 */
function md_slider_generate_video_embeded_url($video_id) {
    if (strlen($video_id) == 11) {
        # Youtube video
        return "https://www.youtube.com/embed/{$video_id}";
    } else {
        # Vimeo video
        return "https://player.vimeo.com/video/{$video_id}";
    }
}

/**
 * Recast object to another class
 */
function _md_slider_recast($className, stdClass &$object) {
    if (!class_exists($className))
        throw new InvalidArgumentException(sprintf('Inexistant class %s.', $className));

    $new = new $className();

    foreach ($object as $property => &$value) {
        $new->$property = & $value;
        unset($object->$property);
    }
    unset($value);
    $object = (unset) $object;

    return $new;
}

