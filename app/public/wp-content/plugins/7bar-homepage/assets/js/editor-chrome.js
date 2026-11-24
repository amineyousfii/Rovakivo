/*
 * Page editor: puts the real site header above the content and the real footer below it (read-only preview),
 * so the editor looks like the live page. Re-inserted whenever the editor redraws its canvas.
 */
( function ( wp ) {
	'use strict';
	if ( ! wp || ! wp.apiFetch || ! wp.data ) { return; }

	var chrome = null;
	var loading = false;

	function canvasDoc() {
		var frame = document.querySelector( 'iframe[name="editor-canvas"]' );
		if ( frame && frame.contentDocument && frame.contentDocument.querySelector( '.is-root-container' ) ) {
			return frame.contentDocument;
		}
		// Editor shown without an iframe (older layouts).
		return document.querySelector( '.editor-styles-wrapper .is-root-container' ) ? document : null;
	}

	function make( doc, html, where, label ) {
		var box = doc.createElement( 'div' );
		box.className = 'bar7-editor-chrome bar7-editor-chrome--' + where;
		box.setAttribute( 'contenteditable', 'false' );
		box.setAttribute( 'inert', '' );
		box.setAttribute( 'aria-hidden', 'true' );
		box.setAttribute( 'data-bar7-chrome', where );
		box.innerHTML = html;
		var tag = doc.createElement( 'span' );
		tag.className = 'bar7-editor-chrome__label';
		tag.textContent = label;
		box.appendChild( tag );
		return box;
	}

	function place() {
		if ( ! chrome ) { return; }
		var doc = canvasDoc();
		if ( ! doc ) { return; }
		var root = doc.querySelector( '.is-root-container' );
		if ( ! root || ! root.parentNode ) { return; }
		var parent = root.parentNode;
		if ( ! doc.querySelector( '[data-bar7-chrome="top"]' ) ) {
			parent.insertBefore( make( doc, chrome.header, 'top', chrome.headerLabel ), root );
		}
		if ( ! doc.querySelector( '[data-bar7-chrome="bottom"]' ) ) {
			var footer = make( doc, chrome.footer, 'bottom', chrome.footerLabel );
			if ( root.nextSibling ) { parent.insertBefore( footer, root.nextSibling ); } else { parent.appendChild( footer ); }
		}
	}

	function load() {
		if ( loading || chrome ) { return; }
		var editor = wp.data.select( 'core/editor' );
		if ( ! editor || ! editor.getCurrentPostType || editor.getCurrentPostType() !== 'page' ) { return; }
		loading = true;
		wp.apiFetch( { path: '/rovakivo/v1/chrome?post=' + ( editor.getCurrentPostId() || 0 ) } ).then( function ( data ) {
			chrome = data;
			place();
		} ).catch( function () { /* No preview; the editor works as normal. */ } );
	}

	// The canvas is created and recreated by the editor (device previews, mode switches): check regularly and cheaply.
	setInterval( function () {
		if ( ! chrome ) { load(); return; }
		place();
	}, 800 );
}( window.wp ) );
