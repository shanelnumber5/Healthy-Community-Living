<?php
/**
 * @file: md_slider_edit_form.tpl.php
 * User: Duy
 * Date: 1/23/13
 * Time: 3:37 PM
 */
?>
<div class="md-wrap">
  <?php print theme('admin_slides_render', array('slider' => $slider, 'slides' => $slides)); ?>
  <div id="md-toolbar" class="md-toolbar">
    <a href="#" class="mdt-button mdt-text" title="Add new text"></a>
    <a href="#" class="mdt-button mdt-image" title="Add new image"></a>
    <a href="#" class="mdt-button mdt-video" title="Add new video"></a>

    <a href="#" class="mdt-button mdt-align-left" title="Align left edge"></a>
    <a href="#" class="mdt-button mdt-align-center" title="Align horizontal center"></a>
    <a href="#" class="mdt-button mdt-align-right" title="Align right edge"></a>
    <a href="#" class="mdt-button mdt-align-top" title="Align top edge"></a>
    <a href="#" class="mdt-button mdt-align-vcenter" title="Align vertical center"></a>
    <a href="#" class="mdt-button mdt-align-bottom" title="Align bottom edge"></a>

    <a href="#" class="mdt-button mdt-spacev" title="Space evenly vertically"></a>
    <a href="#" class="mdt-button mdt-spaceh" title="Space evenly horizontally"></a>
    <input type="text" value="" class="mdt-text mdt-spacei"/>

    <a href="#" class="mdt-proportions mdt-proportions-yes" title="proportions"></a>
    <input type="text" value="" name="width" class="mdt-text mdt-input mdt-width"/>
    <input type="text" value="" name="height" class="mdt-text mdt-input mdt-height"/>
    <input type="text" value="" name="left" class="mdt-text mdt-input mdt-left"/>
    <input type="text" value="" name="top" class="mdt-text mdt-input mdt-top"/>

    <div class="mdt-item-type mdt-type-text">
      <div class="mdt-label">Text:</div>
      <textarea class="mdt-text mdt-textvalue" name="text"></textarea>
      <a href="#" class="mdt-button mdt-addlink" title="Add link"></a>

      <input type="text" value="12" name="font-size" class="mdt-text mdt-input mdt-fontsize"/>
      <span class="mdt-fontsizeunit"></span>
      <select name="font-family" class="mdt-select mdt-font-family">
        <option value=""></option>
        <optgroup label="System fonts">
          <option value="Arial" data-fontweight="400,700,400italic,700italic" style="font-family: Arial">Arial</option>
          <option value="Verdana" data-fontweight="400,700,400italic,700italic" style="font-family: Verdana">Verdana
          </option>
          <option value="Trebuchet MS" data-fontweight="400,700,400italic,700italic"
                  style="font-family: 'Trebuchet MS'">Trebuchet MS
          </option>
          <option value="Georgia" data-fontweight="400,700,400italic,700italic" style="font-family: Georgia">Georgia
          </option>
          <option value="Times New Roman" data-fontweight="400,700,400italic,700italic"
                  style="font-family: Times New Roman">Times New Roman
          </option>
          <option value="Tahoma" data-fontweight="400,700,400italic,700italic" style="font-family: Tahoma">Tahoma
          </option>
        </optgroup>
        <?php if (count($fonts) > 0) : ?>
        <optgroup label="Google fonts">
          <?php endif; ?>
          <?php foreach ($fonts as $font): ?>
            <option value="<?php print $font['CSS']; ?>" data-fontweight="<?php print $font['Weight']; ?>"
                    style="font-family: '<?php print $font['CSS']; ?>'"><?php print $font['CSS']; ?></option>
          <?Php endforeach; ?>
          <?php if (count($fonts) > 0) : ?>
        </optgroup>
      <?php endif; ?>
      </select>
      <select name="font-weight" class="mdt-select mdt-font-weight">
        <option value=""></option>
      </select>

      <a href="#" class="mdt-button button-style mdt-font-underline" name="text-decoration" active="underline"
         normal="none" title="Underline"></a>
      <a href="#" class="mdt-button button-style mdt-font-allcaps" name="text-transform" active="uppercase"
         normal="none" title="All caps"></a>

      <a href="#" class="mdt-button button-align mdt-left-alignment" value="left" title="Left alignment"></a>
      <a href="#" class="mdt-button button-align mdt-center-alignment" value="center" title="Center alignment"></a>
      <a href="#" class="mdt-button button-align mdt-right-alignment" value="right" title="Right alignment"></a>
      <a href="#" class="mdt-button button-align mdt-justified-alignment" value="justify"
         title="Justified alignment"></a>

      <a class="mdt-button mdt-text-color" title="Text color"></a>
      <div class="text-color"><input type="hidden" name="color" value="" class="mdt-color mdt-input"/></div>

      <input type="hidden" name="border-color" value="" class="mdt-border-color mdt-input"/>
    </div>

    <div class="mdt-item-type mdt-type-image" style="display: none;">
      <div class="mdti-image">
        <input type="hidden" value="" name="fileid" class="mdt-text mdt-input mdt-image-fileid"/>
        <img class="mdt-imgsrc" src=""/>
      </div>
      <a href="#" class="mdt-button mdt-addlink" title="Add link"></a>

      <div class="mdt-label">Image: <a id="change-image" href="#">Change</a></div>
      <textarea class="mdt-textarea mdt-imgalt" name="imgalt"></textarea>
    </div>
    <div class="mdt-item-type mdt-type-video" style="display: none;">
      <div class="mdti-image">
        <input type="hidden" value="" name="fileid" class="mdt-text mdt-input mdt-video-fileid"/>
        <img class="mdt-videosrc" src=""/><span class="mdt-play"></span>
      </div>
      <div class="mdt-label">Video: <a id="change-video" href="#">Change</a></div>
      <textarea class="mdt-textarea mdt-videoname" name="videoname"></textarea>
    </div>

    <input type="text" value="" readonly="readonly" name="starttime" class="mdt-text mdt-starttime"/>
    <input type="text" value="" readonly="readonly" name="stoptime" class="mdt-text mdt-stoptime"/>

    <select name="startani" class="mdt-select mdt-input mdt-startani">
      <option value="none">none</option>
      <option value="random">random</option>
      <?php foreach (MDCommon::$in_effects as $effect): ?>
        <option value="<?php print $effect; ?>"><?php print $effect; ?></option>
      <?php endforeach; ?>
    </select>
    <select name="stopani" class="mdt-select mdt-input mdt-stopani">
      <option value="none">none</option>
      <option value="keep">Keep in slide</option>
      <option value="random">random</option>
      <?php foreach (MDCommon::$out_effects as $effect): ?>
        <option value="<?php print $effect; ?>"><?php print $effect; ?></option>
      <?php endforeach; ?>
    </select>

    <input type="text" name="opacity" value="100" maxlength="3" class="mdt-text mdt-input mdt-opacity"/>
    <input type="text" value="" maxlength="24" name="mdtclass" class="mdt-text mdt-input mdt-custom-class"/>

    <select name="style" class="mdt-select mdt-input mdt-style">
      <option value="none">None</option>
      <?php foreach ($style_options as $value => $option): ?>
        <option value="<?php print $value; ?>"><?php print $option; ?></option>
      <?php endforeach; ?>
    </select>

    <div class="background-color">
      <input type="hidden" id="background-color" class="mdt-background mdt-input" value="" name="background-color">
    </div>
    <input type="text" value="" maxlength="3" name="background-transparent"
           class="mdt-text mdt-input mdt-background-transparent"/>

    <div class="border-position" id="border-position">
      <a href="#" class="bp-all" title="All borders"><span></span></a>
      <a href="#" class="bp-top" title="Top borders"><span></span></a>
      <a href="#" class="bp-right" title="Right borders"><span></span></a>
      <a href="#" class="bp-bottom" title="Bottom borders"><span></span></a>
      <a href="#" class="bp-left" title="Left borders"><span></span></a>
    </div>
    <input type="text" value="" maxlength="3" name="border-width" class="mdt-text mdt-input mdt-border-width"/>
    <select name="border-style" class="mdt-select mdt-input mdt-border-style">
      <option value="solid">solid</option>
      <option value="dashed">dashed</option>
      <option value="dotted">dotted</option>
      <option value="double">double</option>
    </select>

    <a class="mdt-button mdt-border-color" title="Border color"></a>
    <div class="border-color"><input type="hidden" id="border-color" class="mdt-border mdt-input" value="" name="border-color"></div>

    <input type="text" value="" maxlength="2" name="border-top-left-radius"
           class="mdt-text mdt-border-radius mdt-br-topleft"/>
    <input type="text" value="" maxlength="2" name="border-top-right-radius"
           class="mdt-text mdt-border-radius mdt-br-topright"/>
    <input type="text" value="" maxlength="2" name="border-bottom-right-radius"
           class="mdt-text mdt-border-radius mdt-br-bottomright"/>
    <input type="text" value="" maxlength="2" name="border-bottom-left-radius"
           class="mdt-text mdt-border-radius mdt-br-bottomleft"/>
    <!-- CSS:
    -webkit-border-radius: 1px 2px 3px 4px;
    border-radius: 1px 2px 3px 4px;
    -->

    <input type="text" value="" maxlength="2" name="padding-top" class="mdt-text mdt-padding mdt-p-top"/>
    <input type="text" value="" maxlength="2" name="padding-right" class="mdt-text mdt-padding mdt-p-right"/>
    <input type="text" value="" maxlength="2" name="padding-bottom" class="mdt-text mdt-padding mdt-p-bottom"/>
    <input type="text" value="" maxlength="2" name="padding-left" class="mdt-text mdt-padding mdt-p-left"/>

    <div id="mdt-linkexpand" class="mdt-linkexpand">
      <a href="#" class="mdt-link-close"></a>
      <input type="text" value="" name="" class="mdt-text mdt-link-value"/>
      <input type="text" value="" name="" class="mdt-text mdt-link-title"/>
      <select class="mdt-select mdt-link-target">
        <option value="">None</option>
        <option value="_blank">_blank</option>
        <option value="_self">_self</option>
        <option value="_parent">_parent</option>
        <option value="_top">_top</option>
      </select>
      <div class="link-color-wrap"><input type="hidden" id="link-color" class="link-color" value="" name="link-color"></div>
      <div class="link-background-wrap"><input type="hidden" id="link-background" class="link-background" value="" name="link-background"></div>
      <input type="text" value="" maxlength="3" name="link-background-transparent"  class="mdt-text link-background-transparent"/>
      <div class="link-border-wrap"><input type="hidden" id="link-border" class="link-border" value="" name="link-border"></div>


      <a href="#" class="mdt-link-remove">Remove</a>
      <a href="#" class="mdt-link-save">Save</a>
    </div>
  </div>
  <!-- /#md-toolbar -->
  <div class="md-timeline">
    <div class="mdtl-layers">
      <div class="mdtl-head">
        <div class="mdtl-head-left">Timeline</div>
        <div class="mdtl-head-right">
          <div class="mdtl-ruler">
            <?php for ($i = 0; $i < 20; $i++): ?>
              <div class="number"><span><?php print $i; ?></span></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            <?php endfor; ?>
          </div>
          <div id="slideshow-time">
            <div></div>
          </div>
        </div>
      </div>
      <div class="timeline-wrap">
        <div id="timeline-items">

        </div>
      </div>
    </div>
  </div>
  <!-- /.md-timeline -->
