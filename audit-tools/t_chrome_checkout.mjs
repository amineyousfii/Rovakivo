export default async ({ page }) => {
	await page.send('Emulation.setDeviceMetricsOverride', { width: 1440, height: 3000, deviceScaleFactor: 1, mobile: false });
	await page.goto('file:///C:/Users/HP/AppData/Local/Temp/claude/49a97806ab90cf9d/258a5ee7-bdc8-4778-b0bb-2b5da6bd5f79/scratchpad/editor-sim-checkout.html', 5000);
	const r = await page.eval(() => { const f = document.querySelector('iframe[name="editor-canvas"]'); const doc = f.contentDocument; const cols = doc.querySelector('.ct-customer-details, #customer_details'); return { rejectedCss: f.contentWindow.__rej, chrome: [...doc.querySelectorAll('[data-bar7-chrome]')].length, h1: doc.querySelector('.is-root-container h1')?.textContent, fields: doc.querySelectorAll('form.checkout input:not([type=hidden]), form.checkout select').length, customerWidth: cols && Math.round(cols.getBoundingClientRect().width), bodyFont: getComputedStyle(doc.querySelector('form.checkout label') || doc.body).fontFamily.split(',')[0] }; });
	await page.screenshot('C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/checkout-editor-with-chrome-sim.png', false);
	return { r, errors: page.consoleErrors.slice(0, 3) };
};
