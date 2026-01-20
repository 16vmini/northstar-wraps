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
    <section class="page-hero">
        <div class="container">
            <div class="page-hero-content" data-aos="fade-up">
                <h1>Price Calculator</h1>
                <p>Get an instant estimate for your vehicle wrap</p>
                <nav class="breadcrumb">
                    <a href="/">Home</a>
                    <span>/</span>
                    <span>Price Calculator</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Calculator Section -->
    <section class="calculator-section">
        <div class="container">
            <div class="calculator-wrapper" data-aos="fade-up">

                <!-- Calculator Form -->
                <div class="calculator-form">

                    <!-- Step 1: Vehicle Type -->
                    <div class="calc-step" data-step="1">
                        <div class="calc-step-header">
                            <span class="calc-step-number">1</span>
                            <h3>Select Your Vehicle Type</h3>
                        </div>
                        <div class="calc-options calc-options-grid">
                            <?php foreach ($pricingConfig['vehicleTypes'] as $vehicle): ?>
                            <label class="calc-option-card">
                                <input type="radio" name="vehicleType" value="<?php echo $vehicle['id']; ?>" data-multiplier="<?php echo $vehicle['multiplier']; ?>">
                                <div class="calc-option-content">
                                    <span class="calc-option-name"><?php echo $vehicle['name']; ?></span>
                                    <span class="calc-option-detail"><?php echo $vehicle['examples']; ?></span>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Step 2: Coverage Type -->
                    <div class="calc-step" data-step="2">
                        <div class="calc-step-header">
                            <span class="calc-step-number">2</span>
                            <h3>What Would You Like Wrapped?</h3>
                        </div>
                        <div class="calc-options calc-options-grid">
                            <?php foreach ($pricingConfig['coverageTypes'] as $coverage): ?>
                            <label class="calc-option-card">
                                <input type="radio" name="coverageType" value="<?php echo $coverage['id']; ?>" data-base-price="<?php echo $coverage['basePrice']; ?>">
                                <div class="calc-option-content">
                                    <span class="calc-option-name"><?php echo $coverage['name']; ?></span>
                                    <span class="calc-option-detail"><?php echo $coverage['description']; ?></span>
                                    <span class="calc-option-price">From <?php echo $pricingConfig['currency'] . number_format($coverage['basePrice']); ?></span>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Step 3: Finish Type -->
                    <div class="calc-step" data-step="3">
                        <div class="calc-step-header">
                            <span class="calc-step-number">3</span>
                            <h3>Choose Your Finish</h3>
                        </div>
                        <div class="calc-options calc-options-grid">
                            <?php foreach ($pricingConfig['finishTypes'] as $finish): ?>
                            <label class="calc-option-card">
                                <input type="radio" name="finishType" value="<?php echo $finish['id']; ?>" data-multiplier="<?php echo $finish['multiplier']; ?>">
                                <div class="calc-option-content">
                                    <span class="calc-option-name"><?php echo $finish['name']; ?></span>
                                    <span class="calc-option-detail"><?php echo $finish['description']; ?></span>
                                    <?php if ($finish['multiplier'] > 1): ?>
                                    <span class="calc-option-modifier">+<?php echo (($finish['multiplier'] - 1) * 100); ?>%</span>
                                    <?php endif; ?>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Step 4: Brand Tier -->
                    <div class="calc-step" data-step="4">
                        <div class="calc-step-header">
                            <span class="calc-step-number">4</span>
                            <h3>Select Material Quality</h3>
                        </div>
                        <div class="calc-options calc-options-list">
                            <?php foreach ($pricingConfig['brandTiers'] as $brand): ?>
                            <label class="calc-option-card calc-option-horizontal">
                                <input type="radio" name="brandTier" value="<?php echo $brand['id']; ?>" data-multiplier="<?php echo $brand['multiplier']; ?>">
                                <div class="calc-option-content">
                                    <span class="calc-option-name"><?php echo $brand['name']; ?></span>
                                    <span class="calc-option-detail"><?php echo $brand['description']; ?></span>
                                </div>
                                <span class="calc-option-modifier-right">
                                    <?php if ($brand['multiplier'] < 1): ?>
                                    -<?php echo ((1 - $brand['multiplier']) * 100); ?>%
                                    <?php elseif ($brand['multiplier'] > 1): ?>
                                    +<?php echo (($brand['multiplier'] - 1) * 100); ?>%
                                    <?php else: ?>
                                    Base
                                    <?php endif; ?>
                                </span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Step 5: Vehicle Condition -->
                    <div class="calc-step" data-step="5">
                        <div class="calc-step-header">
                            <span class="calc-step-number">5</span>
                            <h3>Vehicle Condition</h3>
                        </div>
                        <div class="calc-options calc-options-list">
                            <?php foreach ($pricingConfig['conditions'] as $condition): ?>
                            <label class="calc-option-card calc-option-horizontal">
                                <input type="radio" name="condition" value="<?php echo $condition['id']; ?>" data-multiplier="<?php echo $condition['multiplier']; ?>">
                                <div class="calc-option-content">
                                    <span class="calc-option-name"><?php echo $condition['name']; ?></span>
                                    <span class="calc-option-detail"><?php echo $condition['description']; ?></span>
                                </div>
                                <?php if ($condition['multiplier'] > 1): ?>
                                <span class="calc-option-modifier-right">+<?php echo (($condition['multiplier'] - 1) * 100); ?>%</span>
                                <?php endif; ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Step 6: Extras -->
                    <div class="calc-step" data-step="6">
                        <div class="calc-step-header">
                            <span class="calc-step-number">6</span>
                            <h3>Additional Options</h3>
                        </div>

                        <!-- Door Shuts -->
                        <div class="calc-extras-section">
                            <label class="calc-checkbox-card">
                                <input type="checkbox" name="doorShuts" value="1" data-price="<?php echo $pricingConfig['doorShuts']['price']; ?>">
                                <div class="calc-checkbox-content">
                                    <span class="calc-checkbox-name"><?php echo $pricingConfig['doorShuts']['label']; ?></span>
                                    <span class="calc-checkbox-detail"><?php echo $pricingConfig['doorShuts']['description']; ?></span>
                                </div>
                                <span class="calc-checkbox-price">+<?php echo $pricingConfig['currency'] . number_format($pricingConfig['doorShuts']['price']); ?></span>
                            </label>
                        </div>

                        <!-- Existing Wrap Removal -->
                        <div class="calc-extras-section">
                            <label class="calc-checkbox-card">
                                <input type="checkbox" name="wrapRemoval" value="1" data-price="<?php echo $pricingConfig['existingWrapRemoval']['price']; ?>">
                                <div class="calc-checkbox-content">
                                    <span class="calc-checkbox-name"><?php echo $pricingConfig['existingWrapRemoval']['label']; ?></span>
                                    <span class="calc-checkbox-detail"><?php echo $pricingConfig['existingWrapRemoval']['description']; ?></span>
                                </div>
                                <span class="calc-checkbox-price">+<?php echo $pricingConfig['currency'] . number_format($pricingConfig['existingWrapRemoval']['price']); ?></span>
                            </label>
                        </div>

                        <!-- Add-ons -->
                        <h4 class="calc-extras-title">Optional Add-ons</h4>
                        <div class="calc-extras-grid">
                            <?php foreach ($pricingConfig['addOns'] as $addon): ?>
                            <label class="calc-checkbox-card">
                                <input type="checkbox" name="addons[]" value="<?php echo $addon['id']; ?>" data-price="<?php echo $addon['price']; ?>">
                                <div class="calc-checkbox-content">
                                    <span class="calc-checkbox-name"><?php echo $addon['name']; ?></span>
                                    <span class="calc-checkbox-detail"><?php echo $addon['description']; ?></span>
                                </div>
                                <span class="calc-checkbox-price">+<?php echo $pricingConfig['currency'] . number_format($addon['price']); ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>

                <!-- Calculator Summary -->
                <div class="calculator-summary">
                    <div class="calc-summary-sticky">
                        <h3>Your Estimate</h3>

                        <div class="calc-summary-selections">
                            <div class="calc-summary-item" id="summary-vehicle">
                                <span class="calc-summary-label">Vehicle:</span>
                                <span class="calc-summary-value">-</span>
                            </div>
                            <div class="calc-summary-item" id="summary-coverage">
                                <span class="calc-summary-label">Coverage:</span>
                                <span class="calc-summary-value">-</span>
                            </div>
                            <div class="calc-summary-item" id="summary-finish">
                                <span class="calc-summary-label">Finish:</span>
                                <span class="calc-summary-value">-</span>
                            </div>
                            <div class="calc-summary-item" id="summary-brand">
                                <span class="calc-summary-label">Material:</span>
                                <span class="calc-summary-value">-</span>
                            </div>
                            <div class="calc-summary-item" id="summary-condition">
                                <span class="calc-summary-label">Condition:</span>
                                <span class="calc-summary-value">-</span>
                            </div>
                            <div class="calc-summary-item" id="summary-extras">
                                <span class="calc-summary-label">Extras:</span>
                                <span class="calc-summary-value">-</span>
                            </div>
                        </div>

                        <div class="calc-summary-breakdown" id="price-breakdown" style="display: none;">
                            <div class="calc-breakdown-item">
                                <span>Base price:</span>
                                <span id="breakdown-base"><?php echo $pricingConfig['currency']; ?>0</span>
                            </div>
                            <div class="calc-breakdown-item" id="breakdown-extras-row" style="display: none;">
                                <span>Add-ons:</span>
                                <span id="breakdown-extras"><?php echo $pricingConfig['currency']; ?>0</span>
                            </div>
                        </div>

                        <div class="calc-summary-total">
                            <span>Estimated Total:</span>
                            <span class="calc-total-price" id="total-price"><?php echo $pricingConfig['currency']; ?>0</span>
                        </div>

                        <p class="calc-summary-note">
                            <?php echo $pricingConfig['disclaimer']; ?>
                        </p>

                        <a href="/pages/contact.php" class="btn btn-primary btn-lg btn-block" id="get-quote-btn">
                            <i class="fas fa-paper-plane"></i> Get Exact Quote
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content" data-aos="fade-up">
                <h2>Ready to Transform Your Vehicle?</h2>
                <p>Get in touch for a free consultation and accurate quote</p>
                <div class="cta-buttons">
                    <a href="/pages/contact.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane"></i> Contact Us
                    </a>
                    <a href="/pages/gallery.php" class="btn btn-outline btn-lg">
                        <i class="fas fa-images"></i> View Our Work
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Pass config to JavaScript -->
    <script>
        window.pricingConfig = <?php echo json_encode($pricingConfig); ?>;
    </script>

<?php require_once '../includes/footer.php'; ?>
