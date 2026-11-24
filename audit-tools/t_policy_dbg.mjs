export default async ({ page }) => {
	await page.viewport(1440, 900, false);
	await page.goto('file:///C:/Users/HP/AppData/Local/Temp/claude/49a97806ab90cf9d/258a5ee7-bdc8-4778-b0bb-2b5da6bd5f79/scratchpad/preview-679.html', 3000);
	return await page.eval(() => ({ href: location.href, title: document.title, body: document.body ? document.body.innerHTML.length : -1, html: document.documentElement.outerHTML.slice(0, 300), errs: 0 }));
};
