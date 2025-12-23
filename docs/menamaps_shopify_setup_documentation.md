# MENA Maps - Shopify Store Setup Documentation

**Last Updated:** December 23, 2025
**Store URL:** https://menamaps.com
**Platform:** Shopify

---

## Table of Contents
1. [Account Structure](#account-structure)
2. [Store Configuration](#store-configuration)
3. [Collections Structure](#collections-structure)
4. [Product Strategy](#product-strategy)
5. [Fulfillment Setup](#fulfillment-setup)
6. [Policies](#policies)
7. [Analytics & Tracking](#analytics--tracking)
8. [Email Configuration](#email-configuration)
9. [Payment & Testing](#payment--testing)
10. [Market Strategy](#market-strategy)

---

## Account Structure

### Primary Accounts
- **Store Owner Account:** menamaps.com@gmail.com (full admin access)
- **Development Account:** Built initially under dotaim Shopify Partner account
- **Store Status:** Development mode (password protected)
- **Domain:** menamaps.com (connected and active)
  - Primary: menamaps.com
  - Redirect: www.menamaps.com → menamaps.com

### Account Philosophy
- Each project isolated with individual accounts
- Practice workflow mirrors real client projects
- Transfer ownership when ready to launch

---

## Store Configuration

### Basic Settings
- **Store Name:** MENA Maps
- **Tagline:** "Artistically illustrated"
- **Description:** Minimalist map prints of Middle Eastern and North African cities. Transform your favorite places into beautiful wall art - from Beirut to Cairo, Damascus to Dubai. Perfect for celebrating your heritage or decorating with meaningful style.
- **Customer Accounts:** Email-based (passwordless login via one-time code)

### Domain & DNS
- **Primary Domain:** menamaps.com
- **SSL:** Active
- **Status:** Connected and functional in dev mode

---

## Collections Structure

### Country Collections (24 Total)

**Arab Countries (22):**
1. Algeria
2. Bahrain
3. Comoros
4. Djibouti
5. Egypt
6. Iraq
7. Jordan
8. Kuwait
9. Lebanon
10. Libya
11. Mauritania
12. Morocco
13. Oman
14. Palestine
15. Qatar
16. Saudi Arabia
17. Somalia
18. Sudan
19. Syria
20. Tunisia
21. United Arab Emirates
22. Yemen

**Additional MENA Countries (2):**
23. Iran
24. Türkiye

### Product Type Collections

**Current Active Collections:**
- **Clothing**
  - T-Shirts
  - All Clothing
- **Home & Living**
  - Mugs
  - Posters
  - Shower Curtains
  - All Home & Living
- **Accessories**
  - Phone Cases
  - Mouse Pads
  - All Accessories
- **All Products**

**Future Product Type Collections:**
- Additional apparel (hoodies, tank tops, etc.)
- More home decor items
- Other accessories as needed

### Collection Organization Strategy
- Use tags for city names (e.g., "beirut", "cairo", "damascus")
- Country-based collections for geographic browsing
- Product-type collections for category browsing
- Tags enable flexible filtering without sub-collection complexity

---

## Product Strategy

### Current Test Products (Beirut Only)
- Beirut City Map Circular Shape With Border Framed Poster
- Beirut City Map Phone Case
- Beirut Map Black Mug 11oz
- Beirut Map Desk Mat
- Beirut Map Insulated Travel Mug 40oz
- Beirut Map Shower Curtain
- Guess The City Tee - Beirut

### Design Elements
- **Style:** Minimalist, clean map illustrations
- **Map Detail:** Street-level detail in circular or shaped masks
- **Text Overlays:** City names, coordinates, symbolic words
- **Shape Masks:** Circles, location pins, geometric shapes
- **Color Scheme:** Primary black/white with option for variations

### Tagging Strategy
- City name: `beirut`, `cairo`, `damascus`, etc.
- Product type: `poster`, `tshirt`, `mug`, etc.
- Style attributes: `minimalist`, `black-and-white`, etc.
- Use case: `gift`, `home-decor`, `apparel`, etc.

---

## Fulfillment Setup

### Print-on-Demand Provider
- **Primary:** Printify
- **Account:** Connected to MENA Maps Shopify store

### Printify Configuration

**Order Routing:**
- Status: Disabled (not using Printify Choice auto-routing)
- Routing criteria: Manual selection

**Order Submission:**
- Mode: **Manual** (requires manual approval before production)
- Rationale: Full control during testing and scaling phase
- Orders appear in Printify as "Action required" until manually submitted

**Personalized Orders:**
- Mode: **Manual** (requires manual approval)

**Tracking Notifications:**
- Status: Receive as soon as available

**Delayed Orders:**
- Mode: Manually send to production

### Order Flow
1. Customer places order in Shopify
2. Order syncs to Printify with "Action required" status
3. Review order in Printify dashboard
4. Manually submit for production
5. Printify handles printing and fulfillment
6. Tracking updates sent automatically

### Quality Control
- Monitor first orders closely for print quality
- Check map detail clarity on different products
- Verify color accuracy across product types

---

## Policies

### Return and Refund Policy

**Summary:** Made-to-order products, no returns except for defects/damages

**Full Policy Text:**

```
Return and Refund Policy

All products are made-to-order specifically for you. We do not accept returns or exchanges unless the item is defective or damaged.

Defective or Damaged Items

If you receive a defective or damaged product, please contact us at contact@menamaps.com within 30 days of delivery with:
- Order number
- Photo of the defect or damage
- Description of the issue

We will review your case and, if approved, send you a replacement at no additional cost.

Wrong Item Shipped

If you receive the wrong item, contact us at contact@menamaps.com within 30 days of delivery. We will send the correct item at no charge.

Order Cancellations

Orders can be cancelled within 24 hours of purchase. After production begins, orders cannot be cancelled.

Non-Returnable Items

As all items are custom-made to order, we cannot accept returns for:
- Change of mind
- Incorrect size ordered
- Color differences due to monitor settings

Questions?

Contact us at contact@menamaps.com for any concerns about your order.
```

### Shopify Return Rules
- **Status:** Turned OFF
- **Rationale:** POD business model doesn't support standard returns
- Handle defects case-by-case through Printify quality guarantee

### Privacy Policy & Terms
- Using Shopify default templates (to be customized before launch)
- Shipping policy reflects POD fulfillment times

---

## Analytics & Tracking

### Google Analytics 4
- **Account:** menamaps.com@gmail.com Google account
- **Property Name:** MENA Maps
- **Stream ID:** 13169350495
- **Measurement ID:** G-7W7MHM8KMT
- **Implementation:** Via Google & YouTube Shopify app

### App Permissions Granted
- ✅ See and download Google Analytics data
- ✅ View Google Tag Manager container and subcomponents
- ✅ See, edit, create, delete Google Ads accounts (for future use)
- ✅ Manage product listings for Google Shopping (for future use)

### Tracking Strategy
- **Dev Mode:** Track test data (minimal volume, acceptable)
- **Launch:** Continue with same property (test data negligible)
- Enhanced ecommerce tracking enabled

### Future Analytics Considerations
- Facebook Pixel (for social media advertising)
- TikTok Pixel (for TikTok ads)
- Pinterest Tag (for Pinterest marketing)

---

## Email Configuration

### Sender Email
- **Primary Contact:** contact@menamaps.com
- **Reply-To:** contact@menamaps.com
- **From Address:** MENA Maps <store+72511389879@t.shopifyemail.com>
  - Note: Shopify limitation - all stores send from @shopifyemail.com for deliverability
  - Customers see "MENA Maps" as sender name
  - Replies go to contact@menamaps.com

### Email Notifications
- Order confirmation: Active
- Shipping confirmation: Active (automated by Printify)
- Order cancellation: Active
- Customer account invitations: Active (passwordless login codes)

### Email Templates
- Using Shopify defaults
- Customized with brand colors/logo

---

## Payment & Testing

### Development Phase
- **Test Gateway:** Shopify Bogus Gateway (active)
- **Test Card:** Card number `1` or `4111111111111111`
- **Test Results:** Successful order flow from cart → checkout → Shopify → Printify

### Production Phase (Pre-Launch Setup Needed)
- **Primary Gateway:** Shopify Payments (to be activated)
- **Alternative:** PayPal (to be added as backup)
- **Supported Regions:** USA, Canada, Australia, New Zealand initially

### Tax Configuration
- **Shopify Tax Service:** Active
- Automatic tax calculation for applicable regions

### Shipping Rates
- **Provider:** Printify (automatic calculation)
- Rates added automatically based on product and destination
- International shipping: $8-15+ depending on product and destination

---

## Market Strategy

### Phase 1: Launch Markets (Priority Order)
1. **United States** (Primary)
   - Largest e-commerce market
   - Significant MENA diaspora
   - Straightforward regulations
   - Fast, affordable fulfillment via Printify US facilities

2. **Canada** (Secondary)
   - Large MENA diaspora (especially Lebanese, Egyptian, Syrian communities)
   - Similar consumer behavior to USA
   - Reasonable shipping costs

### Phase 2: Expansion Markets
3. **Australia**
   - English-speaking market
   - Growing diaspora communities
   - Good e-commerce adoption

4. **New Zealand**
   - Similar to Australia
   - Smaller but accessible market

### Phase 3: Future Consideration
5. **MENA Region**
   - Target market for diaspora gifts
   - Local customers with city pride
   - Challenge: High shipping costs ($15-25 from US/EU POD)
   - Opportunity: Explore local POD partnerships when scaled

6. **European Union**
   - Deferred due to complexity
   - VAT, GDPR, varied consumer protection laws
   - Requires significant resources to manage properly
   - Consider only after establishing strong presence in primary markets

### Market Strategy Rationale
- Start simple with English-speaking, regulation-friendly markets
- Build revenue and operational experience
- Expand to complex markets only when justified by scale
- MENA region requires local fulfillment solution to be viable

---

## Store Transfer & Launch Checklist

### Pre-Transfer Checklist
- ✅ Domain connected: menamaps.com
- ✅ Email configured: contact@menamaps.com
- ✅ Google Analytics setup: In progress
- ✅ Policies created: Return/Refund policy added
- ✅ Test orders completed successfully
- ⏳ Logo finalized (in progress)
- ⏳ Products created (testing phase - Beirut only)
- ⏳ About page content added

### Transfer Process
1. Verify menamaps.com@gmail.com has full admin permissions
2. From Shopify Partner dashboard: Transfer ownership
3. New owner accepts transfer
4. Select Shopify plan (billing starts)
5. Continue working as actual owner

### Launch Checklist
- [ ] Complete product catalog (5-10 major cities minimum)
- [ ] Add logo and favicon
- [ ] Customize email templates
- [ ] Remove password protection
- [ ] Activate real payment gateway (Shopify Payments)
- [ ] Final policy review
- [ ] Social media announcement posts ready
- [ ] Initial marketing campaigns planned
- [ ] Customer service protocols established

---

## Additional Notes

### Testing Philosophy
- Thorough testing in dev mode before transfer/launch
- Test complete customer journey: browse → cart → checkout → order
- Verify Printify integration at every step
- Acceptable to have minimal test data in GA4

### Client Workflow Template
This setup process serves as template for agency client projects:
1. Build under Partner account
2. Add client as admin with full permissions
3. Configure all settings in dev mode
4. Connect domain and test fully
5. Transfer ownership when ready to launch
6. Client selects plan and goes live

### Design Consistency
- Maintain minimalist aesthetic across all products
- Ensure map detail is legible at all product sizes
- Test print quality on various materials
- Cultural sensitivity in city selection and design choices

### Future Enhancements
- Blog for city stories and cultural content
- User-generated content features
- Custom map requests
- Bulk order discounts for corporate/event gifts
- Subscription service for map collectors

---

**Document Version:** 1.0
**Created:** December 23, 2025
**Purpose:** Reference for MENA Maps development and template for future client projects
