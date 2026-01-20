/**
 * Price Calculator - ES5 Compatible for iOS Safari
 */
(function() {
    'use strict';

    var config = window.pricingConfig;
    if (!config) return;

    var currency = config.currency || '£';

    function findById(arr, id) {
        if (!arr || !id) return null;
        for (var i = 0; i < arr.length; i++) {
            if (arr[i].id === id) return arr[i];
        }
        return null;
    }

    function getDataPrice(el) {
        if (!el) return 0;
        return parseInt(el.getAttribute('data-price'), 10) || 0;
    }

    function formatPrice(num) {
        return currency + Math.round(num).toLocaleString();
    }

    function calculate() {
        var vSelect = document.getElementById('vehicleType');
        var cSelect = document.getElementById('coverageType');
        var fSelect = document.getElementById('finishType');
        var bSelect = document.getElementById('brandTier');
        var condSelect = document.getElementById('condition');

        var vehicleId = vSelect ? vSelect.value : '';
        var coverageId = cSelect ? cSelect.value : '';
        var finishId = fSelect ? fSelect.value : '';
        var brandId = bSelect ? bSelect.value : '';
        var conditionId = condSelect ? condSelect.value : '';

        var vehicle = findById(config.vehicleTypes, vehicleId);
        var coverage = findById(config.coverageTypes, coverageId);
        var finish = findById(config.finishTypes, finishId);
        var brand = findById(config.brandTiers, brandId);
        var condition = findById(config.conditions, conditionId);

        var totalPriceEl = document.getElementById('totalPrice');
        var priceBaseEl = document.getElementById('price-base');
        var rowVehicle = document.getElementById('row-vehicle');
        var rowFinish = document.getElementById('row-finish');
        var rowMaterial = document.getElementById('row-material');
        var rowCondition = document.getElementById('row-condition');
        var rowExtras = document.getElementById('row-extras');
        var priceVehicleEl = document.getElementById('price-vehicle');
        var priceFinishEl = document.getElementById('price-finish');
        var priceMaterialEl = document.getElementById('price-material');
        var priceConditionEl = document.getElementById('price-condition');
        var priceExtrasEl = document.getElementById('price-extras');

        if (!coverage) {
            if (totalPriceEl) totalPriceEl.textContent = currency + '0';
            if (priceBaseEl) priceBaseEl.textContent = '-';
            if (rowVehicle) rowVehicle.style.display = 'none';
            if (rowFinish) rowFinish.style.display = 'none';
            if (rowMaterial) rowMaterial.style.display = 'none';
            if (rowCondition) rowCondition.style.display = 'none';
            if (rowExtras) rowExtras.style.display = 'none';
            return;
        }

        var vehicleMult = vehicle ? vehicle.multiplier : 1;
        var finishMult = finish ? finish.multiplier : 1;
        var brandMult = brand ? brand.multiplier : 1;
        var conditionMult = condition ? condition.multiplier : 1;
        var basePrice = coverage.basePrice;

        var wrapSubtotal = basePrice * vehicleMult * finishMult * brandMult * conditionMult;

        if (priceBaseEl) priceBaseEl.textContent = formatPrice(basePrice);

        if (vehicle && vehicleMult !== 1) {
            var vehicleAdj = basePrice * (vehicleMult - 1);
            if (priceVehicleEl) priceVehicleEl.textContent = (vehicleAdj >= 0 ? '+' : '') + formatPrice(vehicleAdj);
            if (rowVehicle) rowVehicle.style.display = 'flex';
        } else {
            if (rowVehicle) rowVehicle.style.display = 'none';
        }

        if (finish && finishMult !== 1) {
            var finishAdj = basePrice * vehicleMult * (finishMult - 1);
            if (priceFinishEl) priceFinishEl.textContent = (finishAdj >= 0 ? '+' : '') + formatPrice(finishAdj);
            if (rowFinish) rowFinish.style.display = 'flex';
        } else {
            if (rowFinish) rowFinish.style.display = 'none';
        }

        if (brand && brandMult !== 1) {
            var brandAdj = basePrice * vehicleMult * finishMult * (brandMult - 1);
            if (priceMaterialEl) priceMaterialEl.textContent = (brandAdj >= 0 ? '+' : '') + formatPrice(brandAdj);
            if (rowMaterial) rowMaterial.style.display = 'flex';
        } else {
            if (rowMaterial) rowMaterial.style.display = 'none';
        }

        if (condition && conditionMult !== 1) {
            var condAdj = basePrice * vehicleMult * finishMult * brandMult * (conditionMult - 1);
            if (priceConditionEl) priceConditionEl.textContent = (condAdj >= 0 ? '+' : '') + formatPrice(condAdj);
            if (rowCondition) rowCondition.style.display = 'flex';
        } else {
            if (rowCondition) rowCondition.style.display = 'none';
        }

        var extrasTotal = 0;
        var doorShuts = document.getElementById('doorShuts');
        var wrapRemoval = document.getElementById('wrapRemoval');
        var addons = document.querySelectorAll('.addon-checkbox');

        if (doorShuts && doorShuts.checked) extrasTotal += getDataPrice(doorShuts);
        if (wrapRemoval && wrapRemoval.checked) extrasTotal += getDataPrice(wrapRemoval);
        for (var j = 0; j < addons.length; j++) {
            if (addons[j].checked) extrasTotal += getDataPrice(addons[j]);
        }

        if (extrasTotal > 0) {
            if (priceExtrasEl) priceExtrasEl.textContent = '+' + formatPrice(extrasTotal);
            if (rowExtras) rowExtras.style.display = 'flex';
        } else {
            if (rowExtras) rowExtras.style.display = 'none';
        }

        var finalTotal = wrapSubtotal + extrasTotal;
        if (totalPriceEl) totalPriceEl.textContent = formatPrice(finalTotal);
    }

    function attachListeners() {
        var selects = ['vehicleType', 'coverageType', 'finishType', 'brandTier', 'condition'];
        for (var i = 0; i < selects.length; i++) {
            var el = document.getElementById(selects[i]);
            if (el) {
                el.onchange = calculate;
                el.oninput = calculate;
            }
        }

        var checkboxes = document.querySelectorAll('#doorShuts, #wrapRemoval, .addon-checkbox');
        for (var j = 0; j < checkboxes.length; j++) {
            checkboxes[j].onchange = calculate;
            checkboxes[j].onclick = calculate;
        }
    }

    // Initialize
    function init() {
        attachListeners();
        calculate();
    }

    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Also run on window load as backup for iOS
    window.addEventListener('load', init);
})();
