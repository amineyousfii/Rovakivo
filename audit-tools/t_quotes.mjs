const SHOT = 'C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/';
const clipShot = async (page, file) => {
	const r = await page.eval(async () => { const s = document.querySelector('.bar7-quotes'); s.scrollIntoView({ block: 'start' }); await new Promise((x) => setTimeout(x, 1600)); const b = s.getBoundingClientRect(); return { y: b.top + scrollY, h: b.height, w: document.documentElement.clientWidth }; });
	const shot = await page.send('Page.captureScreenshot', { format: 'png', captureBeyondViewport: true, clip: { x: 0, y: r.y, width: r.w, height: r.h, scale: 1 } });
	(await import('node:fs')).writeFileSync(file, Buffer.from(shot.data, 'base64'));
};
const lum = (rgb) => { const c = rgb.match(/\d+(\.\d+)?/g).slice(0, 3).map((v) => { v = v / 255; return v <= 0.03928 ? v / 12.92 : ((v + 0.055) / 1.055) ** 2.4; }); return 0.2126 * c[0] + 0.7152 * c[1] + 0.0722 * c[2]; };
export default async ({ page }) => {
	const out = { layout: {} };
	await page.send('Network.setCookie', { name: 'rovakivo_consent', value: 'rejected', domain: '7bar.local', path: '/' });
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'no-preference' }] });
	for (const [w, h] of [[1920, 1080], [1440, 900], [1024, 768], [768, 1024], [390, 844], [320, 568]]) {
		await page.viewport(w, h, w < 768);
		await page.goto('http://7bar.local/', 1200);
		out.layout[w] = await page.eval(async () => {
			const s = document.querySelector('.bar7-quotes'); s.scrollIntoView(); await new Promise((x) => setTimeout(x, 1500));
			const cards = [...s.querySelectorAll('.bar7-quote__inner')].map((c) => c.getBoundingClientRect());
			const rows = [...new Set(cards.map((c) => Math.round(c.top)))].length;
			return { cols: cards.filter((c) => Math.round(c.top) === Math.round(cards[0].top)).length, rows, widths: [...new Set(cards.map((c) => Math.round(c.width)))], heightsRow1: cards.filter((c) => Math.round(c.top) === Math.round(cards[0].top)).map((c) => Math.round(c.height)), overflow: document.documentElement.scrollWidth - document.documentElement.clientWidth, prev: s.previousElementSibling?.className, next: s.nextElementSibling?.className };
		});
		if ([1440, 768, 390].includes(w)) await clipShot(page, `${SHOT}testimonials-${w}.png`);
	}
	await page.viewport(1440, 900, false);
	await page.goto('http://7bar.local/', 1200);
	out.details = await page.eval(async (lumSrc) => {
		const lum = eval(lumSrc);
		const s = document.querySelector('.bar7-quotes'); s.scrollIntoView(); await new Promise((x) => setTimeout(x, 1800));
		const ratio = (el, bgEl) => { const a = lum(getComputedStyle(el).color), b = lum(getComputedStyle(bgEl).backgroundColor); return +((Math.max(a, b) + 0.05) / (Math.min(a, b) + 0.05)).toFixed(2); };
		const card = s.querySelector('.bar7-quote__inner');
		return {
			fonts: { h2: getComputedStyle(s.querySelector('h2')).fontFamily.split(',')[0] + ' ' + getComputedStyle(s.querySelector('h2')).fontWeight, body: getComputedStyle(s.querySelector('.bar7-quote__text p')).fontFamily.split(',')[0], name: getComputedStyle(s.querySelector('.bar7-quote__name')).fontFamily.split(',')[0] },
			contrast: { review: ratio(s.querySelector('.bar7-quote__text p'), card), name: ratio(s.querySelector('.bar7-quote__name'), card), sample: ratio(s.querySelector('.bar7-quotes__sample'), s.querySelector('.bar7-quotes__sample')), eyebrow: ratio(s.querySelector('.bar7-eyebrow'), document.querySelector('.bar7-quotes').closest('main') || document.body) },
			starStroke: getComputedStyle(s.querySelector('.bar7-star.is-on')).stroke, emptyStar: s.querySelectorAll('.bar7-star:not(.is-on)').length,
			ratingText: [...s.querySelectorAll('.bar7-quote__rating .screen-reader-text')].map((e) => e.textContent),
			starsHidden: [...s.querySelectorAll('.bar7-quote__stars')].every((e) => e.getAttribute('aria-hidden') === 'true'),
			avatars: [...s.querySelectorAll('.bar7-quote__avatar img')].map((i) => i.src.split('/').pop() + ' ' + (i.complete && i.naturalWidth > 0 ? 'loaded' : 'NOT')),
			avatarHidden: [...s.querySelectorAll('.bar7-quote__avatar')].every((a) => a.getAttribute('aria-hidden') === 'true'),
			sampleLabel: s.querySelector('.bar7-quotes__sample')?.textContent,
			opacity: [...s.querySelectorAll('[data-reveal-item], [data-reveal]')].map((e) => getComputedStyle(e).opacity),
			a11yText: [...s.querySelectorAll('.bar7-quote')].map((li) => li.innerText.replace(/\s+/g, ' ').slice(0, 60)),
		};
	}, lum.toString());
	// Stagger: record each card's opacity over time when the section scrolls into view
	await page.goto('http://7bar.local/', 1200);
	out.stagger = await page.eval(async () => {
		const items = [...document.querySelectorAll('.bar7-quote')];
		const before = items.map((e) => getComputedStyle(e).opacity);
		document.querySelector('.bar7-quotes').scrollIntoView();
		const samples = [];
		for (let i = 0; i < 8; i++) { await new Promise((x) => setTimeout(x, 90)); samples.push(items.map((e) => (+getComputedStyle(e).opacity).toFixed(2) + '/' + (getComputedStyle(e).transform.split(',')[5] || '0').trim().replace(')', ''))); }
		return { before, samples };
	});
	// Initials fallback when an avatar fails
	out.fallback = await page.eval(async () => { const img = document.querySelector('.bar7-quote__avatar img'); img.src = '/missing-avatar.svg'; for (let i = 0; i < 40 && document.querySelector('.bar7-quote__avatar img'); i++) await new Promise((x) => setTimeout(x, 200)); const a = document.querySelector('.bar7-quote__avatar'); return { imgGone: !a.querySelector('img'), initials: a.querySelector('.bar7-quote__initials').textContent, visible: getComputedStyle(a.querySelector('.bar7-quote__initials')).visibility }; });
	await clipShot(page, `${SHOT}testimonials-initials-fallback-1440.png`);
	// Reduced motion and blocked motion scripts
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'reduce' }] });
	await page.goto('http://7bar.local/', 1000);
	out.reduced = await page.eval(() => [...document.querySelectorAll('.bar7-quotes [data-reveal], .bar7-quote')].map((e) => getComputedStyle(e).opacity + ' ' + getComputedStyle(e).transform));
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'no-preference' }] });
	await page.send('Network.setBlockedURLs', { urls: ['*gsap*', '*ScrollTrigger*', '*lenis*', '*motion.js*'] });
	await page.goto('http://7bar.local/', 1500);
	out.blocked = await page.eval(() => [...document.querySelectorAll('.bar7-quotes [data-reveal], .bar7-quote')].map((e) => getComputedStyle(e).opacity));
	out.errors = page.consoleErrors.filter((e) => !/blocked|missing-avatar|ppc|PayPal/i.test(e));
	return out;
};
