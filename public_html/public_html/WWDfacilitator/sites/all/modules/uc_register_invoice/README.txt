Ubercart Register Invoice Templates

This module finally gives a good answer to the common question from
Ubercart administrators: "How do I customize the e-mail invoice sent
to customers after they make a purchase?"

Ubercart invoices are formatted by template files. Getting Ubercart
to recognize and use your custom template file is a technical,
error-prone process. This module does the registration work for
you automatically.

To customize the e-mail, install this module. Make a copy of the template file
sites/all/modules/ubercart/uc_order/templates/uc-order--customer.tpl.php.
Put the copy in your default theme directory (or any of its subdirectories)
with a new name. Modify the new file to your liking. This module automatically
registers your new template. Then, just select the new template in the
"E-mail customer checkout notification" action rule.

More detailed directions are in the module's built-in help.
