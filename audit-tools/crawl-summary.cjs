const j = require(process.argv[2]); const P = j.pages;
console.log('pages', Object.keys(P).length, 'images checked', j.imagesChecked);
for (const [p, r] of Object.entries(P)) {
	const f = [];
	if (r.status !== 200) f.push('STATUS ' + r.status + (r.location ? ' → ' + r.location.replace('http://7bar.local', '') : ''));
	if (r.type === 'text/html' && r.status === 200) {
		if (!r.description) f.push('NO DESC'); else f.push('desc ' + r.description[0].length);
		if (!r.h1 || r.h1.length !== 1) f.push('h1×' + (r.h1 || []).length);
		if (r.noAlt.length) f.push('img no alt: ' + r.noAlt.join(' '));
		if (r.sampleText) f.push('SAMPLE/PLACEHOLDER TEXT');
		if (r.cookiez) f.push('COOKIEZ');
		if (r.phpNotice) f.push('PHP NOTICE');
		if (r.jsonld.includes('INVALID')) f.push('bad JSON-LD');
		if (!r.ogImage) f.push('no og:image');
		if (!r.canonical.length) f.push('no canonical'); else if ((r.canonical[0]||"").replace('http://7bar.local', '') !== p) f.push('canon ' + (r.canonical[0]||"").replace('http://7bar.local', ''));
	}
	const robots = (r.robots || [''])[0] || '';
	console.log(p.slice(0, 62).padEnd(62), (robots.startsWith('noindex') ? 'NOINDEX' : robots ? 'index' : '-').padEnd(8), '|', (r.title || '').slice(0, 50).padEnd(50), '|', f.join('; '), r.locs ? ' locs=' + r.locs.map((l) => l.replace('http://7bar.local', '')).join(' ') : '', r.text ? JSON.stringify(r.text) : '');
}
console.log('broken images:', JSON.stringify(j.brokenImages));
console.log('external links:', Object.keys(j.external).join(' '));
console.log('jsonld types (home):', JSON.stringify(P['/'].jsonld), ' product:', JSON.stringify(Object.entries(P).find(([k]) => k.startsWith('/product/'))[1].jsonld));
