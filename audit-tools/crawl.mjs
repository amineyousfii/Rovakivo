// Crawls http://7bar.local, following internal links, and records SEO tags and link targets.
// Usage: node crawl.mjs > crawl.json
const ORIGIN = 'http://7bar.local';
const START = ['/', '/shop/', '/cart/', '/checkout/', '/my-account/', '/my-account/lost-password/', '/about/', '/contact/', '/privacy-policy-2/', '/?s=green&post_type=product', '/this-page-does-not-exist-404-test/', '/robots.txt', '/wp-sitemap.xml'];
const SKIP = /(add-to-cart=|customer-logout|\/wp-admin|\/wp-login|\/feed\/?|wp-json|xmlrpc|\?replytocom|\.(jpg|jpeg|png|webp|svg|ico|css|js|woff2?)($|\?))/i;
const seen = new Map();
const queue = [...START];
const external = new Map();

function attr(tag, name) {
	const m = tag.match(new RegExp(`${name}\\s*=\\s*("([^"]*)"|'([^']*)')`, 'i'));
	return m ? (m[2] ?? m[3]) : null;
}
function metas(html) {
	const out = {};
	for (const tag of html.match(/<meta\b[^>]*>/gi) || []) {
		const key = attr(tag, 'property') || attr(tag, 'name');
		if (key) { (out[key] ||= []).push(attr(tag, 'content')); }
	}
	return out;
}

while (queue.length && seen.size < 400) {
	const path = queue.shift();
	if (seen.has(path)) continue;
	let res, html = '';
	try {
		res = await fetch(ORIGIN + path, { redirect: 'manual' });
		html = await res.text();
	} catch (e) {
		seen.set(path, { status: 'ERR ' + e.message });
		continue;
	}
	const rec = { status: res.status, location: res.headers.get('location') };
	if ((res.headers.get('content-type') || '').includes('text/html')) {
		const m = metas(html);
		const links = [...html.matchAll(/<a\b[^>]*href\s*=\s*["']([^"'#]+)[^"']*["'][^>]*>/gi)].map((x) => x[1].replace(/&amp;/g, '&'));
		rec.title = (html.match(/<title>([^<]*)<\/title>/i) || [])[1] || null;
		rec.description = m.description || null;
		rec.robots = m.robots || null;
		rec.canonical = [...html.matchAll(/<link\b[^>]*rel=["']canonical["'][^>]*>/gi)].map((x) => attr(x[0], 'href'));
		rec.og = Object.fromEntries(Object.entries(m).filter(([k]) => k.startsWith('og:') || k.startsWith('twitter:')).map(([k, v]) => [k, v.length > 1 ? v : v[0]]));
		rec.h1 = [...html.matchAll(/<h1\b[^>]*>([\s\S]*?)<\/h1>/gi)].map((x) => x[1].replace(/<[^>]+>/g, '').replace(/\s+/g, ' ').trim());
		rec.icons = [...html.matchAll(/<link\b[^>]*rel=["'](?:icon|apple-touch-icon|shortcut icon|manifest)["'][^>]*>/gi)].length;
		rec.jsonld = [...html.matchAll(/<script[^>]*application\/ld\+json[^>]*>/gi)].length;
		for (const href of links) {
			let u;
			try { u = new URL(href, ORIGIN + path); } catch { continue; }
			if (u.origin === ORIGIN) {
				const p = u.pathname + u.search;
				if (!SKIP.test(p) && !seen.has(p) && !queue.includes(p)) queue.push(p);
			} else if (/^https?:/.test(u.protocol)) {
				const list = external.get(u.origin) || new Set();
				list.add(path);
				external.set(u.origin, list);
			}
		}
	}
	seen.set(path, rec);
}

console.log(JSON.stringify({
	pages: Object.fromEntries(seen),
	external: Object.fromEntries([...external].map(([k, v]) => [k, [...v].slice(0, 5)])),
}, null, 1));
