<?php

/**
 * Plugin Name:       Quick C Woocommerce Checkout
 * Plugin URI:        https://noderavel.com/
 * Description:       Connects with the SolarEdge scrape
 * Version:           1.0.0
 * Requires at least: 5.5
 * Requires PHP:      7.2
 * Author:            Muzammil Ahmed
 * Author URI:        https://noderavel.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: quick-c-woocommerce-checkout-plugin
 *
 * Plugin Icon: assets/images/icon-logo.png
 */

/**
 * Direct access protection
 */
defined('ABSPATH') or die('This path is not accessable');

require_once plugin_dir_path(__FILE__) . 'API.php';
global $platform;
$platform = 'WP';

/**
 * Include js and css files
 */
function QCWC_includes_resources()
{
    $plugin_url = plugin_dir_url(__FILE__);
    //plugin styles
    wp_enqueue_style('QCWC-styles', plugins_url('assets/css/styles.css', __FILE__));
    wp_enqueue_style('poppins-font', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
    wp_enqueue_style('fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css', array(), '6.5.0');

    //plugin scripts
    wp_enqueue_script('QCWC-script', plugins_url('assets/js/script.js', __FILE__), array(), '1.1.0', true);

    wp_localize_script('QCWC-script', 'MyPlugin', array(
        'sunIconUrl' => plugins_url('assets/images/sun.png', __FILE__),
        'midNightIconUrl' => plugins_url('assets/images/mid-night.png', __FILE__),
        'afterNoonIconUrl' => plugins_url('assets/images/afternoon.png', __FILE__),
        'eveningIconUrl' => plugins_url('assets/images/evening.png', __FILE__)
    ));

    wp_localize_script('QCWC-script', 'ajaxurl', admin_url('admin-ajax.php'));
}
add_action('wp_enqueue_scripts', 'QCWC_includes_resources');

function QCWC_includes_admin_resources()
{
    //plugin admin script
    wp_enqueue_script('QCWC-admin-js', plugins_url('assets/js/admin.js', __FILE__), array(), '1.0.0', true);

    // //include ajax vars
    // $nonce_val = wp_create_nonce('ajax_check');
    // $js_object = array(
    // 	'ajax_url' 		=> admin_url( 'admin-ajax.php' ),
    // 	'nonce'    		=> $nonce_val,
    // );

    // wp_localize_script( 'QCWC-admin-js', 'QCWC_ajax_obj', $js_object);
}

add_action('admin_enqueue_scripts', 'QCWC_includes_admin_resources');


/**
 * Global Variables
 */
// $GLOBALS['json_file_path'] = ABSPATH . 'wp-content/plugins/pollen-forcast-chart-ambee/data/data.json';
// $GLOBALS['api_key']  = '{KEY}';


add_action('init', 'start_session', 1);
function start_session()
{
    if (!session_id()) {
        session_start();
    }
}

function qcwc_register_settings()
{
    register_setting('qcwc_settings_group', 'qcwc_enable_short_address');
    register_setting('qcwc_settings_group', 'qcwc_enable_region');
    register_setting('qcwc_settings_group', 'qcwc_enable_unit_number');
    register_setting('qcwc_settings_group', 'qcwc_enable_building_number');
    register_setting('qcwc_settings_group', 'qcwc_enable_street_name');
    register_setting('qcwc_settings_group', 'qcwc_enable_district');
    register_setting('qcwc_settings_group', 'qcwc_enable_lat');
    register_setting('qcwc_settings_group', 'qcwc_enable_long');

    add_settings_section(
        'qcwc_settings_section',
        'Billing Fields',
        null,
        'qcwc_settings_page'
    );

    // Add individual fields
    add_settings_field(
        'qcwc_enable_short_address',
        'Enable Short Address',
        'qcwc_display_checkbox',
        'qcwc_settings_page',
        'qcwc_settings_section',
        array('field' => 'qcwc_enable_short_address')
    );

    add_settings_field('qcwc_enable_region', 'Enable Region', 'qcwc_display_checkbox', 'qcwc_settings_page', 'qcwc_settings_section', array('field' => 'qcwc_enable_region'));
    add_settings_field('qcwc_enable_unit_number', 'Enable Unit Number', 'qcwc_display_checkbox', 'qcwc_settings_page', 'qcwc_settings_section', array('field' => 'qcwc_enable_unit_number'));
    add_settings_field('qcwc_enable_building_number', 'Enable Building Number', 'qcwc_display_checkbox', 'qcwc_settings_page', 'qcwc_settings_section', array('field' => 'qcwc_enable_building_number'));
    add_settings_field('qcwc_enable_street_name', 'Enable Street Name', 'qcwc_display_checkbox', 'qcwc_settings_page', 'qcwc_settings_section', array('field' => 'qcwc_enable_street_name'));
    add_settings_field('qcwc_enable_district', 'Enable District', 'qcwc_display_checkbox', 'qcwc_settings_page', 'qcwc_settings_section', array('field' => 'qcwc_enable_district'));
    add_settings_field('qcwc_enable_lat', 'Enable Latitude', 'qcwc_display_checkbox', 'qcwc_settings_page', 'qcwc_settings_section', array('field' => 'qcwc_enable_lat'));
    add_settings_field('qcwc_enable_long', 'Enable Longitude', 'qcwc_display_checkbox', 'qcwc_settings_page', 'qcwc_settings_section', array('field' => 'qcwc_enable_long'));
}

add_action('admin_init', 'qcwc_register_settings');

function qcwc_display_checkbox($args)
{
    $field = $args['field'];
    $value = get_option($field) ? 'checked' : '';
    echo "<input type='checkbox' name='$field' value='1' $value>";
}

add_action('admin_menu', 'qcwc_add_admin_menu');

function qcwc_add_admin_menu()
{
    add_menu_page(
        'Quick C Admin Tab',
        'Quick C',
        'manage_options',
        'qcwc-admin-tab',
        'qcwc-settings',
        'dashicons-admin-generic',
        50
    );

    add_submenu_page(
        'qcwc-admin-tab',
        'Settings Page',
        'Settings',
        'manage_options',
        'qcwc-settings',
        'qcwc_settings_page_content'
    );

    add_action('admin_init', 'qcwc_redirect_main_tab_to_settings');
}

function qcwc_settings_page_content()
{
    ?>
    <div class="wrap">
        <h1>Quick C Settings</h1>
        <?php
        settings_errors();
        ?>
        <form method="post" action="options.php">
            <?php
            settings_fields('qcwc_settings_group');
            do_settings_sections('qcwc_settings_page');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}


function qcwc_redirect_main_tab_to_settings()
{
    global $pagenow;

    if (isset($_GET['page']) && $_GET['page'] === 'qcwc-admin-tab') {
        wp_redirect(admin_url('admin.php?page=qcwc-settings'));
        exit;
    }
}

add_action('admin_head', 'qcwc_hide_parent_submenu_item');

function qcwc_hide_parent_submenu_item()
{
    echo '<style>
        #toplevel_page_qcwc-admin-tab .wp-submenu li.wp-first-item {
            display: none;
        }
    </style>';
}

// Function to register custom order statuses
function my_custom_register_order_statuses()
{
    register_post_status('wc-created', array(
        'label' => _x('Created', 'Order status', 'quick-c-woocommerce-checkout-plugin'),
        'public' => true,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'show_in_invoice' => true,
        'label_count' => _n_noop('Created (%s)', 'Created (%s)', 'quick-c-woocommerce-checkout-plugin'),
    ));

    register_post_status('wc-transit', array(
        'label' => _x('In Transit', 'Order status', 'quick-c-woocommerce-checkout-plugin'),
        'public' => true,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'show_in_invoice' => true,
        'label_count' => _n_noop('In Transit (%s)', 'In Transit (%s)', 'quick-c-woocommerce-checkout-plugin'),
    ));

    register_post_status('wc-delivered', array(
        'label' => _x('Delivered', 'Order status', 'quick-c-woocommerce-checkout-plugin'),
        'public' => true,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'show_in_invoice' => true,
        'label_count' => _n_noop('Delivered (%s)', 'Delivered (%s)', 'quick-c-woocommerce-checkout-plugin'),
    ));
}


register_activation_hook(__FILE__, function () {
    QCWC_woocommerce_plugin_activate();
    my_custom_register_order_statuses();
});

