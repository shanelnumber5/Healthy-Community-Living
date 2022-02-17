=== FG Drupal to WordPress Premium ===
Contributors: Kerfred
Plugin Uri: https://www.fredericgilles.net/fg-drupal-to-wordpress/
Tags: drupal, wordpress, importer, migration, migrator, converter, import, cck, internationalization, ubercart
Requires at least: 4.5
Tested up to: 5.9
Stable tag: 3.18.0
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=fred%2egilles%40free%2efr&lc=FR&item_name=fg-drupal-to-wp&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted

A plugin to migrate a Drupal site to WordPress

== Description ==

This plugin migrates articles, stories, pages, categories, tags, images, users and comments from Drupal to WordPress.

It has been tested with **Drupal 4, 5, 6, 7, 8 & 9** and **WordPress 5.9**. It is compatible with multisite installations.

Major features include:

* migrates the Drupal articles
* migrates the Drupal 6 stories
* migrates the Drupal basic pages
* migrates the Drupal categories
* migrates the Drupal tags
* migrates the Drupal images
* migrates the comments
* migrates the authors
* migrates the administrators
* migrates the users
* migrates the custom post types
* migrates the custom taxonomies
* migrates the custom fields
* migrates the custom users fields
* migrates the users pictures
* migrates the navigation menus
* migrates the pages hierarchy
* migrates the blocks as inactive widgets
* authenticates the users using their Drupal passwords
* uploads all the posts media in WP uploads directories
* uploads external media (as an option)
* modifies the post content to keep the media links
* resizes images according to the sizes defined in WP
* defines the featured image to be the first post image (as an option)
* keeps the alt image attribute
* modifies the internal links
* compatible with the MySQL, PostgreSQL and SQLite Drupal database drivers
* SEO: redirects the Drupal URLs to the corresponding WordPress URLs
* ability to not import some data
* ability to import only specific node types
* imports and replaces the Image Assist shortcodes
* imports the images managed by the Image Attach Drupal module
* imports the nodes relationships
* imports the Drupal 8 Media entities
* imports the Drupal Media
* imports the Video Embed fields

No need to subscribe to an external web site.

= Add-ons =

The Premium version allows the use of add-ons that enhance functionality:

* CCK Custom Content Kit
* Meta tags
* Location custom fields
* Ubercart store
* Drupal Commerce store
* Name custom fields
* Addressfield custom fields
* Internationalization
* NodeBlock fields
* EntityReference relationships
* Media Provider (S3, SoundCloud, YouTube media)
* Forum
* Field collections
* Paragraphs
* Domain Access
* Countries
* Profile2 user fields
* Entity Embed

