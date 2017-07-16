<?php
/*
*		Plugin Name: WP GoToWebinar
*		Plugin URI: https://www.northernbeacheswebsites.com.au
*		Description: Show upcoming GoToWebinars on any post or page or in a widget and register users on your website. 
*		Version: 9.2
*		Author: Martin Gibson
*		Author URI:  https://www.northernbeacheswebsites.com.au
*		Text Domain: wp-gotowebinar   
*		Support: https://www.northernbeacheswebsites.com.au/contact
*		Licence: GPL2
*/
// Assign global variables
global $gotowebinar_is_pro;
$gotowebinar_is_pro = "NO";


function wpgotowebinar_plugin_get_version() {
	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$plugin_file = basename( ( __FILE__ ) );
	return $plugin_folder[$plugin_file]['Version'];
}


$options = array();
$time_zone_list = array("Pacific/Tongatapu"=>13, "Pacific/Fiji"=>12, "Pacific/Auckland"=>12, "Asia/Magadan"=>11, "Asia/Vladivostok"=>10, "Australia/Hobart"=>10, "Pacific/Guam"=>10, "Australia/Sydney"=>10, "Australia/Brisbane"=>10, "Australia/Darwin"=>9.5, "Australia/Adelaide"=>9.5, "Asia/Yakutsk"=>9, "Asia/Seoul"=>9, "Asia/Tokyo"=>9, "Asia/Taipei"=>8, "Australia/Perth"=>8, "Asia/Singapore"=>8, "Asia/Irkutsk"=>8, "Asia/Shanghai"=>8, "Asia/Krasnoyarsk"=>7, "Asia/Bangkok"=>7, "Asia/Jakarta"=>7, "Asia/Rangoon"=>6.5, "Asia/Colombo"=>6, "Asia/Dhaka"=>6, "Asia/Novosibirsk"=>6, "Asia/Katmandu"=>5.75, "Asia/Calcutta"=>5.5, "Asia/Karachi"=>5, "Asia/Yekaterinburg"=>5, "Asia/Kabul"=>4.5, "Asia/Tbilisi"=>4, "Asia/Muscat"=>4, "Asia/Tehran"=>3.5, "Africa/Nairobi"=>3, "Europe/Moscow"=>3, "Asia/Kuwait"=>3, "Asia/Baghdad"=>3, "Asia/Jerusalem"=>2, "Europe/Helsinki"=>2, "Africa/Harare"=>2, "Africa/Cairo"=>2, "Europe/Bucharest"=>2, "Europe/Athens"=>2, "Africa/Malabo"=>1, "Europe/Warsaw"=>1, "Europe/Brussels"=>1, "Europe/Prague"=>1, "Europe/Amsterdam"=>1, "GMT"=>0, "Europe/London"=>0, "Africa/Casablanca"=>0, "Atlantic/Cape_Verde"=>-1, "Atlantic/Azores"=>-1, "America/Buenos_Aires"=>-3, "America/Sao_Paulo"=>-3, "America/St_Johns"=>-3, "America/Santiago"=>-4, "America/Caracas"=>-4, "America/Halifax"=>-4, "America/Indianapolis"=>-5, "America/New_York"=>-5, "America/Bogota"=>-5, "America/Mexico_City"=>-6, "America/Chicago"=>-6, "America/Denver"=>-7, "America/Phoenix"=>-7, "America/Los_Angeles"=>-8, "America/Anchorage"=>-9, "Pacific/Honolulu"=>-10, "MIT"=>-11);


//disable updates if pro version
function wp_gotowebinar_disable_updates( $value ) {
    global $gotowebinar_is_pro;
    if(isset($value->response['wp-gotowebinar/wp-gotowebinar.php']) && $gotowebinar_is_pro == "YES"){        
        unset($value->response['wp-gotowebinar/wp-gotowebinar.php']);
    }
    return $value;
}
add_filter( 'site_transient_update_plugins', 'wp_gotowebinar_disable_updates' );

// Add a link to our plugin in the admin menu under Settings > GoToWebinar
function wp_gotowebinar_wp_menu() {
    global $gotowebinar_wp_settings_page;
    $gotowebinar_wp_settings_page = add_options_page(
        'WP GoToWebinar Options',
        'WP GoToWebinar',
        'manage_options',
        'wp-gotowebinar',
        'wp_gotowebinar_options_page'    
    );
}
add_action('admin_menu','wp_gotowebinar_wp_menu');


