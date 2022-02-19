<?php
/**
 * @file: slide_setting_form.tpl.php
 * User: Duy
 * Date: 1/24/13
 * Time: 3:32 PM
 */
?>
<div class="dlg-inner">
    <form id="form-slider-panelsetting">
        <fieldset class="ui-helper-reset">
            <div class="form-item">
                <input type="text" name="txtvideoid" id="txtvideoid" class="form-text" autocomplete="false" value="" />
                <button id="btn-search" type="button">Search</button>
            </div>
            <div class="form-item">
                <label for="videoid">Video Id</label>
                <input type="text" name="videoid" id="videoid" class="form-text" autocomplete="false" value="" />
            </div>
            <div class="form-item">
                <label for="videoname">Video Name</label>
                <input type="text" name="videoname" id="videoname" class="form-text" autocomplete="false" value="" />
            </div>
            <div class="form-item">
                <img src="" id="videothumb" width="100px" height="100px" />
              <?php if ($show_change):?>
                <a class="panel-change-videothumb" href="#">[Change video thumb]</a>
              <?php endif;?>
            </div>
        </fieldset>
    </form>
</div>
