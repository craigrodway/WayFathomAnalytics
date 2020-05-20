<?php
namespace ProcessWire;

require_once(__DIR__ . '/WayFathom.php');

/**
 * MarkupWayFathomAnalytics
 *
 * Markup module for WayFathomAnalytics to generate the tracking code.
 *
 * @author Craig A Rodway
 *
 * ProcessWire 3.x, Copyright 2016 by Ryan Cramer
 * https://processwire.com
 *
 */

class MarkupWayFathomAnalytics extends WireData implements Module
{


	public static function getModuleInfo()
	{
		return [
			'title' => "MarkupWayFathomAnalytics",
			'version' => "0.0.1",
			'summary' => 'Markup module for WayFathomAnalytics to generate the tracking code.',

			'author' => "Craig A Rodway",
			'href' => 'https://github.com/craigrodway/WayFathomAnalytics',

			'autoload' => true,
			'singular' => true,
			'permanent' => false,
			'icon' => 'bar-chart',

			'requires' => [
				'PHP>=7.0.0',
				'ProcessWire>=3.0.0',
				'WayFathomAnalytics',
			],
		];
	}


	public function ready()
	{
		$wayFathom = $this->modules->get('WayFathomAnalytics');

		$siteId = $wayFathom->siteId;
		$trackingAddScript = $wayFathom->trackingAddScript;

		// Register Page::render() hook which will add the <script> tag
		if (strlen($siteId) && $trackingAddScript == '1' && $this->page->template->name != 'admin') {
			$this->addHookAfter('Page::render', $this, 'addEmbed');
		}
	}


	/**
	 * Hook to automatically add the embed code at the end of the <body> element.
	 *
	 * @param HookEevent $event The Page::render() event.
	 *
	 */
	public function addEmbed(HookEvent $event)
	{
		$html = $event->return;
		$script = $this->render();
		$event->return = str_replace('</head>', "{$script}\n</head>", $html);
	}


	/**
	 * Get the script tag that loads Fathom.
	 *
	 * @param array $options Override module settings with custom values.
	 * @return string Script tag to load Fathom Analytics.
	 *
	 */
	public function render(array $options = [])
	{
		$attributes = $this->getScriptAttributes($options);
		$tag = sprintf('<script %s></script>', $attributes);

		if (isset($options['hideComments']) && $options['hideComments']) {
			return $tag;
		}

		$out = "\n<!-- Fathom - beautiful, simple website analytics -->\n";
		$out .= "{$tag}\n";
		$out .= "<!-- / Fathom -->\n";

		return $out;
	}


	/**
	 * Get the URL to the Fathom tracking script.
	 *
	 * @param array $options Override module settings with custom values.
	 *
	 */
	public function getScriptUrl($options = [])
	{
		$config = $this->getConfig($options);

		$customDomain = $this->sanitizer->text_entities($config->customDomain);

		if (strlen($customDomain)) {
			$host = str_replace(['http://', 'https://', '/'], '', $customDomain);
		} else {
			$host = 'cdn.usefathom.com';
		}

		$url = sprintf("https://%s/script.js", $host);

		return $url;
	}


	/**
	 * Get the attributes for the script tag.
	 *
	 * @param array $options Override module settings with custom values.
	 *
	 */
	public function getScriptAttributes(array $options = [])
	{
		$config = $this->getConfig($options);

		$attrs = [
			'src' => $this->getScriptUrl($options),
		];

		if (strlen($config->siteId)) {
			$attrs['site'] = $config->siteId;
		}

		if ($config->trackingHonorDnt == 1) {
			$attrs['honor-dnt'] = 'true';
		}

		if ($config->trackingAutomatic == 0) {
			$attrs['auto'] = 'false';
		}

		if ($config->trackingCanonical == 0) {
			$attrs['canonical'] = 'false';
		}

		if (strlen($config->trackingExcludeDomains)) {
			$attrs['excluded-domains'] = $config->trackingExcludeDomains;
		}

		if (strlen($config->trackingIncludeDomains)) {
			$attrs['included-domains'] = $config->trackingIncludeDomains;
		}

		if (strlen($config->trackingSpaMode)) {
			$attrs['spa'] = $config->trackingSpaMode;
		}

		$attrs['defer'] = '';

		$attrList = [];

		foreach ($attrs as $attr => $value) {

			switch ($attr) {

				case 'included-domains':
				case 'excluded-domains':
					$value = str_replace(',', "\n", $value);
					$items = explode("\n", $value);
					$items = array_filter($items, 'trim');
					$value = implode(',', $items);
					$value = $this->sanitizer->textarea_entities($value);
				break;

				default:
					$value = $this->sanitizer->text_entities($value);
			}

			if (strlen($value)) {
				$attrList[] = "{$attr}=\"{$value}\"";
			} else {
				$attrList[] = "{$attr}";
			}

		}

		$attributes = implode(' ', $attrList);

		return $attributes;
	}


	/**
	 * Get a WireArray of configuration values, with optional overrides supplied via $options.
	 *
	 * @param array $options Override the defaults with custom values.
	 * @return WireArray All configuration values.
	 *
	 */
	private function getConfig(array $options = [])
	{
		$wayFathom = $this->modules->get('WayFathomAnalytics');
		$defaults = WayFathomAnalytics::getDefaults();

		$config = [];

		foreach ($defaults as $k => $v) {
			// Get initial value, as configured, from the WayFathomAnalytics module
			$config[$k] = $wayFathom->{$k};
		}

		$data = new WireArray();
		$data->setArray(array_merge($config, $options));
		return $data;
	}


}
