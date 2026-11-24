<?php
/**
 * Rovakivo content update. Run with WP-CLI:  wp eval-file rovakivo-content.php
 *
 * Changes only titles, descriptions, alt text, SEO fields and branding settings.
 * Product IDs, SKUs, slugs (URLs), prices, stock, variations, attributes, reviews and
 * relationships are not touched. Every value written here is also in rovakivo-copy.md.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tagline = 'For every day. For wherever’s next.';

/* ---------------------------------------------------------------------------
 * Products, keyed by ID. Text describes only what the photos and store data show.
 * ------------------------------------------------------------------------ */
$products = array(
	66  => array(
		'title' => 'Olive Everyday Backpack',
		'short' => 'An olive backpack with dark contrast straps, a buckled top flap and two front pockets. A practical everyday pick for commutes, errands and weekends.',
		'body'  => array(
			'The Olive Everyday Backpack pairs a muted olive body with dark straps and hardware, giving it a relaxed, outdoorsy look that works just as well in town.',
			'A top flap closes with two buckled straps, and two front pockets sit below it, so the things you reach for most stay close at hand. A small emblem patch on the flap and a top grab handle finish the design.',
		),
		'bullets' => array( 'Olive body with dark contrast straps', 'Top flap with two buckle straps', 'Two front pockets', 'Emblem patch on the flap', 'Top grab handle' ),
		'alt'   => array( 67 => 'Olive backpack with dark straps, a buckled top flap and two front pockets, resting against a fallen tree trunk.' ),
		'meta'  => 'Olive backpack with dark contrast straps, a buckled top flap and two front pockets. An everyday backpack for commutes, errands and weekends.',
	),
	57  => array(
		'title' => 'Brown Flap Backpack',
		'short' => 'A warm brown backpack with a darker brown top flap and a front pocket. Simple lines and rich tones make it an easy match for everyday outfits.',
		'body'  => array(
			'The Brown Flap Backpack keeps things simple: a warm brown body, a darker brown flap that folds over the top, and a front pocket below it.',
			'A round button detail on the flap, a top handle and contrasting shoulder straps complete the look. It suits the daily routine, from the commute to a weekend out.',
		),
		'bullets' => array( 'Warm brown body with a darker brown top flap', 'Front pocket', 'Round button detail on the flap', 'Top carry handle' ),
		'alt'   => array( 58 => 'Brown backpack with a darker brown top flap and a front pocket, standing on grass.' ),
		'meta'  => 'Warm brown backpack with a darker brown top flap, a front pocket and a round button detail. Simple, everyday style from Rovakivo.',
	),
	64  => array(
		'title' => 'Charcoal Buckle Backpack',
		'short' => 'A structured charcoal backpack with a lighter grey flap, tan buckle tabs and an orange stripe down the front. A sharp, modern bag for the daily routine.',
		'body'  => array(
			'The Charcoal Buckle Backpack has a squared-off, structured shape in charcoal, with a lighter grey top flap and tan buckle tabs that add a little warmth.',
			'An orange stripe runs down the front pocket as a subtle accent, and a top handle makes it easy to grab on the way out. It is a clean, modern choice for commutes and city days.',
		),
		'bullets' => array( 'Structured charcoal body', 'Lighter grey top flap with tan buckle tabs', 'Front pocket with an orange stripe', 'Top carry handle' ),
		'alt'   => array( 65 => 'Charcoal backpack with a grey top flap, tan buckle tabs and an orange stripe, sitting on a rock outdoors.' ),
		'meta'  => 'Structured charcoal backpack with a grey top flap, tan buckle tabs and an orange stripe. A clean, modern bag for the daily routine.',
	),
	166 => array(
		'title' => 'Forest Green Travel Backpack',
		'short' => 'A tall forest-green backpack with a brown base, long brown straps and a buckled flap. Classic styling for weekends away and longer trips.',
		'body'  => array(
			'The Forest Green Travel Backpack combines a deep green upper with a brown base and long brown straps that run over the top flap and fasten at the front.',
			'Diamond-shaped details anchor each strap, and a top handle and side straps add to its classic look. Its tall shape and timeless colours make it a natural companion for weekends away and longer trips.',
		),
		'bullets' => array( 'Deep green upper with a brown base', 'Two long front straps over the top flap', 'Diamond-shaped strap details', 'Top carry handle and side straps' ),
		'alt'   => array(
			167 => 'Forest-green backpack with a brown base and brown front straps, standing on a paved path.',
			176 => 'Forest-green backpack with brown straps, set among plants outdoors.',
			177 => 'Close view of the forest-green backpack showing its brown straps, diamond strap details and brown base.',
			178 => 'Person in a tweed jacket holding a brown shoulder strap with a small hanging tag.',
		),
		'meta'  => 'Tall forest-green backpack with a brown base, long brown straps and diamond strap details. Classic styling for weekends away and longer trips.',
	),
	157 => array(
		'title' => 'Three Backpack Selection',
		'short' => 'Three backpacks in one place: the Charcoal Buckle Backpack, the Forest Green Travel Backpack and the Charcoal & Tan Travel Backpack. Add one, two or all three to your cart.',
		'body'  => array(
			'This listing brings three backpacks together, so you can compare them and add any combination to your cart in one step.',
			'The Charcoal Buckle Backpack is a structured charcoal bag with a grey flap and an orange stripe. The Forest Green Travel Backpack pairs a deep green upper with a brown base and straps. The Charcoal & Tan Travel Backpack combines a dark body with warm tan straps and trim.',
			'Each backpack’s current price, any sale price and stock are shown below, and each one also has its own product page with more photos.',
		),
		'bullets' => array( 'Charcoal Buckle Backpack', 'Forest Green Travel Backpack', 'Charcoal & Tan Travel Backpack', 'Choose quantities for each backpack separately' ),
		'alt'   => array( 158 => 'Black backpack with tan straps and trim, standing on a wooden desk beside a vintage camera.' ),
		'meta'  => 'Compare three Rovakivo backpacks and add any combination to your cart: Charcoal Buckle, Forest Green Travel and Charcoal & Tan Travel.',
	),
	155 => array(
		'title' => 'Green Zip Backpack',
		'short' => 'A green backpack with a zipped front pocket, a buckled top strap and dark contrast trim. A relaxed, versatile pick for school runs, commutes and days out.',
		'body'  => array(
			'The Green Zip Backpack brings a fresh green shade to the everyday, with dark trim and webbing that keep the look grounded.',
			'A zipped pocket across the front holds small essentials, and a dark strap with a metal buckle fastens over the top. Dark shoulder straps and a top handle complete a relaxed, versatile design.',
		),
		'bullets' => array( 'Green body with dark trim', 'Zipped front pocket', 'Dark strap with a metal buckle over the top', 'Top carry handle' ),
		'alt'   => array( 156 => 'Green backpack with a zipped front pocket and a dark buckled strap, standing against a white wooden wall beside tools.' ),
		'meta'  => 'Green backpack with a zipped front pocket, a buckled top strap and dark trim. A relaxed, versatile everyday bag from Rovakivo.',
	),
	162 => array(
		'title' => 'Black Everyday Backpack',
		'short' => 'A sleek black backpack with white lettering printed across the top and a small red accent. Clean and understated for school, work and errands.',
		'body'  => array(
			'The Black Everyday Backpack keeps a low-key, all-black profile with a few graphic touches: white lettering printed across the upper panel and a small red detail.',
			'A top handle makes it easy to carry in one hand when you are not wearing it. It is a simple, understated choice for school, work and everyday errands.',
		),
		'bullets' => array( 'All-black design', 'White printed lettering on the upper panel', 'Small red accent', 'Top carry handle' ),
		'alt'   => array( 163 => 'Black backpack with white printed lettering and a small red detail, held up by its top handle in front of green plants.' ),
		'meta'  => 'Sleek black backpack with white printed lettering and a small red accent. An understated everyday bag for school, work and errands.',
	),
	159 => array(
		'title' => 'Black Travel Backpack',
		'short' => 'A soft black backpack with a relaxed shape, a top handle and a small tan patch on the front. An easy, low-key companion for day trips and weekends away.',
		'body'  => array(
			'The Black Travel Backpack has a soft, unstructured shape and an all-black finish, with a small tan patch on the front as the only accent.',
			'Black shoulder straps and a top handle keep the look clean and simple, so it fits in on a city break as well as on a day out.',
		),
		'bullets' => array( 'All-black body', 'Soft, relaxed shape', 'Small tan patch on the front', 'Top carry handle' ),
		'alt'   => array( 161 => 'Black backpack with a small tan patch on the front, hanging from a white chair.' ),
		'meta'  => 'Soft black backpack with a relaxed shape, a top handle and a small tan patch. A low-key companion for day trips and weekends away.',
	),
	164 => array(
		'title' => 'Red Roll-Top Backpack',
		'short' => 'A bright red roll-top style backpack with a grey centre strap and a round tan clasp. A bold, easy-to-spot bag for everyday use and time outdoors.',
		'body'  => array(
			'The Red Roll-Top Backpack stands out in a vivid red, with a fold-over top held in place by a grey strap and a round tan clasp at the front.',
			'Its simple, rounded shape keeps the focus on colour, and it looks as at home on a forest walk, as pictured, as it does on the daily commute.',
		),
		'bullets' => array( 'Vivid red body', 'Fold-over, roll-top style closure', 'Grey centre strap with a round tan clasp', 'Shown worn on a forest walk' ),
		'alt'   => array(
			165 => 'Red roll-top backpack with a grey strap and a round tan clasp on a dark wooden table.',
			179 => 'Red roll-top backpack lying on dark wooden boards.',
			180 => 'Red roll-top backpack photographed against a grey wall.',
			181 => 'Person wearing the red backpack on a forest path in front of a wooden cabin.',
		),
		'meta'  => 'Bright red roll-top style backpack with a grey centre strap and a round tan clasp. A bold bag for everyday use and time outdoors.',
	),
	168 => array(
		'title' => 'Charcoal & Tan Travel Backpack',
		'short' => 'A dark charcoal backpack with tan straps and trim and a buckled front flap. Classic, travel-inspired styling for weekends away and days on the move.',
		'body'  => array(
			'The Charcoal & Tan Travel Backpack pairs a dark body with warm tan straps and trim for a classic, travel-inspired look.',
			'A front flap fastens with buckled straps, and a top handle keeps it easy to grab. Pictured with a notebook and other travel essentials, it is a natural fit for weekends away and days on the move.',
		),
		'bullets' => array( 'Dark charcoal body', 'Tan straps and trim', 'Buckled front flap', 'Top carry handle' ),
		'alt'   => array( 169 => 'Dark charcoal backpack with tan straps and trim, laid on a black surface with a notebook and a wristwatch.' ),
		'meta'  => 'Dark charcoal backpack with tan straps and trim and a buckled front flap. Classic styling for weekends away and days on the move.',
	),
	170 => array(
		'title' => 'White Printed Flap Backpack',
		'short' => 'A white backpack with an all-over black illustrated print, tan straps and orange trim. A light, playful look for city trips, holidays and weekends away.',
		'body'  => array(
			'The White Printed Flap Backpack brings a playful touch, with an all-over black illustrated print on a white body framed by orange trim.',
			'Tan straps fasten the top flap, and two front pockets with their own flaps keep smaller items organised. It is a cheerful pick for holidays, city trips and relaxed weekends.',
		),
		'bullets' => array( 'White body with an all-over black print', 'Orange trim', 'Tan straps over the top flap', 'Two front pockets with flaps' ),
		'alt'   => array( 171 => 'White backpack with a black illustrated print, tan straps and orange trim, lying on white bedding.' ),
		'meta'  => 'White backpack with an all-over black illustrated print, tan straps and orange trim. A playful bag for holidays, city trips and weekends.',
	),
	279 => array(
		'title' => 'Woven Flap Backpack',
		'short' => 'A backpack with a natural woven body, a smooth black top flap, black straps and a slim top handle. A minimal, eye-catching piece for everyday outfits.',
		'body'  => array(
			'The Woven Flap Backpack pairs a natural-tone woven body with a clean black top flap, for a minimal look with plenty of texture.',
			'Slim black shoulder straps and a top handle let you wear it on your back or carry it by hand, and a small label on the side of the flap adds a discreet finishing detail.',
		),
		'bullets' => array( 'Natural-tone woven body', 'Black top flap and straps', 'Top carry handle', 'Small side label' ),
		'alt'   => array(
			280 => 'Backpack with a natural woven body and a black top flap, displayed on a white stand.',
			281 => 'Back of the woven backpack, showing its two black shoulder straps.',
			282 => 'Person in a long dark coat holding the woven backpack with a black flap.',
		),
		'meta'  => 'Backpack with a natural woven body, a black top flap and black straps. A minimal, textured piece for everyday outfits.',
	),
	68  => array(
		'title' => 'Minimal Zip Backpack',
		'short' => 'A clean, minimal backpack with a zipped front pocket and a top handle. Choose black or grey in the sizes available for an easy everyday and travel bag.',
		'body'  => array(
			'The Minimal Zip Backpack keeps things simple: clean lines, a zipped front pocket and a top handle, with no extra hardware to get in the way.',
			'Choose from the options currently offered: Black in Small or Medium, or Grey in Medium. The price for each option is shown when you select it.',
			'Its understated look works for the daily commute as well as for trips away.',
		),
		'bullets' => array( 'Zipped front pocket', 'Top carry handle', 'Black: Small or Medium', 'Grey: Medium' ),
		'alt'   => array(
			69  => 'Black backpack lying on snow.',
			223 => 'Black backpack with a zipped front pocket, sitting on a white chair.',
			221 => 'Grey backpack with a top handle and a zipped front pocket, sitting on a dark chair.',
			219 => 'Hand holding a black backpack by its top handle against a white background.',
		),
		'meta'  => 'Minimal backpack with a zipped front pocket and a top handle, in black or grey. An understated bag for the commute and trips away.',
	),
);

