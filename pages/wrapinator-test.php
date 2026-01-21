<?php
/**
 * Wrapinator Integration Test Page
 * Test page for wrapinator.co.uk embed integration
 */

$pageTitle = "Wrapinator Test";
$pageDescription = "Testing Wrapinator.co.uk integration";
$currentPage = 'wrapinator-test';

// Test configuration
$wrapinatorDomain = 'https://wrapinator.co.uk';
$testApiKey = 'wk_aa031df03b7854ce46a11046260ee1f8838fa56e6d0d0951483f327ce0ba';

require_once __DIR__ . '/../includes/header.php';
?>

<style>
    .test-page {
        padding: 60px 20px;
        min-height: 100vh;
        background: #0a0a0a;
    }

    .test-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .test-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .test-header h1 {
        font-size: 2rem;
        color: #fff;
        margin-bottom: 10px;
    }

    .test-header p {
        color: #888;
    }

    .test-config {
        background: #1a1a1a;
        border: 1px solid #333;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
    }

    .test-config h3 {
        color: #7cb518;
        margin-bottom: 15px;
        font-size: 1rem;
    }

    .config-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .config-item {
        background: #0a0a0a;
        padding: 12px;
        border-radius: 8px;
    }

    .config-item label {
        display: block;
        font-size: 0.75rem;
        color: #666;
        margin-bottom: 5px;
        text-transform: uppercase;
    }

    .config-item code {
        color: #7cb518;
        font-family: monospace;
        word-break: break-all;
    }

    .test-options {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }

    .test-option {
        flex: 1;
        min-width: 300px;
        background: #1a1a1a;
        border: 1px solid #333;
        border-radius: 12px;
        padding: 20px;
    }

    .test-option h3 {
        color: #fff;
        margin-bottom: 10px;
        font-size: 1rem;
    }

    .test-option p {
        color: #888;
        font-size: 0.875rem;
        margin-bottom: 15px;
    }

    .test-option pre {
        background: #0a0a0a;
        padding: 15px;
        border-radius: 8px;
        overflow-x: auto;
        font-size: 0.8rem;
        color: #ccc;
    }

    .test-option pre code {
        color: #7cb518;
    }

    .embed-section {
        background: #1a1a1a;
        border: 1px solid #333;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
    }

    .embed-section h3 {
        color: #fff;
        margin-bottom: 20px;
    }

    .embed-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .embed-tab {
        padding: 10px 20px;
        background: #0a0a0a;
        border: 1px solid #333;
        border-radius: 8px;
        color: #888;
        cursor: pointer;
        transition: all 0.2s;
    }

    .embed-tab:hover {
        border-color: #7cb518;
        color: #fff;
    }

    .embed-tab.active {
        background: #7cb518;
        border-color: #7cb518;
        color: #fff;
    }

    .iframe-container {
        background: #0a0a0a;
        border-radius: 12px;
        overflow: hidden;
        min-height: 600px;
    }

    .iframe-container iframe {
        width: 100%;
        height: 700px;
        border: none;
    }

    .widget-container {
        background: #0a0a0a;
        border-radius: 12px;
        padding: 20px;
        min-height: 600px;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-pending {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }

    .status-ready {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .test-notes {
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 12px;
        padding: 20px;
        margin-top: 30px;
    }

    .test-notes h4 {
        color: #3b82f6;
        margin-bottom: 10px;
    }

    .test-notes ul {
        color: #94a3b8;
        padding-left: 20px;
    }

    .test-notes li {
        margin-bottom: 8px;
    }
</style>

<div class="test-page">
    <div class="test-container">
        <div class="test-header">
            <h1>Wrapinator Integration Test</h1>
            <p>Testing embed integration with wrapinator.co.uk</p>
            <span class="status-badge status-pending">Awaiting Configuration</span>
        </div>

        <!-- Configuration Display -->
        <div class="test-config">
            <h3>Current Configuration</h3>
            <div class="config-grid">
                <div class="config-item">
                    <label>Wrapinator Domain</label>
                    <code><?= htmlspecialchars($wrapinatorDomain) ?></code>
                </div>
                <div class="config-item">
                    <label>API Key</label>
                    <code><?= htmlspecialchars($testApiKey) ?></code>
                </div>
                <div class="config-item">
                    <label>Theme</label>
                    <code>dark</code>
                </div>
                <div class="config-item">
                    <label>Branding</label>
                    <code>hidden (white-label)</code>
                </div>
            </div>
        </div>

        <!-- Integration Options -->
        <div class="test-options">
            <div class="test-option">
                <h3>Option 1: iFrame Embed</h3>
                <p>Simple iframe integration - just add to your page</p>
                <pre><code>&lt;iframe
  src="<?= $wrapinatorDomain ?>/embed/iframe?key=YOUR_API_KEY&theme=dark&branding=false"
  width="100%"
  height="700"
  frameborder="0"
&gt;&lt;/iframe&gt;</code></pre>
            </div>

            <div class="test-option">
                <h3>Option 2: JavaScript Widget</h3>
                <p>More control with JS widget - customizable</p>
                <pre><code>&lt;div id="wrapinator-widget"
     data-key="YOUR_API_KEY"
     data-theme="dark"
     data-branding="false"&gt;
&lt;/div&gt;
&lt;script src="<?= $wrapinatorDomain ?>/embed/widget.js"&gt;&lt;/script&gt;</code></pre>
            </div>
        </div>

        <!-- Live Test Section -->
        <div class="embed-section">
            <h3>Live Test</h3>

            <div class="embed-tabs">
                <button class="embed-tab active" onclick="showEmbed('iframe')">iFrame Embed</button>
                <button class="embed-tab" onclick="showEmbed('widget')">JS Widget</button>
            </div>

            <!-- iFrame Test -->
            <div id="iframe-test" class="iframe-container">
                <iframe
                    src="<?= $wrapinatorDomain ?>/embed/iframe?key=<?= urlencode($testApiKey) ?>&theme=dark&branding=false"
                    allow="clipboard-write"
                ></iframe>
            </div>

            <!-- Widget Test -->
            <div id="widget-test" class="widget-container" style="display: none;">
                <div id="wrapinator-widget"
                     data-key="<?= htmlspecialchars($testApiKey) ?>"
                     data-theme="dark"
                     data-branding="false">
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="test-notes">
            <h4>Integration Notes</h4>
            <ul>
                <li>Replace <code>wk_test_xxxxx</code> with your actual API key from the Wrapinator dashboard</li>
                <li>Ensure your domain is added to the allowed domains list in your Wrapinator API settings</li>
                <li>Set <code>branding=false</code> for white-label (requires Pro plan or higher)</li>
                <li>The widget will load wraps from your Wrapinator account</li>
                <li>Generations will be counted against your API usage limits</li>
                <li>For production, use HTTPS for both domains</li>
            </ul>
        </div>
    </div>
</div>

<script>
function showEmbed(type) {
    // Update tabs
    document.querySelectorAll('.embed-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    event.target.classList.add('active');

    // Show/hide containers
    document.getElementById('iframe-test').style.display = type === 'iframe' ? 'block' : 'none';
    document.getElementById('widget-test').style.display = type === 'widget' ? 'block' : 'none';

    // Load widget script if showing widget
    if (type === 'widget' && !window.WrapinatorWidget) {
        const script = document.createElement('script');
        script.src = '<?= $wrapinatorDomain ?>/embed/widget.js';
        document.body.appendChild(script);
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
