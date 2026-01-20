<?php
require_once '../includes/config.php';
$page_title = 'Price Calculator';
$page_description = 'Get an instant estimate for your vehicle wrap. Calculate costs for full wraps, partial wraps, chrome delete and more.';

// Load pricing config
$pricingConfigPath = __DIR__ . '/../includes/pricing-config.json';
$pricingConfig = json_decode(file_get_contents($pricingConfigPath), true);

require_once '../includes/header.php';
?>

    <!-- Page Hero -->
    <section class="page-hero page-hero-compact">
        <div class="container">
            <div class="page-hero-content" data-aos="fade-up">
                <h1>Price Calculator</h1>
                <p>Get an instant estimate for your vehicle wrap</p>
            </div>
        </div>
    </section>

    <!-- Calculator Styles (inline to ensure they load) -->
    <style>
    .calculator-section {
        padding: 60px 0 80px;
        background: #f5f5f5;
        overflow-x: hidden;
    }

    .calculator-section .container {
        overflow: visible;
    }

    .calculator-wrapper {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 40px;
        align-items: start;
        overflow: visible;
    }

    .calculator-form-inner,
    .extras-grid,
    .extra-item {
        box-sizing: border-box;
    }

    .calculator-form-inner {
        background: #fff;
        border-radius: 16px;
        padding: 35px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    .calculator-form-inner > h3 {
        font-family: 'Rajdhani', sans-serif;
        font-size: 1.4rem;
        font-weight: 600;
        color: #1a1a1a;
        margin: 0 0 25px 0;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .calculator-form-inner > h3 i {
        color: #7CB518;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-col {
        display: flex;
        flex-direction: column;
    }

    .form-col-full {
        grid-column: 1 / -1;
    }

    .form-col label {
        font-family: 'Rajdhani', sans-serif;
        font-size: 0.9rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-col label i {
        color: #7CB518;
        width: 16px;
    }

    .form-col select {
        width: 100%;
        padding: 12px 40px 12px 14px;
        font-family: 'Open Sans', sans-serif;
        font-size: 0.9rem;
        color: #333;
        background-color: #fff;
        border: 2px solid #ddd;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
    }

    .form-col select:hover {
        border-color: #7CB518;
    }

    .form-col select:focus {
        outline: none;
        border-color: #7CB518;
        box-shadow: 0 0 0 3px rgba(124, 181, 24, 0.2);
    }

    .extras-section {
        margin-top: 30px;
        padding-top: 25px;
        border-top: 2px solid #f0f0f0;
    }

    .extras-section h4 {
        font-family: 'Rajdhani', sans-serif;
        font-size: 1.1rem;
        font-weight: 600;
        color: #1a1a1a;
        margin: 0 0 15px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .extras-section h4 i {
        color: #7CB518;
    }

    .extras-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .extra-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        background: #f8f8f8;
        border: 2px solid transparent;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .extra-item:hover {
        background: #fff;
        border-color: #ddd;
    }

    .extra-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: #7CB518;
        cursor: pointer;
        flex-shrink: 0;
    }

    .extra-details {
        flex: 1;
        min-width: 0;
    }

    .extra-details strong {
        display: block;
        font-family: 'Rajdhani', sans-serif;
        font-size: 0.85rem;
        font-weight: 600;
        color: #333;
    }

    .extra-details small {
        display: block;
        font-size: 0.7rem;
        color: #888;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .extra-cost {
        font-family: 'Rajdhani', sans-serif;
        font-size: 0.9rem;
        font-weight: 700;
        color: #7CB518;
        white-space: nowrap;
        flex-shrink: 0;
        padding-left: 5px;
    }

    .calculator-result {
        position: sticky;
        top: 100px;
        z-index: 100;
        align-self: start;
    }

    .calculator-result-inner {
        background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
        border-radius: 16px;
        padding: 30px;
        color: #fff;
        box-shadow: 0 8px 30px rgba(0,0,0,0.3);
    }

    .calculator-result-inner > h3 {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.1rem;
        text-align: center;
        margin: 0 0 20px 0;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .calculator-result-inner > h3 i {
        color: #7CB518;
    }

    .price-breakdown {
        background: rgba(0,0,0,0.3);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .breakdown-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
        color: #bbb;
        padding: 8px 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .breakdown-line:last-child {
        border-bottom: none;
    }

    .breakdown-line span:last-child {
        font-family: 'Rajdhani', sans-serif;
        font-weight: 600;
        color: #fff;
    }

    .price-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 2px solid #7CB518;
        margin-bottom: 15px;
    }

    .price-total span:first-child {
        font-family: 'Rajdhani', sans-serif;
        font-size: 1rem;
        color: #bbb;
    }

    .total-value {
        font-family: 'Orbitron', sans-serif;
        font-size: 2.2rem;
        font-weight: 700;
        color: #7CB518;
    }

    .price-disclaimer {
        font-size: 0.7rem;
        color: #888;
        line-height: 1.5;
        margin: 0 0 20px 0;
        text-align: center;
        padding: 12px;
        background: rgba(0,0,0,0.2);
        border-radius: 8px;
    }

    .price-disclaimer i {
        color: #7CB518;
        margin-right: 5px;
    }

    .btn-block {
        display: block;
        width: 100%;
        text-align: center;
    }

    /* Mobile footer - hidden on desktop */
    .result-footer-mobile {
        display: none;
    }

    /* Desktop footer - shown by default */
    .result-footer-desktop {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid rgba(255,255,255,0.1);
    }

    @media (max-width: 1024px) {
        .calculator-wrapper {
            grid-template-columns: 1fr;
        }
        /* Adjust sticky for tablet/mobile */
        .calculator-result {
            top: 90px;
            order: -1;
        }
        /* Hide desktop footer, show mobile footer */
        .result-footer-desktop {
            display: none;
        }
        .result-footer-mobile {
            display: block;
            margin-top: 30px;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .result-footer-mobile .price-disclaimer {
            background: #f5f5f5;
            color: #666;
        }
        .result-footer-mobile .price-disclaimer i {
            color: #7CB518;
        }
        /* Make sticky box more compact */
        .calculator-result-inner {
            padding: 15px 20px;
        }
        .price-breakdown {
            display: none;
        }
        .price-total {
            padding: 0;
            margin: 0;
            border: none;
        }
        .total-value {
            font-size: 1.8rem;
        }
    }

    @media (max-width: 768px) {
        .calculator-section {
            padding: 40px 0 60px;
        }
        .calculator-section .container {
            padding-left: 15px;
            padding-right: 15px;
        }
        .form-row {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        .extras-grid {
            grid-template-columns: 1fr;
        }
        .calculator-form-inner {
            padding: 20px;
            border-radius: 12px;
        }
        .calculator-form-inner > h3 {
            font-size: 1.2rem;
        }
        .form-col select {
            padding: 10px 35px 10px 12px;
            font-size: 0.85rem;
        }
        .extra-item {
            padding: 10px 12px;
            padding-right: 8px;
        }
        .extra-details strong {
            font-size: 0.8rem;
        }
        .extra-details small {
            font-size: 0.65rem;
        }
        .extra-cost {
            font-size: 0.85rem;
            min-width: 50px;
            text-align: right;
        }
    }

    @media (max-width: 400px) {
        .calculator-section .container {
            padding-left: 12px;
            padding-right: 12px;
        }
        .calculator-form-inner {
            padding: 15px;
        }
        .calculator-result-inner {
            padding: 12px 15px;
        }
        .form-col label {
            font-size: 0.8rem;
        }
        .form-col select {
            padding: 10px 30px 10px 10px;
            font-size: 0.8rem;
        }
        .total-value {
            font-size: 1.5rem;
        }
    }
    </style>

    <!-- Calculator Section -->
    <section class="calculator-section">
        <div class="container">
            <div class="calculator-wrapper" data-aos="fade-up">

                <!-- Left: Form -->
                <div class="calculator-form">
                    <div class="calculator-form-inner">
                        <h3><i class="fas fa-calculator"></i> Configure Your Wrap</h3>

                        <div class="form-row">
                            <div class="form-col">
                                <label for="vehicleType"><i class="fas fa-car"></i> Vehicle Type</label>
                                <select id="vehicleType">
                                    <option value="">-- Select vehicle --</option>
                                    <?php foreach ($pricingConfig['vehicleTypes'] as $vehicle): ?>
                                    <option value="<?php echo $vehicle['id']; ?>"><?php echo $vehicle['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-col">
                                <label for="coverageType"><i class="fas fa-paint-roller"></i> Wrap Coverage</label>
                                <select id="coverageType">
                                    <option value="">-- Select coverage --</option>
                                    <?php foreach ($pricingConfig['coverageTypes'] as $coverage): ?>
                                    <option value="<?php echo $coverage['id']; ?>"><?php echo $coverage['name']; ?> (from £<?php echo number_format($coverage['basePrice']); ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-col">
                                <label for="finishType"><i class="fas fa-palette"></i> Wrap Finish</label>
                                <select id="finishType">
                                    <option value="">-- Select finish --</option>
                                    <?php foreach ($pricingConfig['finishTypes'] as $finish): ?>
                                    <option value="<?php echo $finish['id']; ?>"><?php echo $finish['name']; ?><?php echo $finish['multiplier'] > 1 ? ' (+' . round(($finish['multiplier'] - 1) * 100) . '%)' : ''; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-col">
                                <label for="brandTier"><i class="fas fa-award"></i> Material Quality</label>
                                <select id="brandTier">
                                    <option value="">-- Select quality --</option>
                                    <?php foreach ($pricingConfig['brandTiers'] as $brand): ?>
                                    <option value="<?php echo $brand['id']; ?>"><?php echo $brand['name']; ?><?php
                                        if ($brand['multiplier'] < 1) echo ' (-' . round((1 - $brand['multiplier']) * 100) . '%)';
                                        elseif ($brand['multiplier'] > 1) echo ' (+' . round(($brand['multiplier'] - 1) * 100) . '%)';
                                    ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-col form-col-full">
                                <label for="condition"><i class="fas fa-clipboard-check"></i> Vehicle Condition</label>
                                <select id="condition">
                                    <option value="">-- Select condition --</option>
                                    <?php foreach ($pricingConfig['conditions'] as $condition): ?>
                                    <option value="<?php echo $condition['id']; ?>"><?php echo $condition['name']; ?> - <?php echo $condition['description']; ?><?php echo $condition['multiplier'] > 1 ? ' (+' . round(($condition['multiplier'] - 1) * 100) . '%)' : ''; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Extras -->
                        <div class="extras-section">
                            <h4><i class="fas fa-plus-circle"></i> Optional Extras</h4>
                            <div class="extras-grid">
                                <label class="extra-item">
                                    <input type="checkbox" id="doorShuts" data-price="<?php echo $pricingConfig['doorShuts']['price']; ?>">
                                    <span class="extra-details">
                                        <strong>Door Shuts</strong>
                                        <small><?php echo $pricingConfig['doorShuts']['description']; ?></small>
                                    </span>
                                    <span class="extra-cost">+£<?php echo $pricingConfig['doorShuts']['price']; ?></span>
                                </label>

                                <label class="extra-item">
                                    <input type="checkbox" id="wrapRemoval" data-price="<?php echo $pricingConfig['existingWrapRemoval']['price']; ?>">
                                    <span class="extra-details">
                                        <strong>Existing Wrap Removal</strong>
                                        <small><?php echo $pricingConfig['existingWrapRemoval']['description']; ?></small>
                                    </span>
                                    <span class="extra-cost">+£<?php echo $pricingConfig['existingWrapRemoval']['price']; ?></span>
                                </label>

                                <?php foreach ($pricingConfig['addOns'] as $addon): ?>
                                <label class="extra-item">
                                    <input type="checkbox" class="addon-checkbox" data-addon-id="<?php echo $addon['id']; ?>" data-price="<?php echo $addon['price']; ?>">
                                    <span class="extra-details">
                                        <strong><?php echo $addon['name']; ?></strong>
                                        <small><?php echo $addon['description']; ?></small>
                                    </span>
                                    <span class="extra-cost">+£<?php echo $addon['price']; ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Price Card -->
                <div class="calculator-result">
                    <!-- Sticky estimate box (compact on mobile) -->
                    <div class="calculator-result-inner">
                        <div class="price-total">
                            <span>Estimated Total</span>
                            <span class="total-value" id="totalPrice">£0</span>
                        </div>

                        <div class="price-breakdown" id="priceBreakdown">
                            <div class="breakdown-line" id="row-base">
                                <span>Base wrap price:</span>
                                <span id="price-base">-</span>
                            </div>
                            <div class="breakdown-line" id="row-vehicle" style="display:none;">
                                <span>Vehicle size:</span>
                                <span id="price-vehicle">-</span>
                            </div>
                            <div class="breakdown-line" id="row-finish" style="display:none;">
                                <span>Finish type:</span>
                                <span id="price-finish">-</span>
                            </div>
                            <div class="breakdown-line" id="row-material" style="display:none;">
                                <span>Material quality:</span>
                                <span id="price-material">-</span>
                            </div>
                            <div class="breakdown-line" id="row-condition" style="display:none;">
                                <span>Condition prep:</span>
                                <span id="price-condition">-</span>
                            </div>
                            <div class="breakdown-line" id="row-extras" style="display:none;">
                                <span>Extras:</span>
                                <span id="price-extras">-</span>
                            </div>
                        </div>

                        <!-- Desktop only: disclaimer and button inside card -->
                        <div class="result-footer-desktop">
                            <p class="price-disclaimer">
                                <i class="fas fa-info-circle"></i> <?php echo $pricingConfig['disclaimer']; ?>
                            </p>
                            <a href="/pages/contact.php" class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-paper-plane"></i> Get Exact Quote
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Mobile only: disclaimer and CTA at bottom of form -->
                <div class="result-footer-mobile">
                    <p class="price-disclaimer">
                        <i class="fas fa-info-circle"></i> <?php echo $pricingConfig['disclaimer']; ?>
                    </p>
                    <a href="/pages/contact.php" class="btn btn-primary btn-lg btn-block">
                        <i class="fas fa-paper-plane"></i> Get Exact Quote
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- Pass config to JavaScript (before footer so it's available when calculator.js loads) -->
    <script>window.pricingConfig = <?php echo json_encode($pricingConfig); ?>;</script>

<?php require_once '../includes/footer.php'; ?>

    <!-- Calculator Script (loaded after footer, separate file for cleaner code) -->
    <script src="/assets/js/calculator.js?v=<?php echo CALC_VERSION; ?>"></script>
