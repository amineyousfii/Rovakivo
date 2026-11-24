// Minimal headless-Chrome driver over the DevTools protocol (Node 22 global WebSocket).
// Usage: node cdp.mjs <script.mjs>   — the script default-exports async ({ page }) => result
import { spawn } from 'node:child_process';
import { mkdtempSync, writeFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';
import { pathToFileURL } from 'node:url';

const CHROME = 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const port = 9300 + Math.floor(Math.random() * 500);
const profile = mkdtempSync(join(tmpdir(), 'cdp-'));
const proc = spawn(CHROME, ['--headless=new', `--remote-debugging-port=${port}`, `--user-data-dir=${profile}`, '--no-first-run', '--disable-gpu', '--hide-scrollbars', 'about:blank'], { stdio: 'ignore' });
const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

let wsUrl;
for (let i = 0; i < 150 && !wsUrl; i++) {
	await sleep(200);
	try {
		const list = await (await fetch(`http://127.0.0.1:${port}/json/list`)).json();
		wsUrl = list.find((t) => t.type === 'page')?.webSocketDebuggerUrl;
	} catch {}
}
const ws = new WebSocket(wsUrl);
await new Promise((r) => ws.addEventListener('open', r));
let id = 0;
const pending = new Map();
const listeners = [];
ws.addEventListener('message', (ev) => {
	const msg = JSON.parse(ev.data);
	if (msg.id && pending.has(msg.id)) {
		const { res, rej } = pending.get(msg.id);
		pending.delete(msg.id);
		msg.error ? rej(new Error(msg.error.message)) : res(msg.result);
	} else if (msg.method) {
		listeners.forEach((l) => l(msg));
	}
});
const send = (method, params = {}) => new Promise((res, rej) => { const i = ++id; pending.set(i, { res, rej }); ws.send(JSON.stringify({ id: i, method, params })); });
await send('Page.enable');
await send('Runtime.enable');
await send('Network.enable');

const consoleErrors = [];
listeners.push((m) => {
	if (m.method === 'Runtime.exceptionThrown') consoleErrors.push(m.params.exceptionDetails.exception?.description || m.params.exceptionDetails.text);
	if (m.method === 'Runtime.consoleAPICalled' && m.params.type === 'error') consoleErrors.push(m.params.args.map((a) => a.value ?? a.description).join(' '));
	if (m.method === 'Log.entryAdded' && m.params.entry.level === 'error') consoleErrors.push(m.params.entry.text + ' ' + (m.params.entry.url || ''));
});
await send('Log.enable');

const page = {
	send,
	consoleErrors,
	async viewport(width, height, mobile = width < 768) {
		await send('Emulation.setDeviceMetricsOverride', { width, height, deviceScaleFactor: 1, mobile });
	},
	async goto(url, wait = 1500) {
		const loaded = new Promise((r) => { const l = (m) => { if (m.method === 'Page.loadEventFired') { listeners.splice(listeners.indexOf(l), 1); r(); } }; listeners.push(l); });
		await send('Page.navigate', { url });
		await Promise.race([loaded, sleep(20000)]);
		await sleep(wait);
	},
	async eval(fn, ...args) {
		const expr = `(${fn.toString()})(...${JSON.stringify(args)})`;
		const r = await send('Runtime.evaluate', { expression: expr, awaitPromise: true, returnByValue: true, userGesture: true });
		if (r.exceptionDetails) throw new Error(r.exceptionDetails.exception?.description || r.exceptionDetails.text);
		return r.result.value;
	},
	async screenshot(file, fullPage = false) {
		let clip;
		if (fullPage) {
			const m = await send('Page.getLayoutMetrics');
			clip = { x: 0, y: 0, width: m.cssContentSize.width, height: Math.min(m.cssContentSize.height, 16000), scale: 1 };
		}
		const r = await send('Page.captureScreenshot', { format: 'png', captureBeyondViewport: fullPage, ...(clip ? { clip } : {}) });
		writeFileSync(file, Buffer.from(r.data, 'base64'));
	},
	sleep,
	listeners,
	async settle(min = 1500, max = 25000) {
		await sleep(min);
		const t0 = Date.now();
		while (Date.now() - t0 < max) {
			try {
				const r = await send('Runtime.evaluate', { expression: 'document.readyState === "complete" && !!document.body', returnByValue: true });
				if (r.result && r.result.value === true) { await sleep(800); return; }
			} catch {}
			await sleep(300);
		}
	},
};

let out;
try {
	const mod = await import(pathToFileURL(process.argv[2]).href);
	out = await mod.default({ page });
} catch (e) {
	out = { error: e.message };
}
console.log(JSON.stringify(out, null, 1));
ws.close();
proc.kill();
process.exit(0);