$log = array();
foreach ( $products as $id => $p ) {
	$product = wc_get_product( $id );
	if ( ! $product ) {
		$log[] = "MISSING product $id";
		continue;
	}
	$old  = $product->get_name();
	$slug = get_post_field( 'post_name', $id );

	$html = '';
	foreach ( $p['body'] as $para ) {
		$html .= '<p>' . esc_html( $para ) . "</p>\n";
	}
	$html .= "<h3>Highlights</h3>\n<ul>\n";
	foreach ( $p['bullets'] as $b ) {
		$html .= '<li>' . esc_html( $b ) . "</li>\n";
	}
	$html .= "</ul>\n";

	$product->set_name( $p['title'] );
	$product->set_short_description( '<p>' . esc_html( $p['short'] ) . '</p>' );
	$product->set_description( $html );
	$product->save();

	// Keep the existing URL.
	if ( get_post_field( 'post_name', $id ) !== $slug ) {
		wp_update_post( array( 'ID' => $id, 'post_name' => $slug ) );
	}

	foreach ( $p['alt'] as $att => $alt ) {
		update_post_meta( $att, '_wp_attachment_image_alt', $alt );
	}
	update_post_meta( $id, '_bar7_seo_title', $p['title'] . ' | Rovakivo' );
	update_post_meta( $id, '_bar7_meta_description', $p['meta'] );

	$log[] = sprintf( '%d  %s  →  %s  (slug kept: %s)', $id, $old, $p['title'], get_post_field( 'post_name', $id ) === $slug ? 'yes' : 'NO' );
}

