<?php
/**
 * Mini-cart (cart drawer). Keeps WooCommerce's hooks and remove links; adds quantity steppers
 * that update the cart through the WooCommerce Store API.
 *
 * @version 11.2.2
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' ); ?>

<?php if ( WC()->cart && ! WC()->cart->is_empty() ) : ?>

	<ul class="woocommerce-mini-cart cart_list product_list_widget bar7-mini">
		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				continue;
			}

			$name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
			$permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
			$price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
			$qty       = (int) $cart_item['quantity'];
			$max       = $_product->get_max_purchase_quantity();
			$sold_ind  = $_product->is_sold_individually();
			$parent    = wc_get_product( $product_id );
			$thumb     = $parent ? bar7_product_image( $parent, '72px', true ) : $_product->get_image( 'woocommerce_gallery_thumbnail' );
			?>
			<li class="woocommerce-mini-cart-item <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?> bar7-mini__item" data-key="<?php echo esc_attr( $cart_item_key ); ?>">
				<div class="bar7-mini__thumb"><?php echo $thumb; // phpcs:ignore ?></div>
				<div class="bar7-mini__info">
					<p class="bar7-mini__name">
						<?php if ( $permalink ) : ?>
							<a href="<?php echo esc_url( $permalink ); ?>"><?php echo wp_kses_post( $name ); ?></a>
						<?php else : ?>
							<?php echo wp_kses_post( $name ); ?>
						<?php endif; ?>
					</p>
					<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore ?>
					<p class="bar7-mini__price"><?php echo wp_kses_post( $price ); ?></p>
					<div class="bar7-mini__row">
						<?php if ( ! $sold_ind ) : ?>
							<div class="bar7-stepper" role="group" aria-label="<?php echo esc_attr( sprintf( __( 'Quantity of %s', 'bar7' ), wp_strip_all_tags( $name ) ) ); ?>">
								<button type="button" data-bar7-qty="<?php echo esc_attr( (string) ( $qty - 1 ) ); ?>" <?php disabled( $qty <= 1 ); ?> aria-label="<?php esc_attr_e( 'Decrease quantity', 'bar7' ); ?>"><?php echo bar7_icon( 'minus' ); // phpcs:ignore ?></button>
								<output aria-live="polite"><?php echo esc_html( (string) $qty ); ?></output>
								<button type="button" data-bar7-qty="<?php echo esc_attr( (string) ( $qty + 1 ) ); ?>" <?php disabled( $max > 0 && $qty >= $max ); ?> aria-label="<?php esc_attr_e( 'Increase quantity', 'bar7' ); ?>"><?php echo bar7_icon( 'plus' ); // phpcs:ignore ?></button>
							</div>
						<?php else : ?>
							<span class="bar7-mini__qty"><?php echo esc_html( sprintf( __( 'Qty %d', 'bar7' ), $qty ) ); ?></span>
						<?php endif; ?>
						<?php
						echo apply_filters( // phpcs:ignore
							'woocommerce_cart_item_remove_link',
							sprintf(
								'<a role="button" href="%s" class="remove remove_from_cart_button bar7-mini__remove" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s" data-success_message="%s">%s</a>',
								esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
								esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $name ) ) ),
								esc_attr( $product_id ),
								esc_attr( $cart_item_key ),
								esc_attr( $_product->get_sku() ),
								esc_attr( sprintf( __( '&ldquo;%s&rdquo; has been removed from your cart', 'woocommerce' ), wp_strip_all_tags( $name ) ) ),
								esc_html__( 'Remove', 'bar7' )
							),
							$cart_item_key
						);
						?>
					</div>
				</div>
			</li>
			<?php
		}

		do_action( 'woocommerce_mini_cart_contents' );
		?>
	</ul>

	<div class="bar7-mini__foot">
		<p class="woocommerce-mini-cart__total total"><?php do_action( 'woocommerce_widget_shopping_cart_total' ); ?></p>
		<p class="bar7-mini__note"><?php esc_html_e( 'Shipping and any discounts are calculated at checkout.', 'bar7' ); ?></p>
		<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>
		<p class="woocommerce-mini-cart__buttons buttons"><?php do_action( 'woocommerce_widget_shopping_cart_buttons' ); ?></p>
		<?php do_action( 'woocommerce_widget_shopping_cart_after_buttons' ); ?>
	</div>

<?php else : ?>

	<div class="woocommerce-mini-cart__empty-message bar7-mini__empty">
		<?php echo bar7_icon( 'bag' ); // phpcs:ignore ?>
		<p class="bar7-h3"><?php esc_html_e( 'Your cart is empty.', 'bar7' ); ?></p>
		<p><?php esc_html_e( 'Find a backpack that feels like you.', 'bar7' ); ?></p>
		<a class="bar7-btn bar7-btn--yellow" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Shop backpacks', 'bar7' ); ?><?php echo bar7_icon( 'arrow' ); // phpcs:ignore ?></a>
	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
