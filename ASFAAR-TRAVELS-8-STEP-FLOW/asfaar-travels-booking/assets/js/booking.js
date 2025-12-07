jQuery(document).ready(function($) {
    console.log('AFSAR Booking System with Visa Calculator Loaded');
    
    const booking = {
        package: null,
        persons: { adults: 2, children: 0, infants: 0 },
        hotels: { makkah: null, madinah: null },
        transport: null,
        travelers: [],
        flight: {
            outbound: { airline: '', date: '', time: '', airport: '' },
            return: { airline: '', date: '', time: '', airport: '' }
        },
        visa: null,
        currentStep: 1
    };
    
    // Initialize
    init();
    
    function init() {
        setupPackageSelection();
        setupTravelerCounters();
        setupHotelSelection();
        setupTransportSelection();
        setupFlightDetails();
        setupSkipButtons();
        setupNavigation();
        setupCompletion();
        setupDateValidation();
    }
    
    // STEP 1: Package Selection
    function setupPackageSelection() {
        $('.afsar-package-card').on('click', function() {
            $('.afsar-package-card').removeClass('selected');
            $(this).addClass('selected');
            
            booking.package = JSON.parse($(this).attr('data-package'));
            $('[data-step="1"] .afsar-btn-next').prop('disabled', false);
            
            console.log('Package selected:', booking.package);
        });
        
        $('.afsar-btn-select-package').on('click', function(e) {
            e.stopPropagation();
            $(this).closest('.afsar-package-card').click();
            goToStep(2);
        });
    }
    
    // STEP 2: Traveler Counters with Visa Calculation
    function setupTravelerCounters() {
        $('.afsar-counter-btn').on('click', function() {
            const action = $(this).data('action');
            const target = $(this).data('target');
            
            if (action === 'plus') {
                booking.persons[target]++;
            } else if (action === 'minus') {
                const min = target === 'adults' ? 1 : 0;
                if (booking.persons[target] > min) {
                    booking.persons[target]--;
                }
            }
            
            $('#' + target + 'Count').val(booking.persons[target]);
            
            // Calculate visa costs
            updateVisaCalculation();
            
            console.log('Travelers:', booking.persons);
        });
        
        // Initial visa calculation
        updateVisaCalculation();
    }
    
    // Update visa calculation
    function updateVisaCalculation() {
        if (typeof AsfaarVisaCalculator !== 'undefined') {
            booking.visa = AsfaarVisaCalculator.calculateVisaCost(
                booking.persons.adults,
                booking.persons.children,
                booking.persons.infants
            );
            
            // Display visa info if container exists
            const visaDisplay = $('#afsar-visa-display');
            if (visaDisplay.length) {
                visaDisplay.html(AsfaarVisaCalculator.formatBreakdown(booking.visa));
            }
        }
    }
    
    // STEP 3: Hotel Selection (Optional)
    function setupHotelSelection() {
        $('.afsar-hotel-select').on('change', function() {
            const city = $(this).data('city');
            const selectedOption = $(this).find('option:selected');
            
            if (selectedOption.val()) {
                booking.hotels[city] = JSON.parse(selectedOption.attr('data-hotel'));
            } else {
                booking.hotels[city] = null;
            }
            
            $('[data-step="3"] .afsar-btn-next').prop('disabled', false);
            console.log('Hotels:', booking.hotels);
        });
        
        $('[data-step="3"] .afsar-btn-next').prop('disabled', false);
    }
    
    // STEP 4: Transport Selection (Optional)
    function setupTransportSelection() {
        $('.afsar-transport-card').on('click', function() {
            $('.afsar-transport-card').removeClass('selected');
            $(this).addClass('selected');
            
            booking.transport = JSON.parse($(this).attr('data-transport'));
            $('[data-step="4"] .afsar-btn-next').prop('disabled', false);
            
            console.log('Transport selected:', booking.transport);
        });
        
        $('.afsar-btn-select-transport').on('click', function(e) {
            e.stopPropagation();
            $(this).closest('.afsar-transport-card').click();
            goToStep(5);
        });
        
        $('[data-step="4"] .afsar-btn-next').prop('disabled', false);
    }
    
    // NEW: Flight Details
    function setupFlightDetails() {
        // Handle flight detail inputs
        $('.flight-detail-input').on('input change', function() {
            const direction = $(this).data('direction');
            const field = $(this).data('field');
            const value = $(this).val();
            
            booking.flight[direction][field] = value;
            console.log('Flight details updated:', booking.flight);
        });
    }
    
    // Skip Buttons (Hotels & Transport)
    function setupSkipButtons() {
        $('.afsar-btn-skip').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const currentStep = parseInt($(this).data('skip'));
            const nextStep = parseInt($(this).data('next'));
            
            console.log('Skipping step', currentStep, 'to', nextStep);
            
            // Clear selection for skipped step
            if (currentStep === 3) {
                booking.hotels = { makkah: null, madinah: null };
                $('.afsar-hotel-select').val('');
            } else if (currentStep === 4) {
                booking.transport = null;
                $('.afsar-transport-card').removeClass('selected');
            } else if (currentStep === 6) {
                // Clear flight details when skipped (Step 6 now)
                booking.flight = {
                    outbound: { airline: '', date: '', time: '', airport: '' },
                    return: { airline: '', date: '', time: '', airport: '' }
                };
                $('.flight-detail-input').val('');
                // Generate summary before going to step 7
                generateSummary();
            }
            
            goToStep(nextStep);
        });
    }
    
    // Navigation
    function setupNavigation() {
        $('.afsar-btn-prev').on('click', function() {
            const currentStep = parseInt($(this).closest('.afsar-step').data('step'));
            goToStep(currentStep - 1);
        });
        
        $('.afsar-btn-next').on('click', function() {
            const currentStep = parseInt($(this).closest('.afsar-step').data('step'));
            const nextStep = parseInt($(this).data('next'));
            
            if (currentStep === 2) {
                // Moving from travelers to hotels
                goToStep(nextStep);
            } else if (currentStep === 5) {
                // Moving from traveler details to flight details
                if (collectTravelerData()) {
                    goToStep(nextStep);
                }
            } else if (currentStep === 6) {
                // Moving from flight details to summary
                generateSummary();
                goToStep(nextStep);
            } else {
                goToStep(nextStep);
            }
        });
    }
    
    function goToStep(step) {
        $('.afsar-step').removeClass('active');
        $('.afsar-progress-step').removeClass('active completed');
        
        $(`.afsar-step[data-step="${step}"]`).addClass('active');
        
        for (let i = 1; i < step; i++) {
            $(`.afsar-progress-step[data-step="${i}"]`).addClass('completed');
        }
        $(`.afsar-progress-step[data-step="${step}"]`).addClass('active');
        
        booking.currentStep = step;
        
        // Scroll to top
        $('html, body').animate({ scrollTop: $('.afsar-booking-wrapper').offset().top - 100 }, 300);
    }
    
    // Age Validation
    function setupDateValidation() {
        $('.traveler-dob').on('change', function() {
            const dob = new Date($(this).val());
            const today = new Date();
            const age = Math.floor((today - dob) / (365.25 * 24 * 60 * 60 * 1000));
            const type = $(this).data('type');
            
            let valid = true;
            let message = '';
            
            if (type === 'adult' && age < 12) {
                valid = false;
                message = 'Adults must be 12+ years old';
            } else if (type === 'child' && (age < 2 || age > 11)) {
                valid = false;
                message = 'Children must be between 2-11 years old';
            } else if (type === 'infant' && age > 2) {
                valid = false;
                message = 'Infants must be under 2 years old';
            }
            
            if (!valid) {
                alert(message);
                $(this).val('');
                $(this).closest('.traveler-form').addClass('invalid');
            } else {
                $(this).closest('.traveler-form').removeClass('invalid');
            }
        });
    }
    
    // Collect Traveler Data
    function collectTravelerData() {
        booking.travelers = [];
        let valid = true;
        
        $('.traveler-form').each(function() {
            const name = $(this).find('.traveler-name').val();
            const dob = $(this).find('.traveler-dob').val();
            const passport = $(this).find('.traveler-passport').val();
            const email = $(this).find('.traveler-email').val();
            const phone = $(this).find('.traveler-phone').val();
            
            if (!name || !dob || !passport) {
                valid = false;
                $(this).addClass('invalid');
                alert('Please fill all required fields for traveler: ' + (booking.travelers.length + 1));
                return false;
            }
            
            booking.travelers.push({
                name: name,
                dob: dob,
                passport: passport,
                email: email || '',
                phone: phone || ''
            });
        });
        
        return valid;
    }
    
    // Generate Summary with VISA and Currency Conversion
    function generateSummary() {
        const packageCost = booking.package.price * (booking.persons.adults + booking.persons.children * 0.7);
        
        // Calculate visa cost
        let visaCost = 0;
        let visaHTML = '';
        if (booking.visa) {
            visaCost = booking.visa.costs.total;
            visaHTML = AsfaarVisaCalculator.formatBreakdown(booking.visa);
        }
        
        // Calculate hotel cost (optional)
        let hotelCost = 0;
        if (booking.hotels.makkah && booking.hotels.madinah) {
            hotelCost = (booking.hotels.makkah.price * 7) + (booking.hotels.madinah.price * 7);
        } else if (booking.hotels.makkah) {
            hotelCost = booking.hotels.makkah.price * 7;
        } else if (booking.hotels.madinah) {
            hotelCost = booking.hotels.madinah.price * 7;
        }
        
        // Calculate transport cost (optional)
        const totalPersons = booking.persons.adults + booking.persons.children + booking.persons.infants;
        let transportCost = 0;
        if (booking.transport) {
            transportCost = booking.transport.price * totalPersons;
        }
        
        const grandTotal = packageCost + visaCost + hotelCost + transportCost;
        
        // Get currency conversion
        let currencyHTML = '';
        if (typeof AsfaarCurrencyConverter !== 'undefined') {
            const conversion = AsfaarCurrencyConverter.formatCurrency(grandTotal);
            currencyHTML = conversion.html;
        }
        
        // Build flight details HTML
        let flightHTML = '';
        if (booking.flight.outbound.airline || booking.flight.return.airline) {
            flightHTML = `
                <div class="afsar-summary-block">
                    <h3>✈️ Flight Details</h3>
                    ${booking.flight.outbound.airline ? `
                    <div class="flight-info">
                        <p><strong>📤 Outbound Flight:</strong></p>
                        <p>Airline: ${booking.flight.outbound.airline}</p>
                        <p>Date: ${booking.flight.outbound.date}</p>
                        <p>Time: ${booking.flight.outbound.time}</p>
                        <p>Airport: ${booking.flight.outbound.airport}</p>
                    </div>
                    ` : ''}
                    ${booking.flight.return.airline ? `
                    <div class="flight-info">
                        <p><strong>📥 Return Flight:</strong></p>
                        <p>Airline: ${booking.flight.return.airline}</p>
                        <p>Date: ${booking.flight.return.date}</p>
                        <p>Time: ${booking.flight.return.time}</p>
                        <p>Airport: ${booking.flight.return.airport}</p>
                    </div>
                    ` : ''}
                </div>
            `;
        } else {
            flightHTML = `
                <div class="afsar-summary-block">
                    <h3>✈️ Flight Details</h3>
                    <p><em>Skipped - Own arrangement</em></p>
                </div>
            `;
        }
        
        // Build hotels HTML
        let hotelsHTML = '';
        if (booking.hotels.makkah || booking.hotels.madinah) {
            hotelsHTML = `
                <div class="afsar-summary-block">
                    <h3>🏨 Hotels</h3>
                    ${booking.hotels.makkah ? `<p><strong>Makkah:</strong> ${booking.hotels.makkah.name}</p>` : ''}
                    ${booking.hotels.madinah ? `<p><strong>Madinah:</strong> ${booking.hotels.madinah.name}</p>` : ''}
                    <p>Cost: SAR ${hotelCost.toLocaleString()} (7 nights each)</p>
                </div>
            `;
        } else {
            hotelsHTML = `
                <div class="afsar-summary-block">
                    <h3>🏨 Hotels</h3>
                    <p><em>Skipped - Own arrangement</em></p>
                </div>
            `;
        }
        
        // Build transport HTML
        let transportHTML = '';
        if (booking.transport) {
            transportHTML = `
                <div class="afsar-summary-block">
                    <h3>🚗 Transport</h3>
                    <p><strong>${booking.transport.name}</strong></p>
                    <p>${totalPersons} persons × SAR ${booking.transport.price}</p>
                    <p>Cost: SAR ${transportCost.toLocaleString()}</p>
                </div>
            `;
        } else {
            transportHTML = `
                <div class="afsar-summary-block">
                    <h3>🚗 Transport</h3>
                    <p><em>Skipped - Own arrangement</em></p>
                </div>
            `;
        }
        
        const html = `
            <div class="afsar-summary-block">
                <h3>📦 Package</h3>
                <p><strong>${booking.package.name}</strong></p>
                <p>Category: ${booking.package.category}</p>
                <p>Duration: ${booking.package.duration}</p>
                <p>Cost: SAR ${packageCost.toLocaleString()}</p>
            </div>
            
            <div class="afsar-summary-block">
                <h3>👥 Travelers</h3>
                <p>${booking.persons.adults} Adults + ${booking.persons.children} Children + ${booking.persons.infants} Infants</p>
                <p><strong>Lead Traveler:</strong> ${booking.travelers[0].name}</p>
                <p><strong>Contact:</strong> ${booking.travelers[0].email}</p>
            </div>
            
            ${visaHTML}
            
            ${hotelsHTML}
            
            ${transportHTML}
            
            ${flightHTML}
            
            <div class="afsar-summary-total">
                <h3>TOTAL AMOUNT</h3>
                ${currencyHTML || `<div class="price">SAR ${grandTotal.toLocaleString()}</div>`}
            </div>
        `;
        
        $('#afsar-booking-summary').html(html);
    }
    
    // Complete Booking
    function setupCompletion() {
        $('.afsar-btn-complete').on('click', function() {
            const btn = $(this);
            btn.prop('disabled', true).text('Processing...');
            
            // Calculate final total with visa
            const packageCost = booking.package.price * (booking.persons.adults + booking.persons.children * 0.7);
            const visaCost = booking.visa ? booking.visa.costs.total : 0;
            
            // Calculate hotel cost (optional)
            let hotelCost = 0;
            if (booking.hotels.makkah && booking.hotels.madinah) {
                hotelCost = (booking.hotels.makkah.price * 7) + (booking.hotels.madinah.price * 7);
            } else if (booking.hotels.makkah) {
                hotelCost = booking.hotels.makkah.price * 7;
            } else if (booking.hotels.madinah) {
                hotelCost = booking.hotels.madinah.price * 7;
            }
            
            // Calculate transport cost (optional)
            const totalPersons = booking.persons.adults + booking.persons.children + booking.persons.infants;
            let transportCost = 0;
            if (booking.transport) {
                transportCost = booking.transport.price * totalPersons;
            }
            
            const grandTotal = packageCost + visaCost + hotelCost + transportCost;
            
            // Prepare booking data
            const bookingData = {
                action: 'asfaar_travels_complete_booking',
                package: booking.package,
                persons: booking.persons,
                hotels: booking.hotels,
                transport: booking.transport,
                travelers: booking.travelers,
                flight: booking.flight,
                visa: booking.visa,
                total: grandTotal
            };
            
            $.post(afsar_ajax.ajax_url, bookingData, function(response) {
                if (response.success) {
                    $('.afsar-booking-reference').text(response.data.reference);
                    if (response.data.pdf_url) {
                        $('.afsar-pdf-link').attr('href', response.data.pdf_url).show();
                    }
                    goToStep(7);
                } else {
                    alert('Booking failed: ' + (response.data || 'Unknown error'));
                    btn.prop('disabled', false).text('Complete Booking');
                }
            }).fail(function() {
                alert('Connection error. Please try again.');
                btn.prop('disabled', false).text('Complete Booking');
            });
        });
    }
    
});
