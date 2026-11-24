// Performance (local server, cold cache per run) and motion checks.
const O = 'http://7bar.local';
const METRICS = () => new Promise((resolve) => {
	let lcp = 0; let cls = 0;
	new PerformanceObserver((l) => { for (const e of l.getEntries()) lcp = e.startTime; }).observe({ type: 'largest-contentful-paint', buffered: true });
	new PerformanceObserver((l) => { for (const e of l.getEntries()) if (!e.hadRecentInput) cls += e.value; }).observe({ type: 'layout-shift', buffered: true });
	setTimeout(() => {
		const n = performance.getEntriesByType('navigation')[0];
		const res = performance.getEntriesByType('resource');
		const sum = (f) => Math.round(res.filter(f).reduce((a, r) => a + (r.transferSize || 0), 0) / 1024);
		resolve({
			ttfb: Math.round(n.responseStart), dcl: Math.round(n.domContentLoadedEventEnd), load: Math.round(n.loadEventEnd),
			lcp: Math.round(lcp), cls: +cls.toFixed(3), requests: res.length + 1,
			kbTotal: Math.round((n.transferSize || 0) / 1024) + sum(() => true), kbHtml: Math.round((n.transferSize || 0) / 1024),
			kbJs: sum((r) => r.initiatorType === 'script'), kbCss: sum((r) => r.initiatorType === 'link' && /\.css/.test(r.name)), kbImg: sum((r) => r.initiatorType === 'img' || /\.(webp|jpe?g|png|svg)/.test(r.name)), kbFont: sum((r) => /woff2?|fonts\.g/.test(r.name)),
			lcpEl: (() => { try { return performance.getEntriesByType('largest-contentful-paint').slice(-1)[0]?.element?.tagName; } catch { return null; } })(),
		});
	}, 2500);
});

export default async ({ page }) => {
	const out = { perf: {}, motion: {} };
	await page.send('Network.setCookie', { name: 'rovakivo_consent', value: 'rejected', domain: '7bar.local', path: '/' });
	await page.send('Network.setCacheDisabled', { cacheDisabled: true });
	const pages = { home: '/', shop: '/shop/', product: '/product/aturna-condimentum-mattis-pellentesque-nibh-tortor/', contact: '/contact/' };
	for (const [vw, vh, label] of [[390, 844, 'mobile'], [1440, 900, 'desktop']]) {
		await page.viewport(vw, vh, vw < 768);
		for (const [k, p] of Object.entries(pages)) {
			const runs = [];
			for (let i = 0; i < 2; i++) { await page.goto(O + p, 200); runs.push(await page.eval(METRICS)); }
			out.perf[`${k}-${label}`] = runs;
		}
	}
	await page.send('Network.setCacheDisabled', { cacheDisabled: false });

	// Motion on (no reduced-motion preference): every reveal ends visible after scrolling; libraries load once.
	await page.viewport(1440, 900, false);
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'no-preference' }] });
	for (const p of ['/', '/shop/', '/product/aturna-condimentum-mattis-pellentesque-nibh-tortor/', '/about/']) {
		await page.goto(O + p, 1200);
		out.motion['on ' + p] = await page.eval(async () => {
			const r = { motionClass: document.documentElement.classList.contains('bar7-motion'), lenis: !!(window.bar7 && window.bar7.lenis), scripts: { gsap: document.querySelectorAll('script[src*="gsap"]').length, lenis: document.querySelectorAll('script[src*="lenis"]').length, motion: document.querySelectorAll('script[src*="motion.js"]').length }, triggers: window.ScrollTrigger ? ScrollTrigger.getAll().length : null };
			for (let y = 0; y <= document.body.scrollHeight; y += 300) { window.scrollTo(0, y); await new Promise((x) => setTimeout(x, 90)); }
			await new Promise((x) => setTimeout(x, 1500));
			const els = [...document.querySelectorAll('[data-reveal], [data-reveal-item], [data-intro-item], [data-reveal-grid] > *, [data-reveal-media]')];
			r.stuckHidden = els.filter((e) => +getComputedStyle(e).opacity < 0.99 && e.offsetParent).map((e) => e.className.toString().slice(0, 40)).slice(0, 5);
			r.checked = els.length;
			r.triggersAfter = window.ScrollTrigger ? ScrollTrigger.getAll().length : null;
			return r;
		});
	}

	// Reduced motion: no animation, nothing hidden at load, Lenis off.
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'reduce' }] });
	for (const p of ['/', '/product/aturna-condimentum-mattis-pellentesque-nibh-tortor/']) {
		await page.goto(O + p, 1000);
		out.motion['reduced ' + p] = await page.eval(() => {
			const els = [...document.querySelectorAll('[data-reveal], [data-reveal-item], [data-intro-item], [data-reveal-grid] > *, [data-reveal-media]')];
			return { motionClass: document.documentElement.classList.contains('bar7-motion'), lenis: !!(window.bar7 && window.bar7.lenis), hidden: els.filter((e) => +getComputedStyle(e).opacity < 0.99).length, transformed: els.filter((e) => getComputedStyle(e).transform !== 'none').length, checked: els.length };
		});
	}

	// Motion scripts fail to load: content must still be visible.
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'no-preference' }] });
	await page.send('Network.setBlockedURLs', { urls: ['*gsap*', '*ScrollTrigger*', '*lenis*', '*motion.js*'] });
	for (const p of ['/', '/shop/']) {
		await page.goto(O + p, 1500);
		out.motion['blocked ' + p] = await page.eval(() => {
			const els = [...document.querySelectorAll('[data-reveal], [data-reveal-item], [data-intro-item], [data-reveal-grid] > *, [data-reveal-media]')];
			return { motionClass: document.documentElement.classList.contains('bar7-motion'), hidden: els.filter((e) => +getComputedStyle(e).opacity < 0.99).length, checked: els.length };
		});
	}
	await page.send('Network.setBlockedURLs', { urls: [] });
	out.errors = page.consoleErrors.filter((e) => !/ERR_BLOCKED_BY_CLIENT|blocked/i.test(e));
	return out;
};
