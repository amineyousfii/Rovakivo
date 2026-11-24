export default async ({ page }) => {
	await page.send('Network.setCookie', { name: 'rovakivo_consent', value: 'rejected', domain: '7bar.local', path: '/' });
	await page.viewport(1440, 900, false);
	await page.goto('http://7bar.local/?add-to-cart=66', 800);
	await page.goto('http://7bar.local/cart/', 3000);
	const info = await page.eval(() => { const i = document.querySelector('.woocommerce-cart-form input.qty'); const w = i.closest('.quantity'); return { input: i.outerHTML, wrap: w.innerHTML.replace(/<svg[\s\S]*?<\/svg>/g, '').slice(0, 600), buttons: [...w.querySelectorAll('button')].map((b) => b.getAttribute('aria-label') + ' disabled=' + b.disabled) }; });
	const r = await page.eval(() => { const b = document.querySelector('.woocommerce-cart-form .quantity button:last-of-type'); const b2 = [...document.querySelectorAll('.woocommerce-cart-form .quantity button')].pop(); const before = document.querySelector('.woocommerce-cart-form input.qty').value; b2.click(); return { lastOfType: b.getAttribute('aria-label'), last: b2.getAttribute('aria-label'), before, after: document.querySelector('.woocommerce-cart-form input.qty').value }; });
	await page.sleep(1200);
	const t0 = Date.now();
	await page.eval(async () => { for (let i = 0; i < 300; i++) { await new Promise((x) => setTimeout(x, 200)); if (!document.querySelector('.blockUI') && i > 3) return i; } return -1; });
	const res = await page.eval(() => ({ qty: document.querySelector('.woocommerce-cart-form input.qty').value, line: document.querySelector('.product-subtotal .amount')?.textContent, total: document.querySelector('.order-total .amount')?.textContent, live: document.getElementById('bar7-live')?.textContent }));
	await page.screenshot('C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/state-cart-qty-updated-1440.png');
	return { info, r, res, waitedMs: Date.now() - t0 };
};
