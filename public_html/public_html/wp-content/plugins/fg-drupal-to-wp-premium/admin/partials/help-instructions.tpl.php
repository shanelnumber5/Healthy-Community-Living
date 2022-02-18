<div id="fgd2wp-help-instructions">

<h1>FG Drupal to WordPress Instructions</h1>

<h2>Step 0:</h2>
<p>Before using the plugin, you must:
	<ul>
		<li>Define the media sizes on <a href="<?php echo admin_url('options-media.php'); ?>" target="_blank">the media settings screen</a><br />
		The plugin will move your Drupal images to the WordPress media library and will resize them to all the sizes defined here.</li>
	</ul>
</p>

<h2>Step 1:</h2>
<h3>Empty the WordPress content</h3>
<p>This action is not mandatory the first time you run the import. But it is required if you have already ran an import and if you want to restart if from scratch. It will delete all the WordPress content (posts, pages, attachments, categories, tags, comments, navigation menus, custom post types).</p>

<h2>Step 2:</h2>
<h3>Test the connection</h3>
<p>After having filled in the database parameters, you can test the connection to the Drupal database. It will tell you how many articles the plugin has found in the Drupal database.</p>

<h2>Step 3:</h2>
<h3>Run the import</h3>
<p>After having chosen the different import options (see the options help tab), you click on this button to run the import. It can take a long time depending on the number of articles and images in Drupal.</p>
<p>Once the process is finished, it will display the import results.</p>
<p>If the process stops before having imported all the content, you can run it again and it will continue where it left off. This may happen if you have a timeout on your server or if the memory becomes low. In this case, ensure that the automatic removal checkbox is not checked.</p>

<h2>Step 4:</h2>
<h3>Modify the internal links</h3>
<p>Click on this button to modify the links inside each post or page to make them point to the new WordPress URLs.</p>

<h2>Import in command line by WP CLI <span class="fgd2wp_premium_feature">(Premium feature)</span></h2>
<p>The import in command line is much faster than the import by the browser.</p>
<p>You must first install WP CLI on your WordPress server. See the <a href="https://wp-cli.org/" target="_blank">WP CLI installation procedure</a>.</p>
<p>Before using the WP CLI commands, you must configure all the plugin settings in the WordPress backend.</p>
<p>Here are the WP CLI commands that you can use:
	<ul>
		<li><strong>wp import-drupal empty</strong> : Empty the imported data</li>
		<li><strong>wp import-drupal empty all</strong> : Empty all the WordPress data</li>
		<li><strong>wp import-drupal test_database</strong> : Test the database connection</li>
		<li><strong>wp import-drupal import</strong> : Import the data</li>
		<li><strong>wp import-drupal modify_links</strong> : Modify the internal links</li>
	</ul>
</p>

<h2>Automatic import by cron <span class="fgd2wp_premium_feature">(Premium feature)</span></h2>
<p>If you want to import automatically the new data added to the Drupal database, you can do it with a cron command.
	<ul>
		<li>First you need to set up correctly all the settings in the import screen. It is advised to run the first import manually to be sure that the settings are correct.</li>
		<li>Then define your crontab like:<br />
			<code>
				0 0 * * * php /path/to/wp/wp-content/plugins/fg-drupal-to-wp-premium/cron_import.php >>/dev/null
			</code><br />
			This will run the import once a day at 0:00.<br />
			You can of course change the frequency if you want.
		</li>
	</ul>
</p>

<?php do_action('fgd2wp_help_instructions'); ?>

</div>
