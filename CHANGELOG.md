# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [4.4.7] - 2024-04-05

### Changed

- Changed the visibility of `$key` property from `private` to `public` to allow external access and manipulation, enhancing the flexibility for class interactions.
- Changed the visibility of `$user_options` property from `private` to `public`, enabling direct access to user options pulled from the database. This adjustment aims to streamline processes that require external manipulation of user options.

## [4.4.6.2] - 2024-04-03

### Fixed

- Implemented stricter attribute sanitization in `SWP_Buttons_Panel_Shortcode` class to enhance security and mitigate the risk of cross-site scripting (XSS) attacks through shortcode attributes. This update introduces a more rigorous sanitization process for all attributes passed through the shortcode handling mechanism. 
  - Enhanced `sanitize_attributes` method in `SWP_Buttons_Panel_Shortcode` class.
  - Added regex pattern to remove special characters from attribute values after initial sanitization.

## [4.4.6.1] - 2024-03-21

### Removed

- Removed Facebook share count functionality, simplifying the display and enhancing performance.

### Changed

- Upgraded `SWP_Database_Migration` class to version 4.4.6.1, introducing new methods and improvements for database handling.

### Fixed

- Fixed an initialization issue in the `SWP_Database_Migration` class, ensuring smooth operation and compatibility with the latest WordPress versions.

## [4.4.6] - 2024-03-12

### Changed

- Upgraded to Facebook's Graph API v18.0, ensuring compatibility and compliance with the latest API standards.
  - Deprecated Graph API v6.0 and v17.0 endpoints removed, aligning with Facebook's 2-year lifecycle policy.
- Implemented WordPress coding standards across the plugin to enhance code quality and consistency.

### Fixed