//add a settings link on the plugin page
function plugin_add_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=wp-gotowebinar">' . __( 'Settings' ) . '</a>';
    array_unshift( $links, $settings_link );
  	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'plugin_add_settings_link' );

//add custom links to plugin on plugins page
function wp_gotowebinar_custom_plugin_row_meta( $links, $file ) {
   if ( strpos( $file, 'wp-gotowebinar.php' ) !== false ) {
      $new_links = array(
               '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VGVE97KF74FVN" target="_blank">' . __('Donate') . '</a>',
               '<a href="https://northernbeacheswebsites.com.au/wp-gotowebinar-pro/" target="_blank">' . __('Pro Version') . '</a>',
               '<a href="http://wordpress.org/support/plugin/wp-gotowebinar" target="_blank">' . __('Support Forum') . '</a>',
            );
      $links = array_merge( $links, $new_links );
   }
   return $links;
}
add_filter( 'plugin_row_meta', 'wp_gotowebinar_custom_plugin_row_meta', 10, 2 );

// Create our main options page
function wp_gotowebinar_options_page(){
    if(!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permission to access this page.');
    }
    
    
//check if form has been submitted
global $options;
if( isset( $_POST['gotowebinar_settings_form_submitted'])){
    $hidden_field = esc_html($_POST['gotowebinar_settings_form_submitted']);
        if( $hidden_field == 'Y') {
            $gotowebinar_authorization = esc_html($_POST['gotowebinar_authorization']);
            $options['gotowebinar_authorization'] = $gotowebinar_authorization;
            $gotowebinar_organizer_key = esc_html($_POST['gotowebinar_organizer_key']);
            $options['gotowebinar_organizer_key'] = $gotowebinar_organizer_key;
            $gotowebinar_date_format = esc_html($_POST['gotowebinar_date_format']);
            $options['gotowebinar_date_format'] = $gotowebinar_date_format;
            $gotowebinar_time_format = esc_html($_POST['gotowebinar_time_format']);
            $options['gotowebinar_time_format'] = $gotowebinar_time_format;
            $gotowebinar_disable_tooltip = esc_html(isset($_POST['gotowebinar_disable_tooltip']));
            $options['gotowebinar_disable_tooltip'] = $gotowebinar_disable_tooltip;
            $gotowebinar_tooltip_text_color = esc_html($_POST['gotowebinar_tooltip_text_color']);
            $options['gotowebinar_tooltip_text_color'] = $gotowebinar_tooltip_text_color;           
            $gotowebinar_tooltip_background_color = esc_html($_POST['gotowebinar_tooltip_background_color']);
            $options['gotowebinar_tooltip_background_color'] = $gotowebinar_tooltip_background_color;
            $gotowebinar_tooltip_border_color = esc_html($_POST['gotowebinar_tooltip_border_color']);
            $options['gotowebinar_tooltip_border_color'] = $gotowebinar_tooltip_border_color;           
            $gotowebinar_icon_color = esc_html($_POST['gotowebinar_icon_color']);
            $options['gotowebinar_icon_color'] = $gotowebinar_icon_color; 
            $gotowebinar_custom_registration_page = esc_html($_POST['gotowebinar_custom_registration_page']);
            $options['gotowebinar_custom_registration_page'] = $gotowebinar_custom_registration_page;
            $gotowebinar_custom_thankyou_page = esc_html($_POST['gotowebinar_custom_thankyou_page']);
            $options['gotowebinar_custom_thankyou_page'] = $gotowebinar_custom_thankyou_page;
            $gotowebinar_button_text_color = esc_html($_POST['gotowebinar_button_text_color']);
            $options['gotowebinar_button_text_color'] = $gotowebinar_button_text_color;           
            $gotowebinar_button_background_color = esc_html($_POST['gotowebinar_button_background_color']);
            $options['gotowebinar_button_background_color'] = $gotowebinar_button_background_color;
            $gotowebinar_button_border_color = esc_html($_POST['gotowebinar_button_border_color']);
            $options['gotowebinar_button_border_color'] = $gotowebinar_button_border_color;  
            //start cache option
            $gotowebinar_disable_cache = esc_html(isset($_POST['gotowebinar_disable_cache']));
            $options['gotowebinar_disable_cache'] = $gotowebinar_disable_cache;
            //start enable timezone converstion option
            $gotowebinar_enable_timezone_conversion = esc_html(isset($_POST['gotowebinar_enable_timezone_conversion']));
            $options['gotowebinar_enable_timezone_conversion'] = $gotowebinar_enable_timezone_conversion;
            $gotowebinar_timezone_error_message = esc_html(stripslashes($_POST['gotowebinar_timezone_error_message']));
            $options['gotowebinar_timezone_error_message'] = $gotowebinar_timezone_error_message;
            //start translate options
            $gotowebinar_translate_firstName = esc_html(stripslashes($_POST['gotowebinar_translate_firstName']));
            $options['gotowebinar_translate_firstName'] = $gotowebinar_translate_firstName;
            $gotowebinar_translate_lastName = esc_html(stripslashes($_POST['gotowebinar_translate_lastName']));
            $options['gotowebinar_translate_lastName'] = $gotowebinar_translate_lastName;
            $gotowebinar_translate_email = esc_html(stripslashes($_POST['gotowebinar_translate_email']));
            $options['gotowebinar_translate_email'] = $gotowebinar_translate_email;
            $gotowebinar_translate_address = esc_html(stripslashes($_POST['gotowebinar_translate_address']));
            $options['gotowebinar_translate_address'] = $gotowebinar_translate_address;
            $gotowebinar_translate_city = esc_html(stripslashes($_POST['gotowebinar_translate_city']));
            $options['gotowebinar_translate_city'] = $gotowebinar_translate_city;
            $gotowebinar_translate_state = esc_html(stripslashes($_POST['gotowebinar_translate_state']));
            $options['gotowebinar_translate_state'] = $gotowebinar_translate_state;
            $gotowebinar_translate_zipCode = esc_html(stripslashes($_POST['gotowebinar_translate_zipCode']));
            $options['gotowebinar_translate_zipCode'] = $gotowebinar_translate_zipCode;
            $gotowebinar_translate_country = esc_html(stripslashes($_POST['gotowebinar_translate_country']));
            $options['gotowebinar_translate_country'] = $gotowebinar_translate_country;
            $gotowebinar_translate_phone = esc_html(stripslashes($_POST['gotowebinar_translate_phone']));
            $options['gotowebinar_translate_phone'] = $gotowebinar_translate_phone;
            $gotowebinar_translate_organization = esc_html(stripslashes($_POST['gotowebinar_translate_organization']));
            $options['gotowebinar_translate_organization'] = $gotowebinar_translate_organization;
            $gotowebinar_translate_jobTitle = esc_html(stripslashes($_POST['gotowebinar_translate_jobTitle']));
            $options['gotowebinar_translate_jobTitle'] = $gotowebinar_translate_jobTitle;
            $gotowebinar_translate_questionsAndComments = esc_html(stripslashes($_POST['gotowebinar_translate_questionsAndComments']));
            $options['gotowebinar_translate_questionsAndComments'] = $gotowebinar_translate_questionsAndComments;
            $gotowebinar_translate_industry = esc_html(stripslashes($_POST['gotowebinar_translate_industry']));
            $options['gotowebinar_translate_industry'] = $gotowebinar_translate_industry;
            $gotowebinar_translate_numberOfEmployees = esc_html(stripslashes($_POST['gotowebinar_translate_numberOfEmployees']));
            $options['gotowebinar_translate_numberOfEmployees'] = $gotowebinar_translate_numberOfEmployees;
            $gotowebinar_translate_purchasingTimeFrame = esc_html(stripslashes($_POST['gotowebinar_translate_purchasingTimeFrame']));
            $options['gotowebinar_translate_purchasingTimeFrame'] = $gotowebinar_translate_purchasingTimeFrame;
            $gotowebinar_translate_purchasingRole = esc_html(stripslashes($_POST['gotowebinar_translate_purchasingRole']));
            $options['gotowebinar_translate_purchasingRole'] = $gotowebinar_translate_purchasingRole;
            $gotowebinar_translate_submitButton = esc_html(stripslashes($_POST['gotowebinar_translate_submitButton']));
            $options['gotowebinar_translate_submitButton'] = $gotowebinar_translate_submitButton;
            $gotowebinar_translate_successMessage = esc_html(stripslashes($_POST['gotowebinar_translate_successMessage']));
            $options['gotowebinar_translate_successMessage'] = $gotowebinar_translate_successMessage;
            $gotowebinar_translate_alreadyRegisteredMessage = esc_html(stripslashes($_POST['gotowebinar_translate_alreadyRegisteredMessage']));
            $options['gotowebinar_translate_alreadyRegisteredMessage'] = $gotowebinar_translate_alreadyRegisteredMessage;
            $gotowebinar_translate_errorMessage = esc_html(stripslashes($_POST['gotowebinar_translate_errorMessage']));
            $options['gotowebinar_translate_errorMessage'] = $gotowebinar_translate_errorMessage;
            $gotowebinar_translate_attendeeLimit = esc_html(stripslashes($_POST['gotowebinar_translate_attendeeLimit']));
            $options['gotowebinar_translate_attendeeLimit'] = $gotowebinar_translate_attendeeLimit;
            $gotowebinar_translate_required = esc_html(stripslashes($_POST['gotowebinar_translate_required']));
            $options['gotowebinar_translate_required'] = $gotowebinar_translate_required;

            
            
            
            //pro options
            $gotowebinar_mailchimp_api = esc_html($_POST['gotowebinar_mailchimp_api']);
            $options['gotowebinar_mailchimp_api'] = $gotowebinar_mailchimp_api;              
            $gotowebinar_mailchimp_default_list = esc_html($_POST['gotowebinar_mailchimp_default_list']);
            $options['gotowebinar_mailchimp_default_list'] = $gotowebinar_mailchimp_default_list;  
            $gotowebinar_email_service = esc_html($_POST['gotowebinar_email_service']);
            $options['gotowebinar_email_service'] = $gotowebinar_email_service; 
            $gotowebinar_emailservice_opt_in = esc_html(isset($_POST['gotowebinar_emailservice_opt_in']));
            $options['gotowebinar_emailservice_opt_in'] = $gotowebinar_emailservice_opt_in;
            $gotowebinar_constantcontact_token = esc_html($_POST['gotowebinar_constantcontact_token']);
            $options['gotowebinar_constantcontact_token'] = $gotowebinar_constantcontact_token;
            $gotowebinar_constantcontact_default_list = esc_html($_POST['gotowebinar_constantcontact_default_list']);
            $options['gotowebinar_constantcontact_default_list'] = $gotowebinar_constantcontact_default_list; 
            $gotowebinar_mailchimp_subscribe_if = esc_html($_POST['gotowebinar_mailchimp_subscribe_if']);
            $options['gotowebinar_mailchimp_subscribe_if'] = $gotowebinar_mailchimp_subscribe_if;  
            
            
            $gotowebinar_activecampaign_api = esc_html($_POST['gotowebinar_activecampaign_api']);
            $options['gotowebinar_activecampaign_api'] = $gotowebinar_activecampaign_api;  
            
            $gotowebinar_activecampaign_account = esc_html($_POST['gotowebinar_activecampaign_account']);
            $options['gotowebinar_activecampaign_account'] = $gotowebinar_activecampaign_account;  
            
            
            $gotowebinar_activecampaign_default_list = esc_html($_POST['gotowebinar_activecampaign_default_list']);
            $options['gotowebinar_activecampaign_default_list'] = $gotowebinar_activecampaign_default_list; 
            
            
            
            //dismiss admin welcome note
            $gotowebinar_welcome_message = esc_html(isset($_POST['gotowebinar_welcome_message']));
            $options['gotowebinar_welcome_message'] = $gotowebinar_welcome_message;
            $gotowebinar_recaptcha_site_key = esc_html($_POST['gotowebinar_recaptcha_site_key']);
            $options['gotowebinar_recaptcha_site_key'] = $gotowebinar_recaptcha_site_key;
            $gotowebinar_translate_cancelledWebinar = esc_html($_POST['gotowebinar_translate_cancelledWebinar']);
            $options['gotowebinar_translate_cancelledWebinar'] = $gotowebinar_translate_cancelledWebinar;
            $gotowebinar_disable_autodrafting = esc_html(isset($_POST['gotowebinar_disable_autodrafting']));
            $options['gotowebinar_disable_autodrafting'] = $gotowebinar_disable_autodrafting;
            update_option('gotowebinar_settings', $options);
            
        }
}
    
    
    $options = get_option('gotowebinar_settings');
    if($options != ''){
        $gotowebinar_authorization = $options['gotowebinar_authorization'];
        $gotowebinar_organizer_key = $options['gotowebinar_organizer_key'];
        $gotowebinar_date_format = $options['gotowebinar_date_format'];
        $gotowebinar_time_format = $options['gotowebinar_time_format'];
        $gotowebinar_disable_tooltip = $options['gotowebinar_disable_tooltip'];
        $gotowebinar_tooltip_text_color = $options['gotowebinar_tooltip_text_color'];
        $gotowebinar_tooltip_background_color = $options['gotowebinar_tooltip_background_color'];
        $gotowebinar_tooltip_border_color = $options['gotowebinar_tooltip_border_color'];
        $gotowebinar_icon_color = $options['gotowebinar_icon_color'];
        $gotowebinar_custom_registration_page = $options['gotowebinar_custom_registration_page'];
        $gotowebinar_custom_thankyou_page = $options['gotowebinar_custom_thankyou_page'];
        $gotowebinar_button_text_color = $options['gotowebinar_button_text_color'];
        $gotowebinar_button_background_color = $options['gotowebinar_button_background_color'];
        $gotowebinar_button_border_color = $options['gotowebinar_button_border_color'];
        $gotowebinar_disable_cache = $options['gotowebinar_disable_cache'];
        $gotowebinar_enable_timezone_conversion = $options['gotowebinar_enable_timezone_conversion'];
        $gotowebinar_timezone_error_message = $options['gotowebinar_timezone_error_message'];
        $gotowebinar_translate_firstName = $options['gotowebinar_translate_firstName'];
        $gotowebinar_translate_lastName = $options['gotowebinar_translate_lastName'];
        $gotowebinar_translate_email = $options['gotowebinar_translate_email'];
        $gotowebinar_translate_address = $options['gotowebinar_translate_address'];
        $gotowebinar_translate_city = $options['gotowebinar_translate_city'];
        $gotowebinar_translate_state = $options['gotowebinar_translate_state'];
        $gotowebinar_translate_zipCode = $options['gotowebinar_translate_zipCode'];
        $gotowebinar_translate_country = $options['gotowebinar_translate_country'];
        $gotowebinar_translate_phone = $options['gotowebinar_translate_phone'];
        $gotowebinar_translate_organization = $options['gotowebinar_translate_organization'];
        $gotowebinar_translate_jobTitle = $options['gotowebinar_translate_jobTitle'];
        $gotowebinar_translate_questionsAndComments = $options['gotowebinar_translate_questionsAndComments'];
        $gotowebinar_translate_industry = $options['gotowebinar_translate_industry'];
        $gotowebinar_translate_numberOfEmployees = $options['gotowebinar_translate_numberOfEmployees'];
        $gotowebinar_translate_purchasingTimeFrame = $options['gotowebinar_translate_purchasingTimeFrame'];
        $gotowebinar_translate_purchasingRole = $options['gotowebinar_translate_purchasingRole'];
        $gotowebinar_translate_submitButton = $options['gotowebinar_translate_submitButton'];
        $gotowebinar_translate_successMessage = $options['gotowebinar_translate_successMessage'];
        $gotowebinar_translate_alreadyRegisteredMessage = $options['gotowebinar_translate_alreadyRegisteredMessage'];
        $gotowebinar_translate_errorMessage = $options['gotowebinar_translate_errorMessage'];
        $gotowebinar_mailchimp_api = $options['gotowebinar_mailchimp_api'];
        $gotowebinar_mailchimp_default_list = $options['gotowebinar_mailchimp_default_list'];
        $gotowebinar_email_service = $options['gotowebinar_email_service'];
        $gotowebinar_emailservice_opt_in = $options['gotowebinar_emailservice_opt_in'];
        $gotowebinar_constantcontact_token = $options['gotowebinar_constantcontact_token'];
        $gotowebinar_constantcontact_default_list = $options['gotowebinar_constantcontact_default_list'];
        $gotowebinar_mailchimp_subscribe_if = $options['gotowebinar_mailchimp_subscribe_if'];
        $gotowebinar_welcome_message = $options['gotowebinar_welcome_message'];
        $gotowebinar_translate_cancelledWebinar = $options['gotowebinar_translate_cancelledWebinar'];
        $gotowebinar_recaptcha_site_key = $options['gotowebinar_recaptcha_site_key'];
        $gotowebinar_disable_autodrafting = $options['gotowebinar_disable_autodrafting'];
        $gotowebinar_translate_attendeeLimit = $options['gotowebinar_translate_attendeeLimit'];
        $gotowebinar_activecampaign_api = $options['gotowebinar_activecampaign_api'];
        $gotowebinar_activecampaign_default_list = $options['gotowebinar_activecampaign_default_list'];
        $gotowebinar_activecampaign_account = $options['gotowebinar_activecampaign_account'];
        $gotowebinar_translate_required = $options['gotowebinar_translate_required'];
    
        
    }
    require('inc/options-page-wrapper.php');
}


