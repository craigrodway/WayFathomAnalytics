<?php
namespace ProcessWire;

require_once(__DIR__ . '/WayFathom.php');

/**
 * WayFathomAnalytics
 *
 * Main Fathom Analytics module for ProcessWire.
 * Display your Fathom dashboard in the PW admin panel and easily generate markup for the tracking code.
 *
 * @author Craig A Rodway
 *
 * ProcessWire 3.x, Copyright 2016 by Ryan Cramer
 * https://processwire.com
 *
 */

class WayFathomAnalytics extends WireData implements Module, ConfigurableModule
{


	public static function getModuleInfo()
	{
		return [
			'title' => 'WayFathomAnalytics',
			'version' => '0.0.1',
			'summary' => __('Fathom Analytics for ProcessWire. Display your Fathom dashboard in the PW admin panel and easily generate markup for the tracking code.'),
			'author' => 'Craig A Rodway',
			'href' => 'https://github.com/craigrodway/WayFathomAnalytics',
			'autoload' => false,
			'singular' => true,
			'permanent' => false,
			'icon' => 'bar-chart',
			'requires' => [
				'PHP>=7.0.0',
				'ProcessWire>=3.0.0',
			],
			'installs' => ['ProcessWayFathomAnalytics', 'MarkupWayFathomAnalytics'],
		];
	}


	static public function getDefaults()
	{
		return [
			'siteId' => '',
			'sharePassword' => '',
			'customDomain' => '',
			'trackingAddScript' => 0,
			'trackingHonorDnt' => 0,
			'trackingAutomatic' => 1,
			'trackingCanonical' => 1,
			'trackingExcludeDomains' => '',
			'trackingIncludeDomains' => '',
			'trackingSpaMode' => '',
		];
	}


	static public function getModuleConfigInputfields(array $data)
	{
		wire('config')->scripts->add(wire('config')->urls->WayFathomAnalytics . 'WayFathomAnalytics.js');

		$inputfields = new InputfieldWrapper();

		$defaults = self::getDefaults();
		$data = array_merge($defaults, $data);

		$inputfields->add(self::getSettingsConfig($data));

		if (strlen($data['siteId'])) {
			$inputfields->add(self::getOwnTrackingConfig($data));
		}

		$inputfields->add(self::getEmbedConfig($data));

		return $inputfields;

	}


	static public function getOwnTrackingConfig(array $data)
	{
		$modules = wire('modules');

		wire('config')->js('WayFathomAnalytics', [
			'siteId' => $data['siteId'],
			'scriptUrl' => $modules->get('MarkupWayFathomAnalytics')->getScriptUrl(),
		]);

		$fs = $modules->get('InputfieldFieldset');
		$fs->set('themeOffset', true);
		$fs->name = 'mytracking';
		$fs->icon = 'user-secret';
		$fs->collapsed = Inputfield::collapsedYes;
		$fs->label = __('Block or unblock your own visits');
		$fs->description = __("There are instances where you want to block your own visits, so you loading pages on your site does not skew your stats. This is done by storing a value in something called localStorage (like a cookie) on your device. And yes, this is fully GDPR, CCPA and PECR compliant, as it's opt-in by you.");

        $btn1 = $modules->get('InputfieldButton');
        $btn1->value = 'Block tracking for me';
        $btn1->id = 'blockTrackingForMe';
        $fs->add($btn1);

        $btn2 = $modules->get('InputfieldButton');
        $btn2->setSecondary();
        $btn2->id = 'enableTrackingForMe';
        $btn2->value = 'Enable tracking for me';
        $fs->add($btn2);

		return $fs;
	}


	static public function getSettingsConfig(array $data)
	{
		$modules = wire('modules');

		$fs = $modules->get('InputfieldFieldset');
		$fs->set('themeOffset', true);
		$fs->name = 'settings';
		$fs->icon = 'cog';
		$fs->label = __('Settings');
		$fs->description = __('Configure the main settings for Fathom Analytics.');


		$f = $modules->get('InputfieldText');
		$f->name = 'siteId';
		$f->icon = 'paper-plane';
		$f->label = __('Site ID');
		$f->description = __('The unique tracking ID for your site.');
		$f->value = $data['siteId'];
		$fs->add($f);

		$f = $modules->get('InputfieldText');
		$f->name = 'sharePassword';
		$f->icon = 'lock';
		$f->label = __('Fathom Share Password');
		$f->description = __('Required if you have shared your dashboard privately. Publicly shared dashboards do not need a password.');
		$f->notes = __('Note: Like Fathom, this is not hashed or encrypted.');
		$f->value = $data['sharePassword'];
		$fs->add($f);

		$f = $modules->get('InputfieldText');
		$f->name = 'customDomain';
		$f->icon = 'globe';
		$f->label = __('Custom Domain');
		$f->description = __('Optional. Only use this if you have set up a Custom Domain.');
		$f->detail = sprintf(__('[Read more about custom domains](%s).'), 'https://usefathom.com/support/custom-domains');
		$f->value = $data['customDomain'];
		$fs->add($f);

		return $fs;
	}


