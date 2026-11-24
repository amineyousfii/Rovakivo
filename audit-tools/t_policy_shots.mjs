// Screenshots of the draft policy pages (preview HTML rendered server-side as the admin, loaded from the scratchpad), desktop + phone.
const SP = 'file:///C:/Users/HP/AppData/Local/Temp/claude/49a97806ab90cf9d/258a5ee7-bdc8-4778-b0bb-2b5da6bd5f79/scratchpad/';
const SHOT = 'C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/';
export default async ({ page }) => {
	const fs = await import('node:fs');
	const out = {};
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'reduce' }] });
	for (const id of [679, 680, 531]) {
		for (const [w, h] of [[1440, 900], [390, 844]]) {
			await page.viewport(w, h, w < 768);
			await page.goto(`${SP}preview-${id}.html`, 2500);
			await page.eval(() => { document.querySelectorAll('[class*="consent"], [class*="cky-"]').forEach((e) => { if (e !== document.body && e !== document.documentElement && /fixed|sticky/.test(getComputedStyle(e).position)) e.remove(); }); document.documentElement.style.scrollBehavior = 'auto'; });
			const info = await page.eval(() => {
				const p = document.querySelector('.bar7-prose--page');
				const marks = [...document.querySelectorAll('.bar7-prose--page mark')];
				const t = document.querySelector('.bar7-prose--page table');
				return { h: document.documentElement.scrollHeight, overflowX: document.documentElement.scrollWidth > innerWidth, prose: p ? Math.round(p.getBoundingClientRect().width) : 0, marks: marks.length, markBg: marks[0] ? getComputedStyle(marks[0]).backgroundColor : '', table: t ? Math.round(t.getBoundingClientRect().width) + 'px wide' : '', h2: document.querySelectorAll('.bar7-prose--page h2').length, crumbs: (document.querySelector('.bar7-breadcrumbs, nav[aria-label*="read"]')?.textContent || '').replace(/\s+/g, ' ').trim() };
			});
			out[`${id}@${w}`] = info;
			const r = await page.send('Page.captureScreenshot', { format: 'png', captureBeyondViewport: true, clip: { x: 0, y: 0, width: w, height: Math.min(info.h, 7000), scale: w > 800 ? 0.6 : 1 } });
			fs.writeFileSync(`${SHOT}policy-${id}-${w}.png`, Buffer.from(r.data, 'base64'));
		}
	}
	out.errors = page.consoleErrors.filter((e) => !/ppc|PayPal|client-token|CORS|net::ERR|file:/i.test(e)).slice(0, 5);
	return out;
};
