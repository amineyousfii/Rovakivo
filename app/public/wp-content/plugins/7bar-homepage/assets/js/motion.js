/**
 * 7bar shared motion system: GSAP 3.15 + ScrollTrigger + Lenis 1.3.
 *
 * - One Lenis instance per page, driven by GSAP's ticker (no second rAF loop).
 * - Reveal patterns are declared in markup:
 *     [data-intro] … [data-intro-item]   short page introduction, on load
 *     [data-intro-media]                 hero/category/product photo, on load
 *     [data-reveal]                      a block that fades up once on scroll
 *     [data-reveal-group] … [data-reveal-item]  related items, staggered once
 *     [data-reveal-grid] > *             product grids, staggered once (never replayed)
 *     [data-reveal-media]                a large photo, once
 *     [data-reveal-sale]                 the homepage sale banner (its one orchestrated moment)
 * - Reduced motion: no Lenis, no reveals, native scrolling. Add ?motion to a URL to force motion on.
 * - Content never depends on this file: elements only start hidden once the inline flag in <head>
 *   has run, and that flag is removed at DOMContentLoaded if this file never reports in.
 *
 * Public API on window.bar7: tokens, reduced, lenis, refresh(), scrollTo(target), lock(), unlock().
 */
( function ( w, d ) {
	'use strict';

	var html = d.documentElement;
	var gsap = w.gsap;
	var ST = w.ScrollTrigger;
	var LenisCtor = w.Lenis;
	var cfg = w.bar7Motion || {};
	var forced = /[?&]motion\b/.test( w.location.search );
	var reduced = !! ( w.matchMedia && w.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) && ! forced;

	/* Motion tokens (CSS mirrors the fast/base durations as --dur-fast / --dur-base). */
	var T = {
		dur: { fast: 0.2, base: 0.5, reveal: 0.65, media: 0.9, drawerIn: 0.32, drawerOut: 0.22 },
		ease: 'power3.out',
		easeSoft: 'power2.out',
		easeIn: 'power2.in',
		y: 18,
		stagger: 0.07,
		gridStagger: 0.06,
		mediaScale: 1.04,
		start: 'top 88%'
	};

	var api = {
		tokens: T,
		reduced: reduced,
		lenis: null,
		refresh: function () {},
		scrollTo: function ( target ) { nativeScrollTo( target ); },
		lock: function () { d.body.classList.add( 'bar7-locked' ); },
		unlock: function () { d.body.classList.remove( 'bar7-locked' ); }
	};
	w.bar7 = api;

	function headerOffset() {
		var h = d.querySelector( '[data-bar7-header]' );
		var bar = d.getElementById( 'wpadminbar' );
		return ( h ? h.offsetHeight : 0 ) + ( bar && w.getComputedStyle( bar ).position === 'fixed' ? bar.offsetHeight : 0 ) + 16;
	}

	function nativeScrollTo( target ) {
		var top = typeof target === 'number' ? target : target.getBoundingClientRect().top + w.scrollY - headerOffset();
		w.scrollTo( { top: Math.max( 0, top ), behavior: reduced ? 'auto' : 'smooth' } );
	}

	/* Sticky header: a quiet shadow once the page has scrolled (no layout change). */
	var header = d.querySelector( '[data-bar7-header]' );
	if ( header ) {
		var ticking = false;
		var onScroll = function () {
			if ( ticking ) { return; }
			ticking = true;
			w.requestAnimationFrame( function () {
				header.classList.toggle( 'is-scrolled', w.scrollY > 8 );
				ticking = false;
			} );
		};
		w.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
	}

	if ( ! gsap ) {
		html.classList.remove( 'bar7-motion' );
		w.bar7MotionReady = true;
		return;
	}

	if ( ST ) {
		gsap.registerPlugin( ST );
	}
	gsap.defaults( { ease: T.ease, duration: T.dur.base } );

	/* ---------- Smooth scrolling ---------- */
	var lenis = null;
	if ( ! reduced && cfg.smoothScroll && LenisCtor ) {
		lenis = new LenisCtor( { autoRaf: false, duration: 1.1, smoothWheel: true, anchors: false } );
		if ( ST ) {
			lenis.on( 'scroll', ST.update );
		}
		gsap.ticker.add( function ( time ) { lenis.raf( time * 1000 ); } );
		gsap.ticker.lagSmoothing( 0 );
		api.lenis = lenis;
	}

	api.scrollTo = function ( target ) {
		if ( lenis ) {
			lenis.scrollTo( target, { offset: typeof target === 'number' ? 0 : -headerOffset(), duration: 1 } );
		} else {
			nativeScrollTo( target );
		}
	};
	api.lock = function () {
		d.body.classList.add( 'bar7-locked' );
		if ( lenis ) { lenis.stop(); }
	};
	api.unlock = function () {
		d.body.classList.remove( 'bar7-locked' );
		if ( lenis ) { lenis.start(); }
	};

	var refreshTimer = 0;
	api.refresh = function () {
		w.clearTimeout( refreshTimer );
		refreshTimer = w.setTimeout( function () {
			if ( lenis ) { lenis.resize(); }
			if ( ST ) { ST.refresh(); }
		}, 120 );
	};
	w.addEventListener( 'load', api.refresh );
	if ( d.fonts && d.fonts.ready ) { d.fonts.ready.then( api.refresh ); }
	if ( w.jQuery ) {
		w.jQuery( d.body ).on( 'updated_cart_totals updated_checkout wc_fragments_refreshed found_variation reset_data updated_wc_div', api.refresh );
	}

	/* Same-page anchor links: scroll with the header offset, update history, move focus. */
	d.addEventListener( 'click', function ( e ) {
		var a = e.target.closest && e.target.closest( 'a[href*="#"]' );
		if ( ! a || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || a.hasAttribute( 'data-bar7-top' ) ) { return; }
		var url = new URL( a.href, w.location.href );
		if ( url.pathname !== w.location.pathname || url.search !== w.location.search || ! url.hash || url.hash === '#' ) { return; }
		var target = d.getElementById( decodeURIComponent( url.hash.slice( 1 ) ) );
		if ( ! target ) { return; }
		e.preventDefault();
		api.scrollTo( target );
		if ( w.history && w.history.pushState ) { w.history.pushState( null, '', url.hash ); }
		if ( ! target.hasAttribute( 'tabindex' ) && ! /^(A|BUTTON|INPUT|SELECT|TEXTAREA)$/.test( target.tagName ) ) {
			target.setAttribute( 'tabindex', '-1' );
		}
		target.focus( { preventScroll: true } );
	} );

	/* ---------- Reveals ---------- */
	if ( reduced ) {
		html.classList.remove( 'bar7-motion' );
		w.bar7MotionReady = true;
		return;
	}

	var shown = function ( els ) { gsap.set( els, { opacity: 1, clearProps: 'transform' } ); };
	var fadeUp = function ( els, extra ) {
		return gsap.fromTo(
			els,
			{ opacity: 0, y: T.y },
			Object.assign( { opacity: 1, y: 0, duration: T.dur.reveal, stagger: T.stagger, overwrite: true, clearProps: 'transform' }, extra || {} )
		);
	};

	// 1. Page introductions: title, introduction and key controls, in sequence.
	d.querySelectorAll( '[data-intro]' ).forEach( function ( group ) {
		var items = Array.prototype.filter.call( group.querySelectorAll( '[data-intro-item]' ), function ( el ) {
			return el.closest( '[data-intro]' ) === group;
		} );
		if ( items.length ) {
			fadeUp( items, { delay: 0.05 } );
		}
	} );

	// 2. Introductory photography: a gentle settle from a slight scale.
	d.querySelectorAll( '[data-intro-media]' ).forEach( function ( el ) {
		// Variant: the photograph rises into its frame (clip reveal) while the image settles inside it.
		if ( el.classList.contains( 'is-media-clip' ) ) {
			var img = el.querySelector( 'img' );
			gsap.set( el, { opacity: 1 } );
			gsap.fromTo( el, { clipPath: 'inset(100% 0% 0% 0%)' }, { clipPath: 'inset(0% 0% 0% 0%)', duration: 1.15, delay: 0.12, ease: 'power3.inOut', clearProps: 'clipPath' } );
			if ( img ) {
				gsap.fromTo( img, { scale: 1.12 }, { scale: 1, duration: 1.6, delay: 0.12, ease: T.easeSoft, clearProps: 'transform' } );
			}
			return;
		}
		var target = el;
		if ( el.tagName === 'PICTURE' ) {
			// Transforming a <picture> would re-anchor its absolutely positioned image; animate the <img>.
			gsap.set( el, { opacity: 1 } );
			target = el.querySelector( 'img' );
		}
		gsap.fromTo( target, { opacity: 0, scale: T.mediaScale }, { opacity: 1, scale: 1, duration: T.dur.media, ease: T.easeSoft, clearProps: 'transform' } );
	} );

	// Anything never hidden by an intro (e.g. intro items outside a group) is shown.
	d.querySelectorAll( '[data-intro-item]' ).forEach( function ( el ) {
		if ( ! el.closest( '[data-intro]' ) ) { shown( el ); }
	} );

	var pending = [];
	var track = function ( els ) { pending = pending.concat( Array.prototype.slice.call( els ) ); };

	if ( ! ST ) {
		shown( d.querySelectorAll( '[data-reveal], [data-reveal-item], [data-reveal-media], [data-reveal-sale], [data-reveal-grid] > *' ) );
		w.bar7MotionReady = true;
		return;
	}

	// 3. Single blocks.
	var blocks = d.querySelectorAll( '[data-reveal]' );
	track( blocks );
	ST.batch( blocks, { start: T.start, once: true, onEnter: function ( batch ) { fadeUp( batch, { stagger: 0.08 } ); } } );

	// 4. Groups of related items, staggered together.
	d.querySelectorAll( '[data-reveal-group]' ).forEach( function ( group ) {
		var items = group.querySelectorAll( '[data-reveal-item]' );
		if ( ! items.length ) { return; }
		track( items );
		ST.create( { trigger: group, start: T.start, once: true, onEnter: function () { fadeUp( items ); } } );
	} );

	// 5. Product grids: a restrained stagger the first time each row appears, never replayed.
	d.querySelectorAll( '[data-reveal-grid]' ).forEach( function ( grid ) {
		var cards = grid.children;
		track( cards );
		ST.batch( cards, {
			start: 'top 92%',
			once: true,
			interval: 0.08,
			onEnter: function ( batch ) { fadeUp( batch, { stagger: T.gridStagger, duration: T.dur.base } ); }
		} );
	} );

	// 6. Large photographs.
	d.querySelectorAll( '[data-reveal-media]' ).forEach( function ( el ) {
		track( [ el ] );
		ST.create( {
			trigger: el,
			start: 'top 90%',
			once: true,
			onEnter: function () {
				gsap.fromTo( el, { opacity: 0, scale: 1.02 }, { opacity: 1, scale: 1, duration: T.dur.media, ease: T.easeSoft, clearProps: 'transform' } );
			}
		} );
	} );

	// 7. The sale banner: panel, then the two photos settle, then the stamp lands.
	d.querySelectorAll( '[data-reveal-sale]' ).forEach( function ( banner ) {
		track( [ banner ] );
		var copy = banner.querySelectorAll( '.bar7-sale__copy > *' );
		var photos = banner.querySelectorAll( '.bar7-sale__photo' );
		var stamp = banner.querySelector( '.bar7-sale__stamp' );
		gsap.set( copy, { opacity: 0, y: T.y } );
		gsap.set( photos, { opacity: 0, y: 28 } );
		if ( stamp ) { gsap.set( stamp, { opacity: 0, scale: 0.6 } ); }
		ST.create( {
			trigger: banner,
			start: 'top 80%',
			once: true,
			onEnter: function () {
				var tl = gsap.timeline();
				tl.to( banner, { opacity: 1, duration: 0.4 } )
					.to( copy, { opacity: 1, y: 0, stagger: T.stagger, duration: T.dur.reveal, clearProps: 'transform' }, '-=0.15' )
					.to( photos, { opacity: 1, y: 0, stagger: 0.12, duration: 0.7 }, '<0.1' );
				if ( stamp ) {
					tl.to( stamp, { opacity: 1, scale: 1, duration: 0.5, ease: 'back.out(1.7)' }, '-=0.25' );
				}
			}
		} );
	} );

	// 8. About page, "Where does your day end?": scroll-linked typography on wide screens only.
	//    The heading fills from grey to ink and eases right; a large outline of the same words (decorative,
	//    hidden from assistive technology) drifts behind the options and fades. Phones get the plain layout.
	var choose = d.querySelector( '.bar7-choose' );
	var chooseTitle = choose && choose.querySelector( '.bar7-choose__title' );
	if ( choose && chooseTitle && gsap.matchMedia ) {
		gsap.matchMedia().add( '(min-width: 900px)', function () {
			var ghost = d.createElement( 'span' );
			ghost.className = 'bar7-choose__ghost';
			ghost.setAttribute( 'aria-hidden', 'true' );
			ghost.textContent = chooseTitle.textContent.replace( /\s+/g, ' ' ).trim();
			choose.insertBefore( ghost, choose.firstChild );
			choose.classList.add( 'is-scrubbed' );
			var width = function () { return choose.offsetWidth; };
			var tl = gsap.timeline( {
				scrollTrigger: { trigger: choose, start: 'top 80%', end: 'bottom 35%', scrub: 0.8, invalidateOnRefresh: true }
			} );
			tl.fromTo( chooseTitle, { '--fill': '0%' }, { '--fill': '100%', ease: 'none', duration: 0.55 }, 0 )
				.fromTo( chooseTitle, { x: 0 }, { x: function () { return Math.min( 48, w.innerWidth * 0.03 ); }, ease: 'none', duration: 1 }, 0 )
				.fromTo( ghost, { x: function () { return -0.2 * width(); }, opacity: 0, scale: 0.94 }, { x: function () { return 0.18 * width(); }, opacity: 1, scale: 1, ease: 'none', duration: 0.6 }, 0 )
				.to( ghost, { x: function () { return 0.42 * width(); }, opacity: 0, ease: 'none', duration: 0.4 }, 0.6 );
			return function () {
				ghost.remove();
				choose.classList.remove( 'is-scrubbed' );
				gsap.set( chooseTitle, { clearProps: 'transform,--fill' } );
			};
		} );
	}

	// Safety net: whatever has not revealed by the time the page bottom is reached is shown.
	ST.create( {
		trigger: d.body,
		start: 'bottom bottom+=2',
		once: true,
		onEnter: function () {
			var left = pending.filter( function ( el ) { return w.getComputedStyle( el ).opacity === '0'; } );
			if ( left.length ) { fadeUp( left, { stagger: 0.03 } ); }
		}
	} );

	w.bar7MotionReady = true;
}( window, document ) );