	static public function getEmbedConfig(array $data)
	{
		$modules = wire('modules');

		$fs = $modules->get('InputfieldFieldset');
		$fs->set('themeOffset', true);
		$fs->name = 'tracking';
		$fs->icon = 'code';
		$fs->collapsed = Inputfield::collapsedYes;
		$fs->label = __('Tracking code');
		$fs->description = __('Configure different options for the tracking code.');

		$name = 'trackingAddScript';
		$f = $modules->get('InputfieldToggle');
		$f->name = $name;
		$f->icon = 'code';
		$f->label = __('Automatic embed code');
		$f->checkboxLabel = __('Enable');
		$f->description = __('Automatically add the embed code via MarkupWayFathomAnalytics to every front-end page.');
		$f->value = $data[$name];
		$fs->add($f);

		$name = 'trackingHonorDnt';
		$f = $modules->get('InputfieldToggle');
		$f->name = $name;
		$f->icon = 'ban';
		$f->label = __('Honour Do Not Track (DNT)');
		$f->description = __("By default, Fathom tracks every visitor to your website, regardless of them having DNT turned on or not. That's because Fathom is privacy-focused analytics, so nothing personal or identifiable is ever 'tracked'.");
		$f->notes = __("Selecing 'Yes' adds the `honor-dnt=\"true\"` attribute to the script tag.");
		$f->value = (isset($data[$name]) && $data[$name] == '1');
		$fs->add($f);

		$name = 'trackingAutomatic';
		$f = $modules->get('InputfieldToggle');
		$f->name = $name;
		$f->icon = 'flash';
		$f->label = __('Automatic tracking');
		$f->description = __("By default, Fathom tracks a page view every time a visitor to your website loads a page with the script on it. If you don't want that functionality, click No.");
		$f->notes = __("Selecing 'No' adds the `auto=\"false\"` attribute to the script tag.");
		$f->value = (isset($data[$name]) && $data[$name] == '1');
		$fs->add($f);

		$name = 'trackingCanonical';
		$f = $modules->get('InputfieldToggle');
		$f->name = $name;
		$f->icon = 'link';
		$f->label = __('Canonical URL');
		$f->description = __("If there's a canonical URL in place, then by default Fathom uses it instead of the current URL. This is what most customers want, and it's why it's the default. If you want to use the current URL, even if there's canonical (ignoring the canonical), then set this to 'No'.");
		$f->notes = __("Selecing 'No' adds the `canonical=\"false\"` attribute to the script tag.");
		$f->value = (isset($data[$name]) && $data[$name] == '1');
		$fs->add($f);


		$fsmarkup = new InputfieldFieldset();
		$fsmarkup->name = 'domains';
		$fsmarkup->icon = 'globe';
		$fsmarkup->label = __('Excluded or included domains');
		$fs->add($fsmarkup);

		$domains = new InputfieldMarkup();
		$domains->textFormat = Inputfield::textFormatMarkdown;
		$domains->description = __("By default, you can use the tracking code on any domain, it doesn't matter if you use it on multiple sites or domains.")."\n\n";
		$domains->description .= __("You can, however, exclude one or several domains, so the tracker will track things on every domain, _except_ the ones excluded. This is useful to exclude stats being tracked on your local development environment (if you have one).")."\n\n";
		$domains->description .= __("You can also go in the opposite direction and only track stats on a specific domain. For example, if you have 2 development environments and a staging environment, you might want to track stats on your live site, example.com, but not on example.local and not on staging.example.com.");
		$fsmarkup->add($domains);

		$name = 'trackingExcludeDomains';
		$f = $modules->get('InputfieldTextarea');
		$f->name = $name;
		$f->columnWidth = '50%';
		$f->icon = 'times';
		$f->label = __('Exclude domains');
		// $f->textFormat = Inputfield::textFormatMarkdown;
		$f->description = __("Track all visits, except on the domains listed here. Enter one domain per line.");
		$f->notes = __("These domains will be added to the `excluded-domains` attribute on the script tag.");
		$f->value = $data[$name];
		$fsmarkup->add($f);

		$name = 'trackingIncludeDomains';
		$f = $modules->get('InputfieldTextarea');
		$f->name = $name;
		$f->columnWidth = '50%';
		$f->icon = 'check';
		$f->label = __('Include domains');
		$f->description = __("Only track visits on the domains listed here. Enter one domain per line.");
		$f->notes = __("These domains will be added to the `included-domains` attribute on the script tag.");
		$f->value = $data[$name];
		$fsmarkup->add($f);


		$name = 'trackingSpaMode';
		$f = $modules->get('InputfieldRadios');
		$f->name = $name;
		$f->icon = 'file-o';
		$f->label = __('Single page applications');
		$f->description = __("If there's not a Fathom plugin for your specific javascript system, you can use the generic SPA mode. Most applications use HTML5 History API, so the 'auto' mode will typically work as-is. This code checks if the History API is available, and if it's not, it falls back to listening to hash changes.");
		$f->notes = __("Selecing any value other than 'Off' will add the `spa={mode}` attribute to the script tag.");
		$f->value = $data[$name];
		$f->addOptions([
			'' => 'Off',
			'auto' => 'Auto',
			'history' => 'History',
			'hash' => 'Hash',
		]);
		$fs->add($f);


		return $fs;
	}



}
