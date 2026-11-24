// Contact page checks: layout per width, form labels and validation (no message is sent), links, menu state,
// keyboard order, tap sizes, contrast. Screenshots to the audit folder.
const SHOT = 'C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/';
const lum = (rgb) => { const c = rgb.match(/\d+(\.\d+)?/g).slice(0, 3).map((v) => { v = v / 255; return v <= 0.03928 ? v / 12.92 : ((v + 0.055) / 1.055) ** 2.4; }); return 0.2126 * c[0] + 0.7152 * c[1] + 0.0722 * c[2]; };
const ratio = (a, b) => +((Math.max(lum(a), lum(b)) + 0.05) / (Math.min(lum(a), lum(b)) + 0.05)).toFixed(1);

export default async ({ page }) => {
	const out = { widths: {} };
	await page.send('Network.setCookie', { name: 'rovakivo_consent', value: 'rejected', domain: '7bar.local', path: '/' });
	await page.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'reduce' }] });
	for (const [w, h] of [[1440, 900], [1024, 768], [768, 1024], [390, 844], [320, 640]]) {
		await page.viewport(w, h, w < 768);
		await page.goto('http://7bar.local/contact/', 1800);
		out.widths[w] = await page.eval(() => {
			const form = document.querySelector('.bar7-contact__form').getBoundingClientRect();
			const help = document.querySelector('.bar7-contact__help').getBoundingClientRect();
			return {
				overflow: document.documentElement.scrollWidth - document.documentElement.clientWidth,
				layout: Math.round(help.top) > Math.round(form.bottom) - 5 ? 'stacked' : 'side by side',
				smallTargets: [...document.querySelectorAll('main a, main button, main input, main textarea')].filter((e) => e.offsetParent && e.getBoundingClientRect().height < 44 && e.type !== 'hidden').map((e) => (e.textContent.trim() || e.name || e.type) + ':' + Math.round(e.getBoundingClientRect().height)),
			};
		});
		if (w === 1440 || w === 390) await page.screenshot(`${SHOT}contact-check-${w}-full.png`, true);
	}
	await page.viewport(1440, 900, false);
	await page.goto('http://7bar.local/contact/', 1800);
	out.page = await page.eval(() => {
		const main = document.querySelector('main');
		return {
			title: document.title,
			h1: main.querySelector('h1')?.textContent.trim(),
			headings: [...main.querySelectorAll('h1, h2, h3')].map((x) => x.tagName + ':' + x.textContent.trim()).join(' / '),
			crumbs: main.querySelector('.bar7-crumbs')?.textContent.replace(/\s+/g, ' ').trim(),
			landmarks: [...main.querySelectorAll('[aria-labelledby]')].map((e) => e.tagName.toLowerCase() + '→' + document.getElementById(e.getAttribute('aria-labelledby'))?.textContent.trim()).join(', '),
			menuCurrent: document.querySelector('.bar7-nav [aria-current="page"]')?.textContent.trim(),
			helpLinks: [...main.querySelectorAll('.bar7-helplist a')].map((a) => a.querySelector('strong')?.textContent.trim() + ' → ' + a.getAttribute('href').replace('http://7bar.local', '')),
			fields: [...main.querySelectorAll('form.wpforms-form input:not([type=hidden]), form.wpforms-form textarea')].filter((f) => f.offsetParent).map((f) => {
				const label = f.id ? document.querySelector(`label[for="${f.id}"]`) : null;
				return (label ? label.textContent.replace(/\s+/g, ' ').trim() : '(no label)') + ' [' + f.type + (f.required || f.getAttribute('aria-required') === 'true' ? ', required' : '') + (f.autocomplete ? ', autocomplete=' + f.autocomplete : '') + ']';
			}),
			submit: main.querySelector('form.wpforms-form button[type=submit]')?.textContent.trim(),
		};
	});
	const colors = await page.eval(() => ({ lede: getComputedStyle(document.querySelector('main .bar7-lede')).color, label: getComputedStyle(document.querySelector('.wpforms-field-label')).color, help: getComputedStyle(document.querySelector('.bar7-helplist a span span')).color, helpBg: getComputedStyle(document.querySelector('.bar7-helplist a')).backgroundColor }));
	out.contrast = { lede: ratio(colors.lede, 'rgb(245,245,243)'), labels: ratio(colors.label, 'rgb(255,255,255)'), helpText: ratio(colors.help, colors.helpBg) };
	out.validation = await page.eval(async () => {
		const f = document.querySelector('form.wpforms-form');
		f.querySelector('button[type=submit]').click();
		await new Promise((x) => setTimeout(x, 1200));
		const errs = [...f.querySelectorAll('em.wpforms-error, label.wpforms-error')].filter((e) => e.offsetParent).map((e) => e.textContent.trim());
		const firstInvalid = f.querySelector('.wpforms-error:not(label):not(em)');
		const email = f.querySelector('input[type=email]');
		email.value = 'not-an-email'; email.dispatchEvent(new Event('input', { bubbles: true })); email.dispatchEvent(new Event('blur', { bubbles: true })); email.dispatchEvent(new Event('focusout', { bubbles: true }));
		await new Promise((x) => setTimeout(x, 600));
		const emailErr = (email.closest('.wpforms-field').querySelector('.wpforms-error:not(input)')?.textContent || '').trim();
		return { errors: errs, describedBy: firstInvalid ? firstInvalid.getAttribute('aria-describedby') || firstInvalid.getAttribute('aria-errormessage') || 'none' : 'n/a', invalidAria: [...f.querySelectorAll('[aria-invalid="true"]')].length, badEmail: emailErr, stillOnPage: location.pathname };
	});
	out.keyboard = await page.eval(() => [...document.querySelectorAll('main a[href], main input:not([type=hidden]), main textarea, main button')].filter((e) => e.offsetParent && e.tabIndex >= 0).map((e) => (e.querySelector('strong')?.textContent || e.textContent.trim() || e.name).slice(0, 22)).join(' > '));
	out.errors = page.consoleErrors.filter((e) => !/ppc|PayPal|client-token/i.test(e));
	return out;
};
