<?php
namespace ProcessWire;

require_once(__DIR__ . '/WayFathom.php');

/**
 * ProcessWayFathomAnalytics
 *
 * View your Fathom Analytics dashboard in the ProcessWire admin panel.
 *
 * @author Craig A Rodway
 *
 * ProcessWire 3.x, Copyright 2016 by Ryan Cramer
 * https://processwire.com
 *
 */

class ProcessWayFathomAnalytics extends Process implements Module
{


	public static function getModuleInfo()
	{
		return [
			'title' => 'ProcessWayFathomAnalytics',
			'version' => '0.0.1',
			'summary' => __('View your Fathom Analytics dashboard in the ProcessWire admin panel.'),

			'author' => 'Craig A Rodway',
			'href' => 'https://github.com/craigrodway/WayFathomAnalytics',

			'permission' => WayFathom::PERMISSION_NAME,
			'permissions' => [WayFathom::PERMISSION_NAME => __('Fathom Analytics')],
			'autoload' => false,
			'singular' => false,
			'permanent' => false,
			'icon' => 'bar-chart',

			'requires' => [
				'PHP>=7.0.0',
				'ProcessWire>=3.0.0',
				'WayFathomAnalytics',
			],

			'page' => [
				'name' => WayFathom::PAGE_NAME,
				'title' => __('Fathom Analytics'),
			],
		];
	}


	public function init()
	{
		parent::init();
		$this->config->scripts->add($this->config->urls->ProcessWayFathomAnalytics . 'iframeResizer.min.js');
		$this->config->scripts->add($this->config->urls->ProcessWayFathomAnalytics . 'fathomIframe.js');
	}


	public function ___execute()
	{
		$wayFathom = $this->modules->get('WayFathomAnalytics');
		$siteId = $wayFathom->siteId;

		if ( ! strlen($siteId)) {
			$msg = $this->_('Please add your Fathom Analytics Site ID to the WayFathomAnalytics module settings page.');
			$configureUrl = "{$this->config->urls->admin}module/edit?name=WayFathomAnalytics&collapse_info=1";
			$msg = "<a href='{$configureUrl}'>{$msg}</a>";
			$this->warning($msg, Notice::allowMarkup);
			return '&nbsp;';
		}

		return $this->getDashboardCode();
	}


	/**
	 * Get the iframe embed code for the Fathom share page.
	 *
	 */
	public function getDashboardCode()
	{
		$src = $this->getIframeUrl();
		$style = 'width:1px;min-width:100%;height:1000px;max-width:1100px';
		$iframe = "<iframe id='fathom-stats-iframe' src='{$src}' style='{$style}' frameborder='0' onload='fathomResizeIframe()'></iframe>";
		$out = "<div class='wrapper'>{$iframe}</div>";
		return $out;
	}


	/**
	 * Get the URL to the Fathom share page for the configured Site ID.
	 *
	 */
	public function getIframeUrl()
	{
		$wayFathom = $this->modules->get('WayFathomAnalytics');
		$siteId = $this->sanitizer->entities($wayFathom->siteId);
		$password = hash('sha256', $wayFathom->sharePassword);
		$url = sprintf('https://app.usefathom.com/share/%s/processwire?password=%s', $siteId, $password);
		return $url;
	}


}
