<?php

/**
 * Implements hook_views_data().
 */
function tweet_feed_views_data() {
  return array(
    'tweet_feed' => array(
      'table' => array(
        'group' => 'Twitter Feed',
        'base' => array(
          'field' => 'tid',
          'title' => 'Twitter Feed Posts',
          'weight' => -10,
        ),
      ),
      'tweet_id' => array(
        'title' => t('Tweet ID'),
        'help' => t('The Twitter Tweet ID of the tweet.'),
        'field' => array(
          'handler' => 'views_handler_field_numeric',
          'click sortable' => TRUE,
        ),
        'filter' => array(
          'handler' => 'views_handler_filter_numeric',
        ),
        'sort' => array(
          'handler' => 'views_handler_sort',
        ),
      ),
      'tweet' => array(
        'title' => t('Tweet Text'),
        'help' => t('The actual 140 character tweet text.'),
        'field' => array(
          'handler' => 'views_handler_tweet_field',
          'click sortable' => FALSE,
        ),
        'sort' => array(
          'handler' => 'views_handler_sort',
        ),
      ),
      'created_at' => array(
        'title' => t('Tweet Creation Time'),
        'help' => t('The creation time stamp of the tweet.'),
        'field' => array(
          'handler' => 'views_handler_field_date',
        ),
        'sort' => array(
          'handler' => 'views_handler_sort',
        ),
      ),
      'user_id' => array(
        'title' => t('User ID'),
        'help' => t('The user_id of the person making the tweet.'),
        'field' => array(
          'handler' => 'views_handler_field_numeric',
          'click sortable' => TRUE,
        ),
        'filter' => array(
          'handler' => 'views_handler_filter_numeric',
        ),
        'sort' => array(
          'handler' => 'views_handler_sort',
        ),
      ),
      'is_reply' => array(
        'title' => t('Is Reply'),
        'help' => t('Is this tweet a reply to another tweet?'),
        'field' => array(
          'handler' => 'views_handler_field_numeric',
          'click sortable' => TRUE,
        ),
        'filter' => array(
          'handler' => 'views_handler_filter_numeric',
        ),
        'sort' => array(
          'handler' => 'views_handler_sort',
        ),
      ),
      'profile_image_url' => array(
        'title' => t('Profile Image URL'),
        'help' => t('The url to the profile image of the tweeter.'),
        'field' => array(
          'handler' => 'views_handler_field',
        ),
      ),
      'profile_image_https_url' => array(
        'title' => t('Profile Image Secure URL'),
        'help' => t('The url to the profile image of the tweeter (secure url).'),
        'field' => array(
          'handler' => 'views_handler_field',
        ),
      ),
      'media_url' => array(
        'title' => t('Media Image URL'),
        'help' => t('The url to the media image for a tweet.'),
        'field' => array(
          'handler' => 'views_handler_field',
        ),
      ),
      'media_url_https' => array(
        'title' => t('Media Image Secure URL'),
        'help' => t('The url to the media image for a tweet (secure url).'),
        'field' => array(
          'handler' => 'views_handler_field',
        ),
      ),
      'screen_name' => array(
        'title' => t('Screen Name'),
        'help' => t('The screen name of the tweeter.'),
        'field' => array(
          'handler' => 'views_handler_field',
        ),
      ),
      'name' => array(
        'title' => t('Name'),
        'help' => t('The full name of the tweeter.'),
        'field' => array(
          'handler' => 'views_handler_field',
        ),
      ),
      'hashtags' => array(
        'title' => t('Hashtags'),
        'help' => t('The Hashtags associated with a tweet.'),
        'field' => array(
          'handler' => 'views_handler_hashtag_field',
        ),
        'argument' => array(
          'handler' => 'views_handler_argument_hashtag',
        ),
        'filter' => array(
          'handler' => 'views_handler_filter_hashtag_string',
        ),
      ),
    ),
  );
}


class views_handler_filter_hashtag_string extends views_handler_filter_string {
  function op_contains($field) {
    $this->query->add_where($this->options['group'], $field, array($this->value), 'IN');
  }
}

/**
 * override views_handler_field::render so that it does not sanitize our URL's and other links
 */
class views_handler_tweet_field extends views_handler_field {

  function render($values) {
    $tweet = $this->get_value($values);

    /* based on our preference, assign all links to new windows or to the same window */
    $target = (variable_get('tweet_feed_new_window',0) == 1) ? '_blank' : '_self';

    /* look for twitter handles and make them clickable */
    /* modified so that slashes in the twitter handle are counted */
    $pattern = '/@([A-Za-z0-9_]{1,15})(?![.A-Za-z])/';
    $replace  = '<a target="'.$target.'" href="http://twitter.com/'.strtolower('\1').'">@\1</a>';
    $tweet   = preg_replace($pattern,$replace,$tweet);

    /* look for twitter hash tags and make them clickable */
    /* modified so that slashes in the twitter handle are counted */
    $tweet = preg_replace('/(^|\s)#(\w*+\w*)/', '\1<a target="'.$target.'" href="http://twitter.com/search?q=%23\2">#\2</a>', $tweet);
    
    $tweet = utf8_decode($tweet);
    return htmlspecialchars_decode($tweet);
  }
}

/**
 * override the string handler for handling hash tags as we need to change the query 
 * so we can look through the string with a LIKE instead of an =
 */
class views_handler_argument_hashtag extends views_handler_argument_string {

	function option_definition() {
		$options = array();
		$options['match_exact'] = array(
			'default' => FALSE,
			'bool' => TRUE,
		);
		return $options;
	}

	function options_form(&$form, &$form_state) {
		$form['match_exact'] = array(
			'#type' => 'checkbox',
			'#title' => t('Exact Match Mode'),
			'#description' => t('Match the argument exactly. Not as a potential substring.'),
			'#default_value' => $this->options['match_exact'],
			'#fieldset' => 'more',
		);
	}

  function query($group_by = FALSE) {  
  	if (empty($this->options['match_exact'])) {
  		$argument = '%' . $this->argument . '%';
    }
    else {
    	$argument = ' ' . $this->argument . ' ';
    }
    
    $this->value = array($argument);
  	$this->operator = 'or';
  
    $this->ensure_my_table();
    $formula = FALSE;

		$field = "$this->table_alias.$this->real_field";
 
    if (count($this->value) > 1) {
      $operator = 'IN';
      $argument = $this->value;
    }
    else {
    	if (empty($this->options['match_exact'])) {
      	$operator = 'LIKE';
      } 
      else {
      	$operator = 'REGEXP';
      }
    }
    
    $this->query->add_where(0, $field, $argument, $operator);
  }
}