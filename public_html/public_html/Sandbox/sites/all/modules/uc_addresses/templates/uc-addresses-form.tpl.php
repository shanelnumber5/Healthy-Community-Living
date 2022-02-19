<?php

/**
 * @file
 * Displays the address edit form.
 *
 * Available variables:
 * - $form: The complete address edit form array, not yet rendered.
 * - $req: A span for required fields:
 *   <span class="form-required">*</span>
 *
 * @see template_preprocess_uc_addresses_form()
 *
 * @ingroup themeable
 */
?>
<div class="address-pane-table">
  <table>
    <?php foreach (element_children($form) as $fieldname): ?>
      <?php
        // Skip fields with:
        // - #access == FALSE
        // - #type == value
        // - #type == hidden for fields without a label.
        if (
          (isset($form[$fieldname]['#access']) && $form[$fieldname]['#access'] == FALSE)
          || ($form[$fieldname]['#type'] == 'value')
          || ($form[$fieldname]['#type'] == 'hidden' && empty($form[$fieldname]['#title']))
        ) {
          continue;
        }
      ?>
      <tr class="field-<?php print $fieldname; ?>">
        <?php if (!empty($form[$fieldname]['#title'])): ?>
          <td class="field-label">
            <?php if ($form[$fieldname]['#required']): ?>
              <?php print $req; ?>
            <?php endif; ?>
            <?php print $form[$fieldname]['#title']; ?>:
          </td>
        <?php unset($form[$fieldname]['#title']); ?>
        <?php else: ?>
          <td class="field-label"></td>
        <?php endif; ?>
        <td class="field-field"><?php print drupal_render($form[$fieldname]); ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>
<div class="address-form-bottom"><?php print drupal_render_children($form); ?></div>