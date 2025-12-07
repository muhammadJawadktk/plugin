<?php
if (!defined('ABSPATH')) exit;
global $wpdb;
$bookings = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}afsar_bookings ORDER BY id DESC LIMIT 50");
?>
<div class="wrap">
    <h1>Bookings</h1>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Reference</th>
                <th>Customer</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($bookings)): ?>
                <tr><td colspan="7">No bookings yet.</td></tr>
            <?php else: ?>
                <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td><strong><?php echo esc_html($b->booking_reference); ?></strong></td>
                        <td><?php echo esc_html($b->customer_name); ?></td>
                        <td><?php echo esc_html($b->customer_email); ?></td>
                        <td><?php echo esc_html($b->customer_phone); ?></td>
                        <td>SAR <?php echo number_format($b->total_price, 2); ?></td>
                        <td><?php echo esc_html($b->status); ?></td>
                        <td><?php echo date('M d, Y', strtotime($b->created_at)); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
