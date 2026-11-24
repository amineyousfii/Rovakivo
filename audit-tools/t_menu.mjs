const SHOT = 'C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/';
const navState = () => {
	const nav = document.querySelector('.bar7-nav');
	const top = [...nav.querySelectorAll(':scope > ul > li')].map((li) => {
		const a = li.querySelector(':scope > a');
		const mark = a.getAttribute('aria-current') ? '*' : li.classList.contains('is-ancestor') ? '^' : '';
		const subs = [...li.querySelectorAll('.bar7-nav__sub a')].map((s) => s.textContent.trim() + (s.getAttribute('aria-current') ? '*' : '') + (s.classList.contains('is-sale') ? '(dot)' : ''));
		return a.textContent.trim() + mark + (subs.length ? ' [' + subs.join(', ') + ']' : '');
	});
	return top.join(' | ');
};
export default async ({ page }) => {
	const out = { current: {} };
	await page.send('Network.setCookie', { name: 'rovakivo_consent', value: 'rejected', domain: '7bar.local', path: '/' });
	await page.viewport(1440, 900, false);
	for (const p of ['/', '/about/', '/shop/', '/product-category/casual/', '/shop/?on_sale=1', '/contact/', '/cart/']) {
		await page.goto('http://7bar.local' + p, 1200);
		out.current[p] = await page.eval(navState);
	}
	await page.goto('http://7bar.local/about/', 1500);
	out.dropdown = await page.eval(async () => {
		const sub = document.querySelector('.bar7-nav__sub'); const btn = document.querySelector('.bar7-nav__toggle');
		const vis = () => getComputedStyle(sub).visibility;
		const r = { closed: vis(), btnLabel: btn.textContent.trim(), controls: btn.getAttribute('aria-controls') === sub.id };
		btn.click(); await new Promise((x) => setTimeout(x, 300)); r.afterClick = vis() + ' expanded=' + btn.getAttribute('aria-expanded');
		sub.querySelector('a').focus(); await new Promise((x) => setTimeout(x, 100));
		document.activeElement.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape', bubbles: true }));
		await new Promise((x) => setTimeout(x, 300)); r.afterEscape = 'expanded=' + btn.getAttribute('aria-expanded') + ' focusOnToggle=' + (document.activeElement === btn);
		btn.click(); await new Promise((x) => setTimeout(x, 200)); document.body.click(); await new Promise((x) => setTimeout(x, 300)); r.afterOutsideClick = 'expanded=' + btn.getAttribute('aria-expanded');
		const rect = sub.getBoundingClientRect(); r.subBox = Math.round(rect.left) + ',' + Math.round(rect.top) + ' ' + Math.round(rect.width) + 'x' + Math.round(rect.height);
		return r;
	});
	// Hover opens it (real mouse)
	const pos = await page.eval(() => { const a = document.querySelector('.has-sub > a').getBoundingClientRect(); return { x: a.x + a.width / 2, y: a.y + a.height / 2 }; });
	await page.send('Input.dispatchMouseEvent', { type: 'mouseMoved', x: pos.x, y: pos.y });
	await page.sleep(400);
	out.hover = await page.eval(() => getComputedStyle(document.querySelector('.bar7-nav__sub')).visibility);
	await page.screenshot(SHOT + 'menu-dropdown-1440.png', false);
	// Move into the panel: stays open
	const sp = await page.eval(() => { const a = document.querySelectorAll('.bar7-nav__sub a')[2].getBoundingClientRect(); return { x: a.x + 20, y: a.y + a.height / 2 }; });
	await page.send('Input.dispatchMouseEvent', { type: 'mouseMoved', x: sp.x, y: pos.y + 20 });
	await page.send('Input.dispatchMouseEvent', { type: 'mouseMoved', x: sp.x, y: sp.y });
	await page.sleep(300);
	out.hoverIntoPanel = await page.eval(() => getComputedStyle(document.querySelector('.bar7-nav__sub')).visibility);
	// Widths: no overflow, nav fits
	out.widths = {};
	for (const w of [1024, 1100, 1280, 1440, 1920]) {
		await page.viewport(w, 900, false);
		await page.goto('http://7bar.local/', 900);
		out.widths[w] = await page.eval(() => { const nav = document.querySelector('.bar7-nav'); const r = nav.getBoundingClientRect(); const tools = document.querySelector('.bar7-header__tools').getBoundingClientRect(); const logo = document.querySelector('.bar7-header__logo').getBoundingClientRect(); return { overflow: document.documentElement.scrollWidth - document.documentElement.clientWidth, navShown: getComputedStyle(nav).display !== 'none', overlap: r.right > tools.left || r.left < logo.right }; });
	}
	// Phone drawer
	await page.viewport(390, 844, true);
	await page.goto('http://7bar.local/product-category/travel/', 1500);
	out.drawer = await page.eval(async () => {
		document.querySelector('.bar7-header__menu').click(); await new Promise((x) => setTimeout(x, 700));
		const d = document.getElementById('bar7-drawer');
		return { items: [...d.querySelectorAll('.bar7-drawer__nav > li')].map((li) => li.querySelector(':scope > a').textContent.trim() + ([...li.querySelectorAll('.bar7-drawer__sub a')].map((s) => s.textContent.trim() + (s.getAttribute('aria-current') ? '*' : '')).join('/') ? ' [' + [...li.querySelectorAll('.bar7-drawer__sub a')].map((s) => s.textContent.trim() + (s.getAttribute('aria-current') ? '*' : '')).join(', ') + ']' : '')).join(' | '), extra: [...d.querySelectorAll('.bar7-drawer__links a')].map((a) => a.textContent.trim()).join(', '), overflow: document.documentElement.scrollWidth - document.documentElement.clientWidth };
	});
	await page.screenshot(SHOT + 'menu-drawer-390.png', false);
	out.errors = page.consoleErrors.filter((e) => !/ppc|PayPal|client-token/i.test(e));
	return out;
};
