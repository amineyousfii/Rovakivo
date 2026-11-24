Launch-audit test scripts (2026-11-20). Need Node 22+ and Chrome. The site must be running in Local.
Run from this folder:  node cdp.mjs t_views.mjs   (or t_flows / t_perf / t_sticky / t_fix / t_qty / t_cart2)
                       node crawl.mjs > crawl.json
Screenshots go to ..\screenshots\audit\. The *.json files are the results used in the audit report.
No script places an order: checkout is only submitted with empty fields (validation test).
