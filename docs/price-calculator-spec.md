# Price Calculator Specification

## Overview
A dynamic calculator that provides customers with instant price estimates for vehicle wrapping services. This helps set expectations and generates qualified leads.

---

## Input Factors

### 1. Vehicle Size/Type
The primary cost driver - larger vehicles require more material and labour.

| Category | Examples | Multiplier |
|----------|----------|------------|
| Small | Smart Car, Fiat 500, Mini Cooper | 0.7x |
| Compact | VW Golf, Ford Fiesta, Audi A3 | 0.85x |
| Saloon | BMW 3 Series, Mercedes C-Class, Audi A4 | 1.0x (base) |
| Estate | BMW 3 Touring, Audi A4 Avant | 1.15x |
| Large Saloon | BMW 5 Series, Mercedes E-Class, Audi A6 | 1.2x |
| SUV (Small) | Nissan Juke, VW T-Roc | 1.1x |
| SUV (Medium) | Range Rover Sport, BMW X3, Audi Q5 | 1.3x |
| SUV (Large) | Range Rover, BMW X5, Mercedes GLE | 1.5x |
| Sports Car | Porsche 911, Audi TT | 1.1x |
| Supercar | Ferrari, Lamborghini, McLaren | 1.8x |
| Van (Small) | Ford Transit Connect, VW Caddy | 1.2x |
| Van (Large) | Ford Transit, Mercedes Sprinter | 1.8x |
| Pickup | Ford Ranger, Toyota Hilux | 1.4x |

### 2. Wrap Coverage
What areas of the vehicle are being wrapped.

| Coverage Type | Description | Base Price |
|---------------|-------------|------------|
| Full Wrap | All exterior panels | £2,500 |
| 3/4 Wrap | Full minus roof or bonnet | £2,000 |
| Half Wrap | Front or rear half only | £1,500 |
| Roof Only | Roof panel and pillars | £350 |
| Bonnet Only | Bonnet/hood | £250 |
| Roof + Bonnet | Common combo | £550 |
| Mirrors Only | Wing mirrors | £80 |
| Spoiler/Splitter | Aero parts | £150 |
| Custom Selection | Pick specific panels | Calculated |

### 3. Door Shuts / Jambs
Whether the inner door edges are wrapped (visible when doors open).

| Option | Price Addition |
|--------|----------------|
| No door shuts | +£0 |
| Door shuts included | +£300 - £500 |

**Note:** Door shuts significantly increase labour time but give a more OEM finish.

### 4. Wrap Material Type
Different finishes have different costs.

| Finish | Description | Price Modifier |
|--------|-------------|----------------|
| Gloss | High shine, like paint | Base price |
| Matte | Flat, non-reflective | +5% |
| Satin | Between gloss and matte | +5% |
| Metallic | Contains metal flakes | +10% |
| Gloss Metallic | Shiny with flakes | +10% |
| Pearl | Colour-shifting subtle | +15% |
| Chrome | Mirror finish | +80% |
| Colour Shift | Changes with angle | +60% |
| Carbon Fibre | Textured pattern | +15% |
| Brushed Metal | Directional texture | +20% |

### 5. Wrap Material Brand
Premium brands cost more but offer better durability and finish.

| Brand Tier | Examples | Price Modifier |
|------------|----------|----------------|
| Economy | Budget films | -15% |
| Standard | Avery, Oracal | Base price |
| Premium | 3M 2080, Avery Supreme | +15% |
| Ultra Premium | KPMF, Inozetek, Hexis | +25% |

### 6. Additional Services
Extras that can be added to any wrap job.

| Service | Description | Price |
|---------|-------------|-------|
| Chrome Delete | Black out chrome trim | £300 - £600 |
| Badge Removal | De-badge and wrap smooth | £50 - £100 |
| Badge Colour Change | Wrap or paint badges | £80 - £150 |
| Window Tints | While car is in | £150 - £400 |
| Headlight/Taillight Tint | Smoked lights | £80 - £200 |
| Ceramic Coating | Protect the wrap | £200 - £500 |
| Paint Correction | Pre-wrap prep if needed | £150 - £400 |

