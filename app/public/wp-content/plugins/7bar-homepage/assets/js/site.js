/**
 * 7bar storefront interactions: drawers (menu, cart, filters), search, quantity steppers,
 * mini-cart updates, add-to-cart feedback, product gallery and tabs, homepage filter, newsletter.
 * Motion comes from window.bar7 (motion.js); every interaction also works without it.
 */

/* Imagify delivers WebP with <picture><source type="image/webp">…<img></picture>: when a script changes the
   photo, the WebP source must follow it (or be removed, so the new photo's own file is shown). */
function bar7WebpSource( img, webpSrcset ) {
	var pic = img && img.parentNode && img.parentNode.nodeName === 'PICTURE' ? img.parentNode : null;
	if ( ! pic ) { return; }
	var source = pic.querySelector( 'source[type="image/webp"]' );
	if ( webpSrcset ) {
		if ( ! source ) { source = document.createElement( 'source' ); source.type = 'image/webp'; pic.insertBefore( source, img ); }
		source.srcset = webpSrcset;
		if ( img.getAttribute( 'sizes' ) ) { source.sizes = img.getAttribute( 'sizes' ); }
	} else if ( source ) {
		source.parentNode.removeChild( source );
	}
}

( function ( w, d, $ ) {
	'use strict';

	var S = w.bar7Site || {};
	var t = S.i18n || {};
	var M = function () { return w.bar7 || { tokens: { dur: {} }, reduced: true, lock: function () {}, unlock: function () {}, refresh: function () {}, scrollTo: function ( el ) { el.scrollIntoView(); } }; };
	var gsap = w.gsap;
	var motion = function () { return gsap && ! M().reduced; };

	var live = d.getElementById( 'bar7-live' );
	function announce( text ) {
		if ( ! live || ! text ) { return; }
		live.textContent = '';
		w.setTimeout( function () { live.textContent = text; }, 60 );
	}

	/* ---------- Drawers: menu, cart, filters ---------- */
	var openDrawer = null;
	var lastTrigger = null;

	function focusables( root ) {
		return Array.prototype.filter.call(
			root.querySelectorAll( 'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select, textarea, [tabindex]:not([tabindex="-1"])' ),
			function ( el ) { return el.offsetParent !== null; }
		);
	}

	function showDrawer( drawer, trigger ) {
		if ( openDrawer === drawer ) { return; }
		if ( openDrawer ) { hideDrawer( openDrawer, true ); }
		lastTrigger = trigger || d.activeElement;
		openDrawer = drawer;
		drawer.hidden = false;
		d.querySelectorAll( '[aria-controls="' + drawer.id + '"]' ).forEach( function ( b ) { b.setAttribute( 'aria-expanded', 'true' ); } );
		M().lock();
		var panel = drawer.querySelector( '.bar7-drawer__panel' );
		var backdrop = drawer.querySelector( '.bar7-drawer__backdrop' );
		var fromRight = drawer.classList.contains( 'bar7-drawer--right' );
		if ( motion() ) {
			var T = M().tokens;
			gsap.fromTo( backdrop, { opacity: 0 }, { opacity: 1, duration: T.dur.drawerIn * 0.8, ease: 'power1.out' } );
			gsap.fromTo( panel, { xPercent: fromRight ? 100 : -100 }, { xPercent: 0, duration: T.dur.drawerIn, ease: T.ease } );
		}
		var first = focusables( panel )[ 0 ];
		( first || panel ).focus( { preventScroll: true } );
	}

	function hideDrawer( drawer, instant ) {
		if ( ! drawer || drawer.hidden ) { return; }
		var finish = function () {
			drawer.hidden = true;
			gsap && gsap.set( drawer.querySelectorAll( '.bar7-drawer__panel, .bar7-drawer__backdrop' ), { clearProps: 'all' } );
		};
		d.querySelectorAll( '[aria-controls="' + drawer.id + '"]' ).forEach( function ( b ) { b.setAttribute( 'aria-expanded', 'false' ); } );
		if ( openDrawer === drawer ) { openDrawer = null; }
		M().unlock();
		if ( motion() && ! instant ) {
			var T = M().tokens;
			var fromRight = drawer.classList.contains( 'bar7-drawer--right' );
			gsap.to( drawer.querySelector( '.bar7-drawer__backdrop' ), { opacity: 0, duration: T.dur.drawerOut } );
			gsap.to( drawer.querySelector( '.bar7-drawer__panel' ), { xPercent: fromRight ? 100 : -100, duration: T.dur.drawerOut, ease: T.easeIn, onComplete: finish } );
		} else {
			finish();
		}
		if ( ! instant && lastTrigger && d.contains( lastTrigger ) ) {
			lastTrigger.focus( { preventScroll: true } );
		}
	}

	d.addEventListener( 'click', function ( e ) {
		var opener = e.target.closest( '[data-bar7-open]' );
		if ( opener ) {
			var drawer = d.getElementById( opener.getAttribute( 'data-bar7-open' ) );
			if ( drawer ) {
				e.preventDefault();
				showDrawer( drawer, opener );
			}
			return;
		}
		if ( e.target.closest( '[data-bar7-close]' ) && openDrawer ) {
			hideDrawer( openDrawer );
		}
	} );

	d.addEventListener( 'keydown', function ( e ) {
		if ( ! openDrawer ) { return; }
		if ( e.key === 'Escape' ) {
			hideDrawer( openDrawer );
			return;
		}
		if ( e.key !== 'Tab' ) { return; }
		var items = focusables( openDrawer.querySelector( '.bar7-drawer__panel' ) );
		if ( ! items.length ) { return; }
		var first = items[ 0 ], last = items[ items.length - 1 ];
		if ( e.shiftKey && d.activeElement === first ) { e.preventDefault(); last.focus(); }
		else if ( ! e.shiftKey && d.activeElement === last ) { e.preventDefault(); first.focus(); }
	} );

	// Desktop layouts have no menu or filter drawer.
	if ( w.matchMedia ) {
		w.matchMedia( '(min-width: 1100px)' ).addEventListener( 'change', function ( mq ) {
			if ( mq.matches && openDrawer && openDrawer.id !== 'bar7-cartdrawer' ) { hideDrawer( openDrawer, true ); }
		} );
	}

	/* ---------- Search panel ---------- */
	var searchBtn = d.querySelector( '[data-bar7-search-toggle]' );
	var searchPanel = d.getElementById( 'bar7-search' );
	if ( searchBtn && searchPanel ) {
		searchBtn.addEventListener( 'click', function () {
			var open = searchPanel.hidden;
			searchPanel.hidden = ! open;
			searchBtn.setAttribute( 'aria-expanded', String( open ) );
			if ( open ) {
				if ( motion() ) { gsap.fromTo( searchPanel, { opacity: 0, y: -6 }, { opacity: 1, y: 0, duration: M().tokens.dur.fast, clearProps: 'all' } ); }
				searchPanel.querySelector( 'input[type="search"]' ).focus();
			}
		} );
		searchPanel.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' ) {
				searchPanel.hidden = true;
				searchBtn.setAttribute( 'aria-expanded', 'false' );
				searchBtn.focus();
			}
		} );
	}

	/* ---------- Quantity steppers for WooCommerce quantity fields ---------- */
	function enhanceQuantities( root ) {
		( root || d ).querySelectorAll( '.quantity input.qty[type="number"]' ).forEach( function ( input ) {
			if ( input.closest( '.bar7-stepper' ) ) { return; }
			var wrap = d.createElement( 'div' );
			wrap.className = 'bar7-stepper';
			var label = input.getAttribute( 'aria-label' ) || ( input.id && d.querySelector( 'label[for="' + input.id + '"]' ) ? d.querySelector( 'label[for="' + input.id + '"]' ).textContent.trim() : 'Quantity' );
			var mk = function ( dir ) {
				var b = d.createElement( 'button' );
				b.type = 'button';
				b.setAttribute( 'aria-label', ( dir < 0 ? 'Decrease ' : 'Increase ' ) + label.toLowerCase() );
				b.innerHTML = dir < 0
					? '<svg class="bar7-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><path d="M6 12h12"/></svg>'
					: '<svg class="bar7-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><path d="M12 6v12M6 12h12"/></svg>';
				b.addEventListener( 'click', function () {
					var step = parseFloat( input.step ) || 1;
					var min = input.min !== '' ? parseFloat( input.min ) : 0;
					var max = input.max !== '' ? parseFloat( input.max ) : Infinity;
					var val = ( parseFloat( input.value ) || 0 ) + dir * step;
					val = Math.min( max, Math.max( min, val ) );
					if ( String( val ) === input.value ) { return; }
					input.value = val;
					input.dispatchEvent( new Event( 'input', { bubbles: true } ) );
					input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
					sync();
				} );
				return b;
			};
			var minus = mk( -1 ), plus = mk( 1 );
			var sync = function () {
				var v = parseFloat( input.value ) || 0;
				minus.disabled = input.min !== '' && v <= parseFloat( input.min );
				plus.disabled = input.max !== '' && v >= parseFloat( input.max );
			};
			input.parentNode.insertBefore( wrap, input );
			wrap.appendChild( minus );
			wrap.appendChild( input );
			wrap.appendChild( plus );
			input.addEventListener( 'input', sync );
			sync();
		} );
	}
	enhanceQuantities();

	/* ---------- Cart page: quantity changes update the cart through WooCommerce ---------- */
	var cartTimer = 0;
	d.addEventListener( 'change', function ( e ) {
		if ( ! e.target.matches || ! e.target.matches( '.woocommerce-cart-form input.qty' ) ) { return; }
		w.clearTimeout( cartTimer );
		cartTimer = w.setTimeout( function () {
			var btn = d.querySelector( '.woocommerce-cart-form [name="update_cart"]' );
			if ( btn ) {
				btn.disabled = false;
				btn.removeAttribute( 'aria-disabled' );
				announce( t.updating );
				btn.click();
			}
		}, 700 );
	} );

	if ( $ ) {
		$( d.body ).on( 'updated_wc_div updated_cart_totals', function () {
			enhanceQuantities();
			announce( t.updated );
			if ( motion() ) {
				gsap.fromTo( '.cart_totals', { opacity: 0.4 }, { opacity: 1, duration: M().tokens.dur.base } );
			}
		} );
		// Removing a row: fade it while WooCommerce processes the request.
		$( d.body ).on( 'click', '.woocommerce-cart-form .product-remove a.remove', function () {
			var row = this.closest( 'tr' );
			if ( row && motion() ) { gsap.to( row, { opacity: 0.25, duration: M().tokens.dur.fast } ); }
		} );
		$( d.body ).on( 'removed_from_cart', function () { announce( t.removed ); } );
		$( d.body ).on( 'updated_checkout', function () { enhanceQuantities(); } );
	}

	/* ---------- Mini-cart quantity steppers (WooCommerce Store API) ---------- */
	d.addEventListener( 'click', function ( e ) {
		var btn = e.target.closest( '[data-bar7-qty]' );
		if ( ! btn || ! S.storeApi ) { return; }
		var item = btn.closest( '.bar7-mini__item' );
		if ( ! item ) { return; }
		var qty = parseInt( btn.getAttribute( 'data-bar7-qty' ), 10 );
		if ( ! ( qty >= 1 ) ) { return; }
		item.classList.add( 'is-busy' );
		item.setAttribute( 'aria-busy', 'true' );
		announce( t.updating );
		fetch( S.storeApi + 'cart/update-item', {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/json', Nonce: S.storeNonce },
			body: JSON.stringify( { key: item.getAttribute( 'data-key' ), quantity: qty } )
		} ).then( function ( r ) {
			var nonce = r.headers.get( 'Nonce' );
			if ( nonce ) { S.storeNonce = nonce; }
			if ( ! r.ok ) { throw new Error( 'cart' ); }
			return r.json();
		} ).then( function () {
			$ && $( d.body ).trigger( 'wc_fragment_refresh' );
			$ && $( d.body ).one( 'wc_fragments_refreshed', function () {
				announce( t.updated );
				var again = d.querySelector( '.bar7-mini__item[data-key="' + item.getAttribute( 'data-key' ) + '"] [data-bar7-qty="' + ( qty + ( btn.getAttribute( 'aria-label' ).indexOf( 'Increase' ) === 0 ? 1 : -1 ) ) + '"]' );
				if ( again && ! again.disabled ) { again.focus(); }
			} );
		} ).catch( function () {
			item.classList.remove( 'is-busy' );
			item.removeAttribute( 'aria-busy' );
			announce( t.cartError );
		} );
	} );

	/* ---------- Add to cart from product cards ---------- */
	if ( $ ) {
		$( d.body ).on( 'adding_to_cart', function ( e, $button ) {
			if ( $button && $button.length ) {
				$button.attr( 'aria-busy', 'true' );
			}
		} );
		$( d.body ).on( 'added_to_cart', function ( e, fragments, hash, $button ) {
			if ( ! $button || ! $button.length ) { return; }
			$button.removeAttr( 'aria-busy' );
			var text = $button.find( '.bar7-card__action-text' );
			text.text( t.addedShort || 'Added to cart' );
			var title = $button.closest( '.bar7-card' ).find( '.bar7-card__title' ).text().trim();
			announce( ( t.added || '%s was added to your cart.' ).replace( '%s', title || 'Item' ) );
			w.setTimeout( function () { text.text( t.addToCart || 'Add to cart' ); }, 2600 );
			var drawer = d.getElementById( 'bar7-cartdrawer' );
			if ( drawer && S.openCartOnAdd ) {
				showDrawer( drawer, $button.get( 0 ) );
			}
		} );
		$( d.body ).on( 'ajax_request_not_sent.adding_to_cart', function () { announce( t.cartError ); } );
	}

	// Single product form: show a real "adding" state while the form submits.
	d.addEventListener( 'submit', function ( e ) {
		var form = e.target;
		if ( ! form.matches || ! form.matches( 'form.cart' ) ) { return; }
		var btn = form.querySelector( '.single_add_to_cart_button' );
		if ( btn && ! btn.classList.contains( 'disabled' ) ) {
			btn.classList.add( 'is-loading' );
			btn.setAttribute( 'aria-busy', 'true' );
		}
	} );
	w.addEventListener( 'pageshow', function () {
		d.querySelectorAll( '.single_add_to_cart_button.is-loading' ).forEach( function ( b ) {
			b.classList.remove( 'is-loading' );
			b.removeAttribute( 'aria-busy' );
		} );
	} );

	// Variations: make the price/stock change noticeable.
	if ( $ ) {
		$( d.body ).on( 'found_variation', 'form.variations_form', function () {
			var box = this.querySelector( '.woocommerce-variation' );
			if ( box && motion() ) {
				gsap.fromTo( box, { opacity: 0, y: 6 }, { opacity: 1, y: 0, duration: M().tokens.dur.base, clearProps: 'transform' } );
			}
		} );
	}

	/* ---------- Product gallery ---------- */
	d.querySelectorAll( '[data-bar7-gallery]' ).forEach( function ( gallery ) {
		var main = gallery.querySelector( '[data-bar7-gallery-main]' );
		var thumbs = gallery.querySelectorAll( '.bar7-gallery__thumb' );
		if ( ! main || ! thumbs.length ) { return; }
		thumbs.forEach( function ( thumb ) {
			thumb.addEventListener( 'click', function () {
				if ( thumb.getAttribute( 'aria-pressed' ) === 'true' ) { return; }
				thumbs.forEach( function ( b ) { b.setAttribute( 'aria-pressed', String( b === thumb ) ); } );
				var swap = function () {
					bar7WebpSource( main, thumb.getAttribute( 'data-webp-srcset' ) );
					main.src = thumb.getAttribute( 'data-src' );
					if ( thumb.getAttribute( 'data-srcset' ) ) { main.srcset = thumb.getAttribute( 'data-srcset' ); } else { main.removeAttribute( 'srcset' ); }
					main.width = thumb.getAttribute( 'data-w' );
					main.height = thumb.getAttribute( 'data-h' );
					if ( thumb.getAttribute( 'data-alt' ) ) { main.alt = thumb.getAttribute( 'data-alt' ); }
				};
				if ( motion() ) {
					gsap.to( main, { opacity: 0, duration: 0.15, onComplete: function () {
						swap();
						var reveal = function () { gsap.to( main, { opacity: 1, duration: 0.3 } ); };
						if ( main.complete ) { reveal(); } else { main.addEventListener( 'load', reveal, { once: true } ); }
					} } );
				} else {
					swap();
				}
			} );
		} );
	} );

	/* ---------- Product information tabs (WAI-ARIA tabs pattern) ---------- */
	d.querySelectorAll( '[data-bar7-tabset]' ).forEach( function ( set ) {
		var tabs = Array.prototype.slice.call( set.querySelectorAll( '[role="tab"]' ) );
		var select = function ( tab, focus ) {
			tabs.forEach( function ( tb ) {
				var on = tb === tab;
				tb.setAttribute( 'aria-selected', String( on ) );
				tb.tabIndex = on ? 0 : -1;
				var panel = d.getElementById( tb.getAttribute( 'aria-controls' ) );
				if ( panel ) { panel.hidden = ! on; }
				if ( on && panel && motion() ) {
					gsap.fromTo( panel, { opacity: 0, y: 8 }, { opacity: 1, y: 0, duration: M().tokens.dur.base, clearProps: 'transform' } );
				}
			} );
			if ( focus ) { tab.focus(); }
			M().refresh();
		};
		tabs.forEach( function ( tab, i ) {
			tab.addEventListener( 'click', function () { select( tab ); } );
			tab.addEventListener( 'keydown', function ( e ) {
				var next = null;
				if ( e.key === 'ArrowRight' ) { next = tabs[ ( i + 1 ) % tabs.length ]; }
				if ( e.key === 'ArrowLeft' ) { next = tabs[ ( i - 1 + tabs.length ) % tabs.length ]; }
				if ( e.key === 'Home' ) { next = tabs[ 0 ]; }
				if ( e.key === 'End' ) { next = tabs[ tabs.length - 1 ]; }
				if ( next ) { e.preventDefault(); select( next, true ); }
			} );
		} );
		// Rating link and #reviews / #comment-N URLs open the Reviews tab.
		var openReviews = function () {
			var tab = set.querySelector( '[data-tab="reviews"]' );
			if ( tab ) { select( tab ); }
			return tab;
		};
		d.querySelectorAll( '[data-bar7-tab-link="reviews"]' ).forEach( function ( a ) {
			a.addEventListener( 'click', function ( e ) {
				if ( openReviews() ) {
					e.preventDefault();
					M().scrollTo( set );
				}
			} );
		} );
		if ( /^#(reviews|tab-reviews|comment-\d+|respond)$/.test( w.location.hash ) ) { openReviews(); }
	} );

	/* ---------- Homepage: popular products filter ---------- */
	var tabsGroup = d.querySelector( '[data-bar7-tabs]' );
	var grid = d.querySelector( '[data-bar7-filter-grid]' );
	if ( tabsGroup && grid ) {
		tabsGroup.hidden = false;
		tabsGroup.addEventListener( 'click', function ( e ) {
			var btn = e.target.closest( 'button[data-filter]' );
			if ( ! btn ) { return; }
			var filter = btn.getAttribute( 'data-filter' );
			tabsGroup.querySelectorAll( 'button' ).forEach( function ( b ) { b.setAttribute( 'aria-pressed', String( b === btn ) ); } );
			var visible = [];
			grid.querySelectorAll( '.bar7-card' ).forEach( function ( card ) {
				var cats = ( card.getAttribute( 'data-cats' ) || '' ).split( ' ' );
				var match = ! filter || cats.indexOf( filter ) !== -1;
				card.hidden = ! match;
				if ( match ) { visible.push( card ); }
			} );
			if ( motion() ) {
				gsap.fromTo( visible, { opacity: 0, y: 10 }, { opacity: 1, y: 0, duration: M().tokens.dur.base, stagger: 0.04, overwrite: true, clearProps: 'transform' } );
			}
			M().refresh();
			announce( visible.length === 1 ? ( t.shownOne || '1 backpack shown' ) : ( t.shown || '%d backpacks shown' ).replace( '%d', visible.length ) );
		} );
	}

	/* ---------- Newsletter ---------- */
	var form = d.querySelector( '[data-bar7-newsletter]' );
	if ( form && w.fetch && S.ajaxUrl ) {
		var input = form.querySelector( 'input[type="email"]' );
		var msg = form.querySelector( '.bar7-form__msg' );
		var submit = form.querySelector( 'button[type="submit"]' );
		var setMsg = function ( text, ok ) {
			msg.textContent = text;
			msg.classList.toggle( 'is-ok', !! ok && !! text );
			msg.classList.toggle( 'is-error', ! ok && !! text );
			input.setAttribute( 'aria-invalid', ok || ! text ? 'false' : 'true' );
		};
		input.addEventListener( 'input', function () {
			if ( input.getAttribute( 'aria-invalid' ) === 'true' ) { setMsg( '', true ); }
		} );
		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			if ( ! input.value.trim() || ! input.checkValidity() ) {
				setMsg( t.invalid, false );
				input.focus();
				return;
			}
			var data = new FormData( form );
			data.set( 'action', 'bar7_subscribe' );
			submit.disabled = true;
			submit.setAttribute( 'aria-busy', 'true' );
			setMsg( t.subscribing, true );
			fetch( S.ajaxUrl, { method: 'POST', body: data, credentials: 'same-origin' } )
				.then( function ( r ) { return r.json(); } )
				.then( function ( res ) {
					setMsg( res && res.message ? res.message : t.cartError, !! ( res && res.ok ) );
					if ( res && res.ok ) { form.reset(); }
				} )
				.catch( function () { setMsg( t.offline, false ); } )
				.then( function () {
					submit.disabled = false;
					submit.removeAttribute( 'aria-busy' );
				} );
		} );
	}

	/* ---------- Back to top ---------- */
	var top = d.querySelector( '[data-bar7-top]' );
	if ( top ) {
		top.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			M().scrollTo( 0 );
			var logo = d.querySelector( '.bar7-header__logo' );
			if ( logo ) { logo.focus( { preventScroll: true } ); }
		} );
	}
}( window, document, window.jQuery ) );

