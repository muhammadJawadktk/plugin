<?php
if (!defined('ABSPATH')) exit;

// Handle settings update
if (isset($_POST['update_settings']) && check_admin_referer('asfaar_settings')) {
    update_option('asfaar_visa_group_10_20', sanitize_text_field($_POST['visa_group_10_20']));
    update_option('asfaar_visa_group_3_9', sanitize_text_field($_POST['visa_group_3_9']));
    update_option('asfaar_visa_2_persons', sanitize_text_field($_POST['visa_2_persons']));
    update_option('asfaar_visa_1_person', sanitize_text_field($_POST['visa_1_person']));
    update_option('asfaar_visa_child', sanitize_text_field($_POST['visa_child']));
    update_option('asfaar_visa_infant', sanitize_text_field($_POST['visa_infant']));
    
    update_option('asfaar_sar_to_pkr_rate', sanitize_text_field($_POST['sar_to_pkr_rate']));
    update_option('asfaar_auto_currency_update', sanitize_text_field($_POST['auto_currency_update']));
    update_option('asfaar_enable_flights', sanitize_text_field($_POST['enable_flights']));
    update_option('asfaar_terms_conditions', wp_kses_post($_POST['terms_conditions']));
    
    echo '<div class="notice notice-success"><p>Settings updated successfully!</p></div>';
}

// Get current settings
$visa_10_20 = get_option('asfaar_visa_group_10_20', 820);
$visa_3_9 = get_option('asfaar_visa_group_3_9', 850);
$visa_2 = get_option('asfaar_visa_2_persons', 880);
$visa_1 = get_option('asfaar_visa_1_person', 980);
$visa_child = get_option('asfaar_visa_child', 720);
$visa_infant = get_option('asfaar_visa_infant', 670);

$sar_to_pkr = get_option('asfaar_sar_to_pkr_rate', 74.50);
$auto_update = get_option('asfaar_auto_currency_update', 'yes');
$enable_flights = get_option('asfaar_enable_flights', 'yes');
$terms = get_option('asfaar_terms_conditions', '');
?>

<div class="wrap">
    <h1>⚙️ Asfaar Travels Settings</h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('asfaar_settings'); ?>
        
        <div style="background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2>💳 Visa Pricing (SAR)</h2>
            <p>Set visa prices based on group size</p>
            
            <table class="form-table">
                <tr>
                    <th>10-20 Persons</th>
                    <td><input type="number" name="visa_group_10_20" value="<?php echo esc_attr($visa_10_20); ?>" step="0.01" class="regular-text"> SAR</td>
                </tr>
                <tr>
                    <th>3-9 Persons</th>
                    <td><input type="number" name="visa_group_3_9" value="<?php echo esc_attr($visa_3_9); ?>" step="0.01" class="regular-text"> SAR</td>
                </tr>
                <tr>
                    <th>2 Persons</th>
                    <td><input type="number" name="visa_2_persons" value="<?php echo esc_attr($visa_2); ?>" step="0.01" class="regular-text"> SAR</td>
                </tr>
                <tr>
                    <th>1 Person</th>
                    <td><input type="number" name="visa_1_person" value="<?php echo esc_attr($visa_1); ?>" step="0.01" class="regular-text"> SAR</td>
                </tr>
                <tr>
                    <th>Child (2-11 years)</th>
                    <td><input type="number" name="visa_child" value="<?php echo esc_attr($visa_child); ?>" step="0.01" class="regular-text"> SAR</td>
                </tr>
                <tr>
                    <th>Infant (1-2 years)</th>
                    <td><input type="number" name="visa_infant" value="<?php echo esc_attr($visa_infant); ?>" step="0.01" class="regular-text"> SAR</td>
                </tr>
            </table>
        </div>
        
        <div style="background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2>💱 Currency Settings</h2>
            
            <table class="form-table">
                <tr>
                    <th>SAR to PKR Rate</th>
                    <td>
                        <input type="number" name="sar_to_pkr_rate" value="<?php echo esc_attr($sar_to_pkr); ?>" step="0.0001" class="regular-text">
                        <p class="description">Current exchange rate: 1 SAR = <?php echo esc_html($sar_to_pkr); ?> PKR</p>
                    </td>
                </tr>
                <tr>
                    <th>Auto-Update Currency</th>
                    <td>
                        <label>
                            <input type="checkbox" name="auto_currency_update" value="yes" <?php checked($auto_update, 'yes'); ?>>
                            Automatically fetch real-time currency rates (updates hourly)
                        </label>
                    </td>
                </tr>
            </table>
        </div>
        
        <div style="background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2>✈️ Flight Settings</h2>
            
            <table class="form-table">
                <tr>
                    <th>Enable Flight Details</th>
                    <td>
                        <label>
                            <input type="checkbox" name="enable_flights" value="yes" <?php checked($enable_flights, 'yes'); ?>>
                            Show flight details in booking form
                        </label>
                    </td>
                </tr>
            </table>
        </div>
        
        <div style="background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2>📜 Terms & Conditions</h2>
            
            <table class="form-table">
                <tr>
                    <td>
                        <textarea name="terms_conditions" rows="12" class="large-text code"><?php echo esc_textarea($terms); ?></textarea>
                        <p class="description">These terms will be displayed during the booking process</p>
                    </td>
                </tr>
            </table>
        </div>
        
        <p class="submit">
            <input type="submit" name="update_settings" class="button button-primary" value="Save Settings">
        </p>
    </form>
</div>