function QCWC_woocommerce_plugin_activate()
{

    $domain = $_SERVER['HTTP_HOST'];
    $root_domain = get_root_domain($domain);

    global $platform;
    $description = "An example wordpress platform for demonstration.";
    $ip_address = $_SERVER['REMOTE_ADDR'];

    $api_handler = new API_Handler('https://quick-c.devsy.tech/api/v1/platform/register/');
    $response = $api_handler->registerPlatForm($platform, $root_domain, $description, $ip_address);
}

function get_root_domain($domain)
{
    $parts = explode('.', $domain);
    $num_parts = count($parts);

    if ($num_parts > 2) {
        return $parts[$num_parts - 2] . '.' . $parts[$num_parts - 1];
    }

    return $domain;
}

function my_custom_add_order_statuses($order_statuses)
{
    $order_statuses['wc-created'] = _x('Created', 'Order status', 'my-custom-plugin');
    $order_statuses['wc-transit'] = _x('In Transit', 'Order status', 'my-custom-plugin');
    $order_statuses['wc-delivered'] = _x('Delivered', 'Order status', 'my-custom-plugin');
    return $order_statuses;
}

add_action('init', 'my_custom_register_order_statuses');
add_filter('wc_order_statuses', 'my_custom_add_order_statuses');

function get_quick_c_user_token_from_session()
{
    if (isset($_SESSION['quick_c_user_token']) && !empty($_SESSION['quick_c_user_token'])) {
        return $_SESSION['quick_c_user_token'];
    } else {
        return '';
    }
}

function remove_auth_token_from_session()
{
    if (isset($_SESSION['quick_c_user_token'])) {
        unset($_SESSION['quick_c_user_token']);
    }
}



// Check is User Logged in and is Checkout so script pass token and user email
function QCWC_custom_checkout_popup()
{
    if (is_checkout() || is_cart() || has_shortcode(get_post()->post_content, 'qcwc_checkout_module')) {
        $user_id = get_current_user_id();
        if ($user_id) {
            $user_token = get_user_meta($user_id, 'quick-c-user-api-key', true);
        }

        wp_localize_script('QCWC-script', 'popupData', array(
            'isTokenEmpty' => isset($user_id) ? empty($user_token) : "1",
            // 'userEmail' => wp_get_current_user()->user_email,
            'userEmail' => isset($user_id) ? wp_get_current_user()->user_email : "",
            // 'userEmail' => "admin@divistack.com",
            'quick_c_checkout' => isset($_GET['quick-c-checkout']) && $_GET['quick-c-checkout'] === 'true',
            'is_open' => is_checkout() ? true : false
        ));
    }
}
add_action('wp_enqueue_scripts', 'QCWC_custom_checkout_popup');



function add_custom_button_checkout()
{
    if (is_user_logged_in() && (isset($_GET['quick-c-checkout']) && $_GET['quick-c-checkout'] === 'true')) {
        echo '<div class="edit-address-container">';
        echo '<button id="edit-address-button" type="button" class="button">Edit Address</button>';
        echo '</div>';
    }
}
add_action('woocommerce_before_checkout_billing_form', 'add_custom_button_checkout', 10);

add_action('template_redirect', 'redirect_if_no_token_on_checkout');

function redirect_if_no_token_on_checkout()
{
    if (is_checkout() && isset($_GET['quick-c-checkout']) && $_GET['quick-c-checkout'] === 'true') {
        $user_id = get_current_user_id();

        $user_token = get_user_meta($user_id, 'quick-c-user-api-key', true);

        if (empty($user_token)) {
            wp_redirect(wc_get_cart_url());
            exit;
        }
    }
}



function custom_billing_fields($checkout)
{

    if (get_option('qcwc_enable_short_address') && (isset($_GET['quick-c-checkout']) && $_GET['quick-c-checkout'] === 'true')) {
        woocommerce_form_field(
            'billing_short_address',
            array(
                'type' => 'text',
                'class' => array('form-row-wide'),
                'label' => __('Short Address'),
                'placeholder' => __('Enter short address'),
            ),
            $checkout->get_value('billing_short_address')
        );
    }

    if (get_option('qcwc_enable_region') && (isset($_GET['quick-c-checkout']) && $_GET['quick-c-checkout'] === 'true')) {
        woocommerce_form_field(
            'billing_region',
            array(
                'type' => 'text',
                'class' => array('form-row-wide'),
                'label' => __('Region (State/County)'),
                'placeholder' => __('Enter region'),
            ),
            $checkout->get_value('billing_region')
        );
    }

    if (get_option('qcwc_enable_unit_number') && (isset($_GET['quick-c-checkout']) && $_GET['quick-c-checkout'] === 'true')) {
        woocommerce_form_field(
            'billing_unit_number',
            array(
                'type' => 'text',
                'class' => array('form-row-wide'),
                'label' => __('Unit Number'),
                'placeholder' => __('Enter unit number'),
            ),
            $checkout->get_value('billing_unit_number')
        );
    }

    if (get_option('qcwc_enable_building_number') && (isset($_GET['quick-c-checkout']) && $_GET['quick-c-checkout'] === 'true')) {
        woocommerce_form_field(
            'billing_building_number',
            array(
                'type' => 'text',
                'class' => array('form-row-wide'),
                'label' => __('Building Number'),
                'placeholder' => __('Enter building number'),
            ),
            $checkout->get_value('billing_building_number')
        );
    }

    if (get_option('qcwc_enable_street_name') && (isset($_GET['quick-c-checkout']) && $_GET['quick-c-checkout'] === 'true')) {
        woocommerce_form_field(
            'billing_street_name',
            array(
                'type' => 'text',
                'class' => array('form-row-wide'),
                'label' => __('Street Name'),
                'placeholder' => __('Enter street name'),
            ),
            $checkout->get_value('billing_street_name')
        );
    }

    if (get_option('qcwc_enable_district') && (isset($_GET['quick-c-checkout']) && $_GET['quick-c-checkout'] === 'true')) {
        woocommerce_form_field(
            'billing_district',
            array(
                'type' => 'text',
                'class' => array('form-row-wide'),
                'label' => __('District'),
                'placeholder' => __('Enter district'),
            ),
            $checkout->get_value('billing_district')
        );
    }

    if (get_option('qcwc_enable_lat') && (isset($_GET['quick-c-checkout']) && $_GET['quick-c-checkout'] === 'true')) {
        woocommerce_form_field(
            'billing_lat',
            array(
                'type' => 'text',
                'class' => array('form-row-wide'),
                'label' => __('Latitude'),
                'placeholder' => __('Enter latitude'),
            ),
            $checkout->get_value('billing_lat')
        );
    }

    if (get_option('qcwc_enable_long') && (isset($_GET['quick-c-checkout']) && $_GET['quick-c-checkout'] === 'true')) {
        woocommerce_form_field(
            'billing_long',
            array(
                'type' => 'text',
                'class' => array('form-row-wide'),
                'label' => __('Longitude'),
                'placeholder' => __('Enter longitude'),
            ),
            $checkout->get_value('billing_long')
        );
    }


    woocommerce_form_field(
        'quick_c_checkout_enabled',
        array(
            'type' => 'hidden',
            'default' => isset($_GET['quick-c-checkout']) && $_GET['quick-c-checkout'] === 'true' ? 'true' : 'false',
        ),
        $checkout->get_value('quick_c_checkout_enabled')
    );
}

add_action('woocommerce_after_checkout_billing_form', 'custom_billing_fields');

add_action('woocommerce_after_cart_totals', 'qcwc_add_custom_cart_button', 10);

function qcwc_add_custom_cart_button()
{
    $checkout_url = wc_get_checkout_url() . '?quick-c-checkout=true';

    $user_id = get_current_user_id();
    if ($user_id) {
        $user_token = get_user_meta($user_id, 'quick-c-user-api-key', true);
    }

    ?>
    <div class="wc-proceed-to-checkout">
        <a href="<?php if (isset($user_id) && empty($user_token)) {
            echo "#";
        } else {
            echo esc_url($checkout_url);
        } ?>" class="qcwc-cart-button" id="qcwc-cart-button">Quick c checkout</a>
    </div>
    <?php
}

