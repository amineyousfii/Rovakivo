# Rovakivo: rebrand and content handoff

Working brand: **Rovakivo** · Tagline: **For every day. For wherever’s next.**
Site: `http://7bar.local` (local). Domain, URLs, products, prices, stock, variations, reviews and order history are unchanged.
Database backup taken before any change: `C:\Users\HP\Desktop\Claude Code\Rovakivo\backups\7bar-db-before-rebrand-2026-11-20.sql`.

---

## 1. Logo and brand files

Folder: `C:\Users\HP\Desktop\Claude Code\Rovakivo\brand\`

| File | Use |
|---|---|
| `svg/rovakivo-logo.svg` | Main horizontal logo (black monogram + wordmark) for light backgrounds |
| `svg/rovakivo-logo-white.svg` | Horizontal logo for dark backgrounds (used in the footer) |
| `svg/rovakivo-monogram.svg` / `-white.svg` | Standalone R monogram |
| `svg/rovakivo-favicon.svg` | Monogram tuned for small sizes (heavier R, larger dot) |
| `png/rovakivo-logo-1215w.png`, `-486w.png` | Transparent PNG, black version |
| `png/rovakivo-logo-white-1215w.png`, `-486w.png` | Transparent PNG, white version |
| `png/rovakivo-monogram-512.png`, `-white-512.png` | Transparent PNG monograms |
| `favicon/favicon.ico` (16/32/48), `favicon.svg`, `favicon-16/32/48/192/512.png`, `apple-touch-icon.png` | Browser, phone and app icons |

Design: geometric uppercase wordmark drawn as clean strokes in the spirit of Manrope (no font file is needed to display it), a black circle R monogram, and one small yellow (#FFD100) dot at the end of the R’s leg as a subtle “destination” detail. The SVGs are drawn as shapes, so they render the same everywhere.

Applied on the site: header and mobile header, menu drawer, footer (white version), favicon set, WordPress site icon, theme logo (custom logo), WooCommerce email header image.

## 2. Branding settings changed

- Site title: **Rovakivo**; tagline: **For every day. For wherever’s next.**
- WooCommerce emails: sender name “Rovakivo”, header image = Rovakivo logo, base colour #161616 (was WooCommerce purple), footer “{site_title} | For every day. For wherever’s next.”
- MailPoet newsletter: sender name “Rovakivo” (its confirmation email already uses the site title, so it now says “Confirm your subscription to Rovakivo”).
- Contact form (WPForms): notification sender “Rovakivo” (was “Modern Shop”), subject “New message from the Rovakivo contact form”, confirmation “Thank you. Your message has been sent, and we’ll reply by email.”, field “Comment or Message” renamed “Message”, button “Send message”.
- Page titles, meta descriptions and social-sharing tags (Open Graph/Twitter) on the homepage, shop, collections, every product and the main pages. There is no SEO plugin, so the storefront plugin outputs these. It switches them off automatically if Yoast, Rank Math, AIOSEO or SEOPress is activated later.

## 3. Website copy rewritten

**Announcement bar:** Rovakivo · For every day. For wherever’s next. · Explore the collection
**Navigation:** Shop all · Casual · Travel · Sale · About (was “Our story”: no story is confirmed yet)

**Homepage**
- Hero: “BAGS FOR EVERYDAY AND BEYOND” / “Carry your day. Explore what’s next.” / “Discover bags and backpacks for daily routines, travel plans, and time outdoors.” / Shop backpacks · Explore the collection
- Help strip: Casual & travel backpacks (Browse the full collection) · On sale now (See current offers) · Questions? (Send us a message) · Your account (Orders and details)
- Featured: “Meet your next backpack.” / “Four picks for the daily routine and the weekend away.”
- Collections: “A bag for every kind of day.” / “The daily routine. The trip ahead. Pick the bag that fits.”
  - Casual: “Easy-going backpacks for commutes, errands and the daily routine.”
  - Travel: “Backpacks for weekends away, city breaks and longer trips.”
- Sale banner: “The sale edit. Good bags. Lower prices.” plus a live count, “4 backpacks are reduced right now.” The banner hides itself when nothing is on sale.
- More products: “From the collection.” / “More backpacks for everyday routes and trips away.” (was “Popular for a reason”, which implied sales data that isn’t available)
- **The sample testimonial (Jason Wong) is removed.** The existing product reviews are placeholder text, so there is no genuine customer feedback to show instead.
- Newsletter: “Good things. In your inbox.” / “New arrivals, collection updates and occasional offers from Rovakivo. Unsubscribe at any time.” / “We’ll ask you to confirm your address first.”
- Footer: Rovakivo · For every day. For wherever’s next. · “Carry your day.” · © Rovakivo · Bags and backpacks for every day, travel and time outdoors.

**Shop and collections:** “All bags & backpacks.” / “Bags and backpacks for daily routines, travel plans and time outdoors.” Collection descriptions are saved in WooCommerce (Products → Categories), so they also appear in search engines.
- Casual: “Backpacks for the daily routine: commutes, errands, school runs and weekends close to home. Easy shapes and colours that work with what you already wear.”
- Travel: “Backpacks for trips away, from city breaks and weekends to longer journeys. Classic shapes and colours for days on the move.”
- Sale: “On sale now” / “Backpacks that are reduced right now. Prices shown are the current sale prices.”

**About** (page renamed “About”): states what Rovakivo sells and how the collection is organised, with a live product count. No founder, history, makers or materials are claimed.
**Contact** (page renamed “Contact”): “Questions about a bag, an order or your account? Send us a message using the form and we’ll reply by email.” No contact details are shown, because none are confirmed.
**Privacy Policy:** the lorem ipsum is replaced with a short factual description of what the site collects (orders, newsletter, contact form, cart cookies, order source). It is not a complete legal policy; see section 6.
**Shop page title:** “Shop” (was “Take Your Adventure To The Next Level”).

**Demo pages set to draft (not deleted):** Sample Page, Left Sidebar, Right Sidebar.

## 4. Pages and products updated

Pages: Homepage, Shop (+ page 2, sale view, search), Casual, Travel, every product page, Cart, Checkout, My account, About, Contact, Privacy Policy, 404, header, footer, menu drawer, cart drawer.

Products (all 13): see the mapping and full copy below. Kept for every product: ID, SKU, URL slug (so no redirects are needed), prices and sale prices, stock, variations and attributes, reviews, grouped and related links. Not product data, but worth knowing: the Minimal Zip Backpack showed “Out of stock” although all three of its variations are in stock. Re-running WooCommerce’s own stock sync corrected its status to “in stock”.

## 5. Questions for you (essential facts I could not verify)

1. **Final brand name:** is “Rovakivo” final? If it changes, the logo and about 30 text strings need a pass.
2. **Woven Flap Backpack (ID 279):** resolved on 2026-11-20. At your request it was moved to the trash (Products → Trash, restorable). Its photos remain in the media library, and its old URL now returns “page not found”.
3. **Existing reviews:** resolved on 2026-11-20. At your request the seven lorem-ipsum reviews by "admin" were set to "Pending" (not deleted), so they and their star ratings no longer appear. They can be restored from Comments → Pending.
4. **Three Backpack Selection (ID 157):** its photo shows a black-and-tan backpack that is not one of the three backpacks it contains. Do you have a photo of the three together, or should the listing use one of their photos?
5. **Contact details:** a support email address (and phone, address or opening hours, if you want them shown) for the Contact page and the privacy policy.
6. **Policies:** shipping zones and costs, delivery times, the returns and refunds policy, terms and conditions, and a reviewed privacy policy (data retention, legal basis, who to contact). The current shipping setting offers free shipping to Portugal only, so please confirm whether that is intended.
7. **Product facts** (for richer descriptions and the “Additional information” tab), per product: materials, dimensions, weight, capacity (litres), laptop sleeve size if any, water resistance, care instructions and warranty. None of these are in the store, so none are claimed.
8. **Brand story** for the About page: anything true you want to say about who runs Rovakivo and why.
9. **Social profiles**, if any, for the footer and sharing settings.

## 6. Notes

- The old Elementor content of Home, About and Contact is still in the database, untouched, but not shown.
- Category names stay “Casual” and “Travel” with their existing URLs. If you prefer “Everyday” instead of “Casual”, only the name needs changing (Products → Categories); the URL can stay `/casual/`.
- Prices in sale text are never written by hand: the sale banner reads them from WooCommerce, so the copy stays accurate when prices change.

---

# Product names and copy


## Old-to-new product names

| ID | SKU | Old name | New name | URL (unchanged) |
|---|---|---|---|---|
| 66 | 011 | Aturna condimentum mattis pellentesque nibh tortor | **Olive Everyday Backpack** | /product/aturna-condimentum-mattis-pellentesque-nibh-tortor/ |
| 57 | 013 | Fringilla Phasellus Faucus Scelerisque Eleifend Donec | **Brown Flap Backpack** | /product/fringilla-phasellus-faucus-scelerisque-eleifend-donec/ |
| 64 | 012 | Mollis nunc sed id semper risus in hendrerit | **Charcoal Buckle Backpack** | /product/mollis-nunc-sed-id-semper-risus-in-hendrerit/ |
| 166 | 004 | Scelerisque mauris pellentesque pulvinar | **Forest Green Travel Backpack** | /product/scelerisque-mauris-pellentesque-pulvinar/ |
| 157 | 008 | Ornare arcudui vivamus arcu felis bibendum | **Three Backpack Selection** | /product/ornare-arcudui-vivamus-arcu-felis-bibendum/ |
| 155 | 009 | Libero faucibus nistincidunt elementum integer | **Green Zip Backpack** | /product/libero-faucibus-nistincidunt/ |
| 162 | 006 | Donec ultrices tincidunt arcu non sodales | **Black Everyday Backpack** | /product/donec-ultrices-tincidunt-arcu-non-sodales/ |
| 159 | 007 | Vestibulum sedarcu nonodio euismod | **Black Travel Backpack** | /product/vestibulum-sedarcu-nonodio-euismod/ |
| 164 | 005 | Rhoncus aenean velelit scelerisque mauris | **Red Roll-Top Backpack** | /product/rhoncus-aenean-velelit-scelerisque-mauris/ |
| 168 | 003 | Ornare arcu odio utsem nullapharetra diam | **Charcoal & Tan Travel Backpack** | /product/ornare-arcu-odio-utsem-nullapharetra-diam/ |
| 170 | 002 | Tellus molestie nuncnon blandit elementum | **White Printed Flap Backpack** | /product/tellus-molestie-nuncnon-blandit/ |
| 279 | 001 | Neque laoreet suspendisse interdum consectetur | **Woven Flap Backpack** | /product/neque-laoreet-suspendisse-interdum-consectetur/ | *(moved to trash 2026-11-20)*
| 68 | 010 | Eleifend donec pretium vulputate sapien sagittis | **Minimal Zip Backpack** | /product/eleifend-donec-pretium-vulputate-sapien-sagittis/ |

## Product copy (as published)

### Olive Everyday Backpack

ID 66 · SKU 011 · Casual · simple · price shown: 70.00 € (was 80.00 €)

**Short description:** An olive backpack with dark contrast straps, a buckled top flap and two front pockets. A practical everyday pick for commutes, errands and weekends.

**Full description:**

The Olive Everyday Backpack pairs a muted olive body with dark straps and hardware, giving it a relaxed, outdoorsy look that works just as well in town.

A top flap closes with two buckled straps, and two front pockets sit below it, so the things you reach for most stay close at hand. A small emblem patch on the flap and a top grab handle finish the design.

**Highlights**

- Olive body with dark contrast straps
- Top flap with two buckle straps
- Two front pockets
- Emblem patch on the flap
- Top grab handle

**SEO title:** Olive Everyday Backpack | Rovakivo  
**Meta description:** Olive backpack with dark contrast straps, a buckled top flap and two front pockets. An everyday backpack for commutes, errands and weekends.

**Image alt text:**

- andrew-neel-UcgSpU8bOYY-unsplash.jpg: Olive backpack with dark straps, a buckled top flap and two front pockets, resting against a fallen tree trunk.

### Brown Flap Backpack

ID 57 · SKU 013 · Casual · simple · price shown: 55.00 € (was 60.00 €)

**Short description:** A warm brown backpack with a darker brown top flap and a front pocket. Simple lines and rich tones make it an easy match for everyday outfits.

**Full description:**

The Brown Flap Backpack keeps things simple: a warm brown body, a darker brown flap that folds over the top, and a front pocket below it.

A round button detail on the flap, a top handle and contrasting shoulder straps complete the look. It suits the daily routine, from the commute to a weekend out.

**Highlights**

- Warm brown body with a darker brown top flap
- Front pocket
- Round button detail on the flap
- Top carry handle

**SEO title:** Brown Flap Backpack | Rovakivo  
**Meta description:** Warm brown backpack with a darker brown top flap, a front pocket and a round button detail. Simple, everyday style from Rovakivo.

**Image alt text:**

- micah-tindell-ysu9athq_BU-unsplash.jpg: Brown backpack with a darker brown top flap and a front pocket, standing on grass.

### Charcoal Buckle Backpack

ID 64 · SKU 012 · Casual · simple · price shown: 45.00 € (was 60.00 €)

**Short description:** A structured charcoal backpack with a lighter grey flap, tan buckle tabs and an orange stripe down the front. A sharp, modern bag for the daily routine.

**Full description:**

The Charcoal Buckle Backpack has a squared-off, structured shape in charcoal, with a lighter grey top flap and tan buckle tabs that add a little warmth.

An orange stripe runs down the front pocket as a subtle accent, and a top handle makes it easy to grab on the way out. It is a clean, modern choice for commutes and city days.

**Highlights**

- Structured charcoal body
- Lighter grey top flap with tan buckle tabs
- Front pocket with an orange stripe
- Top carry handle

**SEO title:** Charcoal Buckle Backpack | Rovakivo  
**Meta description:** Structured charcoal backpack with a grey top flap, tan buckle tabs and an orange stripe. A clean, modern bag for the daily routine.

**Image alt text:**

- jakob-owens-BmH09wAkJa8-unsplash.jpg: Charcoal backpack with a grey top flap, tan buckle tabs and an orange stripe, sitting on a rock outdoors.

### Forest Green Travel Backpack

ID 166 · SKU 004 · Travel · simple · price shown: 35.00 € (was 55.00 €)

**Short description:** A tall forest-green backpack with a brown base, long brown straps and a buckled flap. Classic styling for weekends away and longer trips.

**Full description:**

The Forest Green Travel Backpack combines a deep green upper with a brown base and long brown straps that run over the top flap and fasten at the front.

Diamond-shaped details anchor each strap, and a top handle and side straps add to its classic look. Its tall shape and timeless colours make it a natural companion for weekends away and longer trips.

**Highlights**

- Deep green upper with a brown base
- Two long front straps over the top flap
- Diamond-shaped strap details
- Top carry handle and side straps

**SEO title:** Forest Green Travel Backpack | Rovakivo  
**Meta description:** Tall forest-green backpack with a brown base, long brown straps and diamond strap details. Classic styling for weekends away and longer trips.

**Image alt text:**

- lina-verovaya-GQQ6BRJxysU-unsplash.jpg: Forest-green backpack with a brown base and brown front straps, standing on a paved path.
- lina-verovaya-Tu9MHTJZhyk-unsplash.jpg: Forest-green backpack with brown straps, set among plants outdoors.
- lina-verovaya-TeBKnxH_9XA-unsplash.jpg: Close view of the forest-green backpack showing its brown straps, diamond strap details and brown base.
- andrew-neel-HqtYwlY9dxs-unsplash.jpg: Person in a tweed jacket holding a brown shoulder strap with a small hanging tag.

### Three Backpack Selection

ID 157 · SKU 008 · Casual · grouped · price shown: from 35.00 €

**Short description:** Three backpacks in one place: the Charcoal Buckle Backpack, the Forest Green Travel Backpack and the Charcoal &amp; Tan Travel Backpack. Add one, two or all three to your cart.

**Full description:**

This listing brings three backpacks together, so you can compare them and add any combination to your cart in one step.

The Charcoal Buckle Backpack is a structured charcoal bag with a grey flap and an orange stripe. The Forest Green Travel Backpack pairs a deep green upper with a brown base and straps. The Charcoal & Tan Travel Backpack combines a dark body with warm tan straps and trim.

Each backpack’s current price, any sale price and stock are shown below, and each one also has its own product page with more photos.

**Highlights**

- Charcoal Buckle Backpack
- Forest Green Travel Backpack
- Charcoal & Tan Travel Backpack
- Choose quantities for each backpack separately

**SEO title:** Three Backpack Selection | Rovakivo  
**Meta description:** Compare three Rovakivo backpacks and add any combination to your cart: Charcoal Buckle, Forest Green Travel and Charcoal & Tan Travel.

**Image alt text:**

- haupes-co-LhTPg9gwxzs-unsplash.jpg: Black backpack with tan straps and trim, standing on a wooden desk beside a vintage camera.

### Green Zip Backpack

ID 155 · SKU 009 · Casual · simple · price shown: 45.00 €

**Short description:** A green backpack with a zipped front pocket, a buckled top strap and dark contrast trim. A relaxed, versatile pick for school runs, commutes and days out.

**Full description:**

The Green Zip Backpack brings a fresh green shade to the everyday, with dark trim and webbing that keep the look grounded.

A zipped pocket across the front holds small essentials, and a dark strap with a metal buckle fastens over the top. Dark shoulder straps and a top handle complete a relaxed, versatile design.

**Highlights**

- Green body with dark trim
- Zipped front pocket
- Dark strap with a metal buckle over the top
- Top carry handle

**SEO title:** Green Zip Backpack | Rovakivo  
**Meta description:** Green backpack with a zipped front pocket, a buckled top strap and dark trim. A relaxed, versatile everyday bag from Rovakivo.

**Image alt text:**

- haupes-co-6hpJZcqWt4I-unsplash.jpg: Green backpack with a zipped front pocket and a dark buckled strap, standing against a white wooden wall beside tools.

### Black Everyday Backpack

ID 162 · SKU 006 · Casual · simple · price shown: 25.00 €

**Short description:** A sleek black backpack with white lettering printed across the top and a small red accent. Clean and understated for school, work and errands.

**Full description:**

The Black Everyday Backpack keeps a low-key, all-black profile with a few graphic touches: white lettering printed across the upper panel and a small red detail.

A top handle makes it easy to carry in one hand when you are not wearing it. It is a simple, understated choice for school, work and everyday errands.

**Highlights**

- All-black design
- White printed lettering on the upper panel
- Small red accent
- Top carry handle

**SEO title:** Black Everyday Backpack | Rovakivo  
**Meta description:** Sleek black backpack with white printed lettering and a small red accent. An understated everyday bag for school, work and errands.

**Image alt text:**

- joshua-fuller-2xAaR_xDCPY-unsplash.jpg: Black backpack with white printed lettering and a small red detail, held up by its top handle in front of green plants.

### Black Travel Backpack

ID 159 · SKU 007 · Travel · simple · price shown: 25.00 €

**Short description:** A soft black backpack with a relaxed shape, a top handle and a small tan patch on the front. An easy, low-key companion for day trips and weekends away.

**Full description:**

The Black Travel Backpack has a soft, unstructured shape and an all-black finish, with a small tan patch on the front as the only accent.

Black shoulder straps and a top handle keep the look clean and simple, so it fits in on a city break as well as on a day out.

**Highlights**

- All-black body
- Soft, relaxed shape
- Small tan patch on the front
- Top carry handle

**SEO title:** Black Travel Backpack | Rovakivo  
**Meta description:** Soft black backpack with a relaxed shape, a top handle and a small tan patch. A low-key companion for day trips and weekends away.

**Image alt text:**

- linus-rogge-SO-4BNfONaU-unsplash.jpg: Black backpack with a small tan patch on the front, hanging from a white chair.

### Red Roll-Top Backpack

ID 164 · SKU 005 · Casual · simple · price shown: 45.00 €

**Short description:** A bright red roll-top style backpack with a grey centre strap and a round tan clasp. A bold, easy-to-spot bag for everyday use and time outdoors.

**Full description:**

The Red Roll-Top Backpack stands out in a vivid red, with a fold-over top held in place by a grey strap and a round tan clasp at the front.

Its simple, rounded shape keeps the focus on colour, and it looks as at home on a forest walk, as pictured, as it does on the daily commute.

**Highlights**

- Vivid red body
- Fold-over, roll-top style closure
- Grey centre strap with a round tan clasp
- Shown worn on a forest walk

**SEO title:** Red Roll-Top Backpack | Rovakivo  
**Meta description:** Bright red roll-top style backpack with a grey centre strap and a round tan clasp. A bold bag for everyday use and time outdoors.

**Image alt text:**

- daniel-j-schwarz-Ccw6ZV2NT0M-unsplash.jpg: Red roll-top backpack with a grey strap and a round tan clasp on a dark wooden table.
- daniel-j-schwarz-JtPAZUXZzvk-unsplash.jpg: Red roll-top backpack lying on dark wooden boards.
- daniel-j-schwarz-TCgsurVKVi8-unsplash.jpg: Red roll-top backpack photographed against a grey wall.
- kimo-lNhZPVxlO-w-unsplash.jpg: Person wearing the red backpack on a forest path in front of a wooden cabin.

### Charcoal & Tan Travel Backpack

ID 168 · SKU 003 · Travel · simple · price shown: 35.00 €

**Short description:** A dark charcoal backpack with tan straps and trim and a buckled front flap. Classic, travel-inspired styling for weekends away and days on the move.

**Full description:**

The Charcoal & Tan Travel Backpack pairs a dark body with warm tan straps and trim for a classic, travel-inspired look.

A front flap fastens with buckled straps, and a top handle keeps it easy to grab. Pictured with a notebook and other travel essentials, it is a natural fit for weekends away and days on the move.

**Highlights**

- Dark charcoal body
- Tan straps and trim
- Buckled front flap
- Top carry handle

**SEO title:** Charcoal & Tan Travel Backpack | Rovakivo  
**Meta description:** Dark charcoal backpack with tan straps and trim and a buckled front flap. Classic styling for weekends away and days on the move.

**Image alt text:**

- cynthia-del-rio-HM599jibAQs-unsplash.jpg: Dark charcoal backpack with tan straps and trim, laid on a black surface with a notebook and a wristwatch.

### White Printed Flap Backpack

ID 170 · SKU 002 · Travel · simple · price shown: 25.00 €

**Short description:** A white backpack with an all-over black illustrated print, tan straps and orange trim. A light, playful look for city trips, holidays and weekends away.

**Full description:**

The White Printed Flap Backpack brings a playful touch, with an all-over black illustrated print on a white body framed by orange trim.

Tan straps fasten the top flap, and two front pockets with their own flaps keep smaller items organised. It is a cheerful pick for holidays, city trips and relaxed weekends.

**Highlights**

- White body with an all-over black print
- Orange trim
- Tan straps over the top flap
- Two front pockets with flaps

**SEO title:** White Printed Flap Backpack | Rovakivo  
**Meta description:** White backpack with an all-over black illustrated print, tan straps and orange trim. A playful bag for holidays, city trips and weekends.

**Image alt text:**

- cynthia-del-rio-tJWUHtIDzWs-unsplash.jpg: White backpack with a black illustrated print, tan straps and orange trim, lying on white bedding.

### Woven Flap Backpack

ID 279 · SKU 001 · Casual · external · price shown: 25.00 €

**Short description:** A backpack with a natural woven body, a smooth black top flap, black straps and a slim top handle. A minimal, eye-catching piece for everyday outfits.

**Full description:**

The Woven Flap Backpack pairs a natural-tone woven body with a clean black top flap, for a minimal look with plenty of texture.

Slim black shoulder straps and a top handle let you wear it on your back or carry it by hand, and a small label on the side of the flap adds a discreet finishing detail.

**Highlights**

- Natural-tone woven body
- Black top flap and straps
- Top carry handle
- Small side label

**SEO title:** Woven Flap Backpack | Rovakivo  
**Meta description:** Backpack with a natural woven body, a black top flap and black straps. A minimal, textured piece for everyday outfits.

**Image alt text:**

- byork-J7Dl7cA5lmg-unsplash.jpg: Backpack with a natural woven body and a black top flap, displayed on a white stand.
- byork-azsIrEsOvJY-unsplash.jpg: Back of the woven backpack, showing its two black shoulder straps.
- byork-Vc4P1KHp-FQ-unsplash.jpg: Person in a long dark coat holding the woven backpack with a black flap.

### Minimal Zip Backpack

ID 68 · SKU 010 · Travel · variable · price shown: from 30.00 €

**Short description:** A clean, minimal backpack with a zipped front pocket and a top handle. Choose black or grey in the sizes available for an easy everyday and travel bag.

**Full description:**

The Minimal Zip Backpack keeps things simple: clean lines, a zipped front pocket and a top handle, with no extra hardware to get in the way.

Choose from the options currently offered: Black in Small or Medium, or Grey in Medium. The price for each option is shown when you select it.

Its understated look works for the daily commute as well as for trips away.

**Highlights**

- Zipped front pocket
- Top carry handle
- Black: Small or Medium
- Grey: Medium

**SEO title:** Minimal Zip Backpack | Rovakivo  
**Meta description:** Minimal backpack with a zipped front pocket and a top handle, in black or grey. An understated bag for the commute and trips away.

**Image alt text:**

- daniel-lincoln-dp75uo9Qflo-unsplash.jpg: Black backpack lying on snow.
- alexandru-acea-XbY0xayztVU-unsplash.jpg: Black backpack with a zipped front pocket, sitting on a white chair.
- sj-baren-eX0N1Ex-qFo-unsplash.jpg: Grey backpack with a top handle and a zipped front pocket, sitting on a dark chair.
- luis-quintero-8TSqJoI-NVs-unsplash.jpg: Hand holding a black backpack by its top handle against a white background.
