<?php 
/*
Plugin Name: Easily navigate pages on dashboard
Plugin URI: http://www.tristanbotly.com
Description: Display a folder tree of your pages that is easy to expand and contract on your Dashboard.
Author: Tristan Botly
Version: 1.01
Author URI: http://www.tristanbotly.com
*/

/*  Copyright 2010  PLUGIN_AUTHOR_NAME  (email : tristanbotly@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// Create the function to output the contents of our Dashboard Widget

// A big thank you to Page Tree Developer Måns Jonasson (http://www.mansjonasson.se) as some of that code was used to power this.

add_action( 'init', 'easy_navigate_init' );

function easy_navigate_init()
{
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('easy_navigate', plugins_url('easily-navigate-pages-on-dashboard.js', __FILE__));
}

function pagetree_maketree($pages, $public = false) {
	// Split into messy array
	$pageAr = explode("\n", $pages);

	foreach($pageAr AS $txt) {

		$out = "";

		$re1='.*?';
		$re2='(\\d+)';

		if ($c=preg_match_all ("/".$re1.$re2."/is", $txt, $matches))
		{ 
			$int1=$matches[1][0];

			$pageID = $int1;

			// Get post status (publish|pending|draft|private|future)
			$thisPage = get_page($pageID);
			$pageStatus = $thisPage -> post_status;
			$pageURL = get_permalink($pageID);

			if ($pageStatus != "publish") {
				$pageStatus = "strikethrough";
			}

			// Get page title
			$pageTitle = trim(strip_tags($txt));

			// Make sure we don't display empty page titles
			if ($pageTitle == "") $pageTitle = __("(no title)", "page-tree");

			$linesAr[$pageID] = $pageTitle;
			if (stristr($txt, "<li class")) { // This is a line with beginning LI
				$out .= "<li>";
			}

			if ($public) {
				// Create our own link to edit page for this ID
				//$out .= "<a class=\"$pageStatus\" href=\"$pageURL\">" . $pageTitle . "</a>";
				$out .= "<a href=\"#\" class=\"expand\">&nbsp;</a><a class=\"$pageStatus pagelink\" href=\"" . get_bloginfo('wpurl') . "/wp-admin/page.php?action=edit&post=$pageID\">" . $pageTitle . "</a>";
				//$out .= "<a class=\"$pageStatus\" href=\"#\">" . $pageTitle . "</a>";
			}
			else {
				$out .= "<a class=\"$pageStatus\" href=\"" . get_bloginfo('wpurl') . "/wp-admin/page.php?action=edit&post=$pageID\">" . $pageTitle . "</a>";
			}

			if (stristr($txt, "</li>")) { // This is a line with an ending LI
				$out .= "</li>";
			}

			$outAr[] = $out;


		}
		else { // This is a line with something else than a page (<ul>, </ul>, etc) - just add it to the pile
			$outAr[] = $txt;
		}

		// Keep all lines in $origAr just in case we want to check things again in the future
		$origAr[] = $txt;

	}

	// Print the new, pretty UL-LI by joining the array
	return join("\n", $outAr);
}

function manage_pages_dashboard_widget_function() {

	$args = array(
		"echo" => 0,
		"title_li" => "", 
		"link_before" => "", 
		"link_after" => "",
		"sort_column" => "menu_order"
	);
	$pages = wp_list_pages($args);
	
	
?>	

    <div id="easily-navigate-pages-on-dashboard">
		<ul>
			<?php echo pagetree_maketree($pages, true); ?>
		</ul>
		<a class="devlink" href="http://www.tristanbotly.com/?utm_source=wordpress&utm_medium=easily-navigate-pages-on-dashboard" target="_blank" title="Open a new window">Developer Site</a>
    	<div style="clear: right; height: 1px; overflow:hidden;">&nbsp;</div>
    </div>

<?php
}

// Create the function use in the action hook

function manage_pages_dashboard_widgets() {
	wp_add_dashboard_widget('manage_pages_dashboard_widget', 'Website Pages', 'manage_pages_dashboard_widget_function');	
} 

// Hoook into the 'wp_dashboard_setup' action to register our other functions

add_action('wp_dashboard_setup', 'manage_pages_dashboard_widgets' );

function admin_register_head() {
    $siteurl = get_option('siteurl');
    $url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/easily-navigate-pages-on-dashboard.css';
    $javascript = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/easily-navigate-pages-on-dashboard.js';
	echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
	//echo "<script type='text/javascript' src='$javascript'></script>\n";
    wp_enqueue_script('my-script', '$url', array('jquery'), '1.0');
    
    
    
}

add_action('admin_head', 'admin_register_head');
 
?>