<?php
if (!defined('ABSPATH')) exit;
global $wpdb;
$stats = [
    'packages' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}afsar_packages"),
    'hotels' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}afsar_hotels"),
    'transports' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}afsar_transports"),
    'bookings' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}afsar_bookings")
];
?>
<div class="wrap">
    <h1>TFC Booking Dashboard</h1>
    
    <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:20px; margin:30px 0;">
        <div style="background:#fff; padding:20px; border-left:4px solid #3055FF; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin:0; color:#666;">Packages</h3>
            <p style="font-size:32px; font-weight:bold; margin:10px 0; color:#3055FF;"><?php echo $stats['packages']; ?></p>
        </div>
        <div style="background:#fff; padding:20px; border-left:4px solid #FFB830; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin:0; color:#666;">Hotels</h3>
            <p style="font-size:32px; font-weight:bold; margin:10px 0; color:#FFB830;"><?php echo $stats['hotels']; ?></p>
        </div>
        <div style="background:#fff; padding:20px; border-left:4px solid #28a745; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin:0; color:#666;">Transport Options</h3>
            <p style="font-size:32px; font-weight:bold; margin:10px 0; color:#28a745;"><?php echo $stats['transports']; ?></p>
        </div>
        <div style="background:#fff; padding:20px; border-left:4px solid #dc3545; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin:0; color:#666;">Bookings</h3>
            <p style="font-size:32px; font-weight:bold; margin:10px 0; color:#dc3545;"><?php echo $stats['bookings']; ?></p>
        </div>
    </div>
    
    <div style="background:#fff; padding:20px; margin-top:20px;">
        <h2>Quick Links</h2>
        <p><a href="?page=asfaar-travels-packages&action=add" class="button button-primary">Add Package</a> <a href="?page=asfaar-travels-hotels&action=add" class="button button-primary">Add Hotel</a> <a href="?page=asfaar-travels-transport&action=add" class="button button-primary">Add Transport</a></p>
    </div>
</div>
