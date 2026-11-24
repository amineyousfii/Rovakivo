<?php
/**
 * Homepage testimonials.
 *
 * Edited in Appearance → Homepage testimonials and stored in the `bar7_testimonials` option
 * (never as WooCommerce product reviews). While "sample content" is ticked, the section shows a
 * visible "Sample testimonials — design preview" label. The section adds no review structured data.
 */

defined( 'ABSPATH' ) || exit;

const BAR7_TESTIMONIALS_OPTION = 'bar7_testimonials';

/** Fictional design placeholders supplied with the brief. */
function bar7_testimonials_defaults(): array {
	return array(
		'sample' => true,
		'items'  => array(
			array(
				'name'   => 'Sophie Laurent',
				'rating' => 5,
				'text'   => 'I really like the simple design and neutral colour. It’s easy to match with my everyday outfits, and the overall style is exactly what I was looking for.',
				'avatar' => 0,
			),
			array(
				'name'   => 'Lukas Weber',
				'rating' => 4,
				'text'   => 'I was looking for a backpack with a straightforward look for everyday use and short trips. The understated style and buckle details are what caught my attention.',
				'avatar' => 0,
			),
			array(
				'name'   => 'Elena Rossi',
				'rating' => 5,
				'text'   => 'A great-looking bag with a classic, everyday style. I like that the design works with casual outfits and feels equally suited to a weekend away.',
				'avatar' => 0,
			),
		),
	);
}

function bar7_testimonials(): array {
	$data = get_option( BAR7_TESTIMONIALS_OPTION, null );
	if ( ! is_array( $data ) || ! isset( $data['items'] ) ) {
		$data = bar7_testimonials_defaults();
	}
	$data['items'] = array_values(
		array_filter(
			(array) $data['items'],
			static fn( $i ) => is_array( $i ) && '' !== trim( (string) ( $i['name'] ?? '' ) ) && '' !== trim( (string) ( $i['text'] ?? '' ) )
		)
	);
	$data['sample'] = ! empty( $data['sample'] );
	return $data;
}

function bar7_initials( string $name ): string {
	$parts    = preg_split( '/\s+/', trim( $name ) );
	$initials = '';
	foreach ( array( reset( $parts ), count( $parts ) > 1 ? end( $parts ) : '' ) as $p ) {
		if ( '' !== (string) $p ) {
			$initials .= function_exists( 'mb_substr' ) ? mb_strtoupper( mb_substr( $p, 0, 1 ) ) : strtoupper( substr( $p, 0, 1 ) );
		}
	}
	return $initials;
}

/**
 * Avatar markup: the initials are always rendered and the photo sits on top of them, so a missing
 * or failed image falls back to the initials. The name is printed beside it, so the image is decorative.
 */
function bar7_testimonial_avatar( array $item, int $index, bool $sample ): string {
	$src = '';
	if ( ! empty( $item['avatar'] ) ) {
		$img = wp_get_attachment_image_src( (int) $item['avatar'], 'thumbnail' );
		$src = $img ? $img[0] : '';
	} elseif ( $sample ) {
		$file = 'avatar-sample-' . ( ( $index % 3 ) + 1 ) . '.svg';
		if ( file_exists( BAR7_DIR . 'assets/img/' . $file ) ) {
			$src = bar7_img( $file );
		}
	}
	$html  = '<span class="bar7-quote__avatar" aria-hidden="true">';
	$html .= '<span class="bar7-quote__initials">' . esc_html( bar7_initials( (string) $item['name'] ) ) . '</span>';
	if ( $src ) {
		$html .= '<img src="' . esc_url( $src ) . '" alt="" width="56" height="56" loading="lazy" decoding="async" onerror="this.remove()">';
	}
	return $html . '</span>';
}

function bar7_stars( int $rating ): string {
	$rating = max( 1, min( 5, $rating ) );
	$out    = '<p class="bar7-quote__rating">';
	/* translators: %d: rating from 1 to 5 */
	$out .= '<span class="screen-reader-text">' . esc_html( sprintf( _n( '%d out of 5 stars', '%d out of 5 stars', $rating, 'bar7' ), $rating ) ) . '</span>';
	$out .= '<span class="bar7-quote__stars" aria-hidden="true">';
	for ( $i = 1; $i <= 5; $i++ ) {
		$out .= '<svg class="bar7-star' . ( $i <= $rating ? ' is-on' : '' ) . '" viewBox="0 0 24 24" width="18" height="18" focusable="false"><path d="M12 2.8l2.83 5.73 6.32.92-4.57 4.46 1.08 6.3L12 17.24l-5.66 2.97 1.08-6.3L2.85 9.45l6.32-.92z"/></svg>';
	}
	return $out . '</span></p>';
}

/* ---------- Admin: Appearance → Homepage testimonials ---------- */

add_action(
	'admin_menu',
	static function (): void {
		add_theme_page(
			__( 'Homepage testimonials', 'bar7' ),
			__( 'Homepage testimonials', 'bar7' ),
			'edit_theme_options',
			'bar7-testimonials',
			'bar7_testimonials_screen'
		);
	}
);

add_action(
	'admin_init',
	static function (): void {
		register_setting(
			'bar7_testimonials',
			BAR7_TESTIMONIALS_OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => 'bar7_testimonials_sanitize',
				'default'           => bar7_testimonials_defaults(),
			)
		);
	}
);

function bar7_testimonials_sanitize( $input ): array {
	$clean = array(
		'sample' => ! empty( $input['sample'] ),
		'items'  => array(),
	);
	foreach ( (array) ( $input['items'] ?? array() ) as $item ) {
		$clean['items'][] = array(
			'name'   => sanitize_text_field( $item['name'] ?? '' ),
			'rating' => max( 1, min( 5, (int) ( $item['rating'] ?? 5 ) ) ),
			'text'   => sanitize_textarea_field( $item['text'] ?? '' ),
			'avatar' => absint( $item['avatar'] ?? 0 ),
		);
	}
	return $clean;
}

