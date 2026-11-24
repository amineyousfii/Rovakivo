export default async ({ page }) => {
	await page.send('Network.setCookie', { name: 'rovakivo_consent', value: 'rejected', domain: '7bar.local', path: '/' });
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'reduce' }] });
	await page.viewport(1440, 900, false);
	await page.goto('http://7bar.local/checkout/order-received/630/?key=wc_order_N69eifGl39j8r', 2500);
	const r = await page.eval(() => { const main = document.querySelector('main') || document.body; return { h1s: [...main.querySelectorAll('h1')].map((h) => h.textContent.trim()), lede: main.querySelector('.bar7-lede')?.textContent.trim(), checkoutHeading: main.textContent.includes('A few details and your backpack'), next: [...main.querySelectorAll('.bar7-order-next a')].map((a) => a.textContent.trim()), wooText: (main.querySelector('.woocommerce')?.innerText || '').replace(/\s+/g, ' ').slice(0, 180), title: document.title }; });
	await page.screenshot('C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/order-received-check-1440.png', false);
	return r;
};
