<?php
/**
 * bbCode module
 *
 * Replace the bbcode by HTML code
 * 
 * @link       https://www.fredericgilles.net/drupal-to-wordpress/
 * @since      1.92.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_BbCode', false) ) {

	/**
	 * BbCode class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_BbCode {

		private $plugin;
		
		/**
		 * Initialize the class and set its properties.
		 *
		 * @param object $plugin Admin plugin
		 */
		public function __construct( $plugin ) {
			$this->plugin = $plugin;
		}
		
		/**
		 * Replace the bbcode in the body_value and in the body_summary
		 * 
		 * @param array $node Node
		 * @return array Node
		 */
		public function replace_bbcode_in_node($node) {
			if ( version_compare($this->plugin->drupal_version, '5', '<') ) {
				// Drupal 4
				$node['body_value'] = $this->replace_bbcode($node['body_value']);
				$node['body_summary'] = $this->replace_bbcode($node['body_summary']);
			}
			return $node;
		}
		
		/**
		 * Replace the bbCode shortcodes
		 * 
		 * @param string $content Content
		 * @return string Content
		 */
		private function replace_bbcode($content) {
			$content = preg_replace("#\[img.*\](.*?) *\[/img.*\]#", "<img src=\"$1\" />", $content);
			$content = preg_replace("#\[url\=(.*?)\](.*?)\[/url\]#", "<a href=\"$1\">$2</a>", $content);
			$content = preg_replace("#\[url.*\](.*?)\[/url.*\]#", "<a href=\"$1\">$1</a>", $content);
			$content = preg_replace("#\[b\](.*?)\[/b\]#", "<strong>$1</strong>", $content);
			$content = preg_replace("#\[i\](.*?)\[/i\]#", "<em>$1</em>", $content);
			$content = preg_replace("#\[u\](.*?)\[/u\]#", "<u>$1</u>", $content);
			$content = preg_replace("#\[strike\](.*?)\[/strike\]#", "<del>$1</del>", $content);
			$content = preg_replace("#\[sub\](.*?)\[/sub\]#", "<sub>$1</sub>", $content);
			$content = preg_replace("#\[sup\](.*?)\[/sup\]#", "<sup>$1</sup>", $content);
			$content = preg_replace("#\[size=(\d+)\](.*?)\[/size\]#", "<font size=\"$1\">$2</font>", $content);
			$content = preg_replace("#\[color=(.+)\](.*?)\[/color\]#", "<font color=\"$1\">$2</font>", $content);
			$content = preg_replace("#\[left\](.*?)\[/left\]#s", "<div align=\"left\">$1</div>", $content);
			$content = preg_replace("#\[center\](.*?)\[/center\]#s", "<div align=\"center\">$1</div>", $content);
			$content = preg_replace("#\[right\](.*?)\[/right\]#s", "<div align=\"right\">$1</div>", $content);
			$content = preg_replace("#\[quote\](.*?)\[/quote\]#s", "<blockquote>$1</blockquote>", $content);
			$content = preg_replace("#\[code\](.*?)\[/code\]#s", "<code>$1</code>", $content);
			$content = preg_replace("#\[ul\](.*?)\[/ul\]#s", "<ul>$1</ul>", $content);
			$content = preg_replace("#\[ol\](.*?)\[/ol\]#s", "<ol>$1</ol>", $content);
			$content = preg_replace("#\[li\](.*?)\[/li\]#s", "<li>$1</li>", $content);
			$content = preg_replace("#\[table\](.*?)\[/table\]#s", "<table>$1</table>", $content);
			$content = preg_replace("#\[tr\](.*?)\[/tr\]#s", "<tr>$1</tr>", $content);
			$content = preg_replace("#\[td\](.*?)\[/td\]#s", "<td>$1</td>", $content);
			
			return $content;
		}
		
	}
}
