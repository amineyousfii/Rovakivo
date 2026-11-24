# Media and plugin maintenance — 2026-11-24

Rovakivo (7bar.local). Backups: `backups\maintenance-2026-11-24\` (before any change) and `backups\maintenance-2026-11-24\before-rename-2\` (before the final renaming).

## 1. Renamed images (files, WordPress data, Media Library titles, references)

Every file of each image was renamed with it: all generated sizes, Imagify's WebP copies (`*.jpg.webp`), the pre-conversion originals kept by the old optimizer, and Imagify's backups. Attachment IDs did not change, so featured images, galleries, variation images and page placements are untouched. Old addresses (original names and the interim `product-…` names used earlier today) redirect permanently (301) to the final names. Every File URL below was requested after renaming and returned 200.

"Old filename" is the name before today's maintenance. Full per-file map (676 files, original → interim → final): `backups\maintenance-2026-11-24\media-rename-map-final.csv`.

| Attachment ID | Old filename | New filename | New Media Library title | Verified File URL |
|---|---|---|---|---|
| 171 | cynthia-del-rio-tJWUHtIDzWs-unsplash.webp | backpack-29.webp | backpack-29 | http://7bar.local/wp-content/uploads/2020/03/backpack-29.webp (200) |
| 169 | cynthia-del-rio-HM599jibAQs-unsplash.webp | backpack-2.webp | backpack-2 | http://7bar.local/wp-content/uploads/2020/03/backpack-2.webp (200) |
| 167 | lina-verovaya-GQQ6BRJxysU-unsplash.webp | backpack-3.webp | backpack-3 | http://7bar.local/wp-content/uploads/2020/03/backpack-3.webp (200) |
| 176 | lina-verovaya-Tu9MHTJZhyk-unsplash.webp | backpack-4.webp | backpack-4 | http://7bar.local/wp-content/uploads/2020/03/backpack-4.webp (200) |
| 177 | lina-verovaya-TeBKnxH_9XA-unsplash.webp | backpack-5.webp | backpack-5 | http://7bar.local/wp-content/uploads/2020/03/backpack-5.webp (200) |
| 178 | andrew-neel-HqtYwlY9dxs-unsplash.webp | backpack-6.webp | backpack-6 | http://7bar.local/wp-content/uploads/2020/03/backpack-6.webp (200) |
| 165 | daniel-j-schwarz-Ccw6ZV2NT0M-unsplash.jpg | backpack-7.jpg | backpack-7 | http://7bar.local/wp-content/uploads/2020/03/backpack-7.jpg (200) |
| 179 | daniel-j-schwarz-JtPAZUXZzvk-unsplash.webp | backpack-8.webp | backpack-8 | http://7bar.local/wp-content/uploads/2020/03/backpack-8.webp (200) |
| 180 | daniel-j-schwarz-TCgsurVKVi8-unsplash.webp | backpack-9.webp | backpack-9 | http://7bar.local/wp-content/uploads/2020/03/backpack-9.webp (200) |
| 181 | kimo-lNhZPVxlO-w-unsplash.webp | backpack-10.webp | backpack-10 | http://7bar.local/wp-content/uploads/2020/03/backpack-10.webp (200) |
| 163 | joshua-fuller-2xAaR_xDCPY-unsplash.jpg | backpack-11.jpg | backpack-11 | http://7bar.local/wp-content/uploads/2020/03/backpack-11.jpg (200) |
| 161 | linus-rogge-SO-4BNfONaU-unsplash.jpg | backpack-12.jpg | backpack-12 | http://7bar.local/wp-content/uploads/2020/03/backpack-12.jpg (200) |
| 158 | haupes-co-LhTPg9gwxzs-unsplash.jpg | backpack-13.jpg | backpack-13 | http://7bar.local/wp-content/uploads/2020/03/backpack-13.jpg (200) |
| 156 | haupes-co-6hpJZcqWt4I-unsplash.jpg | backpack-14.jpg | backpack-14 | http://7bar.local/wp-content/uploads/2020/03/backpack-14.jpg (200) |
| 69 | daniel-lincoln-dp75uo9Qflo-unsplash.jpg | backpack-15.jpg | backpack-15 | http://7bar.local/wp-content/uploads/2020/03/backpack-15.jpg (200) |
| 223 | alexandru-acea-XbY0xayztVU-unsplash.webp | backpack-16.webp | backpack-16 | http://7bar.local/wp-content/uploads/2020/03/backpack-16.webp (200) |
| 221 | sj-baren-eX0N1Ex-qFo-unsplash.webp | backpack-17.webp | backpack-17 | http://7bar.local/wp-content/uploads/2020/03/backpack-17.webp (200) |
| 219 | luis-quintero-8TSqJoI-NVs-unsplash.webp | backpack-18.webp | backpack-18 | http://7bar.local/wp-content/uploads/2020/03/backpack-18.webp (200) |
| 67 | andrew-neel-UcgSpU8bOYY-unsplash.jpg | backpack-19.jpg | backpack-19 | http://7bar.local/wp-content/uploads/2020/03/backpack-19.jpg (200) |
| 65 | jakob-owens-BmH09wAkJa8-unsplash.jpg | backpack-20.jpg | backpack-20 | http://7bar.local/wp-content/uploads/2020/03/backpack-20.jpg (200) |
| 58 | micah-tindell-ysu9athq_BU-unsplash.jpg | backpack-21.jpg | backpack-21 | http://7bar.local/wp-content/uploads/2020/03/backpack-21.jpg (200) |
| 648 | backpack-2-scaled.jpg | backpack-22-scaled.jpg | backpack-22 | http://7bar.local/wp-content/uploads/2026/11/backpack-22-scaled.jpg (200) |
| 649 | backpack-3-scaled.jpg | backpack-23-scaled.jpg | backpack-23 | http://7bar.local/wp-content/uploads/2026/11/backpack-23-scaled.jpg (200) |
| 651 | backpack-5-scaled.jpg | backpack-24-scaled.jpg | backpack-24 | http://7bar.local/wp-content/uploads/2026/11/backpack-24-scaled.jpg (200) |
| 650 | backpack-4-scaled.jpg | backpack-25-scaled.jpg | backpack-25 | http://7bar.local/wp-content/uploads/2026/11/backpack-25-scaled.jpg (200) |
| 654 | backpack-10-scaled.jpg | backpack-26-scaled.jpg | backpack-26 | http://7bar.local/wp-content/uploads/2026/11/backpack-26-scaled.jpg (200) |
| 652 | backpack-6-scaled.jpg | backpack-27-scaled.jpg | backpack-27 | http://7bar.local/wp-content/uploads/2026/11/backpack-27-scaled.jpg (200) |
| 653 | backpack-8.jpg | backpack-28.jpg | backpack-28 | http://7bar.local/wp-content/uploads/2026/11/backpack-28.jpg (200) |
| 280 | byork-J7Dl7cA5lmg-unsplash.webp | backpack-30.webp | backpack-30 | http://7bar.local/wp-content/uploads/2020/03/backpack-30.webp (200) |
| 281 | byork-azsIrEsOvJY-unsplash.webp | backpack-31.webp | backpack-31 | http://7bar.local/wp-content/uploads/2020/03/backpack-31.webp (200) |
| 282 | byork-Vc4P1KHp-FQ-unsplash.webp | backpack-32.webp | backpack-32 | http://7bar.local/wp-content/uploads/2020/03/backpack-32.webp (200) |
| 603 | hero.webp | home-section-hero.webp | home-section-hero | http://7bar.local/wp-content/uploads/2026/11/home-section-hero.webp (200) |
| 667 | About-us-scaled.jpg | about-section-hero-scaled.jpg | about-section-hero | http://7bar.local/wp-content/uploads/2026/11/about-section-hero-scaled.jpg (200) |
| 74 | andrew-neel-UcgSpU8bOYY-unsplash-1.jpg | casual-section-hero.jpg | casual-section-hero | http://7bar.local/wp-content/uploads/2020/03/casual-section-hero.jpg (200) |
| 151 | florian-weichelt-oBUkiqaSEME-unsplash-copy.jpg | travel-section-hero.jpg | travel-section-hero | http://7bar.local/wp-content/uploads/2020/03/travel-section-hero.jpg (200) |
| 668 | Casual-section-scaled.jpg | casual-section-tile-scaled.jpg | casual-section-tile | http://7bar.local/wp-content/uploads/2026/11/casual-section-tile-scaled.jpg (200) |
| 669 | Travel-section-scaled.jpg | travel-section-tile-scaled.jpg | travel-section-tile | http://7bar.local/wp-content/uploads/2026/11/travel-section-tile-scaled.jpg (200) |
| 646 | Favicon.png | favicon-1254.png | favicon-1254 | http://7bar.local/wp-content/uploads/2026/11/favicon-1254.png (200) |

### Title updated, file name kept

| Attachment ID | Filename (unchanged) | New Media Library title | Verified File URL | Why the file name stays |
|---|---|---|---|---|
| 600 | favicon-512.webp | favicon-512 | http://7bar.local/wp-content/uploads/2026/11/favicon-512.webp (200) | already names its role (site icon, 512 px) |
| 601 | rovakivo-logo-1215w.webp | rovakivo-logo-1215w | http://7bar.local/wp-content/uploads/2026/11/rovakivo-logo-1215w.webp (200) | WooCommerce email settings point to this exact file (commerce settings are off-limits) |
| 645 | site-logo-dark.png (was Logo.png) | site-logo-dark | http://7bar.local/wp-content/uploads/2026/11/site-logo-dark.png (200) | already renamed earlier today from Logo.png and names its role |
| 647 | backpack-1-scaled.jpg | backpack-1 | http://7bar.local/wp-content/uploads/2026/11/backpack-1-scaled.jpg (200) | already follows backpack-{number} |

### Not renamed

| Attachment ID | File | Reason |
|---|---|---|
| 7, 8 | woocommerce-placeholder.webp / .png | WooCommerce's placeholder (commerce, left unchanged) |
| 101 | shapelined-K9QHL52rE2k-unsplash.jpg | Only used in the unused Elementor template "Modern Shop CTA"; placement uncertain |
| 228 | ashley-knedler-Pf5Pj7A5ddA-unsplash.webp | Set as the About page's title background in Blocksy's page options, which the storefront does not display; placement uncertain |
| 528 | 4139.webp | Not used anywhere (uploaded June 2025); kept, purpose uncertain |

## 2. Removed images (confirmed unused)

10 media items, 46 files, about 0.7 MB: leftovers of the original Elementor demo design, referenced only by old revisions of Home and About (IDs 29, 33, 46, 76, 77, 105, 107, 108, 111, 236). Details for restoring: `backups\maintenance-2026-11-24\deleted-media.json` and the uploads copy.

Correction: three more (#239, #242, #244) were removed at first, but they are the homepage testimonial photos — the testimonials setting refers to them by ID, which the first audit did not check. They were restored the same day with their original IDs, data and files; a follow-up scan found no ID references to any of the 10 images that remain removed.

Kept although not visibly used: the trashed products' photos (backpack-1, backpack-30–32), WooCommerce's spare placeholder (#8), and the original JPG/PNG files that the previously installed Elementor Image Optimizer kept when it converted images to WebP (they are the only full-quality originals of those photos).

## 3. Imagify

- Account: Starter (free) plan, 20 MB a month; 14.6 MB used today; resets 2026-12-24. Connection kept; settings unchanged except WebP delivery.
- Settings: smart compression, WebP generation, automatic optimization of new uploads, originals backed up (restorable from Imagify), larger-than-2560 px resize. WebP delivery changed from "rewrite" (needs server rules this server does not have — no WebP was reaching the browser) to `<picture>` tags, which work on any host. A small storefront layer keeps classes on the image, makes the wrapper layout-neutral and lets the product gallery swap the WebP with the photo.
- Optimized (JPEG files, all sizes): 11 images, 14,387 KB → 8,210 KB (−43%), plus WebP copies. Examples: backpack-22 806 → 293 KB, backpack-23 1,393 → 700 KB, about-section-hero 1,181 → 544 KB.
- Already as small as Imagify can make them (WebP copies still created where smaller): backpack-7, -15, -14, -19, -20, -28, site-logo-dark.png (transparency and text unchanged).
- Not processed this month (quota): backpack-11, -12, -13 — the original products' main photos, which the storefront displays from its own pre-optimised WebP files. Run Imagify's bulk optimization after 2026-12-24.
- Not processed by design: images that are already WebP (no repeated lossy compression), unused/uncertain images, WooCommerce's placeholder.
- Verified delivery: every uploads image loaded on Home, About, Shop, both categories, Sale and all 18 product pages arrives as `image/webp`; 1,324 image addresses checked, 0 broken.
- Known Imagify limitation: for portrait photos the full-size WebP is generated 2560 px wide (larger than WordPress's 1707 px scaled JPEG); it is only chosen on very large, very sharp screens.

## 4. Plugins

No plugin deleted (none confirmed unnecessary), none deactivated. Unchanged and protected: WooCommerce, WooPayments, WooCommerce PayPal Payments, WooCommerce Tax, and Jetpack (WooCommerce Tax may rely on its connection — uncertain, so untouched). No commerce setting changed.

| Plugin | State | Note |
|---|---|---|
| Imagify | configured | see section 3 |
| Yoast SEO | active | Replaces the storefront's own SEO module (which switches itself off). The homepage has no meta description in Yoast yet, company logo not set, indexing not finished. |
| Wordfence | active | Firewall (basic mode), login security, scheduled scans on |
| Akismet | active, no API key | Needs your Akismet key to filter spam |
| Site Kit by Google | active, not connected | Outputs nothing until connected to your Google account (then needs consent gating) |
| Cookiez | active, not configured | No settings and no visible banner; the storefront's own consent banner is the one shown |
| Blocksy Companion | active | Its cookie-notice extension outputs an inactive template only (no second banner) |
| Elementor, MailPoet, WPForms Lite, Novamira, Rovakivo Storefront | active, in use | Novamira gives AI tools full site access — consider turning it off before going live |

## 5. Verification

Media Library titles and File URLs (42 items), all size files on disk, 301 redirects from old names, Home/About/Contact/Shop/categories/Sale/all product pages on desktop and phone: no broken images, no console errors, no horizontal overflow, tiles/heroes/cards fill their frames, product gallery switches photos with WebP. Elementor CSS cache and the object cache were refreshed.