/* ---------------------------------------------------------------------------
 * Collections
 * ------------------------------------------------------------------------ */
$cats = array(
	'casual' => array(
		'desc' => 'Backpacks for the daily routine: commutes, errands, school runs and weekends close to home. Easy shapes and colours that work with what you already wear.',
		'meta' => 'Casual backpacks from Rovakivo for commutes, errands and the daily routine, in easy shapes and colours that work with what you already wear.',
	),
	'travel' => array(
		'desc' => 'Backpacks for trips away, from city breaks and weekends to longer journeys. Classic shapes and colours for days on the move.',
		'meta' => 'Travel backpacks from Rovakivo for city breaks, weekends away and longer trips, in classic shapes and colours.',
	),
);
foreach ( $cats as $slug => $c ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	if ( $term ) {
		wp_update_term( $term->term_id, 'product_cat', array( 'description' => $c['desc'] ) );
		update_term_meta( $term->term_id, '_bar7_meta_description', $c['meta'] );
		$log[] = "category $slug: description set";
	}
}

/* ---------------------------------------------------------------------------
 * Pages: titles, SEO, demo pages to draft, privacy page placeholder text replaced
 * ------------------------------------------------------------------------ */
wp_update_post( array( 'ID' => 17, 'post_title' => 'Shop' ) );
$pages_seo = array(
	17  => array( 'All Bags & Backpacks | Rovakivo', 'Shop Rovakivo bags and backpacks for daily routines, travel plans and time outdoors. Casual and travel collections, with current offers.' ),
	115 => array( 'About | Rovakivo', 'Rovakivo is an online shop for bags and backpacks for the daily commute, trips away and time spent outdoors.' ),
	117 => array( 'Contact | Rovakivo', 'Questions about a bag, an order or your account? Send Rovakivo a message and we’ll reply by email.' ),
	18  => array( 'Your Cart | Rovakivo', '' ),
	19  => array( 'Checkout | Rovakivo', '' ),
	20  => array( 'My Account | Rovakivo', '' ),
	363 => array( 'Privacy Policy | Rovakivo', 'How the Rovakivo website uses the information you share when you order, sign up for the newsletter or send a message.' ),
);
foreach ( $pages_seo as $pid => [ $t, $d ] ) {
	update_post_meta( $pid, '_bar7_seo_title', $t );
	if ( $d ) {
		update_post_meta( $pid, '_bar7_meta_description', $d );
	}
}
wp_update_post( array( 'ID' => 115, 'post_title' => 'About' ) );
wp_update_post( array( 'ID' => 117, 'post_title' => 'Contact' ) );

