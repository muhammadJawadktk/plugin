<?php
/**
 * Pricing Helper Functions
 * Handles visa pricing, currency conversion, and flight pricing
 */

if (!defined('ABSPATH')) exit;

class Asfaar_Pricing_Helper {
    
    /**
     * Get SAR to PKR conversion rate
     * Can be updated to fetch from API
     */
    public static function get_sar_to_pkr_rate() {
        $rate = get_option('asfaar_sar_to_pkr_rate', 74.50);
        
        // If auto-update is enabled, fetch from API
        if (get_option('asfaar_auto_currency_update') === 'yes') {
            $cached_rate = get_transient('asfaar_currency_rate');
            if ($cached_rate) {
                return floatval($cached_rate);
            }
            
            // Fetch from API (you can replace with your preferred API)
            $api_rate = self::fetch_currency_rate();
            if ($api_rate) {
                set_transient('asfaar_currency_rate', $api_rate, 3600); // Cache for 1 hour
                return floatval($api_rate);
            }
        }
        
        return floatval($rate);
    }
    
    /**
     * Fetch currency rate from API
     */
    private static function fetch_currency_rate() {
        // Example: Using exchangerate-api.com (free tier available)
        $api_url = 'https://api.exchangerate-api.com/v4/latest/SAR';
        
        $response = wp_remote_get($api_url, ['timeout' => 10]);
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['rates']['PKR'])) {
            return $data['rates']['PKR'];
        }
        
        return false;
    }
    
    /**
     * Convert SAR to PKR
     */
    public static function convert_sar_to_pkr($amount_sar) {
        $rate = self::get_sar_to_pkr_rate();
        return round($amount_sar * $rate, 2);
    }
    
    /**
     * Calculate visa cost based on group size
     */
    public static function calculate_visa_cost($adults, $children, $infants) {
        $total_travelers = $adults + $children + $infants;
        
        // Adult visa pricing based on group size
        $adult_visa_price = 0;
        if ($total_travelers >= 10 && $total_travelers <= 20) {
            $adult_visa_price = get_option('asfaar_visa_group_10_20', 820);
        } elseif ($total_travelers >= 3 && $total_travelers <= 9) {
            $adult_visa_price = get_option('asfaar_visa_group_3_9', 850);
        } elseif ($total_travelers == 2) {
            $adult_visa_price = get_option('asfaar_visa_2_persons', 880);
        } else {
            $adult_visa_price = get_option('asfaar_visa_1_person', 980);
        }
        
        $child_visa_price = get_option('asfaar_visa_child', 720);
        $infant_visa_price = get_option('asfaar_visa_infant', 670);
        
        $total_visa_cost = ($adults * $adult_visa_price) + 
                          ($children * $child_visa_price) + 
                          ($infants * $infant_visa_price);
        
        return [
            'adult_price' => $adult_visa_price,
            'child_price' => $child_visa_price,
            'infant_price' => $infant_visa_price,
            'total_cost' => $total_visa_cost
        ];
    }
    
    /**
     * Get visa pricing breakdown for display
     */
    public static function get_visa_breakdown($adults, $children, $infants) {
        $visa_data = self::calculate_visa_cost($adults, $children, $infants);
        $sar_to_pkr = self::get_sar_to_pkr_rate();
        
        $breakdown = [];
        
        if ($adults > 0) {
            $breakdown[] = [
                'type' => 'Adult Visa',
                'quantity' => $adults,
                'price_sar' => $visa_data['adult_price'],
                'price_pkr' => round($visa_data['adult_price'] * $sar_to_pkr, 2),
                'total_sar' => $adults * $visa_data['adult_price'],
                'total_pkr' => round($adults * $visa_data['adult_price'] * $sar_to_pkr, 2)
            ];
        }
        
        if ($children > 0) {
            $breakdown[] = [
                'type' => 'Child Visa (2-11 years)',
                'quantity' => $children,
                'price_sar' => $visa_data['child_price'],
                'price_pkr' => round($visa_data['child_price'] * $sar_to_pkr, 2),
                'total_sar' => $children * $visa_data['child_price'],
                'total_pkr' => round($children * $visa_data['child_price'] * $sar_to_pkr, 2)
            ];
        }
        
        if ($infants > 0) {
            $breakdown[] = [
                'type' => 'Infant Visa (1-2 years)',
                'quantity' => $infants,
                'price_sar' => $visa_data['infant_price'],
                'price_pkr' => round($visa_data['infant_price'] * $sar_to_pkr, 2),
                'total_sar' => $infants * $visa_data['infant_price'],
                'total_pkr' => round($infants * $visa_data['infant_price'] * $sar_to_pkr, 2)
            ];
        }
        
        return [
            'breakdown' => $breakdown,
            'total_sar' => $visa_data['total_cost'],
            'total_pkr' => round($visa_data['total_cost'] * $sar_to_pkr, 2),
            'sar_to_pkr_rate' => $sar_to_pkr
        ];
    }
}
