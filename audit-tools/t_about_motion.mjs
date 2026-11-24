// About v2 motion checks (motion enabled): load sequence, clip reveal, scroll-linked typography, staggered options,
// hover states, then phone / reduced motion / blocked scripts.
const SHOT = 'C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/';
export default async ({ page }) => {
	const out = {};
	await page.send('Network.setCookie', { name: 'rovakivo_consent', value: 'rejected', domain: '7bar.local', path: '/' });
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'no-preference' }] });
	await page.viewport(1440, 900, false);
	await page.goto('http://7bar.local/about/', 200);
	out.load = await page.eval(async () => {
		const t = document.querySelector('.bar7-aboutopen__title'); const f = document.querySelector('.bar7-aboutopen__media');
		const s = [];
		for (let i = 0; i < 14; i++) {
			const ty = new DOMMatrix(getComputedStyle(t).transform).m42;
			s.push('title ' + (+getComputedStyle(t).opacity).toFixed(2) + '/y' + ty.toFixed(0) + '  photo clip ' + (getComputedStyle(f).clipPath.match(/inset\(([\d.]+)%/) || [0, '0'])[1] + '%');
			await new Promise((x) => setTimeout(x, 110));
		}
		return s.filter((v, i, a) => a.indexOf(v) === i);
	});
	out.scroll = await page.eval(async () => {
		const sec = document.querySelector('.bar7-choose'); const title = sec.querySelector('.bar7-choose__title'); const ghost = sec.querySelector('.bar7-choose__ghost');
		const r = { ghost: !!ghost, ghostHidden: ghost && ghost.getAttribute('aria-hidden'), scrubbed: sec.classList.contains('is-scrubbed'), steps: [] };
		const top = sec.getBoundingClientRect().top + scrollY;
		for (const off of [-700, -450, -250, 0, 250, 500]) {
			window.scrollTo(0, top + off); await new Promise((x) => setTimeout(x, 900));
			const gs = getComputedStyle(ghost); const gm = new DOMMatrix(gs.transform);
			r.steps.push('scroll ' + off + ': fill ' + getComputedStyle(title).getPropertyValue('--fill').trim() + ' title x' + new DOMMatrix(getComputedStyle(title).transform).m41.toFixed(0) + ' | ghost x' + gm.m41.toFixed(0) + ' op ' + (+gs.opacity).toFixed(2) + ' | items ' + [...sec.querySelectorAll('.bar7-choose__item')].map((i) => (+getComputedStyle(i).opacity).toFixed(1)).join('/') + ' | overflow ' + (document.documentElement.scrollWidth - document.documentElement.clientWidth));
		}
		return r;
	});
	// Mid-effect screenshot: ghost drifting behind the options
	await page.eval(async () => { const sec = document.querySelector('.bar7-choose'); window.scrollTo(0, sec.getBoundingClientRect().top + scrollY - 250); await new Promise((x) => setTimeout(x, 1200)); return 1; });
	await page.screenshot(SHOT + 'about-choose-scroll-mid-1440.png', false);
	// Hover the first option
	const p = await page.eval(() => { const q = document.querySelector('.bar7-choose__item .bar7-choose__q'); const r = q.getBoundingClientRect(); return { x: r.x + 20, y: r.y + r.height / 2 }; });
	await page.send('Input.dispatchMouseEvent', { type: 'mouseMoved', x: p.x, y: p.y });
	await page.sleep(600);
	out.hover = await page.eval(() => { const it = document.querySelector('.bar7-choose__item'); const q = it.querySelector('.bar7-choose__q'); return { rowHighlight: getComputedStyle(it, '::before').opacity, questionShift: new DOMMatrix(getComputedStyle(q).transform).m41, underline: getComputedStyle(q, '::after').transform }; });
	await page.screenshot(SHOT + 'about-choose-hover-1440.png', false);
	await page.send('Input.dispatchMouseEvent', { type: 'mouseMoved', x: 5, y: 5 });
	// Phone, motion on: no outline text, heading solid
	await page.viewport(390, 844, true);
	await page.goto('http://7bar.local/about/', 1500);
	out.phone = await page.eval(async () => { const sec = document.querySelector('.bar7-choose'); sec.scrollIntoView(); await new Promise((x) => setTimeout(x, 1200)); const t = sec.querySelector('.bar7-choose__title'); return { ghost: !!sec.querySelector('.bar7-choose__ghost'), titleColor: getComputedStyle(t).color, overflow: document.documentElement.scrollWidth - document.documentElement.clientWidth, items: [...sec.querySelectorAll('.bar7-choose__item')].map((i) => (+getComputedStyle(i).opacity).toFixed(1)).join('/') }; });
	// Reduced motion
	await page.viewport(1440, 900, false);
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'reduce' }] });
	await page.goto('http://7bar.local/about/', 1200);
	out.reduced = await page.eval(() => { const sec = document.querySelector('.bar7-choose'); const t = sec.querySelector('.bar7-choose__title'); const f = document.querySelector('.bar7-aboutopen__media'); return { ghost: !!sec.querySelector('.bar7-choose__ghost'), titleColor: getComputedStyle(t).color, photo: getComputedStyle(f).opacity + ' clip ' + getComputedStyle(f).clipPath, hidden: [...document.querySelectorAll('[data-intro-item],[data-intro-media],[data-reveal],[data-reveal-item]')].filter((e) => getComputedStyle(e).opacity !== '1').length }; });
	// Scripts blocked
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'no-preference' }] });
	await page.send('Network.setBlockedURLs', { urls: ['*gsap*', '*ScrollTrigger*', '*lenis*', '*motion.js*'] });
	await page.goto('http://7bar.local/about/', 1500);
	out.blocked = await page.eval(() => { const t = document.querySelector('.bar7-choose__title'); return { titleColor: getComputedStyle(t).color, ghost: !!document.querySelector('.bar7-choose__ghost'), hidden: [...document.querySelectorAll('[data-intro-item],[data-intro-media],[data-reveal],[data-reveal-item]')].filter((e) => getComputedStyle(e).opacity !== '1').length }; });
	await page.send('Network.setBlockedURLs', { urls: [] });
	out.errors = page.consoleErrors.filter((e) => !/ppc|PayPal|client-token|blocked|ERR_BLOCKED/i.test(e));
	return out;
};
