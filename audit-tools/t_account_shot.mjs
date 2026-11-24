const tag = process.env.SHOT_TAG || 'before';
const PAGES = { signin: '/my-account/', lost: '/my-account/lost-password/', orders: '/my-account/orders/' };
export default async ({ page }) => {
	await page.send('Network.setCookie', { name: 'rovakivo_consent', value: 'rejected', domain: '7bar.local', path: '/' });
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'reduce' }] });
	const out = {};
	for (const [key, path] of Object.entries(PAGES)) {
		for (const [w, h] of [[1440, 900], [390, 844]]) {
			await page.viewport(w, h, w < 768);
			await page.goto('http://7bar.local' + path, 2000);
			out[key + ' ' + w] = await page.eval(() => {
				const main = document.querySelector('main') || document.body;
				return { height: document.documentElement.scrollHeight, sections: [...main.children].map((e) => e.tagName.toLowerCase() + ' ' + Math.round(e.getBoundingClientRect().height)).join(' | '), h1: main.querySelector('h1')?.textContent.trim(), crumbs: main.querySelector('.bar7-crumbs')?.textContent.replace(/\s+/g, ' ').trim(), forms: main.querySelectorAll('form').length, inputs: main.querySelectorAll('input:not([type=hidden])').length, links: main.querySelectorAll('a[href]').length, motion: main.querySelectorAll('[data-intro],[data-intro-item],[data-intro-body]').length, title: document.title };
			});
			await page.screenshot(`C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/account-${key}-${tag}-${w}-full.png`, true);
		}
	}
	out.errors = page.consoleErrors.filter((e) => !/ppc|PayPal|client-token/i.test(e));
	return out;
};
