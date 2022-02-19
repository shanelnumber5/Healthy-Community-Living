<?php

/**
 * @file
 * Hooks provided by the Page Builder module.
 */

/**
 * 
 * @param type $page
 *  - id : page id
 *  - page_title 
 *  - show_title 0 or 1, if 0 page tile will disable on template
 *  - active 0 or 1, if 0 page will not show, and return drupal page not found
 *  - data string of array page data
 *  - created 
 *  - updated
 */
function HOOK_page_builer_view($page) {
  $page->page_title = t('My custom title');
}

/**
 * Respond to a page being updated.
 * @param type $page
 * - id : page id
 *  - page_title 
 *  - show_title
 *  - active 
 *  - data string of array page data
 *  - created 
 *  - updated
 */
function HOOK_page_builder_update($page) {
  // do you custom here. 
}

/**
 * Respond to a page being Inserted.
 * @param type $page
 * - id : page id
 *  - page_title 
 *  - show_title
 *  - active 
 *  - data string of array page data
 *  - created 
 *  - updated
 */
function HOOK_page_builder_insert($page) {
  // do you custom here. 
}


/**
 * Respond to a page being Deleted.
 * @param type $page
 * - id : page id
 *  - page_title 
 *  - show_title
 *  - active 
 *  - data string of array page data
 *  - created 
 *  - updated
 */
function HOOK_page_builder_delete($page) {
  // do you custom here. 
}