### 7. Vehicle Condition
Poor condition requires more prep work.

| Condition | Description | Price Modifier |
|-----------|-------------|----------------|
| Excellent | New or like-new paint | Base price |
| Good | Minor imperfections | +5% |
| Fair | Some chips, scratches | +10% |
| Poor | Significant damage, rust | +20% or quote required |

### 8. Complexity Factors
Additional considerations that affect difficulty.

| Factor | Description | Price Modifier |
|--------|-------------|----------------|
| Standard | Normal bodywork | Base price |
| Complex Curves | Bumpers, deep recesses | +10% |
| Wide Body Kit | Aftermarket panels | +15% |
| Carbon Parts | Existing carbon pieces | +10% |
| Wrap Removal | Existing wrap to remove | +£200 - £500 |

---

## Calculation Formula

```
Base Price (coverage type)
× Vehicle Size Multiplier
× Material Type Modifier
× Brand Tier Modifier
× Condition Modifier
× Complexity Modifier
+ Door Shuts (if selected)
+ Additional Services
= Estimated Total
```

---

## Example Calculations

### Example 1: Range Rover Sport Full Wrap
- Coverage: Full Wrap = £2,500
- Vehicle: SUV (Medium) = ×1.3
- Material: Satin = ×1.05
- Brand: 3M Premium = ×1.15
- Condition: Excellent = ×1.0
- Door Shuts: Yes = +£400
- Chrome Delete: Yes = +£400

**Calculation:**
£2,500 × 1.3 × 1.05 × 1.15 = £3,924 + £400 + £400 = **£4,724**

### Example 2: VW Golf Roof + Bonnet
- Coverage: Roof + Bonnet = £550
- Vehicle: Compact = ×0.85
- Material: Gloss Black = ×1.0
- Brand: Standard = ×1.0

**Calculation:**
£550 × 0.85 = **£467**

### Example 3: BMW 5 Series Chrome Delete Only
- Service: Chrome Delete = £500
- Vehicle: Large Saloon = ×1.1

**Calculation:**
£500 × 1.1 = **£550**

---

## UI/UX Considerations

### Step-by-Step Wizard
1. **Step 1:** Select vehicle type (with images/icons)
2. **Step 2:** Choose coverage type (visual diagram of car)
3. **Step 3:** Select wrap finish (colour swatches)
4. **Step 4:** Choose brand tier (with explanations)
5. **Step 5:** Add-ons and extras (checkboxes)
6. **Step 6:** Review and get quote

### Visual Elements
- Interactive car diagram showing selected panels
- Colour/finish preview swatches
- Real-time price update as options change
- Before/after gallery examples for each finish type

### Output
- Estimated price range (e.g., £4,500 - £5,000)
- Breakdown of costs
- "This is an estimate" disclaimer
- Call-to-action: "Get Exact Quote" → Contact form
- Option to save/email quote

---

## Disclaimer Text

> **Please Note:** This calculator provides an estimate only. Final pricing may vary based on:
> - Exact vehicle model and year
> - Current condition upon inspection
> - Specific colour/material availability
> - Custom design requirements
> - Current material costs
>
> Contact us for an accurate quote for your vehicle.

---

## Future Enhancements

1. **Vehicle Database** - Dropdown with specific makes/models with accurate sizing
2. **Photo Upload** - Customer uploads photos for more accurate assessment
3. **Design Preview** - 3D visualisation of colour on their car model
4. **Appointment Booking** - Book consultation directly from calculator
5. **Financing Calculator** - Monthly payment options
6. **Colour Matching** - Upload photo to match a specific colour
7. **Fleet Calculator** - Multi-vehicle discounts for businesses

---

## Technical Implementation Notes

### Data Storage
- Store pricing in database for easy updates
- Admin panel to adjust base prices and multipliers
- Log calculator usage for analytics

### Integration
- Connect to contact form with pre-filled vehicle/options
- Email quote to customer
- CRM integration for lead tracking

### Mobile
- Must work well on mobile devices
- Touch-friendly panel selection
- Easy scrolling through options
