=== Booking Manager - Sync WP Booking Calendar - Import Events, Export Bookings to ICS Calendar ===
Contributors: wpdevelop, oplugins
Donate link: https://oplugins.com/plugins/booking-manager
Tags: booking calendar, events, ics, google calendar, iCalendar
Requires at least: 4.0
Requires PHP: 5.6
Tested up to: 6.9
Stable tag: 2.1.18
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Showing events listing from .ics feeds or sync bookings from different sources to your website

== Description =

Booking Manager plugin can easily show list of events in customizable way from external .ics feeds at your website.
Booking Manager have native integration with **[Booking Calendar](https://wordpress.org/plugins/booking/)** plugin.
It can sync bookings from **Booking Calendar** with different sources (Airbnb, Booking.com, HomeAway, TripAdvisor, VRBO, FlipKey and any other calendar that uses .ics format).

>[Plugin Homepage](https://oplugins.com/plugins/booking-manager/ "Booking Manager Homepage") | [Support](https://oplugins.com/plugins/booking-manager/#faq "Support")

= Booking Manager IS GREAT FOR =

* Listing of upcoming events at your website from .ics feeds
* Sync bookings from different sources with [Booking Calendar](https://wordpress.org/plugins/booking/) plugin

= FEATURES =

* List of events from external .ics feeds.
* Ability to upload .ics file(s) to your website and use it.
* Customization of events listing template - it's how events showing at front-end side of your website.
* Easily inserting shortcode for events listing into any post or page via popup dialog, where you can select different parameters.
* Setting different parameters for events listing, like "start from" and "finish to" dates, etc...
* Native integration with **Booking Calendar** plugin.
* **Sync bookings** from Booking Calendar with different sources (Airbnb, Booking.com, HomeAway, TripAdvisor, VRBO, FlipKey and any other calendar that uses .ics format).
* **Import .ics** feeds (files) into Booking Calendar. Its useful, if you need to import bookings from multiple external websites into one calendar in Booking Calendar plugin.
* **Export .ics** feeds (files) from Booking Calendar. You can publish bookings from Booking Calendar as .ics feeds at  different pages, and then import such  bookings in your other different website, like Airbnb.
* Configure URLs for pages where you want to publish your ics feeds.
* Mobile friendly.

== Installation ==

= Automatic installation =

To do an automatic install, log in to your WordPress admin panel, navigate to the Plugins menu and click Add New.
In the search field type "Booking Manager" and click Search Plugins.
Once you've found the plugin you can view details about it such as the point release, rating and description.
Now, you can install it by clicking "Install Now".

= Manual installation via WordPress admin panel =

* Download plugin zip file to your computer
* In your WordPress admin panel, navigate to the Plugins menu and click Add New.
* Click "Upload Plugin" button and hit "Choose File" button
* When the popup appears select your downloaded zip file of plugin
* Follow the on-screen instructions and wait as the upload completes.
* When it's finished, activate the plugin via the prompt. A message will show confirming activation was successful.

= Manual installation via FTP =

* Download plugin zip file to your computer and unzip it
* Using an FTP application, or your hosting control panel, upload the unzipped plugin folder to your WordPress installation's `wp-content/plugins/` directory.
* In your WordPress admin panel, navigate to the Plugins menu and find your uploaded plugin
* Click on Activate link under the plugin. A message will show confirming activation was successful.

That's it!

== Frequently Asked Questions ==

= How to start showing events from .ics feeds (files)? =

* Open "oPlugins Panel" menu page in WordPress admin panel
* Upload .ics file via this page or simply use URL to .ics feed from external website
* Insert into page or post the shortcode for listing events from .ics feed. Please click on insert shortcode button in edit content toolbar at edit post page. Then in popup dialog select your parameters for showing events and click on Insert button. Save changes. Test it.

= How to start import of .ics feeds (files)? =

* Install [Booking Calendar](https://wordpress.org/plugins/booking/) plugin.
* Insert [booking-manager-import ...] shortcode into some post(s) or page(s) easily via configuration popup window. Please click on insert shortcode button in edit content toolbar at edit post page.
* Using such shortcodes in pages give a great flexibility to import from different .ics feeds (sources) into the same resource (calendar). Also it's possible to define different CRON parameters for accessing such different pages with different time intervals.
* Or you can import .ics feed or file directly at Booking > Settings > Sync > Import page.

= How to start export of .ics feeds (files)? =

* Install [Booking Calendar](https://wordpress.org/plugins/booking/) plugin.
* Configure ULR feed(s) at the Booking > Settings > Sync > Export page.
* Using such URL(s) you can import .ics feeds, from interface of other websites. Check more info about how to import .ics feeds into other websites at the support pages of specific website.
* Visit these (previously configured URL feeds) pages for downloading .ics files (for example by configuring CRON at your server).

= Support Languages =

- English
- Dutch [63% Completed]
- German [62% Completed]
- Italian [62% Completed]
- Norwegian [62% Completed]
- Swedish [62% Completed]
- Hungarian [62% Completed]
- Ukrainian [62% Completed]
- Russian [62% Completed]
- French [62% Completed]
- Chinese [62% Completed]
- Chinese (Taiwan) [61% Completed]
- Hebrew [61% Completed]
- Danish [61% Completed]
- Finnish [61% Completed]
- Brazilian Portuguese [61% Completed]    
- Polish [61% Completed] 
- Portugal [37% Completed]
- Spanish [37% Completed]
- Greece [37% Completed]
- Czech [37% Completed]
- Slovak [37% Completed]
- Croatian [37% Completed]    
- Turkish [37% Completed]
- Catalan [37% Completed]
- Bulgarian [37% Completed]
- Arabic [58% Completed]
- Belarussian [12% Completed]

= Requirements =

- PHP 5.6 or newer,
- MySQL version 5.5 or newer,
- WordPress 4.0 or newer,
- jQuery 1.7.1 or newer

== Screenshots ==

1. **Events Listing** - showing events listing at front-end side of website.
2. **Manage ics** - upload .ics file(s) to your server
3. **Settings** - configure different settings of plugin
4. **Events Listing Template** - customize template how to show events at your website
5. **Inserting shortcode** - popup dialog for easy configuring and inserting plugin shortcode into content of post

== Changelog ==
= 2.1.18 =
* **Fix** Fix XSS during listing of .ics feed content, if the .ics file was broken or intentionaly inject by malicious script. (2.1.18.1)

= 2.1.17 =
* **Fix** If enabled "Use selected times for each booking date" option  on the WP Booking Calendar > Settings > Calendar > Calendar Settings page,  system  imports time slots,  instead of check  in/out times for such  bookings. (2.1.17.1)

= 2.1.16 =
* **Fix** Timed events where end <= start (equal or earlier, e.g., due to a bug or reversed inputs) -> you now get a minimal valid span of +1 hour. Previously this produced invalid ICS (“DTEND before DTSTART”).
* **Fix** All-day events where end ≤ start -> coerced to +1 day (exclusive end), which matches iCal semantics for single-day all-day events.
* **Fix** So: for correctly formed bookings, durations are identical. The only “change” is that bad/zero/negative durations are repaired to a minimal valid length so calendars won’t reject the feed.

= 2.1.15 =
* **Improvement** Block import/delete shortcodes on non-published posts/pages to prevent unintended actions during review. (2.1.15.1)

= 2.1.14 =
* **Improvement** Improved iCal/ICS export for full compatibility.
* **Improvement** Reworked recurring and multi-date export logic: now generates one VEVENT per date/range instead of RDATE rules, ensuring full compatibility with Google Calendar, Outlook, Apple Calendar, Booking.com, Airbnb, and other iCal consumers.
* **Improvement** Corrected date handling to respect the site’s timezone for both all-day and timed events, preventing off-by-one-day issues.
* **Improvement** Added safer UID generation and consistent metadata for better sync reliability.

= 2.1.13 =
* Fix: When accessing the .ics calendar feed for a parent booking resource (with specific capacity) in Booking Calendar Business Large, only bookings from the parent resource are now exported. Previously, bookings from all associated child resources were incorrectly included. (Version 10.12.3.2)

= 2.1.12 =
* New: Added option "Export only full days (not time)" on "oPlugins Panel > Settings General", to export only full days without time to the .ics feed. This is a limitation in Airbnb and some other services. (2.1.12.1)
* **Fix** Notice: Function _load_textdomain_just_in_time was called incorrectly. (2025-04-04)

= 2.1.11 =
* **Fix** Notice: Function _load_textdomain_just_in_time was called incorrectly. (2025-03-28)

= 2.1.10 =
* **Fix** Correctly import of full day events, which contains of several not consecutive dates into the Google Calendar (2.1.10.1)

= 2.1.9 =
* **Under hood** Added FBTYPE:BUSY for all exported bookings in .ics feeds. Purpose of this property to specify the free or busy time type. Some external  servers does not mark  the bookings as unavailable without this property (2.1.9.1)
* Fix Added removed empty spaces in .ics feed at  the beginning and end of the .ics feed,  which  can be reason  of  PHP Fatal error:  Uncaught Error: Call to a member function getName() on null in ../booking-manager/assets/libs/icalendar/includes/ical.php:461 ... (2.1.9.2)

= 2.1.8 =
* **Under hood** Prevent of execution shortcodes, while editing the post/page. Instead of that show Sketchup block. It's reduce potential conflict  issues and improve speed of such  edit page. (9.9.0.39)

= 2.1.7 =
* Fix empty  spaces in the UID of exported events in some servers. 2.1.7.1

= 2.1.6 =
* Fix vulnerable to SQL injection in the [booking-manager-delete ...] shortcode.

= 2.1.5 =
* New shortcode parameter. Ability to use shortcode [booking-manager-delete resource_id=1 action='delete'] to delete all imported bookings from specific booking resource (calendar). ( In paid versions of Booking Calendar you can define ID of booking resource,  where you want to  delete imported bookings with this parameter: resource_id=1 ).

= 2.1.4 =
* New shortcode parameter in import shortcode: delete='skip'. It will  prevent of deletion of all previous imported bookings,  even if you activated option "Trash / delete all imported bookings before new import" at  the Booking > Settings > Sync > "General" page.   Example: [booking-manager-import delete='skip' resource_id=1 import_conditions='if_dates_free' from='today' until='any' url='https://calendar.google.com/calendar/ical/CALENDAR_ID/public/basic.ics']

= 2.1.3 =
* Fix issue of not importing events with only one date (if was activated "Use check in/out time" at the Booking > Settings > Sync > "General" page). In this case, system append one additional date for such events (even if the option "Append check out day", unchecked). It is required for the bookings with check in/out times (chnge over dates), because such bookings minimum for 2 dates.

= 2.1.2 =
* Added support legacy versions of Booking Calendar.

= 2.1.1 =
* Add message that  Booking Manager require Booking Calendar 9.8 or newer

= 2.1 =
* Support Booking Calendar 9.8

= 2.0.31 =
* **Fix**. PHP Fatal error:  Uncaught TypeError: count(): Argument #1 ($value) must be of type Countable|array, null given ... (2.0.31.1)

= 2.0.30 =
* **New** Added new REFRESH-INTERVAL property to head of exported .ics feeds with PERIOD 10 minutes. This property specifies a suggested minimum interval for polling for changes of the calendar data from the original source of that data. (2.0.30.1)
* **New** Added new X-PUBLISHED-TTL:PT10M property to head of exported .ics feeds with PERIOD 10 minutes for MS Outlook. (2.0.30.1)

= 2.0.29 =
* **Fix**. Possible Server Side Request Forgery (SSRF) issue.

= 2.0.28 =
* **Fix**. Issue of import events, where last day of event blocking the whole day on the calendar. (2.0.28.1)

= 2.0.27 =
* **Fix**. If activated option "Append check out day" at Booking > Settings > Sync > "General" page,  system  will append this additional  day  does not depend on the change-over days option, as before. (2.0.27.1)

= 2.0.26 =
* **Fix**. Hide 'ATTENDEE' and 'MAILTO' fields in events of exported .ics feed,  if activated option 'Remove booking details in exported .ics feed' at the Booking Manager plugin at menu 'oPlugins Panel' > General Settings page.

= 2.0.25 =
* **Fix**. Uncaught TypeError: Cannot access offset of type string on string in ../wp-content/plugins/booking-manager/core/wpbm-functions.php:218 (2.0.25.1)

= 2.0.24 =
* **New**. Ability permanently delete all imported bookings before new import, instead of sending to Trash. Activate this option at the Booking > Settings > Sync > "General" page. (2.0.24.1)

= 2.0.23 =
* **Fix**. issue when .ics event have DTSTART has a DATE data type, and there is no DTEND. The event will ends on the same calendar date and time of day specified by the "DTSTART" property. (2.0.23.1)

= 2.0.22 =
* **New** shortcode for deleting importing bookings in specific booking resource: [booking-manager-delete resource_id=5]
* **Fix**. issue of incorrectly import events that ended with 00:00:00 time. (2.0.22.2)

= 2.0.21 =
* **Fix**. issue of incorrectly import events that ended with 00:00:00 time. Previously if you import event from 2022-01-28 23:00 to 00:00 system was marked as unavailable 2022-01-28 23:00 to 2022-01-29 (all day). Currenlty system will import from 2022-01-28 23:00 to 2022-01-28 23:59 (2.0.21.1)
* **Fix**. issue of loading not needed calendar.css file (8.9.4.13)

= 2.0.20 =
* Added "ATTENDEE" block  to  the exported .ics feeds ( 2.0.20.1)
* Ability to export into .ics feeds only bookings, that was created in Booking Calendar plugin,  without any  other imported bookings. Activate it at Booking > Settings > Sync > "General" page. (2.0.20.2)

= 2.0.19 =
* **Fix**.  Deprecated jQuery( ... ).submit() message
* **Fix**.  Error with  undefined $milliseconds variable ( 8.8.2.1 )

= 2.0.18 =
* **Support** WordPress 5.7 (2.0.18.4)
* **Improvement** removed "Chosen" library
* **Fix**. Showing deprecated message: ../booking-manager/core/any/js/admin-support.js:527:16: jQuery.fn.load() is deprecated (2.0.18.1)
* **Fix**. Showing deprecated message: .. get_magic_quotes_runtime / set_magic_quotes_runtime ..  (2.0.18.2)
* **Fix**. Delete imported bookings after the downloading of the .ics feed, to prevent issue of deleting of all imported bookings and having issue with downloading new .ics feed and new import (2.0.18.3)

= 2.0.17 =
* **Fix**. Deprecated warnings: Array and string offset access syntax with curly braces is deprecated, while using PHP 7.4

= 2.0.16 =
* **Support** WordPress 5.5 (2.0.16.1)

= 2.0.15 =
* **Fix**.  Warning: preg_match(): Compilation failed: invalid range in character class for emails (2.0.15.1)
* **Improvement** of working import_conditions='if_dates_free'  parameter  in [booking-manager-import ... shortcode,  during checking events import for specific times. Previously  was checked if entire day  was available. (2.0.15.2)
* **Improvement** Replaced usage of Bootstrap slideToggle to jQuery toggle function - for ability to  show some sections, if bootstrap library deactivated. (2.0.15.3)
* **Fix**. Issue of not ability to import the events for specific time-slots at the same date (2.0.15.4)

= 2.0.14 =
* **Fix**.  Error Parse error: syntax error, unexpected '[' in ..wpbm-bc-import.php on line 866,  while using PHP 5.2.4 (2.0.14.1)
* **Fix**.  Invalid link error for some type of .ics feeds, which  have & in  URL (2.0.14.2)
* **Fix**.  Error "[WPBM Error] File does not contain events " during showing events listing at  oPlugins Panel > Manage .ics page for some type of .ics URLs (2.0.14.3)

= 2.0.13 =
* **Compatibility**. Support **WordPress 5.3** - update of admin panel styles. (2.0.13.1)

= 2.0.12 =
* **New** Ability remove all  details from  the .ics feed (SUMMARY and DESCRIPTION fields)  during export of .ics feeds and export only booked dates. You can  activate this option  at the oPlugins Panel > General Settings page (2.0.12.3)
* **Fix** issue of ability to  import the events from  the "Expedia" ("expediapartnercentral.com"),  which is require to  define some 'user-agent'  for request , like  'Mozilla/5.0 (iPad; U; CPU OS 3_2_1 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Mobile/7B405'. Otherwise its prevent from  loading .ics feeds. (2.0.12.1)
* **Fix** Added back "STATUS:CONFIRMED" in exported .ics feed. (2.0.12.2)

= 2.0.11 =
* **New** Ability to check events (in .ics feed), if they was imported into the "Booking Calendar". Use [BOOKING_ID] shortcode in Settings > "Listing Template" for showing ID of imported booking 9relative to  specific event),  if its exist.  (2.0.11.4)
* **New** [BOOKING_LINK] shortcode in Listing template for direct link to booking in Booking Listing page,  if event was imported. Example of usage shortcode in Settings > "Listing Template" Booking ID: &lt;a href="[BOOKING_LINK]" target="_blank"&gt;[BOOKING_ID]&lt;/a&gt; (2.0.11.5)
* **New** Ability to export only approved bookings into .ics feeds. Available in the Booking Calendar Business Small or higher versions since 8.5.2 or newer update. (2.0.11.1)
* **Fix** Issue of previosly exporting bookings from "child" booking resources for "parent booking resource". Currently  system  export only bookings from specific booking resource (does not include child booking resources) (2.0.11.2)
* **Fix** Removed "STATUS:CONFIRMED" in exported .ics feed. Otherwise possible issue in the booking.com  that  show  in dashboard such  bookings with red status instead of yellow. (2.0.11.3)

= 2.0.10 =
* **New Experimental Feature**. Trash all imported bookings before new import. Move all previously imported bookings to trash  before new import bookings. Its can resolve issue of updating deleted and edited events in external sources. Its work only, if you are using one source (.ics feed) for importing into specific booking resource! Work only in update of Booking Calendar 8.4.7 or newer. (2.0.10.3)
* **Improvement** Force import. Ability to import bookings without checking, if such bookings already have been imported. Available in the Booking Calendar 8.4.7 or newer. (2.0.10.1)
* **Improvement** Show more detail info of not ability to  download .ics feeds.	(2.0.10.5)
* **Fix** PHP Warning:  count(): Parameter must be an array or an object that implements Countable in ../booking-manager/core/wpbc/wpbm-bc-import.php on line 259 (2.0.10.4)

= 2.0.9 =
* **Improvement** Ability to import new bookings from .ics feed,  is such  bookings already  exist  in the Booking Calendar, but was moved to  trash. (2.0.9.3)
* **Fix** Warning:  Invalid argument supplied for foreach() in ../booking-manager/core/wpbc/wpbm-bc.php on line 73 (2.0.9.2)
* **Fix** error warning in PHP 7.2:  "PHP Fatal error: Uncaught ArgumentCountError: Too few arguments to function wpbm_recheck_plugin_locale(), 1 passed and exactly 2 expected in ../booking-manager/core/wpbm-translation.php:226" (2.0.9.1)

= 2.0.8 =
* **Support** Fix compatibility with Gutenberg 4.1- 4.3( or newer ). Before this having JavaScript error " ReferenceError: jQuery is not defined"  at edit post page,  because of weird behavior with  'edit_form_advanced' hook, while activated Gutenberg. (2.0.8.2)
* **Fix** issue of checkboxes and radio buttons  height in new Firefox updates in admin panel,  otherwise sometimes, there exist weird artefact (2.0.8.1)

= 2.0.7 =
* **Improvement**  Export bookings from  Booking Calendar into  .ics feed for 2 years instead of 1 year previously. (2.0.7.1)
* **Improvement**  Add notes to the booking relative source of imported booking.  (2.0.7.2)
* **Fix** Warning: count(): Parameter must be an array or an object that implements Countable in ../core/wpbc/wpbm-bc-import.php on line 12 (2.0.7.3)
* **Fix** Skip adding Timezone to "middle" days, if Booking Calendar use change over days, to prevent of having clock icon in middle days. (2.0.7.4)

= 2.0.6 =
* **Improvement**  Set timezone frrom  Booking > Settings > Sync  page for booking listing shortcode (2.0.6.1)

= 2.0.5 =
* **Improvement** Added check  in/out times to  imported bookings,  if in Booking Calendar was activated "change over" functionality and activated this option at the Booking > Settings > Sync page in Booking Calendar 8.2 or newer.  (2.0.5.1)
* **Under Hood** Ability to add one additional day to .ics event (useful in some cases for bookings with  change-over days). Possible to activate this option at the Booking > Settings > Sync page in Booking Calendar 8.2 or newer.  (2.0.5.2)
* **Fix** Add checking about exist PHP function 'mb_detect_encoding'. In some systems,  PHP mbstring extension  can  not be active.  (2.0.5.3)
* **Fix** Issue of not ability to  export .ics feed, if the WordPress website was installed not in root directory. Home url, have additional folder, like this: http://server.com/my-website/ (2.0.5.4)

= 2.0.4 =
* **Improvement** Updated all links from  http to https of plugin website.
* **Improvement** Add timezone to the export for .ics feed from  Booking Calendar, in case if you was defined timezone at the Booking > Settings > Sync > "Import Google Calendar Events" page. (2.0.3.3)

= 2.0.3 =
* **Fix** Issue of  JavaScript error during inserting shortcode from  popup window (2.0.3.1)
* **Fix** Issue of importing event with  admin email  instead of email like ics@you-server.com (some domain configurations can not have such email at all). (2.0.3.2)

= 2.0.2 =
* **Impovement** Export to  .ics feed bookings from  Booking Calendar that  does not inside of Trash folder (2.0.2.3)
* **Fix** issue of showing warning "parsererror ~ SyntaxError: JSON.parse: unexpected character at line 1 column 1 of the JSON data" (2.0.2.1)
* **Fix** issue of showing Fatal error: "Uncaught Error: Call to a member function get_error_message()"  (2.0.2.2)

= 2.0.1 =
* **Impovement** Do not show 'Import XX bookings' message, if parameter silence=1 exist in import shortcode (2.0.1.2)
* **Impovement** Show error description if plugin  can  not download .ics file by some reason (2.0.1.3)
* **Fix** issue of not importing events, if end date set  more than 20 years from today date (2.0.1.1)
* **Fix** issue of showing error in PHP 7,  at  the Settings General page  (2.0.1.4)
* **Fix** showing "Deprecated" warnings in PHP 7 environment (2.0.1.5)
* **Fix** correctly  showing single and double quotes (' and ") symbols during export bookings to .ics feed (2.0.1.6)

= 2.0 =
* Fully redeveloping version of plugin

== Upgrade Notice ==
= 2.0.16 =
Support WordPress 5.5