foreach ( array( 2, 342, 351 ) as $demo ) {
	if ( 'publish' === get_post_status( $demo ) ) {
		wp_update_post( array( 'ID' => $demo, 'post_status' => 'draft' ) );
		$log[] = "page $demo (" . get_the_title( $demo ) . ') set to draft';
	}
}

// Privacy page: the old text was entirely lorem ipsum. This describes only what the site
// demonstrably does; it is not a full legal policy (see the handoff for what is missing).
$privacy = <<<HTML
<!-- wp:paragraph -->
<p>This page explains what information the Rovakivo website collects when you use it, and why.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>When you place an order</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>At checkout we ask for your name, billing and delivery address, email address and phone number. We use these details to process and deliver your order and to contact you about it. If you create an account, your order history and saved addresses are kept with it.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Newsletter</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>If you sign up for the newsletter, we store your email address and send you a message to confirm it. Every newsletter includes a link to unsubscribe.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Contact form</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>When you send us a message, we receive your name, email address and message by email so that we can reply.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Cookies</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The shop uses cookies to keep the contents of your cart and to keep you signed in to your account. When you place an order, information about how you arrived at the site (for example, the page or link that brought you here) may be saved with the order.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Questions</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>For any question about your information, please use our contact page.</p>
<!-- /wp:paragraph -->
HTML;
wp_update_post( array( 'ID' => 363, 'post_content' => $privacy ) );
$log[] = 'privacy page 363: placeholder text replaced';

