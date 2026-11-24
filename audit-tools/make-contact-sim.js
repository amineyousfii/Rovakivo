// Builds a simulated editor canvas for the Contact page: WordPress's own editor style processing,
// the page content as the editor renders it, and the site header/footer preview script.
const fs = require('fs');
const S = __dirname;
const scripts = fs.readFileSync(S + '/wp-scripts.html', 'utf8').replace(/Warning: PHP Startup[^\n]*\n?/g, '');
const d = JSON.parse(fs.readFileSync(S + '/editor-data-contact.json', 'utf8'));
const inner = `<!doctype html><html><head><meta charset="utf-8"></head><body class="editor-styles-wrapper">${scripts}<div class="editor-visual-editor__post-title-wrapper" style="margin-top:4rem"><h1 class="editor-post-title">Contact</h1></div><div class="is-root-container block-editor-block-list__layout wp-block-post-content">${d.html}</div><script>window.__rej=(function(){ var out=wp.blockEditor.transformStyles(${JSON.stringify(d.editor_css)}, ".editor-styles-wrapper", { ignoredSelectors:[/\\.editor-styles-wrapper/gi] }); out.forEach(function(c){ var s=document.createElement("style"); s.textContent=c||""; document.head.appendChild(s); }); return out.filter(function(c){return !c}).length; })();<\/script></body></html>`;
const esc = inner.replace(/&/g, '&amp;').replace(/"/g, '&quot;');
const outer = `<!doctype html><html><head><meta charset="utf-8"><style>body{margin:0}iframe{display:block;width:1440px;height:2000px;border:0}</style></head><body>
<script>window.wp={apiFetch:function(){ return Promise.resolve(${JSON.stringify(d.chrome)}); }, data:{ select:function(){ return { getCurrentPostType:function(){return "page"}, getCurrentPostId:function(){return 117} }; } } };<\/script>
<iframe name="editor-canvas" srcdoc="${esc}"></iframe>
<script src="file:///C:/Users/HP/Local%20Sites/7bar/app/public/wp-content/plugins/7bar-homepage/assets/js/editor-chrome.js"><\/script></body></html>`;
fs.writeFileSync(S + '/editor-sim-contact.html', outer);
console.log('sim written');
