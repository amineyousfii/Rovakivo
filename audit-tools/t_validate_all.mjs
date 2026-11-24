export default async ({ page }) => {
	await page.viewport(1200, 800, false);
	await page.goto('file:///C:/Users/HP/AppData/Local/Temp/claude/49a97806ab90cf9d/258a5ee7-bdc8-4778-b0bb-2b5da6bd5f79/scratchpad/validate-all.html', 3000);
	const r = await page.eval(() => window.__result || { notReady: true, wp: typeof wp, keys: window.wp ? Object.keys(wp).join(',') : '' });
	return { r, errors: page.consoleErrors.slice(0, 5).map((e) => e.slice(0, 300)) };
};
