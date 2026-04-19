/**
 * RentalPriceCalculator
 * 
 * JavaScript/TypeScript version of the rental price calculator
 * Suitable for client-side price calculations and real-time updates
 * 
 * @example
 * ```javascript
 * const calculator = new RentalPriceCalculator({
 *   minTotal: 1500,
 *   roundTo: 1,
 *   platformFeePercent: 7,
 * });
 * 
 * const result = calculator.calculate({
 *   dailyPrice: 2500,
 *   days: 10,
 *   seasonMultiplier: 1.2,
 *   classMultiplier: 1.1,
 *   extras: {
 *     childSeat: 300,
 *     delivery: 500,
 *   },
 *   deposit: 10000,
 *   includeDepositInTotal: false,
 * });
 * ```
 */
class RentalPriceCalculator {
  /**
   * @param {Object} config
   * @param {number} [config.minTotal=0] - Minimum final price
   * @param {number} [config.roundTo=1] - Rounding: 1 = to baht, 10 = to tens
   * @param {Array} [config.longTermDiscounts] - Long-term rental discounts
   * @param {number} [config.platformFeePercent=0] - Platform commission percentage
   */
  constructor(config = {}) {
    this.config = {
      minTotal: 0,
      roundTo: 1,
      longTermDiscounts: [
        { minDays: 7, percent: 5 },
        { minDays: 14, percent: 10 },
        { minDays: 30, percent: 15 },
      ],
      platformFeePercent: 0,
      ...config,
    };
  }

  /**
   * Calculate rental price with full breakdown
   * 
   * @param {Object} input
   * @param {number} input.dailyPrice - Base daily price
   * @param {number} input.days - Number of rental days
   * @param {number} [input.seasonMultiplier=1.0] - Season coefficient
   * @param {number} [input.classMultiplier=1.0] - Car class coefficient
   * @param {Object} [input.extras={}] - Extra options {name: price}
   * @param {number} [input.deposit=0] - Deposit amount
   * @param {boolean} [input.includeDepositInTotal=false] - Include deposit in final total
   * 
   * @returns {Object} Calculation result with breakdown
   */
  calculate(input) {
    const dailyPrice = parseFloat(input.dailyPrice || 0);
    const days = Math.max(1, parseInt(input.days || 1));
    const seasonMultiplier = parseFloat(input.seasonMultiplier || 1.0);
    const classMultiplier = parseFloat(input.classMultiplier || 1.0);
    const extras = input.extras || {};
    const deposit = parseFloat(input.deposit || 0);
    const includeDepositInTotal = Boolean(input.includeDepositInTotal);

    // 1. Base price
    let base = dailyPrice * days;

    // 2. Season coefficient
    let seasonAdjusted = base * seasonMultiplier;

    // 3. Car class coefficient
    let classAdjusted = seasonAdjusted * classMultiplier;

    // 4. Extra options
    let extrasTotal = 0;
    for (const price of Object.values(extras)) {
      extrasTotal += parseFloat(price);
    }

    // 5. Long-term rental discount
    const discountPercent = this.resolveDiscountPercent(days);
    let discount = (classAdjusted * discountPercent) / 100;

    // 6. Platform fee
    const platformFeePercent = parseInt(this.config.platformFeePercent);
    let platformFee = ((classAdjusted - discount + extrasTotal) * platformFeePercent) / 100;

    // 7. Subtotal
    let total = classAdjusted - discount + extrasTotal + platformFee;

    // 8. Deposit
    let finalTotal = includeDepositInTotal ? (total + deposit) : total;

    // 9. Minimum threshold
    const minTotal = parseFloat(this.config.minTotal);
    if (finalTotal < minTotal) {
      finalTotal = minTotal;
    }

    // 10. Rounding
    const roundTo = Math.max(1, parseInt(this.config.roundTo));
    base = this.roundAmount(base, roundTo);
    seasonAdjusted = this.roundAmount(seasonAdjusted, roundTo);
    classAdjusted = this.roundAmount(classAdjusted, roundTo);
    extrasTotal = this.roundAmount(extrasTotal, roundTo);
    discount = this.roundAmount(discount, roundTo);
    platformFee = this.roundAmount(platformFee, roundTo);
    const depositRounded = this.roundAmount(deposit, roundTo);
    total = this.roundAmount(total, roundTo);
    finalTotal = this.roundAmount(finalTotal, roundTo);

    return {
      base,
      seasonAdjusted,
      classAdjusted,
      extrasTotal,
      discount,
      platformFee,
      deposit: depositRounded,
      total,
      finalTotal,
      breakdown: {
        dailyPrice,
        days,
        seasonMultiplier,
        classMultiplier,
        discountPercent,
        platformFeePercent,
        includeDepositInTotal,
        extras,
      },
    };
  }

