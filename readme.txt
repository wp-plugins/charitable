=== Charitable ===
Contributors: WPCharitable, ericdaams
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=paypal%40164a%2ecom
Tags: donations, donate, donation plugin, fundraising, fundraising plugin, non-profit, non-profits, charity, churches, commerce, crowdfunding, paypal donations, paypal, stripe, stripe donations, campaigns, gifts, giving, wordpress fundraising, wordpress donations, wordpress donation plugin
Requires at least: 4.1
Tested up to: 4.3.1
Stable tag: 1.1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Charitable is a powerful, extendable fundraising plugin created to help non-profits accept donations on their own website. 

== Description ==

**[Charitable](https://wpcharitable.com?utm_campaign=readme&utm_medium=description_tab&utm_content=intro)** is the WordPress fundraising alternative for non-profits, built to help non-profits raise money on their own website. 

You can accept PayPal or offline donations right out of the box, with support for other gateways available as [extensions](https://wpcharitable.com/extensions?utm_campaign=readme&utm_medium=description_tab&utm_content=intro).

= Unlimited fundraising campaigns = 

You can set up as many campaigns as you'd like with Charitable, and you can tailor them to your needs. You can set up suggested donation amounts, allow them to donate as much as they want, or do both.

Need to set a fundraising goal? No problem. 

Running a time-sensitive campaign? Set an end date for your campaign and give it a sense of urgency.

= Easy to use =

Install, activate and create your first fundraising campaign in less than 5 minutes. With Charitable, adding campaigns is a straightforward, intuitive process. Less time setting up campaigns means more time for you to raise awareness for your campaign.

= Accept credit card donations = 

By coupling Charitable with our **[Stripe extension](https://wpcharitable.com/extensions/charitable-stripe?utm_campaign=readme&utm_medium=description_tab&utm_content=credit-card-donations)**, you can accept credit card donations directly on your website. Improve your donor conversion rates by keeping them on your website instead of redirecting them to PayPal.

Want to use a different payment gateway? [Let us know](https://wpcharitable.com/support?utm_campaign=readme&utm_medium=description_tab&utm_content=credit-card-donations). 

= Skip the transaction fees = 

Other fundraising software charges you for every donation you receive. 

Charitable is different. We won't charge you any transaction fees and you can use Charitable for free.

= Works with any theme = 

Charitable has been designed to work with any well-coded theme, including the default WordPress themes.

= Extensions = 

One size does *not* fit all. That's why we made Charitable an extendable platform. 

* **[Stripe](https://wpcharitable.com/extensions/charitable-stripe?utm_campaign=readme&utm_medium=description_tab&utm_content=extensions)** - Accept credit card donations on your website.
* **[Anonymous Donations](https://wpcharitable.com/extensions/charitable-anonymous-donations?utm_campaign=readme&utm_medium=description_tab&utm_content=extensions)** - Allow people to make donations anonymously.
* **[User Avatars](https://wpcharitable.com/extensions/charitable-user-avatar?utm_campaign=readme&utm_medium=description_tab&utm_content=extensions)** - Let your donors upload their own profile photo to your site, instead of using their Gravatar profile.
* **[Simple Updates](https://wpcharitable.com/extensions/charitable-simple-updates?utm_campaign=readme&utm_medium=description_tab&utm_content=extensions)** - Add updates about your fundraising campaigns.

Looking for more? 

[View all extensions](https://wpcharitable.com/extensions?utm_campaign=readme&utm_medium=description_tab&utm_content=extensions). 

= Get involved =

Join the community on [WP Charitable](https://wpcharitable.com?utm_campaign=readme&utm_medium=description_tab&utm_content=get-involved). 

Developers can contribute to Charitable on our [Github repository](https://github.com/Charitable/Charitable).

== Installation ==

1. Upload `charitable.php` to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Go to Charitable > Add Campaign to create your first campaign!

== Frequently Asked Questions ==

= How do I add a grid of campaigns to my page? = 

Easy. Just add `[campaigns]` into your page content. [Read more](https://wpcharitable.com/documentation/the-campaigns-shortcode/?utm_campaign=readme&utm_medium=faq_tab&utm_content=campaigns-shortcode).

= Does Charitable support recurring donations? =

Support for recurring donations will be added as an extension, but is not yet available.

= How do I get support? = 

You can post in the [support forum](https://wordpress.org/support/plugin/charitable) or reach us via [our support form](http://wpcharitable.com/support?utm_campaign=readme&utm_medium=faq_tab&utm_content=support). 

== Screenshots ==

1. Creating a campaign.
2. A campaign running on Twentyfifteen (the default WordPress theme).
3. A grid of campaigns, added using the `[campaigns]` shortcode.
4. Setting up Charitable: The General settings area. 
5. Setting up Charitable: The Payment Gateways settings area. 
6. Setting up Charitable: The Email settings area. 

== Changelog ==

= 1.1.3 = 
* Enhancement: Added the ability to change the dimensions of the user avatars added using Charitable User Avatar, with a PHP filter function.
* Fixes an issue where only having one active gateway meant that those gateway's donation form fields would not show.
* Fixes a problem with the permalinks structure that prevented you being able to create pages with slugs of "/donate/" or "/widget".
* Fixes the WP Editor form field template to prevent the text from being wrapped in HTML tags.

= 1.1.2 = 
* Security Fix: Prevent unauthorized users accessing your donation receipt.
* Fix: Localization with the .po/.mo files now really does work correctly. For real this time.

= 1.1.1 = 
* Fix: Emails will now correctly be sent with the body, headline and subject you set, instead of the default. 

= 1.1.0 = 
* Enhancement: Added a new email that can be sent when a campaign has finished. 
* Fix: Localization with the .po/.mo files now works correctly.
* Fix: Chrome 45 bug when clicking directly on suggested amount inputs is resolved.

= 1.0.3 =
* Improvement: Using `wp_list_pluck` instead of `array_column` for compatibility with versions of PHP prior to 5.5.
* PHP 5.2 Compatibility: Avoid T_PAAMAYIM_NEKUDOTAYIM error in older versions of PHP.

= 1.0.2 =
* Fix: Added missing file into the repo. 

= 1.0.1 =
* Improvement: Moved the user dashboard functionality into the core of the plugin, so that it is always available.
* Fix: The installation routine now flushes permalinks correctly -- no more "Page not Found" problems!

= 1.0.0 =
* Initial release