<?php
defined( 'ABSPATH' ) or die( 'Invalid Access!' );
/**
 * Plugin Name: Orders Track Parcel
 * Plugin URI: https://www.codegranite.com/download/orders-parcel/
 * Description: Add tracking link for an order once its been shipped. It also displays a link on the default orders list view of the customer and get to notify your customer via email.
 * Version: 1.0
 * Author: CodeGranite
 * License: GPLv3 or later
 * Author URI: https://www.codegranite.com
 * Text Domain: woocommerce-orders-parcel
 * Domain Path: /languages/
 */
class WC_Order_Parcel{
    function __construct() {
        add_action('plugins_loaded', array($this, 'woocommerce_order_parcel_init'));
        add_action('add_meta_boxes', array($this, 'parcel_tracking_link') );
        add_action('wp_ajax_add_tracking_parcel', array($this, 'add_tracking_parcel'));
        add_action('wp_ajax_remove_tracking_parcel', array($this, 'remove_tracking_parcel'));
        add_action('woocommerce_order_details_after_order_table', array($this, 'tracking_parcel_details_after_order_table'));
        add_filter('woocommerce_my_account_my_orders_actions', array($this, 'tracking_parcel_details_order_actions'), 20, 2);
    }
    function woocommerce_order_parcel_init() {
        $plugin_rel_path = basename( dirname( __FILE__ ) ) . '/languages';
        load_plugin_textdomain( 'woocommerce-orders-parcel', false, $plugin_rel_path );
    }
    function parcel_tracking_link() {
        add_meta_box( 'tracking_parcel_fields', __('Track Parcels','woocommerce-orders-parcel'), array($this, 'tracking_parcel_content'), 'shop_order', 'side', 'core' );
    }
    function tracking_parcel_content(){
        global $post;
        $plugin_dir = plugin_dir_url(__FILE__);
        wp_enqueue_script('order-parcel-script', $plugin_dir.'script.min.js', array('jquery'));
        wp_enqueue_style('order-parcel-style', $plugin_dir.'styles.min.css');
        $parcel_links = unserialize(get_post_meta( $post->ID, '_custom_tracking_parcel', true));
        $parcel_links = empty($parcel_links) ? array() : $parcel_links;
        include_once('pages/tracking-form.php');
    }
    function add_tracking_parcel(){
        if( is_admin() ){
            $order_id = $this->get_request_var('order_id', 0);
            $parcel_links = unserialize(get_post_meta($order_id, '_custom_tracking_parcel', true));
            $link = $this->get_request_var('tracking_link', '#');
            $company = $this->get_request_var('shipping_company', __('Track Parcel', 'woocommerce-orders-parcel'));
            if(empty($parcel_links)){
                $parcel_links = array();
            }
            if($this->get_request_var('notify', false)){
                $order = wc_get_order($order_id);
                $message = apply_filters('wc_tracking_link_notification', 'Hello %s, Your order have just been shipped thru <a href="%s">%s Click this Link</a> to see the progress. You can also track order shipment using My Account page.');
                $order->add_order_note(
                    sprintf($message, $order->get_billing_first_name(), $link, $company), 
                    1, 
                    true
                );
            }
            $parcel_links[] = apply_filters('wc_tracking_link_before_saving', array(
                'company'=> $company,
                'url' => $link
            ));
            do_action('wc_before_adding_parcel', $order_id, $parcel_links);
            update_post_meta($order_id, '_custom_tracking_parcel', serialize($parcel_links));
            do_action('wc_after_adding_parcel', $order_id, $parcel_links);
            foreach($parcel_links as $id => $track){
                echo "<li><a href=\"{$track['url']}\">{$track['company']}</a> - <a href=\"#\" onclick=\"remove_tracking_parcel('{$id}', this)\">".__('Remove', 'woocommerce-orders-parcel')."</a></li>";
            }
        }
        wp_die();
    }
    function remove_tracking_parcel(){
        if(is_admin()){
            $order_id = $this->get_request_var('order_id', 0);
            $parcel_links = unserialize(get_post_meta($order_id, '_custom_tracking_parcel', true));
            unset($parcel_links[$_POST['id']]);
            update_post_meta($order_id, '_custom_tracking_parcel', serialize($parcel_links));
            foreach($parcel_links as $id => $track){
                echo "<li><a href=\"{$track['url']}\">{$track['company']}</a> - <a href=\"#\" onclick=\"remove_tracking_parcel('{$id}', this)\">".__('Remove', 'woocommerce-orders-parcel')."</a></li>";
            }
        }
        wp_die();
    }
    function tracking_parcel_details_order_actions( $actions, $order ) {
        $details = unserialize(get_post_meta($order->get_id(), '_custom_tracking_parcel', true));
        if(empty($details)) return $actions;
        foreach($details as $id => $track ){
            $actions['parcel_tracking_link_'.$id] = array(
                    'url'  => $track['url'],
                    'name' => $track['company'],
            );
        }
        return apply_filters('wc_tracking_parcel_my_orders_actions', $actions);
    }
    function tracking_parcel_details_after_order_table($order){
        $details = unserialize(get_post_meta($order->get_id(), '_custom_tracking_parcel', true));
        foreach($details as $id => $track){
            echo "<a class='button' href='{$track['url']}'>{$track['company']}</a>";
        }
    }
    function get_request_var($var_name, $default){
        if(isset($_REQUEST[$var_name])){
            return $_REQUEST[$var_name];
        }
        return $default;
    }
}
$GLOBALS['wcorderparcel'] = new WC_Order_Parcel();
