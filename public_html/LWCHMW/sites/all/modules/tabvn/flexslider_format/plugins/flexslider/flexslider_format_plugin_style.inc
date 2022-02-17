<?php

class FlexsliderFormatPluginStyle extends views_plugin_style {

  /**
   * Definition.
   */
  public function option_definition() {
    $options = parent::option_definition();

    $options['animation'] = array('default' => 'slide');
    $options['slideshow'] = array('default' => 1);
    $options['slideshowSpeed'] = array('default' => 7000);
    $options['animationSpeed'] = array('default' => 600);
    $options['animationLoop'] = array('default' => 1);
    $options['itemWidth'] = array('default' => 370);
    $options['itemMargin'] = array('default' => 30);
    $options['minItems'] = array('default' => 1);
    $options['maxItems'] = array('default' => 3);
    $options['move'] = array('default' => 0);
    $options['controlNav'] = array('default' => 1);
    $options['directionNav'] = array('default' => 1);

    return $options;
  }

  /**
   * Form.
   */
  function options_form(&$form, &$form_state) {

    // $fields = $this->display->handler->get_handlers('field');

    $form['animation'] = array(
        '#title' => t('Animation'),
        '#type' => 'select',
        '#options' => array(
            'slide' => t('Slide'),
            'fade' => t('Fade'),
        ),
        '#default_value' => $this->options['animation'],
        '#description' => t('Controls the animation type, "fade" or "slide".')
    );

    $form['slideshow'] = array(
        '#type' => 'select',
        '#title' => t('Slideshow'),
        '#options' => array(
            0 => 'False',
            1 => 'True',
        ),
        '#default_value' => $this->options['slideshow'], // default false 0
        '#description' => t('Setup a slideshow for the slider to animate automatically.'),
    );

    $form['slideshowSpeed'] = array(
        '#title' => t('Slideshow speed'),
        '#type' => 'textfield',
        '#default_value' => $this->options['slideshowSpeed'], // default 7000
        '#description' => t('Set the speed of the slideshow cycling, in milliseconds.'),
    );
    $form['animationSpeed'] = array(
        '#title' => t('Animation speed'),
        '#type' => 'textfield',
        '#default_value' => $this->options['animationSpeed'], // default 600
        '#description' => t('Set the speed of animations, in milliseconds.'),
    );

    $form['animationLoop'] = array(
        '#type' => 'select',
        '#title' => t('Animation loop'),
        '#options' => array(
            0 => 'False',
            1 => 'True',
        ),
        '#default_value' => $this->options['animationLoop'], // default false 0
        '#description' => t('Gives the slider a seamless infinite loop.'),
    );

    $form['itemWidth'] = array(
        '#title' => t('Item width'),
        '#type' => 'textfield',
        '#default_value' => $this->options['itemWidth'], // default 370
        '#description' => t('Box-model width of individual carousel items, including horizontal borders and padding.'),
    );
    $form['itemMargin'] = array(
        '#title' => t('Item margin'),
        '#type' => 'textfield',
        '#default_value' => $this->options['itemMargin'], // default 30
        '#description' => t('Margin between carousel items.'),
    );
    $form['minItems'] = array(
        '#title' => t('Minimum items'),
        '#type' => 'textfield',
        '#default_value' => $this->options['minItems'], // default 1
        '#description' => t('Minimum number of carousel items that should be visible.'),
    );
    $form['maxItems'] = array(
        '#title' => t('Maximum items'),
        '#type' => 'textfield',
        '#default_value' => $this->options['maxItems'], // default 3
        '#description' => t('Maximum number of carousel items that should be visible.'),
    );
    $form['move'] = array(
        '#title' => t('Move'),
        '#type' => 'textfield',
        '#default_value' => $this->options['move'], // default 0
        '#description' => t('Number of carousel items that should move on animation.'),
    );

    $form['controlNav'] = array(
        '#title' => t('Control navigation'),
        '#type' => 'select',
        '#options' => array(
            0 => 'False',
            1 => 'True',
        ),
        '#default_value' => $this->options['controlNav'], // default false 0
        '#description' => t('Create navigation for paging control of each slide.'),
    );

    $form['directionNav'] = array(
        '#title' => t('Control navigation'),
        '#type' => 'select',
        '#options' => array(
            0 => 'False',
            1 => 'True',
        ),
        '#default_value' => $this->options['directionNav'], // default false 0
        '#description' => t('Create previous/next arrow navigation.'),
    );
  }

}
