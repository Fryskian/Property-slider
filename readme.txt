=== Property Slider Free ===
Contributors: fryskian
Stable tag: 1.1.0
Requires at least: 6.4
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight property CPT and responsive slider for Elementor Free or plain WordPress.

== Description ==

Property Slider Free adds a Properties custom post type, editable native WordPress fields, a responsive horizontal card slider, and an optional Elementor Free widget.

No ACF, Elementor Pro, slider library, or CDN is required.

Fields include price, price note, address, property type, bedrooms, bathrooms, parking, sale/rent, contact email, and phone. The property image uses WordPress's normal Featured Image.

== Installation ==

1. Upload the plugin ZIP in WordPress under Plugins > Add New Plugin > Upload Plugin, or copy the plugin folder to wp-content/plugins/.
2. Activate Property Slider Free.
3. Go to Properties > Add property.
4. Add a title, Featured Image, and the Property data fields.
5. Publish a few properties.
6. In Elementor Free, drag in the Property Slider widget, or use a Shortcode widget with: [property_slider]

Shortcode example:

[property_slider title="New to the market" limit="12" default="sale" show_filters="yes"]

`default` accepts `sale`, `rent`, or `all`.

== Limitations ==

* Theme or page-builder CSS can override the component. Broad global heading/link/button/grid/image rules and !important declarations can change or break the intended appearance. Small CSS overrides may be required on some themes.
* The HTML preview is exported from the actual renderer. Screenshots show a WordPress/Elementor Free test installation; results can differ across themes, fonts, widths and browsers.
* Single-property and archive pages use the active theme; this plugin does not ship full property templates.
* One Featured Image per card; no gallery or lightbox.
* Sale/rent filtering is client-side and only filters the properties already loaded by the shortcode limit.
* Price is display text, not a numeric searchable/sortable price model.
* Save/favorite uses browser localStorage only and does not sync to an account/server.
* Email/phone values are present in page markup if configured.
* No MLS/IDX, import/sync, map, advanced search, pagination, account favorites, enquiry form, CRM integration, or schema package.
* Basic accessibility support is present, but there has been no formal accessibility audit.
* No uninstall cleanup is performed; property posts/meta remain in the database if the plugin is removed.
* Not tested against every WordPress/Elementor/theme/cache/minifier combination. Test on staging first.

== MU-plugin option ==

For MU-plugin use, put the folder in wp-content/mu-plugins/ and add a loader PHP file directly in wp-content/mu-plugins/:

<?php require WPMU_PLUGIN_DIR . '/property-slider-free/property-slider-free.php';

== Changelog ==

= 1.1.0 =
* Four desktop cards, fractional tablet cards, 89vw mobile cards and 4:3 mobile photos.
* 48px contact/save targets, compact metadata and native scroll-snap.
* Scoped CSS and a limited cascade guard layer for common theme conflicts.
* Fix Elementor widget registration and dynamic initialization; respect reduced motion.
* Actual Elementor screenshots, Unsplash demo photographs and exported interactive HTML.
