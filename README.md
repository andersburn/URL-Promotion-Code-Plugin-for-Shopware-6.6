# URL Promotion Code Plugin for Shopware 6.6

## Title

**Send rabatkode med i URL – Shopware Plugin**

## Description

Make your Shopware campaigns smoother by letting customers redeem discounts directly from a URL. The plugin automatically detects a promotion code passed as a URL parameter and applies it to the customer’s cart or checkout session — no typing required.

**Written by:** Anders Bagnegaard
**Category:** Blog, Plugin, Shopware

### Why this plugin?

Imagine sending out a newsletter, running Meta ads, or sharing influencer links — and the discount is instantly applied when the customer clicks. This plugin removes all friction between your campaign and the checkout.

### Key Use Cases

* **Newsletters:** Include links like `https://dinshop.dk/?promotioncode=SOMMER25` to apply the discount automatically.
* **Influencer campaigns:** Give each partner a unique link so their followers don’t need to type or remember codes.
* **Retargeting / Meta Ads:** Use codes directly in ad URLs to maximize conversions.
* **QR codes / Flyers:** Print QR codes embedding discounts for physical campaigns.

### How it works

1. When a user visits your shop with `?promotioncode=CODE`, the plugin stores it in a browser cookie.
2. At checkout, if the code matches an active promotion, it’s automatically applied.
3. If the code is invalid or inactive, nothing happens — no errors are shown, ensuring a smooth experience.

### Installation

1. Download or clone the repository.
2. Upload it to your Shopware instance under **Custom Plugins**.
3. Install and activate it.
4. Create your promotion code in **Marketing > Promotions**.
5. Use a URL like: `https://dinshop.dk/?promotioncode=RABAT2025` — the plugin handles the rest.

### Technical Details

* Built for **Shopware 6.6**, compatible with 6.7.
* Lightweight and dependency-free.
* Uses Shopware’s Event Subscribers for request detection and cart updates.

### English Summary

Make your marketing campaigns frictionless with automatic discount code application from URLs. Perfect for newsletters, influencers, Meta Ads, and QR campaigns. Just add `?promotioncode=CODE` to your links, and the plugin applies the promotion automatically.

### Credits

Developed by Anders Bagnegaard
Inspired by Ecomwise’s concept, but built entirely from scratch with extended functionality.

---

## README.md

````markdown
# URL Promotion Code Plugin for Shopware 6.6

Automatically applies discount codes from URLs directly in your Shopware store.

## Overview
This Shopware plugin allows your customers to redeem discount codes by simply clicking a link. No more manual typing of promo codes — perfect for newsletters, ads, influencers, and QR campaigns.

**Example:**
```bash
https://yourshop.com/?promotioncode=SUMMER25
````

When the user visits this link, the plugin saves the code in a cookie and automatically applies it during checkout if valid.

## Features

* Auto-applies promotion codes from URL parameters
* No manual input required
* Works with newsletters, ads, and affiliate campaigns
* Fully compatible with Shopware 6.6 and likely 6.7

## Installation

1. Download or clone the repository.
2. Upload the plugin via the Shopware Admin or place it in `/custom/plugins/`.
3. Install and activate the plugin.
4. Create your promotion code in **Marketing → Promotions**.
5. Share URLs like:

   ```bash
   https://yourshop.com/?promotioncode=BLACKFRIDAY
   ```

## Technical

* Uses Shopware’s Event Subscribers for request and cart detection.
* Stores the promotion code in a browser cookie.
* Gracefully ignores invalid codes (no error message shown).

## Author

**Anders Bagnegaard**
Built from scratch and inspired by Ecomwise with added functionality.

## License

MIT License

## Compatibility

* Shopware 6.6+

## Example Use Cases

* Newsletters & Email Campaigns
* Influencer / Affiliate Links
* Retargeting / Meta Ads
* QR Codes & Flyers

## Screenshot / Demo

*(Add screenshot or demo link if available)*

---

**Less friction → more conversions.**

```
```
