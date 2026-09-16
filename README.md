# Property Slider Free

A small, copyleft WordPress property-listing slider built for a simple use case: editable property data and a card carousel without Elementor Pro, ACF, JetEngine, or a paid slider plugin.

It registers a `Properties` custom post type, stores the listing fields as native WordPress post meta, and renders the cards either through a shortcode or an Elementor Free widget.

![Property Slider preview](preview/property-slider-preview.png)

## Features

- `Properties` custom post type.
- Native WordPress fields for price, price note, address, property type, bedrooms, bathrooms, parking, sale/rent, email, and phone.
- Uses the normal Featured Image as the property image.
- Responsive horizontal card slider.
- `For sale` / `To rent` client-side filtering.
- Native Elementor widget when Elementor is active. Elementor Pro is **not** required.
- `[property_slider]` shortcode, so Elementor is optional.
- No ACF or other custom-field plugin required.
- No carousel dependency or CDN: CSS scroll-snap plus a small amount of vanilla JavaScript.
- Simple browser-local `Save` button using `localStorage`.

## Installation

### Option A: install the GitHub ZIP

1. On GitHub choose **Code → Download ZIP**.
2. In WordPress go to **Plugins → Add New Plugin → Upload Plugin**.
3. Select the downloaded ZIP and install it.
4. Activate **Property Slider Free**.
5. Go to **Properties → Add property**.
6. Enter the property data and set a **Featured Image**.
7. Publish several properties.
8. Add the slider to a page using one of the methods below.

### Option B: install the folder manually

1. Copy this repository into `wp-content/plugins/property-slider-free/`.
2. Activate **Property Slider Free** in WordPress.

## Elementor Free

If Elementor is active, search the Elementor widget panel for **Property Slider** and drag it onto the page.

The widget currently exposes:

- Heading
- Maximum number of properties
- Default tab
- Show/hide sale/rent tabs

You can also use Elementor's normal **Shortcode** widget with:

```text
[property_slider]
```

## Shortcode

Basic:

```text
[property_slider]
```

With options:

```text
[property_slider title="New to the market" limit="12" default="sale" show_filters="yes"]
```

`default` accepts `sale`, `rent`, or `all`.

## Editing a property

Each property is a normal WordPress post under **Properties**. The plugin adds a **Property data** box with the listing fields. You can change the data later and the slider will use the new values automatically.

The property photo comes from WordPress's **Featured Image** field.

## Styling

The front-end CSS lives in:

```text
assets/property-slider.css
```

Most selectors are scoped below `.psf` to reduce conflicts, and the component deliberately inherits the site's font. It is intended to be easy to fork and restyle rather than to provide a large visual-settings UI.

## Known limitations / things to know

This is deliberately a small plugin rather than a full real-estate platform.

- **Theme CSS can change or break the appearance.** Themes and page builders sometimes apply broad rules to headings, links, buttons, grids, images, or `box-sizing`, occasionally with `!important`. The CSS is namespaced to reduce this risk, but it cannot guarantee an identical rendering on every theme. Some sites will need small CSS overrides.
- **The preview is a design target, not a pixel-perfect guarantee.** Font availability, Elementor container width, theme spacing, browser rendering, and image proportions all affect the result.
- The plugin does **not** provide a custom single-property or property-archive template. Clicking a card opens the normal WordPress single CPT URL and your active theme controls that page's layout.
- One Featured Image is supported per card. There is no gallery/lightbox implementation.
- `For sale` / `To rent` filtering happens in the browser and only filters the properties already loaded by the shortcode's `limit`; it is not an AJAX search or database filter UI.
- The price is stored as display text so any currency format can be used. That also means there is no numeric price sorting or range search.
- The `Save` heart is **local to that browser/device** through `localStorage`. It is not tied to a WordPress account and is not synchronized to the server.
- Contact email and phone links are rendered into the page source when supplied. Do not treat that as protection against scraping/spam.
- There is no MLS/IDX feed, property import, synchronization, map/geocoding, advanced search, pagination, favorites account, enquiry form, structured-data/schema package, or CRM integration.
- Accessibility has basic keyboard/focus/ARIA support, but this small project has not had a formal accessibility audit.
- There is no uninstall cleanup routine. Removing the plugin does not automatically delete the `psf_property` posts or their post meta, to avoid accidental data loss.
- It has not been tested against every WordPress, Elementor, browser, caching/minification, and theme combination. Test it on a staging site before relying on it in production.

If another theme/plugin conflicts with the component, inspect the computed styles first. In most cases the fix should be a small selector override in the theme or a fork of `assets/property-slider.css`.

## Data model

The custom post type is:

```text
psf_property
```

The plugin stores these post-meta keys:

```text
_psf_price
_psf_price_note
_psf_address
_psf_property_type
_psf_beds
_psf_baths
_psf_parking
_psf_listing_type
_psf_email
_psf_phone
```

This keeps the data in ordinary WordPress storage rather than locking it into Elementor content.

## MU-plugin use

It is packaged as a normal plugin because that is simpler for most users. If you want it as an MU plugin, place the plugin folder under `wp-content/mu-plugins/` and add a loader PHP file directly inside `wp-content/mu-plugins/`:

```php
<?php
require WPMU_PLUGIN_DIR . '/property-slider-free/property-slider-free.php';
```

## License

`GPL-2.0-or-later`.

You may use, modify, redistribute, and sell the software under the terms of the GNU General Public License. Distributed derivative works remain subject to the GPL's copyleft requirements.

See [LICENSE](LICENSE).
