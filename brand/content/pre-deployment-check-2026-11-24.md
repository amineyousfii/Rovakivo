# Rovakivo pre-deployment check

**Date:** 24 November 2026
**Site:** http://7bar.local, a Local copy. WordPress 7.1.5, WooCommerce 11.3.1, PHP 8.2.27, MySQL 8.0.35.
**Baseline:** the launch audit of 20 November (`launch-audit-2026-11-20.md`). This check covers what changed since then and what the move to a host needs.

**How it was checked:**
- Read-only queries of every setting, plugin, product, page, menu and media item, using WP-CLI.
- A crawl of all 54 public addresses and the 258 images they use (`audit-tools\crawl-predeploy*.json`).
- Order-email previews rendered on the server, with nothing sent.
- The PHP error log.

**Verdict:** the site is ready to move to a host. It is **not ready to take orders** until the steps in part 4 are done. Those steps need a public HTTPS address, a domain and a working mailbox.

---

## 1. Fixed today

| What | Problem | Fix | Checked |
|---|---|---|---|
| Search titles and descriptions | When Yoast was installed, the storefront's own SEO module switched itself off. Yoast had no descriptions, so **no page had a meta description**. Titles were generic, e.g. "Home - Rovakivo" and "Casual Archives - Rovakivo". | Copied into Yoast: 23 page and product descriptions, the homepage title and description, the 2 category titles and descriptions, and the custom titles for Shop, Cart and My account. Yoast's title separator is now "\|", to match. The category title template no longer adds "Archives". | Crawl: every indexable page has a title ending "\| Rovakivo" and a description of 98–142 characters. Shop page 2 now shows its page number. |
| Share image (Facebook, WhatsApp, LinkedIn) | Most pages had no `og:image`. The homepage used a WebP photo. | The storefront's 1200×630 share image is now in the Media Library (#690 `social-share-default`). Yoast uses it as the default and for the homepage. Products keep their own photo. | Crawl: every page has an `og:image`. |
| Yoast organisation logo | Empty | Set to `site-logo-dark` (#645), which also got alt text "Rovakivo". | Yoast settings |
| Sitemap | Listed Cart, Checkout, My account, the Sale page and 2 MailPoet system pages. The MailPoet pages could also be indexed. | Those pages are now excluded or set to `noindex`. | `sitemap_index.xml` holds 5 pages, 18 products and 2 categories, all returning HTTP 200. |
| Product web addresses | 12 products still had demo addresses, e.g. `/product/aturna-condimentum-mattis-pellentesque-nibh-tortor/` | Readable addresses, e.g. `/product/olive-everyday-backpack/`. Old addresses redirect permanently (301). | Old address returns 301 to the new one, which returns 200. The map is in `backups\predeploy-2026-11-24\product-slug-map.json`. |
| Timezone | Fixed at UTC+0. Lisbon has summer time, so order times would be an hour off half the year. | Set to Europe/Lisbon. | Settings → General |
| Footer links to the policy pages | Terms, Shipping and Returns weren't linked anywhere. | Added to the footer menu (Help: Shipping and delivery, Returns and refunds; Rovakivo: Terms and conditions). The storefront now hides menu links to unpublished pages, so each link appears when you publish its page. | Footer shows only published pages for now. |
| Leftovers (you approved) | 7 lorem-ipsum demo reviews from 2020 (unapproved); trash; data left by the removed Image Optimizer, including a stored client secret; 2 unused themes; the unconfigured Cookiez plugin, with its stored key and 4 empty tables | All deleted. Image Optimizer: 16 settings, 34 image records, 162 background-task records (55 failed plus 107 completed history) and 1 scheduled task. Trash: 3 products (test product, duplicate rucksack, old demo item), 6 pages, 1 post, 2 old Customizer drafts, 3 auto-drafts. Twenty Twenty-Five is kept as a fallback theme. | Full database backup taken first |

**Backups:**
- `backups\7bar-db-before-predeploy-cleanup-2026-11-24.sql`, the full database before the deletions.
- `backups\predeploy-2026-11-24\`, which holds:
  - the Yoast, timezone and menu values from before the fix
  - copies of the two deleted themes
  - a copy of the Cookiez plugin

## 2. Checked and fine

- **Updates:** WordPress, every plugin and the Blocksy theme are on their latest versions. WooCommerce's database is current. The storefront's 2 WooCommerce template overrides match WooCommerce 11.3.
- **Pages:** every public page returns 200. Unknown addresses return a proper 404. No PHP notices. No broken images (258 checked). No links to other sites.
- **Index control:** search results, cart, account, 404 and author pages are `noindex`. `/author/amine/` returns 404, so your username isn't published. Sale views point their canonical to the shop or category page.
- **Content:** no lorem ipsum left on any public page or product. Every product has a price, SKU, category, photo with alt text, description and short description.
- **Data:** no test orders or customers. No REST API keys or webhooks.
- **Error log:** no new PHP errors since this afternoon's memory fix (OPcache).
- **Privacy:**
  - **Cookies:** the Rovakivo banner still controls order-attribution cookies, and Cookiez is gone, so there's only one banner.
  - **Third-party requests:** the site loads nothing from other servers except the PayPal script; see part 4.

## 3. What only you can supply or decide

These don't need hosting. They can be done before or after the move.

1. **Policy pages:** fill in the yellow placeholders, then publish the three drafts.
   - **Terms:** your NIF, the VAT sentence, the consumer arbitration centre, and (optionally) the payment methods.
   - **Shipping:** the courier, and whether you send tracking links.
2. **VAT:** taxes are switched off in WooCommerce. Ask your accountant whether you're VAT-registered. If you are, WooCommerce needs Portuguese VAT set up before launch.
3. **Sale prices:** 4 products show a crossed-out price (for example €60 → €55). EU price rules (the Omnibus directive) say the old price must be the lowest price actually charged in the 30 days before the discount. The shop hasn't sold at the old prices yet, so either launch at normal prices and start the sale later, or remove the old prices.
4. **Testimonials:** the homepage still shows the sample testimonials. Swap in real ones when you have them.
5. **Product photos:** some show other brands' logos (PROVOGUE, deuter, WOODS, Mammut). Make sure you're allowed to use them.
6. **Product details (recommended):** none of the 18 products has a weight or dimensions, and none tracks stock. All show "In stock" with no quantity limit.
7. **Order email look:** the emails use WooCommerce's default purple (#8526ff) for links and buttons. Pick your brand colour in WooCommerce → Settings → Emails → Email design. I left this alone because it's a WooCommerce email setting.
8. **Optional privacy choices:**
   - WooCommerce usage tracking is on (WooCommerce → Settings → Advanced → WooCommerce.com).
   - Yoast usage tracking is on (Yoast → Settings → Site features).
   - MailPoet open and click tracking: decide the level.
   - Personal-data retention is empty, which means orders are kept forever (WooCommerce → Settings → Accounts & Privacy).
9. **Legal review:** have a lawyer or accountant check the three policy pages, and consider Portuguese versions.

## 4. Only possible after hosting and a domain

### A. Moving day

1. **Right before the move:**
   - Deactivate and delete **Novamira** (the AI-access plugin).
   - Revoke the two application passwords **"claude 2"** and **"claude4"** (Users → Profile → Application Passwords). On a public site they give admin access to anyone who has them.
2. **Choose a host that meets these requirements:**
   - PHP 8.2 or 8.3
   - MySQL 8
   - OPcache of at least 256 MB (the plugins need about 250 MB, and a smaller cache caused today's editor errors)
   - a PHP memory limit of 256 MB
   - free SSL
   - daily backups and a staging copy
   - servers in Europe
3. **Move the site** with Local's Connect (WP Engine/Flywheel) or the host's migration tool. It must replace `http://7bar.local` with `https://your-domain` throughout the database. About 50 content rows, settings and the email logo need it.
4. **Check `wp-config.php` on the host:**
   - `WP_ENVIRONMENT_TYPE` must be **`production`**. Local's copy says `local`, and while it does, Yoast won't build its SEO data and some plugins act as if it's a test site.
   - Also `WP_DEBUG` false, `DISALLOW_FILE_EDIT` true, and fresh security keys.
5. **After the move:**
   - Settings → Permalinks → **Save** once, so the server's address rules are rebuilt.
   - Force HTTPS.
   - Check that Site Health no longer warns about a "secure connection".
6. **Turn on WooCommerce → Settings → Site visibility → "Coming soon"** while you finish setup.
7. **Set up a real server cron:** `DISABLE_WP_CRON` plus a server task every 5 minutes, for WooCommerce's background jobs and emails.
8. **If the host has page caching:** exclude Cart, Checkout, My account and the WooCommerce cart and session cookies. Then test the cookie banner.

### B. Email (before payments)

1. Create the mailbox **geral@rovakivo.com** on your domain.
2. Set up a sending service: your host's SMTP or a transactional email service. Add the SPF, DKIM and DMARC records it gives you to the domain's DNS.
3. Change the "From" address to geral@rovakivo.com in three places:
   - WooCommerce → Settings → Emails (it's now a Gmail address)
   - WPForms → Contact form → Notifications
   - MailPoet → Settings → Sender

   A Gmail "From" address sent by a web server fails DMARC and lands in spam.
4. Test the contact form, the newsletter sign-up and an order email.

### C. Payments

1. Publish Terms, Shipping and Returns first, because Stripe and PayPal review the site. In WooCommerce → Settings → Advanced, choose **Terms and conditions**, which adds the "I agree to the terms" checkbox at checkout.
2. **WooPayments:** connect it (this needs Jetpack connected), complete the business verification, and turn on **test mode**.
3. **PayPal Payments:** either onboard it in sandbox mode or deactivate it. Until it's set up, it loads its script on every shop page and logs an error.
4. Place a full test order: payment, both emails, the order-received page, and a refund.
5. Switch payments to live.

### D. Connect the services

| Service | What to do |
|---|---|
| Jetpack | Connect (WooPayments and WooCommerce Tax depend on it) |
| Site Kit | Connect Google Search Console. Add Analytics only if it waits for consent ("Allow" on the banner) |
| Google Search Console | Verify the domain and submit `https://your-domain/sitemap_index.xml` |
| Yoast | Run Yoast → Tools → **Optimize SEO data** once on the live site |
| Akismet | Add an API key (spam filtering for the contact form and reviews) |
| Wordfence | Run the firewall's "Optimize" step for the host, turn on two-factor login for your account, and set the alert email |
| MailPoet | Choose the sending method (MailPoet Sending Service or your SMTP) |
| Livro de Reclamações Eletrónico | Register the business at livroreclamacoes.pt. Portuguese online shops must show its link clearly on the site; I can add it to the footer. |

### E. Final checks, then launch

1. Test on real phones (iPhone and Android) and run PageSpeed Insights on the live address.
2. Test the share preview with Facebook's Sharing Debugger.
3. Run Google's Rich Results test on a product page.
4. Update the Privacy Policy so it names the services you connected: the payment provider, email service, Jetpack/Wordfence, and Google if you add Analytics.
5. Turn **Coming soon** off.

## 5. Still open

- **Imagify:** backpack-1, -12 and -13 are now optimised (one was already as small as Imagify could make it). backpack-11 was still processing at my last check. If it still shows as unoptimised, run Media → Bulk Optimization once.
- **About page:** it still carries an empty Elementor flag. It's harmless, but saving it from the Elementor editor would switch the page to an Elementor layout.
