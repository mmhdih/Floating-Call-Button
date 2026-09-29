/* Floating Call Button – https://tavoosweb.ir/ */
(function () {
	'use strict';

	function init() {
		var root = document.getElementById('fcb');
		if (!root) {
			return;
		}
		var btn = root.querySelector('.fcb__toggle');
		var menu = root.querySelector('.fcb__menu');
		if (!btn || !menu) {
			return;
		}

		function setOpen(open) {
			root.classList.toggle('is-open', open);
			menu.hidden = !open;
			btn.setAttribute('aria-expanded', open ? 'true' : 'false');
		}

		btn.addEventListener('click', function (e) {
			e.stopPropagation();
			setOpen(!root.classList.contains('is-open'));
		});

		document.addEventListener('click', function (e) {
			if (!root.contains(e.target)) {
				setOpen(false);
			}
		});

		document.addEventListener('keydown', function (e) {
			if ((e.key === 'Escape' || e.key === 'Esc') && root.classList.contains('is-open')) {
				setOpen(false);
				btn.focus();
			}
		});

		menu.addEventListener('click', function (e) {
			if (e.target.closest && e.target.closest('a')) {
				setOpen(false);
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
