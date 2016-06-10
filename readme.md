<!-- DO NOT EDIT THIS FILE; it is auto-generated from readme.txt -->
# GoDaddy Email Marketing

![Banner](assets/banner-1544x500.png)
Add the GoDaddy Email Marketing plugin to your WordPress site! Easy to set up, the plugin allows your site visitors to subscribe to your email lists.

**Contributors:** [godaddy](https://profiles.wordpress.org/godaddy), [humanmade](https://profiles.wordpress.org/humanmade), [xwp](https://profiles.wordpress.org/xwp), [fjarrett](https://profiles.wordpress.org/fjarrett), [jonathanbardo](https://profiles.wordpress.org/jonathanbardo)  
**Tags:** [email](https://wordpress.org/plugins/tags/email), [forms](https://wordpress.org/plugins/tags/forms), [godaddy](https://wordpress.org/plugins/tags/godaddy), [mailing list](https://wordpress.org/plugins/tags/mailing list), [marketing](https://wordpress.org/plugins/tags/marketing), [newsletter](https://wordpress.org/plugins/tags/newsletter), [opt-in](https://wordpress.org/plugins/tags/opt-in), [signup](https://wordpress.org/plugins/tags/signup), [subscribe](https://wordpress.org/plugins/tags/subscribe), [widget](https://wordpress.org/plugins/tags/widget), [contacts](https://wordpress.org/plugins/tags/contacts)  
**Requires at least:** 3.8  
**Tested up to:** 4.5  
**Stable tag:** 1.1.0  
**License:** [GPLv2 or later](http://www.gnu.org/licenses/gpl-2.0.html)  

[![Build Status](https://travis-ci.org/godaddy/wp-godaddy-email-marketing.svg?branch=master)](https://travis-ci.org/godaddy/wp-godaddy-email-marketing) [![Coverage Status](https://coveralls.io/repos/godaddy/wp-godaddy-email-marketing/badge.svg?branch=master)](https://coveralls.io/github/godaddy/wp-godaddy-email-marketing) [![Built with Grunt](https://cdn.gruntjs.com/builtwith.svg)](http://gruntjs.com) [![devDependency Status](https://david-dm.org/godaddy/wp-godaddy-email-marketing/dev-status.svg)](https://david-dm.org/godaddy/wp-godaddy-email-marketing#info=devDependencies) 

## Description ##

The GoDaddy Email Marketing Signup Forms plugin makes it easy to start building an email list to drive repeat traffic to your WordPress site! Use this plugin to add a signup form to your site in no time flat.

With a GoDaddy Email Marketing starter account, you can collect as many email addresses as you like for free. And you can send up to 50 total emails to try it out. To learn more about GoDaddy Email Marketing, check out an overview [here](https://www.godaddy.com/online-marketing/email-marketing).

Once the plugin is activated, you can easily add a default signup form to your site using a widget. Or you can build your own custom signup form in GoDaddy Email Marketing and add it to your site by using a widget, shortcode, or template tag.

Setup is easy; in the plugin Settings, simply enter your GoDaddy username and GoDaddy Email Marketing API key. Don’t have one? The plugin makes it easy to sign up.

**Official GoDaddy Email Marketing Signup Forms plugin features:**

* Automatically add new forms for users to subscribe to an email list of your choice.
* Insert unlimited signup forms using the widget, shortcode, or template tag.
* Try GoDaddy Email Marketing for free — no credit card required.

**Languages Supported:**

* English
* Dansk
* Deutsch
* Ελληνικά
* Español
* Español de México
* Suomi
* Français
* हिन्दी
* Bahasa Indonesia
* Italiano
* 日本語
* 한국어
* मराठी
* Bahasa Melayu
* Norsk bokmål
* Nederlands
* Polski
* Português do Brasil
* Português
* Русский
* Svenska
* ไทย
* Tagalog
* Türkçe
* Українська
* Tiếng Việt
* 简体中文
* 香港中文版
* 繁體中文

**Find a bug?**

Please fill out an issue [here](https://github.com/godaddy/wp-godaddy-email-marketing/issues).

## Installation ##

* Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer.
* Activate the plugin.
* Go to the plugin Settings page (under Settings > GoDaddy Email Marketing Settings).
* Enter your account email address and API key (found in the Settings section of your GoDaddy Email Marketing account).
* Click **Save Changes**.

After your account is verified, you can insert a form into your site by using a widget, shortcode, or template tag:

* **Widget** Go to Appearance > Widgets, find the GoDaddy Email Marketing widget, and drag it into the widget area of your choice. You can then add a title and select a form!
* **Shortcode** Add a form to any post or page by adding the shortcode (e.g., `[gem id=123456 ]`) in the page/post editor. You can find a form's ID on the GoDaddy Email Marketing Settings page.
* **Template** tag Add the following template tag into any WordPress file: `<?php gem_form; ?>`. For example: `<?php gem_form( 91 ); ?>` You can find a form's ID on the GoDaddy Email Marketing Settings page.

That's it. You're ready to go!

## Frequently Asked Questions ##

### What is GoDaddy Email Marketing? ###
GoDaddy Email Marketing is the easiest way to create, send, share, and track email newsletters online. It's for people who want email marketing to be simple.

### Do I need a GoDaddy Email Marketing account to use this plugin? ###
Yes, this plugin requires a [GoDaddy Email Marketing](https://www.godaddy.com/business/email-marketing) account.

### Is there a widget? ###
Absolutely. Use it by finding the GoDaddy Email Marketing widget under Appearance >Widgets in the WordPress Dashboard and dragging it into the widget area of your choice. You can then add a title and select a form!

### Is there a shortcode? ###
Yes! You can add a form to any post or page by adding the shortcode with the form ID (e.g., `[gem id=123456 ]`) in the page/post editor. Form IDs are listed on the GoDaddy Signup Forms Settings page.

### Is there a template tag? ###
Yup! Add the following template tag into any WordPress file: `<?php gem_form( $form_id ); ?>`. For example: `<?php gem_form( 91 ); ?>`. Form IDs are listed on the GoDaddy Signup Forms Settings page.

### Where can I find the API Key? ###
You can find your API key in the Settings section of your GoDaddy Email Marketing account.


## Screenshots ##

### Settings screen.

![Settings screen.](assets/screenshot-1.png)

### A full list of your GoDaddy Email Marketing Webforms, with ready shortcodes.

![A full list of your GoDaddy Email Marketing Webforms, with ready shortcodes.](assets/screenshot-2.png)

### The widget, on the widgets page.

![The widget, on the widgets page.](assets/screenshot-3.png)

### The widget, on the front-end.

![The widget, on the front-end.](assets/screenshot-4.png)

## Changelog ##

### 1.1.0 ###
* UI & UX overhaul using tabbed navigation & enhanced admin notices.
* Added the `debug` setting to replace the `gem_debug` filter.
* Added Shortcake plugin integration for the `gem` shortcode.
* Language support for many new locales.

### 1.0.6 ###
* Fixed shortcode display and localization bugs [#12](https://github.com/godaddy/wp-godaddy-email-marketing/pull/12)
* Localization updates

### 1.0.5 ###
* Refresh branding

### 1.0.4 ###
* Localization
* Code style improvements
* Unit tests

### 1.0.3 ###
* Added support for web form fancy fields
* Made some styling changes to mobile view

### 1.0.2 ###
* Fixed incorrectly loaded stylesheet
* Minor style improvements to front-end form output

### 1.0.1 ###
* Move the "Powered by GoDaddy Link" below the submit button and link it up to the correct place

### 1.0 ###
* Initial version. forked from the Mad Mimi Sign Up Forms WordPress Plugin: https://wordpress.org/plugins/mad-mimi-sign-up-forms/


