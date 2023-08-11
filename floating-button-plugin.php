<?php
/**
 * Plugin Name: CTA Floating Button Plugin
 * plugin URI:https://github.com/alireza-sadi/floting-button-plugin
 * Description: Adds CTA floating button to your site.support email,phone,webpage.other ways on the way.
 * Version: 1.2
 * Author: Alireza Sadi
 * Author URI:https://github.com/alireza-sadi
 */
 
 // disallow execution out of context
if( ! function_exists('is_admin') ){
    return;
}

// Initialize text domain for translation
function floating_button_load_textdomain() {
    load_plugin_textdomain('floating-button-plugin', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'floating_button_load_textdomain');

// Enqueue CSS and JS files
function floating_button_enqueue_scripts() {
    wp_enqueue_style('floating-button-style', plugins_url('css/style.css', __FILE__));
    wp_enqueue_style('dashicons');
    wp_enqueue_script('floating-button-script', plugins_url('js/script.js', __FILE__), array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'floating_button_enqueue_scripts');

//setting link in plugins list page
add_filter( 'plugin_action_links_floating-button-plugin/floating-button-plugin.php', 'fb_settings_link' );
function fb_settings_link( $links ) {
	// Build and escape the URL.
	$url = esc_url( add_query_arg(
	    'page',
	    'floating-button-settings',
		'options-general.php'
        
	) );
	// Create the link.
	$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
	// Adds the link to the end of the array.
	array_push(
		$links,
		$settings_link
	);
	return $links;
}

// Add floating button HTML
function add_floating_button() {
    $options = get_option('floating_button_settings');
    $link_address = isset($options['link_address']) ? $options['link_address'] : '+1234567890';
    $color = isset($options['floating_button_color'])? $options['floating_button_color']:'green';
    $link_type = isset($options['link_type'])? $options['link_type']:'';
    if($options['link_type']=='tel:'){
    echo '<div id="floating-button" style="background-color:'.esc_attr($color).'"><a href="'.esc_attr($link_type).''.esc_attr($link_address).' "aria-label="call"><span class="dashicons dashicons-phone"></span></a></div>';
    }elseif($options['link_type']=='mailto:'){
    echo '<div id="floating-button" style="background-color:'.esc_attr($color).'"><a href="'.esc_attr($link_type).''.esc_attr($link_address).' "aria-label="call"><span class="dashicons dashicons-email-alt"></span></a></div>';
    }else{
    echo '<div id="floating-button" style="background-color:'.esc_attr($color).'"><a href="'.esc_attr($link_type).''.esc_attr($link_address).' "aria-label="call"><span class="dashicons dashicons-admin-site-alt3"></span></a></div>';
    };
}
add_action('wp_footer', 'add_floating_button');

// Add admin menu
function floating_button_add_admin_menu() {
    add_options_page(esc_html__('Floating Button Settings','floating-button-plugin'), esc_html__('Floating Button','floating-button-plugin'), 'manage_options', 'floating-button-settings', 'floating_button_settings_page');
}
add_action('admin_menu', 'floating_button_add_admin_menu');

// Options page rendering
function floating_button_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html__('Floating Button Settings','floating-button-plugin')?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('floating_button_settings_group'); ?>
            <?php do_settings_sections('floating-button-settings'); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Settings section and fields
function floating_button_settings_init() {
    register_setting('floating_button_settings_group', 'floating_button_settings');

    add_settings_section(
        'floating_button_settings_section',
        esc_html__('Button Appearance','floating-button-plugin'),
        'floating_button_settings_section_callback',
        'floating-button-settings'
    );
    
    add_settings_field(
        'link_type',
        esc_html__('Link Type','floating-button-plugin'),
        'link_type_render',
        'floating-button-settings',
        'floating_button_settings_section'
    );

    add_settings_field(
        'link_address',
        esc_html__('Link address','floating-button-plugin'),
        'link_address_render',
        'floating-button-settings',
        'floating_button_settings_section'
    );
    
    add_settings_field(
        'floting_button_color',
        esc_html__('Button color','floating-button-plugin'),
        'button_color_render',
        'floating-button-settings',
        'floating_button_settings_section'
    );
}
add_action('admin_init', 'floating_button_settings_init');

// Section callback
function floating_button_settings_section_callback() {
    echo esc_html__('Customize the appearance and behavior of the floating button.','floating-button-plugin');
    echo '</br>';
}


//field render function
function link_type_render() {
    $options = get_option('floating_button_settings');
    $link_type = isset($options['link_type']) ? $options['link_type'] : '';
    ?>
    <select name="floating_button_settings[link_type]">
        <option value="http://" <?php selected($link_type, 'http://'); ?>><?php _e('Web page','floating-button-plugin');?></option>
        <option value="tel:" <?php selected($link_type, 'tel:'); ?>><?php _e('Phone Number','floating-button-plugin');?></option>
        <option value="mailto:" <?php selected($link_type, 'mailto:'); ?>><?php _e('Email','floating-button-plugin');?></option>
        <!-- Add more options for other link types -->
    </select>
    <?php
}


function link_address_render() {
    $options = get_option('floating_button_settings');
    $link_address = isset($options['link_address']) ? $options['link_address'] : '';
    echo    "<input type='text' name='floating_button_settings[link_address]' value='$link_address' dir='ltr'style='margin-bottom:.5rem;' />";
    echo    "<br>";
    echo   esc_html__('You do not need to write any prefixes like (tel, mailto, http, etc.).','floating-button-plugin');
    echo    "<br>";
    echo   esc_html__('Just select the link type and enter your desired link.','floating-button-plugin');
}

function button_color_render() {
    $options = get_option('floating_button_settings');
    $color = isset($options['floating_button_color'])? $options['floating_button_color']:'';
    echo "<input type='color' name='floating_button_settings[floating_button_color]' value='$color'/>";
}



// Sanitize and validate input
function floating_button_settings_sanitize($input) {
    $output = array();
    if (isset($input['link_address'] )) {
        $output['link_address'] = sanitize_text_field($input['link_address']);
    }
    if (isset($input['floating_button_color'])) {
        $output['floating_button_color'] = sanitize_hex_color($input['floating_button_color']);
    }
    if (isset($input['link_type'])) {
        $output['link_type'] = sanitize_text_field($input['link_type']);
    }
    return $output;
}

add_filter('sanitize_option_floating_button_settings', 'floating_button_settings_sanitize');



// Display saved options
function floating_button_options() {
    $options = get_option('floating_button_settings');
    $link_address = isset($options['link_address']) ? $options['link_address'] : '+1234567890';
    $color = isset($options['floating_button_color'])? $options['floating_button_color']:'green';
    $link_type = isset($options['link_type'])? $options['link_type']:'';
    echo esc_html__('Link address:'.$link_address,'floating-button-plugin');
    echo esc_html__('Button color:'.$color,'floating-button-plugin');
    echo esc_html__('Link type:'.$link_type,'floating-button-plugin');
}
