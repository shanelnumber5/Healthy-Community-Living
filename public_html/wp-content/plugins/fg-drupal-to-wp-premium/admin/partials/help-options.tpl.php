<div id="fgd2wp-help-options">
<h1>FG Drupal to WordPress Options</h1>

<h2>Empty WordPress content</h2>
<p>Before running the import or if you want to rerun the import from scratch, you can empty the WordPress content.</p>
<p><strong>Remove only the new imported data:</strong> Only the new imported data will be removed when you click on the "Empty WordPress content" button.</p>
<p><strong>Remove all WordPress content:</strong> All the WordPress content (posts, pages, attachments, categories, tags, comments, navigation menus, custom post types) will be removed when you click on the "Empty WordPress content" button.</p>
<p><strong>Automatic removal:</strong> If you check this option, all the WordPress content will be deleted when you click on the Import button.</p>


<h2>Drupal web site parameters</h2>

<p><strong>URL:</strong> In this field, you fill in the Drupal home page URL. This field is required to transfer the images from Drupal to WordPress.</p>


<h2>Drupal database parameters</h2>

<p>You can find the following informations in the Drupal file <strong>sites/default/settings.php</strong></p>

<p><strong>Hostname:</strong> host</p>
<p><strong>Port:</strong> By default, it is 3306.</p>
<p><strong>Database:</strong> database</p>
<p><strong>Username:</strong> username</p>
<p><strong>Password:</strong> password</p>
<p><strong>Drupal Table Prefix:</strong> prefix</p>


<h2>Behavior</h2>

<p><strong>Import summary:</strong> The summary can be imported to the post excerpt or to the post content or to both.</p>

<p><strong>Medias:</strong><br />
<ul>
<li><strong>Skip media:</strong> You can import or skip the medias (images, attached files).</li>
<li><strong>Set featured image from:</strong> You can set the WordPress featured image from the Drupal image field, or from the first image of the content, or don't set a featured image.</li>
<li><strong>Import only the featured images:</strong> By selecting this option, the images contained in the post content won't be imported to the WordPress media library.</li>
<li><strong>Remove the first image from the content:</strong> this option can be useful if your theme displays the featured image at the top of the article, to avoid the same image displayed twice.</li>
<li><strong>Import external media:</strong> If you want to import the medias that are not on your site, check the "External media" option. Be aware that it can reduce the speed of the import or even hang the import.</li>
<li><strong>Import media with duplicate names:</strong> If you have several images with the exact same filename in different directories, you need to check the "media with duplicate names" option. In this case, all the filenames will be named with the directory as a prefix.</li>
<li><strong>Force media import:</strong> If you already imported some images and these images are corrupted on WordPress (images with a size of 0Kb for instance), you can force the media import. It will overwrite the already imported images. In a normal use, you should keep this option unchecked.</li>
<li><strong>Timeout for each media:</strong> The default timeout to copy a media is 5 seconds. You can change it if you have many errors like "Can't copy xxx. Operation timeout".</li>
</ul>
</p>

<p><strong>SEO <span class="fgd2wp_premium_feature">(Premium feature)</span>:</strong>
<ul>
<li><strong>Redirect the Drupal URLs:</strong> With this option checked, the old Drupal article links will be automatically redirected to the new WordPress URLs. It uses "301 redirect". By this way, the SEO will be kept. The plugin must remain active to redirect the URLs.</li>
</ul></p>

<p><strong>Users <span class="fgd2wp_premium_feature">(Premium feature)</span>:</strong>
<ul>
<li><strong>Allow Unicode characters in the usernames:</strong> With this option checked, the non latin characters will be kept in the usernames (like Greek, Cyrillic, Arabic, ...). Otherwise these characters will be converted to latin characters.</li>
</ul></p>

<p><strong>Partial import <span class="fgd2wp_premium_feature">(Premium feature)</span>:</strong> If you don't want to import all the Drupal data, you can use this option. Please note that even if you don't use this option and if you rerun the import, the already imported content won't be imported twice.</p>

<?php do_action('fgd2wp_help_options'); ?>

</div>
