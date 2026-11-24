const SHOT = 'C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/';
const tab = async (page, shift = false) => {
	for (const type of ['rawKeyDown', 'keyUp']) await page.send('Input.dispatchKeyEvent', { type, key: 'Tab', code: 'Tab', windowsVirtualKeyCode: 9, modifiers: shift ? 8 : 0 });
	await page.sleep(120);
};
export default async ({ page }) => {
	const out = { photo: {}, keyboard: [] };
	await page.send('Network.setCookie', { name: 'rovakivo_consent', value: 'rejected', domain: '7bar.local', path: '/' });
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'reduce' }] });
	for (const [w, h, dpr] of [[1440, 900, 1], [1024, 768, 1], [768, 1024, 1], [390, 844, 3]]) {
		await page.send('Emulation.setDeviceMetricsOverride', { width: w, height: h, deviceScaleFactor: dpr, mobile: w < 768 });
		await page.goto('http://7bar.local/about/', 1500);
		out.photo[w + '@' + dpr + 'x'] = await page.eval(async () => {
			const i = document.querySelector('.bar7-aboutopen__media img');
			for (let k = 0; k < 40 && !i.complete; k++) await new Promise((x) => setTimeout(x, 200));
			const r = i.getBoundingClientRect();
			const drawnWidth = Math.max(r.width, (r.height * i.naturalWidth) / i.naturalHeight);
			return i.currentSrc.split('/').pop() + ' (' + i.naturalWidth + 'px wide, drawn ' + Math.round(drawnWidth * devicePixelRatio) + ' device px)';
		});
		if (w === 390) {
			out.phoneTargets = await page.eval(() => [...document.querySelectorAll('.bar7-aboutopen__actions a')].map((a) => a.textContent.trim() + ':' + Math.round(a.getBoundingClientRect().height)).join(', '));
		}
	}
	// Keyboard: Tab from the breadcrumb through the opening and the guide link; focus ring must show.
	await page.send('Emulation.setDeviceMetricsOverride', { width: 1440, height: 900, deviceScaleFactor: 1, mobile: false });
	await page.goto('http://7bar.local/about/', 1500);
	await page.eval(() => { document.querySelector('.bar7-aboutopen .bar7-crumbs a').focus(); return 1; });
	for (let i = 0; i < 4; i++) {
		await tab(page);
		out.keyboard.push(await page.eval(() => { const a = document.activeElement; const s = getComputedStyle(a); return (a.textContent.trim() || a.tagName) + ' → ' + s.outlineStyle + ' ' + s.outlineWidth + ' ' + s.outlineColor; }));
	}
	await page.eval(() => { document.activeElement.blur(); window.scrollTo(0, 0); return 1; });
	// Final screenshots (motion off so everything is in its resting state)
	for (const [w, h, name] of [[1440, 900, 'desktop'], [390, 844, 'mobile']]) {
		await page.send('Emulation.setDeviceMetricsOverride', { width: w, height: h, deviceScaleFactor: 1, mobile: w < 768 });
		await page.goto('http://7bar.local/about/', 1500);
		await page.eval(async () => { for (let k = 0; k < 40 && [...document.images].some((im) => !im.complete); k++) await new Promise((x) => setTimeout(x, 200)); return 1; });
		await page.screenshot(`${SHOT}about-v2-final-${name}-full.png`, true);
	}
	out.errors = page.consoleErrors.filter((e) => !/ppc|PayPal|client-token/i.test(e));
	return out;
};
