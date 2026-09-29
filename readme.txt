=== Floating Call Button ===
Contributors: mmhdih
Tags: call button, whatsapp, telegram, floating button, contact
Requires at least: 5.6
Tested up to: 7.1
Requires PHP: 7.2
Stable tag: 1.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A floating contact button that opens your contact channels: phone, WhatsApp, Telegram, Instagram, email and more.

== Description ==

Floating Call Button adds a floating button to all of your pages, or only to the pages you choose. When a visitor clicks it, a menu opens with the ways they can reach you.

= Features =

* Show the button on all pages, only on selected pages, or on all pages except the selected ones (pages, the front page, and any post or product by ID).
* Eight preset positions (corners, edge centers) plus a custom position in percent, picked by clicking or dragging on a preview screen.
* Separate size and edge distance for desktop and mobile, with a configurable breakpoint.
* Change the button icon: 19 preset icons, your own SVG code, or an image from the Media Library.
* Solid or two-color gradient background, icon color, and menu card colors.
* Unlimited contact channels: phone, WhatsApp, Telegram, Instagram, email, SMS, Eitaa, Bale, Rubika, LinkedIn, map links and custom links. Add, remove, disable and reorder them by dragging.
* Links are built for you (for example `wa.me` for WhatsApp and `tel:` for phone numbers). Persian and Arabic digits are accepted.
* Default message text for WhatsApp and SMS, and a default subject for email.
* Live preview in the settings page.
* Optional direct link when only one channel is enabled.
* Right-to-left support, keyboard accessible (Esc closes the menu), no jQuery on the front end. Assets load only on pages where the button is shown.

= For developers =

Filters: `tavoos_fcb_should_display`, `tavoos_fcb_channels`, `tavoos_fcb_channel_url`, `tavoos_fcb_channel_types` and `tavoos_fcb_icons`.

== Installation ==

1. Upload the plugin through Plugins > Add New > Upload Plugin, or extract it into `wp-content/plugins/`.
2. Activate the plugin from the Plugins screen.
3. Open the new "Call Button" menu in the dashboard to add your contact channels.

== Frequently Asked Questions ==

= A channel does not appear on the site =

Channels without a number, username or link are hidden. Make sure the channel is enabled and has a value.

= The button is hidden behind other elements =

Increase the z-index value in the Position tab.

= Can I use my own icon? =

Yes. For the main button and for every channel you can pick a preset icon, paste SVG code, or choose an image from the Media Library.

== Changelog ==

= 1.2.0 =
* All code prefixes are now at least four characters (`tavoos_fcb_`). Settings saved by earlier versions are migrated automatically.
* The source strings are now in English, with a bundled Persian (fa_IR) translation.
* Filters were renamed from `fcb_*` to `tavoos_fcb_*`.

= 1.1.0 =
* Logo icons for Eitaa, Bale and Rubika.
* Preset icons can be multicolor.

= 1.0.0 =
* First release.

== Upgrade Notice ==

= 1.2.0 =
Filters were renamed from fcb_* to tavoos_fcb_*. Update any custom code that uses them.
