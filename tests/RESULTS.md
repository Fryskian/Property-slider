# Validation — 2026-09-16

Final run: WordPress **6.8.8**, PHP **8.3** (Playground/SQLite), Elementor Free **4.2.4**, Hello Elementor **3.5.1**, Chromium **149** through Playwright. Actual native Elementor widget, six seeded listings and four locally stored Unsplash photographs. No Elementor Pro.

| Viewport | Track | Card | Fully visible | Photo ratio | Action height |
| --- | --- | --- | --- | --- | --- |
| 1440px | 1344px | 319.5px | 4 | 1.72:1 | 48px |
| 1024px | 928px | 381.27px | 2 + partial next | 1.72:1 | 48px |
| 768px | 672px | 272.33px | 2 + partial next | 1.72:1 | 48px |
| 390px | 374px | 347.09px (89vw) | 1 + 14.91px hint | 4:3 | 48px |
| 320px | 304px | 284px (88.75vw) | 1 + 8px hint | 4:3 | 48px |

Passed: no horizontal document overflow, no broken property photographs, one metadata row, mobile arrows hidden, no excess Elementor widget height, desktop next arrow and end-disabled state, sale/rent filtering and scroll reset, transparent focused filter background, Save toggling and persistence after reload, independent instances, initialization through Elementor's real frontend hook after inserting another widget.

Injected late CSS for oversized buttons, heading margins/fonts, purple links, important image dimensions and important box sizing inside the component. The component's image crop, action dimensions and layout survived. This does not prove immunity to arbitrary styles on ancestors; an important content-box rule on the surrounding Elementor container can still change its overall width.

A **synthesized Chromium touch gesture** advanced the track to 359px for a 359.09px card step (under 0.1px snap difference). A vertical touch gesture starting over the slider scrolled the page. This is browser input evidence, not a physical phone test. The screenshots in `output/playwright/` are from the actual installation.

PHP syntax checks, JavaScript syntax check and `git diff --check` passed. Impeccable's detector reported only its generic Arial warning for the standalone demo; inherited site typography was intentionally retained. The standalone HTML was also opened in Chromium: filtering passed and all image sources loaded. The static HTML is exported from the real PHP-rendered page, not a separately maintained card mockup.

The first installation auto-updated WordPress during testing and briefly entered maintenance mode. The final run used a fresh installation with automatic updates and cron disabled; only the final 6.8.8 run is reported above. Its browser console contained jQuery Migrate informational messages and no errors.

Not covered: physical iOS/Android devices, Firefox/Safari, the Elementor editor UI itself, every theme, production traffic, or a formal accessibility audit. The rerender test invokes the actual Elementor frontend hook; it does not claim an editor drag-and-drop session.
