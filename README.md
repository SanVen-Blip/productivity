# SanvenDocs

A **Google Docs-inspired productivity web app** built with Laravel 13, Tailwind CSS v4, and Vite.

![Laravel](https://img.shields.io/badge/Laravel-13-red?logo=laravel) ![PHP](https://img.shields.io/badge/PHP-8.3-blue?logo=php) ![Tailwind](https://img.shields.io/badge/Tailwind-v4-06B6D4?logo=tailwindcss)

---

## Features

### Documents
- ✅ Create, edit, rename, duplicate, delete documents
- ✅ Rich text editor — bold, italic, underline, strikethrough, headings H1–H3, lists, alignment, font size
- ✅ Insert tables (custom rows × columns)
- ✅ Embed images (upload or base64 fallback)
- ✅ Insert hyperlinks
- ✅ Auto-save (3s idle) + manual save (Ctrl+S)
- ✅ Version history — up to 30 snapshots, restore any version
- ✅ Export / Print as PDF via browser

### Organisation
- ✅ Folder system — assign documents to folders, filter by folder
- ✅ Star / favourite documents
- ✅ Tag labels (up to 10 per document)
- ✅ Sort by last modified / created / title
- ✅ Full-text search (title + content)

### Sharing
- ✅ Public share link — toggle on/off, anyone can view without login
- ✅ Copy-to-clipboard share URL

### Templates
- ✅ Blank Document
- ✅ Meeting Notes
- ✅ To-Do List
- ✅ Project Brief
- ✅ Quick Notes

### UI / UX
- ✅ Dark mode (persisted, no flash on load, follows system preference)
- ✅ Responsive — mobile sidebar drawer
- ✅ Document info panel (stats, actions, share, tags)
- ✅ Find & Replace (Ctrl+H)
- ✅ Toast notifications
- ✅ Keyboard shortcuts (Ctrl+S, B, I, U, H, ?)
- ✅ Word / character count + estimated read time
- ✅ Onboarding welcome screen for new users
- ✅ Custom error pages (404, 403, 419, 500)

### Auth & Profile
- ✅ Register / Login / Logout
- ✅ Profile settings — update name, email, password
- ✅ Danger zone (account deletion placeholder)

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13 (PHP 8.3) |
| Frontend | Blade + Tailwind CSS v4 |
| Build tool | Vite 8 |
| Database | SQLite (default) / MySQL / PostgreSQL |
| Storage | Laravel local disk (images) |

---

## Setup

```bash
# 1. Install PHP dependencies
composer install

# 2. Create .env
cp .env.example .env
php artisan key:generate

# 3. Run migrations
php artisan migrate

# 4. Create storage symlink (for image uploads)
php artisan storage:link

# 5. Install & build frontend
npm install
npm run build

# 6. Start dev server
php artisan serve
```

Then open [http://localhost:8000](http://localhost:8000).

### Development (hot reload)

```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

---

## Keyboard Shortcuts

| Shortcut | Action |
|---|---|
| `Ctrl + S` | Save document |
| `Ctrl + B` | Bold |
| `Ctrl + I` | Italic |
| `Ctrl + U` | Underline |
| `Ctrl + H` | Find & Replace |
| `Ctrl + Z` | Undo |
| `Ctrl + Y` | Redo |
| `?` | Show shortcuts (dashboard) |

---


---

## License

MIT
