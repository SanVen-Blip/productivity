# SanvenDocs

A Google Docs-inspired productivity web app built with **Laravel 13**, **Tailwind CSS v4**, and **Vite**.

## Progress

| Feature | Status |
|---|---|
| User authentication (Login / Register / Logout) | ✅ Done |
| Document dashboard (list, create, delete) | ✅ Done |
| Document editor (contenteditable + toolbar) | ✅ Done |
| Auto-save (3s idle) + manual save (Ctrl+S) | ✅ Done |
| Rich text formatting (Bold, Italic, Underline, Headings, Lists) | ✅ Done |
| Real-time save status indicator | ✅ Done |
| Responsive layout | ✅ Done |
| Collaborative editing | 🔜 Planned |
| Document sharing / permissions | 🔜 Planned |
| Export to PDF / DOCX | 🔜 Planned |
| Comments & suggestions | 🔜 Planned |
| Version history | 🔜 Planned |

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
```

## Tech Stack

- **Backend:** Laravel 13 (PHP 8.3)
- **Frontend:** Blade + Tailwind CSS v4 + Vite
- **Database:** SQLite (default) / MySQL