/* Filters: leave empty price fields out of the URL. */
document.addEventListener( 'submit', function ( e ) {
	if ( ! e.target.matches || ! e.target.matches( '.bar7-filters__form' ) ) { return; }
	e.target.querySelectorAll( 'input[type="number"]' ).forEach( function ( input ) {
		if ( input.value === '' ) { input.disabled = true; }
	} );
} );

/* Contact form (WPForms): the form's own confirmation text is empty, so show one when WPForms reports success. */
if ( window.jQuery ) {
	window.jQuery( document ).on( 'wpformsAjaxSubmitSuccess', function ( e, response ) {
		var form = e.target;
		if ( ! form || ( response && response.data && response.data.confirmation ) ) { return; }
		var box = form.parentNode.querySelector( '.bar7-form-done' );
		if ( ! box ) {
			box = document.createElement( 'div' );
			box.className = 'bar7-form-done wpforms-confirmation-container-full';
			box.setAttribute( 'role', 'status' );
			box.setAttribute( 'tabindex', '-1' );
			form.parentNode.insertBefore( box, form );
		}
		box.textContent = ( window.bar7Site && bar7Site.i18n && bar7Site.i18n.messageSent ) || 'Thank you. Your message has been sent.';
		form.reset();
		box.focus();
	} );
}

