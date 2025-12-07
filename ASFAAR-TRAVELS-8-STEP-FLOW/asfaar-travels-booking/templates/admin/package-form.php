<?php
if (!defined('ABSPATH')) exit;
global $wpdb;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$package = $id ? $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}afsar_packages WHERE id = %d", $id)) : null;
$is_edit = $package ? true : false;
?>
<div class="wrap">
    <h1><?php echo $is_edit ? 'Edit Package' : 'Add Package'; ?></h1>
    
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="asfaar_travels_save_package">
        <?php wp_nonce_field('asfaar_travels_save_package'); ?>
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?php echo $package->id; ?>">
        <?php endif; ?>
        
        <table class="form-table">
            <tr>
                <th><label for="package_name">Package Name *</label></th>
                <td><input type="text" name="package_name" id="package_name" class="regular-text" value="<?php echo $is_edit ? esc_attr($package->package_name) : ''; ?>" required></td>
            </tr>
            <tr>
                <th><label for="image_url">Package Image</label></th>
                <td>
                    <div class="afsar-image-wrapper">
                        <input type="hidden" name="image_url" class="afsar-image-url" value="<?php echo $is_edit && !empty($package->image_url) ? esc_attr($package->image_url) : ''; ?>">
                        <div class="afsar-image-preview" style="<?php echo ($is_edit && !empty($package->image_url)) ? '' : 'display:none;'; ?>">
                            <?php if ($is_edit && !empty($package->image_url)): ?>
                                <img src="<?php echo esc_url($package->image_url); ?>" style="max-width:300px;height:auto;border:1px solid #ddd;border-radius:4px;padding:5px;display:block;margin-bottom:10px;">
                            <?php endif; ?>
                        </div>
                        <button type="button" class="button afsar-upload-image-btn"><?php echo ($is_edit && !empty($package->image_url)) ? 'Change Image' : 'Upload Image'; ?></button>
                        <button type="button" class="button afsar-remove-image-btn" style="<?php echo ($is_edit && !empty($package->image_url)) ? '' : 'display:none;'; ?>">Remove Image</button>
                        <p class="description">Upload an image for this package (optional)</p>
                    </div>
                </td>
            </tr>
            <tr>
                <th><label for="description">Description</label></th>
                <td><textarea name="description" id="description" rows="4" class="large-text"><?php echo $is_edit ? esc_textarea($package->description) : ''; ?></textarea></td>
            </tr>
            <tr>
                <th><label for="price">Price (SAR) *</label></th>
                <td><input type="number" step="0.01" name="price" id="price" value="<?php echo $is_edit ? $package->price : ''; ?>" required></td>
            </tr>
            <tr>
                <th><label for="duration">Duration</label></th>
                <td><input type="text" name="duration" id="duration" placeholder="e.g., 7 Days / 6 Nights" value="<?php echo $is_edit ? esc_attr($package->duration) : ''; ?>"></td>
            </tr>
            <tr>
                <th><label for="category">Category</label></th>
                <td>
                    <select name="category" id="category">
                        <option value="Economy" <?php echo ($is_edit && $package->category == 'Economy') ? 'selected' : ''; ?>>Economy</option>
                        <option value="Standard" <?php echo ($is_edit && $package->category == 'Standard') ? 'selected' : ''; ?>>Standard</option>
                        <option value="Premium" <?php echo ($is_edit && $package->category == 'Premium') ? 'selected' : ''; ?>>Premium</option>
                        <option value="Luxury" <?php echo ($is_edit && $package->category == 'Luxury') ? 'selected' : ''; ?>>Luxury</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="itinerary">Itinerary</label></th>
                <td><textarea name="itinerary" id="itinerary" rows="6" class="large-text"><?php echo $is_edit ? esc_textarea($package->itinerary) : ''; ?></textarea></td>
            </tr>
            <tr>
                <th><label for="inclusions">Inclusions</label></th>
                <td><textarea name="inclusions" id="inclusions" rows="4" class="large-text"><?php echo $is_edit ? esc_textarea($package->inclusions) : ''; ?></textarea></td>
            </tr>
            <tr>
                <th><label for="exclusions">Exclusions</label></th>
                <td><textarea name="exclusions" id="exclusions" rows="4" class="large-text"><?php echo $is_edit ? esc_textarea($package->exclusions) : ''; ?></textarea></td>
            </tr>
            <tr>
                <th><label for="status">Status</label></th>
                <td>
                    <select name="status" id="status">
                        <option value="active" <?php echo ($is_edit && $package->status == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($is_edit && $package->status == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" class="button button-primary" value="<?php echo $is_edit ? 'Update Package' : 'Create Package'; ?>">
            <a href="?page=asfaar-travels-packages" class="button">Cancel</a>
        </p>
    </form>
</div>
