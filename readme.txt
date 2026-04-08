=== WhatsApp Chat Button ===
Contributors: zignites
Tags: whatsapp, whatsapp chat, chat button, click to chat, contact button, floating button, analytics
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Convert visitors into WhatsApp leads with a customizable floating chat button.

== Description ==

WhatsApp Chat Button helps businesses start conversations through a lightweight floating WhatsApp button with basic routing and built-in click analytics.

Current MVP features:

* Floating frontend WhatsApp button
* `wa.me` click-to-chat link generation
* Pre-filled message template support
* Smart variables: `{page_title}`, `{url}`, `{site_name}`
* Button text, color, side, and delay settings
* Routing rules for pages, posts, categories, and a default fallback
* Lightweight click analytics inside wp-admin
* Privacy-conscious tracking that stores only page URL, click time, and device type

The plugin is built to stay review-safe and WordPress.org friendly:

* No remote assets
* No external analytics services
* No IP storage
* No cookies
* No upsells or hidden behavior

== Screenshots ==

1. Settings screen with general, design, variables, and routing sections.
2. Analytics screen with total clicks, clicks today, top pages, and device breakdown.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/whatsapp-chat-button` directory.
2. Activate the plugin through the `Plugins` screen in WordPress.
3. Go to `Settings > WhatsApp Chat Button`.
4. Enter your primary WhatsApp number in international format, for example `15551234567`.
5. Configure the default message, button design, and optional routing rules.
6. Save settings and visit the frontend to confirm the floating button appears.

== Frequently Asked Questions ==

= What format should the WhatsApp number use? =

Use the international format required by `wa.me`, for example `15551234567`. The plugin removes spaces, punctuation, and plus signs before saving.

= Which message variables are supported? =

The plugin currently supports:

* `{page_title}`
* `{url}`
* `{site_name}`

These placeholders are replaced at runtime when the button is rendered.

= How do routing rules work? =

Rules are evaluated in this order:

1. Page
2. Post
3. Category
4. Default fallback

The first matching rule wins. If no matching routed number is found, the plugin falls back to the default rule and then to the primary WhatsApp number when appropriate.

= What analytics data is stored? =

The plugin stores only the current page URL, the click timestamp, and a simple `mobile` or `desktop` device label. It does not store IP addresses, cookies, or personal data.

= Does the plugin load assets everywhere? =

No. Frontend assets load only when the button can actually render, and admin assets load only on the plugin settings screen.

== Changelog ==

= 0.1.0 =

* Initial public MVP release.
* Added floating WhatsApp button output.
* Added configurable number, message, text, color, side, and delay settings.
* Added smart message variables for page title, URL, and site name.
* Added routing rules for pages, posts, categories, and a default fallback.
* Added lightweight click tracking and analytics reporting.
* Added release hardening, validation, and WordPress.org readiness improvements.
