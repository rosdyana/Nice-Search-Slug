<?php 
	/*
	Plugin Name: Nice Search Slug
	Plugin URI: http://wordpress.org/plugins/nice-search-slug/
	Description: a plugin to redirects ?s=query searches to /search/query, and converts %20 to -
	Version: 0.1
	Author: Rosdyana Kusuma
	Author URI: http://r3m1ck.us/about
	License: GPL2
	*/
	
	if ( !function_exists( 'add_action' ) ) {
		echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
		exit;
	}
	
	add_action('init', 'nice_search_init');
	//init
	function nice_search_init() {
		// Localization
		load_plugin_textdomain('NC', false, dirname(plugin_basename(__FILE__)) . '/languages/');
	}
	//
	
	// register the admin menu settings for plugin settings
	add_action('admin_menu', 'nice_search_admin_actions');

	function nice_search_admin_actions() {
		$page_title = 'Nice Search Slug';
		$menu_title = 'Nice Search Setting';
		$capability = 'manage_options';
		$menu_slug	= 'nice-search-slug';
		$function 	= 'nice_search_admin';
		
		add_options_page($page_title, $menu_title, $capability, $menu_slug, $function);
	}
	//
	
	// add setting link in plugin page
	add_filter('plugin_action_links', 'nice_search_action_links', 10, 2);

	function nice_search_action_links($links, $file) {
		static $this_plugin;
		
		if (!$this_plugin) {
			$this_plugin = plugin_basename(__FILE__);
		}
		
		if ($file == $this_plugin) {
			$settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=nice-search-slug">Settings</a>';
			array_unshift($links, $settings_link);
		}
		return $links;
	}
	//
	
		// show setting page for admin
	function nice_search_admin(){
		if (!current_user_can('manage_options')) {
			wp_die('You do not have sufficient permissions to access this page.');
		}

		$nice_code = htmlentities(get_option('nice_code') != '') ? get_option('nice_code') : 'search';
		
		$html = '</pre>
	<div class="wrap"><form action="options.php" method="post" name="options">
	'.'<h2>' . __('Nice Search Setting Page!', 'NC') . '</h2>' .'
	' . wp_nonce_field('update-options') . '
	'.'<h3>' . __('Put your search slug here','NC').'</h3>'.'
	<textarea name="nice_code" rows="1" cols="26">' . $nice_code . '</textarea> 
	<br>
	 <input type="hidden" name="action" value="update" />

	 <input type="hidden" name="page_options" value="nice_code" />

	<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="'. __('Save Changes','NC').'"  /></form></div>

	'.'<h3>'. __('How to use ?','NC').'</h3>'.'
	'.__('<p><cite>Input the slug and when your search page got 404 it will redirect into your customize slug.</cite></p>','NC').'
	'.'<h3>'.__('Contact me','NC').'</h3>'.'
		<a href="mailto:admin@r3m1ck.us" target="_blank"><img src="' . plugins_url( 'images/email.png' , __FILE__ ) . '" alt="mail"/></a>
		<a href="https://plus.google.com/u/0/115883076446540246884/posts" target="_blank"><img src="' . plugins_url( 'images/gplus.png' , __FILE__ ) . '" alt="google"/></a>
		<a href="https://twitter.com/XremickX" target="_blank"><img src="' . plugins_url( 'images/twitter.png' , __FILE__ ) . '" alt="twitter"/></a>
	<pre>
	';
		echo $html;
	}
	//

	function plus_to_minus() {
		$get_code = get_option('nice_code');
		if( $get_code == "" )
			$get_code = "search";
		if ( is_search() && strpos($_SERVER['REQUEST_URI'], '/wp-admin/') === false && strpos($_SERVER['REQUEST_URI'], '/$get_code/') === false ) {
			wp_redirect(get_bloginfo('home') . '/'.$get_code.'/' . str_replace(' ', '-', str_replace('%20', '+', get_query_var('s'))));
			exit();
		}
	}

	add_action('template_redirect', 'plus_to_minus');

?>