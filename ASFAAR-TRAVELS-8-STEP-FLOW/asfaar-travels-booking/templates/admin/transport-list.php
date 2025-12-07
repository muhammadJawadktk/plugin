<?php
if (!defined('ABSPATH')) exit;
global $wpdb;
$transports = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}afsar_transports ORDER BY name");
$message = isset($_GET['message']) ? $_GET['message'] : '';
?>
<div class="wrap">
    <h1 class="wp-heading-inline">Transport Options</h1>
    <a href="?page=asfaar-travels-transport&action=add" class="page-title-action">Add New</a>
    <hr class="wp-header-end">
    
    <?php if ($message): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php 
                if ($message == 'created') echo 'Transport created successfully!';
                elseif ($message == 'updated') echo 'Transport updated successfully!';
                elseif ($message == 'deleted') echo 'Transport deleted successfully!';
            ?></p>
        </div>
    <?php endif; ?>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width:50px;">Icon</th>
                <th>Name</th>
                <th>Type</th>
                <th>Price/Person</th>
                <th>Capacity</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transports)): ?>
                <tr><td colspan="7">No transport options found.</td></tr>
            <?php else: ?>
                <?php foreach ($transports as $t): ?>
                    <tr>
                        <td style="font-size:28px;"><?php echo esc_html($t->icon); ?></td>
                        <td><strong><?php echo esc_html($t->name); ?></strong></td>
                        <td><?php echo ucfirst(esc_html($t->type)); ?></td>
                        <td>SAR <?php echo number_format($t->price_per_person, 2); ?></td>
                        <td><?php echo $t->capacity; ?> persons</td>
                        <td><?php echo esc_html($t->status); ?></td>
                        <td>
                            <a href="?page=asfaar-travels-transport&action=edit&id=<?php echo $t->id; ?>">Edit</a> |
                            <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=asfaar_travels_delete_transport&id=' . $t->id), 'asfaar_travels_delete_transport_' . $t->id); ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
