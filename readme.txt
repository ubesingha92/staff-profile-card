=== Staff Profile Card ===
Contributors: ubesingha92
Tags: elementor, staff, profile, directory, academic
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display staff profile cards in Elementor using profile data from the University Profiles API.

== Description ==

Staff Profile Card is a simple Elementor widget for showing academic staff profiles on a WordPress website.

The plugin connects to a profile API, then displays staff cards with details such as name, designation, qualifications, photo, and public profile link.

Project: https://github.com/ubesingha92/staff-profile-card
Author: Chanaka Chathuranga Ubesingha
Support: https://github.com/ubesingha92/staff-profile-card/issues

== How to Use ==

1. Install and activate the plugin in WordPress.
2. Go to Settings > Staff Profile Card.
3. Add your API endpoint URL, for example:
   http://localhost/profiles/api/profile
4. Open a page with Elementor.
5. Add the Staff Profile Card widget.
6. Add one or more API Profile IDs, for example:
   SPC-8F3K2Q9X
7. Save the page.

Use only the API Profile ID. Do not use a Staff ID or database ID.

== Notes ==

By default, API URLs are allowed only for localhost, 127.0.0.1, and ::1.
To use another API host, add it to SPC_ALLOWED_API_HOSTS in your WordPress config.

If an API Profile ID is wrong, inactive, or unavailable, that profile is skipped.

If an API Profile ID is regenerated in University Profiles, update it in the Elementor widget.

== Changelog ==

= 2.0.0 =
* Added support for multiple staff profiles.
* Added drag-to-reorder profile items.
* Added section title and subtitle controls.
* Added responsive card layout.

= 1.0.0 =
* Initial release.
