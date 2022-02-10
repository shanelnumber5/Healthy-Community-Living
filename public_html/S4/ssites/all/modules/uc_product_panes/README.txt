-------------------------------
Ubercart Product Checkout Panes
-------------------------------

Developed and maintained by Martin B. - martin@webscio.net
Supported by JoyGroup - http://www.joygroup.nl


Introduction
------------
This module allows you to select which panes should be shown at checkout depending on what products are in a user's
cart. It utilizes the powerful Product Features interface to achieve this, meaning that you can now configure
which panes to show during checkout based on product classes and also individually on a per product basis.

When defining checkout panes as features for an individual product, it is now even possible to do this for every
adjustment of a product, if you are using attributes and have alternative SKU's configured for some attribute values.


Requirements
------------
 * Ubercart Product Checkout Panes requires any Ubercart 3.x version from after 15-03-2011.
 * If you are using an older version of Ubercart, you will need the patch from http://drupal.org/node/964232.


Installation
------------
 * Copy the module's directory to your modules directory and activate the module.


Usage
-----
 * To set a class-specific configuration of checkout panes, go to admin/store/products/classes, select the class
 you're interested in, and configure the desired options in the "Default Product Panes" fieldset at the bottom.

 * To set a product-specific configuration, go to your product node, click on "Edit" and then "Features". In the
 opened view, select the "Product Panes" feature and add it to your product, setting the desired checkout panes
 in the feature settings.

 * To set attribute-specific configurations, follow the steps described in the preceding paragraph, only when
 adding the feature to your product, make sure to select the SKU corresponding to the targeted attribute value.


Pane Selection
--------------
 * If there is only one product in your cart and either the product's class or the product node itself has the
 Product Checkout Panes feature enabled, the default settings will be ignored and the panes specified for that
 product (class) will be used instead.

 * If attributes are used and SKU adjustments have been defined, only the feature corresponding to the most specific
 SKU will be used. This means that the panes from the feature defined for "-Any-" will NOT be added to the checkout
 if there is also a feature defined for the adjusted product SKU.

 * If there are multiple products in your cart, the panes displayed will be the union of every product's (class)
 pane settings. If one of the products does not have custom checkout panes specified for either the product class
 or the product node, the default settings will apply to that product.
