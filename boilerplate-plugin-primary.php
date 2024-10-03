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

    //plugin scripts
    wp_enqueue_script('QCWC-script', plugins_url('assets/js/script.js', __FILE__), array(), '1.1.0', true);


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

    // $domain = $_SERVER['HTTP_HOST'];
    $domain = "saqibdev.com";
    global $platform;
    $description = "An example wordpress platform for demonstration.";
    $ip_address = $_SERVER['REMOTE_ADDR'];

    $api_handler = new API_Handler('https://quick-c.devsy.tech/api/v1/platform/register/');
    $response = $api_handler->registerPlatForm($platform, $domain, $description, $ip_address);

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
    if (is_checkout() && is_user_logged_in()) {
        $user_id = get_current_user_id();
        $user_token = get_user_meta($user_id, 'quick-c-user-api-key', true);

        wp_localize_script('QCWC-script', 'popupData', array(
            'isTokenEmpty' => empty($user_token),
            'userEmail' => wp_get_current_user()->user_email,
        ));
    }
}
add_action('wp_enqueue_scripts', 'QCWC_custom_checkout_popup');



function add_custom_button_checkout()
{
    if (is_user_logged_in()) {
        echo '<div class="edit-address-container">';
        echo '<button id="edit-address-button" type="button" class="button">Edit Address</button>';
        echo '</div>';
    }
}
add_action('woocommerce_before_checkout_billing_form', 'add_custom_button_checkout', 10);

function display_delivery_preferences_checkout($checkout)
{
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();

        $delivery_preferences = get_user_meta($user_id, 'delivery_preferences', true);

        $day = isset($delivery_preferences['day']) ? $delivery_preferences['day'] : '';
        $start_time = isset($delivery_preferences['start_time']) ? $delivery_preferences['start_time'] : '';
        $end_time = isset($delivery_preferences['end_time']) ? $delivery_preferences['end_time'] : '';

        // Output the preferences on the checkout page
        echo '<div id="delivery-preferences-section">';
        echo '<h3> Your Delivery Preferences</h3>';
        echo '<p><strong>' . __('Day:') . '</strong> ' . esc_html($day) . '</p>';
        echo '<p><strong>' . __('Start Time:') . '</strong> ' . esc_html($start_time) . '</p>';
        echo '<p><strong>' . __('End Time:') . '</strong> ' . esc_html($end_time) . '</p>';
        echo '</div>';
    }
}
add_action('woocommerce_before_order_notes', 'display_delivery_preferences_checkout');