/* Cookie consent: optional tracking (WooCommerce order attribution) only after "Allow". */
( function ( w, d ) {
	'use strict';
	var NAME = 'rovakivo_consent';
	var banner = d.querySelector( '[data-bar7-consent]' );
	if ( ! banner ) { return; }

	function read() {
		var m = d.cookie.match( new RegExp( '(?:^|; )' + NAME + '=([^;]*)' ) );
		return m ? decodeURIComponent( m[ 1 ] ) : '';
	}
	function setTracking( allow ) {
		if ( w.wc_order_attribution && typeof w.wc_order_attribution.setOrderTracking === 'function' ) {
			w.wc_order_attribution.setOrderTracking( allow );
		}
		if ( ! allow ) {
			d.cookie.split( ';' ).forEach( function ( c ) {
				var n = c.split( '=' )[ 0 ].trim();
				if ( n.indexOf( 'sbjs_' ) === 0 ) {
					d.cookie = n + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';
					d.cookie = n + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; domain=' + w.location.hostname;
					d.cookie = n + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; domain=.' + w.location.hostname.replace( /^www\./, '' );
				}
			} );
		}
	}
	function show( focus ) {
		banner.hidden = false;
		d.body.classList.add( 'has-consent' );
		if ( focus ) {
			var first = banner.querySelector( 'button' );
			if ( first ) { first.focus(); }
		}
	}
	function hide() {
		banner.hidden = true;
		d.body.classList.remove( 'has-consent' );
	}

	var current = read();
	if ( current === 'accepted' ) {
		setTracking( true );
	} else if ( current === 'rejected' ) {
		setTracking( false );
	} else {
		show( false );
	}

	banner.addEventListener( 'click', function ( e ) {
		var btn = e.target.closest( '[data-bar7-consent-choice]' );
		if ( ! btn ) { return; }
		var choice = btn.getAttribute( 'data-bar7-consent-choice' );
		d.cookie = NAME + '=' + choice + '; max-age=' + ( 180 * 24 * 60 * 60 ) + '; path=/; SameSite=Lax' + ( w.location.protocol === 'https:' ? '; Secure' : '' );
		setTracking( choice === 'accepted' );
		hide();
		var live = d.getElementById( 'bar7-live' );
		if ( live ) { live.textContent = choice === 'accepted' ? 'Cookie choice saved: allowed.' : 'Cookie choice saved: essential only.'; }
		var opener = d.querySelector( '[data-bar7-consent-open]' );
		if ( opener && banner.getAttribute( 'data-opened-from' ) === 'footer' ) { opener.focus(); }
		banner.removeAttribute( 'data-opened-from' );
	} );

	d.addEventListener( 'click', function ( e ) {
		if ( e.target.closest( '[data-bar7-consent-open]' ) ) {
			banner.setAttribute( 'data-opened-from', 'footer' );
			show( true );
		}
	} );
}( window, document ) );

