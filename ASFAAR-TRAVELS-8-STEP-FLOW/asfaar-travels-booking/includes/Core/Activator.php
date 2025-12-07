<?php
namespace AsfaarTravels\Core;

if (!defined('ABSPATH')) exit;

class Activator {
    
    public static function activate() {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $charset = $wpdb->get_charset_collate();
        
        // Packages table WITH image_url
        $table = $wpdb->prefix . 'asfaar_travels_packages';
        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            package_name varchar(255) NOT NULL,
            description longtext,
            price decimal(10,2) NOT NULL DEFAULT 0.00,
            duration varchar(100) DEFAULT '',
            category varchar(100) DEFAULT 'Economy',
            itinerary longtext,
            inclusions longtext,
            exclusions longtext,
            image_url varchar(500) DEFAULT '',
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset;";
        dbDelta($sql);
        
        // Hotels table WITH multiple room types
        $table = $wpdb->prefix . 'asfaar_travels_hotels';
        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            city varchar(100) NOT NULL,
            star_rating int(11) DEFAULT 3,
            distance varchar(100) DEFAULT '',
            source varchar(100) DEFAULT '',
            price_sharing decimal(10,2) DEFAULT 0.00,
            price_triple decimal(10,2) DEFAULT 0.00,
            price_double decimal(10,2) DEFAULT 0.00,
            price_room decimal(10,2) DEFAULT 0.00,
            price_per_night decimal(10,2) DEFAULT 0.00,
            description longtext,
            amenities longtext,
            image_url varchar(500) DEFAULT '',
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset;";
        dbDelta($sql);
        
        // Transports table
        $table = $wpdb->prefix . 'asfaar_travels_transports';
        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            type varchar(100) NOT NULL,
            description text,
            price_per_person decimal(10,2) NOT NULL,
            capacity int(11) DEFAULT 0,
            icon varchar(10) DEFAULT '🚗',
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset;";
        dbDelta($sql);
        
        // Add default transports
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}afsar_transports");
        if ($count == 0) {
            $transports = [
                ['Private Car', 'car', 'Comfortable private vehicle', 800, 4, '🚗'],
                ['Shared Car', 'car', 'Economical shared vehicle', 500, 4, '🚙'],
                ['Standard Bus', 'bus', 'Comfortable bus service', 150, 50, '🚌'],
                ['VIP Bus', 'bus', 'Luxury bus experience', 250, 30, '🚐'],
                ['Hiace Van', 'van', 'Perfect for small groups', 350, 15, '🚐'],
                ['Coaster', 'bus', 'Medium-sized coach', 400, 25, '🚌']
            ];
            
            foreach ($transports as $t) {
                $wpdb->insert("{$wpdb->prefix}afsar_transports", [
                    'name' => $t[0], 'type' => $t[1], 'description' => $t[2],
                    'price_per_person' => $t[3], 'capacity' => $t[4], 'icon' => $t[5], 'status' => 'active'
                ], ['%s', '%s', '%s', '%f', '%d', '%s', '%s']);
            }
        }
        
        // Bookings table
        $table = $wpdb->prefix . 'asfaar_travels_bookings';
        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            booking_reference varchar(50) NOT NULL,
            package_id bigint(20),
            customer_name varchar(255) NOT NULL,
            customer_email varchar(255) NOT NULL,
            customer_phone varchar(50),
            adults int(11) DEFAULT 1,
            children int(11) DEFAULT 0,
            infants int(11) DEFAULT 0,
            visa_cost decimal(10,2) DEFAULT 0.00,
            flight_cost decimal(10,2) DEFAULT 0.00,
            total_price decimal(10,2) DEFAULT 0.00,
            total_price_pkr decimal(10,2) DEFAULT 0.00,
            sar_to_pkr_rate decimal(10,4) DEFAULT 74.50,
            booking_data longtext,
            pdf_path varchar(500),
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY booking_reference (booking_reference)
        ) $charset;";
        dbDelta($sql);
        
        // Visa Pricing Settings
        add_option('asfaar_visa_group_10_20', 820); // 10-20 persons
        add_option('asfaar_visa_group_3_9', 850);   // 3-9 persons
        add_option('asfaar_visa_2_persons', 880);   // 2 persons
        add_option('asfaar_visa_1_person', 980);    // 1 person
        add_option('asfaar_visa_child', 720);       // Child (2-11 years)
        add_option('asfaar_visa_infant', 670);      // Infant (1-2 years)
        
        // Currency Settings
        add_option('asfaar_currency', 'SAR');
        add_option('asfaar_sar_to_pkr_rate', 74.50); // Default rate
        add_option('asfaar_auto_currency_update', 'yes');
        
        // Flight Settings
        add_option('asfaar_enable_flights', 'yes');
        add_option('asfaar_admin_email', get_option('admin_email'));
        
        // Terms & Conditions
        $terms = "1. 100% payment should be received in advance.\n";
        $terms .= "2. Packages are non-refundable.\n";
        $terms .= "3. Passenger can stay Maximum of 30 Days in KSA.\n";
        $terms .= "4. Incase passenger got overstay above visa duration SR 25,000 per person will be charged.\n";
        $terms .= "5. Hotel Vouchers should be created prior to 3 days of travelling.\n";
        $terms .= "6. Send to Embassy cases will be charged as per Market Rate.\n";
        $terms .= "7. Any other Tax imposed by KSA/PAK Govt. will be charged accordingly.";
        add_option('asfaar_terms_conditions', $terms);
        
        flush_rewrite_rules();
    }
    
    public static function repair_database() {
        global $wpdb;
        $tables = ['afsar_packages', 'afsar_hotels', 'afsar_transports', 'afsar_bookings'];
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}$table");
        }
        self::activate();
        return true;
    }
}
