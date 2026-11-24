const tag = process.env.SHOT_TAG || 'before';
const PAGES = { shop: '/shop/', casual: '/product-category/casual/', sale: '/shop/?on_sale=1', search: '/?s=green&post_type=product', price: '/shop/?min_price=40&max_price=60', sorted: '/shop/?orderby=price' };
export default async ({ page }) => {
	await page.send('Network.setCookie', { name: 'rovakivo_consent', value: 'rejected', domain: '7bar.local', path: '/' });
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'reduce' }] });
	const out = {};
	for (const [key, path] of Object.entries(PAGES)) {
		for (const [w, h] of [[1440, 900], [390, 844]]) {
			if (w === 390 && !['shop', 'casual'].includes(key)) continue;
			await page.viewport(w, h, w < 768);
			await page.goto('http://7bar.local' + path, 1500);
			out[key + ' ' + w] = await page.eval(async () => {
				for (let y = 0; y <= document.body.scrollHeight; y += 400) { window.scrollTo(0, y); await new Promise((x) => setTimeout(x, 100)); }
				window.scrollTo(0, 0);
				for (let i = 0; i < 40 && [...document.images].some((im) => !im.complete); i++) await new Promise((x) => setTimeout(x, 250));
				await new Promise((x) => setTimeout(x, 300));
				const main = document.querySelector('main') || document.body;
				return {
					height: document.documentElement.scrollHeight,
					sections: [...main.children].map((e) => e.tagName.toLowerCase() + '.' + (e.className || '').toString().split(' ')[0] + ' ' + Math.round(e.getBoundingClientRect().height)).join(' | '),
					cards: main.querySelectorAll('.bar7-card').length,
					count: main.querySelector('.bar7-toolbar__count')?.textContent.trim(),
					ordering: !!main.querySelector('select.orderby'),
					h1: main.querySelector('h1')?.textContent.trim(),
					crumbs: main.querySelector('.bar7-crumbs')?.textContent.replace(/\s+/g, ' ').trim(),
					motion: main.querySelectorAll('[data-intro],[data-intro-item],[data-intro-media],[data-reveal],[data-reveal-grid],[data-reveal-item]').length,
					links: main.querySelectorAll('a[href]').length,
					forms: main.querySelectorAll('form, input, select').length,
					drawer: !!document.getElementById('bar7-filterdrawer'),
				};
			});
			await page.screenshot(`C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/listing-${key}-${tag}-${w}-full.png`, true);
		}
	}
	out.errors = page.consoleErrors.filter((e) => !/ppc|PayPal/i.test(e));
	return out;
};