add_shortcode("qcwc_checkout_module", "qcwc_add_custom_cart_button");

function display_delivery_preferences_checkout($checkout)
{
    if (is_user_logged_in() && (isset($_GET['quick-c-checkout']) && $_GET['quick-c-checkout'] === 'true')) {
        $user_id = get_current_user_id();

        $delivery_preferences = get_user_meta($user_id, 'delivery_preferences', true);

        $day = isset($delivery_preferences['day']) ? $delivery_preferences['day'] : '';
        $start_time = isset($delivery_preferences['start_time']) ? $delivery_preferences['start_time'] : '';
        $end_time = isset($delivery_preferences['end_time']) ? $delivery_preferences['end_time'] : '';

        echo '<div class="edit-prefences-container">';
        echo '<button id="edit-prefences-button" type="button" class="button">Edit Delivery Prefences</button>';
        echo '</div>';
        echo '<div class="delivery-preferences-section">';
        echo '<h3> Your Delivery Preferences</h3>';
        if (!empty($day) && !is_null($day) && $day !== "null") {
            echo '<p><strong>' . __('Day:') . '</strong> ' . esc_html($day) . '</p>';
        }
        if (!empty($start_time) && !is_null($start_time) && $start_time !== "null") {
            echo '<p><strong>' . __('Start Time:') . '</strong> ' . esc_html($start_time) . '</p>';
        }
        if (!empty($end_time) && !is_null($end_time) && $end_time !== "null") {
            echo '<p><strong>' . __('End Time:') . '</strong> ' . esc_html($end_time) . '</p>';
        }
        echo '</div>';
    }
}
add_action('woocommerce_before_order_notes', 'display_delivery_preferences_checkout');


// Add Authentication and Verify OTP Modal
function QCWC_custom_popup_html()
{
    if (is_checkout() || is_cart() || has_shortcode(get_post()->post_content, 'qcwc_checkout_module')) {
        ?>
        <div id="QCWC_loginModal" class="QCWC_loginModal" style="display: none;">
            <div class="QCWC_modal-content">
                <p class="message"><span class="text"></span><span class="qcwc_loader"></span></p>
                <div class="close-btn QCWC_loginModal_close_btn">
                    &times;
                </div>
                <p class="error-message"></p>
                <div class="login-content">
                    <img src="<?php echo plugins_url('assets/images/icon-logo.png', __FILE__); ?>" />
                    <h2>Authentication Required</h2>
                    <div class="QCWC_form-group">
                        <label for="#userEmail">Username or Email</label>
                        <input type="email" id="userEmail" value="" placeholder="Enter your Email Address" value="">
                        <span class="errorText errorText1"></span>
                    </div>
                    <button id="authenticateButton" type="button"><span class="btn-text">Authenticate</span> <span class="btn-loader"></span> </button>
                    <p class="register-account-para">Don't have an account? <span class="link">Register now</span></p>
                </div>
                <div class="register-content" style="display: none;">
                    <img src="<?php echo plugins_url('assets/images/icon-logo.png', __FILE__); ?>" />
                    <h2>Register Account</h2>
                    <div class="register-tabs">
                        <div class="register-tab" data-target="main-detail">
                            <div class="register-tab-icon-container">
                                <i class="fa-solid fa-exclamation register-tab-icon"></i>
                            </div>
                            <h3 class="register-tab-text">Main Details</h3>
                        </div>
                        <div class="register-tab" data-target="address-detail">
                            <div class="register-tab-icon-container">
                                <i class="fa-solid fa-location-dot register-tab-icon"></i>
                            </div>
                            <h3 class="register-tab-text">Address</h3>
                        </div>
                        <div class="register-tab" data-target="delivery-time-detail">
                            <div class="register-tab-icon-container">
                                <i class="fa-regular fa-clock register-tab-icon"></i>
                            </div>
                            <h3 class="register-tab-text">Delivery Time</h3>
                        </div>
                        <div class="middle-line">
                            <div class="middle-line-indicator"></div>
                        </div>
                    </div>
                    <div class="register-tabs-content">
                        <div class="register-tab-content" id="main-detail">
                            <div class="QCWC_form-groups">
                                <div class="QCWC_form-group">
                                    <label for="#register_first_name">First name</label>
                                    <input type="text" id="register_first_name" placeholder="Enter your first name...">
                                    <span class="error" id="firstNameError"></span>
                                </div>
                                <div class="QCWC_form-group">
                                    <label for="#register_last_name">Last name</label>
                                    <input type="text" id="register_last_name" placeholder="Enter your last name...">
                                    <span class="error" id="lastNameError"></span>
                                </div>
                                <div class="QCWC_form-group">
                                    <label for="#register_primary_phone_number">Primary</label>
                                    <div class="QCWC_form-group-inputs">
                                        <div class="QCWC_child-form-group">
                                            <input type="text" id="register_primary_phone_number_code" placeholder="" value="+966">
                                        </div>
                                        <div class="QCWC_child-form-group">
                                            <input type="number" id="register_primary_phone_number" placeholder="Enter phone number">
                                            <span class="error" id="primaryPhoneNumberError"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="QCWC_form-group">
                                    <label for="#register_secondary_phone_number">Secondary</label>
                                    <div class="QCWC_form-group-inputs">
                                        <div class="QCWC_child-form-group">
                                            <input type="text" id="register_secondary_phone_number_code" placeholder="" value="+966">
                                        </div>
                                        <div class="QCWC_child-form-group">
                                            <input type="number" id="register_secondary_phone_number" placeholder="Enter phone number">
                                            <span class="error" id="secondaryPhoneNumberError"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="QCWC_form-group">
                                    <label for="#register_email">Email address</label>
                                    <input type="text" id="register_email" placeholder="Enter your email address...">
                                    <span class="error" id="emailError"></span>
                                </div>
                            </div>
                        </div>
                        <div class="register-tab-content" id="address-detail">
                            <div class="QCWC_addresses">
                            </div>
                            <p class="add-new-address-tagline">+ Add New Address</p>
                        </div>
                        <div class="register-tab-content" id="delivery-time-detail">
                            <div class="delivery-times">
                            </div>
                        </div>
                    </div>
                    <button id="registerButton" type="button"><span class="register-btn-text">Continue</span> <span class="btn-loader register-btn-loader"></span> </button>
                    <p class="login-account-para">Already have an account? <span class="link">Login now</span></p>
                </div>
                <div class="verification-content" style="display: none;">
                    <img src="<?php echo plugins_url('assets/images/icon-logo.png', __FILE__); ?>" />
                    <h2>Verification Required</h2>
                    <p class="verification-tagline">Please select the verification option to verify</p>
                    <div class="btns">
                        <button id="verifyViaDeviceBtn" type="button"><span class="btn-text">Verify Via Device</span> <span class="btn-loader"></span></button>
                        <button id="verifyViaOtp" type="button"><span class="btn-text">Verify Via OTP</span> <span class="btn-loader"></span></button>
                    </div>
                    <p class="register-account-para">Don't have an account? <span class="link">Register now</span></p>
                </div>
                <div class="otp-content" style="display: none;">
                    <img src="<?php echo plugins_url('assets/images/icon-logo.png', __FILE__); ?>" />
                    <h2>Verify OTP</h2>
                    <p>Enter the code from the email we sent you</p>
                    <div class="verify-otp-input">
                        <div class="QCWC_form-group">
                            <input type="number" id="userOtp" maxlength="5" placeholder="00000" />
                            <span class="errorText errorText2"></span>
                        </div>
                        <button id="verifyOtpButton" type="button"><span class="btn-text">Submit</span> <span class="btn-loader"></span></button>
                    </div>
                    <p class="register-account-para">Don't have an account? <span class="link">Register now</span></p>
                </div>
            </div>
            <p class="quick_c_registered_para">POWERED BY Quick-c</p>
        </div>
        <?php
    }
}
add_action('wp_footer', 'QCWC_custom_popup_html');

