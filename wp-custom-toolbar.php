<?php
/*
Plugin Name: WP Custom Toolbar
Plugin URI: http://www.clearpathdigital.com/
Description: Disable portions of the Wordpress Toolbar for users in a particular role.
Version: 0.1
Author: David Turner for ClearPath Digital
Author URI: http://www.clearpathdigital.com/
Text Domain: wp-custom-toolbar
*/

/**
 * Class WP_Custom_Toolbar
 */
class WP_Custom_Toolbar {

	/**
	 * Properties
	 */
	private $menus = [
		'wpadminbar' => 'Entire Admin Bar (Overrides Boxes Below)',
		'wp-admin-bar-wp-logo' => 'Admin Bar WP Logo',
		'wp-admin-bar-site-name' => 'Site Name',
		'wp-admin-bar-my-account' => 'My Account',
		'wp-admin-bar-search' => 'Search',
		'menu-dashboard' => 'Admin Menu Dashboard',
	];

	/**
	 * Constructor
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_menu', [$this, 'wpct_add_admin_menu'] );
			add_action( 'admin_init', [$this, 'wpct_settings_init'] );
			add_action( 'admin_enqueue_scripts', [$this, 'wpct_hide_toolbars'] );
		} else {
			add_action( 'wp_enqueue_scripts', [$this, 'wpct_hide_toolbars'] );
		}
	}

	/**
	 * Add settings page
	 */
	function wpct_add_admin_menu(  ) { 
		add_options_page( __( 'WP Custom Toolbar', 'wp-custom-toolbar' ), __( 'WP Custom Toolbar', 'wp-custom-toolbar' ), 'manage_options', 'wp_custom_toolbar',  [$this, 'wpct_options_page'] );
	}

	/**
	 * Add forms and setting to settings page
	 */
	function wpct_settings_init(  ) { 
		register_setting( 'wp_custom_toolbar', 'wpct_settings' );
		$roles = get_editable_roles();
		$menus = $this->menus;
		foreach($roles as $rkey => $r) {
			add_settings_section(
				"wpct_pluginPage_section_{$rkey}", 
				__( $r['name'], 'wp-custom-toolbar' ), 
				[$this, 'wpct_settings_section_render'], 
				'wp_custom_toolbar'
			);
			foreach ($this->menus as $mkey => $m) {
				add_settings_field( 
					"wpct_checkbox_field_{$mkey}", 
					false, 
					[$this, 'wpct_menu_checkbox_render'],
					'wp_custom_toolbar', 
					"wpct_pluginPage_section_{$rkey}",
					[
						'rkey' => $rkey,
						'r' => $r,
						'mkey' => $mkey,
						'm' => $m,
					]
				);
			}
		}
	}

	/**
	 * Render checkbox on settings page based on args: role and menu
	 */
	function wpct_menu_checkbox_render ( $args ) {
		$settings = get_option( 'wpct_settings' );
		ob_start();
		include( plugin_dir_path(__FILE__)."/templates/tpl-checkbox-render.php" );
		echo ob_get_clean();
	}

	/**
	 * Render secion on settings page based on args: role
	 */
	function wpct_settings_section_render( $args ) {
		echo __("Select checkboxes below for any toolbar sections to hide for the {$args['title']} role.");
	}

	/**
	 * Render settings page layout
	 */
	function wpct_options_page() { 
		ob_start();
		include( plugin_dir_path(__FILE__)."/templates/tpl-admin-menu.php" );
		echo ob_get_clean();
	}

	/**
	 * Check settings, compare with current user role(s) and hide toolbar(s)
	 */
	function wpct_hide_toolbars() {
		$user = get_userdata( get_current_user_id() );
		if(!isset($user->roles)) {
			return;
		}
		$settings = get_option( 'wpct_settings' );
		$hide = $this->menus;
		foreach($user->roles as $rkey => $r) {
			foreach($this->menus as $mkey => $m) {
				if($settings[$r][$mkey] != 1) {
					unset($hide[$mkey]);
				}
			}
		}
		foreach(array_keys($hide) as $h) {
			wp_enqueue_style( "wpct-hide-{$h}", plugins_url( "/css/{$h}.css", __FILE__ ), [], microtime() );
		}
	}
}

global $wp_custom_toolbar;
$wp_custom_toolbar = new WP_Custom_Toolbar();