  /**
   * Returns discount percentage based on rental duration
   * @private
   */
  resolveDiscountPercent(days) {
    let bestPercent = 0;
    for (const tier of this.config.longTermDiscounts) {
      if (days >= parseInt(tier.minDays)) {
        bestPercent = Math.max(bestPercent, parseInt(tier.percent));
      }
    }
    return bestPercent;
  }

  /**
   * Round to nearest value
   * @private
   */
  roundAmount(value, roundTo) {
    return Math.round(value / roundTo) * roundTo;
  }

  /**
   * Format price for display
   * @param {number} amount
   * @param {string} [currency='฿']
   * @returns {string}
   */
  formatPrice(amount, currency = '฿') {
    return `${amount.toLocaleString()} ${currency}`;
  }

  /**
   * Generate HTML breakdown for display
   * @param {Object} result - Result from calculate()
   * @returns {string} HTML string
   */
  generateBreakdownHTML(result) {
    const lines = [];
    
    lines.push(`<div class="price-breakdown">`);
    lines.push(`  <div class="breakdown-line"><span>Base Price:</span><span>${this.formatPrice(result.base)}</span></div>`);
    
    if (result.breakdown.seasonMultiplier !== 1.0) {
      lines.push(`  <div class="breakdown-line"><span>After Season (×${result.breakdown.seasonMultiplier}):</span><span>${this.formatPrice(result.seasonAdjusted)}</span></div>`);
    }
    
    if (result.breakdown.classMultiplier !== 1.0) {
      lines.push(`  <div class="breakdown-line"><span>After Class (×${result.breakdown.classMultiplier}):</span><span>${this.formatPrice(result.classAdjusted)}</span></div>`);
    }
    
    if (result.discount > 0) {
      lines.push(`  <div class="breakdown-line discount"><span>Discount (${result.breakdown.discountPercent}%):</span><span>-${this.formatPrice(result.discount)}</span></div>`);
    }
    
    if (result.extrasTotal > 0) {
      lines.push(`  <div class="breakdown-line"><span>Extras:</span><span>+${this.formatPrice(result.extrasTotal)}</span></div>`);
      for (const [name, price] of Object.entries(result.breakdown.extras)) {
        lines.push(`  <div class="breakdown-line extra"><span>  • ${name}:</span><span>${this.formatPrice(price)}</span></div>`);
      }
    }
    
    if (result.platformFee > 0) {
      lines.push(`  <div class="breakdown-line"><span>Platform Fee (${result.breakdown.platformFeePercent}%):</span><span>+${this.formatPrice(result.platformFee)}</span></div>`);
    }
    
    lines.push(`  <div class="breakdown-line subtotal"><span>Subtotal:</span><span>${this.formatPrice(result.total)}</span></div>`);
    
    if (result.deposit > 0) {
      const depositLabel = result.breakdown.includeDepositInTotal ? 'included' : 'separate';
      lines.push(`  <div class="breakdown-line"><span>Deposit (${depositLabel}):</span><span>${this.formatPrice(result.deposit)}</span></div>`);
    }
    
    lines.push(`  <div class="breakdown-line total"><span>FINAL TOTAL:</span><span>${this.formatPrice(result.finalTotal)}</span></div>`);
    lines.push(`  <div class="breakdown-line daily-rate"><span>Daily Rate:</span><span>${this.formatPrice(Math.round(result.finalTotal / result.breakdown.days))}/day</span></div>`);
    lines.push(`</div>`);
    
    return lines.join('\n');
  }
}

// Export for Node.js/CommonJS
if (typeof module !== 'undefined' && module.exports) {
  module.exports = RentalPriceCalculator;
}

// Export for ES6 modules
if (typeof exports !== 'undefined') {
  exports.RentalPriceCalculator = RentalPriceCalculator;
}
