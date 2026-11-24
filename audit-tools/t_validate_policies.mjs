// Validates the Terms, Shipping and Returns block markup with WordPress's own block parser (validate-policies.html in the scratchpad).
export default async ({ page }) => {
	await page.viewport(1200, 800, false);
	await page.goto('file:///C:/Users/HP/AppData/Local/Temp/claude/49a97806ab90cf9d/258a5ee7-bdc8-4778-b0bb-2b5da6bd5f79/scratchpad/validate-policies.html', 3000);
	let r;
	for (let i = 0; i < 40; i++) { r = await page.eval(() => window.__result || null); if (r) break; await page.sleep(1000); }
	return { r: r || 'not ready', errors: page.consoleErrors.slice(0, 5).map((e) => e.slice(0, 300)) };
};
