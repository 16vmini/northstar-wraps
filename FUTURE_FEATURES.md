# North Star Wraps - Future Feature Ideas

## Phase 2 - Dynamic Features

### 1. Wrap Cost Calculator
- Select vehicle type/size (sedan, SUV, truck, van)
- Choose coverage (full, partial, specific panels)
- Pick material/finish type
- Get instant ballpark estimate
- Integration with quote form

### 2. Virtual Wrap Visualizer (Car Color Changer)
- **User uploads photo of their vehicle**
- AI/Canvas-based color replacement tool
- Choose from available wrap colors/finishes
- See their actual car in different wrap options
- Before/after slider comparison
- Save/share visualizations
- Direct "Get Quote" with visualization attached

**Tech considerations:**
- Could use Canvas API for basic color overlay
- More advanced: ML-based car segmentation (remove.bg API or similar)
- Color mapping/hue shifting for realistic preview
- Save visualizations to database linked to quotes

### 3. Online Booking System
- Calendar integration
- Appointment scheduling for consultations
- Automated reminders (email/SMS)
- Deposit payment integration

### 4. Customer Portal
- Track project status
- View past work/invoices
- Warranty information
- Care instructions

### 5. Admin Dashboard
- Manage gallery items (CRUD)
- View/manage quote requests
- Customer database
- Analytics dashboard
- Appointment management

## Phase 3 - Advanced Features

### 6. Interactive Wrap Designer
- Choose vehicle template (silhouette)
- Apply colors to different panels
- Add decals/graphics
- Custom text placement
- Export design for quote

### 7. Fleet Management Portal
- Business customer accounts
- Multi-vehicle quotes
- Bulk scheduling
- Fleet branding consistency tools

### 8. Referral Program
- Customer referral tracking
- Discount codes generation
- Automated rewards

### 9. Review/Testimonial System
- Automated review requests post-service
- Integration with Google Reviews
- Photo upload with reviews
- Featured reviews on homepage

## Database Schema Ideas (for when we add MySQL)

```sql
-- Quotes/Leads
CREATE TABLE quotes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    service_type VARCHAR(50),
    vehicle_year VARCHAR(4),
    vehicle_make VARCHAR(50),
    vehicle_model VARCHAR(50),
    message TEXT,
    visualization_path VARCHAR(255), -- For saved car visualizations
    status ENUM('new', 'contacted', 'quoted', 'booked', 'completed', 'lost'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Gallery Items
CREATE TABLE gallery (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100),
    category VARCHAR(50),
    description TEXT,
    color VARCHAR(100),
    image_path VARCHAR(255),
    before_image VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    display_order INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Appointments
CREATE TABLE appointments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quote_id INT,
    appointment_date DATE,
    appointment_time TIME,
    service_type VARCHAR(50),
    notes TEXT,
    status ENUM('scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Customers
CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    address TEXT,
    total_spent DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Priority Order
1. Wrap Cost Calculator (high impact, relatively simple)
2. **Virtual Wrap Visualizer** (unique selling point, wow factor)
3. Admin Dashboard (operational efficiency)
4. Online Booking (convenience)
5. Customer Portal (retention)
