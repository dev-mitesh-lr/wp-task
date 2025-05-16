# Custom User Table for WordPress

A lightweight and extensible WordPress plugin that displays a remote user directory fetched from an external API via a custom frontend endpoint. Built with performance in mind using transient caching and Ajax rendering.

---

## 🚀 Features

- Custom endpoint for public-facing user directory.
- Admin settings page to configure:
  - Endpoint slug
  - External API URL
  - Cache duration (in hours)
- Transient-based caching for performance.
- Clear cache button in admin settings.
- Ajax-ready structure for future enhancements.
- Clean OOP architecture with Composer autoloading.

---

## 📁 Folder Structure

```
custom-user-table/
├── composer.json
├── custom-user-table.php
├── assets/
│   └── js/
│       └── script.js
├── includes/
│   ├── helpers.php
│   └── template-functions.php
├── src/
│   ├── EndpointHandler.php
│   ├── AjaxHandler.php
│   ├── Settings.php
│   └── views/
│       ├── user-table.php
│       └── user-detail.php
└── vendor/
```

---

## ⚙️ Installation

### Option 1: From GitHub Source

1. Clone or download this repository.
2. Run `composer install` to generate the autoload files.
3. Upload the `custom-user-table` folder to your WordPress `wp-content/plugins/` directory.
4. Activate the plugin from the WordPress dashboard.

### Option 2: Install from GitHub Release

1. Go to the **Releases** section.
2. Download the `.zip` distribution package (includes `vendor/` folder).
3. Upload and install it via WordPress Admin > Plugins > Add New > Upload Plugin.

---

## 🔧 Configuration

After activation, go to:

> **Settings → User Directory**

You can configure the following:

- **Custom Endpoint Slug:** The slug used in the frontend URL (e.g. `/user-directory/`)
- **API Endpoint URL:** The external API to fetch users from (must return JSON)
- **Cache Duration (in hours):** Cache user data to improve performance
- **Clear Cache:** Manually flush the cached data

---

## 🌐 Frontend Access

The user table will be accessible at:

```
https://yoursite.com/{your-custom-slug}/
```

Default slug: `/user-directory/`

---

## 🔒 Security

- Uses `wp_nonce_field` and `wp_verify_nonce` for Ajax requests.
- Caches only public API data using `transients`.

---

## 🧱 Developer Notes

- Plugin follows PSR-4 autoloading via Composer.
- Organized under `CUT` namespace.
- Extendable and well-commented for easy integration.

---

## 📦 Build & Distribution

To prepare a distributable plugin zip:

```bash
composer install --no-dev
zip -r custom-user-table.zip custom-user-table/
```

> Optional: You can upload the built `.zip` to GitHub under the **Releases** tab.

---

## 📄 License

MIT License – Feel free to use and modify.

---

## 👨‍💻 Author

Built by [Mitesh P]

---