//get translations from plugin folder
add_action('plugins_loaded', 'wp_gotowebinar_translations');
function wp_gotowebinar_translations() {
	load_plugin_textdomain( 'wp-gotowebinar', false, dirname( plugin_basename(__FILE__) ) . '/inc/lang/' );
}


//common function to get upcoming webinars
function wp_gotowebinar_upcoming_webinars($transientName, $transientDuration){
    //get options
    $options = get_option('gotowebinar_settings');
    //get transient
    $getTransient = get_transient($transientName);
    //if transient doesn't exist or caching disabled do this
    if ($getTransient != false && $options['gotowebinar_disable_cache'] == 0){
        $jsondata = $getTransient; 
        $json_response = 200;
        return array($jsondata,$json_response);
    } //otherwise do this 
    else { 
        $json_feed = wp_remote_get( 'https://api.getgo.com/G2W/rest/organizers/'.$options['gotowebinar_organizer_key'].'/upcomingWebinars', array(
        'headers' => array(
        'Authorization' => $options['gotowebinar_authorization'],
	    ),));
        //store the data and response in a variable
        $jsondata = json_decode(preg_replace('/("\w+"):(\d+(\.\d+)?)/', '\\1:"\\2"', wp_remote_retrieve_body( $json_feed )), true);
        $json_response = wp_remote_retrieve_response_code($json_feed);    
        //if response is successful set the transient    
        if($json_response == 200){    
        set_transient($transientName,$jsondata, 86400);  
        }   
        //return the data and response
        return array($jsondata,$json_response);
    } //end else  
} //end function


