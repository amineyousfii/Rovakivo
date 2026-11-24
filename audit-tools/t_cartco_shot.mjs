const tag = process.env.SHOT_TAG || 'before';
const SHOT = 'C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/';
const facts = () => {
	const main = document.querySelector('main') || document.body;
	return {
		height: document.documentElement.scrollHeight,
		sections: [...main.children].map((e) => e.tagName.toLowerCase() + ' ' + Math.round(e.getBoundingClientRect().height)).join(' | '),
		h1: main.querySelector('h1')?.textContent.trim(), lede: main.querySelector('.bar7-lede')?.textContent.trim() || null,
		crumbs: main.querySelector('.bar7-crumbs')?.textContent.replace(/\s+/g, ' ').trim(),
		continue: main.querySelector('.bar7-cart-continue')?.textContent.trim() || null,
		rows: main.querySelectorAll('.woocommerce-cart-form__cart-item').length, empty: !!main.querySelector('.cart-empty, .wc-empty-cart-message'),
		fields: main.querySelectorAll('form.checkout input, form.checkout select, form.checkout textarea').length, placeOrder: !!main.querySelector('#place_order'),
		total: main.querySelector('.order-total .amount')?.textContent || null,
		motion: main.querySelectorAll('[data-intro],[data-intro-item],[data-intro-body]').length, links: main.querySelectorAll('a[href]').length,
	};
};
export default async ({ page }) => {
	await page.send('Network.setCookie', { name: 'rovakivo_consent', value: 'rejected', domain: '7bar.local', path: '/' });
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'reduce' }] });
	const out = {};
	for (const [w, h] of [[1440, 900], [390, 844]]) {
		await page.viewport(w, h, w < 768);
		await page.goto('http://7bar.local/cart/', 2000);
		out['cart-empty ' + w] = await page.eval(facts);
		await page.screenshot(`${SHOT}cart-empty-${tag}-${w}-full.png`, true);
	}
	await page.goto('http://7bar.local/?add-to-cart=66', 1500);
	for (const [w, h] of [[1440, 900], [390, 844]]) {
		await page.viewport(w, h, w < 768);
		await page.goto('http://7bar.local/cart/', 2500);
		out['cart-item ' + w] = await page.eval(facts);
		await page.screenshot(`${SHOT}cart-item-${tag}-${w}-full.png`, true);
		await page.goto('http://7bar.local/checkout/', 3500);
		out['checkout ' + w] = await page.eval(facts);
		await page.screenshot(`${SHOT}checkout-${tag}-${w}-full.png`, true);
	}
	out.errors = page.consoleErrors.filter((e) => !/ppc|PayPal|client-token/i.test(e));
	return out;
};
