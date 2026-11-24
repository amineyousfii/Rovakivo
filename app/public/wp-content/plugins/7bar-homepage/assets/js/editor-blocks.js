/* Rovakivo blocks in the editor: each one shows the same server-rendered markup as the website. */
( function ( wp ) {
	'use strict';
	if ( ! wp || ! wp.blocks || ! wp.serverSideRender ) { return; }
	var el = wp.element.createElement;
	var SSR = wp.serverSideRender;
	var be = wp.blockEditor;
	var c = wp.components;

	var blocks = {
		'rovakivo/breadcrumbs': {},
		'rovakivo/product-count': {},
		'rovakivo/collection-tiles': { section: { type: 'boolean', default: true }, headingLevel: { type: 'number', default: 2 } },
		'rovakivo/newsletter': {},
		'rovakivo/support-links': {},
		'rovakivo/product-grid': { ids: { type: 'string', default: '' }, filterable: { type: 'boolean', default: false } },
		'rovakivo/product-tabs': {},
		'rovakivo/sale-banner': {},
		'rovakivo/testimonials': {},
		'rovakivo/contact-form': { formId: { type: 'number', default: 506 } },
		'rovakivo/help-links': {},
		'rovakivo/product-catalog': {},
		'rovakivo/account-title': {}
	};

	var help = {
		'rovakivo/sale-banner': 'Shown only while products are on sale, with the live count.',
		'rovakivo/testimonials': 'Edit the cards in Appearance → Homepage testimonials.',
		'rovakivo/product-tabs': 'Filters the product grid on this page that has “Category filter” switched on.',
		'rovakivo/newsletter': 'Connected to the MailPoet list.',
		'rovakivo/account-title': 'Changes by itself: “Sign in” or “Reset your password” for visitors, and the section name (My account, Orders, Addresses…) for signed-in customers.',
		'rovakivo/product-catalog': 'The live product list with filters and sorting. Products are edited in Products; how many show per page is set in WooCommerce. On the Sale page it lists only products that have a sale price.'
	};

	function settings( name, props ) {
		var a = props.attributes;
		var set = props.setAttributes;
		var fields = [];
		if ( name === 'rovakivo/product-grid' ) {
			fields.push( el( c.TextControl, { key: 'ids', label: 'Product IDs', help: 'Comma-separated, in display order (Products → hover a product to see its ID).', value: a.ids, onChange: function ( v ) { set( { ids: v } ); }, __nextHasNoMarginBottom: true } ) );
			fields.push( el( c.ToggleControl, { key: 'f', label: 'Category filter', help: 'Lets the “All / Casual / Travel” buttons filter this grid.', checked: !! a.filterable, onChange: function ( v ) { set( { filterable: v } ); }, __nextHasNoMarginBottom: true } ) );
		}
		if ( name === 'rovakivo/contact-form' ) {
			fields.push( el( c.TextControl, { key: 'form', type: 'number', label: 'WPForms form ID', value: a.formId, onChange: function ( v ) { set( { formId: parseInt( v, 10 ) || 0 } ); }, __nextHasNoMarginBottom: true } ) );
		}
		if ( help[ name ] ) {
			fields.push( el( 'p', { key: 'help' }, help[ name ] ) );
		}
		if ( ! fields.length ) { return null; }
		return el( be.InspectorControls, { key: 'inspector' }, el( c.PanelBody, { title: 'Settings' }, fields ) );
	}

	Object.keys( blocks ).forEach( function ( name ) {
		var server = wp.blocks.getBlockType( name ) || {};
		wp.blocks.registerBlockType( name, {
			apiVersion: 3,
			title: server.title || name,
			category: 'design',
			icon: 'store',
			attributes: blocks[ name ],
			supports: { html: false, reusable: false },
			edit: function ( props ) {
				return [
					settings( name, props ),
					el( 'div', be.useBlockProps( { key: 'preview', className: 'bar7-ssr' } ),
						el( SSR, { block: name, attributes: props.attributes, skipBlockSupportAttributes: true } ) )
				];
			},
			save: function () { return null; }
		} );
	} );
}( window.wp ) );

/* WooCommerce cart/checkout shortcodes: show a live preview (the editor's own current cart) instead of the bare code.
   The shortcode itself stays in the page; select the block to see it. */
( function ( wp ) {
	'use strict';
	if ( ! wp || ! wp.hooks || ! wp.compose || ! wp.serverSideRender ) { return; }
	var el = wp.element.createElement;
	var SSR = wp.serverSideRender;
	wp.hooks.addFilter( 'editor.BlockEdit', 'rovakivo/woo-shortcode-preview', wp.compose.createHigherOrderComponent( function ( BlockEdit ) {
		return function ( props ) {
			var m = props.name === 'core/shortcode' && /\[woocommerce_(cart|checkout|my_account)\b/.exec( props.attributes.text || '' );
			if ( ! m ) { return el( BlockEdit, props ); }
			var preview = el( 'div', { key: 'preview', className: 'bar7-ssr bar7-woo-preview' },
				el( SSR, { block: 'rovakivo/woo-preview', attributes: { kind: m[ 1 ] } } ),
				el( 'p', { className: 'bar7-woo-preview__note' }, {
					cart: 'Preview of your own current cart. Visitors see their own cart here.',
					checkout: 'Preview of the checkout form. Visitors see their own cart and details here.',
					my_account: 'Preview of your own account. Signed-out visitors see the sign-in form here; customers see their orders, addresses and details.'
				}[ m[ 1 ] ] )
			);
			return props.isSelected ? [ preview, el( BlockEdit, Object.assign( { key: 'code' }, props ) ) ] : preview;
		};
	}, 'withRovakivoWooPreview' ) );
}( window.wp ) );