/* Product page: variation photo, and the sticky buy bar on small screens. */
( function ( w, d ) {
	'use strict';
	var $ = w.jQuery;
	var gallery = d.querySelector( '[data-bar7-gallery]' );

	// Show the chosen variation's photo (it is one of the gallery thumbnails).
	if ( $ && gallery ) {
		var thumbs = gallery.querySelectorAll( '.bar7-gallery__thumb' );
		var main = gallery.querySelector( '[data-bar7-gallery-main]' );
		$( d.body ).on( 'found_variation', 'form.variations_form', function ( e, variation ) {
			if ( ! variation || ! variation.image_id ) { return; }
			var match = gallery.querySelector( '.bar7-gallery__thumb[data-image-id="' + variation.image_id + '"]' );
			if ( match ) {
				match.click();
			} else if ( main && variation.image && variation.image.src ) {
				bar7WebpSource( main, '' );
				main.src = variation.image.src;
				if ( variation.image.srcset ) { main.srcset = variation.image.srcset; } else { main.removeAttribute( 'srcset' ); }
				if ( variation.image.alt ) { main.alt = variation.image.alt; }
				thumbs.forEach( function ( b ) { b.setAttribute( 'aria-pressed', 'false' ); } );
			}
		} );
		$( d.body ).on( 'reset_data', 'form.variations_form', function () {
			if ( thumbs[ 0 ] && thumbs[ 0 ].getAttribute( 'aria-pressed' ) !== 'true' ) { thumbs[ 0 ].click(); }
		} );
	}

	// Sticky buy bar: on narrow screens, once the real buy area has scrolled up out of view.
	var bar = d.querySelector( '[data-bar7-stickybuy]' );
	var buy = d.querySelector( '.bar7-pdp__buy' );
	if ( ! bar || ! buy ) { return; }
	var btn = bar.querySelector( '[data-bar7-stickybuy-btn]' );
	var form = buy.querySelector( 'form.cart' );
	var realBtn = buy.querySelector( '.single_add_to_cart_button' );
	var type = bar.getAttribute( 'data-type' );
	var narrow = w.matchMedia( '(max-width: 699px)' );
	var buyPassed = false;
	var typing = false;

	function blocked() {
		return d.body.classList.contains( 'bar7-locked' ) || d.body.classList.contains( 'has-consent' );
	}
	function update() {
		var show = narrow.matches && buyPassed && ! typing && ! blocked();
		if ( show !== bar.hidden ) { return; }
		bar.hidden = ! show;
		d.body.classList.toggle( 'has-stickybuy', show );
	}
	function variationReady() {
		return !! realBtn && ! realBtn.classList.contains( 'disabled' );
	}
	function label() {
		if ( type !== 'variable' ) { return; }
		btn.textContent = variationReady() ? btn.getAttribute( 'data-label-add' ) : btn.getAttribute( 'data-label-choose' );
	}
	function goToForm( focusSel ) {
		var target = buy.querySelector( focusSel );
		if ( w.bar7 && w.bar7.scrollTo ) { w.bar7.scrollTo( buy, { offset: -110 } ); } else { buy.scrollIntoView( { block: 'center' } ); }
		if ( target ) { setTimeout( function () { target.focus( { preventScroll: true } ); }, 500 ); }
	}

	var ticking = false;
	function measure() {
		ticking = false;
		// Shown whenever the real buy area is off screen: below it on first load (the photos come first
		// on phones), or above it after scrolling past.
		var r = buy.getBoundingClientRect();
		buyPassed = r.bottom < 0 || r.top > w.innerHeight;
		update();
	}
	function onScroll() {
		if ( ! ticking ) { ticking = true; w.requestAnimationFrame( measure ); }
	}
	w.addEventListener( 'scroll', onScroll, { passive: true } );
	w.addEventListener( 'resize', onScroll, { passive: true } );
	measure();
	if ( narrow.addEventListener ) { narrow.addEventListener( 'change', update ); }
	new MutationObserver( update ).observe( d.body, { attributes: true, attributeFilter: [ 'class' ] } );
	d.addEventListener( 'focusin', function ( e ) {
		typing = !! e.target.closest( 'input:not([type="button"]):not([type="submit"]):not([type="checkbox"]):not([type="radio"]), textarea, select' ) && ! bar.contains( e.target );
		update();
	} );
	d.addEventListener( 'focusout', function () { typing = false; setTimeout( update, 0 ); } );

	if ( $ && type === 'variable' && form ) {
		$( form ).on( 'show_variation hide_variation reset_data', function () {
			setTimeout( function () {
				label();
				var price = form.querySelector( '.woocommerce-variation-price .price' );
				var slot = bar.querySelector( '[data-bar7-stickybuy-price]' );
				if ( slot ) {
					if ( ! slot.hasAttribute( 'data-default' ) ) { slot.setAttribute( 'data-default', slot.innerHTML ); }
					slot.innerHTML = price && variationReady() ? price.innerHTML : slot.getAttribute( 'data-default' );
				}
			}, 0 );
		} );
		label();
	}

	btn.addEventListener( 'click', function () {
		if ( ( type === 'simple' || type === 'variable' ) && variationReady() ) {
			realBtn.click();
		} else if ( type === 'variable' ) {
			goToForm( '.variations select' );
		} else {
			goToForm( 'input.qty, .single_add_to_cart_button' );
		}
	} );
}( window, document ) );

