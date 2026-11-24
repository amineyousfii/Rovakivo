const SHOT = 'C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/';
export default async ({ page }) => {
	await page.send('Network.setCookie', { name: 'rovakivo_consent', value: 'rejected', domain: '7bar.local', path: '/' });
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'reduce' }] });
	const out = {};
	for (const [w, h] of [[1440, 900], [768, 1024], [390, 844]]) {
		await page.viewport(w, h, w < 768);
		await page.goto('http://7bar.local/about/', 1500);
		out[w] = await page.eval(() => {
			const f = document.querySelector('.bar7-footer');
			f.scrollIntoView({ block: 'end' });
			const cols = [...f.querySelectorAll('.bar7-footer__col')];
			const small = [...f.querySelectorAll('a, button')].filter((a) => a.offsetParent && a.getBoundingClientRect().height < 44).map((a) => (a.textContent.trim() || a.getAttribute('aria-label')) + ':' + Math.round(a.getBoundingClientRect().height));
			return {
				landmarks: cols.map((n) => n.tagName + '[' + n.getAttribute('aria-labelledby') + '→' + document.getElementById(n.getAttribute('aria-labelledby'))?.textContent + ']').join(' '),
				colsPerRow: (() => { const tops = cols.map((c) => Math.round(c.getBoundingClientRect().top)); return tops.filter((t) => t === tops[0]).length; })(),
				overflow: document.documentElement.scrollWidth - document.documentElement.clientWidth,
				contrast: getComputedStyle(f.querySelector('.bar7-footer__col a')).color + ' on ' + getComputedStyle(f).backgroundColor,
				smallTargets: small,
				tabOrder: [...f.querySelectorAll('a[href], button')].map((a) => a.textContent.trim()).join(' > '),
			};
		});
		await page.eval(async () => { document.querySelector('.bar7-footer').scrollIntoView({ block: 'start' }); await new Promise((x) => setTimeout(x, 400)); return 1; });
		const clip = await page.eval(() => { const r = document.querySelector('.bar7-footer').getBoundingClientRect(); return { y: r.top + scrollY, h: r.height, w: document.documentElement.clientWidth }; });
		const shot = await page.send('Page.captureScreenshot', { format: 'png', captureBeyondViewport: true, clip: { x: 0, y: clip.y, width: clip.w, height: clip.h, scale: 1 } });
		(await import('node:fs')).writeFileSync(`${SHOT}footer-${w}.png`, Buffer.from(shot.data, 'base64'));
	}
	out.errors = page.consoleErrors.filter((e) => !/ppc|PayPal|client-token/i.test(e));
	return out;
};
