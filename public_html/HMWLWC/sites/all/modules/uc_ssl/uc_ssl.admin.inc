<?php

function uc_ssl_admin()
{
   $form = array();
   $form['uc_ssl_ssl_domain'] = array(
    '#type' => 'textfield',
    '#title' => t('Secure Domain Name'),
    '#default_value' => variable_get('uc_ssl_ssl_domain', ''),
    //'#size' => 2,
    //'#maxlength' => 2,
    '#description' => t("This field accepts domain names in the following format:  https://something.com OR https://something.com:5938.  <br><br><b>IPMORTANT DRUPAL 7 NOTICE ABOUT DOMAIN NAMES:</b>  This module DOES support using port numbers and different domains for SSL and normal HTTP like it's Drupal 6 predecessor HOWEVER, this is NOT meant for a production environment!!!!  RFC restrictions PROHIBIT passing cookie data between sites with different port numbers (80 to 5933 for example.  The ONLY exception is for the default ports of 80 and 443 which are never specified to begin with and thus pass RFC requirements for sharing data!!) and it treats them as separate sites.  So if you use a port number drupal WILL require you to login BEFORE that url will work (drupal 6 has the same limitation btw, I just never documented it).  In Drupal 7 this results in the 'Store' configuration panel NOT showing up.  You must login to the store config panel by going to the https://yoursite.com:PORTNUMBER/admin/store url FIRST and login.  Once you have done that, you can access store/admin.  The administration panel overlay code for drupal 7 is NOT compatible with this module because it is NOT a secure method to access data!!!!  ALWAYS go DIRECTLY to the /store/admin link DIRECTLY.  The same goes for /cart.  /cart WILL ask you to login.   Once your site is ready for production you must absolutely use the SAME url for both secure AND non-secure pages.  So your site should always be accessed from http://yoursite.com and https://yoursite.com  OR  http://www.yoursite.com and https://www.yoursite.com.  It should NEVER be accessed like this:  http://yoursite.com http://ssl.yoursite.com.  Or any other sub domain for that matter.  It will result in drupal asking you to login!"),
    '#required' => TRUE,
   );

   $form['uc_ssl_non_ssl_domain'] = array(
    '#type' => 'textfield',
    '#title' => t('NON-Secure Domain Name'),
    '#default_value' => variable_get('uc_ssl_non_ssl_domain', ''),
    //'#size' => 2,
    //'#maxlength' => 2,
    '#description' => t("This field accepts domain names in the following format:  http://something.com OR http://something.com:8080"),
    '#required' => TRUE,
   );

   $form['uc_ssl_status'] = array(
    '#type' => 'radios',
    '#title' => t('Ubercart SSL Status'), 
    '#default_value' => variable_get('uc_ssl_status', ''), 
    '#options' => array( TRUE => t('Enabled'), FALSE => t('Disabled')),
    '#required' => TRUE,
   );

   $form['uc_ssl_switch_to_non_ssl'] = array(
    '#type' => 'radios',
    '#title' => t('Ubercart Non-SSL Switch'), 
    '#default_value' => variable_get('uc_ssl_switch_to_non_ssl', ''), 
    '#options' => array( TRUE => t('Enabled'), FALSE => t('Disabled')),
    '#required' => TRUE,
    '#description' => t("Since this module switches to SSL mode during checkout, all links will link back to a secured version of the website.  If you want to switch back to non-secured mode after a transaction is complete or they click on a keep shopping link, etc.  This isnt really necessary but some people may want to save on the small resources savings you can get from doing this.  So if you want to keep http for any non-cart related things, you should check this as enabled.  If you dont care, then set it to disabled.<br><br>DRUPAL 7 NOTICE:  Enabling this will DISABLE the Overlay module because it cannot work securely in a mixed SSL environment!!  Setting this back to Disabled WILL NOT re-enable the Overlay module.  You must re-enable the Overlay module yourself."),
   );
   
   
   $form['uc_ssl_case_insensitive'] = array(
    '#type' => 'radios',
    '#title' => t('Ubercart SSL Case Sensitivity'), 
    '#default_value' => variable_get('uc_ssl_case_insensitive', TRUE), 
    '#options' => array( TRUE => t('Enabled'), FALSE => t('Disabled')),
    '#required' => TRUE,
    '#description' => t('By default drupal allows paths to be accessed in a case insensitive manor.  This is fine but not very secure.  SSL Standards dictate that paths should always be case sensitive.  So in order to be compatible with SSL standards, we allow the administrator to change SSL path recognition to case sensitive by simply setting this setting to disabled.  In order to stick close to drupal standards, this module defaults this value to Enabled which means /Cart AND /cart would match and be secured where as in case sensitive mode /Cart would NOT be secured, alerting the user to an insecure page but /cart would be secured.'), 
   );
   
   return system_settings_form($form);
}

function uc_ssl_admin_validate($form, &$form_state) 
{
   $ssl_domain = $form_state['values']['uc_ssl_ssl_domain'];
   $non_ssl_domain = $form_state['values']['uc_ssl_non_ssl_domain'];
   if (!valid_url($ssl_domain) || !stristr($ssl_domain, 'https://'))
   {
      form_set_error('uc_ssl_ssl_domain', t('You must enter a valid SSL Domain name.'));
   }

   if (!valid_url($non_ssl_domain) || !stristr($non_ssl_domain, 'http://'))
   {
      form_set_error('uc_ssl_non_ssl_domain', t('You must enter a valid Non-SSL Domain name.'));
   }

   if (!uc_ssl_check($ssl_domain.base_path()))
   {
      form_set_error('uc_ssl_ssl_domain', t('CRITICAL ERROR!  The domain you entered for the secured domain came back as non-secure or it does not point back to this installation of drupal.  Your secure domain name MUST be setup and MUST be pointing at this install of drupal.'));
      variable_set('uc_ssl_status', '0');
      $form_state['values']['uc_ssl_status'] = '0';
      $form['values']['uc_ssl_status'] = '0';
   }

}