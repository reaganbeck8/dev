# How to Transport Claude Code to Another Machine via Google Drive

## What Is "Claude" in This Context?

Claude Code is the CLI tool you run in your terminal. It doesn't have a brain of its own that lives on your machine — it calls the Claude API each time. But it DOES store your **preferences, memories, instructions, and skills** locally. That's what makes it feel like "your Claude" — and that's what we need to transport.

## What Makes Your Claude "Yours"

There are two sets of files that make Claude behave the way you've set it up:

### 1. Your Claude Profile (global — applies to all projects)
Located at `~/.claude/` on your Mac, which is:
```
/Users/photohire/.claude/
```

Key files inside:
| File/Folder | What It Does |
|-------------|-------------|
| `CLAUDE.md` | Your global instructions (Karpathy guidelines, behavioral rules) |
| `settings.json` | Permissions, hooks, environment variables |
| `settings.local.json` | Model preference (e.g., Opus) |
| `skills/` | Custom skills like `/kap` (karpathy-guidelines) |
| `projects/-Users-photohire/memory/` | Memories about you, your preferences, project context |

### 2. Your Project Files (specific to Medical Rekordz CMS)
Located in the project folder itself:
| File | What It Does |
|------|-------------|
| `CLAUDE.md` | Project-specific instructions |
| `PROGRESS.md` | Our version control — where we left off, what's done, what's next |

---

## Step-by-Step: Upload to Google Drive

### On Your Work Mac

**Step 1 — Zip your Claude profile:**
Open Terminal and run:
```bash
cd /Users/photohire
zip -r ~/Desktop/my-claude-profile.zip .claude/ -x "*.DS_Store"
```
This creates `my-claude-profile.zip` on your Desktop.

**Step 2 — Zip the CMS project:**
```bash
cd /Users/photohire/Downloads/medical-rekordz-cms/medical-rekordz-cms
zip -r ~/Desktop/medical-rekordz-cms.zip . -x "*.DS_Store"
```
This creates `medical-rekordz-cms.zip` on your Desktop.

**Step 3 — Upload both zips to Google Drive:**
- Go to drive.google.com
- Create a folder called `Claude Transport`
- Upload both zip files:
  - `my-claude-profile.zip`
  - `medical-rekordz-cms.zip`

---

### On Your Windows 10 PC at Home

**Step 1 — Install Claude Code** (if not already installed):
- Install Node.js from https://nodejs.org (LTS version)
- Open Command Prompt or PowerShell and run:
```
npm install -g @anthropic-ai/claude-code
```
- Log in with your Anthropic account:
```
claude auth login
```

**Step 2 — Download both zips from Google Drive**
- Go to drive.google.com → `Claude Transport` folder
- Download both zips to your Downloads folder

**Step 3 — Extract the CMS project:**
- Right-click `medical-rekordz-cms.zip` → Extract All
- Put it somewhere you'll remember, e.g.:
```
C:\Users\YourName\projects\medical-rekordz-cms\
```

**Step 4 — Extract your Claude profile:**
- Right-click `my-claude-profile.zip` → Extract All
- This gives you a `.claude` folder
- Move its CONTENTS into your home directory's `.claude` folder:
```
C:\Users\YourName\.claude\
```
If `.claude` doesn't exist yet, just move the whole folder there.

**Important:** The memory folder path changes between machines. On Mac it was:
```
.claude/projects/-Users-photohire/memory/
```
On Windows, Claude will create a new path based on where your project lives. When you first run Claude in the project, it'll create something like:
```
.claude/projects/-C-Users-YourName-projects-medical-rekordz-cms/memory/
```
Copy all the `.md` files from the Mac memory folder into this new Windows memory folder after it's created.

**Step 5 — Run Claude Code:**
```
cd C:\Users\YourName\projects\medical-rekordz-cms
claude
```

**Step 6 — First message to Claude:**
Just say:
> Read PROGRESS.md and pick up where we left off.

Claude will read the progress file, see the memories, load your instructions, and behave exactly like it does on your work Mac.

---

## Keeping Things in Sync (Ongoing)

After working on either machine:
1. Re-zip the project folder (or just the changed files)
2. Re-zip `~/.claude/` (or just the memory folder)
3. Upload to Google Drive, replacing the old zips

**The most important file to keep synced is `PROGRESS.md`** — it's our handoff doc. As long as that file is current, Claude can always pick up where it left off, even without the memory files.

---

## TL;DR

| What | Where on Mac | Where on Windows | Why |
|------|-------------|-----------------|-----|
| Claude profile | `~/.claude/` | `C:\Users\YourName\.claude\` | Your instructions, skills, memories |
| CMS project | wherever you extracted it | wherever you extract it | The actual code + PROGRESS.md |
| Google Drive | `Claude Transport` folder | same | The bridge between machines |

That's it. Two zips up, two zips down, and Claude picks up right where you left off.
