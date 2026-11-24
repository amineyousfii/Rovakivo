export default async ({ page }) => {
	await page.send('Emulation.setDeviceMetricsOverride', { width: 1440, height: 4600, deviceScaleFactor: 1, mobile: false });
	await page.goto('file:///C:/Users/HP/AppData/Local/Temp/claude/49a97806ab90cf9d/258a5ee7-bdc8-4778-b0bb-2b5da6bd5f79/scratchpad/editor-sim-shop.html', 5000);
	const r = await page.eval(async () => {
		const doc = document.querySelector('iframe[name="editor-canvas"]').contentDocument;
		for (let i = 0; i < 40 && [...doc.images].some((im) => !im.complete); i++) await new Promise((x) => setTimeout(x, 250));
		return { chrome: [...doc.querySelectorAll('[data-bar7-chrome]')].map((e) => e.getAttribute('data-bar7-chrome')), cards: doc.querySelectorAll('.bar7-card').length, current: doc.querySelector('.bar7-nav [aria-current]')?.textContent, h1: doc.querySelector('.is-root-container h1')?.textContent, listingCols: getComputedStyle(doc.querySelector('.bar7-listing')).gridTemplateColumns };
	});
	await page.screenshot('C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/shop-editor-with-chrome-sim.png', false);
	return { r, errors: page.consoleErrors.slice(0, 3) };
};
