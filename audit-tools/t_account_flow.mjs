export default async ({ page }) => {
	await page.send('Network.setCookie', { name: 'rovakivo_consent', value: 'rejected', domain: '7bar.local', path: '/' });
	await page.viewport(1440, 900, false);
	await page.goto('http://7bar.local/my-account/', 1500);
	await page.eval(() => { setTimeout(() => document.querySelector('button[name="login"]').click(), 30); return 1; });
	await page.settle(1500);
	const login = await page.eval(() => ({ error: (document.querySelector('.woocommerce-error')?.textContent || '').trim().replace(/\s+/g, ' '), h1: document.querySelector('main h1')?.textContent.trim() }));
	await page.goto('http://7bar.local/my-account/lost-password/', 1500);
	await page.eval(() => { document.querySelector('#user_login').value = 'audit-nobody@example.invalid'; setTimeout(() => document.querySelector('.woocommerce-ResetPassword button[type="submit"], button[name="wc_reset_password"]').click(), 30); return 1; });
	await page.settle(1500);
	const lost = await page.eval(() => ({ error: (document.querySelector('.woocommerce-error')?.textContent || '').trim().replace(/\s+/g, ' '), h1: document.querySelector('main h1')?.textContent.trim() }));
	return { login, lost, errors: page.consoleErrors.filter((e) => !/ppc|PayPal|client-token/i.test(e)) };
};
