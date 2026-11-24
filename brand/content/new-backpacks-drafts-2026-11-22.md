# New backpacks — drafts for approval (2026-11-22)

**Status:** approved and published on 2026-11-22 (#656–661). Duplicate draft #655 was moved to the trash, where it can be restored. Live tests passed:
- Shop: 18 backpacks over 2 pages; Casual 10, Travel 8.
- Filters: Sale, price and sorting all work.
- Product pages: correct titles, meta, breadcrumbs, prices and galleries.
- Add to Cart: works from a card and from a product page (cart €99.00 for 2 items, then emptied).
- Layout: no sideways scrolling at 1440, 768 or 390 px.

All products below are **drafts** (not visible to customers). None has a sale price. Stock follows the shop's existing setup: in stock, stock not tracked. SEO title = "Name | Rovakivo". Card photos fill the shop's 3:4 frame.

| # | SKU | Name | Category | Price | Photos (Media IDs) | URL slug |
|---|---|---|---|---|---|---|
| 656 | 015 | Onyx Double-Zip Backpack | Casual | €55 | 648 | /product/onyx-double-zip-backpack/ |
| 657 | 016 | Heather Grey City Backpack | Casual | €42 | 649 | /product/heather-grey-city-backpack/ |
| 658 | 017 | Black & Orange Commuter Backpack | Casual | €48 | 651 | /product/black-orange-commuter-backpack/ |
| 659 | 018 | Orange Trail Daypack | Travel | €45 | 650 | /product/orange-trail-daypack/ |
| 660 | 019 | Evergreen Trek Backpack | Travel | €57 | 654 + gallery 652 | /product/evergreen-trek-backpack/ |
| 661 | 020 | Alpine Blue Trekking Backpack | Travel | €52 | 653 | /product/alpine-blue-trekking-backpack/ |
| 655 | 014 | ~~Chestnut Classic Rucksack~~ | Casual | €59 | 647 | duplicate: remove |

## Short descriptions

- **Onyx Double-Zip Backpack:** A sleek black backpack with twin zips, a front pocket and subtle contrast stitching. Polished enough for the office, relaxed enough for the weekend, and easy to wear with almost anything. (31 words)
- **Heather Grey City Backpack:** A soft heather-grey backpack with a rounded shape, silver-tone zips and a zipped front pocket. Quietly smart for lectures, desk days and weekend errands, and easy to pair with everything you own. (32 words)
- **Black & Orange Commuter Backpack:** A practical black backpack with grey panels, bright orange zip accents and several zipped compartments. Ready for the daily commute, school runs and spontaneous detours to the river after work. (30 words)
- **Orange Trail Daypack:** A bright orange and grey daypack with cord lacing across the front and mesh side pockets. Made for day hikes, crisp autumn walks and weekends when the only plan is to head outside. (33 words)
- **Evergreen Trek Backpack:** A tall evergreen pack with mustard-yellow straps and a domed top lid. Made for lakeside weekends, island hopping and long days outdoors, it looks just as good leaning on a rock as it does on your back. (37 words)
- **Alpine Blue Trekking Backpack:** A tall blue trekking pack with red accents and plenty of straps on the outside. Made for long weekends in the mountains and holidays where the road ends and the trail begins. (31 words)

Each product page also has a two-paragraph description and a "Highlights" list, in the same format as the existing products. Both describe only what is visible in the photos: colours, zips, pockets, straps and lids. They make no claims about materials, size, capacity, weight or waterproofing.

## Before publishing

1. **Duplicate:** `backpack-1` (Media 647) is the uncropped original of the photo already used by the published *Brown Flap Backpack* (#57). Draft #655 should go to the trash.
2. **Third-party logos visible in photos:**
   - PROVOGUE on 651 (Black & Orange Commuter)
   - deuter on 650 (Orange Trail)
   - WOODS on 652 and 654 (Evergreen Trek)
   - Mammut on 653 (Alpine Blue)

   Selling them as Rovakivo products could mislead customers or raise trademark issues, unless you stock these brands.
3. **Media 653** shows two different packs with red bedrolls. On the shop card, the 3:4 crop shows the left pack and half of the right one. A single-pack photo would be better. The draft's description says the bedrolls are not included.
4. No files named `backpack-7` or `backpack-9` were uploaded (the numbering skips them).

## Image alt text set

- 648: Black backpack with two top zips, a zipped front pocket and light contrast stitching, against a dark grey backdrop.
- 649: Heather-grey backpack with silver-tone zips and a zipped front pocket, standing on a white bed.
- 650: Orange and grey daypack with black cord lacing on the front, resting in tall autumn grass under a cloudy sky.
- 651: Black backpack with grey side panels and orange zip accents, standing on grass beside a river and pine trees.
- 652: Green backpack with mustard-yellow straps standing on a rocky lakeshore.
- 653: Two blue trekking backpacks with red bedrolls strapped on, standing on grass in front of colourful mountains.
- 654: Tall green backpack with mustard-yellow straps and buckles, leaning against a rock by a lake.
- 647: (set for the duplicate draft) Chestnut-brown rucksack with a darker brown flap, a round detail and two front pockets, standing on grass outdoors.

## Already in place (checked 2026-11-22)

- Shop filters: All backpacks, Casual and Travel, each with a count that updates on publish.
- "Shop casual" → /product-category/casual/
- "Shop travel" → /product-category/travel/
- "Explore the collection":
  - Homepage hero → the category tiles (#bar7-categories)
  - Header bar and About → /shop/
- Header Shop menu: Casual, Travel and Sale.
- CSS fix in `site.css`: WooCommerce's listing CSS forced `height:auto` on card photos. Any photo that isn't 3:4 left a grey gap on the Shop and category pages. Card photos now always fill the frame and are centred.
