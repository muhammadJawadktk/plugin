<?php
if (!defined('ABSPATH')) exit;
global $wpdb;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$transport = $id ? $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}afsar_transports WHERE id = %d", $id)) : null;
$is_edit = $transport ? true : false;
?>
<div class="wrap">
    <h1><?php echo $is_edit ? 'Edit Transport' : 'Add Transport'; ?></h1>
    
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="asfaar_travels_save_transport">
        <?php wp_nonce_field('asfaar_travels_save_transport'); ?>
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?php echo $transport->id; ?>">
        <?php endif; ?>
        
        <table class="form-table">
            <tr>
                <th><label for="name">Transport Name *</label></th>
                <td><input type="text" name="name" id="name" class="regular-text" value="<?php echo $is_edit ? esc_attr($transport->name) : ''; ?>" required></td>
            </tr>
            <tr>
                <th><label for="type">Type *</label></th>
                <td>
                    <select name="type" id="type" required>
                        <option value="">Select Type</option>
                        <option value="car" <?php echo ($is_edit && $transport->type == 'car') ? 'selected' : ''; ?>>Car</option>
                        <option value="van" <?php echo ($is_edit && $transport->type == 'van') ? 'selected' : ''; ?>>Van</option>
                        <option value="bus" <?php echo ($is_edit && $transport->type == 'bus') ? 'selected' : ''; ?>>Bus</option>
                        <option value="coach" <?php echo ($is_edit && $transport->type == 'coach') ? 'selected' : ''; ?>>Coach</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="description">Description</label></th>
                <td><textarea name="description" id="description" rows="3" class="large-text"><?php echo $is_edit ? esc_textarea($transport->description) : ''; ?></textarea></td>
            </tr>
            <tr>
                <th><label for="price_per_person">Price per Person (SAR) *</label></th>
                <td><input type="number" step="0.01" name="price_per_person" id="price_per_person" value="<?php echo $is_edit ? $transport->price_per_person : ''; ?>" required></td>
            </tr>
            <tr>
                <th><label for="capacity">Capacity *</label></th>
                <td><input type="number" name="capacity" id="capacity" value="<?php echo $is_edit ? $transport->capacity : ''; ?>" required> <span class="description">Maximum number of passengers</span></td>
            </tr>
            <tr>
                <th><label for="icon">Icon</label></th>
                <td>
                    <input type="text" name="icon" id="icon" value="<?php echo $is_edit ? esc_attr($transport->icon) : '🚗'; ?>" style="font-size:24px; width:60px;">
                    <p class="description">Choose emoji: 🚗 🚙 🚌 🚐 🚕 🚖 🚎 🛻</p>
                </td>
            </tr>
            <tr>
                <th><label for="status">Status</label></th>
                <td>
                    <select name="status" id="status">
                        <option value="active" <?php echo ($is_edit && $transport->status == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($is_edit && $transport->status == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" class="button button-primary" value="<?php echo $is_edit ? 'Update Transport' : 'Create Transport'; ?>">
            <a href="?page=asfaar-travels-transport" class="button">Cancel</a>
        </p>
    </form>
</div>
