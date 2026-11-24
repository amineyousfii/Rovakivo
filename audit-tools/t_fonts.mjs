const SHOT = 'C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/';
export default async ({ page }) => {
	const hosts = new Set();
	page.listeners.push((m) => { if (m.method === 'Network.requestWillBeSent') hosts.add(new URL(m.params.request.url).host); });
	await page.send('Network.setCookie', { name: 'rovakivo_consent', value: 'rejected', domain: '7bar.local', path: '/' });
	await page.send('Network.setCacheDisabled', { cacheDisabled: true });
	const out = {};
	for (const [w, h] of [[1440, 900], [390, 844]]) {
		await page.viewport(w, h, w < 768);
		for (const p of ['/', '/product/aturna-condimentum-mattis-pellentesque-nibh-tortor/', '/checkout/', '/cart/']) {
			await page.goto('http://7bar.local' + p, 1500);
			out[w + ' ' + p] = await page.eval(async () => {
				await document.fonts.ready;
				const loaded = [...document.fonts].filter((f) => f.status === 'loaded').map((f) => f.family.replace(/"/g, '') + ' ' + f.weight);
				const h = document.querySelector('h1');
				return { loaded: [...new Set(loaded)], dmsans: document.fonts.check('500 16px "DM Sans"'), manrope: document.fonts.check('800 40px Manrope'), h1Font: h && getComputedStyle(h).fontFamily.split(',')[0], bodyFont: getComputedStyle(document.body).fontFamily.split(',')[0], fontFiles: performance.getEntriesByType('resource').filter((r) => /woff2/.test(r.name)).map((r) => r.name.split('/').pop() + ' ' + Math.round(r.transferSize / 1024) + 'KB') };
			});
		}
		await page.goto('http://7bar.local/', 1500);
		await page.screenshot(`${SHOT}fonts-selfhosted-home-${w}.png`);
	}
	out.hosts = [...hosts];
	out.errors = page.consoleErrors.filter((e) => !/ppc|PPCP|PayPal/i.test(e));
	return out;
};
