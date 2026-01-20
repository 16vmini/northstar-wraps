<?php
require_once '../includes/config.php';

// Load pricing config
$pricingConfigPath = __DIR__ . '/../includes/pricing-config.json';
$pricingConfig = json_decode(file_get_contents($pricingConfigPath), true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculator Debug Test</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
            padding: 15px;
            margin: 0;
            font-size: 14px;
        }
        h1 { font-size: 18px; margin: 0 0 15px; }
        .debug-box {
            background: #111;
            color: #0f0;
            padding: 10px;
            font-family: monospace;
            font-size: 11px;
            max-height: 200px;
            overflow-y: auto;
            margin-bottom: 15px;
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }
        select {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 2px solid #ccc;
            border-radius: 8px;
            background: #fff;
            -webkit-appearance: none;
        }
        .result {
            background: #222;
            color: #7CB518;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            margin: 15px 0;
        }
        .info {
            background: #e3f2fd;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <h1>Calculator Debug Test</h1>

    <div class="info">
        <strong>User Agent:</strong><br>
        <span id="ua"></span>
    </div>

    <div class="debug-box" id="debug">Debug output will appear here...</div>

    <div class="result" id="result">£0</div>

    <div class="form-group">
        <label for="vehicle">Vehicle Type</label>
        <select id="vehicle">
            <option value="">-- Select --</option>
            <?php foreach ($pricingConfig['vehicleTypes'] as $v): ?>
            <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?> (<?php echo $v['multiplier']; ?>x)</option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="coverage">Coverage Type</label>
        <select id="coverage">
            <option value="">-- Select --</option>
            <?php foreach ($pricingConfig['coverageTypes'] as $c): ?>
            <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?> (£<?php echo $c['basePrice']; ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>

    <button onclick="testCalc()" style="width:100%;padding:15px;font-size:16px;background:#7CB518;color:#fff;border:none;border-radius:8px;margin-top:10px;">
        Manual Calculate
    </button>

    <script>
        // Store config
        var config = <?php echo json_encode($pricingConfig); ?>;

        // Debug logger
        var debugEl = document.getElementById('debug');
        function log(msg) {
            var time = new Date().toLocaleTimeString();
            debugEl.innerHTML = '[' + time + '] ' + msg + '<br>' + debugEl.innerHTML;
            console.log(msg);
        }

        // Show user agent
        document.getElementById('ua').textContent = navigator.userAgent;

        log('Page loaded');
        log('Config loaded: ' + (config ? 'YES' : 'NO'));
        log('Coverage types: ' + (config.coverageTypes ? config.coverageTypes.length : 0));

        // Get elements
        var vehicleEl = document.getElementById('vehicle');
        var coverageEl = document.getElementById('coverage');
        var resultEl = document.getElementById('result');

        log('Vehicle select: ' + (vehicleEl ? 'FOUND' : 'NOT FOUND'));
        log('Coverage select: ' + (coverageEl ? 'FOUND' : 'NOT FOUND'));

        // Find by ID helper
        function findById(arr, id) {
            if (!arr || !id) return null;
            for (var i = 0; i < arr.length; i++) {
                if (arr[i].id === id) return arr[i];
            }
            return null;
        }

        // Calculate function
        function testCalc() {
            log('--- CALCULATE TRIGGERED ---');

            var vId = vehicleEl.value;
            var cId = coverageEl.value;

            log('Vehicle value: "' + vId + '"');
            log('Coverage value: "' + cId + '"');
            log('Vehicle selectedIndex: ' + vehicleEl.selectedIndex);
            log('Coverage selectedIndex: ' + coverageEl.selectedIndex);

            var vehicle = findById(config.vehicleTypes, vId);
            var coverage = findById(config.coverageTypes, cId);

            log('Vehicle found: ' + (vehicle ? vehicle.name : 'NULL'));
            log('Coverage found: ' + (coverage ? coverage.name : 'NULL'));

            if (!coverage) {
                resultEl.textContent = '£0';
                log('No coverage - showing £0');
                return;
            }

            var mult = vehicle ? vehicle.multiplier : 1;
            var base = coverage.basePrice;
            var total = Math.round(base * mult);

            log('Base: £' + base + ' x ' + mult + ' = £' + total);

            resultEl.textContent = '£' + total.toLocaleString();
            log('Result updated to £' + total);
        }

        // Attach events - multiple methods
        log('Attaching event listeners...');

        // Method 1: onchange
        vehicleEl.onchange = function() {
            log('EVENT: vehicle onchange');
            testCalc();
        };
        coverageEl.onchange = function() {
            log('EVENT: coverage onchange');
            testCalc();
        };

        // Method 2: oninput
        vehicleEl.oninput = function() {
            log('EVENT: vehicle oninput');
            testCalc();
        };
        coverageEl.oninput = function() {
            log('EVENT: coverage oninput');
            testCalc();
        };

        // Method 3: addEventListener
        vehicleEl.addEventListener('change', function() {
            log('EVENT: vehicle addEventListener change');
        });
        coverageEl.addEventListener('change', function() {
            log('EVENT: coverage addEventListener change');
        });

        // Method 4: touch events for iOS
        vehicleEl.addEventListener('touchend', function() {
            log('EVENT: vehicle touchend');
            setTimeout(testCalc, 100);
        });
        coverageEl.addEventListener('touchend', function() {
            log('EVENT: coverage touchend');
            setTimeout(testCalc, 100);
        });

        log('All listeners attached');
        log('Ready - select options to test');
    </script>
</body>
</html>
