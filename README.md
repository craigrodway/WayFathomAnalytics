# WayFathomAnalytics

By Craig A Rodway.

[![License: GPL v3](https://img.shields.io/static/v1?label=License&message=GPLv3&color=3DA639&style=flat-square)](https://www.gnu.org/licenses/gpl-3.0)
[![ProcessWire 3](https://img.shields.io/static/v1?label=ProcessWire&message=3.x&color=2480e6&style=flat-square&logo=processwire)](https://github.com/processwire/processwire)

WayFathomAnalytics is a group of modules for the [ProcessWire CMS/CMF](https://processwire.com/) which allows you to view your [Fathom Analytics](https://usefathom.com/) dashboard in the admin panel and (optionally) automatically add and configure the tracking code on front-end pages.

If you appreciate this module and are new to Fathom, [please consider signing up to Fathom using my referral link](https://usefathom.com/ref/SSTMY8) and receive $10 off your first invoice. Thanks!


## What is Fathom Analytics

Fathom Analytics is a simple, privacy-focused website analytics tool for bloggers and businesses.

Stop scrolling through pages of reports and collecting gobs of personal data about your visitors, both of which you probably don't need. Fathom is a simple and private website analytics platform that lets you focus on what's important: your business.

Major features of Fathom Analytics include:

- One screen, all the real-time data you need
- Cookie notices not required (it doesn't use cookies or collect personal data)
- Displays: top content, top referrers, top goals and more

To use use this module, you need have a paid Fathom Analytics account and your dashboard sharing set to public or private with password.


## WayFathomAnalytics module

This module is loosely based on the [official WordPress plugin](https://github.com/usefathom/wordpress-plugin) but has been written from the ground-up for ProcessWire and is separated into 3 parts:

- *WayFathomAnalytics*: Main module where you configure all the settings.
- *ProcessWayFathomAnalytics*: _Process_ module responsible for loading the dashboard in the admin panel.
- *MarkupWayFathomAnalytics*: _Markup_ module to generate the javascript tracking code used to collect data.

Installing the module will create a new page at the root of the admin tree called _Fathom Analytics_ using the name `fathom-analytics`. It will also install a new permission called `fathom-view` which you can use to control access to the analytics page in the admin panel.


## Requirements

You will need the following to make use of this module:

- PHP 7 or newer.
- ProcessWire 3 or newer.
- Fathom Analytics account with a site that has sharing options enabled.


## Installing

### From the Modules Directory

1. Visit the Site > Modules page in your website's admin panel.
2. Click the 'New' tab.
3. Enter 'WayFathomAnalytics' in the 'Module Class Name' field and click 'Get Module Info'.
4. Click the 'Download Now' button.
5. Cick the 'Install Now' button.

### Manually

1. Place the module files in the `/site/modules/WayFathomAnalytics` directory.
2. Visit the Site > Modules page in your website's admin panel.
3. Press the 'Refresh' button.
4. In the 'New' tab, find WayFathomAnalytics in the list and click the 'Install' button.


## Configuring

Once you have installed the modules, you can configure all the settings in the WayFathomAnalytics module. To do this, go to Modules > Configure > WayFathomAnalytics.


### Settings

#### Site ID

Your Site ID is the unique code in the tracking snippet. You can find this in your Fathom Dashboard:

1. Click Settings, then Sites.
2. The Site ID is the second column, which is made up of letters and numbers.
3. Copy and paste this value into the settings page.

#### Fathom Share Password

To see your Fathom Analytics dashboard in ProcessWire, the sharing settings for your Fathom site must be set to 'Viewable by everyone' or 'Viewable by anyone with the share password'.

If your dashboard is 'Viewable by everyone', you can ignore this field. If your dashboard is configured with a share password, enter that password in this field.

You can change this setting in your Fathom Dashboard:

1. Click Settings, then Sites.
2. Click the word in the third column, which is 'Private' by default.
3. Choose your preferred option from the list, and enter a password if you choose that option.
4. Copy and paste the password into the settings page.

#### Custom Domain

If you use the [Custom Domains](https://usefathom.com/support/custom-domains) feature for your site, enter it here. When used, the script will be loaded from your custom domain instead of the default `cdn.usefathom.com`.

The names are automatically generated by Fathom, for example `fox.example.com`.


### Block or unblock your own visits

There are instances where you want to block your own visits, so you loading pages on your site does not skew your stats. This is done by storing a value in something called localStorage (like a cookie) on your device. This is fully GDPR, CCPA and PECR compliant, as it's opt-in by you.

Usually this has to be done by visiting your site where the tracking code is installed, opening the Javascript console in your browser, and running a command.

This section of the module includes two buttons that do the same thing, without having to open the console. When you click one of the buttons to either Block or Enable tracking for you, the tracking script is loaded on-demand (with the `auto="false"` attribute to make sure the current visit isn't tracked) and the relevant code is ran for you.

This panel is only visible when a Site ID has been entered and the module settings have been saved.


### Tracking code

You can find out more about the different options on the [Fathom documentation page](https://usefathom.com/support/tracking-advanced).


#### Automatic embed code

If you want to automatically add the tracking code to front-end pages, enable this option. It will be added just before the closing `</head>` tag.

#### Honour Do Not Track (DNT)

By default, Fathom tracks every visitor to your website, regardless of them having DNT turned on or not. That's because Fathom is privacy-focused analytics, so nothing personal or identifiable is ever 'tracked'. If you want to respect user's DNT preferences, select 'Yes'.

#### Automatic tracking

By default, Fathom tracks a page view every time a visitor to your website loads a page with the script on it. If you don't want that functionality, select 'No'.

#### Canonical URL

If there's a canonical URL in place, then by default, Fathom uses it instead of the current URL. This is what most customers want, and it's why it's the default. If you want to use the current URL, even if there's canonical (ignoring the canonical), then select 'No'.

#### Excluded or included domains

By default, you can use the tracking code on any domain, it doesn't matter if you use it on multiple sites or domains.

You can, however, exclude one or several domains, so the tracker will track things on every domain _except_ the ones excluded. This is useful to exclude stats being tracked on your local development environment (if you have one).

You can also go in the opposite direction and only track stats on a specific domain. For example, if you have 2 development environments and a staging environment, you might want to track stats on your live site, example.com, but not on example.local and not on staging.example.com.

#### Single page applications

If there's not a Fathom plugin for your specific javascript system, you can use the generic SPA mode. Most applications use HTML5 History API, so the 'auto' mode will typically work as-is. This code checks if the History API is available, and if it's not, it falls back to listening to hash changes.


## Using


### Dashboard

After installation, a new page called 'Fathom Analytics' will be visible in the admin panel.

Once you have configured the Site ID (and password, if applicable) this page will display the Fathom dashboard for that site.

This page is only accessible to users in the 'superuser' role by default, but you can make it available to other roles by adding the `fathom-view` permission to them. Go to Access > Roles to manage those.


### Tracking code

If you don't want the tracking code added automatically, you can still use the MarkupWayFathomAnalytics module and do it yourself.

```php
echo $modules->get('MarkupWayFathomAnalytics')->render();
```

Will produce the following HTMl:

```html
<!-- Fathom - beautiful, simple website analytics -->
<script src="https://cdn.usefathom.com/script.js" site="ABCDEF" defer></script>
<!-- / Fathom -->
```

You can pass an array of options to the `render()` method to override any of the saved module values.

```php
echo $modules->get('MarkupWayFathomAnalytics')->render([
	'siteId' => 'UVWXYZ',
	'trackingHonorDnt' => true,
]);
```

The list of relevant options are as follows:


| Key | Value type |
| --- |--- |
| `siteId` | string |
| `customDomain` | string |
| `trackingHonorDnt` | true/false |
| `trackingAutomatic` | true/false |
| `trackingCanonical` | true/false |
| `trackingExcludeDomains` | string |
| `trackingIncludeDomains` | string |
| `trackingSpaMode` | string (auto/history/hash) |
