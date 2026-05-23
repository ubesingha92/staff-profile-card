=== Staff Profile Card ===
Contributors: ubesingha92
Tags: elementor, staff, profile, card, widget, directory, academic
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

An Elementor widget that displays an academic staff directory fetched from a remote API. Supports multiple profiles with drag-to-reorder.

== Description ==

Staff Profile Card is an Elementor widget plugin that fetches and displays
staff profile information from an external API as an academic staff directory.

Plugin URL: https://github.com/ubesingha92/staff-profile-card
Author: Chanaka Chathuranga Ubesingha
Author URL: https://github.com/ubesingha92
Documentation: https://github.com/ubesingha92/staff-profile-card#readme
Support: https://github.com/ubesingha92/staff-profile-card/issues

**Features:**

* Admin settings page to configure the API endpoint URL.
* Elementor widget to display **multiple** staff profile cards in a directory layout.
* **Drag-to-reorder** staff profiles using Elementor's built-in repeater controls.
* Section header with customizable title and subtitle.
* Horizontal card layout with image, name, designation, qualifications, and "View Profile" link.
* Responsive design: desktop rows, tablet compact, and mobile stacked cards.
* Only API Profile IDs are used — internal IDs and Staff IDs are never exposed.
* Show/hide controls for image, designation, qualifications, and "View Profile" link.
* Accent color and spacing controls.
* Batch-mode live preview in the Elementor editor.
* Missing profiles are skipped so the rest of the directory continues cleanly.

== Installation ==

1. Upload the `staff-profile-card` folder to `/wp-content/plugins/`.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **Settings → Staff Profile Card** and enter your API endpoint URL
   (e.g., `http://localhost/profiles/api/profile`). Do not add query
   parameters to this URL.
4. In the Elementor editor, search for "Staff Profile Card" widget.
5. Drag the widget onto your page.
6. Set the section title and subtitle (e.g., "Academic Staff").
7. Click **Add Item** in the Staff Profiles repeater and enter each
   staff member's API Profile ID (e.g., `SPC-8F3K2Q9X`).
8. Drag repeater items to reorder staff members.
   Do not use the Staff ID or an internal database ID.

== Frequently Asked Questions ==

= How do I show multiple profiles? =

Use the **Staff Profiles** repeater in the widget settings. Click
"Add Item" for each staff member and enter their API Profile ID.

= How do I reorder staff members? =

Drag the repeater items up or down in the Elementor editor to change
the display order.

= Which ID should I enter in the widget? =

Use the generated API Profile ID from University Profiles, for example
`SPC-8F3K2Q9X`. Do not use the Staff ID, `users.id`, or `staff_profiles.id`.
If the API Profile ID is regenerated in University Profiles, update this widget
because the old ID will stop working and the faculty website API connection
will use the new ID.

= What happens if the API is down? =

Profiles that are not returned by the API are skipped. Other cards continue to
render normally.

== Documentation ==

Documentation is maintained in the project README:
https://github.com/ubesingha92/staff-profile-card#readme

== Support ==

For support and issue tracking, use GitHub Issues:
https://github.com/ubesingha92/staff-profile-card/issues

== Changelog ==

= 2.0.0 =
* **Breaking**: Widget now uses a Repeater control for multiple profiles. Existing single-profile instances must be reconfigured.
* Added multi-profile support via Elementor Repeater with drag-to-reorder.
* Added section header with customizable title and subtitle.
* Redesigned card layout to horizontal rows with orange accent border.
* Added "View Profile →" link per card.
* Added batch REST endpoint for editor preview.
* Added responsive design for desktop, tablet, and mobile.
* Added accent color style control.
* Added Inter font from Google Fonts.

= 1.0.0 =
* Initial release.
