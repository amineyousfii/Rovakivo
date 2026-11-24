export default async ({ page }) => {
	const out = {};
	await page.send('Network.setCookie', { name: 'rovakivo_consent', value: 'rejected', domain: '7bar.local', path: '/' });
	await page.viewport(320, 568, true);
	await page.goto('http://7bar.local/no-such-page-audit/'); await page.settle(300);
	out.notfound320 = await page.eval(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
	await page.screenshot('C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/notfound-320.png');
	await page.viewport(390, 844, true);
	await page.goto('http://7bar.local/product/aturna-condimentum-mattis-pellentesque-nibh-tortor/'); await page.settle(300);
	out.stickyOnLoad = await page.eval(() => !document.querySelector('[data-bar7-stickybuy]')?.hidden);
	await page.screenshot('C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/product-390.png');
	out.hiddenAtForm = await page.eval(async () => { document.querySelector('.bar7-pdp__buy').scrollIntoView({ block: 'center' }); await new Promise((x) => setTimeout(x, 500)); return document.querySelector('[data-bar7-stickybuy]')?.hidden; });
	await page.goto('http://7bar.local/?add-to-cart=66', 500); await page.settle(300);
	await page.goto('http://7bar.local/cart/', 1500); await page.settle(300);
	out.cartThumbAlt = await page.eval(() => [...document.querySelectorAll('.product-thumbnail a img')].map((i) => i.alt));
	// first visit: consent banner shown, sticky bar waits
	await page.send('Network.deleteCookies', { name: 'rovakivo_consent', domain: '7bar.local' });
	await page.goto('http://7bar.local/product/aturna-condimentum-mattis-pellentesque-nibh-tortor/'); await page.settle(300);
	out.firstVisit = await page.eval(async () => { const r = { consent: !document.getElementById('bar7-consent')?.hidden, sticky: !document.querySelector('[data-bar7-stickybuy]')?.hidden }; document.querySelector('[data-bar7-consent-choice="rejected"]').click(); await new Promise((x) => setTimeout(x, 400)); r.stickyAfterChoice = !document.querySelector('[data-bar7-stickybuy]')?.hidden; return r; });
	await page.send('Network.deleteCookies', { name: 'rovakivo_consent', domain: '7bar.local' });
	await page.goto('http://7bar.local/'); await page.settle(300);
	await page.screenshot('C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/consent-banner-390.png');
	await page.viewport(1440, 900, false);
	await page.goto('http://7bar.local/'); await page.settle(300);
	await page.screenshot('C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/consent-banner-1440.png');
	return out;
};
