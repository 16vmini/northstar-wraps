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

    <!-- Calculator Section -->
    <section class="calculator-section" style="background: #f5f5f5;">
        <div class="container">
            <div class="calculator-compact" data-aos="fade-up">

                <!-- Calculator Form -->
                <div class="calc-form-card">
                    <h3 class="calc-form-title"><i class="fas fa-calculator"></i> Configure Your Wrap</h3>

                    <div class="calc-form-grid">
                        <!-- Vehicle Type -->
                        <div class="calc-field">
                            <label for="vehicleType">
                                <i class="fas fa-car"></i> Vehicle Type
                            </label>
                            <select id="vehicleType" name="vehicleType">
                                <option value="">-- Select vehicle --</option>
                                <?php foreach ($pricingConfig['vehicleTypes'] as $vehicle): ?>
                                <option value="<?php echo $vehicle['id']; ?>">
                                    <?php echo $vehicle['name']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Coverage Type -->
                        <div class="calc-field">
                            <label for="coverageType">
                                <i class="fas fa-paint-roller"></i> Wrap Coverage
                            </label>
                            <select id="coverageType" name="coverageType">
                                <option value="">-- Select coverage --</option>
                                <?php foreach ($pricingConfig['coverageTypes'] as $coverage): ?>
                                <option value="<?php echo $coverage['id']; ?>">
                                    <?php echo $coverage['name']; ?> (from £<?php echo number_format($coverage['basePrice']); ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Finish Type -->
                        <div class="calc-field">
                            <label for="finishType">
                                <i class="fas fa-palette"></i> Wrap Finish
                            </label>
                            <select id="finishType" name="finishType">
                                <option value="">-- Select finish --</option>
                                <?php foreach ($pricingConfig['finishTypes'] as $finish): ?>
                                <option value="<?php echo $finish['id']; ?>">
                                    <?php echo $finish['name']; ?><?php echo $finish['multiplier'] > 1 ? ' (+' . round(($finish['multiplier'] - 1) * 100) . '%)' : ''; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Brand Tier -->
                        <div class="calc-field">
                            <label for="brandTier">
                                <i class="fas fa-award"></i> Material Quality
                            </label>
                            <select id="brandTier" name="brandTier">
                                <option value="">-- Select quality --</option>
                                <?php foreach ($pricingConfig['brandTiers'] as $brand): ?>
                                <option value="<?php echo $brand['id']; ?>">
                                    <?php echo $brand['name']; ?><?php
                                        if ($brand['multiplier'] < 1) echo ' (-' . round((1 - $brand['multiplier']) * 100) . '%)';
                                        elseif ($brand['multiplier'] > 1) echo ' (+' . round(($brand['multiplier'] - 1) * 100) . '%)';
                                    ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Vehicle Condition -->
                        <div class="calc-field calc-field-full">
                            <label for="condition">
                                <i class="fas fa-clipboard-check"></i> Vehicle Condition
                            </label>
                            <select id="condition" name="condition">
                                <option value="">-- Select condition --</option>
                                <?php foreach ($pricingConfig['conditions'] as $condition): ?>
                                <option value="<?php echo $condition['id']; ?>">
                                    <?php echo $condition['name']; ?> - <?php echo $condition['description']; ?><?php echo $condition['multiplier'] > 1 ? ' (+' . round(($condition['multiplier'] - 1) * 100) . '%)' : ''; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Extras Section -->
                    <div class="calc-extras">
                        <h4><i class="fas fa-plus-circle"></i> Optional Extras</h4>
                        <div class="calc-extras-grid">
                            <label class="calc-extra-item">
                                <input type="checkbox" id="doorShuts" data-price="<?php echo $pricingConfig['doorShuts']['price']; ?>">
                                <span class="extra-info">
                                    <strong>Door Shuts</strong>
                                    <small><?php echo $pricingConfig['doorShuts']['description']; ?></small>
                                </span>
                                <span class="extra-price">+£<?php echo $pricingConfig['doorShuts']['price']; ?></span>
                            </label>

                            <label class="calc-extra-item">
                                <input type="checkbox" id="wrapRemoval" data-price="<?php echo $pricingConfig['existingWrapRemoval']['price']; ?>">
                                <span class="extra-info">
                                    <strong>Existing Wrap Removal</strong>
                                    <small><?php echo $pricingConfig['existingWrapRemoval']['description']; ?></small>
                                </span>
                                <span class="extra-price">+£<?php echo $pricingConfig['existingWrapRemoval']['price']; ?></span>
                            </label>

                            <?php foreach ($pricingConfig['addOns'] as $addon): ?>
                            <label class="calc-extra-item">
                                <input type="checkbox" class="addon-checkbox" data-addon-id="<?php echo $addon['id']; ?>" data-price="<?php echo $addon['price']; ?>">
                                <span class="extra-info">
                                    <strong><?php echo $addon['name']; ?></strong>
                                    <small><?php echo $addon['description']; ?></small>
                                </span>
                                <span class="extra-price">+£<?php echo $addon['price']; ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Price Display -->
                <div class="calc-price-card">
                    <div class="calc-price-inner">
                        <h3><i class="fas fa-receipt"></i> Your Estimate</h3>

                        <div class="calc-price-breakdown" id="priceBreakdown">
                            <div class="breakdown-row" id="row-base">
                                <span>Base wrap price:</span>
                                <span id="price-base">-</span>
                            </div>
                            <div class="breakdown-row" id="row-vehicle" style="display:none;">
                                <span>Vehicle size:</span>
                                <span id="price-vehicle">-</span>
                            </div>
                            <div class="breakdown-row" id="row-finish" style="display:none;">
                                <span>Finish type:</span>
                                <span id="price-finish">-</span>
                            </div>
                            <div class="breakdown-row" id="row-material" style="display:none;">
                                <span>Material quality:</span>
                                <span id="price-material">-</span>
                            </div>
                            <div class="breakdown-row" id="row-condition" style="display:none;">
                                <span>Condition prep:</span>
                                <span id="price-condition">-</span>
                            </div>
                            <div class="breakdown-row" id="row-extras" style="display:none;">
                                <span>Extras:</span>
                                <span id="price-extras">-</span>
                            </div>
                        </div>

                        <div class="calc-price-total">
                            <span>Estimated Total</span>
                            <span class="total-amount" id="totalPrice"><?php echo $pricingConfig['currency']; ?>0</span>
                        </div>

                        <p class="calc-disclaimer">
                            <i class="fas fa-info-circle"></i> <?php echo $pricingConfig['disclaimer']; ?>
                        </p>

                        <a href="/pages/contact.php" class="btn btn-primary btn-lg btn-block">
                            <i class="fas fa-paper-plane"></i> Get Exact Quote
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Pass config to JavaScript -->
    <script>
        window.pricingConfig = <?php echo json_encode($pricingConfig); ?>;
        console.log('Calculator config loaded:', window.pricingConfig);
    </script>

<?php require_once '../includes/footer.php'; ?>