- Corrected the orientation of the 'X' Logo (Issue #887).
- Refactored the `post_json` function to utilize cURL directly, enhancing compatibility with the Bitly API and resolving previous link shortening issues.
- Resolved Bitly link generation problem, ensuring short links are now created without issues (Issue #866).
- Addressed CSRF vulnerability by adding user authentication, nonce verification, and improving access secret handling.
- Discontinued Facebook share counter functionality, enhancing user privacy and streamlining performance.

## [4.4.5.1] - 2024-01-11

### Maintenance

- Updated `SWP_Pro_Analytics_Database.php`.

## [4.4.5] - 2024-01-10

### Removed

- Removed Google Plus integration due to its discontinuation.
  - This removal includes any Google Plus social sharing buttons, links, and related code.

## [4.4.4] - 2023-10-23

### Fixed

- Security patch: Prevent XSS vulnerabilities in the plugin.
  - Escaped output in the `generate_css_classes` method to prevent potential XSS vulnerabilities.
  - Added validation and escaping in the `get_min_width` method to ensure input is safe from potential XSS vulnerabilities.

## [4.4.3] - 2023-10-19

### Changed

- Added new Twitter branding.

## [4.4.2] - 2023-08-30

### Changed

- Updated Graph API endpoint to v17.0.
- Changed Twitter button branding.
- Added Mastodon support.

## [4.4.1] - 2023-02-14

### Fixed

- Replaced some characters after they were converted to HTML entities.
- Checked for `$attr` key before trying to use them.
- Updated verbiage on the Advanced tab Frame Buster.

## [4.4.0] - 2023-01-10

### Changed

- Removed the Update Checker from Free into Pro only.
- Removed cURL calls in favor of WordPress built-in calls.
- Ensured users have permission to make AJAX calls.
- Removed PHP short tags.
- Sanitized all inputs that come from outside sources.
- Moved the CSS for jQuery UI to an internal asset.
- Removed all branded logos from the header banner.
- Removed the word "WordPress" from the beginning of the plugin name.
- Added capability and nonce checks to AJAX calls.

## [4.3.0] - 2020-07-20

### Changed

- Major update to the Facebook share checking functionality of the plugin.

## [4.2.1] - 2020-12-07

### Fixed

- Fixed broken admin pages due to JS errors on the analytics charts.
- Fixed division by zero errors in the social optimizer PHP file.
- Fixed other minor bugs and glitches.

## [4.2.0] - 2020-12-02

### Added

- Introduced a suite of social analytics tools.
- Social Optimizer scores are now saved, allowing for comparisons and recommendations.
- Added a check for share counts on URLs with UTM codes for Pinterest.

### Fixed

- Fixed minor CSS issues.
- Fixed line breaks in font URLs in the CSS that made the icon font not appear.
- Fixed blank settings page on some page loads.
- Cleaned up post meta remnants.
- Cleaned out share counts for buttons that don't use share counts like "email" or "more."
- Added a posts column to display the social optimization score.

## [4.1.0] - 2020-08-17

### Added

- Added a "Social Optimizer" to the Gutenberg post editor sidebar.

### Fixed

- Fixed minor CSS issues.
- Updated the post editor custom fields to be compatible with WP 5.5.

## [4.0.2] - 2020-07-28

### Added

- Added Facebook authentications to core to allow for faster and more reliable share counts when authenticated.
- Added full AMP compatibility to the plugin.

### Fixed

- Fixed the custom CSS field on the settings page so that it saves properly now.

### Maintenance

- Added dashboard notifications to alert users as to the state of their Facebook authentications.

## [4.0.1] - 2020-04-14

### Added

- Added the ability to connect Social Warfare with Facebook for faster and more accurate share counts.
- Added a notice about clearing caches after updates.
- Added additional share count debugging to the `?swp_debug=recover` debugger.

### Fixed

- Fixed CSS for the "more" share box overlay.
- Fixed a PHP error related to user options.

### Maintenance

- Adjusted the cache rebuild schedule.

## [4.0.0] - 2020-01-10

### Added

- Added the ability to assign multiple Pinterest images with a slick overlay interface.
- Added a "More" button that brings up an overlay with all available share options.
- Added a social follow widget with lots of styles and options.
- Added new network buttons (Xing, VK, Viber, Blogger, Evernote).
- Added a print button.
- Added OpenShareCount API as a source for Tweet counts.
- Added Rebrandly as a link shortening service.
- Added a bunch of brand new shortcodes.
- Added the ability to emphasize the first one or two buttons in the panel.
- Added the ability to use `?swp_cache=rebuild&swp_debug=recovery` to view the URLs being checked for shares.
- Converted all of the plugin's CSS to neatly organized SCSS/SASS.
- Updated a few styles in the admin options page.
- Updated descriptions and image placeholders for Social Warfare custom options in the post editor.
- Added an "opt-in" only "Powered by Social Warfare" that will automatically link to our site using your affiliate URL.
- Added an "age of post" check for shortlinks.
- Added the option to delay the display of share counts on new posts.

### Fixed

- Fixed an extra doctype that would sometimes get added to the content.
- Fixed some JavaScript/jQuery errors.
- Fixed some PHP errors.

### Removed

- Removed all instances of Google Plus.

### Maintenance

- Vastly improved codebase organization and documentation.

## [3.6.1] - 2019-06-03

### Changed

- Updated Hover Save Pin functionality to work more globally.

### Fixed

- Fixed conflict with hover pin button in Thrive Architect page builder.
- Removed the Google Plus network share button.
- Fixed an "Uncaught Error" for `$` in the JS on the widgets page.
- Fixed a Twitter PHP notice.

## [3.6.0] - 2019-05-02

### Changed

- Updated Hover Save Pin functionality to work more globally.
- Updated how Facebook share counts are requested.

### Fixed

- Fixed placement of hidden Pinterest image.
- Fixed double quotation marks breaking Click to Tweets.
- Fixed the hover-pin-opt out checkbox in a post editor.
- Fixed hover pin description source.
- Fixed whitespace/new HTML document being created in buttons panels.
- Fixed character encoding for `<meta>` tags.

## [3.5.4] - 2019-03-25

### Maintenance

- Conducted a code security audit and updates.

## [3.5.3] - 2019-03-21

### Fixed

- Immediate security patch for 3.5.x.

## [3.5.2] - 2019-03-19

### Fixed

- Fixed `<meta>` tags for OG and Twitter Card.
- Fixed source of Pinterest description when pinning from Save or Pinterest button.
- Fixed empty 'via' being added to Pinterest description and Click to Tweet.
- Updated addon registration and unregistration messages.
- Updated icon font file and encoding.

## [3.5.1] - 2019-02-27

### Fixed

- Fixed the total share displaying an incorrect value.
- Fixed PHP notices about 'undefined variable'.
- Fixed floating buttons not showing or hiding as expected.
- Fixed SWP Addon error.
- Restored Custom Color CSS being applied to the page.

## [3.5.0] - 2019-02-26

### Fixed

- Fixed the side floating buttons not showing on some themes.
- Fixed 'operand type' notice when making cURL share count request.
- Fixed the Pinterest description sometimes being too long when pinning.
- Escaped the Pinterest description before sending to the client.
- Fixed a missing `@via` tag for Click to Tweets.
- Fixed the CSS selector for Gutenberg blocks.
- Updated location of Total Shares and Share Counts options.
- Updated `og:image` tags to include a name attribute (for LinkedIn).
- Updated the Frame Buster feature.
- Updated the Pinterest button search & destroy method.
- Fixed notice when there are no inactive icons.
- Added interface for handling OAuth handshakes.

## [3.4.2] - 2018-12-13

### Fixed

- Fixed floating bottom disappearing on mobile when pro is deactivated.
- Fixed blocks disappearing after Gutenberg update.
- Fixed buttons accidentally showing up on pages created with content builders.

## [3.4.1] - 2018-12-04

### Fixed

- Fixed JS error in Admin (`TypeError: $ is not defined`).
- Fixed placement of mobile floating buttons.
- Fixed breakpoint transition for mobile buttons.
- Fixed option registration when Pro is temporarily deactivated.
- Updated cURL method so API requests are faster.
- Removed references to Open Share Count.

## [3.4.0] - 2018-11-27

### Added

- Added support for Gutenberg blocks Social Warfare and Click To Tweet (as of Gutenberg 4.5.1).
- Added option in Advanced tab to disable Gutenberg blocks.
- Added interactive components to the Admin sidebar.
- Added a tooltip to network icons in the settings page.

### Changed

- Updated JS to open share links in windows instead of tabs.
- Updated Social Warfare settings page to be mobile responsive.
- Updated the cURL request timeout duration to be shorter.

### Fixed

- Fixed extra whitespace when floating buttons are present on mobile.
- Fixed bug during post caching which prevented shares from updating.
- Fixed error `invalid argument supplied for foreach` regarding `$network` objects.
- Fixed Social Shares total counts column in Admin view.
- Fixed Popular Posts widget from defaulting to the wrong post type.
- Fixed use of `total` and `totals` in the `[social_warfare]` shortcode.
- Fixed totals only appearing on one set of buttons.
- Fixed minor cosmetic detail on the buttons panel (removed an underline).
- Fixed tweet character encoding.
- Refactored the `script.js` file (frontend JavaScript).

## [3.3.2] - 2018-09-14

### Fixed

- Fixed a line of code that caused the `social_warfare()` function to disappear.

## [3.3.1] - 2018-09-11

### Fixed

- Fixed the URL for post editor placeholder images.
- Fixed the Twitter Count toggle.
- Added backward compatibility for `swp_kilomega()`.
- Added compatibility for `mb_convert_encoding()`.

## [3.3.0] - 2018-09-11

### Added

- Added CSS to hide buttons on print views.
- Added Mix to the Social Networks.
- Added `url` parameter to the shortcode.

### Fixed

- Fixed plugin compatibility issue with BuddyPress.
- Fixed the decimal separator for share counts.
- Updated the option-getting mechanism (`global $swp_user_options` is now deprecated).
- Updated the sidebar in the Social Warfare settings page.
- Updated the plugin file structure and organization.

## [3.2.1] - 2018-08-16

### Fixed

- Fixed a minor `DOMDocument` warning that was appearing when `data-pin-description` was ON.

## [3.2.0] - 2018-08-13

### Added

- Created shortcode for Pinterest Image.
- Created Custom Pinterest Description on a per-image basis.

### Fixed

- Fixed the Total Shares icon/counts not displaying.
- Fixed many JS-related issues on floating and mobile buttons.
- Fixed an issue where some buttons did not display on some posts (after `post_updated` fires).
- Fixed the total shares missing counts from LinkedIn and Google Plus.
- Fixed floating buttons not showing until the bottom of the content.
- Fixed where posts were receiving false share counts from Facebook.
- Fixed the update process for addons (like Pro) so it should now receive dashboard notifications.
- Fixed sharing on Pinterest when images use relative paths instead of absolute paths.
- Fixed hidden Pinterest image from covering content below.
- Changed the cursor from `cursor` to `pointer` for Total Shares hover state.
- Added an option to add `data-pin-description` to all images in a post.
- Added `!important` tags for our Custom Color option.
- Added a check for whether or not to fetch share counts (based on Button Count and Total Count settings).
- Updated the visibility of Mobile Float Location option.
- Replaced New Share Counts with Twitcount.

## [3.1.1] - 2018-07-12

### Fixed

- Fixed JavaScript `minWidth` is undefined.
- Fixed which buttons are displayed when using Dynamic Sorting.
- Fixed `Undefined Index: post_id` error.

### Changed

- Changed default `Float Before Content` from `ON` to `OFF`.
- Removed excess printing of `Float Before Content` variable.

## [3.1.0] - 2018-07-09

### Fixed

- Fixed incompatibility with Yoast update.
- Fixed side floating button fade effect.
- Fixed the shares icon CSS in the side floating buttons.

### Added

- Added an option for post-specific share recovery.
- Added an option to show/hide buttons before content.
- Added `noopener noreferrer` to outbound links.
- Added two Pinterest image selectors.
- Added the Pinterest character counter.
- Added Google Tag Manager compatibility for Click/Event Tracking.

### Removed

- Removed the StumbleUpon button.

### Changed

- Updated references of `http` to `https` where possible.
- Updated the Facebook share count system.
- Updated notifications to read from WarfarePlugins server.

### Maintenance

- Major update to the metadata caching system.
- Major updates on floating buttons and mobile buttons.
- Updated the plugin update processes.
- Ensured all custom post types are showing in the "Position Share Buttons" display section.

## [3.0.9] - 2018-06-08

### Fixed

- Fixed slow page loads on some sites.
- Added notice to warn that StumbleUpon is being removed at the end of the month.
- Fixed buttons not showing on mobile in some cases.
- Removed calls to Google Plus' and LinkedIn's APIs since they no longer offer share counts.
- Fixed the Pinterest fallback image functionality.
- Fixed admin settings page not saving in some instances.
- Fixed the `post_id` parameter not working in the shortcodes.
- Fixed the Buffer share button showing plusses instead of spaces.
- Fixed Bitly Authentication occasionally not working.
- Fixed some undefined index errors.

## [3.0.8] - 2018-05-24

### Fixed

- Fixed `Undefined Index` notices.
- Fixed `File not found` errors.
- Custom Color and Custom Color Outlines are fully functional.
- Floating bar on mobile is back to normal.
- Fixed global/post-specific setting incompatibilities.
- Total shares are responsive to the settings, shortcode, and function.
- Right floating buttons are properly positioned.
- Fixed conflict with buttons showing on Archive/Category post types.
- The shortcode and `social_warfare()` function behave as expected.
- Yummly and StumbleUpon buttons are back.
- Created default settings for float position, location, and size.
- Improved the migration mechanism.

## [3.0.0] - 2018-05-08

### Changed

- Rewrote the core mechanics of the plugin to a class-based system.
- Added top floating bar for mobile.
- Added circular button option.
- Normalized variable and function naming conventions.
- Updated CSS to reflect new class names and keys.
- Share windows now appear in the center of the browser.
- Added toggle to print/not print OG output.

### Added

- Created a custom CSS field for Click To Tweet.
- Added an option for right floating buttons.
- Added size option for floating buttons.
- Added vertical placement options for floating buttons.
- Added option to select from all post-types for the Widget.

### Fixed

- Fixed URL Encoding for social network links.
- URLs are no longer created for attachment or media items.

### Maintenance

- Updated copyright dates.
- Made the buttons preview its own section in the options page.
- Moved Tweet Count registration from Registrations to Social Identity.

## [2.3.5] - 2017-01-12

### Changed

- Changed the Twitter counter from 140 characters to 280 characters.
- Changed the WhatsApp button to also appear on desktop since there are now desktop apps for it.

### Added

- Added a hook to change the location of the menu link in the dashboard.
- Added a filter for adjusting the share recovery URL, especially on development sites.

### Fixed

- Refined the pin image hover button layout.
- Adjusted the radius on the "Leaf on the Wind" layout's CSS.
- Forced `text-transform` to none on icons so that the icons do not show up as text.
- Only output the cache trigger on published posts.
- Adjusted the DOM loaded event to use native JS rather than jQuery.
- Various CSS and minor bug fixes.
- Updated to block shortlinks on attachments.
- Changed the counter error message on the CTT generator.
- Fixed a CSS conflict with UI Tabs.

## [2.3.4] - 2017-12-06

### Changed

- Changed the Twitter counter from 140 characters to 280 characters.
- Changed the WhatsApp button to also appear on desktop since there are now desktop apps for it.

### Added

- Added a hook to change the location of the menu link in the dashboard.
- Added a filter for adjusting the share recovery URL, especially on development sites.

### Fixed

- Refined the pin image hover button layout.
- Adjusted the radius on the "Leaf on the Wind" layout's CSS.
- Forced `text-transform` to none on icons so that the icons do not show up as text.
- Only output the cache trigger on published posts.
- Adjusted the DOM loaded event to use native JS rather than jQuery.
- Various CSS and minor bug fixes.

## [2.3.3] - 2017-09-27

### Added

- Added "OpenShareCount.com" as an alternative source for Twitter share counts.

### Changed

- Moved some functions and classes from the Pro addon into core so that they can be used by all addons.
- Updated the style of the image hover Pin button to be more consistent with the rest of the buttons.

### Fixed

- Fixed the UTM tracking parameters from a bug that would turn them off if they were turned on for the pin button.
- Fixed some typos in the `readme.txt`.
- Moved all registration functions to use the WordPress HTTP API instead of cURL.
- Setup the update checker to check for updates through Easy Digital Downloads (our store) rather than through GitHub.
- Changed the registration functions to be hookable, allowing it to track multiple registrations (like addons) rather than only one single registration.
- Added a hook to allow for additional URLs to be checked for share recovery functionality.

## [2.3.2] - 2017-08-25

### Changed

- Updated the verbiage in the `readme.txt`.

### Fixed

- Fixed an error that was causing a handful of sites to lock up.
- More improvements to the registration functions.
- Fixed the shortcode parameters.

## [2.3.1] - 2017-08-20

### Fixed

- Fixed some issues with the registration system.
- Fixed the CSS that controls the layout of the plugin logo on the options page.
- Adjusted some CSS for the options on the options page.

## [2.3.0] - 2017-08-18

### Changed

- Migrated the registration system from WooCommerce to EDD (THIS IS HUGE!).
- Fixed some CSS for the button icon and count alignment.

### Maintenance

- Updated the share cache function for pin and OG stuff.

## [2.2.11] - 2017-07-27

### Changed

- Updated the Pinterest cache rebuild logic.
- Fixed a CSS bug on iOS mobile display.
- Updated the screen options function to return `$display_boolean` instead of always returning true.

### Fixed

- Fixed a Facebook API JavaScript error.
- Updated the logic for the Popular posts widget.
- Fixed an undefined index error on the Bitly cache reset.
- Modified the way the cache trigger is validated.

## [2.2.10] - 2017-07-03

### Fixed

- Fixed the way languages are loaded.
- Fixed the way Pin image sources and pin description image sources are loaded.
- Adjusted how the Bitly functions fetch links and determine when the cache is expired.
- Set the Bitly cache to be deleted when a post is updated in case the permalink has changed.
- Fixed the way that the buttons appear on Right-to-Left direction sites.
- Adjusted how share counts are updated. They should be far more reliable now.

## [2.2.9] - 2017-06-29

### Added

- Added Brazilian Portuguese to available languages.

### Fixed

- Fixed some formatting and functionality of the email button.
- Fixed an unusual HTML issue in the OG title tag.

## [2.2.8] - 2017-06-29

### Added

- Added the option to use the custom Pinterest image for the image hover pin buttons rather than the image being hovered.
- Added the option to use the custom Pinterest description for the image hover pin buttons rather than the alt text of the image.
- Added the option to turn off UTM tracking for Pinterest since Pinterest seems to tally pin counts separately when UTM is used.
- Added an option to force new shares. Normally the plugin ignores new share counts if the count is lower than the count that we previously fetched from the APIs. If you activate this feature, it will go with the new count even if it is lower than previously reported by the APIs.

### Fixed

- Fixed an issue that was affecting share counts being fetched on some sites.
- Fixed an issue that was affecting the open graph image on some sites when posts were scheduled.
- Fixed an issue that was causing some sites not to generate Bitly links on new posts.
- Added some debugging tools to make it easier to diagnose any future issues with Bitly links.
- Added a conditional to catch an undefined index warning.
- Changed the link in the head HTML comment to be `https`.

## [2.2.7] - 2017-06-15

### Fixed

- Fixed a 500 internal server error.
- Fixed a CSS bug that would sometimes cut off the bottom pixel of the button's border.
- Added `HTML Entity Decode` function to ensure foreign characters are populated correctly in open graph tags.

## [2.2.6] - 2017-05-24

### Added

- Added Event Tracking for Click-to-Tweets and Image Pins.

### Fixed

- Fixed the CSS for the total shares in non-English foreign languages.
- Fixed a notice that would sometimes appear on attachment pages.
- Fixed an error that caused share counts to not update for some users.

## [2.2.5] - 2017-05-10

### Fixed

- Fixed a PHP warning that appeared on some user's websites: `Warning: DOMDocument::loadHTML(): Empty string supplied as input`.

## [2.2.4] - 2017-05-09

### Added

- Added a feature to set `og:type` values for all post types with individual post control via the `swp_og_type` custom field.
- Added information links for all options sets on the admin options page.
- Added a feature to add a Pinterest image that is picked up by the Pinterest browser extensions.
- Added better support for buttons being displayed on very tiny screens.
- Added system checks to ensure that the site is using a compatible version of PHP, WordPress, cURL, etc.

### Fixed

- Fixed a few random PHP warnings and errors.

### Maintenance

- Added a filter to remove script and style tags from meta descriptions. They will now only be text. No HTML allowed.
- Added UTM parameters to the Pinterest share links.
- Updated lots of in-file code documentation.
- Updated the functionality of the `?swp_cache=rebuild` URL parameter.
- Updated Facebook share link from `http` to `https`.
- Updated Italian and French translations to 100%.
- Reorganized all file and folder organization structures.
- Refactored and reorganized the code in all of the social network files.
- Fixed the `no_pin` class. You can now add a class of `no_pin` to an image to opt it out from having a Pinterest hover share button.

## [2.2.3] - 2017-02-22

### Fixed

- Fixed a misnamed function that was causing a "Call to undefined function" error when using the shortcode that specifically names which network buttons to show. For example, this was fine: `[social_warfare]`, while this would throw the error: `[social_warfare buttons="Twitter,Facebook,Google Plus,Pinterest,Total"]`. This update fixes it so that it no longer throws any errors.

## [2.2.2] - 2017-02-21

### Changed

- Changed the widget titles from `h3`'s to `h4`'s to be more consistent with how other widgets work in WordPress plugins and themes.

### Removed

- Removed the Pinterest character counter. Pinterest has now fixed the bug that was causing descriptions in shares to get truncated, which means that once again we no longer need to count characters.

### Added

- Add a "no_pin" class to the pin it hover button. If you add the class "no_pin" to an image, it will not get a pin hover button attached to it.

### Fixed

- Fixed the pin hover button. We refactored a couple of pieces of the JavaScript that control the hover button so it should work properly under most circumstances.
- Fixed the Really Simple SSL compatibility issue. You should now be able to use share recovery without Really Simple SSL forcing the recovery URL to be `https` when trying to recover from `http`.

## [2.2.1] - 2017-01-05

### Fixed

- Fixed the WhatsApp button.
- Fixed some invalid integer warnings that appeared in PHP 7.1.
- Made it so the social shares columns only display on pages and posts admin pages.
- Fixed a conflict with Yoast open graph tags on the archives pages.
- Fixed a conflict with lazy load plugin and the Pinterest image hover button.
- Fixed a padding issue on the button counts for mobile.
- Fixed the screen options tab from bumping into the header menu on our admin options page.
- Fixed an issue with button outlines.

## [2.2.0] - 2016-11-29

### Important

- Social Warfare has been developed into a core free plugin with the ability to install addons for additional functionality. If you are a premium user, you'll need to [download](https://warfareplugins.com/updates/social-warfare-pro/social-warfare-pro.zip) and install the Social Warfare - Pro addon to immediately regain access to all premium features. Additional addons are currently under development and will be released in the near future.

### Maintenance

- Refactored all of the Open Graph and Twitter Card output logic.
- Broke the meta tags into two filters. One creates the values. The second compiles them into HTML for output. This can allow people to hook in and change specific values on the fly via an `add_filter()` call.
- Refactored all of the cache functions.
- Added a function to clean out meta fields that are no longer used or needed.
- Added a check for invalid responses from Facebook's API.
- Adjusted the default logic and fallback color system for the side custom color option.
- Refactored the Pinterest image logic.
- Added an undefined check for `swpPinit` to avoid JS notices in the admin area.
- Fixed an undefined index notice.
- Removed all non-public post types from the display locations settings.
- Added a homepage-specific location setting.
- Added a function to make the plugin not duplicate share counts when duplicating a post via the Duplicate Post plugin.
- Consolidated some functions and files for more consistent organization.
- Fixed Google Click/Event Tracking.
- Modified the hover pin so it's not created until after all images have finished loading.
- Fixed the raw share text from sometimes showing up in excerpts.
- Made the buttons not output on posts embedded into other posts (like embedded recipes).
- Patched to prevent buttons from being added to a post more than once.
- Removed the "active" index to prevent certain notices.
- Fixed custom color outlines on hover.
- Fixed an issue with double dividers on the admin options page.
- Fixed an issue that would sometimes cause Facebook counts to disappear.
- Fixed the `http/https` share recovery for pages.

## [2.1.3] - 2016-11-04

### Changed

- Updated how the registration check determines if it's been 30 days since the last check.

### Fixed

- Fixed an alignment issue with the pin hover button for Internet Explorer.
- Updated how the Facebook cache rebuild works when the plugin is set to the legacy cache rebuild method.

## [2.1.2] - 2016-11-02

### Fixed

- Fixed the "New Post" bug.
- Improved registration logic.
- Improved the cache rebuild logic.
- Fixed a CSS alignment bug for IE.
- Adjusted the debounce frequency for scroll events.
- Modified how the floating bars hide and reveal.
- Added compatibility for Grid plugin.
- Made all the link filters return modified arrays. This allows for multiple hooks to be added.
- Added the share recovery feature to the Facebook counts.
- Made the Facebook share recovery detect and filter out exact matches.
- Updated the alignment of the button icons and labels.

## [2.1.1] - 2016-10-26

### Fixed

- Fixed the share count issue. We had reports that share counts were not updating on some of the sites.
- Added versioning to the admin scripts and styles. This will prevent users from having to clear browser cache when the files are modified in an update.
- Added a counter to the Pinterest description field as we've discovered that Pinterest cuts off descriptions around 140 characters on share buttons.
- Changed the hover detection method to avoid conflicts across different versions of jQuery for when users are manually declaring a version of jQuery different from what is bundled into WordPress core.
- Added a CSS rule for the `aligncenter` class so the hover Pin-it function will keep centered images centered.
- Fixed a minor JavaScript error in the cache rebuild process.

## [2.1.0] - 2016-10-24

### Important

- This update has been an intensely focused effort to eliminate bugs and maximize the compatibility of our plugin with all other plugins and themes. We also want to give a big shout-out to Rob Neu for helping us audit through the code and make many of these improvements.

### Added

- Added an option to move the side floating buttons to the horizontal bottom floating position when the screen is too small to display the side floating buttons properly (like on mobile).
- Added a separate custom color option for the side floating buttons.
- Added `nofollow` to all the button links.
- Added an `alt` and `title` tag to the popular posts widget thumbnails.

### Changed

- Overhauled / Improved the registration logic.
- Refactored the options page settings.
- Deprecated some unused functions.
- Added a helper function for getting the site URL.
- Cleaned up the share cache rebuild process.
- Moved the mobile detection to the client side to avoid caching collisions with the showing/hiding of the WhatsApp button.
- Removed all custom jQuery functions to avoid collisions.
- Added translation functions to the admin options page. Translations can now be added for that.
- Cleaned up some alignment issues with the side floating buttons.
- Fixed the layout of the button version of the side floating buttons.
- Changed the sizing of the buttons from JS to FlexBox.
- Changed all scale properties from JS to pure CSS.
- Minified all the admin scripts and CSS.
- Removed the fade-in effect from the buttons. They are now properly sized from the start so there's no need to keep them hidden while sizing logic is adjusting them. Instead, they can now be visible from page load.
- Renamed the meta box classes to avoid collisions with other plugins or themes that use the same meta box class.
- Added throttles and debounces to some of the JS events.
- Improved the JS and layout of the Image Hover Pin Button.
- Moved all asset files to a single location.
- Improved script and style loading.
- Encapsulated JS within an anonymous function to improve compatibility with other plugins and themes.
- Removed the `outerHTML` function.
- Cleaned up options page and Click to Tweet assets.

## [2.0.7] - 2016-10-10

### Fixed

- Made some more major updates in response to Facebook's API changes. We've tested this on a dozen sites, and we're experiencing a 100% success rate with the fix so far. If you're experiencing inaccurate share counts, install this update, ensure that the Cache Method is set to Advanced Page Caching on the Advanced Tab of the Social Warfare option page, then wait a few hours. Within a few hours (24 at the very, very latest), all of your Facebook share counts should be 100% fully recovered.

## [2.0.6] - 2016-09-21

### New

- Made some major updates in response to Facebook's API changes. Hopefully, all the shares will count accurately once again.
- Added cross-domain share recovery.
- Updated the way Bitly functions shorten the URLs. Custom domain short links should now be fully supported.

### Fixed

- Fixed a couple of minor PHP notices.
- Made some updates to the registration function.

## [2.0.5] - 2016-08-22

### New

- Updated to Facebook’s new share API.
- Shortcode parameters for `buttons=` now support spaces.
- Better detection of content location for some themes.
- Floating buttons now ignore shortcode (to avoid wonky behavior).
- Updated floating position settings to apply to both side and horizontal instances.
- Added Greek translation.

### Fixed

- Cleaned up some outdated JavaScript.
- Fixed URL encoding for special characters in Pin button descriptions.
- Fixed OG width/height output from showing when no image was uploaded.
- Fixed a bug with the scale/resizing feature.
- Got rid of a few minor notices.
- Fixed a hover bug.
- Fixed alignment issue with side floating share buttons.

## [2.0.4] - 2016-08-09

### Added

- Image Hover Pin Button applies to linked images.
- Added minimum dimensions for Image Hover Pin Button.
- Pinterest image and Twitter handles are now included in the cache rebuild process.
- Social shares column to Pages tab in WordPress.
- Changed Pin button to "Save" and added Pin icon on hover.
- Yummly Category and Tags function are now operational.
- PHP7 compatibility.

### Fixed

- Button ordering on floating buttons is now able to sort dynamically again.
- Image Hover Pin Button image alignment fix.
- Cleaned out false PHP error and undefined index notices.
- Image Hover Pin Button no longer hinders image saving.
- WPML compatibility.
- Extra whitespace issue being seen by some sites resolved.

## [2.0.3] - 2016-07-27

### Fixed

- Fixed invisible buttons reappearing.

## [2.0.2] - 2016-06-21

### Fixed

- Fixed an issue where the Twitter handle wasn’t displaying correctly.

## [2.0.1] - 2016-06-14

### Fixed

- Fixed various bugs related to the display of social buttons on certain themes.

## [2.0.0] - 2016-05-30

### Changed

- Complete rewrite of the plugin codebase to improve performance and maintainability.
- Migrated to a more modern PHP code structure, following object-oriented principles.
- Introduced a new UI/UX design for the settings panel.
- Enhanced the plugin's compatibility with the latest version of WordPress.