/* ---------------------------------------------------------------------------
 * Site identity, emails, newsletter sender, contact form
 * ------------------------------------------------------------------------ */
update_option( 'blogname', 'Rovakivo' );
update_option( 'blogdescription', $tagline );
update_option( 'bar7_home_seo_title', 'Rovakivo | Bags and backpacks for every day, travel and time outdoors' );
update_option( 'bar7_home_meta_description', 'Discover Rovakivo bags and backpacks for daily routines, travel plans and time outdoors. Shop casual and travel backpacks.' );

update_option( 'woocommerce_email_from_name', 'Rovakivo' );
update_option( 'woocommerce_email_base_color', '#161616' );
update_option( 'woocommerce_email_footer_text', '{site_title} | ' . $tagline );

if ( class_exists( '\MailPoet\Settings\SettingsController' ) ) {
	$settings = \MailPoet\DI\ContainerWrapper::getInstance()->get( \MailPoet\Settings\SettingsController::class );
	$sender   = (array) $settings->get( 'sender' );
	$sender['name'] = 'Rovakivo';
	$settings->set( 'sender', $sender );
	$log[] = 'MailPoet sender name: Rovakivo';
}

$form = get_post( 506 );
if ( $form ) {
	$data = json_decode( $form->post_content, true );
	if ( is_array( $data ) ) {
		foreach ( $data['settings']['notifications'] ?? array() as $k => $n ) {
			$data['settings']['notifications'][ $k ]['sender_name'] = 'Rovakivo';
			$data['settings']['notifications'][ $k ]['subject']     = 'New message from the Rovakivo contact form';
		}
		foreach ( $data['settings']['confirmations'] ?? array() as $k => $c ) {
			$data['settings']['confirmations'][ $k ]['message'] = '<p>Thank you. Your message has been sent, and we’ll reply by email.</p>';
		}
		if ( isset( $data['fields'][2] ) ) {
			$data['fields'][2]['label'] = 'Message';
		}
		$data['settings']['form_title'] = 'Contact form';
		$data['settings']['submit_text'] = 'Send message';
		$data['settings']['submit_text_processing'] = 'Sending…';
		wp_update_post( array( 'ID' => 506, 'post_title' => 'Contact form', 'post_content' => wp_slash( wp_json_encode( $data ) ) ) );
		$log[] = 'WPForms 506: sender, subject, confirmation, labels updated';
	}
}

// Variable product: recalculate its stored stock status from its variations (WooCommerce's own sync).
WC_Product_Variable::sync( 68 );
$log[] = 'product 68 stock status after sync: ' . wc_get_product( 68 )->get_stock_status();

echo implode( "\n", $log ), "\n";
