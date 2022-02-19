Ubercart Addresses example module
------------------------
This example module demonstrates how you can add extra address
fields through the Ubercart Addresses field handler API.

It adds three example text fields:
- A title field (for use, see http://en.wikipedia.org/wiki/Title)
- Surname prefix (such as "van", "von" or "de"
    see also: http://en.wikipedia.org/wiki/Tussenvoegsel)
- House number

The schema's of uc_addresses and uc_orders are altered to save
the extra field values.

For a quick start (and if you only need extra text fields), you
can alter the field definitions. In this example module, fields
are defined for the Ubercart Addresses field handler API and for
the schema API.
The field definitions for the field handler API can be found in
the function uc_addresses_example_uc_addresses_fields() from
uc_addresses_example.module.
The field definitions for the schema API can be found in the
function _uc_addresses_example_schema_fields() from
uc_addresses_example.install.

If you like to learn more, follow the instructions below.


Registering address fields
---------------------
You can register address fields for Ubercart Addresses by
implementing hook_uc_addresses_fields() as is done in
uc_addresses_example.module. Each field also needs a handler.
You can use existing handlers or define a new handler.
Defining a new handler is done by implementing
hook_uc_addresses_field_handlers().

This module defines a field handler for simple text fields.
You can find this handler in handlers/uc_addresses.handlers.inc.

For more information about fields and field handlers, check
the online documentation:
http://drupal.org/node/1340694


Saving field values
---------------------
When you register an address field, this doesn't mean
Ubercart Addresses automatically cares for saving it's value.
For this you need to either:
- implement a couple of hooks (see further)
- altering the schema's of uc_addresses and uc_orders.

This example module only demonstrates how to alter the schema's.


How to alter the schema's
---------------------
1. Implement hook_schema_alter() to alter the schema definitions.
2. Implement hook_install() to add the database fields.
3. Implement hook_uninstall() to remove the database fields.

This example module demonstrates a possible way to do that in
the file uc_addresses_example.install.


How to save field values using hooks
---------------------
When you use hooks to save field values, you care yourself
where its value gets stored and how it gets stored. Your
module may have created a new database table or you have
thought of another way to save the values.
1. Implement hook_uc_addresses_address_load() for loading your
   field value. This hook is invoked every time an address is
   loaded.
2. Implement hook_uc_addresses_address_insert() and
   hook_uc_addresses_address_update() for saving your field value.
   The first hook is invoked when a new address is saved, the
   second hook is invoked when an existing address is saved.
3. Implement hook_uc_addresses_address_delete() to remove your
   field value.

More information about the hooks can be found in uc_addresses.api.php.


Notes
---------------------
It's possible this example module will be extended in the future
with more examples.

More documentation of the Ubercart Addresses module can be found
online:
http://drupal.org/node/1340672