function QCWC_custom_addresses_html()
{

    $user_id = get_current_user_id();
    $user_token = get_user_meta($user_id, 'quick-c-user-api-key', true);
    $check_user_token = empty($user_token);

    if (is_checkout() || is_cart() && $check_user_token !== "1") {
        ?>
        <div id="QCWC_addressesModal" class="QCWC_addressesModal">
            <div class="QCWC_modal-content">
                <p class="error-message error-message1"></p>
                <div class="close-btn QCWC_addressesModal_close_btn">
                    &times;
                </div>
                <div class="user-detail-content">
                    <div class="user-detail-content-header">
                        <img src="<?php echo plugins_url('assets/images/icon-logo.png', __FILE__); ?>" />
                        <button type="button" id="quick-c-logout-btn"><span class="btn-text">Logout</span><span class="btn-loader"></span></button>
                    </div>
                    <h2>Please Select Address</h2>
                    <div class="user-address">
                        <div class="user-addresses"></div>
                        <div class="user-address-loader"></div>
                    </div>
                    <!-- <div class="user-delivery-prefences">
                        <h2>Please Select Delivery Preferences</h2>
                        <div class="user-delivery-prefences-loader"></div>
                        <div class="user-delivery-prefences-list">
                        </div>
                    </div> -->
                    <button id="confirmAddressButton" type="button"><span class="confirm-btn-text">Confirm</span><span class="btn-loader confirm-btn-loader"></span></button>
                </div>
            </div>
            <p class="quick_c_registered_para">POWERED BY Quick-c</p>
        </div>
        <?php
    }
}

add_action('wp_footer', 'QCWC_custom_addresses_html');


function QCWC_custom_delivery_prefences_html()
{

    $user_id = get_current_user_id();
    $user_token = get_user_meta($user_id, 'quick-c-user-api-key', true);
    $check_user_token = empty($user_token);

    if (is_checkout() || is_cart() && $check_user_token !== "1") {
        ?>
        <div id="QCWC_prefencesModal" class="QCWC_prefencesModal">
            <div class="QCWC_modal-content">
                <p class="error-message error-message2"></p>
                <div class="close-btn QCWC_prefencesModal_close_btn">
                    &times;
                </div>
                <div class="user-detail-content prefence-detail-content">
                    <div class="user-detail-content-header">
                        <img src="<?php echo plugins_url('assets/images/icon-logo.png', __FILE__); ?>" />
                    </div>
                    <h2>Please Select Delivery Prefences</h2>
                    <div class="user-delivery-prefences">
                        <div class="user-delivery-prefences-loader"></div>
                        <div class="user-delivery-prefences-list">
                        </div>
                    </div>
                    <button id="confirmPrefenceButton" type="button"><span class="confirm-btn-text1">Confirm</span><span class="btn-loader confirm-btn-loader1"></span></button>
                </div>
            </div>
            <p class="quick_c_registered_para">POWERED BY Quick-c</p>
        </div>
        <?php
    }
}

add_action('wp_footer', 'QCWC_custom_delivery_prefences_html');


add_action('wp_ajax_register_user', 'QCWC_handle_register_user');
add_action('wp_ajax_nopriv_register_user', 'QCWC_handle_register_user');

function QCWC_handle_register_user()
{

    $first_name = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : "";
    $last_name = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : "";
    $email = isset($_POST['email']) ? sanitize_text_field($_POST['email']) : "";
    $primary_contact = isset($_POST['profile']['primary_contact']) ? $_POST['profile']['primary_contact'] : "";
    $secondary_contact = isset($_POST['profile']['secondary_contact']) ? $_POST['profile']['secondary_contact'] : "";
    $addresses = isset($_POST['addresses']) ? $_POST['addresses'] : [];
    $delivery_preferences = isset($_POST['delivery_preferences']) ? $_POST['delivery_preferences'] : [];

    $domain = $_SERVER['HTTP_HOST'];
    $root_domain = get_root_domain($domain);

    $api_handler = new API_Handler('https://quick-c.devsy.tech/api/v1/platform/register-user/');
    $response = $api_handler->registerUser($first_name, $last_name, $email, $primary_contact, $secondary_contact, $addresses, $delivery_preferences, $root_domain);


    if ($response) {
        if (isset($response['status_code']) && $response['status_code'] == 201) {
            $api_key = $response['data']['api_key'];

            $user = get_user_by('email', $email);

            if (!$user) {
                $password = wp_generate_password();
                $user_id = wp_create_user($email, $password, $email);

                wp_update_user(array(
                    'ID' => $user_id,
                    'role' => 'customer',
                ));
                $user = get_user_by('id', $user_id);
            }

            wp_set_auth_cookie($user->ID, true);
            wp_set_current_user($user->ID);
            do_action('wp_login', $user->user_login, $user);


            $current_user_id = get_current_user_id();
            update_user_meta($current_user_id, 'quick-c-user-api-key', $api_key);

            $user_token = $api_key;

            $api_handler1 = new API_Handler('https://quick-c.devsy.tech/api/v1/user/details/');
            $response1 = $api_handler1->getUserDetail($user_token, $email);

            if ($response1 && isset($response1['data'])) {
                $user_data = $response1['data'];

                $primary_address = null;
                if (!empty($user_data['addresses'])) {
                    foreach ($user_data['addresses'] as $address) {
                        if ($address['is_primary']) {
                            $primary_address = $address;
                            break;
                        }
                    }
                }

                $primary_delivery_preference = null;
                if (!empty($user_data['delivery_preferences'])) {
                    foreach ($user_data['delivery_preferences'] as $preference) {
                        if ($preference['is_primary']) {
                            $primary_delivery_preference = $preference;
                            break;
                        }
                    }
                }

                if ($primary_address) {
                    $primary_address_field = isset(
                        $primary_address['primary_address']
                    ) && $primary_address['primary_address'] !== null ? sanitize_text_field($primary_address['primary_address']) : "";
                    $secondary_address_field = isset(
                        $primary_address['secondary']
                    ) && $primary_address['secondary'] !== null ? sanitize_text_field($primary_address['secondary']) : "";
                    $short_address = isset(
                        $primary_address['short_address']
                    ) && $primary_address['short_address'] !== null ? sanitize_text_field($primary_address['short_address']) : "";
                    $region = isset(
                        $primary_address['region']
                    ) && $primary_address['region'] !== null ? sanitize_text_field($primary_address['region']) : "";
                    $unit_number = isset(
                        $primary_address['unit_number']
                    ) && $primary_address['unit_number'] !== null ? sanitize_text_field($primary_address['unit_number']) : "";
                    $building_number = isset(
                        $primary_address['building_number']
                    ) && $primary_address['building_number'] !== null ? sanitize_text_field($primary_address['building_number']) : "";
                    $street_name = isset(
                        $primary_address['street_name']
                    ) && $primary_address['street_name'] !== null ? sanitize_text_field($primary_address['street_name']) : "";
                    $district = isset(
                        $primary_address['district']
                    ) && $primary_address['district'] !== null ? sanitize_text_field($primary_address['district']) : "";
                    $latitude = isset(
                        $primary_address['latitude']
                    ) && $primary_address['latitude'] !== null ? sanitize_text_field($primary_address['latitude']) : "";
                    $longitude = isset(
                        $primary_address['longitude']
                    ) && $primary_address['longitude'] !== null ? sanitize_text_field($primary_address['longitude']) : "";
                    $city = sanitize_text_field($primary_address['city']);
                    $postal_code = sanitize_text_field($primary_address['postal_code']);

                    update_user_meta(
                        $user_id,
                        'shipping_address_1',
                        $primary_address_field
                    );
                    update_user_meta($user_id, 'shipping_address_2', $secondary_address_field);
                    update_user_meta($user_id, 'shipping_city', $city);
                    update_user_meta($user_id, 'shipping_postcode', $postal_code);

                    update_user_meta(
                        $user_id,
                        'billing_address_1',
                        $primary_address_field
                    );

                    if (get_option('qcwc_enable_short_address')) {
                        update_user_meta(
                            $user_id,
                            'billing_short_address',
                            $short_address
                        );
                    }

                    if (get_option('qcwc_enable_region')) {
                        update_user_meta(
                            $user_id,
                            'billing_region',
                            $region
                        );
                    }

                    if (get_option('qcwc_enable_unit_number')) {
                        update_user_meta(
                            $user_id,
                            'billing_unit_number',
                            $unit_number
                        );
                    }

                    if (get_option('qcwc_enable_building_number')) {
                        update_user_meta(
                            $user_id,
                            'billing_building_number',
                            $building_number
                        );
                    }

                    if (get_option('qcwc_enable_street_name')) {
                        update_user_meta(
                            $user_id,
                            'billing_street_name',
                            $street_name
                        );
                    }

                    if (get_option('qcwc_enable_district')) {
                        update_user_meta(
                            $user_id,
                            'billing_district',
                            $district
                        );
                    }

                    if (get_option('qcwc_enable_lat')) {
                        update_user_meta(
                            $user_id,
                            'billing_lat',
                            $latitude
                        );
                    }

                    if (get_option('qcwc_enable_long')) {
                        update_user_meta(
                            $user_id,
                            'billing_long',
                            $longitude
                        );
                    }


                    update_user_meta($user_id, 'billing_address_2', $secondary_address_field);
                    update_user_meta($user_id, 'billing_city', $city);
                    update_user_meta($user_id, 'billing_postcode', $postal_code);
                }

                if ($primary_delivery_preference) {
                    $delivery_preferences = array(
                        'day' => $primary_delivery_preference['day'] ? $primary_delivery_preference['day'] : "N/A",
                        'start_time' => $primary_delivery_preference['start_time'] ? $primary_delivery_preference['start_time'] : "N/A",
                        'end_time' => $primary_delivery_preference['end_time'] ? $primary_delivery_preference['end_time'] : "N/A"
                    );

                    update_user_meta($user_id, 'delivery_preferences', $delivery_preferences);
                }

                $user_ids = get_current_user_id();


                update_user_meta($user_ids, 'shipping_first_name', $user_data['first_name']);
                update_user_meta($user_id, 'shipping_last_name', $user_data['last_name']);
                update_user_meta($user_id, 'shipping_phone', $user_data['profile']['primary_contact']);

                update_user_meta($user_ids, 'billing_first_name', $user_data['first_name']);
                update_user_meta($user_id, 'billing_last_name', $user_data['last_name']);
                update_user_meta($user_id, 'billing_phone', $user_data['profile']['primary_contact']);
            }
        }


        wp_send_json($response);
    } else {
        wp_send_json_error('Registration failed');
    }
}


