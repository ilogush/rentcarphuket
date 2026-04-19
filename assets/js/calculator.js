/**
 * Unified Price Calculator for Rent Car Phuket.
 * Fetches calculated prices from /api/calculate_price.php
 */
const RentalCalculator = {
    async calculate(params) {
        const query = new URLSearchParams(params).toString();
        try {
            const response = await fetch(`/api/calculate_price.php?${query}`);
            return await response.json();
        } catch (e) {
            console.error('Calculation error:', e);
            return null;
        }
    },

    updateUI(containerId, data) {
        const container = document.getElementById(containerId);
        if (!container || !data || data.error) return;

        // Update days
        const daysEl = document.getElementById('calc-days');
        if (daysEl) daysEl.textContent = data.days;

        // Update daily price
        const dailyEl = document.getElementById('calc-daily');
        if (dailyEl) {
            if (data.daily_price < data.daily_base) {
                dailyEl.innerHTML = `
                    <div class="font-black text-gray-800 leading-none">฿${Math.round(data.daily_price)}</div>
                    <div class="text-[10px] text-gray-400 line-through">฿${Math.round(data.daily_base)}</div>
                `;
            } else {
                dailyEl.innerHTML = `<div class="font-black text-gray-800">฿${Math.round(data.daily_base)}</div>`;
            }
        }

        // Update delivery/return
        const deliveryEl = document.getElementById('calc-delivery');
        if (deliveryEl) deliveryEl.textContent = '฿' + Math.round(data.delivery_price);

        const returnEl = document.getElementById('calc-return');
        if (returnEl) returnEl.textContent = '฿' + Math.round(data.return_price);

        // Update total
        const totalEl = document.getElementById('calc-total');
        if (totalEl) totalEl.textContent = '฿' + Math.round(data.total);
    }
};