</div>
<div id="dlg-slide-setting" style="display:none;">
  <div class="settings">
    <a class="panel-settings-link" href="#">[Settings]</a> &nbsp;
    <!--      <a class="custom-thumbnail-setting" href="#">[Choose thumbnail image]</a> &nbsp;-->
    <a class="panel-clone" href="#">[Clone slide]</a>
    <input class="panelsettings" type="hidden" value='{"slide_id": -1, "background_image": -1,"background_color": "" ,"disabled": 0, "custom_thumbnail": -1, "transitions": []}' autocomplete="off">
  </div>
  <div class="md-slidewrap<?php print $class_fullwidth; ?>" style="height: <?php print $slider->settings['height']; ?>px; <?php if (!$slider->settings['full_width']): print 'width:' . $slider->settings['width'] . 'px'; endif; ?>">
    <div class="md-slide-image" style="height: <?php print $slider->settings['height']; ?>px; <?php if (!$slider->settings['full_width']): print 'width:' . $slider->settings['width'] . 'px'; endif; ?>"></div>
    <div class="md-objects" style="width: <?php print $slider->settings['width']; ?>px; height: <?php print $slider->settings['height']; ?>px;">
    </div>
  </div>
</div>
<div id="dlg-video" style="display:none;" title="Item setting"></div>
<input type="hidden" value="<?php print $slider->settings['delay']; ?>" name="default-timelinewidth">