// Add Authentication and Verify OTP Modal
function QCWC_custom_popup_html()
{
    if (is_checkout()) {
        ?>
        <div id="QCWC_loginModal" class="QCWC_loginModal" style="display: none;">
            <div class="QCWC_modal-content">
                <p class="message"><span class="text"></span><span class="loader"></span></p>
                <p class="error-message"></p>
                <div class="login-content">
                    <img src="<?php echo plugins_url('assets/images/icon-logo.png', __FILE__); ?>" />
                    <h2>Authentication Required</h2>
                    <div class="QCWC_form-group">
                        <label for="#userEmail">Email Address</label>
                        <input type="email" id="userEmail" value="" placeholder="Enter your Email Address" value="">
                        <span class="errorText errorText1"></span>
                    </div>
                    <button id="authenticateButton" type="button"><span class="btn-text">Authenticate</span> <span class="btn-loader"></span> </button>
                </div>
                <div class="verification-content" style="display: none;">
                    <img src="<?php echo plugins_url('assets/images/icon-logo.png', __FILE__); ?>" />
                    <h2>Verification Required</h2>
                    <p>Please select the verification option to verify</p>
                    <div class="btns">
                        <button id="verifyViaDeviceBtn" type="button"><span class="btn-text">Verify Via Device</span> <span class="btn-loader"></span></button>
                        <button id="verifyViaOtp" type="button"><span class="btn-text">Verify Via OTP</span> <span class="btn-loader"></span></button>
                    </div>
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
                </div>
            </div>
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

    if (is_checkout() && $check_user_token !== "1") {
        ?>
        <div id="QCWC_addressesModal" class="QCWC_addressesModal">
            <div class="QCWC_modal-content">
                <div class="user-detail-content">
                    <img src="<?php echo plugins_url('assets/images/icon-logo.png', __FILE__); ?>" />
                    <h2>Please Select Address</h2>
                    <div class="user-address">
                        <div class="user-addresses"></div>
                        <div class="user-address-loader"></div>
                    </div>
                    <div class="user-delivery-prefences">
                        <h2>Please Select Delivery Preferences</h2>
                        <div class="user-delivery-prefences-loader"></div>
                        <div class="user-delivery-prefences-list">
                        </div>
                    </div>
                    <button id="confirmAddressButton" type="button"><span class="confirm-btn-text">Confirm</span><span class="btn-loader confirm-btn-loader"></span></button>
                </div>
            </div>
        </div>
        <?php
    }

}

add_action('wp_footer', 'QCWC_custom_addresses_html');


// Authentication Ajax

add_action('wp_ajax_authenticate_user', 'QCWC_handle_authentication');
add_action('wp_ajax_nopriv_authenticate_user', 'QCWC_handle_authentication');

function QCWC_handle_authentication()
{
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    // $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://";
    // $domain = $protocol . $_SERVER['HTTP_HOST'];
    $domain = "saqibdev.com";
    global $platform;
    $verifyMethod = isset($_POST['verifyMethod']) ? sanitize_text_field($_POST['verifyMethod']) : 'JWT';

    $api_handler = new API_Handler('https://quick-c.devsy.tech/api/v1/platform/login-by-portal/');
    $response = $api_handler->authenticate($email, $domain, $platform, $verifyMethod);

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
    $user = isset($_POST['user']) ? sanitize_email($_POST['user']) : '';
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
    // $domain = $protocol . $_SERVER['HTTP_HOST'];
    $domain = "saqibdev.com";
    global $platform;
    $email = isset($_GET['email']) ? sanitize_email($_GET['email']) : '';

    $api_handler = new API_Handler('https://quick-c.devsy.tech/api/v1/platform/api-key/?domain=' . $domain . '&platform=' . $platform . '&user=' . $email . '');
    $response = $api_handler->checkApiKey($user_token);

    // $user_id = get_current_user_id();
    // delete_user_meta($user_id, 'quick-c-user-api-key');
    // remove_auth_token_from_session();
    if ($response) {
        if (isset($response['status_code']) && $response['status_code'] == 200) {
            $current_user_id = get_current_user_id();
            $api_key = $response['data']['api_key'];
            update_user_meta($current_user_id, 'quick-c-user-api-key', $api_key);
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
    $street_address = isset($_POST['street_address']) ? sanitize_text_field($_POST['street_address']) : '';
    $city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
    $postal_code = isset($_POST['postal_code']) ? sanitize_text_field($_POST['postal_code']) : '';
    $day = sanitize_text_field($_POST['day']);
    $start_time = sanitize_text_field($_POST['start_time']);
    $end_time = sanitize_text_field($_POST['end_time']);

    $delivery_preferences = array(
        'day' => $day,
        'start_time' => $start_time,
        'end_time' => $end_time
    );

    update_user_meta($user_id, 'shipping_first_name', $first_name);
    update_user_meta($user_id, 'shipping_last_name', $last_name);
    update_user_meta($user_id, 'shipping_address', $street_address);
    update_user_meta($user_id, 'shipping_city', $city);
    update_user_meta($user_id, 'shipping_postcode', $postal_code);
    update_user_meta($user_id, 'shipping_phone', $phone);

    update_user_meta($user_id, 'billing_first_name', $first_name);
    update_user_meta($user_id, 'billing_last_name', $last_name);
    update_user_meta($user_id, 'billing_address', $street_address);
    update_user_meta($user_id, 'billing_city', $city);
    update_user_meta($user_id, 'billing_postcode', $postal_code);
    update_user_meta($user_id, 'billing_phone', $phone);
    update_user_meta($user_id, 'delivery_preferences', $delivery_preferences);

    wp_send_json_success('Address saved successfully.');
    wp_die();
}

add_action('woocommerce_thankyou', 'QCWC_create_order');

function QCWC_create_order($order_id)
{

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

add_action('woocommerce_order_status_changed', 'QCWC_update_order', 10, 4);

function QCWC_update_order($order_id)
{

    $order = wc_get_order($order_id);
    $quick_c_order_meta_id = get_post_meta($order_id, 'quick_c_order_id', true);

    if ($order_id && $quick_c_order_meta_id) {


        $data = array(
            "store_name" => "Quick-c",
            "current_status" => $order->get_status(),
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
        $api_handler = new API_Handler('https://quick-c.devsy.tech/api/v1/order/update/' . $quick_c_order_meta_id . '/');
        $response = $api_handler->updateOrder($user_token, $json_data);

    }

}

?>