export default async ({ page }) => {
	const out = {};
	await page.send('Network.setCookie', { name: 'rovakivo_consent', value: 'rejected', domain: '7bar.local', path: '/' });
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'no-preference' }] });
	await page.send('Network.setCacheDisabled', { cacheDisabled: true });
	await page.viewport(1440, 900, false);
	await page.goto('http://7bar.local/', 2500);
	out.home = await page.eval(async () => {
		const r = {};
		r.heroImgs = performance.getEntriesByType('resource').filter((e) => /hero/.test(e.name)).map((e) => e.name.split('/').pop());
		r.heroCurrent = document.querySelector('.bar7-hero__media img').currentSrc.split('/').pop();
		r.heroAttrs = ['fetchpriority', 'loading', 'sizes'].map((a) => a + '=' + document.querySelector('.bar7-hero__media img').getAttribute(a)).join(' ');
		r.motionOn = document.documentElement.classList.contains('bar7-motion') && !!window.bar7MotionReady;
		r.categoriesTarget = (() => { const s = document.getElementById('bar7-categories'); return s && s.getAttribute('tabindex') + ' ' + s.getAttribute('aria-labelledby'); })();
		r.heroLink = document.querySelector('.bar7-hero__actions .bar7-link')?.getAttribute('href');
		const tabs = document.querySelector('[data-bar7-tabs]');
		r.tabsVisible = tabs && !tabs.hidden;
		tabs.querySelector('[data-filter="travel"]').click();
		await new Promise((x) => setTimeout(x, 600));
		r.afterTravel = [...document.querySelectorAll('[data-bar7-filter-grid] .bar7-card')].filter((c) => !c.hidden).length + ' of ' + document.querySelectorAll('[data-bar7-filter-grid] .bar7-card').length;
		tabs.querySelector('[data-filter=""]').click();
		for (let y = 0; y <= document.body.scrollHeight; y += 300) { window.scrollTo(0, y); await new Promise((x) => setTimeout(x, 80)); }
		await new Promise((x) => setTimeout(x, 1500));
		r.stuckHidden = [...document.querySelectorAll('[data-reveal], [data-reveal-item], [data-intro-item], [data-intro-media], [data-reveal-grid] > *, [data-reveal-sale]')].filter((e) => +getComputedStyle(e).opacity < 0.99 && e.offsetParent).length;
		r.cardsAddToCart = document.querySelectorAll('.bar7-grid .add_to_cart_button').length;
		return r;
	});
	await page.goto('http://7bar.local/contact/', 2500);
	out.contact = await page.eval(async () => {
		const f = document.querySelector('form.wpforms-form');
		const r = { form: !!f, fields: f ? f.querySelectorAll('input:not([type=hidden]), textarea').length : 0, landmarks: [...document.querySelectorAll('main [aria-labelledby]')].map((e) => e.tagName + '#' + e.getAttribute('aria-labelledby')).join(' ') };
		f.querySelector('button[type="submit"]').click();
		await new Promise((x) => setTimeout(x, 1200));
		r.validation = f.querySelectorAll('.wpforms-error').length;
		return r;
	});
	out.errors = page.consoleErrors.filter((e) => !/ppc|PayPal/i.test(e));
	return out;
};
