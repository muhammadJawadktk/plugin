<?php
if (!defined('ABSPATH')) exit;
global $wpdb;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$hotel = $id ? $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}afsar_hotels WHERE id = %d", $id)) : null;
$is_edit = $hotel ? true : false;
?>
<div class="wrap">
    <h1><?php echo $is_edit ? 'Edit Hotel' : 'Add Hotel'; ?></h1>
    
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="asfaar_travels_save_hotel">
        <?php wp_nonce_field('asfaar_travels_save_hotel'); ?>
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?php echo $hotel->id; ?>">
        <?php endif; ?>
        
        <table class="form-table">
            <tr>
                <th><label for="name">Hotel Name *</label></th>
                <td><input type="text" name="name" id="name" class="regular-text" value="<?php echo $is_edit ? esc_attr($hotel->name) : ''; ?>" required></td>
            </tr>
            <tr>
                <th><label for="image_url">Hotel Image</label></th>
                <td>
                    <div class="afsar-image-wrapper">
                        <input type="hidden" name="image_url" class="afsar-image-url" value="<?php echo $is_edit && !empty($hotel->image_url) ? esc_attr($hotel->image_url) : ''; ?>">
                        <div class="afsar-image-preview" style="<?php echo ($is_edit && !empty($hotel->image_url)) ? '' : 'display:none;'; ?>">
                            <?php if ($is_edit && !empty($hotel->image_url)): ?>
                                <img src="<?php echo esc_url($hotel->image_url); ?>" style="max-width:300px;height:auto;border:1px solid #ddd;border-radius:4px;padding:5px;display:block;margin-bottom:10px;">
                            <?php endif; ?>
                        </div>
                        <button type="button" class="button afsar-upload-image-btn"><?php echo ($is_edit && !empty($hotel->image_url)) ? 'Change Image' : 'Upload Image'; ?></button>
                        <button type="button" class="button afsar-remove-image-btn" style="<?php echo ($is_edit && !empty($hotel->image_url)) ? '' : 'display:none;'; ?>">Remove Image</button>
                        <p class="description">Upload an image for this hotel (optional)</p>
                    </div>
                </td>
            </tr>
            <tr>
                <th><label for="city">City *</label></th>
                <td>
                    <select name="city" id="city" required>
                        <option value="">Select City</option>
                        <option value="Makkah" <?php echo ($is_edit && $hotel->city == 'Makkah') ? 'selected' : ''; ?>>Makkah</option>
                        <option value="Madinah" <?php echo ($is_edit && $hotel->city == 'Madinah') ? 'selected' : ''; ?>>Madinah</option>
                        <option value="Jeddah" <?php echo ($is_edit && $hotel->city == 'Jeddah') ? 'selected' : ''; ?>>Jeddah</option>
                    </select>
                    <p class="description">Important: City must be exact (Makkah or Madinah)</p>
                </td>
            </tr>
            <tr>
                <th><label for="star_rating">Star Rating</label></th>
                <td>
                    <select name="star_rating" id="star_rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo ($is_edit && $hotel->star_rating == $i) ? 'selected' : ''; ?>><?php echo $i; ?> Star<?php echo $i > 1 ? 's' : ''; ?></option>
                        <?php endfor; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="distance">Distance from Haram</label></th>
                <td><input type="text" name="distance" id="distance" placeholder="e.g., 200m, Walking distance" value="<?php echo $is_edit ? esc_attr($hotel->distance) : ''; ?>"></td>
            </tr>
            <tr>
                <th><label for="price_per_night">Price per Night (SAR) *</label></th>
                <td><input type="number" step="0.01" name="price_per_night" id="price_per_night" value="<?php echo $is_edit ? $hotel->price_per_night : ''; ?>" required></td>
            </tr>
            <tr>
                <th><label for="description">Description</label></th>
                <td><textarea name="description" id="description" rows="4" class="large-text"><?php echo $is_edit ? esc_textarea($hotel->description) : ''; ?></textarea></td>
            </tr>
            <tr>
                <th><label for="amenities">Amenities</label></th>
                <td><textarea name="amenities" id="amenities" rows="3" class="large-text" placeholder="WiFi, Breakfast, Pool, etc."><?php echo $is_edit ? esc_textarea($hotel->amenities) : ''; ?></textarea></td>
            </tr>
            <tr>
                <th><label for="status">Status</label></th>
                <td>
                    <select name="status" id="status">
                        <option value="active" <?php echo ($is_edit && $hotel->status == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($is_edit && $hotel->status == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" class="button button-primary" value="<?php echo $is_edit ? 'Update Hotel' : 'Create Hotel'; ?>">
            <a href="?page=asfaar-travels-hotels" class="button">Cancel</a>
        </p>
    </form>
</div>
