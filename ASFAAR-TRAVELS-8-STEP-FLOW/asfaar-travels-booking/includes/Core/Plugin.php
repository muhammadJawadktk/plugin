<?php
namespace AsfaarTravels\Core;

if (!defined('ABSPATH')) exit;

class Plugin {
    
    public function init() {
        add_action('admin_menu', [$this, 'add_menus']);
        
        // Admin actions
        add_action('admin_post_asfaar_travels_save_package', [$this, 'save_package']);
        add_action('admin_post_asfaar_travels_delete_package', [$this, 'delete_package']);
        add_action('admin_post_asfaar_travels_save_hotel', [$this, 'save_hotel']);
        add_action('admin_post_asfaar_travels_delete_hotel', [$this, 'delete_hotel']);
        add_action('admin_post_asfaar_travels_save_transport', [$this, 'save_transport']);
        add_action('admin_post_asfaar_travels_delete_transport', [$this, 'delete_transport']);
        add_action('admin_post_asfaar_travels_repair_database', [$this, 'repair_database']);
        add_action('admin_post_asfaar_travels_test_insert', [$this, 'test_insert']);
        
        add_action('admin_enqueue_scripts', [$this, 'admin_assets']);
        add_action('wp_enqueue_scripts', [$this, 'frontend_assets']);
        
        add_shortcode('Asfaar_booking_System', [$this, 'render_booking']);
        
        add_action('wp_ajax_asfaar_travels_complete_booking', [$this, 'ajax_complete_booking']);
        add_action('wp_ajax_nopriv_asfaar_travels_complete_booking', [$this, 'ajax_complete_booking']);
        
        add_action('admin_notices', [$this, 'admin_notices']);
    }
    
    public function add_menus() {
        add_menu_page('Asfaar Travels Booking', 'Asfaar Travels Booking', 'manage_options', 'asfaar-travels-booking', [$this, 'dashboard'], 'dashicons-calendar-alt', 30);
        add_submenu_page('asfaar-travels-booking', 'Dashboard', 'Dashboard', 'manage_options', 'asfaar-travels-booking', [$this, 'dashboard']);
        add_submenu_page('asfaar-travels-booking', 'Packages', 'Packages', 'manage_options', 'asfaar-travels-packages', [$this, 'packages']);
        add_submenu_page('asfaar-travels-booking', 'Hotels', 'Hotels', 'manage_options', 'asfaar-travels-hotels', [$this, 'hotels']);
        add_submenu_page('asfaar-travels-booking', 'Transport', 'Transport', 'manage_options', 'asfaar-travels-transport', [$this, 'transport']);
        add_submenu_page('asfaar-travels-booking', 'Bookings', 'Bookings', 'manage_options', 'asfaar-travels-bookings', [$this, 'bookings']);
        add_submenu_page('asfaar-travels-booking', 'Database Repair', 'Database Repair', 'manage_options', 'asfaar-travels-repair', [$this, 'repair_page']);
    }
    
    public function admin_assets($hook) {
        if (strpos($hook, 'asfaar-travels-') !== false) {
            wp_enqueue_media();
            wp_enqueue_style('asfaar-travels-admin', ASFAAR_TRAVELS_URL . 'assets/css/admin.css', [], ASFAAR_TRAVELS_VERSION);
            wp_enqueue_script('asfaar-travels-admin', ASFAAR_TRAVELS_URL . 'assets/js/admin.js', ['jquery'], ASFAAR_TRAVELS_VERSION, true);
        }
    }
    
    public function admin_notices() {
        if (isset($_GET['asfaar_error'])) {
            echo '<div class="notice notice-error is-dismissible"><p><strong>Error:</strong> ' . esc_html(urldecode($_GET['asfaar_error'])) . '</p></div>';
        }
        if (isset($_GET['asfaar_success'])) {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html(urldecode($_GET['asfaar_success'])) . '</p></div>';
        }
    }
    
