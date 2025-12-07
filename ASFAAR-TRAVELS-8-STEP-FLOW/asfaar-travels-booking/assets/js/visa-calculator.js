/**
 * VISA PRICING CALCULATOR
 * Based on Umrah Package Document
 */

(function($) {
    'use strict';
    
    window.AsfaarVisaCalculator = {
        // Visa rates from PDF document
        rates: {
            group_10_20: 820,    // 10-20 persons
            group_3_9: 850,      // 3-9 people
            group_2: 880,        // 2 persons
            group_1: 980,        // 1 person
            child: 720,          // 2-11 years
            infant: 670          // 1-2 years
        },
        
        /**
         * Calculate visa cost based on travelers
         */
        calculateVisaCost: function(adults, children, infants) {
            const totalPersons = adults + children + infants;
            let adultVisaRate = this.getAdultVisaRate(totalPersons);
            
            const costs = {
                adults: adults * adultVisaRate,
                children: children * this.rates.child,
                infants: infants * this.rates.infant,
                total: 0
            };
            
            costs.total = costs.adults + costs.children + costs.infants;
            
            return {
                costs: costs,
                rates: {
                    adult: adultVisaRate,
                    child: this.rates.child,
                    infant: this.rates.infant
                },
                breakdown: {
                    adults: `${adults} × ${adultVisaRate} SAR = ${costs.adults} SAR`,
                    children: `${children} × ${this.rates.child} SAR = ${costs.children} SAR`,
                    infants: `${infants} × ${this.rates.infant} SAR = ${costs.infants} SAR`
                }
            };
        },
        
        /**
         * Get visa rate based on total group size
         */
        getAdultVisaRate: function(totalPersons) {
            if (totalPersons >= 10 && totalPersons <= 20) {
                return this.rates.group_10_20;
            } else if (totalPersons >= 3 && totalPersons <= 9) {
                return this.rates.group_3_9;
            } else if (totalPersons === 2) {
                return this.rates.group_2;
            } else {
                return this.rates.group_1;
            }
        },
        
        /**
         * Format visa breakdown for display
         */
        formatBreakdown: function(visaData) {
            return `
                <div class="afsar-visa-breakdown">
                    <h4>📋 Visa Costs</h4>
                    <div class="visa-item">
                        <span>Adults (${visaData.breakdown.adults})</span>
                        <strong>SAR ${visaData.costs.adults.toLocaleString()}</strong>
                    </div>
                    ${visaData.costs.children > 0 ? `
                    <div class="visa-item">
                        <span>Children (${visaData.breakdown.children})</span>
                        <strong>SAR ${visaData.costs.children.toLocaleString()}</strong>
                    </div>
                    ` : ''}
                    ${visaData.costs.infants > 0 ? `
                    <div class="visa-item">
                        <span>Infants (${visaData.breakdown.infants})</span>
                        <strong>SAR ${visaData.costs.infants.toLocaleString()}</strong>
                    </div>
                    ` : ''}
                    <div class="visa-total">
                        <span><strong>Total Visa Cost:</strong></span>
                        <strong class="amount">SAR ${visaData.costs.total.toLocaleString()}</strong>
                    </div>
                </div>
            `;
        }
    };
    
    // Currency Converter
    window.AsfaarCurrencyConverter = {
        exchangeRate: null,
        lastUpdate: null,
        
        /**
         * Fetch real-time SAR to PKR exchange rate
         */
        fetchExchangeRate: async function() {
            try {
                // Using Exchange Rate API (free tier)
                const response = await fetch('https://api.exchangerate-api.com/v4/latest/SAR');
                const data = await response.json();
                
                this.exchangeRate = data.rates.PKR;
                this.lastUpdate = new Date();
                
                console.log('Exchange Rate Updated: 1 SAR =', this.exchangeRate, 'PKR');
                return this.exchangeRate;
            } catch (error) {
                console.error('Error fetching exchange rate:', error);
                // Fallback rate (approximate)
                this.exchangeRate = 74.50;
                return this.exchangeRate;
            }
        },
        
        /**
         * Convert SAR to PKR
         */
        convertToPKR: function(amountSAR) {
            if (!this.exchangeRate) {
                // Use default rate if not fetched
                this.exchangeRate = 74.50;
            }
            return Math.round(amountSAR * this.exchangeRate);
        },
        
        /**
         * Format currency display
         */
        formatCurrency: function(amountSAR) {
            const pkrAmount = this.convertToPKR(amountSAR);
            return {
                sar: `SAR ${amountSAR.toLocaleString()}`,
                pkr: `PKR ${pkrAmount.toLocaleString()}`,
                rate: `(@ ${this.exchangeRate} PKR/SAR)`,
                html: `
                    <div class="currency-display">
                        <div class="sar-amount">SAR ${amountSAR.toLocaleString()}</div>
                        <div class="pkr-amount">≈ PKR ${pkrAmount.toLocaleString()}</div>
                        <small class="exchange-rate">Rate: 1 SAR = ${this.exchangeRate} PKR</small>
                    </div>
                `
            };
        }
    };
    
})(jQuery);

// Initialize on page load
jQuery(document).ready(function($) {
    // Fetch exchange rate on page load
    if (typeof AsfaarCurrencyConverter !== 'undefined') {
        AsfaarCurrencyConverter.fetchExchangeRate();
    }
});
