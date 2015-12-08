=== Plugin Name ===
Contributors: Nicholas Cardot
Tags: social media, social sharing
Requires at least: 3.0.1
Tested up to: 5.0.0
Stable tag: 4.3

Your Ultimate Social Sharing Arsenal

== Description ==

The description is going to go here eventually.

== Installation ==

1. Upload social-warfare.zip via the plugins page in the WordPress admin.
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

= What about foo bar? =

Answer to foo bar dilemma.

== Changelog ==

= 0.1 =
* This is the original plugin

= 0.2 =
* Added the ability to automatically update the plugin from within the WordPress admin.

= 1.2 = 

This update has been a big push to create developer hooks that will hopefully get more developers involved and help to expand the available features through the creation of addons or functions.php recipes. In order to do this, we've been toiling behind the scenes rebuilding many files and functions from the ground up. Here's a look what this update brings with it:
1. Changed the way the user's options selections are fetched drastically reducing database calls.
2. Changed the way the buttons are sized 
3. Built in a multilingual functionality. There is currently only one language (Enlish), but now others can easily be added to the plugin. Feel free to send us translations that you would like to see added to the plugin.
4. Changed the way share counts are fetched. Previously each share count was fetched one at a time beginning the request as soon as the previous request was finished. Now all share requests fire off at the same time. This will drastically reduce the amount of time it takes to fetch new share counts when the cache on the existing counts expires.
5. Added a developer hook to allow additional link shortening integrations to be created.
6. Added a developer hook to allow additional analytics parameters to be used with Social Warfare.
7. Changed the priority of the Social Warfare function hook to fix the double share button bug.
8. Added developer hooks for adding options to the Social Warfare settings page and also to fetch the options that the user has set.
9. The developer hooks necessary to add additional social network share buttons are about 75% complete.
10. Adjusted some CSS to clean up the custom options display on the post editor.
11. Fixed some trailing commas in the javascript file that may have interfered with minifying the javascript.
12. Fixed an issue with the naming of our widget function to avoid conflicts with other plugins that were using the same naming pattern.

== Upgrade Notice ==

= 0.1 =
Yup. This one was cool. It was our initial launch.

= 0.2 =
Update to this one to enable automatic updating.