//function to check authentication status
function wp_gotowebinar_authentication_check(){
    //get options
    $options = get_option('gotowebinar_settings');
        $json_feed = wp_remote_get( 'https://api.getgo.com/G2W/rest/organizers/'.$options['gotowebinar_organizer_key'].'/upcomingWebinars', array(
        'headers' => array(
        'Authorization' => $options['gotowebinar_authorization'],
	    ),));
        //store the data and response in a variable
        $json_response = wp_remote_retrieve_response_code($json_feed);    
        //return the data and response
        return $json_response;
} //end function


// Add shortcode
if(file_exists(get_stylesheet_directory().'/wp-gotowebinar/shortcode.php')) {
require(get_stylesheet_directory().'/wp-gotowebinar/shortcode.php');      
} else {
require('inc/shortcode.php');    
}

// Add registration shortcode
if(file_exists(get_stylesheet_directory().'/wp-gotowebinar/shortcode-registration.php')) {
require(get_stylesheet_directory().'/wp-gotowebinar/shortcode-registration.php');      
} else {
require('inc/shortcode-registration.php');   
}

// Add calendar shortcode
if(file_exists(get_stylesheet_directory().'/wp-gotowebinar/shortcode-calendar.php')) {
require(get_stylesheet_directory().'/wp-gotowebinar/shortcode-calendar.php');      
} else {
require('inc/shortcode-calendar.php');   
}

