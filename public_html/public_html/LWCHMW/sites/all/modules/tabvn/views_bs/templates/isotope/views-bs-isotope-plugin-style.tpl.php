<div id="views-bs-isotope-wrapper-<?php print $element_id ?>" class="<?php print $classes ?> <?php print $custom_class; ?>">
  <div class="row">
    <ul class="views-bs-content curl" id="views-bs-isotope-<?php print $element_id ?>">
      <?php foreach ($rows as $key => $row): ?>
        <?php
        $term_css_name = !empty($custom_terms_class[$key]) ? $custom_terms_class[$key] : '';
        ?>
        <li class="isotope-element item-<?php print $key; ?> col <?php print $columns_css ?><?php print $term_css_name; ?>">
         <?php
        $animate = 'fadeInUp';
        if ($key % 2 == 0) {
          $animate = 'fadeInDown';
        }
        ?>
          <div class="isotope-item-wrapper wow <?php print $animate; ?>">
            <?php 
            $style_class= ' class="'.$views_bs_item_style.'"';
            ?>
            <div<?php print $style_class;?>>
              <?php print $row ?>
            </div>
          </div>
        </li>
      <?php endforeach ?>
    </ul>
  </div>
</div>