add_action(
	'admin_enqueue_scripts',
	static function ( string $hook ): void {
		if ( 'appearance_page_bar7-testimonials' === $hook ) {
			wp_enqueue_media();
		}
	}
);

function bar7_testimonials_screen(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$data  = bar7_testimonials();
	$items = $data['items'];
	while ( count( $items ) < 3 ) {
		$items[] = array( 'name' => '', 'rating' => 5, 'text' => '', 'avatar' => 0 );
	}
	$opt = BAR7_TESTIMONIALS_OPTION;
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Homepage testimonials', 'bar7' ); ?></h1>
		<p><?php esc_html_e( 'Shown on the homepage under “What our customers say”. Use only genuine, approved customer feedback with the customer’s permission, in their own words. Leave a card’s name or review empty to hide it.', 'bar7' ); ?></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'bar7_testimonials' ); ?>
			<p>
				<label>
					<input type="checkbox" name="<?php echo esc_attr( $opt ); ?>[sample]" value="1" <?php checked( $data['sample'] ); ?>>
					<?php esc_html_e( 'These are sample testimonials (shows a visible “Sample testimonials — design preview” label and the illustrated placeholder avatars). Untick only when every card is a real, approved testimonial.', 'bar7' ); ?>
				</label>
			</p>
			<?php foreach ( $items as $i => $item ) : ?>
				<?php $field = $opt . '[items][' . $i . ']'; ?>
				<fieldset style="background:#fff;border:1px solid #dcdcde;padding:16px 20px;margin:16px 0;max-width:760px">
					<legend style="font-weight:600;padding:0 6px"><?php echo esc_html( sprintf( __( 'Card %d', 'bar7' ), $i + 1 ) ); ?></legend>
					<p>
						<label for="bar7-t-name-<?php echo (int) $i; ?>"><strong><?php esc_html_e( 'Full name', 'bar7' ); ?></strong></label><br>
						<input class="regular-text" id="bar7-t-name-<?php echo (int) $i; ?>" type="text" name="<?php echo esc_attr( $field ); ?>[name]" value="<?php echo esc_attr( $item['name'] ); ?>">
					</p>
					<p>
						<label for="bar7-t-rating-<?php echo (int) $i; ?>"><strong><?php esc_html_e( 'Rating', 'bar7' ); ?></strong></label><br>
						<select id="bar7-t-rating-<?php echo (int) $i; ?>" name="<?php echo esc_attr( $field ); ?>[rating]">
							<?php for ( $r = 5; $r >= 1; $r-- ) : ?>
								<option value="<?php echo (int) $r; ?>" <?php selected( (int) $item['rating'], $r ); ?>><?php echo esc_html( sprintf( __( '%d out of 5 stars', 'bar7' ), $r ) ); ?></option>
							<?php endfor; ?>
						</select>
					</p>
					<p>
						<label for="bar7-t-text-<?php echo (int) $i; ?>"><strong><?php esc_html_e( 'Review', 'bar7' ); ?></strong></label><br>
						<textarea class="large-text" rows="4" id="bar7-t-text-<?php echo (int) $i; ?>" name="<?php echo esc_attr( $field ); ?>[text]"><?php echo esc_textarea( $item['text'] ); ?></textarea>
					</p>
					<p class="bar7-t-avatar">
						<strong><?php esc_html_e( 'Customer photo (optional)', 'bar7' ); ?></strong><br>
						<?php $thumb = $item['avatar'] ? wp_get_attachment_image_url( (int) $item['avatar'], 'thumbnail' ) : ''; ?>
						<img src="<?php echo esc_url( $thumb ); ?>" alt="" width="56" height="56" style="border-radius:50%;object-fit:cover;vertical-align:middle;margin-right:8px;<?php echo $thumb ? '' : 'display:none'; ?>">
						<input type="hidden" name="<?php echo esc_attr( $field ); ?>[avatar]" value="<?php echo (int) $item['avatar']; ?>">
						<button type="button" class="button bar7-t-pick"><?php esc_html_e( 'Choose photo', 'bar7' ); ?></button>
						<button type="button" class="button-link bar7-t-clear" style="margin-left:8px"><?php esc_html_e( 'Remove', 'bar7' ); ?></button>
						<br><span class="description"><?php esc_html_e( 'Square image, at least 112×112 px. Without a photo, the initials are shown (or the illustrated placeholder while “sample” is ticked).', 'bar7' ); ?></span>
					</p>
				</fieldset>
			<?php endforeach; ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<script>
	( function () {
		document.querySelectorAll( '.bar7-t-avatar' ).forEach( function ( box ) {
			var input = box.querySelector( 'input[type=hidden]' );
			var img = box.querySelector( 'img' );
			box.querySelector( '.bar7-t-pick' ).addEventListener( 'click', function () {
				var frame = wp.media( { title: <?php echo wp_json_encode( __( 'Choose a customer photo', 'bar7' ) ); ?>, library: { type: 'image' }, multiple: false } );
				frame.on( 'select', function () {
					var a = frame.state().get( 'selection' ).first().toJSON();
					input.value = a.id;
					img.src = ( a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url );
					img.style.display = '';
				} );
				frame.open();
			} );
			box.querySelector( '.bar7-t-clear' ).addEventListener( 'click', function () {
				input.value = '0';
				img.style.display = 'none';
			} );
		} );
	}() );
	</script>
	<?php
}
