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
			'version' => '0.0.2',
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
		$defaults = self::getDefaults();
		$data = array_merge($defaults, $data);

		wire('config')->scripts->add(wire('config')->urls->WayFathomAnalytics . 'WayFathomAnalytics.js');

		wire('config')->js('WayFathomAnalytics', [
			'siteId' => $data['siteId'],
			'scriptUrl' => wire('modules')->get('MarkupWayFathomAnalytics')->getScriptUrl(),
		]);

		$inputfields = wire(new InputfieldWrapper());
		$inputfields->add(self::getSettingsConfig($data));
		$inputfields->add(self::getOwnTrackingConfig($data));
		$inputfields->add(self::getEmbedConfig($data));

		return $inputfields;
	}


	private static function getSettingsConfig(array $data)
	{
		$modules = wire('modules');

		$fs = $modules->get('InputfieldFieldset');
		$fs->set('themeOffset', true);
		$fs->attr('name', 'settings');
		$fs->icon = 'cog';
		$fs->label = __('Settings');
		$fs->description = __('Configure the main settings for Fathom Analytics.');

		$f = $modules->get('InputfieldText');
		$f->attr('name', 'siteId');
		$f->value = $data['siteId'];
		$f->icon = 'paper-plane';
		$f->label = __('Site ID');
		$f->description = __('The unique tracking ID for your site.');
		$fs->add($f);

		$f = $modules->get('InputfieldText');
		$f->attr('name' , 'sharePassword');
		$f->value = $data['sharePassword'];
		$f->icon = 'lock';
		$f->label = __('Fathom Share Password');
		$f->description = __('Required if you have shared your dashboard privately. Publicly shared dashboards do not need a password.');
		$f->notes = __('Note: Like Fathom, this is not hashed or encrypted.');
		$fs->add($f);

		$f = $modules->get('InputfieldText');
		$f->attr('name', 'customDomain');
		$f->value = $data['customDomain'];
		$f->icon = 'globe';
		$f->label = __('Custom Domain');
		$f->description = __('Optional. Only use this if you have set up a Custom Domain.');
		$f->detail = sprintf(__('[Read more about custom domains](%s).'), 'https://usefathom.com/support/custom-domains');
		$fs->add($f);

		return $fs;
	}


	private static function getOwnTrackingConfig(array $data)
	{
		$modules = wire('modules');

		$fs = $modules->get('InputfieldFieldset');
		$fs->set('themeOffset', true);
		$fs->attr('name', 'mytracking');
		$fs->icon = 'user-secret';
		$fs->collapsed = strlen($data['siteId']) ? Inputfield::collapsedYes : Inputfield::collapsedHidden;
		$fs->label = __('Block or unblock your own visits');
		$fs->description = __("There are instances where you want to block your own visits, so you loading pages on your site does not skew your stats. This is done by storing a value in something called localStorage (like a cookie) on your device. And yes, this is fully GDPR, CCPA and PECR compliant, as it's opt-in by you.");

		$btn1 = $modules->get('InputfieldButton');
		$btn1->id = 'blockTrackingForMe';
		$btn1->value = 'Block tracking for me';
		$fs->add($btn1);

		$btn2 = $modules->get('InputfieldButton');
		$btn2->setSecondary();
		$btn2->id = 'enableTrackingForMe';
		$btn2->value = 'Enable tracking for me';
		$fs->add($btn2);

		return $fs;
	}


	private function getEmbedConfig(array $data)
	{
		$modules = wire('modules');

		$hasToggle = ($modules->isInstalled('InputfieldToggle'));
		$toggleInputfield = ($hasToggle ? 'InputfieldToggle' : 'InputfieldRadios');

		$yesNoOptions = [
			1 => __('Yes'),
			0 => __('No'),
		];

		$fs = $modules->get('InputfieldFieldset');
		$fs->set('themeOffset', true);
		$fs->attr('name', 'tracking');
		$fs->icon = 'code';
		$fs->collapsed = Inputfield::collapsedYes;
		$fs->label = __('Tracking code');
		$fs->description = __('Configure different options for the tracking code.');

		$f = $modules->get($toggleInputfield);
		$f->attr('name', 'trackingAddScript');
		$f->value = $data['trackingAddScript'];
		$f->icon = 'code';
		$f->label = __('Automatic embed code');
		$f->checkboxLabel = __('Enable');
		$f->description = __('Automatically add the embed code via MarkupWayFathomAnalytics to every front-end page.');
		if ( ! $hasToggle) {
			$f->optionColumns = 1;
			$f->addOptions($yesNoOptions);
		}
		$fs->add($f);

		$f = $modules->get($toggleInputfield);
		$f->attr('name', 'trackingHonorDnt');
		$f->value = $data['trackingHonorDnt'];
		$f->icon = 'ban';
		$f->label = __('Honour Do Not Track (DNT)');
		$f->description = __("By default, Fathom tracks every visitor to your website, regardless of them having DNT turned on or not. That's because Fathom is privacy-focused analytics, so nothing personal or identifiable is ever 'tracked'.");
		$f->notes = __("Selecing 'Yes' adds the `honor-dnt=\"true\"` attribute to the script tag.");
		if ( ! $hasToggle) {
			$f->optionColumns = 1;
			$f->addOptions($yesNoOptions);
		}
		$fs->add($f);

		$f = $modules->get($toggleInputfield);
		$f->attr('name', 'trackingAutomatic');
		$f->value = $data['trackingAutomatic'];
		$f->icon = 'flash';
		$f->label = __('Automatic tracking');
		$f->description = __("By default, Fathom tracks a page view every time a visitor to your website loads a page with the script on it. If you don't want that functionality, click No.");
		$f->notes = __("Selecing 'No' adds the `auto=\"false\"` attribute to the script tag.");
		if ( ! $hasToggle) {
			$f->optionColumns = 1;
			$f->addOptions($yesNoOptions);
		}
		$fs->add($f);

		$f = $modules->get($toggleInputfield);
		$f->attr('name', 'trackingCanonical');
		$f->value = $data['trackingCanonical'];
		$f->icon = 'link';
		$f->label = __('Canonical URL');
		$f->description = __("If there's a canonical URL in place, then by default Fathom uses it instead of the current URL. This is what most customers want, and it's why it's the default. If you want to use the current URL, even if there's canonical (ignoring the canonical), then set this to 'No'.");
		$f->notes = __("Selecing 'No' adds the `canonical=\"false\"` attribute to the script tag.");
		if ( ! $hasToggle) {
			$f->optionColumns = 1;
			$f->addOptions($yesNoOptions);
		}
		$fs->add($f);

		$fsmarkup = $modules->get('InputfieldFieldset');
		$fsmarkup->attr('name', 'domains');
		$fsmarkup->icon = 'globe';
		$fsmarkup->label = __('Excluded or included domains');
		$fs->add($fsmarkup);

		$domains = $modules->get('InputfieldMarkup');
		$domains->textFormat = Inputfield::textFormatMarkdown;
		$domains->description = __("By default, you can use the tracking code on any domain, it doesn't matter if you use it on multiple sites or domains.")."\n\n";
		$domains->description .= __("You can, however, exclude one or several domains, so the tracker will track things on every domain, _except_ the ones excluded. This is useful to exclude stats being tracked on your local development environment (if you have one).")."\n\n";
		$domains->description .= __("You can also go in the opposite direction and only track stats on a specific domain. For example, if you have 2 development environments and a staging environment, you might want to track stats on your live site, example.com, but not on example.local and not on staging.example.com.");
		$fsmarkup->add($domains);

		$f = $modules->get('InputfieldTextarea');
		$f->attr('name', 'trackingExcludeDomains');
		$f->value = $data['trackingExcludeDomains'];
		$f->columnWidth = '50%';
		$f->icon = 'times';
		$f->label = __('Exclude domains');
		$f->description = __("Track all visits, except on the domains listed here. Enter one domain per line.");
		$f->notes = __("These domains will be added to the `excluded-domains` attribute on the script tag.");
		$fsmarkup->add($f);

		$f = $modules->get('InputfieldTextarea');
		$f->attr('name', 'trackingIncludeDomains');
		$f->value = $data['trackingIncludeDomains'];
		$f->columnWidth = '50%';
		$f->icon = 'check';
		$f->label = __('Include domains');
		$f->description = __("Only track visits on the domains listed here. Enter one domain per line.");
		$f->notes = __("These domains will be added to the `included-domains` attribute on the script tag.");
		$fsmarkup->add($f);


		$f = $modules->get('InputfieldRadios');
		$f->attr('name', 'trackingSpaMode');
		$f->value = $data['trackingSpaMode'];
		$f->icon = 'file-o';
		$f->label = __('Single page applications');
		$f->description = __("If there's not a Fathom plugin for your specific javascript system, you can use the generic SPA mode. Most applications use HTML5 History API, so the 'auto' mode will typically work as-is. This code checks if the History API is available, and if it's not, it falls back to listening to hash changes.");
		$f->notes = __("Selecing any value other than 'Off' will add the `spa={mode}` attribute to the script tag.");
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