add_action('wp_ajax_search_short_address', 'QCWC_search_short_address');
add_action('wp_ajax_nopriv_search_short_address', 'QCWC_search_short_address');

function QCWC_search_short_address()
{
    $search = isset($_GET['value']) ? sanitize_text_field($_GET['value']) : '';

    $api_handler = new API_Handler('https://quick-c.devsy.tech/api/v1/user/lat-long-address/');
    $response = $api_handler->getShortAddresses($search);

    if ($response) {
        wp_send_json($response);
    } else {
        wp_send_json_error('API request failed');
    }
}

// Authentication Ajax

add_action('wp_ajax_authenticate_user', 'QCWC_handle_authentication');
add_action('wp_ajax_nopriv_authenticate_user', 'QCWC_handle_authentication');

function QCWC_handle_authentication()
{
    $email = isset($_POST['email']) ? sanitize_text_field($_POST['email']) : '';
    // $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://";
    // $domain = $protocol . $_SERVER['HTTP_HOST'];
    // $domain = $_SERVER['HTTP_HOST'];

    $domain = $_SERVER['HTTP_HOST'];
    $root_domain = get_root_domain($domain);
    global $platform;
    $verifyMethod = isset($_POST['verifyMethod']) ? sanitize_text_field($_POST['verifyMethod']) : 'JWT';

    $api_handler = new API_Handler('https://quick-c.devsy.tech/api/v1/platform/login-by-portal/');
    $response = $api_handler->authenticate($email, $root_domain, $platform, $verifyMethod);

    if ($response) {
        $_SESSION['quick_c_user_token'] = $response['data']['token'];

        wp_send_json($response);
    } else {
        wp_send_json_error('Authentication failed');
    }
}

add_action('wp_ajax_authenticate_verify_user', 'QCWC_handle_verify_authentication');
add_action('wp_ajax_nopriv_authenticate_verify_user', 'QCWC_handle_verify_authentication');

function QCWC_handle_verify_authentication()
{
    $user = isset($_POST['user']) ? sanitize_text_field($_POST['user']) : '';
    $verifyMethod = isset($_POST['verifyMethod']) ? sanitize_text_field($_POST['verifyMethod']) : 'JWT';
    $otp = isset($_POST['value']) ? sanitize_text_field($_POST['value']) : '';
    $user_token = get_quick_c_user_token_from_session();

    $api_handler = new API_Handler('https://quick-c.devsy.tech/api/v1/platform/verify-portal-login/');
    $response = $api_handler->verify_authenticate($user, $verifyMethod, $otp, $user_token);

    if ($response) {
        if (isset($response['status_code']) && $response['status_code'] == 200) {
            $current_user_id = get_current_user_id();
            $api_key = $response['data']['api_key'];
            update_user_meta($current_user_id, 'quick-c-user-api-key', $api_key);
        }
        wp_send_json($response);
    } else {
        wp_send_json_error('Verification failed');
    }
}

