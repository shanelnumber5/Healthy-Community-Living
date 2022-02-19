<?php
/**
 * @file Classes file
 *
 * @author Duynv
 */

class MDSlider {
    public $slid;
    public $title;
    public $description;
    public $machine_name;
    public $settings;

    public function db_fields() {
        return array('title', 'description', 'machine_name', 'settings');
    }

    public function initialize() {
        if (is_string($this->settings)) {
            $this->settings = unserialize($this->settings);
        };
    }

    public function save() {
        $response = FALSE;
        $fields = array();
        if ($this->title == NULL || $this->title == '') {
            return $response;
        }

        foreach ($this->db_fields() as $field) {
            $fields[$field] = $this->{$field};
        }

        if (is_array($fields['settings'])) {
            $fields['settings'] = serialize($fields['settings']);
        }

        if ($this->slid) {
            $response = db_update('md_sliders')
                ->fields($fields)
                ->condition('slid', $this->slid)
                ->execute();
        } else {
            $slid = db_insert('md_sliders')
                ->fields($fields)
                ->execute();

            if ($slid > 0) {
                $this->slid = $slid;
                $response = TRUE;
            }
        }

        return $response;
    }

    public static function create($title, $description = NULL, $settings = NULL, $machine_name = NULL) {
        $default_settings = MDSlider::get_default_settings();

        if ($settings) {
            $settings = array_merge($default_settings, $settings);
        } else {
            $settings = $default_settings;
        }

        # Create new MDSlider object
        $slider = new MDSlider();
        $slider->title = $title;
        $slider->description = $description;
        $slider->machine_name = ($machine_name) ? $machine_name : uniqid('md-slider-');
        $slider->settings = $settings;

        if ($slider->save()) {
            return $slider;
        }

        return NULL;
    }

    public static function get_all() {
        $results = db_select('{md_sliders}', 'mss')
            ->fields('mss')
            ->execute()
            ->fetchAll(PDO::FETCH_CLASS, 'MDSlider');

        if (!count($results)) {
            return array();
        }

        # Run object initialize
        foreach ($results as &$slider) {
            if ($slider instanceof stdClass)
                $slider = _md_slider_recast('MDSlider', $slider);

            $slider->initialize();
        }

        # Return list menus
        return $results;
    }

    public static function get_by_id($slid) {
        $results = db_select('{md_sliders}', 'mss')
            ->fields('mss')
            ->condition('slid', $slid)
            ->execute()
            ->fetchAll(PDO::FETCH_CLASS, 'MDSlider');

        if (!count($results)) {
            return array();
        }

        # Run object initialize
        foreach ($results as &$slider) {
            if ($slider instanceof stdClass)
                $slider = _md_slider_recast('MDSlider', $slider);

            $slider->initialize();
        }

        # Return list menus
        return array_shift($results);
    }

    public static function get_by_machine_name($machine_name) {
        $results = db_select('{md_sliders}', 'mss')
            ->fields('mss')
            ->condition('machine_name', $machine_name)
            ->execute()
            ->fetchAll(PDO::FETCH_CLASS, 'MDSlider');

        if (!count($results)) {
            return NULL;
        }

        # Run object initialize
        foreach ($results as &$slider) {
            if ($slider instanceof stdClass)
                $slider = _md_slider_recast('MDSlider', $slider);

            $slider->initialize();
        }

        # Return list menus
        return array_shift($results);
    }

    public static function get_by_ids($slids) {
        $results = db_select('{md_sliders}', 'mss')
            ->fields('mss')
            ->condition('slid', $slids, 'IN')
            ->execute()
            ->fetchAll(PDO::FETCH_CLASS, 'MDSlider');

        if (!count($results)) {
            return array();
        }

        # Run object initialize
        foreach ($results as &$slider) {
            if ($slider instanceof stdClass)
                $slider = _md_slider_recast('MDSlider', $slider);

            $slider->initialize();
        }

        # Return list menus
        return $results;
    }

    public static function delete($slid) {
        # Delete all slides of slider
        MDSlide::delete_by_slider($slid);

        # Delete slider
        return db_delete('md_sliders')
            ->condition('slid', $slid)
            ->execute();
    }

