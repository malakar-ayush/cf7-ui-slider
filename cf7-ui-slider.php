<?php
/*
 * Plugin Name:       Cf7  UI slider
 * Plugin URI:        https://www.ayushmalakar.com/
 * Description:       A simple way to add jQuery UI Slider to your contact form 7
 * Version:           2.5
 * Author:            Ayush Malakar
 * Author URI:        https://www.ayushmalakar.com/
 * Text Domain:       cf7-ui-slider
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );  // prevent direct access

if ( ! class_exists( 'CF7_UI_SLIDER' ) ) {
	class CF7_UI_SLIDER {
		/**
		 * Plugin version.
		 *
		 * @var string
		 */
		const VERSION = '2.5.0';

		/**
		 * Instance of this class.
		 *
		 * @var object
		 */

		private static $instance;

		public function __construct() {


			/**
			 * Check if Contact Form 7 is active
			 **/
			if ( class_exists( 'WPCF7' ) ) {
				if ( WPCF7_VERSION < 4.6 ) {
					add_action( 'admin_init', array( $this, 'cf7_update_contact_From' ) );
					add_action( 'admin_notices', array( $this, 'cf7_ui_slider_plugin_deactivate' ) );
				} else {
					require 'includes/slider-tag.php';
					require 'includes/range-slider-tag.php';

				}

			} else {

				add_action( 'admin_init', array( $this, 'cf7_ui_slider_cf7_missing_notice' ) );
				add_action( 'admin_init', array( $this, 'cf7_ui_slider_cf7_missing_notice' ) );

			}

		} // end of contruct

		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}


		public function cf7_ui_slider_cf7_missing_notice() {
			echo '<div class="error"><p>' . sprintf( __( '"UH OH!! Looks like you dont have %s Active!" Contact Form 7 ', ' cf7-ui-slider' ), '<a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">' . __( 'Contact Form 7', ' cf7-ui-slider' ) . '</a>' ) . '</p></div>';
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}

		public function cf7_update_contact_From() {
			echo '<div class="error"><p>' . sprintf( __( '"UH OH!! Looks like your current version of Contact form is   %i !" Please upgrade it to 4.6 or above ', ' cf7-ui-slider' ), WPCF7_VERSION ) . '</p></div>';
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}


		public function cf7_ui_slider_plugin_deactivate() {
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}


	}


}
add_action( 'plugins_loaded', array( 'CF7_UI_SLIDER', 'get_instance' ) );
add_action( 'plugins_loaded', array( 'SLIDER_UI_TAG', 'get_instance' ) );
add_action( 'plugins_loaded', array( 'Range_UI_Slider', 'get_instance' ) );


