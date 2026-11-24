// States and flows: empty cart, add/update/remove, coupon, shipping, checkout validation (no order is placed),
// account forms with non-existent users, search empty state, contact/newsletter validation, drawers and keyboard.
const SHOT = 'C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/';
const O = 'http://7bar.local';
const wait = (page, ms) => page.sleep(ms);

export default async ({ page }) => {
	const out = {};
	await page.send('Network.setCookie', { name: 'rovakivo_consent', value: 'rejected', domain: '7bar.local', path: '/' });
	await page.viewport(1440, 900, false);

	// Empty cart
	await page.goto(O + '/cart/');
	out.emptyCart = await page.eval(() => ({ text: (document.querySelector('.cart-empty, .wc-empty-cart-message')?.textContent || '').trim(), back: document.querySelector('.return-to-shop a, a.wc-backward')?.getAttribute('href') }));
	await page.screenshot(SHOT + 'state-empty-cart-1440.png');
	// Checkout with empty cart redirects
	await page.goto(O + '/checkout/');
	out.checkoutEmptyRedirect = await page.eval(() => location.pathname);

	// Mini-cart drawer from a product page, AJAX add from the shop grid
	await page.goto(O + '/shop/');
	out.ajaxAdd = await page.eval(async () => {
		const btn = document.querySelector('.add_to_cart_button.ajax_add_to_cart');
		if (!btn) return 'no ajax button';
		btn.click();
		for (let i = 0; i < 30 && !btn.classList.contains('added'); i++) await new Promise((x) => setTimeout(x, 200));
		await new Promise((x) => setTimeout(x, 700));
		const drawer = document.getElementById('bar7-cartdrawer');
		return { added: btn.classList.contains('added'), drawerOpen: drawer && !drawer.hidden && drawer.getAttribute('aria-hidden') !== 'true', items: drawer?.querySelectorAll('.woocommerce-mini-cart-item, .mini_cart_item').length, focusInDrawer: drawer?.contains(document.activeElement), count: document.querySelector('.bar7-cart-wrap')?.textContent.trim() };
	});
	await page.screenshot(SHOT + 'state-minicart-drawer-1440.png');
	out.drawerEscape = await page.eval(async () => {
		document.activeElement.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape', bubbles: true }));
		await new Promise((x) => setTimeout(x, 700));
		const drawer = document.getElementById('bar7-cartdrawer');
		return { closed: drawer.hidden || drawer.getAttribute('aria-hidden') === 'true' || !drawer.classList.contains('is-open'), focus: document.activeElement.className };
	});

	// Cart: invalid coupon, quantity update, shipping calculator
	await page.goto(O + '/cart/');
	out.cart = await page.eval(() => ({ rows: document.querySelectorAll('.woocommerce-cart-form__cart-item').length, total: document.querySelector('.order-total .amount')?.textContent, shipping: document.querySelector('.woocommerce-shipping-totals td, .shipping td')?.textContent.trim().replace(/\s+/g, ' ').slice(0, 120), calc: !!document.querySelector('.shipping-calculator-button'), coupon: !!document.querySelector('#coupon_code') }));
	await page.eval(() => { document.querySelector('#coupon_code').value = 'NOTAREALCODE'; setTimeout(() => document.querySelector('button[name="apply_coupon"]').click(), 30); return 1; });
	await page.settle(1500);
	await wait(page, 1500);
	out.couponError = await page.eval(() => (document.querySelector('.woocommerce-error, .woocommerce-NoticeGroup .woocommerce-error, .coupon-error-notice, [role="alert"]')?.textContent || '').trim().replace(/\s+/g, ' '));
	await page.screenshot(SHOT + 'state-coupon-error-1440.png');
	await page.eval(() => { const q = document.querySelector('.woocommerce-cart-form input.qty'); q.value = 2; q.dispatchEvent(new Event('change', { bubbles: true })); return 1; });
	await wait(page, 4000);
	await page.settle(500);
	out.qtyUpdate = await page.eval(() => ({ qty: document.querySelector('.woocommerce-cart-form input.qty')?.value, total: document.querySelector('.order-total .amount')?.textContent, notice: (document.querySelector('.woocommerce-message')?.textContent || '').trim() }));

	// Checkout: submit empty (validation only — fields are blank, so no order can be created)
	await page.goto(O + '/checkout/', 2500);
	out.checkout = await page.eval(() => ({
		payment: (document.querySelector('#payment')?.textContent || '').trim().replace(/\s+/g, ' ').slice(0, 200),
		placeOrder: !!document.querySelector('#place_order'),
		terms: !!document.querySelector('#terms'),
		privacyText: (document.querySelector('.woocommerce-privacy-policy-text')?.textContent || '').trim().slice(0, 160),
		countries: document.querySelectorAll('#billing_country option').length,
	}));
	await page.screenshot(SHOT + 'checkout-1440.png');
	await page.eval(() => { setTimeout(() => document.querySelector('#place_order')?.click(), 30); return 1; });
	await wait(page, 5000);
	out.checkoutErrors = await page.eval(() => { const list = [...document.querySelectorAll('.woocommerce-error li, .woocommerce-NoticeGroup-checkout li')].map((l) => l.textContent.trim().replace(/\s+/g, ' ')); return { count: list.length, first: list.slice(0, 8), invalidFields: document.querySelectorAll('.woocommerce-invalid').length, url: location.pathname }; });
	await page.screenshot(SHOT + 'state-checkout-errors-1440.png');

	// Remove the item from the cart and check the empty state again
	await page.goto(O + '/cart/');
	await page.eval(() => { setTimeout(() => document.querySelector('a.remove')?.click(), 30); return 1; });
	await wait(page, 4000);
	await page.settle(500);
	out.afterRemove = await page.eval(() => ({ notice: (document.querySelector('.woocommerce-message')?.textContent || '').trim().replace(/\s+/g, ' ').slice(0, 100), rows: document.querySelectorAll('.woocommerce-cart-form__cart-item').length }));

	// Account: sign-in with empty fields, lost password with an address that is not registered
	await page.goto(O + '/my-account/');
	out.account = await page.eval(() => ({ login: !!document.querySelector('form.woocommerce-form-login'), register: !!document.querySelector('form.woocommerce-form-register'), lost: document.querySelector('.lost_password a')?.getAttribute('href') }));
	await page.eval(() => { setTimeout(() => document.querySelector('button[name="login"]').click(), 30); return 1; });
	await page.settle(1500);
	out.loginEmpty = await page.eval(() => (document.querySelector('.woocommerce-error')?.textContent || '').trim().replace(/\s+/g, ' '));
	await page.goto(O + '/my-account/lost-password/');
	await page.eval(() => { document.querySelector('#user_login').value = 'audit-nobody@example.invalid'; setTimeout(() => document.querySelector('button[name="wc_reset_password"], .woocommerce-ResetPassword button[type="submit"]').click(), 30); return 1; });
	await page.settle(1500);
	out.lostPassword = await page.eval(() => (document.querySelector('.woocommerce-error')?.textContent || '').trim().replace(/\s+/g, ' '));
	await page.screenshot(SHOT + 'state-lost-password-error-1440.png');

	// Search empty state
	await page.goto(O + '/?s=zzqqx&post_type=product');
	out.searchEmpty = await page.eval(() => ({ text: (document.querySelector('main')?.innerText || '').replace(/\s+/g, ' ').slice(0, 260) }));

	// Contact form: empty submit shows field errors (nothing is sent)
	await page.goto(O + '/contact/', 2000);
	out.contactForm = await page.eval(async () => {
		const form = document.querySelector('form.wpforms-form');
		if (!form) return 'no form';
		const fields = [...form.querySelectorAll('input:not([type=hidden]), textarea')].map((f) => ({ type: f.type, label: document.querySelector(`label[for="${f.id}"]`)?.textContent.trim(), required: f.required || f.classList.contains('wpforms-field-required') }));
		form.querySelector('button[type="submit"]').click();
		await new Promise((x) => setTimeout(x, 1200));
		return { fields, errors: [...form.querySelectorAll('.wpforms-error, label.wpforms-error, em.wpforms-error')].map((e) => e.textContent.trim()).filter(Boolean), stillThere: !!document.querySelector('form.wpforms-form') };
	});
	await page.screenshot(SHOT + 'state-contact-errors-1440.png');

	// Newsletter field
	await page.goto(O + '/');
	out.newsletter = await page.eval(() => {
		const f = document.querySelector('form[data-bar7-newsletter], .bar7-newsletter form');
		if (!f) return 'no form';
		const i = f.querySelector('input[type="email"]');
		i.value = 'not-an-email';
		return { type: i.type, required: i.required, label: !!(i.labels && i.labels.length) || !!i.getAttribute('aria-label'), validity: i.checkValidity(), message: i.validationMessage, consentText: (f.textContent || '').replace(/\s+/g, ' ').trim().slice(0, 200) };
	});

	// Mobile menu: open, focus inside, Escape closes, focus returns
	await page.viewport(390, 844, true);
	await page.goto(O + '/');
	out.menu = await page.eval(async () => {
		const b = document.querySelector('.bar7-header__menu');
		b.focus();
		b.click();
		await new Promise((x) => setTimeout(x, 700));
		const d = document.getElementById('bar7-drawer');
		const r = { expanded: b.getAttribute('aria-expanded'), focusIn: d.contains(document.activeElement), links: [...d.querySelectorAll('a')].map((a) => a.textContent.trim()).filter(Boolean).slice(0, 12) };
		document.activeElement.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape', bubbles: true }));
		await new Promise((x) => setTimeout(x, 700));
		r.afterEsc = b.getAttribute('aria-expanded');
		r.focusBack = document.activeElement === b;
		return r;
	});
	// Skip link and landmarks
	out.a11y = await page.eval(() => ({ skip: document.querySelector('a[href="#main"], .bar7-skip, a.skip-link')?.textContent.trim(), main: !!document.querySelector('main#main, main'), lang: document.documentElement.lang, footerTargets: [...document.querySelectorAll('.bar7-footer a, .bar7-footer button')].filter((a) => a.getBoundingClientRect().height < 44 && a.offsetParent).map((a) => a.textContent.trim() + ':' + Math.round(a.getBoundingClientRect().height)).slice(0, 10) }));
	out.errors = page.consoleErrors;
	return out;
};
