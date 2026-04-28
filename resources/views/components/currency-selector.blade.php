{{-- Currency Selector Component --}}
<div class="currency-selector" id="currency-selector">
    <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle shadow-sm" type="button" id="currencyDropdown"
            data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 12px; background: white;">
            <i class="bi bi-currency-exchange me-1"></i> <span id="selected-currency-code">MYR</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="currencyDropdown" id="currency-list" style="border-radius: 12px; border: none;">
            <li><a class="dropdown-item" href="#" data-currency="MYR">MYR - Malaysian Ringgit</a></li>
            <li><a class="dropdown-item" href="#" data-currency="INR">INR - Indian Rupee</a></li>
            <li><a class="dropdown-item" href="#" data-currency="USD">USD - US Dollar</a></li>
            <li><a class="dropdown-item" href="#" data-currency="SGD">SGD - Singapore Dollar</a></li>
            <li><a class="dropdown-item" href="#" data-currency="AED">AED - UAE Dirham</a></li>
        </ul>
    </div>
</div>

<style>
    .currency-selector {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
    }
</style>

<script>
    (function () {
        // Get selected currency from localStorage or default to MYR
        let selectedCurrency = localStorage.getItem('selectedCurrency') || 'MYR';
        let exchangeRates = {};

        // Load exchange rates
        function loadExchangeRates() {
            $.get('/api/v1/currency/rates', function (response) {
                if (response.success && response.rates) {
                    response.rates.forEach(function (rate) {
                        const code = rate.code || rate.currency_code;
                        if (code) {
                            exchangeRates[code] = rate.exchange_rate || rate.rate_to_inr || 1.0;
                        }
                    });
                    // Set MYR rate to 1
                    exchangeRates['MYR'] = 1.0;

                    // Update currency selector
                    updateCurrencySelector();
                    // Convert all prices on page
                    convertAllPrices();
                }
            }).fail(function () {
                console.error('Failed to load exchange rates');
            });
        }

        // Update currency selector display
        function updateCurrencySelector() {
            $('#selected-currency-code').text(selectedCurrency);

            // Update dropdown items
            $('#currency-list .dropdown-item').each(function () {
                const currency = $(this).data('currency');
                if (currency === selectedCurrency) {
                    $(this).addClass('active');
                } else {
                    $(this).removeClass('active');
                }
            });
        }

        // Convert price from one currency to another
        function convertPrice(amount, fromCurrency, toCurrency) {
            if (fromCurrency === toCurrency || !amount) return amount;

            if (!exchangeRates[fromCurrency] || !exchangeRates[toCurrency]) {
                return amount;
            }

            // Convert to MYR first, then to target currency
            // Logic: BaseValue = Amount * Rate, TargetValue = BaseValue / TargetRate
            const amountInMYR = amount * exchangeRates[fromCurrency];
            const convertedAmount = amountInMYR / exchangeRates[toCurrency];

            return Math.round(convertedAmount * 100) / 100;
        }

        // Format currency with symbol
        function formatCurrency(amount, currency) {
            const symbols = {
                'INR': '₹',
                'USD': '$',
                'MYR': 'RM',
                'SGD': 'S$',
                'AED': 'AED'
            };

            const symbol = symbols[currency] || currency;
            return symbol + ' ' + parseFloat(amount).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Convert all prices on the page
        function convertAllPrices() {
            $('[data-price]').each(function () {
                const $element = $(this);
                const originalPrice = parseFloat($element.data('price'));
                const originalCurrency = $element.data('currency') || 'MYR';

                if (originalPrice && originalCurrency) {
                    const convertedPrice = convertPrice(originalPrice, originalCurrency, selectedCurrency);
                    $element.text(formatCurrency(convertedPrice, selectedCurrency));
                }
            });

            // Also handle elements with data-original-price attribute
            $('[data-original-price]').each(function () {
                const $element = $(this);
                const originalPrice = parseFloat($element.data('original-price'));
                const originalCurrency = $element.data('original-currency') || 'MYR';

                if (originalPrice && originalCurrency) {
                    const convertedPrice = convertPrice(originalPrice, originalCurrency, selectedCurrency);
                    $element.text(formatCurrency(convertedPrice, selectedCurrency));
                }
            });
        }

        // Handle currency selection
        $(document).on('click', '#currency-list .dropdown-item', function (e) {
            e.preventDefault();
            const newCurrency = $(this).data('currency');

            if (newCurrency !== selectedCurrency) {
                selectedCurrency = newCurrency;
                localStorage.setItem('selectedCurrency', selectedCurrency);
                updateCurrencySelector();
                convertAllPrices();
            }
        });

        // Initialize on page load
        $(document).ready(function () {
            loadExchangeRates();
            updateCurrencySelector();
        });

        // Expose functions globally for use in other scripts
        window.CurrencyConverter = {
            convert: convertPrice,
            format: formatCurrency,
            getSelectedCurrency: function () { return selectedCurrency; },
            convertAllPrices: convertAllPrices
        };
    })();
</script>