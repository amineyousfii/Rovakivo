export default async ({ page }) => {
	const out = {};
	for (const k of ['home', 'contact']) {
		await page.viewport(1440, 900, false);
		await page.goto(`file:///C:/Users/HP/AppData/Local/Temp/claude/49a97806ab90cf9d/258a5ee7-bdc8-4778-b0bb-2b5da6bd5f79/scratchpad/editor-sim-${k}.html`, 2500);
		out[k] = await page.eval(async () => { await document.fonts.ready; const q = (s) => document.querySelector(s); return { cssRejected: window.__css, h1: q('h1.wp-block-heading') && getComputedStyle(q('h1.wp-block-heading')).fontFamily.split(',')[0], heights: [...document.querySelectorAll('.is-root-container > *')].map((e) => e.tagName.toLowerCase() + ' ' + Math.round(e.getBoundingClientRect().height)).join(' | ') }; });
		await page.screenshot(`C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/${k}-editor-sim-1440-full.png`, true);
	}
	out.errors = page.consoleErrors.slice(0, 3);
	return out;
};