    public static function get_default_settings($key = NULL) {
        static $default_settings = array(
            'full_width' => 0,
            'width' => 960,
            'height' => 420,
            'touch_swipe' => 1,
            'responsive' => 1,
            'videobox' => 0,
            'loop' => 1,
            'loadingbar' => 'bar',
            'bar_position' => 'bottom',
            'show_next_prev_button' => 1,
            'auto_play' => 1,
            'pause_hover' => 1,
            'show_bullet' => 1,
            'bullet_position' => 5,
            'show_thumbnail' => 1,
            'thumbnail_position' => 1,
            'border_style' => 0,
            'dropshadow_style' => 0,
            'animation' => 'fade',
            'delay' => 8000,
            'transtime' => 800,
            'thumb_width' => 100,
            'thumb_height' => 75,
            'create_bg_imgstyle' => 1,
            'dmf_google' => '',
            'enable_font_css' => 0,
            'generate_css_file' => 0,
            "on_start" => "",
            "on_end" => "",
            'color_saved' => 'ff9900,CC0000',
        );

        if ($key) {
            return $default_settings[$key];
        }

        return $default_settings;
    }
}

/**
 * MDSlide class
 */
class MDSlide {
    public $sid;
    public $slid;
    public $position;
    public $settings;
    public $layers;

    public function db_fields() {
        return array('slid', 'position', 'settings', 'layers');
    }

    public function initialize() {
        if (is_string($this->settings)) {
            $this->settings = unserialize($this->settings);
        }

        if (is_string($this->layers)) {
            $this->layers = unserialize($this->layers);
        }
    }

    public function save() {
        $fields = array();
        foreach ($this->db_fields() as $field_name) {
            $fields[$field_name] = $this->{$field_name};
        }

        if (is_array($fields['layers'])) {
            $fields['layers'] = serialize($fields['layers']);
        }

        if (is_array($fields['settings'])) {
            $fields['settings'] = serialize($fields['settings']);
        }

        if ($this->sid) {
            db_update('md_slides')
                ->fields($fields)
                ->condition('sid', $this->sid)
                ->execute();
        } else {
            db_insert('md_slides')
                ->fields($fields)
                ->execute();
        }
    }

    public static function create($slid, $position, $settings = NULL, $layers = NULL) {
        $slider = MDSlider::get_by_id($slid);
        if ($slider == NULL) {
            return;
        }

        $new_slide = new MDSlide();
        $new_slide->slid = $slid;
        $new_slide->position = $position;
        $new_slide->settings = $settings;
        $new_slide->layers = $layers;

        $new_slide->save();
    }

    public static function get_by_slider_id($slid) {
        $results = db_select('md_slides', 'mds')
            ->fields('mds')
            ->condition('slid', $slid)
            ->orderBy('position')
            ->execute()
            ->fetchAll(PDO::FETCH_CLASS, 'MDSlide');

        if (count($results) > 0) {
            foreach ($results as &$slide) {
                if ($slide instanceof stdClass) {
                    $slide = _md_slider_recast("MDSlide", $slide);
                }

                $slide->initialize();
            }

            return $results;
        }

        return array();
    }

    public static function get_by_sliders($slids) {
        $results = db_select('md_slides', 'mds')
            ->fields('mds')
            ->condition('slid', $slids, 'IN')
            ->orderBy('position')
            ->execute()
            ->fetchAll(PDO::FETCH_CLASS, 'MDSlide');

        if (count($results) > 0) {
            foreach ($results as &$slide) {
                if ($slide instanceof stdClass)
                    $slide = _md_slider_recast("MDSlide", $slide);

                $slide->initialize();
            }

            return $results;
        }

        return array();
    }

    public static function get($sid) {
        $slide = NULL;
        $results = db_select('md_slides', 'mds')
            ->fields('mds')
            ->condition('sid', $sid)
            ->execute()
            ->fetchAll(PDO::FETCH_CLASS, 'MDSlide');

        if (count($results) > 0) {
            $slide = array_shift($results);
            if ($slide instanceof stdClass)
                $slide = _md_slider_recast("MDSlide", $slide);

            $slide->initialize();
        }

        return $slide;
    }

  public static function get_all() {
    $slides = db_select('md_slides', 'mds')
      ->fields('mds')
      ->execute()
      ->fetchAll(PDO::FETCH_CLASS, 'MDSlide');

    foreach ($slides as &$slide) {
      if ($slide instanceof stdClass)
        $slide = _md_slider_recast("MDSlide", $slide);

      $slide->initialize();
    }

    return $slides;
  }

