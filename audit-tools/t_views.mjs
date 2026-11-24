// Layout at each width: horizontal overflow, above-the-fold CTA, images; plus screenshots.
const SHOT = 'C:/Users/HP/Desktop/Claude Code/Rovakivo/screenshots/audit/';
const O = 'http://7bar.local';
const PAGES = {
	home: '/',
	shop: '/shop/',
	category: '/product-category/travel/',
	product: '/product/aturna-condimentum-mattis-pellentesque-nibh-tortor/',
	variable: '/product/eleifend-donec-pretium-vulputate-sapien-sagittis/',
	cart: '/cart/',
	checkout: '/checkout/',
	account: '/my-account/',
	about: '/about/',
	contact: '/contact/',
	privacy: '/privacy-policy-2/',
	search: '/?s=zzqqx&post_type=product',
	notfound: '/no-such-page-audit/',
};
const SIZES = [[320, 568], [375, 812], [390, 844], [768, 1024], [1024, 768], [1440, 900], [1920, 1080]];
const SHOTS = { 390: ['home', 'shop', 'product', 'cart', 'checkout', 'contact', 'notfound', 'search'], 1440: ['home', 'shop', 'product', 'cart', 'checkout', 'account', 'about', 'contact', 'notfound'] };

export default async ({ page }) => {
	await page.send('Network.setCookie', { name: 'rovakivo_consent', value: 'rejected', domain: '7bar.local', path: '/' });
	await page.viewport(1440, 900, false);
	await page.goto(O + '/?add-to-cart=66', 1000);
	const out = { overflow: [], fold: {}, images: {} };
	for (const [w, h] of SIZES) {
		await page.viewport(w, h, w < 768);
		for (const [key, path] of Object.entries(PAGES)) {
			await page.goto(O + path, 900);
			const r = await page.eval((key) => {
				const iw = document.documentElement.clientWidth;
				const res = { over: document.documentElement.scrollWidth - iw };
				if (res.over > 0) {
					res.culprits = [...document.querySelectorAll('body *')].filter((el) => { const b = el.getBoundingClientRect(); return b.width && b.right > iw + 1 && getComputedStyle(el).position !== 'fixed'; }).slice(0, 4).map((el) => el.tagName.toLowerCase() + '.' + [...el.classList].join('.') + ' r=' + Math.round(el.getBoundingClientRect().right));
				}
				const cta = { home: '.bar7-hero__actions .bar7-btn', shop: '.bar7-card, .product', category: '.bar7-card, .product', product: '.single_add_to_cart_button', variable: '.variations select', cart: '.checkout-button', checkout: '#billing_first_name' }[key];
				if (cta) {
					const el = document.querySelector(cta);
					res.cta = el ? Math.round(el.getBoundingClientRect().top) + '/' + innerHeight + (el.getBoundingClientRect().bottom <= innerHeight ? ' fold' : ' below') : 'missing';
				}
				res.h1 = document.querySelectorAll('h1').length;
				return res;
			}, key);
			if (r.over > 0) out.overflow.push({ w, key, ...r });
			(out.fold[key] ||= {})[w] = r.cta || ('h1=' + r.h1);
			if (SHOTS[w] && SHOTS[w].includes(key)) {
				await page.eval(async () => { window.scrollTo(0, document.body.scrollHeight); await new Promise((x) => setTimeout(x, 900)); window.scrollTo(0, 0); await new Promise((x) => setTimeout(x, 500)); return 1; });
				await page.screenshot(`${SHOT}${key}-${w}.png`, false);
			}
			if (w === 1440) {
				out.images[key] = await page.eval(() => {
					const imgs = [...document.querySelectorAll('img')];
					return {
						total: imgs.length,
						noAlt: imgs.filter((i) => !i.hasAttribute('alt')).map((i) => i.src.split('/').pop()),
						emptyAltNonDecor: imgs.filter((i) => i.getAttribute('alt') === '' && !i.closest('[aria-hidden="true"], .bar7-gallery__thumb, a[aria-label], .bar7-card')).map((i) => i.src.split('/').pop()).slice(0, 5),
						noDims: imgs.filter((i) => !i.getAttribute('width') || !i.getAttribute('height')).map((i) => i.src.split('/').pop()).slice(0, 5),
						broken: imgs.filter((i) => i.complete && i.naturalWidth === 0 && i.loading !== 'lazy').map((i) => i.src.split('/').pop()),
						oversized: imgs.filter((i) => i.naturalWidth && i.clientWidth && i.naturalWidth > i.clientWidth * 2.5).map((i) => i.src.split('/').pop() + ' ' + i.naturalWidth + '>' + i.clientWidth).slice(0, 5),
					};
				});
			}
		}
	}
	// Full-page captures of the main templates
	for (const [w, h] of [[390, 844], [1440, 900]]) {
		await page.viewport(w, h, w < 768);
		for (const key of ['home', 'product']) {
			await page.goto(O + PAGES[key], 900);
			await page.eval(async () => { for (let y = 0; y < document.body.scrollHeight; y += 400) { window.scrollTo(0, y); await new Promise((x) => setTimeout(x, 120)); } window.scrollTo(0, 0); await new Promise((x) => setTimeout(x, 600)); return 1; });
			await page.screenshot(`${SHOT}${key}-${w}-full.png`, true);
		}
	}
	out.errors = page.consoleErrors;
	return out;
};
