<?php
/*
* Plugin Name: WP-SarahSpell
* Description: Adds support for Arabic Language Spell Checking
* Version:     1.0
* License:     GPLv3
* Author:      SarahSpell - The Arabic Spell Checker Team
* Author URI:  http://arabicspellchecker.com


Copyright (C) 2020 SarahSpell Team / Moutaz Al Khatib

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


class SpellerSettings {
	private $speller_settings_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'speller_settings_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'speller_settings_page_init' ) );
	}

	public function speller_settings_add_plugin_page() {
		add_options_page(
			'Arabic Spell Checking Settings', // page_title
			'Arabic Spell Checking Settings', // menu_title
			'manage_options', // capability
			'speller-settings', // menu_slug
			array( $this, 'speller_settings_create_admin_page' ) // function
		);
	}

	public function speller_settings_create_admin_page() {
		$this->speller_settings_options = get_option( 'speller_settings_option_name' ); ?>

		<div class="wrap">
			<h2>Arabic Speller Settings</h2>
			<p></p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'speller_settings_option_group' );
					do_settings_sections( 'speller-settings-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function speller_settings_page_init() {
		register_setting(
			'speller_settings_option_group', // option_group
			'speller_settings_option_name', // option_name
			array( $this, 'speller_settings_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'speller_settings_setting_section', // id
			'Enter your API key here', // title
			array( $this, 'speller_settings_section_info' ), // callback
			'speller-settings-admin' // page
		);

		add_settings_field(
			'license_key_0', // id
			'API Key', // title
			array( $this, 'license_key_0_callback' ), // callback
			'speller-settings-admin', // page
			'speller_settings_setting_section' // section
		);
	}

	public function speller_settings_sanitize($input) {
		$sanitary_values = array();
		
		// check if license key is valid		
		$remote_response = wp_remote_get( 'https://api.arabicspellchecker.com/ping', [
		  'headers' => [
				'token' => $input['license_key_0']
			]
		]);
		
		if( is_array($remote_response) ) {
			$header = $remote_response['headers']; // array of http header lines
			$body = $remote_response['body']; // use the content
		  
			if ( !empty( $input['license_key_0'] ) && $body == "pong") {
				$sanitary_values['license_key_0'] = sanitize_text_field( $input['license_key_0'] );
			} else {
				add_settings_error( 'speller-settings-admin', 'license_key_0', 'You have entered an invalid license key. Please contact info@arabicspellchecker.com' );
			}		  
		}
		




		return $sanitary_values;
	}

	public function speller_settings_section_info() {
		
	}

	public function license_key_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="speller_settings_option_name[license_key_0]" id="license_key_0" value="%s">',
			isset( $this->speller_settings_options['license_key_0'] ) ? esc_attr( $this->speller_settings_options['license_key_0']) : ''
		);
	}

}
if ( is_admin() )
	$speller_settings = new SpellerSettings();


add_action( "init", "sarahspell_addbuttons" );
add_filter( 'tiny_mce_before_init', 'sarahspell_format_TinyMCE' );


function sarahspell_activate() {
  if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
    include_once( ABSPATH . '/wp-admin/includes/plugin.php' );
  }
  
  if ( current_user_can( 'activate_plugins' ) && ! class_exists( 'Classic_Editor' ) ) {
    // Deactivate the plugin.
    deactivate_plugins( plugin_basename( __FILE__ ) );
    // Throw an error in the WordPress admin console.
    $error_message = '<p style="font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Oxygen-Sans,Ubuntu,Cantarell,\'Helvetica Neue\',sans-serif;font-size: 13px;line-height: 1.5;color:#444;">' . esc_html__( 'This plugin requires ', 'wp-sarahspell' ) . '<a href="' . esc_url( 'https://wordpress.org/plugins/classic-editor/' ) . '">Classic Editor</a>' . esc_html__( ' plugin to be active.', 'wp-sarahspell' ) . '</p>';
    die( $error_message ); // WPCS: XSS ok.
  }
}
register_activation_hook( __FILE__, 'sarahspell_activate' );

function sarahspell_addbuttons() {
	if( !current_user_can ( 'edit_posts' ) && !current_user_can ( 'edit_pages' ) ) {
		return;
	}
	if( get_user_option ( 'rich_editing' ) == 'true' ) {
		add_filter( "mce_external_plugins", "sarahspell_plugin" );
		add_filter( "mce_buttons", "sarahspell_buttons" );
	}
	
	wp_register_style( 'wp-rtl-icon-fix',  plugin_dir_url( __FILE__ ) . 'wp-rtl.css' );
	
}
function sarahspell_buttons($buttons) {
	wp_enqueue_style( 'wp-rtl-icon-fix' );
	return $buttons;
}



function sarahspell_plugin($plugin_array) {
	$plugin_array['spellchecker'] = plugins_url('js/plugin.min.js', __FILE__);
	return $plugin_array;
}


function sarahspell_format_TinyMCE( $in ) {
	$speller_settings_options = get_option( 'speller_settings_option_name' );
	$license_key_0 = $speller_settings_options['license_key_0'];

	$in['browser_spellcheck'] = false;
	$in['spellchecker_language'] = 'ar';
	$in['spellchecker_languages'] = 'Arabic=ar,English=en';
	$in['spellchecker_callback'] = 'function(method,text,success,failure){if(method==="spellcheck"){console.log(text.match(this.getWordCharPattern()));$.ajax({url:"https://api.arabicspellchecker.com/spellcheck",type:"POST",data:JSON.stringify({"text":text.match(this.getWordCharPattern()).join(" ")}),contentType:"application/json;charset=utf-8",dataType:"json",headers:{"token":"'.$license_key_0.'"},async:true,success:function(result){console.log(result);success({"words":result});},error:function(data,error,xhr){console.log(data); failure("Spellcheckerror:"+data.responseText);}})}else{failure("Unsupportedspellcheckmethod");}}';
	
	return $in;
}

?>
