<?php

/**
 * Implements hook_views_default_views().
 */
function tweet_feed_views_default_views() {
  $export = array();

  $view = new view();
  $view->name = 'tweet_feed';
  $view->description = 'A default view for displaying tweets.';
  $view->tag = 'default';
  $view->base_table = 'tweet_feed';
  $view->human_name = 'Twitter Feed';
  $view->core = 7;
  $view->api_version = '3.0';
  $view->disabled = TRUE; /* Edit this to true to make a default view disabled initially */

  /* Display: Master */
  $handler = $view->new_display('default', 'Master', 'default');
  $handler->display->display_options['title'] = 'Twitter Feed';
  $handler->display->display_options['use_more_always'] = FALSE;
  $handler->display->display_options['access']['type'] = 'none';
  $handler->display->display_options['cache']['type'] = 'none';
  $handler->display->display_options['query']['type'] = 'views_query';
  $handler->display->display_options['exposed_form']['type'] = 'basic';
  $handler->display->display_options['pager']['type'] = 'full';
  $handler->display->display_options['pager']['options']['items_per_page'] = '10';
  $handler->display->display_options['style_plugin'] = 'default';
  $handler->display->display_options['row_plugin'] = 'fields';
  /* Field: Twitter Feed: Profile Image URL */
  $handler->display->display_options['fields']['profile_image_url']['id'] = 'profile_image_url';
  $handler->display->display_options['fields']['profile_image_url']['table'] = 'tweet_feed';
  $handler->display->display_options['fields']['profile_image_url']['field'] = 'profile_image_url';
  $handler->display->display_options['fields']['profile_image_url']['label'] = '';
  $handler->display->display_options['fields']['profile_image_url']['alter']['alter_text'] = TRUE;
  $handler->display->display_options['fields']['profile_image_url']['alter']['text'] = '<img src="[profile_image_url]" border="0">';
  $handler->display->display_options['fields']['profile_image_url']['element_label_colon'] = FALSE;
  /* Field: Twitter Feed: Screen Name */
  $handler->display->display_options['fields']['screen_name']['id'] = 'screen_name';
  $handler->display->display_options['fields']['screen_name']['table'] = 'tweet_feed';
  $handler->display->display_options['fields']['screen_name']['field'] = 'screen_name';
  $handler->display->display_options['fields']['screen_name']['label'] = '';
  $handler->display->display_options['fields']['screen_name']['element_label_colon'] = FALSE;
  /* Field: Twitter Feed: Tweet Text */
  $handler->display->display_options['fields']['tweet']['id'] = 'tweet';
  $handler->display->display_options['fields']['tweet']['table'] = 'tweet_feed';
  $handler->display->display_options['fields']['tweet']['field'] = 'tweet';
  $handler->display->display_options['fields']['tweet']['label'] = '';
  $handler->display->display_options['fields']['tweet']['element_label_colon'] = FALSE;
  /* Field: Twitter Feed: Tweet Creation Time */
  $handler->display->display_options['fields']['created_at']['id'] = 'created_at';
  $handler->display->display_options['fields']['created_at']['table'] = 'tweet_feed';
  $handler->display->display_options['fields']['created_at']['field'] = 'created_at';
  $handler->display->display_options['fields']['created_at']['label'] = '';
  $handler->display->display_options['fields']['created_at']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['created_at']['date_format'] = 'long';
  /* Sort criterion: Twitter Feed: Tweet Creation Time */
  $handler->display->display_options['sorts']['created_at']['id'] = 'created_at';
  $handler->display->display_options['sorts']['created_at']['table'] = 'tweet_feed';
  $handler->display->display_options['sorts']['created_at']['field'] = 'created_at';
  $handler->display->display_options['sorts']['created_at']['order'] = 'DESC';

  /* Display: Page */
  $handler = $view->new_display('page', 'Page', 'page');
  $handler->display->display_options['path'] = 'twitter';

  /* Display: Block */
  $handler = $view->new_display('block', 'Block', 'block_1');

  $export['tweet_feed'] = $view;
  return $export;
}