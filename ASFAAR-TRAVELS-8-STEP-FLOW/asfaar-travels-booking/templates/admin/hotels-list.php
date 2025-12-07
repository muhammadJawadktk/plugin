<?php
if (!defined('ABSPATH')) exit;
global $wpdb;
$hotels = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}afsar_hotels ORDER BY city, name");
$message = isset($_GET['message']) ? $_GET['message'] : '';
?>
<div class="wrap">
    <h1 class="wp-heading-inline">Hotels</h1>
    <a href="?page=asfaar-travels-hotels&action=add" class="page-title-action">Add New</a>
    <hr class="wp-header-end">
    
    <?php if ($message): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php 
                if ($message == 'created') echo 'Hotel created successfully!';
                elseif ($message == 'updated') echo 'Hotel updated successfully!';
                elseif ($message == 'deleted') echo 'Hotel deleted successfully!';
            ?></p>
        </div>
    <?php endif; ?>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>City</th>
                <th>Stars</th>
                <th>Price/Night</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($hotels)): ?>
                <tr><td colspan="6">No hotels found. <a href="?page=asfaar-travels-hotels&action=add">Add your first hotel</a></td></tr>
            <?php else: ?>
                <?php foreach ($hotels as $hotel): ?>
                    <tr>
                        <td><strong><?php echo esc_html($hotel->name); ?></strong></td>
                        <td><?php echo esc_html($hotel->city); ?></td>
                        <td><?php echo str_repeat('⭐', $hotel->star_rating); ?></td>
                        <td>SAR <?php echo number_format($hotel->price_per_night, 2); ?></td>
                        <td><?php echo esc_html($hotel->status); ?></td>
                        <td>
                            <a href="?page=asfaar-travels-hotels&action=edit&id=<?php echo $hotel->id; ?>">Edit</a> |
                            <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=asfaar_travels_delete_hotel&id=' . $hotel->id), 'asfaar_travels_delete_hotel_' . $hotel->id); ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
