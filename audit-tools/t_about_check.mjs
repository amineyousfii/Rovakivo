// About redesign checks: load sequence, overlap, anchor, sticky heading, contrast, focus, hover, reduced motion,
// blocked scripts and phone layout.
const lum = (rgb) => { const c = rgb.match(/\d+(\.\d+)?/g).slice(0, 3).map((v) => { v = v / 255; return v <= 0.03928 ? v / 12.92 : ((v + 0.055) / 1.055) ** 2.4; }); return 0.2126 * c[0] + 0.7152 * c[1] + 0.0722 * c[2]; };
const ratio = (a, b) => +((Math.max(lum(a), lum(b)) + 0.05) / (Math.min(lum(a), lum(b)) + 0.05)).toFixed(1);

export default async ({ page }) => {
	const out = {};
	await page.send('Network.setCookie', { name: 'rovakivo_consent', value: 'rejected', domain: '7bar.local', path: '/' });
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'no-preference' }] });
	await page.viewport(1440, 900, false);
	await page.goto('http://7bar.local/about/', 300);

	out.sequence = await page.eval(async () => {
		const els = { crumbs: '.bar7-aboutopen .bar7-crumbs', title: '.bar7-aboutopen__title', lede: '.bar7-aboutopen__lede', actions: '.bar7-aboutopen__actions', photo: '.bar7-aboutopen__media' };
		const samples = [];
		for (let i = 0; i < 16; i++) {
			samples.push(Object.entries(els).map(([k, s]) => k + ':' + (+getComputedStyle(document.querySelector(s)).opacity).toFixed(2)).join(' '));
			await new Promise((x) => setTimeout(x, 110));
		}
		return samples.filter((s, i, a) => a.indexOf(s) === i).slice(0, 9);
	});
	out.loaded = await page.eval(() => {
		const i = document.querySelector('.bar7-aboutopen__media img');
		return { motion: document.documentElement.classList.contains('bar7-motion'), img: i.currentSrc.split('/').pop(), fetchpriority: i.getAttribute('fetchpriority'), loading: i.getAttribute('loading'), sizes: i.getAttribute('sizes') };
	});
	out.overlap = await page.eval(() => {
		const t = document.querySelector('.bar7-aboutopen__title').getBoundingClientRect();
		const m = document.querySelector('.bar7-aboutopen__media').getBoundingClientRect();
		return { overlapPx: Math.round(t.right - m.left), ofPhotoWidth: Math.round(((t.right - m.left) / m.width) * 100) + '%' };
	});
	out.anchor = await page.eval(async () => {
		const a = [...document.querySelectorAll('.bar7-aboutopen__actions a')].find((x) => x.textContent.includes('How to choose'));
		a.click();
		await new Promise((x) => setTimeout(x, 1800));
		const sec = document.getElementById('choose');
		return { href: a.getAttribute('href'), sectionTop: Math.round(sec.getBoundingClientRect().top), focused: document.activeElement === sec, labelledBy: sec.getAttribute('aria-labelledby') };
	});
	out.sticky = await page.eval(async () => {
		const head = document.querySelector('.bar7-choose__head');
		const list = document.querySelector('.bar7-choose__list');
		window.scrollTo(0, list.getBoundingClientRect().top + scrollY - 140);
		await new Promise((x) => setTimeout(x, 500));
		const a = Math.round(head.getBoundingClientRect().top);
		window.scrollBy(0, 150);
		await new Promise((x) => setTimeout(x, 500));
		return { topBefore: a, topAfter: Math.round(head.getBoundingClientRect().top), position: getComputedStyle(head).position };
	});
	const colors = await page.eval(() => {
		const c = (s) => getComputedStyle(document.querySelector(s)).color;
		return { lede: c('.bar7-aboutopen__lede'), title: c('.bar7-aboutopen__title'), answer: c('.bar7-choose__item p'), intro: c('.bar7-choose__intro') };
	});
	out.contrast = { lede: ratio(colors.lede, 'rgb(245,245,243)'), titleOnFog: ratio(colors.title, 'rgb(226,226,224)'), answers: ratio(colors.answer, 'rgb(255,255,255)'), intro: ratio(colors.intro, 'rgb(255,255,255)') };
	out.focus = await page.eval(() => [...document.querySelectorAll('.bar7-aboutopen__actions a, .bar7-choose__item a')].map((a) => { a.focus({ focusVisible: true }); const s = getComputedStyle(a); return a.textContent.trim() + ': ' + s.outlineStyle + ' ' + s.outlineWidth + ' ' + s.outlineColor; }));
	const lp = await page.eval(() => { const a = document.querySelector('.bar7-choose__item a'); a.blur(); a.scrollIntoView({ block: 'center' }); const r = a.getBoundingClientRect(); return { x: r.x + 10, y: r.y + r.height / 2, before: getComputedStyle(a).textDecorationThickness }; });
	await page.send('Input.dispatchMouseEvent', { type: 'mouseMoved', x: lp.x, y: lp.y });
	await page.sleep(300);
	out.hover = { before: lp.before, after: await page.eval(() => getComputedStyle(document.querySelector('.bar7-choose__item a')).textDecorationThickness) };

	const sel = '.bar7-aboutopen [data-intro-item], .bar7-aboutopen [data-intro-media], .bar7-choose [data-reveal], .bar7-choose [data-reveal-item]';
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'reduce' }] });
	await page.goto('http://7bar.local/about/', 1200);
	out.reduced = await page.eval((s) => [...document.querySelectorAll(s)].map((e) => getComputedStyle(e).opacity + (getComputedStyle(e).transform === 'none' ? '' : '(moved)')).join(','), sel);
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'no-preference' }] });
	await page.send('Network.setBlockedURLs', { urls: ['*gsap*', '*ScrollTrigger*', '*lenis*', '*motion.js*'] });
	await page.goto('http://7bar.local/about/', 1500);
	out.scriptsBlocked = await page.eval((s) => [...document.querySelectorAll(s)].map((e) => getComputedStyle(e).opacity).join(','), sel);
	await page.send('Network.setBlockedURLs', { urls: [] });

	await page.viewport(390, 844, true);
	await page.goto('http://7bar.local/about/', 1500);
	out.phone = await page.eval(() => {
		const t = document.querySelector('.bar7-aboutopen__title');
		return {
			order: [...document.querySelectorAll('.bar7-aboutopen .bar7-crumbs, .bar7-aboutopen h1, .bar7-aboutopen__lede, .bar7-aboutopen__actions, .bar7-aboutopen__media, .bar7-choose h2, .bar7-choose h3')]
				.sort((a, b) => a.getBoundingClientRect().top - b.getBoundingClientRect().top)
				.map((e) => e.tagName.toLowerCase() + (e.textContent.trim() ? ':' + e.textContent.trim().slice(0, 14) : '')).join(' > '),
			targets: [...document.querySelectorAll('.bar7-aboutopen a, .bar7-choose a')].map((a) => a.textContent.trim() + ':' + Math.round(a.getBoundingClientRect().height)).join(', '),
			titleLines: Math.round(t.getBoundingClientRect().height / parseFloat(getComputedStyle(t).lineHeight)),
			overflow: document.documentElement.scrollWidth - document.documentElement.clientWidth,
		};
	});
	out.errors = page.consoleErrors.filter((e) => !/ppc|PayPal|client-token|blocked|ERR_BLOCKED/i.test(e));
	return out;
};
