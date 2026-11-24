# Git guide for Rovakivo

**Git** keeps the history of your files on your computer. Every saved version is a *commit*.
**GitHub** keeps a copy of that history online, so it's backed up and you can see every change.

Run these commands in a terminal **opened in the `Rovakivo` folder**: in File Explorer, open the folder,
right-click an empty area and choose **Open in Terminal**. In VS Code, use **Terminal → New Terminal**.

---

## 1. How this repository was created (done once, 25 Nov 2026)

```powershell
git --version
```
Checks that Git is installed and shows its version.

```powershell
git config --global user.name
git config --global user.email
```
Shows the name and email Git writes into every commit. They were already set on this PC.
To change them: `git config --global user.name "Your Name"`.

**The `.gitignore` file** was written first. It lists what Git must leave out: WordPress itself, other
plugins, images, database dumps, backups, logs, screenshots and `wp-config.php`. Git reads it automatically.

```powershell
git init -b main
```
Turns the folder into a Git repository, with a main branch called `main`. It creates a hidden `.git` folder
where the history is kept.

```powershell
git status
```
Lists what has changed and what Git isn't tracking yet. Run it often: it's always safe.

```powershell
git status --ignored
```
Same, but also lists what `.gitignore` leaves out. Useful to double-check nothing private is included.

```powershell
git add -A
```
Stages every new, changed and deleted file, except ignored ones. *Staging* means "include this in the next
commit". To stage one file: `git add path/to/file`.

```powershell
git commit -m "Initial commit: Rovakivo storefront plugin, page sources, tools and server templates"
```
Saves the staged files as a new version, with a message saying what changed.

```powershell
git remote add origin https://github.com/YOUR-USERNAME/rovakivo.git
```
Tells Git where the online copy lives. `origin` is the usual name for it. The private repository was first
created on github.com (**New repository** → name → **Private** → no README).

```powershell
git push -u origin main
```
Uploads your commits to GitHub. `-u` remembers `origin main`, so next time a plain `git push` is enough.
The first push opened a browser window to sign in to GitHub (Git Credential Manager). After that, Windows
remembers the sign-in.

---

## 2. Everyday workflow

After you (or Claude) change something:

```powershell
git status                      # what changed?
git diff                        # show the exact lines that changed
git add -A                      # stage all changes
git commit -m "Short description of the change"
git push                        # upload to GitHub
```

Good commit messages say *what* and *why*, e.g. `Add shipping page link to the footer`.

---

## 3. Looking at history

```powershell
git log --oneline               # one line per commit, newest first
git log --oneline -- brand/     # only commits that touched the brand folder
git show <commit-id>            # what a commit changed (copy the id from git log)
```

---

## 4. Undoing things (safely)

```powershell
git restore path/to/file        # throw away changes to a file since the last commit
git restore --staged path/file  # un-stage a file (keeps your changes)
git revert <commit-id>          # make a new commit that undoes an old one (history stays intact)
```

`git restore` on a changed file discards your unsaved edits to it, so check `git diff` first.
Avoid `git reset --hard` and `git push --force` unless you know exactly why: they can delete history.

---

## 5. Rules for this repository

- Keep it **private**.
- Never commit `wp-config.php`, database exports (`.sql`), the `backups` folder or uploaded images.
  `.gitignore` already blocks them; `git status --ignored` lets you check.
- Commit small, related changes together, and push at the end of a work session.
- Git isn't a backup of the **website content**: products, pages, orders and settings live in the database,
  and images in `uploads`. Back those up with the host's backups and database exports.