add_action('wp_ajax_check_api_key', 'QCWC_check_api_key');
add_action('wp_ajax_nopriv_check_api_key', 'QCWC_check_api_key');
function QCWC_check_api_key()
{
    $user_token = get_quick_c_user_token_from_session();

    // $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://";
    // $domain = $_SERVER['HTTP_HOST'];

    $domain = $_SERVER['HTTP_HOST'];
    $root_domain = get_root_domain($domain);
    global $platform;
    $email = isset($_GET['email']) ? sanitize_text_field($_GET['email']) : '';

    $api_handler = new API_Handler('https://quick-c.devsy.tech/api/v1/platform/api-key/?domain=' . $root_domain . '&platform=' . $platform . '&user=' . $email . '');
    $response = $api_handler->checkApiKey($user_token);

    // $user_id = get_current_user_id();
    // delete_user_meta($user_id, 'quick-c-user-api-key');
    // remove_auth_token_from_session();
    if ($response) {
        if (isset($response['status_code']) && $response['status_code'] == 200) {
            $api_key = $response['data']['api_key'];

            $user = get_user_by('email', $email);

            if (!$user) {
                $random_password = wp_generate_password();
                $user_id = wp_create_user($email, $random_password, $email);

                wp_update_user(array(
                    'ID' => $user_id,
                    'role' => 'customer'
                ));

                $user = get_user_by('id', $user_id);
            }

            wp_set_auth_cookie($user->ID, true);
            wp_set_current_user($user->ID);
            do_action('wp_login', $user->user_login, $user);

            $current_user_id = get_current_user_id();
            update_user_meta($current_user_id, 'quick-c-user-api-key', $api_key);

            $user_token = $api_key;

            $api_handler1 = new API_Handler('https://quick-c.devsy.tech/api/v1/user/details/');
            $response1 = $api_handler1->getUserDetail($user_token, $email);

            if ($response1 && isset($response1['data'])) {
                $user_data = $response1['data'];

                $primary_address = null;
                if (!empty($user_data['addresses'])) {
                    foreach ($user_data['addresses'] as $address) {
                        if ($address['is_primary']) {
                            $primary_address = $address;
                            break;
                        }
                    }
                }

                $primary_delivery_preference = null;
                if (!empty($user_data['delivery_preferences'])) {
                    foreach ($user_data['delivery_preferences'] as $preference) {
                        if ($preference['is_primary']) {
                            $primary_delivery_preference = $preference;
                            break;
                        }
                    }
                }

                if ($primary_address) {
                    $primary_address_field = isset(
                        $primary_address['primary_address']
                    ) && $primary_address['primary_address'] !== null ? sanitize_text_field($primary_address['primary_address']) : "";
                    $secondary_address_field = isset(
                        $primary_address['secondary']
                    ) && $primary_address['secondary'] !== null ? sanitize_text_field($primary_address['secondary']) : "";
                    $short_address = isset(
                        $primary_address['short_address']
                    ) && $primary_address['short_address'] !== null ? sanitize_text_field($primary_address['short_address']) : "";
                    $region = isset(
                        $primary_address['region']
                    ) && $primary_address['region'] !== null ? sanitize_text_field($primary_address['region']) : "";
                    $unit_number = isset(
                        $primary_address['unit_number']
                    ) && $primary_address['unit_number'] !== null ? sanitize_text_field($primary_address['unit_number']) : "";
                    $building_number = isset(
                        $primary_address['building_number']
                    ) && $primary_address['building_number'] !== null ? sanitize_text_field($primary_address['building_number']) : "";
                    $street_name = isset(
                        $primary_address['street_name']
                    ) && $primary_address['street_name'] !== null ? sanitize_text_field($primary_address['street_name']) : "";
                    $district = isset(
                        $primary_address['district']
                    ) && $primary_address['district'] !== null ? sanitize_text_field($primary_address['district']) : "";
                    $latitude = isset(
                        $primary_address['latitude']
                    ) && $primary_address['latitude'] !== null ? sanitize_text_field($primary_address['latitude']) : "";
                    $longitude = isset(
                        $primary_address['longitude']
                    ) && $primary_address['longitude'] !== null ? sanitize_text_field($primary_address['longitude']) : "";
                    $city = sanitize_text_field($primary_address['city']);
                    $postal_code = sanitize_text_field($primary_address['postal_code']);

                    update_user_meta(
                        $user_id,
                        'shipping_address_1',
                        $primary_address_field
                    );
                    update_user_meta($user_id, 'shipping_address_2', $secondary_address_field);
                    update_user_meta($user_id, 'shipping_city', $city);
                    update_user_meta($user_id, 'shipping_postcode', $postal_code);

                    update_user_meta(
                        $user_id,
                        'billing_address_1',
                        $primary_address_field
                    );

                    if (get_option('qcwc_enable_short_address')) {
                        update_user_meta(
                            $user_id,
                            'billing_short_address',
                            $short_address
                        );
                    }

                    if (get_option('qcwc_enable_region')) {
                        update_user_meta(
                            $user_id,
                            'billing_region',
                            $region
                        );
                    }

                    if (get_option('qcwc_enable_unit_number')) {
                        update_user_meta(
                            $user_id,
                            'billing_unit_number',
                            $unit_number
                        );
                    }

                    if (get_option('qcwc_enable_building_number')) {
                        update_user_meta(
                            $user_id,
                            'billing_building_number',
                            $building_number
                        );
                    }

                    if (get_option('qcwc_enable_street_name')) {
                        update_user_meta(
                            $user_id,
                            'billing_street_name',
                            $street_name
                        );
                    }

                    if (get_option('qcwc_enable_district')) {
                        update_user_meta(
                            $user_id,
                            'billing_district',
                            $district
                        );
                    }

                    if (get_option('qcwc_enable_lat')) {
                        update_user_meta(
                            $user_id,
                            'billing_lat',
                            $latitude
                        );
                    }

                    if (get_option('qcwc_enable_long')) {
                        update_user_meta(
                            $user_id,
                            'billing_long',
                            $longitude
                        );
                    }


                    update_user_meta($user_id, 'billing_address_2', $secondary_address_field);
                    update_user_meta($user_id, 'billing_city', $city);
                    update_user_meta($user_id, 'billing_postcode', $postal_code);
                }

                if ($primary_delivery_preference) {
                    $delivery_preferences = array(
                        'day' => $primary_delivery_preference['day'] ? $primary_delivery_preference['day'] : "N/A",
                        'start_time' => $primary_delivery_preference['start_time'] ? $primary_delivery_preference['start_time'] : "N/A",
                        'end_time' => $primary_delivery_preference['end_time'] ? $primary_delivery_preference['end_time'] : "N/A"
                    );

                    update_user_meta($user_id, 'delivery_preferences', $delivery_preferences);
                }

                $user_ids = get_current_user_id();


                update_user_meta($user_ids, 'shipping_first_name', $user_data['first_name']);
                update_user_meta($user_id, 'shipping_last_name', $user_data['last_name']);
                update_user_meta($user_id, 'shipping_phone', $user_data['profile']['primary_contact']);

                update_user_meta($user_ids, 'billing_first_name', $user_data['first_name']);
                update_user_meta($user_id, 'billing_last_name', $user_data['last_name']);
                update_user_meta($user_id, 'billing_phone', $user_data['profile']['primary_contact']);
            }
        }

        wp_send_json($response);
    } else {
        wp_send_json_error('API request failed');
    }
}



add_action('wp_ajax_fetch_user_details', 'QCWC_fetch_user_details');
add_action('wp_ajax_nopriv_fetch_user_details', 'QCWC_fetch_user_details');

function QCWC_fetch_user_details()
{

    $user_id = get_current_user_id();
    $user_token = get_user_meta($user_id, 'quick-c-user-api-key', true);
    $email = $_POST['email'];

    $api_handler = new API_Handler('https://quick-c.devsy.tech/api/v1/user/details/');
    $response = $api_handler->getUserDetail($user_token, $email);

    if ($response) {
        wp_send_json($response);
    } else {
        wp_send_json_error('API request failed');
    }
}

add_action('wp_ajax_save_user_detail', 'QCWC_save_user_detail');
add_action('wp_ajax_nopriv_save_user_detail', 'QCWC_save_user_detail');

function QCWC_save_user_detail()
{
    if (!is_user_logged_in()) {
        wp_send_json_error('User not logged in.');
        wp_die();
    }

    $user_id = get_current_user_id();

    $first_name = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $primary_address = isset($_POST['primary_address']) ? sanitize_text_field($_POST['primary_address']) : '';
    $secondary_address = isset($_POST['secondary_address']) ? sanitize_text_field($_POST['secondary_address']) : '';
    $billing_short_address = isset($_POST['billing_short_address']) ? sanitize_text_field($_POST['billing_short_address']) : '';
    $region = isset(
        $_POST['region']
    ) ? sanitize_text_field($_POST['region']) : '';
    $building_number
        = isset($_POST['building_number']) ? sanitize_text_field($_POST['building_number']) : '';
    $street_name = isset($_POST['street_name']) ? sanitize_text_field($_POST['street_name']) : '';
    $district
        = isset($_POST['district']) ? sanitize_text_field($_POST['district']) : '';
    $unit_number
        = isset($_POST['unit_number']) ? sanitize_text_field($_POST['unit_number']) : '';
    $latitude = isset($_POST['latitude']) ? sanitize_text_field($_POST['latitude']) : '';
    $longitude = isset($_POST['longitude']) ? sanitize_text_field($_POST['longitude']) : '';
    $city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
    $postal_code = isset($_POST['postal_code']) ? sanitize_text_field($_POST['postal_code']) : '';
    // $day = sanitize_text_field($_POST['day']);
    // $start_time = sanitize_text_field($_POST['start_time']);
    // $end_time = sanitize_text_field($_POST['end_time']);

    // $delivery_preferences = array(
    //     'day' => $day,
    //     'start_time' => $start_time,
    //     'end_time' => $end_time
    // );

    update_user_meta($user_id, 'shipping_first_name', $first_name);
    update_user_meta($user_id, 'shipping_last_name', $last_name);
    update_user_meta(
        $user_id,
        'shipping_address_1',
        $primary_address
    );
    update_user_meta($user_id, 'shipping_address_2', $secondary_address);
    update_user_meta($user_id, 'shipping_city', $city);
    update_user_meta($user_id, 'shipping_postcode', $postal_code);
    update_user_meta($user_id, 'shipping_phone', $phone);

    update_user_meta($user_id, 'billing_first_name', $first_name);
    update_user_meta($user_id, 'billing_last_name', $last_name);

    if (get_option('qcwc_enable_short_address')) {
        update_user_meta(
            $user_id,
            'billing_short_address',
            $billing_short_address
        );
    }

    if (get_option('qcwc_enable_region')) {
        update_user_meta(
            $user_id,
            'billing_region',
            $region
        );
    }

    if (get_option('qcwc_enable_building_number')) {
        update_user_meta(
            $user_id,
            'billing_building_number',
            $building_number
        );
    }

    if (get_option('qcwc_enable_street_name')) {
        update_user_meta(
            $user_id,
            'billing_street_name',
            $street_name
        );
    }

    if (get_option('qcwc_enable_district')) {
        update_user_meta(
            $user_id,
            'billing_district',
            $district
        );
    }

    if (get_option('qcwc_enable_unit_number')) {
        update_user_meta(
            $user_id,
            'billing_unit_number',
            $unit_number
        );
    }

    if (get_option('qcwc_enable_lat')) {
        update_user_meta(
            $user_id,
            'billing_lat',
            $latitude
        );
    }

    if (get_option('qcwc_enable_long')) {
        update_user_meta(
            $user_id,
            'billing_long',
            $longitude
        );
    }
    update_user_meta(
        $user_id,
        'billing_address_1',
        $primary_address
    );
    update_user_meta($user_id, 'billing_address_2', $secondary_address);
    update_user_meta($user_id, 'billing_city', $city);
    update_user_meta($user_id, 'billing_postcode', $postal_code);
    update_user_meta($user_id, 'billing_phone', $phone);
    // update_user_meta($user_id, 'delivery_preferences', $delivery_preferences);

    wp_send_json_success('Address saved successfully.');
    wp_die();
}

