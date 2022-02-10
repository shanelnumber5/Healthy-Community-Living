/**
 * @file
 * Javascript functions for Ubercart Addresses.
 */

/**
 * Copy address values from one pane to another for order form.
 *
 * @param string source
 *   The pane to get the address data from.
 * @param string target
 *   The pane to copy address data to.
 *
 * @return void
 */
function uc_addresses_copy_address_order(source, target) {
  var address_pane_source = '#' + source + '-pane';
  var address_pane_target = '#' + target + '-pane';

  // On the order form, the pane type is repeated 1 time.
  var source_source = source;
  var target_target = target;
  zone_field_source = '#edit-' + source_source + '-zone';
  zone_field_target = '#edit-' + target_target + '-zone';

  // Copy over the zone options manually.
  if (jQuery(zone_field_target).html() != jQuery(zone_field_source).html()) {
    jQuery(zone_field_target).empty().append(jQuery(zone_field_source).children().clone());
    jQuery(zone_field_target).attr('disabled', jQuery(zone_field_source).attr('disabled'));
  }

  // For each input field.
  jQuery(address_pane_target + ' input, select, textarea', ':visible', document.body).each(
    function(i) {
      // Copy the values from the source pane to the target pane.
      var source_field = this.id;
      source_field = source_field.replace(target_target, source_source);
      var target_field = this.id;
      if (target_field != source_field) {
        if (this.type == 'checkbox') {
          jQuery('#' + target_field).attr('checked', jQuery('#' + source_field).attr('checked'));
        }
        else {
          jQuery('#' + target_field).val(jQuery('#' + source_field).val());
        }
      }
    }
  );
}

/**
 * Apply the selected address to the appropriate fields in the cart form.
 */
function uc_addresses_apply_address(type, address_str) {
  if (address_str == '0') {
    return;
  }
   
  var address_pane = '#' + type + '-pane';
  var order_field_id_prefix = 'edit-' + type + '-';

  eval('var address = ' + address_str + ';');

  jQuery(address_pane + ' input, select, textarea', ':visible', document.body).each(
    function (i) {
      fieldname = this.id;
      fieldname = fieldname.replace(order_field_id_prefix, '');
      fieldname = fieldname.replace('-', '_');

      if (fieldname != 'country' && fieldname != 'zone' && address[fieldname] != undefined) {
        jQuery(this).val(address[fieldname]).trigger('change');
      }
    }
  );

  // Special treatment for country and zone fields.
  // Order.
  if (jQuery('#' + order_field_id_prefix + 'country').val() != address['country']) {
    try {
      jQuery('#' + order_field_id_prefix + 'country').val(address['country']).trigger('change');
      //uc_update_zone_select(order_field_id_prefix + 'country', address['zone']);
    }
    catch (err) { }
  }
  jQuery('#' + order_field_id_prefix + 'zone').val(address['zone']);
}
