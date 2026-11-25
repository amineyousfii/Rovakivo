# Rovakivo

The Rovakivo online shop (bags and backpacks): a WordPress + WooCommerce site, developed locally with
Local (WP Engine) at `http://7bar.local`.

This repository holds **our own work only**:

| Folder | What it is |
|---|---|
| `app/public/wp-content/plugins/7bar-homepage/` | **Rovakivo Storefront** plugin: renders every front-end page (templates, CSS, JS, SEO, menus, consent banner, WooCommerce tweaks) |
| `brand/` | Page sources (block HTML), policy pages, reports (launch audit, pre-deployment check), favicons |
| `audit-tools/` | Headless-Chrome and crawl test scripts (`node cdp.mjs t_*.mjs`, `node crawl-predeploy.mjs`) and their results |
| `conf/` | Local's server templates, including our PHP (OPcache) and MySQL start-up fixes |

**Not in the repository** (see `.gitignore`): WordPress itself, third-party plugins and themes, uploaded images,
the database, backups, logs, screenshots and `wp-config.php`. Those are backed up separately (host backups and
database exports), never on GitHub.