    public static function delete($sid) {
        return db_delete('md_slides')
            ->condition('sid', $sid)
            ->execute();
    }

    public static function delete_by_slider($slid) {
        return db_delete('md_slides')
            ->condition('slid', $slid)
            ->execute();
    }

    public static function get_default_settings($key = NULL) {
        static $default_settings = array(
            'background_image' => -1,
            'background_color' => "",
            'background_overlay' => "",
            'timelinewidth' => 80,
            'custom_thumbnail' => -1,
            'disabled' => 0,
            "transitions" => array(),
        );

        if ($key && $key != '') {
            return $default_settings[$key];
        }

        return $default_settings;
    }
}

class MDCommon {
    public static $in_effects = array(
        'bounceIn',
        'bounceInDown',
        'bounceInUp',
        'bounceInLeft',
        'bounceInRight',
        'fadeIn',
        'fadeInUp',
        'fadeInDown',
        'fadeInLeft',
        'fadeInRight',
        'fadeInUpBig',
        'fadeInDownBig',
        'fadeInLeftBig',
        'fadeInRightBig',
        'flipInX',
        'flipInY',
        'foolishIn', //-
        'lightSpeedIn',
        'puffIn', //-
        'rollIn',
        'rotateIn',
        'rotateInDownLeft',
        'rotateInDownRight',
        'rotateInUpLeft',
        'rotateInUpRight',
        'twisterInDown', //-
        'twisterInUp', //-
        'swap', //-
        'swashIn', //-
        'tinRightIn', //-
        'tinLeftIn', //-
        'tinUpIn', //-
        'tinDownIn', //-
        'vanishIn' //-
    );

    public static $out_effects = array(
        'bombRightOut', //-
        'bombLeftOut', //-
        'bounceOut',
        'bounceOutDown',
        'bounceOutUp',
        'bounceOutLeft',
        'bounceOutRight',
        'fadeOut',
        'fadeOutUp',
        'fadeOutDown',
        'fadeOutLeft',
        'fadeOutRight',
        'fadeOutUpBig',
        'fadeOutDownBig',
        'fadeOutLeftBig',
        'fadeOutRightBig',
        'flipOutX',
        'flipOutY',
        'foolishOut', //-
        'hinge',
        'holeOut', //-
        'lightSpeedOut',
        'puffOut', //-
        'rollOut',
        'rotateOut',
        'rotateOutDownLeft',
        'rotateOutDownRight',
        'rotateOutUpLeft',
        'rotateOutUpRight',
        'rotateDown', //-
        'rotateUp', //-
        'rotateLeft', //-
        'rotateRight', //-
        'swashOut', //-
        'tinRightOut', //-
        'tinLeftOut', //-
        'tinUpOut', //-
        'tinDownOut', //-
        'vanishOut' //-
    );

    public static $default_layers = array(
        'text' => array(
            'type' => 'text',
            'width' => 100,
            'height' => 20,
            'left' => 0,
            'top' => 0,
            'starttime' => 0,
            'stoptime' => 30,
            'startani' => 'none',
            'stopani' => 'none',
            'style' => 'style1',
            'zindex' => 1000,
            'title' => 'Text',
            'background' => '',
            'backgroundtransparent' => 100,
            'fontsize' => '',
            'fontstyle' => '',
            'textalign' => '',
            'color' => '',
            'transparent' => 100,
        ),
        'image' => array(
            'type' => 'image',
            'width' => 100,
            'height' => 20,
            'left' => 0,
            'top' => 0,
            'starttime' => 0,
            'stoptime' => 30,
            'startani' => 'none',
            'stopani' => 'none',
            'style' => 'style1',
            'zindex' => 1000,
            'title' => '',
            'fileid' => '',
            'background' => '',
            'backgroundtransparent' => 100,
            'transparent' => 100,
        ),
        'video' => array(
            'type' => 'video',
            'width' => 100,
            'height' => 20,
            'left' => 0,
            'top' => 0,
            'starttime' => 0,
            'stoptime' => 30,
            'startani' => 'none',
            'stopani' => 'none',
            'style' => 'style1',
            'zindex' => 1000,
            'title' => '',
            'fileid' => '',
            'background' => '',
            'backgroundtransparent' => 100,
            'transparent' => 100,
        ),
    );
}
