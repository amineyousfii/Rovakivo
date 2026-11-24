# Rovakivo launch-readiness audit

**Date:** 20 November 2026
**Site:** http://7bar.local (Local by WP Engine)
**Stack:** WordPress 7.1.5, WooCommerce 11.3.0, Rovakivo Storefront plugin v2.0.0

**How this was checked.** Every "Passed" or "Fixed and verified" status below was checked on this local copy in one of three ways:
- **Crawl:** a crawl of the page HTML.
- **Headless Chrome:** scripted runs in headless Chrome, at widths of 320, 375, 390, 768, 1024, 1440 and 1920 px.
- **WP-CLI:** queries of the database and settings.

**Evidence.** The screenshots are in `7bar\screenshots\audit\`. The test scripts and their raw results are in `7bar\audit-tools\`.

**Kinds of check.** "Dashboard" means a setting stored in WordPress. "Local" means behaviour seen on this copy. "Production" items can only be confirmed on the live domain and are listed separately.

**What was not touched:**
- payment settings
- product IDs, SKUs, prices, stock and variations
- reviews and orders
- WordPress and WooCommerce core files

No orders were placed. Checkout was only submitted with empty fields, to test validation.

---

## 1. Audit table

| # | Requirement | Page / route | Initial issue | Change made | Final status | Verification evidence | Remaining action |
|---|---|---|---|---|---|---|---|
| 1 | Page inventory and internal links | All 36 crawled URLs | None found | — | **Passed** | Local: crawl (`crawl2.json`) found no broken internal links, no demo links and no external links | — |
| 2 | Custom 404 | Any unknown URL | Page returned HTTP 404, but its heading overflowed by 2 px at 320 px | 404 grid items allowed to shrink; heading wraps | **Fixed and verified** | Local: HTTP 404; overflow 0 px at 320 px (`notfound-320.png`) | — |
| 3 | Unique meta titles | All pages | Filtered and sale views, and account sub-pages, repeated other titles | Titles per view in `inc/seo.php`: "Sale", "Casual on Sale", "Sign In", "Reset Your Password", category titles, "– Page N" | **Fixed and verified** | Local: crawl. The only repeats left are logged-out account sub-pages (they really are the sign-in form) and search URL variants; all are `noindex` | — |
| 4 | Meta descriptions | Home, shop, categories, products, pages | Missing on archives | Descriptions output for every indexable page | **Fixed and verified** | Local: crawl shows 98–142 characters on every indexable page | — |
| 5 | Canonical URLs | Shop, categories, home | No canonical on archives | Clean canonicals (with `page/N` on later pages); none on search, 404 or filtered views | **Fixed and verified** | Local: crawl | Production: confirm they use the live domain with https |
| 6 | Index control | Filters, sale, search, cart, checkout, account | Filtered and sale listings were indexable | `noindex,follow` on filtered and sale views | **Fixed and verified** | Local: crawl. Cart, account and search were already `noindex` | — |
| 7 | robots.txt | /robots.txt | None | — | **Passed** | Local: 200, points to `/wp-sitemap.xml`; "Discourage search engines" is off | Production: re-check after go-live |
| 8 | XML sitemap (WordPress core, no second sitemap) | /wp-sitemap.xml | It listed cart, checkout and account pages, MailPoet pages, the empty blog, tags and the admin's author page | Those providers and pages removed; author archives now 404 | **Fixed and verified** | Local: sitemap lists 5 pages, 12 products and 2 categories. Users, posts and tags sitemaps return 404, and so does `/author/amine/` | Production: submit `/wp-sitemap.xml` in Search Console |
| 9 | Open Graph and Twitter cards, 1200×630 default | All pages | No share image or alt text; product images were WebP | Default 1200×630 JPG share image; products use their own JPG; og/twitter alt text | **Fixed and verified** | Local: crawl shows og:image and alt on every page; image returns 200 | Production: test with Facebook/LinkedIn preview tools |
| 10 | Structured data | Home, products | No homepage JSON-LD | Organization and WebSite (with search) on the homepage | **Fixed and verified** | Local: crawl shows 1 JSON-LD block on the homepage and every product | Production: run the Rich Results test |
| 11 | Favicon set and manifest | All pages | No web manifest | `site.webmanifest` (192/512 icons, theme colour) linked | **Fixed and verified** | Local: ico, svg, 32, 192, apple-touch and manifest all return 200; 6 icon links per page | — |
| 12 | Clear first-screen action | Home | None | — | **Passed** | Local: "Shop backpacks" is fully visible at all 7 widths (`home-390.png`, `home-1440.png`) | — |
| 13 | Clear first-screen action | Product pages (phone) | Add to cart was 960–1100 px down on phones | Sticky buy bar (row 15) | **Fixed and verified** | Local: bar is visible on load at 390 px (`product-390.png`) and hides when the real form is on screen | — |
| 14 | Mobile and desktop layouts | 13 templates × 7 widths | 404 overflow only (row 2) | See row 2 | **Fixed and verified** | Local: no horizontal scroll on any page at 320–1920 px (`views.json`) | Check on real iOS and Android devices |
| 15 | Sticky mobile buy button | Product pages under 700 px | Did not exist | New bar that uses WooCommerce's real add-to-cart. Simple products add straight away. Variable products ask for options first, then show "Add to cart" with the chosen price. Grouped products jump to the quantities. Hidden when out of stock, while a menu or drawer is open, while typing and while the cookie banner is up. Respects the iPhone safe area | **Fixed and verified** | Local: `t_sticky.mjs`. A simple add raised the cart to 1; a variable add (Black/Small) raised it to 2; grouped focuses the quantity; hidden at 1440 px; no console errors | — |
| 16 | Variation photo | Minimal Zip Backpack | Choosing a colour/size didn't change the photo | Matching photo shown and its thumbnail marked; reset restores the first photo | **Fixed and verified** | Local: Grey/Medium shows photo 221, Black/Small shows photo 223 | — |
| 17 | Product completeness | 12 products | Weight and dimensions empty on all | None (real measurements needed) | **Blocked** | WP-CLI: SKU, price, photo + alt, description, short description, category and meta description present on all 12 | Owner: supply weight and dimensions (also needed for weight-based shipping) |
| 18 | Mini-cart drawer | Shop grid add-to-cart | None | — | **Passed** | Local: AJAX add opens the drawer with focus inside, shows the count, and Escape closes it with focus returned (`state-minicart-drawer-1440.png`) | — |
| 19 | Cart: quantity, remove, empty | /cart/ | None (slow only on this server) | — | **Passed** | Local: quantity 1→2 updates totals (70→140 €) and announces "Cart updated."; remove shows "removed. Undo?"; empty state has a "back to shop" link | — |
| 20 | Coupon errors | /cart/ | None | — | **Passed** | Local: invalid code shows "…does not exist." inline with `aria-invalid` (`state-coupon-error-1440.png`) | No coupons exist yet (0) |
| 21 | Checkout validation | /checkout/ | None | — | **Passed** | Local: empty submit gives 8 named errors and highlights 13 fields; an empty cart redirects to /cart/ (`state-checkout-errors-1440.png`) | — |
| 22 | End-to-end payment | /checkout/ | No payment method enabled ("no available payment methods") | None (you asked not to change payment settings) | **Blocked** | Local: checkout message; no gateway enabled | Owner: finish WooPayments or PayPal onboarding in test/sandbox mode, then run a test order |
| 23 | Order confirmation | /checkout/order-received/ | Can't be reached without a payment method | — | **Not verified** | Previewed in an earlier session with a temporary order; not re-run | Re-test after row 22 |
| 24 | Account forms | /my-account/, lost password | None | — | **Passed** | Local: empty sign-in gives "Username is required."; an unknown address on reset gives "Invalid username or email." (`state-lost-password-error-1440.png`) | — |
| 25 | Signed-in account pages | /my-account/* | Not checked | — | **Not verified** | Needs a test login, which I can't enter on this host | Owner: sign in and click through Orders, Addresses and Account details |
| 26 | Search, including no results | /?s= | None | — | **Passed** | Local: helpful "Nothing found" state with search box and filters (`search-390.png`) | — |
| 27 | Contact form | /contact/ | None | — | **Passed** | Local: empty submit shows 3 required-field errors; the success message was set earlier. Real email delivery not tested | Production: send a test message once email (SMTP) is set up |
| 28 | Newsletter form | Footer and home | None | — | **Passed** | Local: email field is required and labelled, and notes the confirmation email | Production: check MailPoet's sending method |
| 29 | Policy pages | Footer, checkout | Privacy setting pointed to a draft page; no Terms page; no shipping or returns page | Privacy setting fixed to the published page 363; cookie section added to it | **Fixed and verified** (privacy), **Blocked** (others) | WP-CLI: privacy page 363; terms page not set; no returns page | Owner: provide Terms, Shipping and Returns/Refund text (I won't write legal terms) |
| 30 | Business information | Footer, contact, legal | No store address, phone or company details | None (nothing invented) | **Blocked** | WP-CLI: store address empty, country PT | Owner: legal name, address, contact email/phone, company and VAT numbers if required |
| 31 | Shipping and tax | Dashboard | Free shipping to Portugal only; selling to all countries; taxes off | None | **Blocked** | WP-CLI and cart: "Free shipping" | Owner: confirm countries, rates and VAT handling |
| 32 | Cookie consent that blocks tracking | All pages | WooCommerce order attribution set 7 `sbjs_*` cookies before any consent | Rovakivo banner ("Essential only" / "Allow") and a "Cookie settings" footer link. Attribution is off unless allowed (server and browser); choosing "Essential only" deletes `sbjs_*` cookies | **Fixed and verified** | Local: no cookies and tracking off before a choice; "Allow" sets attribution cookies; "Essential only" leaves only the consent cookie, which persists across reloads (`consent-banner-390.png`) | Production: re-check once any analytics or ads tags are added |
| 33 | Third-party requests | All pages | Google Fonts loaded from Google's servers, which sent each visitor's IP address to Google | With your OK, DM Sans and Manrope (Latin and Latin Extended, OFL licence) are now self-hosted in `assets/fonts/`. The two main files are preloaded, and the Google stylesheet and DNS hints are removed | **Fixed and verified** | Local: every request across home, product, cart and checkout at 390 and 1440 px went to `7bar.local` only. Both fonts load at every weight (62 KB + 24 KB) and the look is unchanged (`fonts-selfhosted-home-1440.png`, `t_fonts.mjs`) | — |
| 34 | Analytics and ecommerce events | — | No analytics installed | — | **Not applicable** (until you choose a tool) | Local: no analytics requests | Owner: choose a tool and an ID; it must load only after "Allow" |
| 35 | Images and alt text | All pages | Cart-page thumbnail link had no accessible name | Thumbnail gets the product name as alt text | **Fixed and verified** | Local: every image has an alt attribute and width/height; none broken or oversized | — |
| 36 | Page weight and stability | Home, shop, product, contact | WooCommerce zoom/lightbox and comment-reply scripts loaded but unused on product pages | Those scripts and styles removed from product pages | **Fixed and verified** | Local, cold cache: 220–580 KB, 28–43 requests, layout shift ≤ 0.045 (`perf.json`) | Production: measure with PageSpeed Insights. Server response here was 1.4–7.8 s and isn't representative |
| 37 | Console errors | Cart, checkout, product | PayPal Payments (not onboarded) loads its script and its token call returns 500 | None (payment configuration) | **Blocked** | Local: `[PPCP SDK v6] Failed to generate client token`; no errors from the storefront code | Owner: onboard PayPal (test mode) or deactivate it if unused |
| 38 | Motion quality | All pages | None | — | **Passed** | Local: GSAP, Lenis and the motion script load once; nothing stays hidden after a full scroll; with reduced motion nothing animates or hides and Lenis is off; with motion scripts blocked all content stays visible | — |
| 39 | Keyboard and focus | Header, drawers | None | — | **Passed** | Local: skip link present; menu traps focus, Escape closes, focus returns; footer links are 44 px tall on phones | — |
| 40 | Visual consistency | All templates | None new | — | **Passed** | Local: screenshots at 390 and 1440 px of home, shop, product, cart, checkout, account, about, contact, 404 and search | Phone cart table could be simplified later (cosmetic) |
| 42 | Homepage testimonials | Home | New section (added after the audit) with fictional sample content | "What our customers say" section, edited in **Appearance → Homepage testimonials**. It shows a visible "Sample testimonials — design preview" label, adds no review structured data, and creates no WooCommerce reviews | **Blocked** (content) | Local: `t_quotes.mjs`, `testimonials-1440.png`, `testimonials-768.png`, `testimonials-390.png`. On 21 Nov the owner turned the sample label off and added photos; the names and reviews are still the fictional samples, and the owner chose to keep it this way | Owner: replace with genuine, approved testimonials before launch |
| 41 | Email tracking | MailPoet | Open/click tracking set to "full" | None | **Not verified** (needs your decision) | WP-CLI: level "full" | Decide: lower to "basic" or mention it in the privacy policy |

## 2. What changed in this audit

**SEO** (`inc/seo.php`):
- Canonicals and `noindex` on filtered and sale views.
- A unique title for every view.
- JSON-LD on the homepage.
- og/twitter image alt text and a 1200×630 share image.
- The core sitemap cleaned up, and author archives now return 404.
- The web manifest is linked.

**Privacy:**
- The privacy setting now points to the published policy, which has a new Cookies section.
- A new consent banner (`inc/consent.php`) gates WooCommerce order attribution.
- Fonts are self-hosted, so the site makes no requests to Google.

**Product page:**
- A sticky buy bar on phones.
- Each variation shows its own photo.
- Unused WooCommerce gallery and comment-reply scripts are no longer loaded.

**Fixes:**
- The 404 heading no longer overflows at 320 px.
- Cart thumbnail links have alt text.
- Footer links are 44 px tall on phones.

## 3. Launch blockers

1. **No working payment method.** Onboard WooPayments or PayPal in test mode, run a test order, then switch to live.
2. **No Terms, Shipping or Returns pages.** EU/Portuguese distance-selling rules require them. A terms checkbox at checkout depends on the Terms page.
3. **No business identity details.** The site has no legal name, address or contact details.
4. **Shipping and tax.** Shipping covers Portugal only (free), while the store sells to every country, and taxes are off. Confirm the intended setup.
5. **Sample testimonials.** The homepage shows fictional sample testimonials, clearly labelled. Replace them with genuine, approved ones (Appearance → Homepage testimonials) or remove them.
6. **Production-only checks.** HTTPS, canonical URLs on the real domain, email delivery (SMTP), Search Console and real-device tests.

## 4. Information needed from you

**Facts only you can supply:**
- Legal business name, address, contact email and phone, and company/VAT numbers if any.
- Terms of sale, shipping policy and returns/refund policy (or approval to draft them from your stated rules).
- Shipping countries, rates and delivery times; VAT approach.
- Product weights and dimensions.

**Decisions:**
- **Email tracking:** keep MailPoet's "full" open/click tracking?
- **PayPal Payments:** onboard it or deactivate it?
- **Analytics:** which tool and ID, if any?
- **Final brand:** confirm "Rovakivo" is final.

## 5. Readiness assessment

**Not ready to take real orders.** The storefront is in good shape: layouts, states, accessibility, SEO, consent and motion all pass, and no defects are left in the custom code. What remains are business and payment items only the owner can provide. Once rows 22, 29, 30 and 31 are resolved and checked on the live domain, the site can launch.
