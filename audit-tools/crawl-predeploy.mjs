// Pre-deployment crawl (2026-11-24): every internal page + Yoast sitemaps; status, SEO tags, images (alt + status), absolute local links, scripts.
// Usage: node crawl-predeploy.mjs > crawl-predeploy.json
const ORIGIN = 'http://7bar.local';
const START = ['/', '/shop/', '/cart/', '/checkout/', '/my-account/', '/my-account/lost-password/', '/about/', '/contact/', '/privacy-policy-2/', '/shop/?on_sale=1', '/?s=green&post_type=product', '/this-page-does-not-exist-404-test/', '/robots.txt', '/sitemap_index.xml', '/wp-sitemap.xml', '/author/amine/', '/terms/', '/shipping/', '/returns/', '/sale/', '/?p=1', '/feed/'];
const SKIP = /(add-to-cart=|customer-logout|\/wp-admin|\/wp-login|wp-json|xmlrpc|\?replytocom|orderby=|min_price|max_price|\.(jpg|jpeg|png|webp|svg|ico|css|js|woff2?)($|\?))/i;
const seen = new Map(); const queue = [...START]; const external = new Map(); const imgs = new Map(); const scripts = new Set();
const attr = (tag, name) => { const m = tag.match(new RegExp('[\\s/]' + name + '\\s*=\\s*("([^"]*)"|\'([^\']*)\')', 'i')); return m ? (m[2] ?? m[3]) : null; };
while (queue.length && seen.size < 300) {
	const path = queue.shift(); if (seen.has(path)) continue;
	let res, body = '';
	try { res = await fetch(ORIGIN + path, { redirect: 'manual' }); body = await res.text(); } catch (e) { seen.set(path, { status: 'ERR ' + e.message }); continue; }
	const ct = res.headers.get('content-type') || '';
	const rec = { status: res.status, location: res.headers.get('location') || undefined, type: ct.split(';')[0] };
	if (ct.includes('xml')) {
		const locs = [...body.matchAll(/<loc>([^<]+)<\/loc>/g)].map((m) => m[1]);
		rec.locs = locs;
		for (const l of locs) { try { const u = new URL(l); if (u.origin === ORIGIN) { const p = u.pathname + u.search; if (!seen.has(p) && !queue.includes(p) && !/\.(jpg|png|webp)$/i.test(p)) queue.push(p); } } catch {} }
	}
	if (ct.includes('text/plain')) rec.text = body.slice(0, 400);
	if (ct.includes('text/html')) {
		const metas = {}; for (const tag of body.match(/<meta\b[^>]*>/gi) || []) { const k = attr(tag, 'property') || attr(tag, 'name'); if (k) (metas[k] ||= []).push(attr(tag, 'content')); }
		rec.title = (body.match(/<title>([^<]*)<\/title>/i) || [])[1] || null;
		rec.description = metas.description || null; rec.robots = metas.robots || null;
		rec.canonical = [...body.matchAll(/<link\b[^>]*rel=["']canonical["'][^>]*>/gi)].map((x) => attr(x[0], 'href'));
		rec.ogImage = metas['og:image'] || null; rec.ogDesc = metas['og:description'] ? 1 : 0;
		rec.h1 = [...body.matchAll(/<h1\b[^>]*>([\s\S]*?)<\/h1>/gi)].map((x) => x[1].replace(/<[^>]+>/g, '').replace(/\s+/g, ' ').trim());
		rec.jsonld = [...body.matchAll(/<script[^>]*application\/ld\+json[^>]*>([\s\S]*?)<\/script>/gi)].map((x) => { try { const j = JSON.parse(x[1]); const g = j['@graph'] || [j]; return g.map((n) => n['@type']).flat().join(','); } catch { return 'INVALID'; } });
		rec.absLocalLinks = (body.match(/href=["']http:\/\/7bar\.local[^"']*/g) || []).length;
		rec.noAlt = [...body.matchAll(/<img\b[^>]*>/gi)].map((x) => x[0]).filter((t) => attr(t, 'alt') === null).map((t) => (attr(t, 'src') || '').split('/').pop()).slice(0, 5);
		rec.sampleText = /lorem ipsum|\[ADD[: ,]|sample testimonials/i.test(body.replace(/<[^>]+>/g, ' ')) || undefined;
		rec.cookiez = /cookiez|cky-consent|cookieyes/i.test(body) || undefined;
		rec.phpNotice = /(<b>(Warning|Notice|Deprecated|Fatal error)<\/b>:)/.test(body) || undefined;
		for (const m of body.matchAll(/<script\b[^>]*\ssrc=["']([^"']+)["']/gi)) scripts.add(m[1].replace(/\?.*$/, '').replace(ORIGIN, ''));
		for (const m of body.matchAll(/<(?:img|source)\b[^>]*>/gi)) { for (const k of ['src', 'srcset']) { const v = attr(m[0], k); if (!v) continue; for (const part of v.split(',')) { const u = part.trim().split(/\s+/)[0]; if (u && !u.startsWith('data:')) imgs.set(u, imgs.get(u) || path); } } }
		for (const href of [...body.matchAll(/<a\b[^>]*\shref\s*=\s*["']([^"'#]+)[^"']*["']/gi)].map((x) => x[1].replace(/&amp;/g, '&'))) {
			let u; try { u = new URL(href, ORIGIN + path); } catch { continue; }
			if (u.origin === ORIGIN) { const p = u.pathname + u.search; if (!SKIP.test(p) && !seen.has(p) && !queue.includes(p)) queue.push(p); }
			else if (/^https?:/.test(u.protocol)) { const l = external.get(u.href) || new Set(); l.add(path); external.set(u.href, l); }
		}
	}
	seen.set(path, rec);
}
const imgStatus = {};
for (const [u, from] of imgs) { try { const abs = new URL(u, ORIGIN).href; if (!abs.startsWith(ORIGIN)) { imgStatus[u] = 'EXTERNAL from ' + from; continue; } const r = await fetch(abs, { method: 'HEAD' }); if (r.status !== 200) imgStatus[u] = r.status + ' from ' + from; } catch (e) { imgStatus[u] = 'ERR ' + e.message; } }
console.log(JSON.stringify({ pages: Object.fromEntries(seen), brokenImages: imgStatus, imagesChecked: imgs.size, external: Object.fromEntries([...external].map(([k, v]) => [k, [...v].slice(0, 3)])), scripts: [...scripts].sort() }, null, 1));
