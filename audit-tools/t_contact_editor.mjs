export default async ({ page }) => {
	await page.send('Emulation.setDeviceMetricsOverride', { width: 1440, height: 2000, deviceScaleFactor: 1, mobile: false });
	await page.goto('file:///C:/Users/HP/AppData/Local/Temp/claude/49a97806ab90cf9d/258a5ee7-bdc8-4778-b0bb-2b5da6bd5f79/scratchpad/editor-sim-contact.html', 8000);
	const r = await page.eval(() => {
		const f = document.querySelector('iframe[name="editor-canvas"]');
		const doc = f.contentDocument;
		return {
			rejectedCss: f.contentWindow.__rej,
			chrome: [...doc.querySelectorAll('[data-bar7-chrome]')].map((e) => e.getAttribute('data-bar7-chrome')),
			current: doc.querySelector('.bar7-nav [aria-current]')?.textContent,
			h1: doc.querySelector('.is-root-container h1')?.textContent,
			formFields: doc.querySelectorAll('form.wpforms-form input:not([type=hidden]), form.wpforms-form textarea').length,
			helpLinks: doc.querySelectorAll('.bar7-helplist a').length,
			layout: (() => { const a = doc.querySelector('.bar7-contact__form').getBoundingClientRect(); const b = doc.querySelector('.bar7-contact__help').getBoundingClientRect(); return Math.round(b.top) > Math.round(a.bottom) - 5 ? 'stacked' : 'side by side'; })(),
		};
	});
	await page.screenshot('C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/contact-editor-with-chrome-sim.png', false);
	return { r, errors: page.consoleErrors.filter((e) => !/users\/me|^Object$/.test(e)).slice(0, 3) };
};
