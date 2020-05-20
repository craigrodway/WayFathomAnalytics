(function() {

	var added = false;

	var fathomCtl = function(fnName) {

		if (typeof(fathom) !== 'undefined') {
			window.fathom[fnName]();
			return;
		}

		if (added) {
			return;
		}

		var head = document.getElementsByTagName('head')[0];
		var el = document.createElement('script');
		head.append(el);
		added = true;

		el.onload = function() { fathomCtl(fnName); };
		el.setAttribute('auto', 'false');
		el.setAttribute('site', ProcessWire.config.WayFathomAnalytics.siteId);
		el.src = ProcessWire.config.WayFathomAnalytics.scriptUrl;

		return;
	}

	$(document).ready(function() {

		$('#blockTrackingForMe').on('click', function(e) {
			fathomCtl('blockTrackingForMe');
		});

		$('#enableTrackingForMe').on('click', function(e) {
			fathomCtl('enableTrackingForMe');
		});

	});

})();
