export default async ({ page }) => {
	await page.viewport(1440, 1000, false);
	await page.goto('file:///C:/Users/HP/AppData/Local/Temp/claude/49a97806ab90cf9d/258a5ee7-bdc8-4778-b0bb-2b5da6bd5f79/scratchpad/editor-sim-chrome.html', 4000);
	const r = await page.eval(async () => {
		const doc = () => document.querySelector('iframe[name="editor-canvas"]').contentDocument;
		const order = () => [...doc().querySelector('.is-root-container').parentNode.children].filter((e) => e.tagName === 'DIV').map((e) => e.getAttribute('data-bar7-chrome') || e.className.split(' ')[0]).join(' > ');
		const res = { fetched: window.__fetched, order: order() };
		const top = doc().querySelector('[data-bar7-chrome="top"]');
		res.inert = top && top.hasAttribute('inert') && top.getAttribute('aria-hidden');
		res.headerPos = top && getComputedStyle(top.querySelector('.bar7-header')).position;
		res.navFont = top && getComputedStyle(top.querySelector('.bar7-nav a')).fontFamily.split(',')[0];
		res.current = top && top.querySelector('.bar7-nav [aria-current]')?.textContent;
		top.remove(); doc().querySelector('[data-bar7-chrome="bottom"]').remove();
		await new Promise((x) => setTimeout(x, 1200));
		res.afterRedraw = order();
		res.fetchedAfter = window.__fetched;
		return res;
	});
	await page.send('Emulation.setDeviceMetricsOverride', { width: 1440, height: 5200, deviceScaleFactor: 1, mobile: false });
	await page.sleep(800);
	await page.screenshot('C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/about-editor-with-chrome-sim.png', false);
	return { r, errors: page.consoleErrors.slice(0, 3) };
};
