# Wrapinator Service Plan

## Overview
Standalone SaaS service at **wrapinator.co.uk** providing AI-powered vehicle wrap visualization via REST API and embeddable widget.

## Tech Stack (cPanel Compatible)
- **Backend**: PHP 8.x
- **Database**: MySQL 8.x
- **Frontend**: Vanilla JS + Tailwind CSS (no build step needed)
- **Auth**: API keys (Bearer tokens) + session-based for dashboard
- **Hosting**: cPanel shared/VPS

---

## Database Schema

### `clients` (wrap shops/partners)
```sql
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    company VARCHAR(255),
    website VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'suspended', 'pending') DEFAULT 'pending',
    plan ENUM('free', 'starter', 'pro', 'enterprise') DEFAULT 'free'
);
```

### `api_keys`
```sql
CREATE TABLE api_keys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    api_key VARCHAR(64) UNIQUE NOT NULL,
    name VARCHAR(100) DEFAULT 'Default',
    allowed_domains TEXT,           -- JSON array of allowed origins
    rate_limit INT DEFAULT 100,     -- per day
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_used_at DATETIME,
    status ENUM('active', 'revoked') DEFAULT 'active',
    FOREIGN KEY (client_id) REFERENCES clients(id)
);
```

### `generations` (usage tracking)
```sql
CREATE TABLE generations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    api_key_id INT NOT NULL,
    share_id VARCHAR(16) UNIQUE NOT NULL,
    model ENUM('T-800', 'T-1000') NOT NULL,
    wrap_name VARCHAR(100),
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME,
    ip_address VARCHAR(45),
    user_agent TEXT,
    cost_credits DECIMAL(10,4) DEFAULT 0,
    FOREIGN KEY (api_key_id) REFERENCES api_keys(id)
);
```

### `plans` (pricing tiers)
```sql
CREATE TABLE plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    monthly_generations INT NOT NULL,
    price_monthly DECIMAL(10,2),
    features JSON
);

-- Initial data
INSERT INTO plans (name, monthly_generations, price_monthly, features) VALUES
('free', 10, 0, '{"models": ["T-800"], "support": "community"}'),
('starter', 100, 29.99, '{"models": ["T-800", "T-1000"], "support": "email"}'),
('pro', 500, 79.99, '{"models": ["T-800", "T-1000"], "support": "priority", "white_label": true}'),
('enterprise', -1, NULL, '{"models": ["T-800", "T-1000"], "support": "dedicated", "white_label": true, "custom_domain": true}');
```

---

## API Endpoints

### Authentication
All API calls require `Authorization: Bearer {api_key}` header.

### Generate Visualization
```
POST /api/v1/generate
Content-Type: application/json
Authorization: Bearer wk_xxxxxxxxxxxxx

{
    "car_image": "data:image/jpeg;base64,...",  // or URL
    "wrap": "satin-black",                       // wrap ID or...
    "wrap_color": "#FF0000",                     // custom hex color
    "wrap_image": "data:image/...",              // custom pattern (T-1000 only)
    "model": "T-800",                            // T-800 or T-1000
    "webhook_url": "https://..."                 // optional callback
}

Response:
{
    "success": true,
    "job_id": "abc123",
    "status": "processing",
    "estimated_time": 30
}
```

### Check Job Status
```
GET /api/v1/jobs/{job_id}
Authorization: Bearer wk_xxxxxxxxxxxxx

Response:
{
    "job_id": "abc123",
    "status": "completed",
    "result": {
        "image_url": "https://wrapinator.co.uk/output/abc123.png",
        "share_url": "https://wrapinator.co.uk/share/abc123",
        "wrap": "Satin Black",
        "model": "T-800"
    }
}
```

### Get Available Wraps
```
GET /api/v1/wraps
Authorization: Bearer wk_xxxxxxxxxxxxx

Response:
{
    "categories": [
        {
            "name": "Gloss",
            "wraps": [
                {"id": "gloss-black", "name": "Gloss Black", "hex": "#000000"},
                ...
            ]
        }
    ]
}
```

### Usage Stats
```
GET /api/v1/usage
Authorization: Bearer wk_xxxxxxxxxxxxx

Response:
{
    "period": "2025-01",
    "used": 47,
    "limit": 100,
    "remaining": 53,
    "plan": "starter"
}
```

---