    public function frontend_assets() {
        wp_enqueue_style('asfaar-travels-booking', ASFAAR_TRAVELS_URL . 'assets/css/booking.css', [], ASFAAR_TRAVELS_VERSION);
        // Enqueue visa calculator first (dependency)
        wp_enqueue_script('asfaar-travels-visa-calculator', ASFAAR_TRAVELS_URL . 'assets/js/visa-calculator.js', ['jquery'], ASFAAR_TRAVELS_VERSION, true);
        // Then main booking script
        wp_enqueue_script('asfaar-travels-booking', ASFAAR_TRAVELS_URL . 'assets/js/booking.js', ['jquery', 'asfaar-travels-visa-calculator'], ASFAAR_TRAVELS_VERSION, true);
        wp_localize_script('asfaar-travels-booking', 'afsarData', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('afsar_booking')
        ]);
    }
    
    public function render_booking($atts) {
        // Disable caching for this shortcode to ensure fresh data
        if (!defined('DONOTCACHEPAGE')) {
            define('DONOTCACHEPAGE', true);
        }
        
        // Prevent browser caching
        nocache_headers();
        
        ob_start();
        include ASFAAR_TRAVELS_PATH . 'templates/frontend/booking-form.php';
        return ob_get_clean();
    }
    
    public function dashboard() {
        include ASFAAR_TRAVELS_PATH . 'templates/admin/dashboard.php';
    }
    
    public function packages() {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
        if ($action === 'add' || $action === 'edit') {
            include ASFAAR_TRAVELS_PATH . 'templates/admin/package-form.php';
        } else {
            include ASFAAR_TRAVELS_PATH . 'templates/admin/packages-list.php';
        }
    }
    
    public function hotels() {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
        if ($action === 'add' || $action === 'edit') {
            include ASFAAR_TRAVELS_PATH . 'templates/admin/hotel-form.php';
        } else {
            include ASFAAR_TRAVELS_PATH . 'templates/admin/hotels-list.php';
        }
    }
    
    public function transport() {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
        if ($action === 'add' || $action === 'edit') {
            include ASFAAR_TRAVELS_PATH . 'templates/admin/transport-form.php';
        } else {
            include ASFAAR_TRAVELS_PATH . 'templates/admin/transport-list.php';
        }
    }
    
    public function bookings() {
        include ASFAAR_TRAVELS_PATH . 'templates/admin/bookings-list.php';
    }
    
    public function repair_page() {
        include ASFAAR_TRAVELS_PATH . 'templates/admin/repair.php';
    }
    
    // SAVE PACKAGE
    public function save_package() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'asfaar_travels_save_package')) {
            wp_die('Security check failed');
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'asfaar_travels_packages';
        
        // Prepare data
        $data = [
            'package_name' => sanitize_text_field($_POST['package_name']),
            'description' => wp_kses_post($_POST['description']),
            'price' => floatval($_POST['price']),
            'duration' => sanitize_text_field($_POST['duration']),
            'category' => sanitize_text_field($_POST['category']),
            'itinerary' => wp_kses_post($_POST['itinerary']),
            'inclusions' => wp_kses_post($_POST['inclusions']),
            'exclusions' => wp_kses_post($_POST['exclusions']),
            'image_url' => isset($_POST['image_url']) ? esc_url_raw($_POST['image_url']) : '',
            'status' => sanitize_text_field($_POST['status'])
        ];
        
        $formats = ['%s', '%s', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%s'];
        
        if (isset($_POST['id']) && $_POST['id']) {
            // UPDATE
            $id = intval($_POST['id']);
            $result = $wpdb->update($table, $data, ['id' => $id], $formats, ['%d']);
            
            if ($result === false) {
                error_log('AFSAR Package Update Error: ' . $wpdb->last_error);
                wp_redirect(admin_url('admin.php?page=asfaar-travels-packages&asfaar_error=' . urlencode('Update failed: ' . $wpdb->last_error)));
                exit;
            }
            
            wp_redirect(admin_url('admin.php?page=asfaar-travels-packages&asfaar_success=Package updated successfully!'));
            exit;
        } else {
            // INSERT
            $result = $wpdb->insert($table, $data, $formats);
            
            if ($result === false) {
                error_log('AFSAR Package Insert Error: ' . $wpdb->last_error);
                error_log('AFSAR Package Data: ' . print_r($data, true));
                wp_redirect(admin_url('admin.php?page=asfaar-travels-packages&asfaar_error=' . urlencode('Insert failed: ' . $wpdb->last_error)));
                exit;
            }
            
            $insert_id = $wpdb->insert_id;
            error_log('AFSAR Package Created: ID ' . $insert_id);
            
            wp_redirect(admin_url('admin.php?page=asfaar-travels-packages&asfaar_success=Package created successfully! ID: ' . $insert_id));
            exit;
        }
    }
    
    public function delete_package() {
        if (!current_user_can('manage_options')) wp_die('Unauthorized');
        
        $id = intval($_GET['id']);
        if (!wp_verify_nonce($_GET['_wpnonce'], 'asfaar_travels_delete_package_' . $id)) {
            wp_die('Security check failed');
        }
        
        global $wpdb;
        $result = $wpdb->delete($wpdb->prefix . 'asfaar_travels_packages', ['id' => $id], ['%d']);
        
        if ($result === false) {
            wp_redirect(admin_url('admin.php?page=asfaar-travels-packages&asfaar_error=Delete failed'));
            exit;
        }
        
        wp_redirect(admin_url('admin.php?page=asfaar-travels-packages&asfaar_success=Package deleted successfully!'));
        exit;
    }
    
    // SAVE HOTEL
    public function save_hotel() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'asfaar_travels_save_hotel')) {
            wp_die('Security check failed');
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'asfaar_travels_hotels';
        
        $data = [
            'name' => sanitize_text_field($_POST['name']),
            'city' => sanitize_text_field($_POST['city']),
            'star_rating' => intval($_POST['star_rating']),
            'distance' => sanitize_text_field($_POST['distance']),
            'price_per_night' => floatval($_POST['price_per_night']),
            'description' => wp_kses_post($_POST['description']),
            'amenities' => wp_kses_post($_POST['amenities']),
            'image_url' => isset($_POST['image_url']) ? esc_url_raw($_POST['image_url']) : '',
            'status' => sanitize_text_field($_POST['status'])
        ];
        
        $formats = ['%s', '%s', '%d', '%s', '%f', '%s', '%s', '%s', '%s'];
        
        if (isset($_POST['id']) && $_POST['id']) {
            // UPDATE
            $id = intval($_POST['id']);
            $result = $wpdb->update($table, $data, ['id' => $id], $formats, ['%d']);
            
            if ($result === false) {
                error_log('AFSAR Hotel Update Error: ' . $wpdb->last_error);
                wp_redirect(admin_url('admin.php?page=asfaar-travels-hotels&asfaar_error=' . urlencode('Update failed: ' . $wpdb->last_error)));
                exit;
            }
            
            wp_redirect(admin_url('admin.php?page=asfaar-travels-hotels&asfaar_success=Hotel updated successfully!'));
            exit;
        } else {
            // INSERT
            $result = $wpdb->insert($table, $data, $formats);
            
            if ($result === false) {
                error_log('AFSAR Hotel Insert Error: ' . $wpdb->last_error);
                error_log('AFSAR Hotel Data: ' . print_r($data, true));
                wp_redirect(admin_url('admin.php?page=asfaar-travels-hotels&asfaar_error=' . urlencode('Insert failed: ' . $wpdb->last_error)));
                exit;
            }
            
            $insert_id = $wpdb->insert_id;
            error_log('AFSAR Hotel Created: ID ' . $insert_id);
            
            wp_redirect(admin_url('admin.php?page=asfaar-travels-hotels&asfaar_success=Hotel created successfully! ID: ' . $insert_id));
            exit;
        }
    }
    
    public function delete_hotel() {
        if (!current_user_can('manage_options')) wp_die('Unauthorized');
        
        $id = intval($_GET['id']);
        if (!wp_verify_nonce($_GET['_wpnonce'], 'asfaar_travels_delete_hotel_' . $id)) {
            wp_die('Security check failed');
        }
        
        global $wpdb;
        $result = $wpdb->delete($wpdb->prefix . 'asfaar_travels_hotels', ['id' => $id], ['%d']);
        
        if ($result === false) {
            wp_redirect(admin_url('admin.php?page=asfaar-travels-hotels&asfaar_error=Delete failed'));
            exit;
        }
        
        wp_redirect(admin_url('admin.php?page=asfaar-travels-hotels&asfaar_success=Hotel deleted successfully!'));
        exit;
    }
    
    // SAVE TRANSPORT
    public function save_transport() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'asfaar_travels_save_transport')) {
            wp_die('Security check failed');
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'asfaar_travels_transports';
        
        $data = [
            'name' => sanitize_text_field($_POST['name']),
            'type' => sanitize_text_field($_POST['type']),
            'description' => sanitize_text_field($_POST['description']),
            'price_per_person' => floatval($_POST['price_per_person']),
            'capacity' => intval($_POST['capacity']),
            'icon' => sanitize_text_field($_POST['icon']),
            'status' => sanitize_text_field($_POST['status'])
        ];
        
        $formats = ['%s', '%s', '%s', '%f', '%d', '%s', '%s'];
        
        if (isset($_POST['id']) && $_POST['id']) {
            $id = intval($_POST['id']);
            $result = $wpdb->update($table, $data, ['id' => $id], $formats, ['%d']);
            $msg = 'updated';
        } else {
            $result = $wpdb->insert($table, $data, $formats);
            $msg = 'created';
        }
        
        if ($result === false) {
            wp_redirect(admin_url('admin.php?page=asfaar-travels-transport&asfaar_error=' . urlencode($wpdb->last_error)));
            exit;
        }
        
        wp_redirect(admin_url('admin.php?page=asfaar-travels-transport&asfaar_success=Transport ' . $msg . ' successfully!'));
        exit;
    }
    
    public function delete_transport() {
        if (!current_user_can('manage_options')) wp_die('Unauthorized');
        
        $id = intval($_GET['id']);
        if (!wp_verify_nonce($_GET['_wpnonce'], 'asfaar_travels_delete_transport_' . $id)) {
            wp_die('Security check failed');
        }
        
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'asfaar_travels_transports', ['id' => $id], ['%d']);
        
        wp_redirect(admin_url('admin.php?page=asfaar-travels-transport&asfaar_success=Transport deleted successfully!'));
        exit;
    }
    
    // DATABASE REPAIR
    public function repair_database() {
        if (!current_user_can('manage_options')) wp_die('Unauthorized');
        
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'afsar_repair_db')) {
            wp_die('Security check failed');
        }
        
        Activator::repair_database();
        
        wp_redirect(admin_url('admin.php?page=asfaar-travels-repair&asfaar_success=Database repaired successfully!'));
        exit;
    }
    
    // TEST INSERT
    public function test_insert() {
        if (!current_user_can('manage_options')) wp_die('Unauthorized');
        
        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'afsar_test_insert')) {
            wp_die('Security check failed');
        }
        
        global $wpdb;
        
        // Test package insert
        $result = $wpdb->insert(
            $wpdb->prefix . 'asfaar_travels_packages',
            [
                'package_name' => 'TEST Package',
                'description' => 'Test description',
                'price' => 1000.00,
                'duration' => '7 Days',
                'category' => 'Economy',
                'status' => 'active'
            ],
            ['%s', '%s', '%f', '%s', '%s', '%s']
        );
        
        if ($result === false) {
            wp_redirect(admin_url('admin.php?page=asfaar-travels-repair&asfaar_error=Test insert failed: ' . urlencode($wpdb->last_error)));
            exit;
        }
        
        $id = $wpdb->insert_id;
        wp_redirect(admin_url('admin.php?page=asfaar-travels-repair&asfaar_success=Test package created with ID: ' . $id));
        exit;
    }
    
    // AJAX BOOKING
    public function ajax_complete_booking() {
        check_ajax_referer('afsar_booking', 'nonce');
        
        $booking_data = json_decode(stripslashes($_POST['booking_data']), true);
        $reference = 'UMR-' . strtoupper(substr(md5(time() . rand()), 0, 8));
        
        $package_cost = $booking_data['package']['price'] * ($booking_data['persons']['adults'] + $booking_data['persons']['children'] * 0.7);
        $hotel_cost = ($booking_data['hotels']['makkah']['price'] * 7) + ($booking_data['hotels']['madinah']['price'] * 7);
        $total_persons = $booking_data['persons']['adults'] + $booking_data['persons']['children'] + $booking_data['persons']['infants'];
        $transport_cost = $booking_data['transport']['price'] * $total_persons;
        $grand_total = $package_cost + $hotel_cost + $transport_cost;
        
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'asfaar_travels_bookings',
            [
                'booking_reference' => $reference,
                'package_id' => $booking_data['package']['id'],
                'customer_name' => $booking_data['travelers'][0]['name'],
                'customer_email' => $booking_data['travelers'][0]['email'],
                'customer_phone' => $booking_data['travelers'][0]['phone'],
                'adults' => $booking_data['persons']['adults'],
                'children' => $booking_data['persons']['children'],
                'infants' => $booking_data['persons']['infants'],
                'total_price' => $grand_total,
                'booking_data' => json_encode($booking_data),
                'status' => 'pending'
            ],
            ['%s', '%d', '%s', '%s', '%s', '%d', '%d', '%d', '%f', '%s', '%s']
        );
        
        if ($result === false) {
            wp_send_json_error(['message' => 'Failed to save booking: ' . $wpdb->last_error]);
            return;
        }
        
        $admin_email = get_option('afsar_admin_email', get_option('admin_email'));
        $subject = 'New Booking: ' . $reference;
        $message = $this->generate_email_html($booking_data, $reference, $grand_total);
        $headers = ['Content-Type: text/html; charset=UTF-8'];
        
        wp_mail($admin_email, $subject, $message, $headers);
        wp_mail($booking_data['travelers'][0]['email'], 'Booking Confirmation: ' . $reference, $message, $headers);
        
        wp_send_json_success([
            'reference' => $reference,
            'total' => number_format($grand_total, 2),
            'message' => 'Booking completed successfully!'
        ]);
    }
    
    private function generate_email_html($data, $ref, $total) {
        ob_start();
        ?>
        <html><body style="font-family: Arial, sans-serif; padding: 20px;">
        <div style="max-width: 600px; margin: 0 auto; background: #f9f9f9; padding: 30px; border-radius: 10px;">
            <h2 style="color: #3055FF; text-align: center;">Booking Confirmation</h2>
            <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <p style="font-size: 18px;"><strong>Reference:</strong> <?php echo $ref; ?></p>
                <hr style="border: 1px solid #eee;">
                <p><strong>Package:</strong> <?php echo $data['package']['name']; ?></p>
                <p><strong>Travelers:</strong> <?php echo $data['persons']['adults']; ?> Adults, <?php echo $data['persons']['children']; ?> Children, <?php echo $data['persons']['infants']; ?> Infants</p>
                <p><strong>Hotels:</strong></p>
                <ul>
                    <li>Makkah: <?php echo $data['hotels']['makkah']['name']; ?></li>
                    <li>Madinah: <?php echo $data['hotels']['madinah']['name']; ?></li>
                </ul>
                <p><strong>Transport:</strong> <?php echo $data['transport']['name']; ?></p>
            </div>
            <div style="background: #3055FF; color: white; padding: 20px; text-align: center; border-radius: 8px;">
                <h3 style="margin: 0;">TOTAL: SAR <?php echo number_format($total, 2); ?></h3>
            </div>
            <p style="text-align: center; color: #666; margin-top: 20px;">Thank you for booking with TFC Tours!</p>
        </div>
        </body></html>
        <?php
        return ob_get_clean();
    }
}