add_action('wp_ajax_save_user_delivery_prefence_detail', 'QCWC_save_user_delivery_prefence_detail');
add_action('wp_ajax_nopriv_save_user_delivery_prefence_detail', 'QCWC_save_user_delivery_prefence_detail');

function QCWC_save_user_delivery_prefence_detail()
{
    if (!is_user_logged_in()) {
        wp_send_json_error('User not logged in.');
        wp_die();
    }

    $user_id = get_current_user_id();

    $day = sanitize_text_field($_POST['day']);
    $start_time = sanitize_text_field($_POST['start_time']);
    $end_time = sanitize_text_field($_POST['end_time']);

    $delivery_preferences = array(
        'day' => $day,
        'start_time' => $start_time,
        'end_time' => $end_time
    );

    update_user_meta($user_id, 'delivery_preferences', $delivery_preferences);

    wp_send_json_success('Delivery Prefences saved successfully.');
    wp_die();
}

add_action('wp_ajax_save_user_primary_detail', 'QCWC_save_user_primary_detail');
add_action('wp_ajax_nopriv_save_user_primary_detail', 'QCWC_save_user_primary_detail');

function QCWC_save_user_primary_detail()
{
    $user_id = get_current_user_id();
    $user_token = get_user_meta($user_id, 'quick-c-user-api-key', true);
    $email = $_POST['email'];

    $api_handler = new API_Handler('https://quick-c.devsy.tech/api/v1/user/details/');
    $response = $api_handler->getUserDetail($user_token, $email);

    if ($response && isset($response['data'])) {
        $user_data = $response['data'];

        $primary_address = null;
        if (!empty($user_data['addresses'])) {
            foreach ($user_data['addresses'] as $address) {
                if ($address['is_primary']) {
                    $primary_address = $address;
                    break;
                }
            }
        }

        $primary_delivery_preference = null;
        if (!empty($user_data['delivery_preferences'])) {
            foreach ($user_data['delivery_preferences'] as $preference) {
                if ($preference['is_primary']) {
                    $primary_delivery_preference = $preference;
                    break;
                }
            }
        }

        if ($primary_address) {
            $primary_address_field = isset(
                $primary_address['primary_address']
            ) && $primary_address['primary_address'] !== null ? sanitize_text_field($primary_address['primary_address']) : "";
            $secondary_address_field = isset(
                $primary_address['secondary']
            ) && $primary_address['secondary'] !== null ? sanitize_text_field($primary_address['secondary']) : "";
            $short_address = isset(
                $primary_address['short_address']
            ) && $primary_address['short_address'] !== null ? sanitize_text_field($primary_address['short_address']) : "";
            $region = isset(
                $primary_address['region']
            ) && $primary_address['region'] !== null ? sanitize_text_field($primary_address['region']) : "";
            $unit_number = isset(
                $primary_address['unit_number']
            ) && $primary_address['unit_number'] !== null ? sanitize_text_field($primary_address['unit_number']) : "";
            $building_number = isset(
                $primary_address['building_number']
            ) && $primary_address['building_number'] !== null ? sanitize_text_field($primary_address['building_number']) : "";
            $street_name = isset(
                $primary_address['street_name']
            ) && $primary_address['street_name'] !== null ? sanitize_text_field($primary_address['street_name']) : "";
            $district = isset(
                $primary_address['district']
            ) && $primary_address['district'] !== null ? sanitize_text_field($primary_address['district']) : "";
            $latitude = isset(
                $primary_address['latitude']
            ) && $primary_address['latitude'] !== null ? sanitize_text_field($primary_address['latitude']) : "";
            $longitude = isset(
                $primary_address['longitude']
            ) && $primary_address['longitude'] !== null ? sanitize_text_field($primary_address['longitude']) : "";
            $city = sanitize_text_field($primary_address['city']);
            $postal_code = sanitize_text_field($primary_address['postal_code']);

            update_user_meta(
                $user_id,
                'shipping_address_1',
                $primary_address_field
            );
            update_user_meta($user_id, 'shipping_address_2', $secondary_address_field);
            update_user_meta($user_id, 'shipping_city', $city);
            update_user_meta($user_id, 'shipping_postcode', $postal_code);

            update_user_meta(
                $user_id,
                'billing_address_1',
                $primary_address_field
            );

            if (get_option('qcwc_enable_short_address')) {
                update_user_meta(
                    $user_id,
                    'billing_short_address',
                    $short_address
                );
            }

            if (get_option('qcwc_enable_region')) {
                update_user_meta(
                    $user_id,
                    'billing_region',
                    $region
                );
            }

            if (get_option('qcwc_enable_unit_number')) {
                update_user_meta(
                    $user_id,
                    'billing_unit_number',
                    $unit_number
                );
            }

            if (get_option('qcwc_enable_building_number')) {
                update_user_meta(
                    $user_id,
                    'billing_building_number',
                    $building_number
                );
            }

            if (get_option('qcwc_enable_street_name')) {
                update_user_meta(
                    $user_id,
                    'billing_street_name',
                    $street_name
                );
            }

            if (get_option('qcwc_enable_district')) {
                update_user_meta(
                    $user_id,
                    'billing_district',
                    $district
                );
            }

            if (get_option('qcwc_enable_lat')) {
                update_user_meta(
                    $user_id,
                    'billing_lat',
                    $latitude
                );
            }

            if (get_option('qcwc_enable_long')) {
                update_user_meta(
                    $user_id,
                    'billing_long',
                    $longitude
                );
            }


            update_user_meta($user_id, 'billing_address_2', $secondary_address_field);
            update_user_meta($user_id, 'billing_city', $city);
            update_user_meta($user_id, 'billing_postcode', $postal_code);
        }

        if ($primary_delivery_preference) {
            $delivery_preferences = array(
                'day' => $primary_delivery_preference['day'] ? $primary_delivery_preference['day'] : "N/A",
                'start_time' => $primary_delivery_preference['start_time'] ? $primary_delivery_preference['start_time'] : "N/A",
                'end_time' => $primary_delivery_preference['end_time'] ? $primary_delivery_preference['end_time'] : "N/A"
            );

            update_user_meta($user_id, 'delivery_preferences', $delivery_preferences);
        }

        $user_ids = get_current_user_id();


        update_user_meta($user_ids, 'shipping_first_name', $user_data['first_name']);
        update_user_meta($user_id, 'shipping_last_name', $user_data['last_name']);
        update_user_meta($user_id, 'shipping_phone', $user_data['profile']['primary_contact']);

        update_user_meta($user_ids, 'billing_first_name', $user_data['first_name']);
        update_user_meta($user_id, 'billing_last_name', $user_data['last_name']);
        update_user_meta($user_id, 'billing_phone', $user_data['profile']['primary_contact']);

        wp_send_json($response);
    } else {
        wp_send_json_error('API request failed');
    }
}


add_action('woocommerce_thankyou', 'QCWC_create_order');

