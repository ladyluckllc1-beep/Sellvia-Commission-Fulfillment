<?php
/**
 * Plugin Name: Sellvia Commission Manager
 * Plugin URI: https://tophitsemporium.shop
 * Description: Process Sellvia orders using pending commission balance
 * Version: 1.0.0
 * Author: Top Hits Emporium
 * License: GPL v2 or later
 * Text Domain: sellvia-commission-manager
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Sellvia_Commission_Manager {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('rest_api_init', [$this, 'register_rest_routes']);
    }
    
    /**
     * Add admin menu page
     */
    public function add_admin_menu() {
        add_menu_page(
            'Sellvia Orders',
            'Sellvia Orders',
            'manage_woocommerce',
            'sellvia-commission-manager',
            [$this, 'render_admin_page'],
            'dashicons-cart',
            56
        );
    }
    
    /**
     * Render admin page - this will load your React app
     */
    public function render_admin_page() {
        ?>
        <div id="sellvia-commission-root"></div>
        <script>
            // Redirect to your React app URL
            window.location.href = 'https://7fde09b566a44297a9e1207756cc7e61-latest.preview.enter.pro/orders';
        </script>
        <?php
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if ('toplevel_page_sellvia-commission-manager' !== $hook) {
            return;
        }
        
        // You can add custom styles here if needed
        wp_enqueue_style(
            'sellvia-commission-manager',
            plugins_url('assets/css/admin.css', __FILE__),
            [],
            '1.0.0'
        );
    }
    
    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        // Webhook endpoint to sync order status from Sellvia back to WooCommerce
        register_rest_route('sellvia/v1', '/order-status', [
            'methods' => 'POST',
            'callback' => [$this, 'update_order_status'],
            'permission_callback' => [$this, 'verify_webhook_signature'],
        ]);
    }
    
    /**
     * Update order status webhook handler
     */
    public function update_order_status($request) {
        $params = $request->get_json_params();
        
        $order_id = sanitize_text_field($params['order_id'] ?? '');
        $status = sanitize_text_field($params['status'] ?? '');
        
        if (empty($order_id) || empty($status)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Missing required parameters'
            ], 400);
        }
        
        // Update WooCommerce order status
        $order = wc_get_order($order_id);
        if ($order) {
            $order->update_status($status);
            $order->add_order_note(sprintf(
                'Order status updated via Sellvia Commission Manager: %s',
                $status
            ));
            
            return new WP_REST_Response([
                'success' => true,
                'message' => 'Order status updated successfully'
            ], 200);
        }
        
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Order not found'
        ], 404);
    }
    
    /**
     * Verify webhook signature (implement your own security)
     */
    public function verify_webhook_signature($request) {
        // TODO: Implement signature verification
        // For now, we'll check if user has admin capabilities
        return current_user_can('manage_woocommerce');
    }
}

// Initialize plugin
Sellvia_Commission_Manager::get_instance();

/**
 * Activation hook
 */
register_activation_hook(__FILE__, function() {
    // Flush rewrite rules
    flush_rewrite_rules();
});

/**
 * Deactivation hook
 */
register_deactivation_hook(__FILE__, function() {
    // Flush rewrite rules
    flush_rewrite_rules();
});