## Directory Structure

```
wrapinator.co.uk/
├── api/
│   └── v1/
│       ├── generate.php
│       ├── jobs.php
│       ├── wraps.php
│       ├── usage.php
│       └── webhook-test.php
├── includes/
│   ├── config.php
│   ├── database.php
│   ├── auth.php              # API key validation
│   ├── rate-limiter.php
│   ├── replicate-client.php  # Replicate API wrapper
│   └── helpers.php
├── dashboard/                 # Client portal
│   ├── index.php             # Dashboard home
│   ├── api-keys.php          # Manage API keys
│   ├── usage.php             # Usage stats
│   ├── settings.php          # Account settings
│   ├── billing.php           # Subscription management
│   └── assets/
├── embed/                     # Embeddable widget
│   ├── widget.js             # Drop-in script
│   ├── widget.css
│   └── iframe.php            # Full iframe version
├── public/                    # Public pages
│   ├── index.php             # Landing page
│   ├── pricing.php
│   ├── docs.php              # API documentation
│   ├── register.php
│   ├── login.php
│   └── share.php             # Shared image viewer
├── output/                    # Generated images
├── assets/
│   ├── wraps/
│   │   └── wraps.json
│   └── images/
├── logs/
└── .htaccess
```

---

## Integration Options for Partners

### Option 1: Embed Widget (Easiest)
```html
<!-- Drop this in your page -->
<div id="wrapinator-widget" data-key="wk_xxxxx"></div>
<script src="https://wrapinator.co.uk/embed/widget.js"></script>
```

### Option 2: iFrame
```html
<iframe
    src="https://wrapinator.co.uk/embed/iframe.php?key=wk_xxxxx"
    width="100%"
    height="800"
    frameborder="0">
</iframe>
```

### Option 3: Server-Side API (Full Control)
```php
// Partner's server calls Wrapinator API
$ch = curl_init('https://wrapinator.co.uk/api/v1/generate');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer wk_xxxxxxxxxxxxx',
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'car_image' => $base64_image,
        'wrap' => 'satin-black',
        'model' => 'T-800'
    ])
]);
$response = curl_exec($ch);
```

---

## Security Measures

1. **API Key Validation**
   - Keys prefixed with `wk_` for identification
   - 32-byte random hex (64 chars)
   - Domain allowlist (CORS + referer check)

2. **Rate Limiting**
   - Per-key daily limits based on plan
   - Sliding window counter in MySQL
   - 429 response when exceeded

3. **Request Validation**
   - Image size limits (10MB max)
   - Valid image format check
   - Sanitize all inputs

4. **CORS**
   - Only allow origins in client's allowed_domains
   - No wildcard in production

---

## Migration Path from North Star

### Phase 1: Core API
- [ ] Set up new repo/hosting
- [ ] Database + migrations
- [ ] Auth middleware
- [ ] Port visualize.php -> /api/v1/generate
- [ ] Port visualize-v2.php -> /api/v1/generate (T-1000)
- [ ] Rate limiting

### Phase 2: Dashboard
- [ ] Client registration/login
- [ ] API key management
- [ ] Usage stats display
- [ ] Basic billing page (manual/Stripe later)

### Phase 3: Integration Tools
- [ ] Embeddable widget JS
- [ ] iFrame embed
- [ ] API documentation page

### Phase 4: Update North Star
- [ ] Replace direct API calls with Wrapinator client
- [ ] Use own API key
- [ ] Remove visualize.php / visualize-v2.php

---

## Pricing Model (Suggested)

| Plan       | Generations/mo | T-1000 | White Label | Price    |
|------------|----------------|--------|-------------|----------|
| Free       | 10             | No     | No          | £0       |
| Starter    | 100            | Yes    | No          | £29/mo   |
| Pro        | 500            | Yes    | Yes         | £79/mo   |
| Enterprise | Unlimited      | Yes    | Yes         | Custom   |

---

## Questions to Decide

1. **Domain**: wrapinator.co.uk confirmed? yep
2. **Hosting**: Same cPanel or separate? new account on same server
3. **Billing**: Start manual or integrate Stripe from day 1? intergrate / paypal ? 
4. **Wraps library**: Same as North Star or expanded? same 
5. **Gallery**: Shared public gallery across all partners or per-client? shared
6. **Timeline**: MVP target date? when ever , asap