function QCWC_create_order($order_id)
{
    $quick_checkout_enabled = get_post_meta($order_id, 'quick_c_checkout_enabled', true);


    if ($quick_checkout_enabled === 'yes') {
        $order = wc_get_order($order_id);

        if ($order_id) {

            $data = array(
                "order_id" => $order_id,
                "store_name" => "Quick-c",
                "store_image" => "",
                "current_status" => "created",
                "platform_domain" => "saqibdev.com",
                "order_items" => array(),
            );

            foreach ($order->get_items() as $item_id => $item) {
                $product_name = $item->get_name();
                $quantity = $item->get_quantity();

                $product = $item->get_product();

                $image_url = '';
                if ($product && $product->get_image_id()) {
                    $image_url = wp_get_attachment_url($product->get_image_id());
                }

                $data['order_items'][] = array(
                    'item_name' => $product_name,
                    'quantity' => $quantity,
                    'item_image' => $image_url,
                );
            }

            $json_data = json_encode($data);

            $user_id = get_current_user_id();
            $user_token = get_user_meta($user_id, 'quick-c-user-api-key', true);
            $api_handler = new API_Handler('https://quick-c.devsy.tech/api/v1/order/create/');
            $response = $api_handler->createOrder($user_token, $json_data);

            if ($response) {
                if (isset($response['data']['id'])) {

                    update_post_meta($order_id, 'quick_c_order_id', $response['data']['id']);
                }
                $order->update_status('created');
            }
        }
    }
}

add_action('woocommerce_checkout_update_order_meta', 'save_custom_billing_fields');

function save_custom_billing_fields($order_id)
{
    if (isset($_POST['billing_short_address']) && !empty($_POST['billing_short_address'])) {
        update_post_meta($order_id, 'billing_short_address', sanitize_text_field($_POST['billing_short_address']));
    }
    if (isset($_POST['billing_region']) && !empty($_POST['billing_region'])) {
        update_post_meta($order_id, 'billing_region', sanitize_text_field($_POST['billing_region']));
    }
    if (isset($_POST['billing_unit_number']) && !empty($_POST['billing_unit_number'])) {
        update_post_meta($order_id, 'billing_unit_number', sanitize_text_field($_POST['billing_unit_number']));
    }
    if (isset($_POST['billing_building_number']) && !empty($_POST['billing_building_number'])) {
        update_post_meta($order_id, 'billing_building_number', sanitize_text_field($_POST['billing_building_number']));
    }
    if (isset($_POST['billing_street_name']) && !empty($_POST['billing_street_name'])) {
        update_post_meta($order_id, 'billing_street_name', sanitize_text_field($_POST['billing_street_name']));
    }
    if (isset($_POST['billing_district']) && !empty($_POST['billing_district'])) {
        update_post_meta($order_id, 'billing_district', sanitize_text_field($_POST['billing_district']));
    }
    if (isset($_POST['billing_lat']) && !empty($_POST['billing_lat'])) {
        update_post_meta($order_id, 'billing_lat', sanitize_text_field($_POST['billing_lat']));
    }
    if (isset($_POST['billing_long']) && !empty($_POST['billing_long'])) {
        update_post_meta($order_id, 'billing_long', sanitize_text_field($_POST['billing_long']));
    }
    if (isset($_POST['quick_c_checkout_enabled'])) {
        $quick_checkout_enabled = $_POST['quick_c_checkout_enabled'] === 'true' ? 'yes' : 'no';
        update_post_meta($order_id, 'quick_c_checkout_enabled', $quick_checkout_enabled);
    }
}


add_action('woocommerce_order_status_changed', 'QCWC_update_order', 10, 4);

function QCWC_update_order($order_id)
{

    $order = wc_get_order($order_id);
    $quick_c_order_meta_id = get_post_meta($order_id, 'quick_c_order_id', true);
    $quick_checkout_enabled = get_post_meta($order_id, 'quick_c_checkout_enabled', true);

    if ($quick_checkout_enabled === 'yes') {

        if ($order_id && $quick_c_order_meta_id) {


            $data = array(
                "store_name" => "Quick-c",
                "current_status" => $order->get_status(),
                // "order_items" => array(),
            );

            // foreach ($order->get_items() as $item_id => $item) {
            //     $product_name = $item->get_name();
            //     $quantity = $item->get_quantity();

            //     $product = $item->get_product();

            //     $image_url = '';
            //     if ($product && $product->get_image_id()) {
            //         $image_url = wp_get_attachment_url($product->get_image_id());
            //     }

            //     $data['order_items'][] = array(
            //         'item_name' => $product_name,
            //         'quantity' => $quantity,
            //         'item_image' => $image_url,
            //     );
            // }

            $json_data = json_encode($data);

            $user_id = $order->get_user_id();
            $user_token = get_user_meta($user_id, 'quick-c-user-api-key', true);
            $api_handler = new API_Handler('https://quick-c.devsy.tech/api/v1/order/update/' . $quick_c_order_meta_id . '/');
            $response = $api_handler->updateOrder($user_token, $json_data);
        }
    }
}

add_action('woocommerce_admin_order_data_after_billing_address', 'display_custom_billing_fields_in_admin', 10, 1);

function display_custom_billing_fields_in_admin($order)
{
    $short_address = get_post_meta($order->get_id(), 'billing_short_address', true);
    $region = get_post_meta($order->get_id(), 'billing_region', true);
    $unit_number = get_post_meta($order->get_id(), 'billing_unit_number', true);
    $building_number = get_post_meta($order->get_id(), 'billing_building_number', true);
    $street_name = get_post_meta($order->get_id(), 'billing_street_name', true);
    $district = get_post_meta($order->get_id(), 'billing_district', true);
    $lat = get_post_meta($order->get_id(), 'billing_lat', true);
    $long = get_post_meta($order->get_id(), 'billing_long', true);
    if (!empty($short_address)) {
        echo '<p><strong>' . __('Short Address') . ':</strong> ' . $short_address . '</p>';
    }

    if (!empty($region)) {
        echo '<p><strong>' . __('Region') . ':</strong> ' . $region . '</p>';
    }

    if (!empty($unit_number)) {
        echo '<p><strong>' . __('Unit Number') . ':</strong> ' . $unit_number . '</p>';
    }

    if (!empty($building_number)) {
        echo '<p><strong>' . __('Building Number') . ':</strong> ' . $building_number . '</p>';
    }

    if (!empty($street_name)) {
        echo '<p><strong>' . __('Street Name') . ':</strong> ' . $street_name . '</p>';
    }

    if (!empty($district)) {
        echo '<p><strong>' . __('District') . ':</strong> ' . $district . '</p>';
    }

    if (!empty($lat)) {
        echo '<p><strong>' . __('Latitude') . ':</strong> ' . $lat . '</p>';
    }

    if (!empty($long)) {
        echo '<p><strong>' . __('Longitude') . ':</strong> ' . $long . '</p>';
    }
}


add_action('wp_ajax_logout_quick_c', 'QCWC_logout_quick_c');
add_action('wp_ajax_nopriv_logout_quick_c', 'QCWC_logout_quick_c');

function QCWC_logout_quick_c()
{

    $user_id = get_current_user_id();
    delete_user_meta(
        $user_id,
        'quick-c-user-api-key'
    );
    update_user_meta(
        $user_id,
        'shipping_address_1',
        ""
    );
    update_user_meta(
        $user_id,
        'shipping_address_2',
        ""
    );
    update_user_meta(
        $user_id,
        'shipping_city',
        ""
    );
    update_user_meta(
        $user_id,
        'shipping_postcode',
        ""
    );
    update_user_meta(
        $user_id,
        'shipping_phone',
        ""
    );

    update_user_meta(
        $user_id,
        'billing_address_1',
        ""
    );
    update_user_meta(
        $user_id,
        'billing_address_2',
        ""
    );
    update_user_meta(
        $user_id,
        'billing_city',
        ""
    );
    update_user_meta(
        $user_id,
        'billing_postcode',
        ""
    );
    update_user_meta(
        $user_id,
        'billing_phone',
        ""
    );
    update_user_meta(
        $user_id,
        'billing_short_address',
        ""
    );
    update_user_meta(
        $user_id,
        'billing_region',
        ""
    );
    update_user_meta(
        $user_id,
        'billing_unit_number',
        ""
    );
    update_user_meta(
        $user_id,
        'billing_building_number',
        ""
    );
    update_user_meta(
        $user_id,
        'billing_street_name',
        ""
    );
    update_user_meta(
        $user_id,
        'billing_district',
        ""
    );
    update_user_meta(
        $user_id,
        'billing_lat',
        ""
    );
    update_user_meta(
        $user_id,
        'billing_long',
        ""
    );

    remove_auth_token_from_session();

    wp_send_json_success([
        "message" => "SuccessFully Logout"
    ]);
}

?>