These modules can be purchased on: [https://www.fredericgilles.net/fg-drupal-to-wordpress/add-ons/](https://www.fredericgilles.net/fg-drupal-to-wordpress/add-ons/)

== Installation ==

1.  Install the plugin in the Admin => Plugins menu => Add New => Upload => Select the zip file => Install Now
2.  Activate the plugin in the Admin => Plugins menu
3.  Run the importer in Tools > Import > Drupal
4.  Configure the plugin settings. You can find the Drupal database parameters in the Drupal file sites/default/settings.php<br />

== WP CLI Usage ==

wp import-drupal empty              Empty the imported data | empty all : Empty all the WordPress data
wp import-drupal import             Import the data
wp import-drupal modify_links       Modify the internal links
wp import-drupal test_database      Test the database connection
wp import-drupal test_media         Test the media connection

== Screenshots ==

1. Parameters screen

== Translations ==
* English (default)
* French (fr_FR)
* other can be translated

== Frequently Asked Questions ==

The FAQ is available on https://www.fredericgilles.net/support/kb/index.php

You can let a comment or report a bug on the Support Center: https://www.fredericgilles.net/support/

== Changelog ==

= 3.18.0 =
* New: Don't delete the theme's customizations (WP 5.9) when removing all WordPress content
* Tested with WordPress 5.9

= 3.17.1 =
* Fixed: YouTube videos were not imported

= 3.17.0 =
* New: Add the hooks "fgd2wp_get_featured_image" and "fgd2wp_get_custom_field_file_date"
* Tested with WordPress 5.8.3

= 3.16.0 =
* New: Add the hook "fgd2wp_post_register_user_fields"

= 3.15.0 =
* New: Allow the add-ons to intercept the creation of the field
* New: Support the "time" field type for ACF
* New: Add the hook "fgd2wp_map_acf_field_type"
* New: Add the hook "fgd2wp_convert_custom_field_to_meta_values"

= 3.14.0 =
* New: Support ACF "Group" fields
* New: Support the "number" custom field
* New: Add the hook "fgd2wp_user_parent_group_id"
* New: Add the hook "fgd2wp_get_user_meta_key"
* Tested with WordPress 5.8.2

= 3.13.1 =
* Fixed: Only one checkbox value was imported
* Fixed: user_nicename not sanitized if "Allow Unicode characters in the usernames" is selected

= 3.13.0 =
* New: Import the User reference fields to ACF
* Fixed: Warning: Invalid argument supplied for foreach()
* Fixed: Notice: Undefined index: options
* Fixed: Missing or wrong post object relationships with ACF

= 3.12.1 =
* Fixed: "[ERROR] Not a media" for HTML links

= 3.12.0 =
* New: Add the hook "fgd2wp_post_import_nodes_relations"
* New: Import the post object fields values with a prefix in order to replace them when importing the node relationships

= 3.11.0 =
* New: Add the author in the custom posts

= 3.10.2 =
* Fixed: Fatal error: Uncaught ArgumentCountError: Too few arguments to function FG_Drupal_to_WordPress_CPT_ACF::create_acf5_field()

= 3.10.1 =
* Fixed: List fields were imported as serialized
* Fixed: Address fields were not imported to ACF

= 3.10.0 =
* New: Import the "post" post types
* Fixed: Notice: Undefined index: topic-primary_topic
* Fixed: The taxonomy "post_type" prevents the posts to display with CPT UI

= 3.9.0 =
* New: Import all the text blocks
* Tested with WordPress 5.8.1

= 3.8.1 =
* Fixed: Images surrounded with a link were not imported in the content

= 3.8.0 =
* New: Import the image captions of the custom image fields
* Fixed: Post object fields entered manually were not displayed on the front-end

= 3.7.0 =
* New: Import the image captions

= 3.6.0 =
* New: Add a spinner during the AJAX actions

= 3.5.3 =
* Fixed: Notice: Undefined index: referenceable_types
* Fixed: [ERROR] Error:SQLSTATE[42S22]: Column not found: 1054 Unknown column 'field_media_image_alt' in 'field list'

= 3.5.2 =
* Fixed: Entity reference fields were not imported in ACF

= 3.5.1 =
* Fixed: Fatal error when updating plugin

= 3.5.0 =
* New: Support the custom fields color_field_type, textstyled and video_embed_field

= 3.4.4 =
* Fixed: Link fields not imported on Drupal 8
* Tweak: Prevent ACF from removing the Custom Fields metabox in development mode

= 3.4.3 =
* Fixed: Only one value was imported in the ACF repeater field
* Fixed: Some variables were not escaped before displaying

= 3.4.2 =
* Fixed: Warning: Invalid argument supplied for foreach()

= 3.4.1 =
* Fixed: Fatal error: Uncaught ArgumentCountError: Too few arguments to function FG_Drupal_to_WordPress_CPT_Toolset::set_post_association()
* Fixed: Posts relationships not imported
* Fixed: Wrong plural terms
* Tested with WordPress 5.8

= 3.4.0 =
* Tweak: Refactoring ACF and Toolset classes
* Fixed: ACF Post objects not displayed on the front-end
* Fixed: ACF dates displayed in wrong format on the front-end

= 3.3.0 =
* New: Display the custom post types names in the partial import section

= 3.2.0 =
* New: Display the reference of the Drupal entity when a media can't be downloaded

= 3.1.0 =
* New: Add the hook "fgd2wp_modify_links_in_content"

= 3.0.4 =
* Fixed: Images containing spaces were not replaced in the post content

= 3.0.3 =
* Fixed: WP CLI and cron can't connect to the Drupal database

= 3.0.2 =
* New: Add the hook "fgd2wp_set_plugin_options"
* Fixed: The database information was not updated during the import
* Tweak: Refactoring

= 3.0.1 =
* Fixed: Some post relationships were not imported

= 3.0.0 =
* New: Compatible with ACF and CPT UI
* New: Add an option to choose between Toolset or ACF + CPT UI
* New: Add the "map_taxonomy" function
* New: Add the "fgd2wp_pre_dispatch" hook
* Tweak: Rename "convert_node_type" by "map_post_type"
* Fixed: With Toolset, replace the multi-select field (not supported on Toolset) by checkboxes
* Fixed: Wrong order of Toolset custom fields

= 2.40.0 =
* New: Test the media connection through WP CLI
* New: Log a message during internal links modification
* New: Add a progress bar when modifying internal links through WP CLI
* Fixed: During the import by cron or by WP CLI, the admin user could be wrong
* Tested with WordPress 5.7.2

= 2.39.0 =
* New: Check if we need the Geodata add-on
* Tested with WordPress 5.7.1

= 2.38.1 =
* Fixed: Media containing % were not imported
* Fixed: The Metatag add-on warning was not displayed for Drupal 8 databases

= 2.38.0 =
* New: Import the custom fields of type "image_miw"
* New: Import the custom fields of type "video_embed_field_video"
* Tested with WordPress 5.7

= 2.37.0 =
* Tweak: Remove the "wp_insert_post" that consumes a lot of CPU and memory
* New: Ability to import entity relationships that are different from nodes

= 2.36.0 =
* New: Make the plugin more generic to be able to import the data that are not in the "node" table
* Tested with WordPress 5.6.2

= 2.35.1 =
* Fixed: Missing associations with the error: The element xxx has already the maximum allowed amount of associations (1) as child in the relationship yyy.

= 2.35.0 =
* New: Import the Drupal 8 media containing multiple values
* New: Add the hook "fgd2wp_get_nodes_types_sql" for Drupal < 8
* Tested with WordPress 5.6.1

= 2.34.3 =
* Fixed: Wrong version of Drupal guessed when taxonomies are not set

= 2.34.2 =
* Fixed: "Warning: unserialize() expects parameter 1 to be string, resource given" with PostgreSQL database

= 2.34.1 =
* Fixed: Media with space not imported

= 2.34.0 =
* New: Import the Drupal 8 taxonomies fields
* Fixed: Custom fields imported in wrong language
* Fixed: Counter doesn't reach 100% with Drupal 8

= 2.33.0 =
* New: Add the hook "fgd2wp_get_urls_sql"
* Fixed: URLs containing multiple numbers were not redirected

= 2.32.2 =
* Fixed: Notice: register_taxonomy was called incorrectly. Taxonomy names must be between 1 and 32 characters in length.

= 2.32.1 =
* Fixed: Tags not associated with custom post types if the user language is not the same as the site language

= 2.32.0 =
* New: Add the hook "fgd2wp_get_comments_sql"

= 2.31.0 =
* New: Get the post slug from the URL alias

= 2.30.0 =
* New: Ability to download the media by http, ftp or file system
* Fixed: Images inserted in the post content with width and height = 0 when the option "Don't generate the thumbnails" is selected

= 2.29.0 =
* New: Add documentation about WP CLI
* Fixed: Plugin and add-ons not displayed in the debug informations on Windows

= 2.28.2 =
* Tested with WordPress 5.6

= 2.28.1 =
* Fixed: Fatal error: Uncaught InvalidArgumentException: The element to connect with doesn't belong to the relationship definition provided.

= 2.28.0 =
* New: Display a progress bar on WP CLI when importing the node relationships

= 2.27.5 =
* Fixed: Notice: Undefined index: entity_type

= 2.27.4 =
* Fixed: [ERROR] Error:SQLSTATE[42S02]: Base table or view not found: 1146 Table 'image' doesn't exist
* Fixed: JQuery Migrate warning: jQuery.fn.load() is deprecated

= 2.27.3 =
* Fixed: Notice: map_meta_cap was called <strong>incorrectly</strong>. The post type wp-types-group is not registered

= 2.27.2 =
* Fixed: Notice: Undefined index: entity_type

= 2.27.1 =
* Fixed: Character " not displayed in the settings
* Fixed: With WP CLI, don't exit the process on error

= 2.27.0 =
* New: Ability to change the default import timeout by adding `define('IMPORT_TIMEOUT', 7200);` in the wp-config.php file
* Fixed: Empty the new Toolset tables "toolset_connected_elements" and "toolset_maps_address_cache" when emptying the WordPress content
* Fixed: Prevent a Toolset bug: the relationship is inverted if the parent type is the same as the child type

= 2.26.2 =
* Fixed: Fatal error: Uncaught Error: Call to undefined function deactivate_plugins() on WP CLI on Windows

= 2.26.1 =
* Fixed: Fatal error: Uncaught InvalidArgumentException: The element to connect with doesn't belong to the relationship definition provided

= 2.26.0 =
* New: Add WP-CLI support
* Fixed: Notice: Undefined index: nodes_to_skip

= 2.25.0 =
* Change: Add a parameter to the hook "fgd2wp_import_node_field"
* Fixed: Remove unused code

= 2.24.0 =
* New: Add the hook "fgd2wp_get_drupal_post_types_from_wp_post_type"
* Fixed: Missing associations between posts due to a breaking change on Toolset Types
* Fixed: Missing associations between posts when Ubercart add-on is active
* Tested with WordPress 5.5.3

= 2.23.0 =
* New: Add the hook "fgd2wp_get_drupal7_user_fields_sql"
* New: Add the hook "fgd2wp_get_user_entity_id"
* New: Check if we need the Profile2 add-on
* Fixed: Warning: max(): Array must contain at least one element
* Fixed: Progress bar at 0% if the site is in https and the WordPress general settings are in http

= 2.22.3 =
* Fixed: Notice: Trying to get property 'taxonomy' of non-object in /wp-content/plugins/wordpress-seo/src/builders/indexable-hierarchy-builder.php
* Fixed: Notice: Trying to get property 'parent' of non-object in /wp-content/plugins/wordpress-seo/src/builders/indexable-hierarchy-builder.php
* Tested with WordPress 5.5.1

= 2.22.2 =
* Fixed: Custom fields were wrongly modified after modifying internal links
* Tweak: Remove unused function parameter

= 2.22.1 =
* Fixed: Backward compatibility with older versions of WordPress

= 2.22.0 =
* Compatible with WordPress 5.5
* Fixed: Toolset associations duplicated each time we run the import
* Fixed: Column not found: 1054 Unknown column 'f.nid' in 'where clause' (Drupal 5)
* Fixed: Timezone was not the same between the start and the end time in the logs

= 2.21.0 =
* New: Add an option to not generate the images thumbnails
* New: Make the max_allowed_packet human readable
* Change: Set the media timeout to 20 seconds
* Fixed: Non hierarchical taxonomy was not set to "flat"

= 2.20.1 =
* Fixed: Body content imported in wrong language (regression from 2.13.3)

= 2.20.0 =
* New: Ability to change the file paths (Drupal 8+)

= 2.19.3 =
* Fixed: Files containing "&" were not imported

= 2.19.2 =
* Fixed: "<br />0" appended in the custom fields values (regression from 2.19.1)

= 2.19.1 =
* Fixed: Posts associations were duplicated

= 2.19.0 =
* New: Tested with Drupal 9
* Fixed: Georgian post relationships not imported properly

= 2.18.0 =
* New: Import the Drupal blocks as inactive widgets

= 2.17.0 =
* New: Check if we need the Countries add-on
* Fixed: The "options_select" fields were imported as checkboxes and not as dropdowns

= 2.16.0 =
* New: Add the hook "fgd2wp_pre_import_custom_field_values"
* New: Add the attribute "do_not_register" for the custom fields that need to be read but not registered as custom fields (reference fields, geodata fields)
* Tweak: Refactoring

= 2.15.0 =
* New: Display the PHP errors in the logs
* New: Convert the fields of type text_textarea_maxlength_js to wysiwyg
* Fixed: [ERROR] Error:SQLSTATE[42S02]: Base table or view not found: 1146 Table 'stylehome.drupal_field_data_body' doesn't exist

= 2.14.2 =
* Fixed: Some URLs don't redirect

= 2.14.1 =
* Fixed: User first name was equal to last name
* Tested with WordPress 5.4.2

= 2.14.0 =
* New: Import the URL aliases of the Drupal Pathauto module

= 2.13.3 =
* Fixed: Body and excerpt not imported if the body language is not equal to the node language (regression from 2.13.1)

= 2.13.2 =
* Fixed: Comments not imported
* Tested with WordPress 5.4.1

= 2.13.1 =
* Fixed: Content imported in wrong language

= 2.13.0 =
* New: Import the Drupal Media
* Fixed: Notice: Array to string conversion
* Tweak: Refactoring of unserialized data

= 2.12.1 =
* Fixed: Text and CSV attachments were not imported
* Fixed: Only one attachment per post was imported from Drupal 6

= 2.12.0 =
* Fixed: Logs were not displayed
* Fixed: Warning: Invalid argument supplied for foreach()
* Tested with WordPress 5.4

= 2.11.0 =
* New: Add allow_url_fopen in the Debug Info
* Fixed: Notice:  Undefined index: REQUEST_SCHEME

= 2.10.1 =
* Fixed: In multisite, when deleting the imported data, it deletes all the users from all sites

= 2.10.0 =
* New: Add the hook "fgd2wpp_get_users_sql"
* New: Import the field "name" as user first name

= 2.9.0 =
* New: Add the hook "fgd2wp_post_import_post"

= 2.8.0 =
* New: Modify internal links in custom fields
* Tweak: Refactor the modify links function

= 2.7.2 =
* Fixed: Logs were not displayed due to mod_security

= 2.7.1 =
* Fixed: Warning: Invalid argument supplied for foreach()

= 2.7.0 =
* New: Replace the video shortcodes
* New: Check if the Drupal database contains Vimeo media

= 2.6.0 =
* New: Import the Drupal custom roles
* New: Import multiple roles by user
* Fixed: Notice: Undefined index: hierarchy
* Fixed: Notice: date_default_timezone_set(): Timezone ID '' is invalid

= 2.5.1 =
* Fixed: Some internal links containing anchors were badly modified

= 2.5.0 =
* New: Add some hooks useful for the Forum add-on

= 2.4.0 =
* New: Import the Drupal 6 "content_taxonomy" fields (with the CCK add-on)
* Fixed: Some post relationships not imported from Drupal 6

= 2.3.0 =
* New: Import the Drupal 5 attachments
* New: Import HTML media
* Tested with WordPress 5.3.2

= 2.2.0 =
* Fixed: Media not imported from Drupal 5
* Fixed: Nodes with an excerpt length longer than 65K were not imported
* Tested with WordPress 5.3.1

= 2.1.1 =
* Fixed: Forum topics not redirected

= 2.1.0 =
* New: Delete the Yoast SEO data when emptying all the WordPress content
* Tested with WordPress 5.2.4

= 2.0.0 =
* New: Add an help tab
* New: Add a debug info tab

= 1.92.0 =
* New: Compatible with Drupal 4
* Fixed: Logs were not displayed if the URL is wrong in the WordPress general settings
* Fixed: Comments number not updated

= 1.91.0 =
* New: Download the media even if they are redirected
* New: Check if the Drupal Commerce module is used

= 1.90.2 =
* Fixed: [ERROR] Error:SQLSTATE[42S02]: Base table or view not found: 1146 Table 'profile_fields' doesn't exist
* Fixed: [ERROR] Error:SQLSTATE[42S02]: Base table or view not found: 1146 Table 'profile_values' doesn't exist

= 1.90.1 =
* Fixed: Wrong first name and last name when the user name is just the email

= 1.90.0 =
* New: Ability to run the import by cron
* New: Add some hooks
* Tested with WordPress 5.2.3

= 1.89.0 =
* New: Add "Many to many" relationships
* Fixed: [ERROR] Error:SQLSTATE[42S02]: Base table or view not found

= 1.88.3 =
* Fixed: [ERROR] Error:SQLSTATE[42000]: Syntax error or access violation

= 1.88.2 =
* Fixed: The "Uncategorized" category was assigned to custom post types containing categories

= 1.88.1 =
* Fixed: WordPress database error Illegal mix of collations

= 1.88.0 =
* New: Import the "swfupload" custom fields

= 1.87.0 =
* Fixed: The progress bar exceeds 100% with Drupal 8 multilang sites
* Tested with WordPress 5.2.2

= 1.86.2 =
* Fixed: Regression since 1.86.1: the checkbox values were not imported

= 1.86.1 =
* Fixed: Warning: stripslashes() expects parameter 1 to be string, array given

= 1.86.0 =
* New: Import the media shortcodes embed into the custom fields
* Tested with WordPress 5.2.1

= 1.85.0 =
* New: Add the hook "fgd2wp_get_field_columns"

= 1.84.2 =
* Fixed: Images duplicated in the media library

= 1.84.1 =
* Fixed: Regression bug from 1.82.0: Wrong page hierarchy triggers "Page not found" on the front-end

= 1.84.0 =
* New: Import the date_select fields

= 1.83.0 =
* New: Import the image_image fields
* Tested with WordPress 5.1.1

= 1.82.0 =
* New: Keep the pages hierarchy

= 1.81.2 =
* Fixed: Custom fields were registered again when the import was resumed
* Fixed: Relationships were created again when the import was resumed
* Fixed: Collection fields values lost when the import was resumed

= 1.81.1 =
* Fixed: Field groups were not deleted when deleting the imported data
* Tested with WordPress 5.1

= 1.81.0 =
* Fixed: Notice: Undefined variable: image_fields
* Fixed: Paragraphs fields with same type were imported as duplicates

= 1.80.0 =
* New: Allow the Domain Access add-on

= 1.79.0 =
* New: Import the YouTube custom fields

= 1.78.0 =
* New: Import the boolean fields as checkbox

= 1.77.2 =
* Fixed: Notice: Undefined index: module

= 1.77.1 =
* Fixed: Notice: Undefined index: vid
* Fixed: Notice: Undefined index: hierarchy
* Fixed: The Drupal 8 fields of type text_with_summary were not imported

= 1.77.0 =
* New: Import the Drupal 6 user fields
* Fixed: Import the first name, last name and web site as regular user fields and not as custom fields

= 1.76.2 =
* Fixed: [ERROR] Error:SQLSTATE[42S22]: Column not found: 1054 Unknown column 'f.field_header_image_description' in 'field list'
* Fixed: Fatal error: Uncaught TypeError: Argument 1 passed to Toolset_Association_Factory::create() must be an instance of Toolset_Relationship_Definition, null given
* Fixed: Some relationships were not imported

= 1.76.1 =
* Fixed: Don't import the user fields if the users import is skipped
* Fixed: Images of type "media_generic" were not imported
* Fixed: "[ERROR] Error:SQLSTATE[42S02]: Base table or view not found: 1146 Table 'media_field_data' doesn't exist" on Drupal 7
* Tested with WordPress 5.0.3

= 1.76.0 =
* New: Import the editor and author user roles
* New: Allow Unicode characters in usernames

= 1.75.0 =
* New: Check if the Paragraphs Drupal module is used

= 1.74.1 =
* Fixed: Some media were not imported

= 1.74.0 =
* New: Devanagari (Hindi) language compatibility
* Fixed: Some NGINX servers were blocking the images downloads

= 1.73.0 =
* New: Empty the Toolset Post GUIDs table when deleting the data
* Fixed: Notice: Undefined index: taxonomy
* Tested with WordPress 5.0.2

= 1.72.0 =
* New: Import the Drupal 8 link field titles
* Fixed: Date fields whose month or day equals 00 were not correctly imported

= 1.71.0 =
* New: Import the Drupal 6 users first name, last name and web site
* Tested with WordPress 5.0.1

= 1.70.0 =
* New: Import the URLs of the Video Embed fields
* Fixed: Media types other than "image" were not imported

= 1.69.0 =
* New: Compatible with Drupal 8.5 taxonomies hierarchy
* Tested with WordPress 5.0

= 1.68.0 =
* New: Enable the rewrite option for the custom taxonomies

= 1.67.1 =
* Fixed: [ERROR] Error:SQLSTATE[42S22]: Column not found: 1054 Unknown column 'b.body_summary' in 'field list'

= 1.67.0 =
* New: Generate the audio and video meta data (ID3 tag, featured image)
* New: Import the file description
* New: Import the "imagefield_crop_widget" field type
* Fixed: The node types to skip were not saved when clicking on the Save settings button

= 1.66.0 =
* New: Import the navigation menus
* Fixed: [ERROR] Error:SQLSTATE[42000]: Syntax error or access violation: 1064 on Drupal 6

= 1.65.3 =
* Fixed: Users with all unknown characters were not imported. Now they are imported and their login is their email.
* Fixed: No data was imported if the taxonomies tables don't exist on Drupal
* Fixed: Drupal 8 media not imported
* Tweak: Cache some database results to increase import speed

= 1.65.2 =
* Fixed: Comments not imported when the comments body table name was not "comment__comment_body"
* Fixed: Nodes relationships were not imported

= 1.65.1 =
* Fixed: The approved Drupal 6 comments were imported as unapproved on WordPress and vice-versa

= 1.65.0 =
* New: Ability to select / deselect all node types in the partial import box
* New: Import the Drupal 6 Image Attach images
* Change: Don't import the authors if the "Don't import the users" option is selected

= 1.64.1 =
* Fixed: Options imported as radio boxes instead of checkboxes

= 1.64.0 =
* New: Keep the width, height and style of the embedded images

= 1.63.0 =
* New: Bengali language compatibility

= 1.62.1 =
* Fixed: WordPress database error: [Table 'wordpressimport.wp_toolset_post_guid_id' doesn't exist]
* Fixed: Some Drupal 6 forums were not imported
* Fixed: [DOM] Found 3 elements with non-unique id #fgd2wp_nonce
* Fixed: [DOM] Found 2 elements with non-unique id #hostname

= 1.62.0 =
* Tweak: Refactor the Toolset Types code

= 1.61.3 =
* Fixed: The fields with duplicate names were not imported

= 1.61.2 =
* Fixed: The autocomplete entity references fields were not imported
* Tested with WordPress 4.9.8

= 1.61.1 =
* Fixed: "Fatal error: Can't use method return value in write context" on plugin activation for PHP < 5.5

= 1.61.0 =
* New: Compatible with Toolset Types 3
* New: Import the Media Entities
* Fixed: Images with absolute paths were not imported if Drupal is located on a subdirectory
* Fixed: URL field not imported on Drupal 8: [ERROR] Error:SQLSTATE[42S22]: Column not found: 1054 Unknown column 'f.field_url_target_id' in 'on clause'

= 1.60.2 =
* Fixed: The authors images were not imported
* Fixed: Images shortcodes not replaced in the post body
* Tested with WordPress 4.9.7

= 1.60.1 =
* New: Import the "image_miw" fields

= 1.60.0 =
* Tweak: Increase the scope of the get_node_custom_field_values() method so it can be used by the Metatags Quick module
* Change: Wording of the label "Remove only previously imported data"
* Tested with WordPress 4.9.6

= 1.59.0 =
* Change: Don't import the media custom field values if the "Skip media" option is checked
* Change: Don't import the attached images if the "Skip media" option is checked
* Change: Register the custom post types and custom taxonomies before importing the authors
* Fixed: Warning: stripslashes() expects parameter 1 to be string, array given
* Fixed: Mixed custom fields values between term custom fields and node custom fields
* Fixed: Needed modules were checked twice

= 1.58.3 =
* Fixed: Media containing "+" were not imported

= 1.58.2 =
* Fixed: The taxonomy terms hierarchy was not kept when using the Internationalization add-on

= 1.58.1 =
* Fixed: Fatal error: Uncaught Error: Call to a member function table_exists() on null

= 1.58.0 =
* New: Check if the Field Collection Drupal module is used
* New: Add a hook for the Field Collection add-on

= 1.57.2 =
* New: Import the "image_image" fields
* Fixed: The media containing attributes or anchors in their link were not imported
* Fixed: Some media fields were not imported

= 1.57.1 =
* Fixed: Warning: Invalid argument supplied for foreach()
* Fixed: Warning: A non-numeric value encountered

= 1.57.0 =
* New: Import the simple checkbox fields
* New: Import the select fields
* New: Import the radio buttons fields
* Fixed: Some node references were not imported
* Fixed: The nodes to skip were not saved when running the import

= 1.56.1 =
* Fixed: "Fatal error: Uncaught Error: Call to undefined method FG_Drupal_to_WordPress_Custom_Content::build_taxonomy_slug()" for Drupal 8 sites
* Tested with WordPress 4.9.5

= 1.56.0 =
* New: Check if the Nodewords Drupal module is used
* New: Check if the Page Title Drupal module is used
* Fixed: "[ERROR] Error:SQLSTATE[42S02]: Base table or view not found: 1146 Table 'node_field' doesn't exist" on Drupal 5

= 1.55.0 =
* New: Arabic language compatibility

= 1.54.1 =
* Fixed: Taxonomies longer than 30 characters were not imported

= 1.54.0 =
* New: Import the timestamp fields

= 1.53.0 =
* New: Ability to import the taxonomies by module (Drupal 6 only)

= 1.52.0 =
* New: Import the Drupal 8 field types: string_long, telephone, link, file
* Fixed: Notice: Undefined index: nid

= 1.51.0 =
* New: Import the Drupal 8 field types: string, integer, float, email, datetime, text_long, list_string

= 1.50.1 =
* Fixed: Some node reference fields were not imported

= 1.50.0 =
* New: Import the custom fields even if their storage is not defined in the field_config Drupal 7 table
* Tested with WordPress 4.9.4

= 1.49.1 =
* Fixed: Notice: Undefined index: type
* Fixed: Remove default Drupal prefix
* Fixed: Users not imported from the Drupal 8 databases that don't contain user images
* Fixed: The renamed taxonomies were not imported
* Fixed: "[ERROR] Error:SQLSTATE[42S02]: Base table or view not found: 1146 Table 'image' doesn't exist" on Drupal 7+
* Tweak: Use WP_IMPORTING
* Tested with WordPress 4.9.2

= 1.49.0 =
* New: Allow the Drupal 6 "article" node type in addition to the "story" node type

= 1.48.0 =
* New: Can import the Drupal databases stored on PostgreSQL
* Tested with WordPress 4.9.1

= 1.47.2 =
* Fixed: The passwords containing a backslash were not recognized
* Tested with WordPress 4.9

= 1.47.1 =
* Fixed: SQL error when some taxonomies contain quotes

= 1.47.0 =
* New: Import the users pictures
* New: Import the custom users fields

= 1.46.0 =
* New: Import the images managed by the Image Attach Drupal module
* Fixed: Displayed a warning about the Localization module if there were disabled languages
* Fixed: Displayed a warning about the Nodeblock module if the Field Collection module was used without the Nodeblock module

= 1.45.0 =
* New: Can import the Drupal databases stored on SQLite

= 1.44.1 =
* Fixed: The tags were not connected to the custom post types

= 1.44.0 =
* New: Check if we need the Forum add-on
* Tested with WordPress 4.8.3

= 1.43.1 =
* Fixed: Multiple link fields were not imported

= 1.43.0 =
* New: Import multiple date fields
* Fixed: [ERROR] Error:SQLSTATE[42S02]: Base table or view not found: 1146 Table 'config' doesn't exist
* Fixed: Notice: Undefined index: options

= 1.42.0 =
* New: Check if we need the Entity Reference add-on (Drupal 8)

= 1.41.1 =
* Fixed: Wrong images imported

= 1.41.0 =
* New: Add some hooks for the forum add-on
* Fixed: The comments author was not set for the comments written by the current user
* Fixed: Sanitize the file names with spaces

= 1.40.0 =
* New: Import the taxonomy custom fields
* New: Keep the images alignments
* Tested with WordPress 4.8.2

= 1.39.0 =
* New: Import the embedded media fields
* New: Check if we need the Media Provider add-on

= 1.38.1 =
* Fixed: Avoid double slash in the media filenames
* Fixed: Notice: Undefined index: module
* Tweak: code refactoring

= 1.38.0 =
* New: Add the hook "fgd2wp_pre_register_wpcf_field"

= 1.37.0 =
* New: Check if we need the Entity Reference add-on
* Fixed: Security cross-site scripting (XSS) vulnerability in the Ajax importer

= 1.36.0 =
* Tweak: Add some hooks for the Entity Reference add-on

= 1.35.0 =
* New: Import the img_assist images referenced by a NID

= 1.34.1 =
* Fixed: Some image shortcodes were not replaced in the content
* Tested with WordPress 4.8.1

= 1.34.0 =
* Fixed: Notice: Undefined offset: 0
* New: Import Drupal 7 custom media field types whose module equals "file"
* New: Import the Drupal 7 node relationships

= 1.33.0 =
* New: Import the image caption in the media attachment page

= 1.32.0 =
* New: Check if the NodeBlock add-on is necessary
* New: Add a filter for the Nodeblock add-on
* Change: Append custom content to the body instead of overwriting it

= 1.31.1 =
* Change: Wording and translations

= 1.31.0 =
* New: Check if we need the CCK add-on (Drupal 5)
* Fixed: [ERROR] Error:SQLSTATE[42S02]: Base table or view not found: 1146 Table 'upload' doesn't exist (Drupal 5)
* Fixed: Add some fixes for Drupal 5 custom fields

= 1.30.0 =
* New: Authenticate the imported users by their email

= 1.29.1 =
* New: Don't import the authors of the excluded node types
* Fixed: Images imported from the custom fields were not stored in the right folder
* Fixed: Remove the donate sentence

= 1.29.0 =
* New: Import the media contained in the custom fields

= 1.28.0 =
* New: Modify internal links in drafts

= 1.27.0 =
* New: Block the import if the URL field is empty and if the media are not skipped
* New: Add error messages and information

= 1.26.0 =
* New: Import and replace the Image Assist shortcodes

= 1.25.0 =
* New: Add the percentage in the progress bar
* New: Display the progress and the log when returning to the import page
* Change: Restyling the progress bar
* Fixed: Typo - replace "complete" by "completed"
* Tested with WordPress 4.8

= 1.24.1 =
* Fixed: Some URLs were wrongly redirected
* Tested with WordPress 4.7.5

= 1.24.0 =
* New: Import the medias from the imagefield_crop field

= 1.23.2 =
* Fixed: Allow media src containing extra spaces
* Tested with Drupal 5

= 1.23.1 =
* New: Display the custom taxonomy columns in the custom posts list
* Fixed: The relations between node types were not imported if the field name was not equal to the node type
* Tested with WordPress 4.7.4

= 1.23.0 =
* New: Import the images from the image_fupload_imagefield field

= 1.22.1 =
* New: Ability to resume the import during the authors import
* Tweak: Optimize the memory used for the authors import

= 1.22.0 =
* New: Compatible with the Media Internet Sources module
* Fixed: The featured image was always set to the image field

= 1.21.0 =
* New: Import the custom fields for the standard post types

= 1.20.0 =
* New: Redirect the URLs like /999/article-name

= 1.19.0 =
* New: Check if we need the Internationalization module
* Tweak: Add some hooks for internationalization
* Fixed: Images not imported on Drupal 8
* Tested with WordPress 4.7.3

= 1.18.0 =
* New: Migrates the Drupal 6 stories

= 1.17.0 =
* New: Import the images stored on Amazon S3
* New: Check if we need the Ubercart add-on

= 1.16.6 =
* Fixed: [ERROR] Error:SQLSTATE[42S22]: Column not found: 1054 Unknown column 'f.field_audiofield_alt' in 'field list'

= 1.16.5 =
* Fixed: Wrong field type for images and files

= 1.16.4 =
* Fixed: Medias that are not in the standard /sites/default/files directory were not imported
* Fixed: [ERROR] Error:SQLSTATE[42S02]: Base table or view not found: 1146 Table 'field_data_field_image' doesn't exist
* Fixed: [ERROR] Error:SQLSTATE[42000]: Syntax error or access violation: 1064
* Tested with WordPress 4.7.2

= 1.16.3 =
* Fixed: Notice: Undefined index: page
* Fixed: Warning: Invalid argument supplied for foreach()

= 1.16.2 =
* Fixed: Images not imported on some servers
* Tested with WordPress 4.7.1

= 1.16.1 =
* Fixed: Taxonomies containing spaces were unreachable in the backend
* Tweak: Code refactoring

= 1.16.0 =
* New: Import the images descriptions
* New: Import the nodes relationships
* Fixed: Notice: register_taxonomy was called incorrectly. Taxonomy names must be between 1 and 32 characters in length.
* Fixed: Taxonomies that contain non latin characters were not imported

= 1.15.1 =
* Fixed: Existing images attached to imported posts were removed when deleting the imported data
* Tested with WordPress 4.7

= 1.15.0 =
* New: Import the "Media" custom fields

= 1.14.1 =
* Fixed: Images not imported if there is no node summary

= 1.14.0 =
* New: Can import taxonomies terms with a same name and a different parent
* New: Check if the Name add-on is needed
* New: Check if the Addressfield add-on is needed
* Fixed: Notice: Undefined index: repetitive
* Fixed: On Drupal 6 [ERROR] Error:SQLSTATE[42S22]: Column not found: 1054 Unknown column 'f.delta' in 'order clause'
* Tweak: Taxonomies import speed increased

= 1.13.0 =
* New: Import the multiple values custom fields
* Fixed: Drupal 7 images not imported as custom fields
* Fixed: Wrong progress bar color

= 1.12.3 =
* Fixed: Notice: Undefined index: details
* Fixed: Custom taxonomies not assigned to standard posts
* Fixed: Notice: register_post_type was called incorrectly. Post type names must be between 1 and 20 characters in length.

= 1.12.2 =
* Fixed: The progress bar didn't move during the first import
* Fixed: The log window was empty during the first import

= 1.12.1 =
* Fixed: Notice: register_taxonomy was called incorrectly. Taxonomy names must be between 1 and 32 characters in length.
* Fixed: The Body content was imported in a custom field and not in the main post content.

= 1.12.0 =
* New: Ability to import only specific node types

= 1.11.2 =
* Fixed: The "IMPORT COMPLETE" message was still displayed when the import was run again

= 1.11.1 =
* Fixed: The images protected by a user agent protection were not imported

= 1.11.0 =
* New: Check if the Metatag add-on is needed
* Fixed: Database passwords containing "<" were not accepted

= 1.10.1 =
* Fixed: Terms whoose taxonomy contains accents were not imported
* Tweak: Code refactoring

= 1.10.0 =
* New: Allow the import of CCK image fields and CCK file fields
* Fixed: Import the standard fields as textfield and not as wysiwyg because the wysiwyg type can't be changed afterwards

= 1.9.0 =
* New: Import the "File" field type
* Fixed: Drupal 6 custom taxonomies containing spaces were not assigned to custom post types
* Fixed: Drupal 6 terms which taxonomy contains spaces were not imported
* Fixed: Compatibility issue with PHP < 5.4
* Tweak: If the import is blocked, stop sending AJAX requests

= 1.8.1 =
* Fixed: Notice: Undefined index: body_summary
* Fixed: Notice: Undefined index: body_value

= 1.8.0 =
* New: Migrates the link custom field
* New: Authorize the connections to Web sites that use invalid SSL certificates

= 1.7.0 =
* New: Map the date and datetime fields
* New: Check if the CCK add-on is needed
* New: Check if the Location add-on is needed
* New: Allow the use of the CCK add-on
* Fixed: WordPress database error: [Table 'wp_fg_redirect' doesn't exist] TRUNCATE wp_fg_redirect
* Fixed: Warning: Invalid argument supplied for foreach()

= 1.6.0 =
* New: Modify links like /node/xx and like /taxonomy/term/xx in the posts content

= 1.5.0 =
* New: SEO: Redirect the URLs like /node and /taxonomy/term

= 1.4.0 =
* New: Import the URL alias
* New: SEO: Redirect the Drupal URLs
* Fixed: Custom posts front pages show a "404 Not found" page

= 1.3.2 =
* Fixed: Drupal 6 nodes imported with a wrong revision
* Fixed: Warning: Invalid argument supplied for foreach()

= 1.3.1 =
* Fixed: Parse error: syntax error, unexpected '[', expecting ')'

= 1.3.0 =
* New: Import the custom post types
* New: Import the custom taxonomies
* New: Import the custom fields

= 1.2.0 =
* New: Partial import options
* New: Import the Drupal comments
* New: Import the Drupal users
* New: Authenticate the users using their Drupal passwords
