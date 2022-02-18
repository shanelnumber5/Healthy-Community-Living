uc_addresses module
------------------------
by MegaChriz and Tony Freixas

The uc_addresses module adds an address book to the user's profile.
In the address book users can manage their addresses: add new
addresses and edit or delete existing addresses. One address must
be designated as the default billing address and one must be
designated as the default shipping address. This may be the same
address. The default addresses cannot be deleted (but they can be
edited).

The module changes the way Ubercart handles addresses. By default,
Ubercart looks at a customer's previous orders and finds all unique
addresses. It displays these in a select box, using the Street Address
as a label. A customer who has registered but never ordered will have
no contact information other than an e-mail address.

With this module installed, user addresses are stored in a new
database table, one that the user can manipulate as part of the user
profile.


Table of contents
---------------------
- Module overview
- Ubercart Addresses address formats
- Dependencies
- Installation
- Permissions
- Extending the module
---------------------


Module overview:
---------------------
When users create an account, you can request that they be asked to
provide contact information. This initial entry can be edited later.

When users visit their "My account" page, a new tab will be present:
"Address book". They will be able to:

  * Add a new address
  * Edit an existing address
  * Mark one address as their default shipping address
  * Mark one address as their default billing address
  * Delete any address except the "default" addresses

Each address can be given a short "nickname".

When placing an order, users will be able to:

  * Select an address from the set that appears in their profile
  * Modify the address for the order and save it as another address in
    their profile.

The delivery address can be prefilled with the user's default shipping
address and the billing address can be prefilled with the user's
default billing address. This is the default behaviour.

  Warning: If pre-filling the delivery address and you charge for
  shipping, be sure to require that the user select a shipping method
  before they can place an order. Otherwise, the order may go through
  without shipping being charged. You may also want to use the
  Auto-calculate Shipping module to make things easier for your users.

Instead of selecting an address by street name, the selector will
display the address's nickname or else the entire address: Name,
street1, street2, city, etc.

To accommodate sites that have existing users, the code will work even
when users have no addresses.

Note: when a user is deleted, all their addresses are also deleted.


Ubercart Addresses address formats
---------------------
Ubercart Addresses comes with it's own address formats that are build
by using tokens, rather than the predefined set of variables Ubercart
uses. This way it's possible to add any extra address values to the
address format. Only addresses used by Ubercart Addresses are
formatted using Ubercart Addresses' address formats.

The following addresses are formatted by Ubercart Addresses:

  * Address book addresses
  * Delivery and billing addresses on the checkout page, the order
    review page and the order pages.

You can configure the address formats for Ubercart Addresses at the
Ubercart Addresses address formats page:
admin/store/settings/countries/uc_addresses_formats


Dependencies
------------
This module requires uc_store, ctools and token.


Installation
------------
  * Copy the uc_addresses module's directory to your modules directory
    and activate it. I have mine in /sites/all/modules/uc_addresses.
  * Activate the module, set up permissions and go to your account
    page to begin using the new Address book tab.


Permissions
-----------
- view own default addresses:
    Roles with this permission can view their own default addresses in 
    their address book.
- view own addresses:
    Roles with this permission can view all own addresses in their address
    book, *including* the default addresses.
- view all default addresses
    Roles with this permission can view all default addresses of all
    users, *including* their own default addresses.
- view all addresses
    Roles with this permission can view all addresses of all users,
    including addresses of their own.
- add/edit own addresses
    Roles with this permission can add addresses to their own address
    book and edit own addresses. They are also able to view their own
    addresses.
- add/edit all addresses
    Roles with this permission can add addresses to address books of
    any user and edit addresses of all users. They are also be able
    to view all addresses.
- delete own addresses
    Roles with this permission can delete own addresses that are not
    marked as the default shipping or the default billing address.
    (Ubercart Addresses doesn't allow anyone to delete default addresses,
    including the superuser. This is by design.)
- delete all addresses
    Roles with this permission can delete all addresses of all users,
    except addresses that are marked as default shipping or default
    billing.


Extending the module
-----------
Ubercart Addresses provides two API's and a set of hooks to extend the
module:
- The address book API
  With this API you can control addresses used by Ubercart Addresses.
- The field handler API
  With this API you can add extra address fields.
- Hooks
  Hooks allow you to respond to events in the Ubercart Addresses module:
  - Your module can respond when an address is loaded, saved or deleted.
  - Your module can get extra control about address access if the
    existing permissions don't suite your needs.
  - Your module can deliver a list of selectable addresses at checkout
    that don't have to exists in the user's address book.
  - Your module can alter an address field listing before it's displayed.

There is an example module included in the uc_addresses_example sub-
directory which demonstrates how to interact with the field handler API.
Documentation about the hooks can be found in uc_addresses.api.php.

More documentation can be found online:
http://drupal.org/node/1340672