/* Header menu dropdowns: the arrow button opens the section list (also for touch and keyboard); Escape and outside clicks close it. */
( function ( d ) {
	'use strict';
	var toggles = d.querySelectorAll( '.bar7-nav__toggle' );
	if ( ! toggles.length ) { return; }
	function close( btn, focus ) {
		btn.setAttribute( 'aria-expanded', 'false' );
		if ( focus ) { btn.focus(); }
	}
	toggles.forEach( function ( btn ) {
		var item = btn.closest( 'li' );
		btn.addEventListener( 'click', function () {
			var open = btn.getAttribute( 'aria-expanded' ) === 'true';
			toggles.forEach( function ( other ) { if ( other !== btn ) { close( other ); } } );
			btn.setAttribute( 'aria-expanded', String( ! open ) );
		} );
		item.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' && btn.getAttribute( 'aria-expanded' ) === 'true' ) {
				e.stopPropagation();
				close( btn, true );
			}
		} );
		item.addEventListener( 'focusout', function ( e ) {
			if ( ! item.contains( e.relatedTarget ) ) { close( btn ); }
		} );
	} );
	d.addEventListener( 'click', function ( e ) {
		toggles.forEach( function ( btn ) {
			if ( ! btn.closest( 'li' ).contains( e.target ) ) { close( btn ); }
		} );
	} );
}( document ) );
