const SHOT = 'C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/';
export default async ({ page }) => {
	const out = {};
	await page.viewport(375, 812, true);
	await page.send('Network.setCookie', { name: 'rovakivo_consent', value: 'rejected', domain: '7bar.local', path: '/' });

	// Simple product
	await page.goto('http://7bar.local/product/aturna-condimentum-mattis-pellentesque-nibh-tortor/');
	out.simple = await page.eval(async () => {
		const bar = document.querySelector('[data-bar7-stickybuy]');
		const r = { visibleOnLoad: !bar.hidden };
		const buy = document.querySelector('.bar7-pdp__buy');
		window.scrollTo(0, buy.getBoundingClientRect().bottom + scrollY + 300);
		await new Promise((x) => setTimeout(x, 600));
		r.shown = !bar.hidden;
		r.label = bar.querySelector('button').textContent.trim();
		r.rect = bar.getBoundingClientRect().bottom === innerHeight;
		r.overflow = document.documentElement.scrollWidth > innerWidth;
		return r;
	});
	await page.screenshot(SHOT + 'sticky-cta-simple-375.png');
	await page.eval(() => { setTimeout(() => document.querySelector('[data-bar7-stickybuy-btn]').click(), 50); return 1; });
	await page.settle(2500);
	out.simpleAdd = await page.eval(async () => {
		const count = document.querySelector('.bar7-cart-wrap, .bar7-drawer-count');
		return { drawerOpen: document.getElementById('bar7-cartdrawer')?.getAttribute('aria-hidden') === 'false' || document.body.classList.contains('bar7-locked'), count: count && count.textContent.trim(), barHiddenWhileDrawer: document.querySelector('[data-bar7-stickybuy]')?.hidden, url: location.pathname + location.search, notice: document.querySelector('.woocommerce-message, .woocommerce-error')?.textContent.trim().slice(0, 140) };
	});
	await page.screenshot(SHOT + 'sticky-cta-added-drawer-375.png');

	// Variable product
	await page.goto('http://7bar.local/product/eleifend-donec-pretium-vulputate-sapien-sagittis/');
	out.variable = await page.eval(async () => {
		const bar = document.querySelector('[data-bar7-stickybuy]');
		const btn = bar.querySelector('button');
		const buy = document.querySelector('.bar7-pdp__buy');
		window.scrollTo(0, buy.getBoundingClientRect().bottom + scrollY + 300);
		await new Promise((x) => setTimeout(x, 600));
		const r = { shown: !bar.hidden, label: btn.textContent.trim() };
		btn.click();
		await new Promise((x) => setTimeout(x, 1200));
		r.focus = document.activeElement.id;
		r.hiddenWhileSelect = bar.hidden;
		r.formInView = buy.getBoundingClientRect().top >= 0 && buy.getBoundingClientRect().top < innerHeight;
		const sel = document.querySelectorAll('.variations select');
		jQuery(sel[0]).val('Black').trigger('change');
		jQuery(sel[1]).val('Small').trigger('change');
		await new Promise((x) => setTimeout(x, 700));
		document.activeElement.blur();
		r.mainImg = document.querySelector('[data-bar7-gallery-main]').src.split('/').pop();
		window.scrollTo(0, buy.getBoundingClientRect().bottom + scrollY + 300);
		await new Promise((x) => setTimeout(x, 600));
		r.shown2 = !bar.hidden;
		r.label2 = btn.textContent.trim();
		r.price2 = bar.querySelector('[data-bar7-stickybuy-price]').textContent.trim();
		return r;
	});
	await page.screenshot(SHOT + 'sticky-cta-variable-375.png');
	const before = await page.eval(() => document.querySelector('.bar7-cart-wrap')?.textContent.trim());
	await page.eval(() => { setTimeout(() => document.querySelector('[data-bar7-stickybuy-btn]').click(), 50); return 1; });
	await page.settle(2500);
	out.variableAdd = await page.eval(async () => {
		return { before: '', after: document.querySelector('.bar7-cart-wrap')?.textContent.trim(), url: location.pathname, notice: document.querySelector('.woocommerce-message, .woocommerce-error')?.textContent.trim().slice(0, 120) };
	});

	// Grouped product
	await page.goto('http://7bar.local/product/ornare-arcudui-vivamus-arcu-felis-bibendum/');
	out.grouped = await page.eval(async () => {
		const bar = document.querySelector('[data-bar7-stickybuy]');
		if (!bar) return 'no bar';
		const buy = document.querySelector('.bar7-pdp__buy');
		window.scrollTo(0, buy.getBoundingClientRect().bottom + scrollY + 300);
		await new Promise((x) => setTimeout(x, 600));
		const r = { shown: !bar.hidden, label: bar.querySelector('button').textContent.trim() };
		bar.querySelector('button').click();
		await new Promise((x) => setTimeout(x, 1200));
		r.focus = document.activeElement.className || document.activeElement.tagName;
		return r;
	});

	// Desktop: never shown
	await page.viewport(1440, 900, false);
	await page.goto('http://7bar.local/product/aturna-condimentum-mattis-pellentesque-nibh-tortor/');
	out.desktop = await page.eval(async () => { window.scrollTo(0, 1500); await new Promise((x) => setTimeout(x, 600)); return getComputedStyle(document.querySelector('[data-bar7-stickybuy]')).display; });
	out.variableAdd.before = before;
	out.errors = page.consoleErrors;
	return out;
};
