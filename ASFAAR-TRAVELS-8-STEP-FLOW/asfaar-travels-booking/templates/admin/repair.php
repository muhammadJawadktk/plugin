<?php
if (!defined('ABSPATH')) exit;
global $wpdb;

// Get table status
$tables = [
    'packages' => $wpdb->prefix . 'asfaar_travels_packages',
    'hotels' => $wpdb->prefix . 'asfaar_travels_hotels',
    'transports' => $wpdb->prefix . 'asfaar_travels_transports',
    'bookings' => $wpdb->prefix . 'asfaar_travels_bookings'
];

$status = [];
foreach ($tables as $key => $table) {
    $exists = $wpdb->get_var("SHOW TABLES LIKE '$table'") === $table;
    $count = $exists ? $wpdb->get_var("SELECT COUNT(*) FROM $table") : 0;
    $status[$key] = ['exists' => $exists, 'count' => $count, 'table' => $table];
}
?>
<div class="wrap">
    <h1>Database Repair Tool</h1>
    
    <div class="notice notice-info">
        <p><strong>Use this tool if:</strong></p>
        <ul>
            <li>Packages or hotels are not saving</li>
            <li>Tables show 0 items but you added data</li>
            <li>You see database errors</li>
        </ul>
    </div>
    
    <h2>Database Status</h2>
    <table class="wp-list-table widefat fixed striped" style="max-width: 800px;">
        <thead>
            <tr>
                <th>Table</th>
                <th>Full Name</th>
                <th>Exists</th>
                <th>Records</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($status as $key => $info): ?>
            <tr>
                <td><strong><?php echo ucfirst($key); ?></strong></td>
                <td><code><?php echo $info['table']; ?></code></td>
                <td>
                    <?php if ($info['exists']): ?>
                        <span style="color: green;">✓ Yes</span>
                    <?php else: ?>
                        <span style="color: red;">✗ No</span>
                    <?php endif; ?>
                </td>
                <td><?php echo $info['count']; ?></td>
                <td>
                    <?php if ($info['exists']): ?>
                        <span style="color: green;">OK</span>
                    <?php else: ?>
                        <span style="color: red;">MISSING</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <h2>Repair Database</h2>
    <div class="card" style="max-width: 800px;">
        <h3>Option 1: Repair Database (Recommended)</h3>
        <p>This will recreate all tables. <strong>Warning: This will delete all existing data!</strong></p>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" onsubmit="return confirm('Are you sure? This will delete all packages, hotels, and bookings!');">
            <input type="hidden" name="action" value="asfaar_travels_repair_database">
            <?php wp_nonce_field('afsar_repair_db'); ?>
            <button type="submit" class="button button-primary button-large">Repair Database</button>
        </form>
    </div>
    
    <div class="card" style="max-width: 800px; margin-top: 20px;">
        <h3>Option 2: Test Database Insert</h3>
        <p>This will try to insert a test package to verify database is working.</p>
        <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=asfaar_travels_test_insert'), 'asfaar_travels_test_insert'); ?>" class="button button-secondary button-large">Test Insert</a>
    </div>
    
    <h2>Manual Verification</h2>
    <div class="card" style="max-width: 800px;">
        <h3>Check Your Database Manually</h3>
        <ol>
            <li>Go to phpMyAdmin</li>
            <li>Select your WordPress database</li>
            <li>Look for these tables:
                <ul>
                    <?php foreach ($status as $info): ?>
                    <li><code><?php echo $info['table']; ?></code></li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <li>If tables don't exist, click "Repair Database" above</li>
            <li>If tables exist but data is not saving, check error logs</li>
        </ol>
    </div>
    
    <h2>WordPress Debug Log</h2>
    <div class="card" style="max-width: 800px;">
        <h3>Enable Debug Mode</h3>
        <p>To see detailed error messages, enable WordPress debug mode:</p>
        <ol>
            <li>Edit <code>wp-config.php</code></li>
            <li>Add these lines before "That's all, stop editing!":<br>
                <code>
                    define('WP_DEBUG', true);<br>
                    define('WP_DEBUG_LOG', true);<br>
                    define('WP_DEBUG_DISPLAY', false);
                </code>
            </li>
            <li>Try adding a package again</li>
            <li>Check <code>wp-content/debug.log</code> for errors</li>
        </ol>
    </div>
    
    <h2>Shortcode Information</h2>
    <div class="card" style="max-width: 800px;">
        <h3>Use This Shortcode</h3>
        <p><strong>New Shortcode:</strong> <code>[AFSAR_UMRAH]</code></p>
        <p>Add this to any page to display the booking form.</p>
        <p><strong>Note:</strong> Old shortcode <code>[AFSAR_UMRAH]</code> will NOT work!</p>
    </div>
</div>

<style>
.card {
    background: white;
    padding: 20px;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    margin-bottom: 20px;
}
.card h3 {
    margin-top: 0;
}
.button-large {
    height: auto;
    padding: 10px 20px;
    font-size: 14px;
}
</style>
