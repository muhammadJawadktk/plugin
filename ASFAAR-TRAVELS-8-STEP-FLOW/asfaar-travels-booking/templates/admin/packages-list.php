<?php
if (!defined('ABSPATH')) exit;
global $wpdb;
$packages = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}afsar_packages ORDER BY id DESC");
$message = isset($_GET['message']) ? $_GET['message'] : '';
?>
<div class="wrap">
    <h1 class="wp-heading-inline">Packages</h1>
    <a href="?page=asfaar-travels-packages&action=add" class="page-title-action">Add New</a>
    <hr class="wp-header-end">
    
    <?php if ($message): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php 
                if ($message == 'created') echo 'Package created successfully!';
                elseif ($message == 'updated') echo 'Package updated successfully!';
                elseif ($message == 'deleted') echo 'Package deleted successfully!';
            ?></p>
        </div>
    <?php endif; ?>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Duration</th>
                <th>Category</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($packages)): ?>
                <tr><td colspan="6">No packages found. <a href="?page=asfaar-travels-packages&action=add">Add your first package</a></td></tr>
            <?php else: ?>
                <?php foreach ($packages as $pkg): ?>
                    <tr>
                        <td><strong><?php echo esc_html($pkg->package_name); ?></strong></td>
                        <td>SAR <?php echo number_format($pkg->price, 2); ?></td>
                        <td><?php echo esc_html($pkg->duration); ?></td>
                        <td><?php echo esc_html($pkg->category); ?></td>
                        <td><?php echo esc_html($pkg->status); ?></td>
                        <td>
                            <a href="?page=asfaar-travels-packages&action=edit&id=<?php echo $pkg->id; ?>">Edit</a> |
                            <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=asfaar_travels_delete_package&id=' . $pkg->id), 'asfaar_travels_delete_package_' . $pkg->id); ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