// Add widget
require('inc/widget.php');


// Load front end style and scripts
function wp_gotowebinar_register_frontend()
{ 
    $options = get_option('gotowebinar_settings');
    wp_register_style( 'full-calendar-style', plugins_url('/inc/external/fullcalendar.min.css', __FILE__ ) );
    wp_register_style( 'font-awesome-icons', plugins_url('/inc/external/font-awesome.min.css', __FILE__ ) );
    wp_register_style( 'custom-style', plugins_url( '/inc/style.css', __FILE__ ));
    wp_register_script( 'moment', plugins_url('/inc/external/moment.js', __FILE__ ), array( 'jquery' )); 
    wp_register_script( 'moment-timezone', plugins_url('/inc/external/moment-timezone-with-data.js', __FILE__ ), array( 'jquery' ));
    wp_register_script( 'full-calendar', plugins_url('/inc/external/fullcalendar.min.js', __FILE__ ), array( 'jquery' ));
    wp_register_script( 'full-calendar-locale', plugins_url('/inc/external/locale-all.js', __FILE__ ), array( 'jquery' ));
    wp_register_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js'); 
    wp_enqueue_script('jquery-ui', plugins_url('/inc/external/jquery-ui.min.js', __FILE__ ), array( 'jquery'), '1.12.1');
    wp_register_script( 'custom-script', plugins_url( '/inc/script.js', __FILE__ ), array( 'jquery' ),1.0,true);
    wp_localize_script( 'custom-script', 'mailchimp_subscribe', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    wp_localize_script( 'custom-script', 'constantcontact_subscribe', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    wp_localize_script( 'custom-script', 'activecampaign_subscribe', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    wp_localize_script( 'custom-script', 'registration_form_submit', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    wp_enqueue_script( array('moment','moment-timezone','full-calendar','full-calendar-locale','custom-script','google-recaptcha'));
    wp_enqueue_style( array('font-awesome-icons','full-calendar-style','custom-style') );   
    if($options['gotowebinar_button_background_color'] == "#ffffff"){
    $spinnerColor = $options['gotowebinar_button_text_color'];
    } else {
     $spinnerColor = $options['gotowebinar_button_background_color'];   
    }
    $colour_options = "
    .tooltip {
	background-color: {$options['gotowebinar_tooltip_background_color']};
	color: {$options['gotowebinar_tooltip_text_color']};
    border-color: {$options['gotowebinar_tooltip_border_color']};
    }
    .webinar-registration input[type=\"submit\"] {
    background-color: {$options['gotowebinar_button_background_color']};
	color: {$options['gotowebinar_button_text_color']};
    border-color: {$options['gotowebinar_button_border_color']};
    }
    .webinar-registration .fa-spinner {
    color: {$spinnerColor};
    }
    .upcoming-webinars fa, .upcoming-webinars a, .upcoming-webinars-widget fa, .upcoming-webinars-widget a, .webinar-registration a {
    color: {$options['gotowebinar_icon_color']};
    } 
    ";
    wp_add_inline_style( 'custom-style', $colour_options );
}
    add_action( 'wp_enqueue_scripts', 'wp_gotowebinar_register_frontend' );



// Load admin style and scripts
function wp_gotowebinar_register_admin($hook)
{
    wp_enqueue_style( 'visual-composer-style', plugins_url( '/inc/vc-adminstyle.css', __FILE__ ));
    global $gotowebinar_wp_settings_page;
    
    global $post;
    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
        if ( 'product' === $post->post_type ) {     
            wp_enqueue_script( 'custom-admin-script-pro', plugins_url( '/inc/pro/adminscriptpro.js', __FILE__ ), array( 'jquery'));
        }
    }
    
    if($hook != $gotowebinar_wp_settings_page)
        return;
    
    wp_enqueue_script('time-picker', plugins_url('/inc/external/jquery.timepicker.min.js', __FILE__ ), array('jquery'));
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'custom-admin-script', plugins_url( '/inc/adminscript.js', __FILE__ ), array( 'jquery','wp-color-picker' ));
    wp_enqueue_script('jquery-ui', plugins_url('/inc/external/jquery-ui.min.js', __FILE__ ), array( 'jquery'), '1.12.1');
    wp_enqueue_script('jquery-effects-shake');
    wp_enqueue_style( 'custom-admin-style', plugins_url( '/inc/adminstyle.css', __FILE__ ));
    wp_register_style( 'font-awesome-icons', plugins_url('/inc/external/font-awesome.min.css', __FILE__ ) );
    wp_register_style( 'time-picker-style', plugins_url('/inc/external/jquery.timepicker.min.css', __FILE__ ) );
    wp_enqueue_style( array('font-awesome-icons','time-picker-style') );
}
add_action( 'admin_enqueue_scripts', 'wp_gotowebinar_register_admin' );


// Include pro functions
if ($gotowebinar_is_pro == "YES"){ 
include('inc/pro/pro.php');
} 
//clear cache and deactivation tasks
require('inc/clear-cache.php');
// add visual composer functionality
require('inc/visual-composer.php');
// add registration function
require('inc/registration.php');

//function to save dismiss welcome notice

function wp_gotowebinar_disable_welcome_message_callback() {

$gotowebinar_options = get_option('gotowebinar_settings');
$gotowebinar_options['gotowebinar_welcome_message'] = 1;   
     
update_option('gotowebinar_settings', $gotowebinar_options);        
wp_die(); 
    
}
add_action( 'wp_ajax_disable_welcome_message', 'wp_gotowebinar_disable_welcome_message_callback' );



?>