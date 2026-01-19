# North Star Wraps Website

A modern, responsive website for a car wrapping business built with PHP, HTML5, CSS3, and JavaScript.

## Features

- **Responsive Design**: Mobile-first approach, works on all devices
- **Modern UI**: Smooth animations, clean typography, professional look
- **SEO Ready**: Semantic HTML, meta tags, fast loading
- **Contact Form**: Full validation, email notifications, spam protection
- **Gallery**: Filterable portfolio with lightbox
- **Services**: Detailed service pages with pricing
- **Easy Customization**: All settings in config.php

## Project Structure

```
northstar/
├── index.php              # Home page
├── .htaccess              # Apache config (redirects, caching, security)
├── includes/
│   ├── config.php         # Site configuration (EDIT THIS FIRST)
│   ├── header.php         # Header template
│   ├── footer.php         # Footer template
│   └── process-form.php   # Form handler
├── pages/
│   ├── services.php       # Services page
│   ├── gallery.php        # Gallery/portfolio page
│   ├── about.php          # About us page
│   └── contact.php        # Contact page
├── assets/
│   ├── css/
│   │   └── style.css      # Main stylesheet
│   ├── js/
│   │   └── main.js        # JavaScript functionality
│   └── images/
│       └── logo.png       # Add your logo here
└── logs/
    └── quotes.log         # Quote submissions backup
```

## Installation

### 1. Upload Files
Upload all files to your web server via FTP or cPanel File Manager.

### 2. Configure Settings
Edit `includes/config.php` with your business details:

```php
define('SITE_NAME', 'North Star Wraps');
define('SITE_EMAIL', 'your-email@domain.com');
define('SITE_PHONE', '(555) 123-4567');
define('SITE_ADDRESS', 'Your Address Here');

// Social media links
define('SOCIAL_FACEBOOK', 'https://facebook.com/yourpage');
define('SOCIAL_INSTAGRAM', 'https://instagram.com/yourpage');
define('SOCIAL_TIKTOK', 'https://tiktok.com/@yourpage');
```

### 3. Add Logo
Replace `/assets/images/logo.png` with your actual logo.
Recommended: PNG with transparent background, ~200px height.

### 4. Add Gallery Images
Replace placeholder images in the gallery with actual photos.
Update the `$gallery_items` array in `pages/gallery.php`.

### 5. Set File Permissions
```
/logs/ folder: 755
/logs/quotes.log: 644 (created automatically)
```

### 6. Test Contact Form
Submit a test inquiry to verify email is working.
Check `/logs/quotes.log` for backup entries.

## Customization

### Colors
Edit CSS variables in `assets/css/style.css`:

```css
:root {
    --primary: #7CB518;      /* Lime green */
    --primary-dark: #5a8a0f;
    --primary-light: #9ed93d;
}
```

### Services
Edit the `$services` array in `includes/config.php` to add/modify services.

### Pricing
Update `price_from` values in the services array.

## Requirements

- PHP 7.4 or higher
- Apache with mod_rewrite enabled
- Mail function enabled (for contact form)

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## Future Features (Planned)

See `FUTURE_FEATURES.md` for the roadmap including:
- Wrap cost calculator
- Virtual wrap visualizer (upload car photo, preview colors)
- Online booking system
- Admin dashboard
- Customer portal

## Credits

- Fonts: Google Fonts (Orbitron, Rajdhani, Open Sans)
- Icons: Font Awesome 6
- Animations: AOS (Animate On Scroll)

## Support

For issues or questions, contact the developer.

---

Built with care for North Star Wraps
