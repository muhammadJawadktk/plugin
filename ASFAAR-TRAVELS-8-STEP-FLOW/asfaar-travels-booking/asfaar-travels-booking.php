<?php
/**
 * Plugin Name: Asfaar Travels Booking System
 * Plugin URI: https://asfaartravels.com
 * Description: Professional Umrah and Tour booking system with multi-step flow, age validation, and flexible skip options
 * Version: 2.0.0
 * Author: Asfaar Travels
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) exit;

define('ASFAAR_TRAVELS_VERSION', '2.0.0');
define('ASFAAR_TRAVELS_PATH', plugin_dir_path(__FILE__));
define('ASFAAR_TRAVELS_URL', plugin_dir_url(__FILE__));

spl_autoload_register(function($class) {
    if (strpos($class, 'AsfaarTravels\\') !== 0) return;
    $class_file = str_replace('AsfaarTravels\\', '', $class);
    $class_file = str_replace('\\', '/', $class_file);
    $file = ASFAAR_TRAVELS_PATH . 'includes/' . $class_file . '.php';
    if (file_exists($file)) require_once $file;
});

register_activation_hook(__FILE__, function() {
    require_once ASFAAR_TRAVELS_PATH . 'includes/Core/Activator.php';
    AsfaarTravels\Core\Activator::activate();
});

add_action('plugins_loaded', function() {
    $plugin = new AsfaarTravels\Core\Plugin();
    $plugin->init();
});
