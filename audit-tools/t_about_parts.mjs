const tag = process.env.SHOT_TAG || 'before';
export default async ({ page }) => {
	await page.send('Network.setCookie', { name: 'rovakivo_consent', value: 'rejected', domain: '7bar.local', path: '/' });
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'reduce' }] });
	const out = {};
	for (const [w, h] of [[1440, 900], [1024, 768], [768, 1024], [390, 844], [320, 640]]) {
		await page.viewport(w, h, w < 768);
		await page.goto('http://7bar.local/about/', 1800);
		out[w] = await page.eval(async () => {
			for (let y = 0; y <= document.body.scrollHeight; y += 500) { window.scrollTo(0, y); await new Promise((x) => setTimeout(x, 80)); }
			window.scrollTo(0, 0);
			for (let i = 0; i < 40 && [...document.images].some((im) => !im.complete); i++) await new Promise((x) => setTimeout(x, 200));
			const main = document.querySelector('main');
			const kids = [...main.children];
			const tiles = main.querySelector('.bar7-cats')?.closest('section');
			const keep = [tiles, main.querySelector('.bar7-cta'), main.querySelector('.bar7-newsletter'), document.querySelector('.bar7-footer')].map((e) => e ? Math.round(e.getBoundingClientRect().height) : 'missing');
			return { height: document.documentElement.scrollHeight, overflow: document.documentElement.scrollWidth - document.documentElement.clientWidth, sections: kids.map((e) => e.tagName.toLowerCase() + ' ' + Math.round(e.getBoundingClientRect().height)).join(' | '), keptHeights: keep.join(' / '), keptLinks: [tiles, main.querySelector('.bar7-cta'), main.querySelector('.bar7-newsletter')].map((e) => e ? [...e.querySelectorAll('a[href]')].map((a) => a.getAttribute('href')).join(' ') : '').join(' | ') };
		});
		await page.screenshot(`C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/about-redesign-${tag}-${w}-full.png`, true);
	}
	out.errors = page.consoleErrors.filter((e) => !/ppc|PayPal|client-token/i.test(e));
	return out;
};
