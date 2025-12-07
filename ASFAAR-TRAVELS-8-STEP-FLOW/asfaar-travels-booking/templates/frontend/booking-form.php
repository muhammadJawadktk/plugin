<?php 
if (!defined('ABSPATH')) exit;

// Disable caching for this shortcode to ensure fresh data
if (!defined('DONOTCACHEPAGE')) {
    define('DONOTCACHEPAGE', true);
}

global $wpdb;

// Fetch packages with proper ordering
$packages = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}afsar_packages WHERE status = 'active' ORDER BY price ASC, id DESC");

// Fetch hotels with proper ordering - always fresh data
$hotels_makkah = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}afsar_hotels WHERE city = 'Makkah' AND status = 'active' ORDER BY name ASC, id DESC");
$hotels_madinah = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}afsar_hotels WHERE city = 'Madinah' AND status = 'active' ORDER BY name ASC, id DESC");

// Fetch transports with proper ordering
$transports = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}afsar_transports WHERE status = 'active' ORDER BY name ASC, id DESC");
?>

<div class="afsar-booking-wrapper" id="afsarBooking">
    
    <!-- Progress Bar -->
    <div class="afsar-progress">
        <div class="afsar-progress-step active" data-step="1"><span>1</span> Package</div>
        <div class="afsar-progress-step" data-step="2"><span>2</span> Travelers</div>
        <div class="afsar-progress-step" data-step="3"><span>3</span> Hotels</div>
        <div class="afsar-progress-step" data-step="4"><span>4</span> Transport</div>
        <div class="afsar-progress-step" data-step="5"><span>5</span> Details</div>
        <div class="afsar-progress-step" data-step="6"><span>6</span> Flight</div>
        <div class="afsar-progress-step" data-step="7"><span>7</span> Summary</div>
        <div class="afsar-progress-step" data-step="8"><span>8</span> Complete</div>
    </div>

    <!-- STEP 1: PACKAGE -->
    <div class="afsar-step active" data-step="1">
        <h2 class="afsar-step-title">Select Your Umrah Package</h2>
        <div class="afsar-packages-grid">
            <?php if (empty($packages)): ?>
                <p>No packages available.</p>
            <?php else: ?>
                <?php foreach ($packages as $pkg): ?>
                    <div class="afsar-package-card" data-package='<?php echo htmlspecialchars(json_encode([
                        'id' => $pkg->id, 'name' => $pkg->package_name, 'price' => floatval($pkg->price),
                        'duration' => $pkg->duration, 'category' => $pkg->category
                    ])); ?>'>
                        <div class="afsar-package-category"><?php echo esc_html($pkg->category); ?></div>
                        <h3><?php echo esc_html($pkg->package_name); ?></h3>
                        <div class="afsar-package-price">SAR <?php echo number_format($pkg->price, 0); ?></div>
                        <p><?php echo esc_html($pkg->duration); ?></p>
                        <button type="button" class="afsar-btn afsar-btn-select-package">Select →</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="afsar-step-nav">
            <button type="button" class="afsar-btn afsar-btn-secondary" disabled>← Previous</button>
            <button type="button" class="afsar-btn afsar-btn-primary afsar-btn-next" disabled data-next="2">Next →</button>
        </div>
    </div>

    <!-- STEP 2: TRAVELERS -->
    <div class="afsar-step" data-step="2">
        <h2 class="afsar-step-title">Number of Travelers</h2>
        <p style="color: #666; margin-bottom: 30px;">Select the number of travelers for your Umrah package</p>
        <div class="afsar-travelers-section">
            <div class="afsar-traveler-counter">
                <div class="afsar-traveler-info">
                    <div class="afsar-traveler-icon">👨‍👩‍👧‍👦</div>
                    <div class="afsar-traveler-details">
                        <div class="afsar-traveler-label">Adults</div>
                        <div class="afsar-traveler-desc">12+ years</div>
                    </div>
                </div>
                <div class="afsar-counter-controls">
                    <button type="button" class="afsar-counter-btn" data-action="minus" data-target="adults">−</button>
                    <input type="number" id="adultsCount" value="2" readonly>
                    <button type="button" class="afsar-counter-btn" data-action="plus" data-target="adults">+</button>
                </div>
            </div>
            <div class="afsar-traveler-counter">
                <div class="afsar-traveler-info">
                    <div class="afsar-traveler-icon">👧👦</div>
                    <div class="afsar-traveler-details">
                        <div class="afsar-traveler-label">Children</div>
                        <div class="afsar-traveler-desc">2-11 years <span class="afsar-discount-badge">30% off</span></div>
                    </div>
                </div>
                <div class="afsar-counter-controls">
                    <button type="button" class="afsar-counter-btn" data-action="minus" data-target="children">−</button>
                    <input type="number" id="childrenCount" value="0" readonly>
                    <button type="button" class="afsar-counter-btn" data-action="plus" data-target="children">+</button>
                </div>
            </div>
            <div class="afsar-traveler-counter">
                <div class="afsar-traveler-info">
                    <div class="afsar-traveler-icon">👶</div>
                    <div class="afsar-traveler-details">
                        <div class="afsar-traveler-label">Infants</div>
                        <div class="afsar-traveler-desc">Under 2 years <span class="afsar-free-badge">Free</span></div>
                    </div>
                </div>
                <div class="afsar-counter-controls">
                    <button type="button" class="afsar-counter-btn" data-action="minus" data-target="infants">−</button>
                    <input type="number" id="infantsCount" value="0" readonly>
                    <button type="button" class="afsar-counter-btn" data-action="plus" data-target="infants">+</button>
                </div>
            </div>
        </div>
        
        <!-- Visa Calculation Display -->
        <div id="afsar-visa-display" class="afsar-visa-section">
            <!-- Visa costs will be displayed here by JavaScript -->
        </div>
        <div class="afsar-step-nav">
            <button type="button" class="afsar-btn afsar-btn-secondary afsar-btn-prev" data-prev="1">← Previous</button>
            <button type="button" class="afsar-btn afsar-btn-primary afsar-btn-next" data-next="3">Next →</button>
        </div>
    </div>

    <!-- STEP 3: HOTELS -->
    <div class="afsar-step" data-step="3">
        <h2 class="afsar-step-title">Select Hotels</h2>
        <p style="color: #666; margin-bottom: 20px;">Choose your hotels or skip this step if you have your own accommodation arrangements.</p>
        <div class="afsar-hotels-section">
            <div class="afsar-hotel-selection">
                <h3>🕋 Makkah Hotel (Optional)</h3>
                <select class="afsar-hotel-select" data-city="makkah">
                    <option value="">-- Select or Skip --</option>
                    <?php foreach ($hotels_makkah as $h): ?>
                        <option value="<?php echo $h->id; ?>" data-hotel='<?php echo htmlspecialchars(json_encode([
                            'id' => $h->id, 'name' => $h->name, 'price' => floatval($h->price_per_night)
                        ])); ?>'><?php echo esc_html($h->name); ?> - SAR <?php echo number_format($h->price_per_night, 0); ?>/night</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="afsar-hotel-selection">
                <h3>🕌 Madinah Hotel (Optional)</h3>
                <select class="afsar-hotel-select" data-city="madinah">
                    <option value="">-- Select or Skip --</option>
                    <?php foreach ($hotels_madinah as $h): ?>
                        <option value="<?php echo $h->id; ?>" data-hotel='<?php echo htmlspecialchars(json_encode([
                            'id' => $h->id, 'name' => $h->name, 'price' => floatval($h->price_per_night)
                        ])); ?>'><?php echo esc_html($h->name); ?> - SAR <?php echo number_format($h->price_per_night, 0); ?>/night</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="afsar-step-nav">
            <button type="button" class="afsar-btn afsar-btn-secondary afsar-btn-prev" data-prev="2">← Previous</button>
            <button type="button" class="afsar-btn afsar-btn-skip" data-skip="3" data-next="4" >Skip Hotels</button>
            <button type="button" class="afsar-btn afsar-btn-primary afsar-btn-next" disabled data-next="4">Next →</button>
        </div>
    </div>

    <!-- STEP 4: TRANSPORT -->
    <div class="afsar-step" data-step="4">
        <h2 class="afsar-step-title">Select Transport</h2>
        <p style="color: #666; margin-bottom: 20px;">Choose your preferred transportation option or skip if you have your own arrangements.</p>
        <div class="afsar-transport-grid">
            <?php foreach ($transports as $t): ?>
                <div class="afsar-transport-card" data-transport='<?php echo htmlspecialchars(json_encode([
                    'id' => $t->id, 'name' => $t->name, 'price' => floatval($t->price_per_person),
                    'capacity' => $t->capacity, 'icon' => $t->icon
                ])); ?>'>
                    <div class="afsar-transport-icon"><?php echo esc_html($t->icon); ?></div>
                    <h3><?php echo esc_html($t->name); ?></h3>
                    <p><?php echo esc_html($t->description); ?></p>
                    <div class="afsar-transport-price">SAR <?php echo number_format($t->price_per_person, 0); ?>/person</div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="afsar-step-nav">
            <button type="button" class="afsar-btn afsar-btn-secondary afsar-btn-prev" data-prev="3">← Previous</button>
            <button type="button" class="afsar-btn afsar-btn-skip" data-skip="4" data-next="5" >Skip Transport</button>
            <button type="button" class="afsar-btn afsar-btn-primary afsar-btn-next" disabled data-next="5">Next →</button>
        </div>
    </div>

    <!-- STEP 5: TRAVELER DETAILS -->
    <div class="afsar-step" data-step="5">
        <h2 class="afsar-step-title">Traveler Details</h2>
        <p style="color: #666; margin-bottom: 20px;">Please provide information for all travelers</p>
        
        <div id="afsar-traveler-forms"></div>
        
        <div class="afsar-step-nav">
            <button type="button" class="afsar-btn afsar-btn-secondary afsar-btn-prev" data-prev="4">← Previous</button>
            <button type="button" class="afsar-btn afsar-btn-primary afsar-btn-next" data-next="6">Next →</button>
        </div>
    </div>

    <!-- STEP 6: FLIGHT DETAILS -->
    <div class="afsar-step" data-step="6">
        <h2 class="afsar-step-title">Flight Details</h2>
        <p style="color: #666; margin-bottom: 20px;">Add your flight information or skip if you'll arrange later.</p>
        
        <!-- Flight Details Form -->
        <div class="afsar-flight-details-section">
            <div class="afsar-flight-forms">
                <!-- Outbound Flight -->
                <div class="afsar-flight-form-group">
                    <h3>📤 Outbound Flight</h3>
                    <div class="afsar-flight-fields">
                        <div class="afsar-field">
                            <label>Airline</label>
                            <input type="text" class="flight-detail-input" data-direction="outbound" data-field="airline" placeholder="e.g., PIA, Saudi Airlines">
                        </div>
                        <div class="afsar-field">
                            <label>Date</label>
                            <input type="date" class="flight-detail-input" data-direction="outbound" data-field="date">
                        </div>
                        <div class="afsar-field">
                            <label>Time</label>
                            <input type="time" class="flight-detail-input" data-direction="outbound" data-field="time">
                        </div>
                        <div class="afsar-field">
                            <label>Airport City</label>
                            <input type="text" class="flight-detail-input" data-direction="outbound" data-field="airport" placeholder="e.g., Islamabad">
                        </div>
                    </div>
                </div>
                
                <!-- Return Flight -->
                <div class="afsar-flight-form-group">
                    <h3>📥 Return Flight</h3>
                    <div class="afsar-flight-fields">
                        <div class="afsar-field">
                            <label>Airline</label>
                            <input type="text" class="flight-detail-input" data-direction="return" data-field="airline" placeholder="e.g., PIA, Saudi Airlines">
                        </div>
                        <div class="afsar-field">
                            <label>Date</label>
                            <input type="date" class="flight-detail-input" data-direction="return" data-field="date">
                        </div>
                        <div class="afsar-field">
                            <label>Time</label>
                            <input type="time" class="flight-detail-input" data-direction="return" data-field="time">
                        </div>
                        <div class="afsar-field">
                            <label>Airport City</label>
                            <input type="text" class="flight-detail-input" data-direction="return" data-field="airport" placeholder="e.g., Jeddah">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="afsar-step-nav">
            <button type="button" class="afsar-btn afsar-btn-secondary afsar-btn-prev" data-prev="5">← Previous</button>
            <button type="button" class="afsar-btn afsar-btn-skip" data-skip="6" data-next="7">⏭️ Skip Flight Details</button>
            <button type="button" class="afsar-btn afsar-btn-primary afsar-btn-next" data-next="7">Next →</button>
        </div>
    </div>
    <!-- STEP 7: SUMMARY -->
    <div class="afsar-step" data-step="7">
        <h2 class="afsar-step-title">Booking Summary</h2>
        <div id="afsar-booking-summary"></div>
        <div class="afsar-step-nav">
            <button type="button" class="afsar-btn afsar-btn-secondary afsar-btn-prev" data-prev="6">← Previous</button>
            <button type="button" class="afsar-btn afsar-btn-primary afsar-btn-complete">Complete Booking</button>
        </div>
    </div>

    <!-- STEP 8: SUCCESS -->
    <div class="afsar-step" data-step="8">
        <div class="afsar-success-message">
            <div class="afsar-success-icon">✓</div>
            <h2>Booking Completed Successfully!</h2>
            <div class="afsar-booking-info">
                <p><strong>Reference:</strong> <span id="afsar-final-reference">-</span></p>
                <p><strong>Total:</strong> SAR <span id="afsar-final-total">-</span></p>
            </div>
            <button type="button" class="afsar-btn afsar-btn-primary" id="afsarDownloadPDF">📄 Download PDF</button>
            <p class="afsar-success-note">Confirmation email has been sent!</p>
        </div>
    </div>

</div>
