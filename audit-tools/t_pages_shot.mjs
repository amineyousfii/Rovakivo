const tag = process.env.SHOT_TAG || 'before';
const PAGES = { home: '/', contact: '/contact/', about: '/about/' };
export default async ({ page }) => {
	await page.send('Network.setCookie', { name: 'rovakivo_consent', value: 'rejected', domain: '7bar.local', path: '/' });
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'reduce' }] });
	const out = {};
	for (const [key, path] of Object.entries(PAGES)) {
		for (const [w, h] of [[1440, 900], [390, 844]]) {
			await page.viewport(w, h, w < 768);
			await page.goto('http://7bar.local' + path, 2000);
			out[key + ' ' + w] = await page.eval(() => ({
				height: document.documentElement.scrollHeight,
				sections: [...document.querySelectorAll('main > *, #main > *')].map((e) => e.tagName.toLowerCase() + ' ' + Math.round(e.getBoundingClientRect().height)).join(' | '),
				landmarks: [...document.querySelectorAll('main section[aria-labelledby], main aside[aria-labelledby], main section[aria-label]')].length,
				motionAttrs: document.querySelectorAll('main [data-intro], main [data-intro-item], main [data-intro-media], main [data-reveal], main [data-reveal-group], main [data-reveal-item], main [data-reveal-grid], main [data-reveal-sale], main [data-reveal-media]').length,
				links: [...document.querySelectorAll('main a[href]')].length,
				h: [...document.querySelectorAll('main h1, main h2, main h3')].map((e) => e.tagName + ':' + e.textContent.trim().slice(0, 18)).join(' / '),
			}));
			await page.screenshot(`C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/${key}-${tag}-${w}-full.png`, true);
		}
	}
	out.errors = page.consoleErrors.filter((e) => !/ppc|PayPal/i.test(e));
	return out;
};
