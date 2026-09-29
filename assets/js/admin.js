/* Floating Call Button – admin – https://tavoosweb.ir/ */
(function ($) {
	'use strict';

	var data = window.fcbAdmin || { icons: {}, types: {}, i18n: {} };
	var $form = $('.fcb-form');
	if (!$form.length) {
		return;
	}

	/* ---------------- Tabs ---------------- */
	function storage(key, value) {
		try {
			if (value === undefined) {
				return window.sessionStorage.getItem(key);
			}
			window.sessionStorage.setItem(key, value);
		} catch (e) {}
		return null;
	}

	function showTab(tab) {
		if (!$('.fcb-tab[data-tab="' + tab + '"]').length) {
			tab = 'channels';
		}
		$('.fcb-tabs .nav-tab').removeClass('nav-tab-active').filter('[data-tab="' + tab + '"]').addClass('nav-tab-active');
		$('.fcb-tab').removeClass('is-active').filter('[data-tab="' + tab + '"]').addClass('is-active');
		storage('fcbTab', tab);
	}

	$('.fcb-tabs').on('click', '.nav-tab', function (e) {
		e.preventDefault();
		var tab = $(this).data('tab');
		showTab(tab);
		if (window.history && window.history.replaceState) {
			window.history.replaceState(null, '', '#' + tab);
		}
	});
	showTab((window.location.hash || '').replace('#', '') || storage('fcbTab') || 'channels');

	/* ---------------- Helpers ---------------- */
	function initColors($scope) {
		$scope.find('.fcb-color').each(function () {
			var $input = $(this);
			if ($input.closest('.wp-picker-container').length) {
				return;
			}
			$input.wpColorPicker({
				change: function () {
					// مقدار جدید بعد از این رویداد در input قرار می‌گیرد.
					setTimeout(function () {
						$input.trigger('fcb:color');
					}, 0);
				},
				clear: function () {
					setTimeout(function () {
						$input.trigger('fcb:color');
					}, 0);
				}
			});
		});
	}

	function setColor($input, value) {
		if ($input.closest('.wp-picker-container').length) {
			$input.wpColorPicker('color', value);
		} else {
			$input.val(value);
		}
		$input.trigger('fcb:color');
	}

	function escapeHtml(str) {
		return String(str).replace(/[&<>"']/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
		});
	}

	/** HTML آیکون فعلی یک فیلد آیکون (برای پیش‌نمایش). */
	function iconHtml($field) {
		var source = $field.find('.fcb-icon-source:checked').val() || 'preset';
		if (source === 'svg') {
			var svg = $.trim($field.find('.fcb-icon-svg').val());
			// فقط برای پیش‌نمایش: حذف اسکریپت‌ها و رویدادها.
			if (svg.indexOf('<svg') !== -1) {
				var $svg = $('<div>').append($.parseHTML(svg, document, false)).find('svg').first();
				$svg.find('script,foreignObject').remove();
				$svg.find('*').addBack().each(function () {
					var el = this;
					$.each($.makeArray(el.attributes), function (_, attr) {
						if (/^on/i.test(attr.name) || /javascript:/i.test(attr.value)) {
							el.removeAttribute(attr.name);
						}
					});
				});
				if ($svg.length) {
					return $svg.prop('outerHTML');
				}
			}
		} else if (source === 'image') {
			var src = $.trim($field.find('.fcb-icon-img').val());
			if (src) {
				return '<img src="' + escapeHtml(src) + '" alt="">';
			}
		}
		var key = $field.find('.fcb-icon-preset:checked').val() || 'phone';
		return data.icons[key] || data.icons.phone || '';
	}

	/* ---------------- Icon fields ---------------- */
	$form.on('change', '.fcb-icon-source', function () {
		$(this).closest('.fcb-icon-field').attr('data-source', this.value).trigger('fcb:icon');
	});
	$form.on('change input', '.fcb-icon-preset, .fcb-icon-svg, .fcb-icon-img', function () {
		$(this).closest('.fcb-icon-field').trigger('fcb:icon');
	});

	var mediaFrame;
	$form.on('click', '.fcb-upload', function (e) {
		e.preventDefault();
		var $input = $(this).siblings('.fcb-icon-img');
		if (!window.wp || !wp.media) {
			return;
		}
		mediaFrame = wp.media({
			title: data.i18n.mediaTitle,
			button: { text: data.i18n.mediaButton },
			library: { type: 'image' },
			multiple: false
		});
		mediaFrame.on('select', function () {
			var file = mediaFrame.state().get('selection').first().toJSON();
			$input.val(file.url).trigger('change');
		});
		mediaFrame.open();
	});

	/* ---------------- Channels ---------------- */
	var $list = $('#fcb-channels');
	var counter = Date.now();

	function refreshEmpty() {
		$('.fcb-empty').prop('hidden', $list.children('.fcb-ch').length > 0);
	}

	function updateHead($row) {
		var type = $row.find('.fcb-ch__type-select').val();
		var t = data.types[type] || {};
		var $field = $row.find('.fcb-icon-field');
		$row.find('.fcb-ch__title').text($.trim($row.find('.fcb-ch__title-input').val()) || data.i18n.untitled);
		$row.find('.fcb-ch__type').text(t.label || type);
		$row.find('.fcb-ch__value').text($.trim($row.find('.fcb-ch__value-input').val()) || data.i18n.noValue);
		$row.find('.fcb-ch__preview')
			.html(iconHtml($field))
			.css({
				'--ic-bg': $row.find('input[name$="[icon_bg]"]').val(),
				'--ic-color': $row.find('input[name$="[icon_color]"]').val()
			});
		$row.toggleClass('is-disabled', !$row.find('.fcb-ch__enabled input').is(':checked'));
		renderPreview();
	}

	/** اعمال پیش‌فرض‌های نوع کانال (آیکون، رنگ، راهنما و ...). */
	function applyType($row, type, previousType) {
		var t = data.types[type];
		if (!t) {
			return;
		}
		var prev = data.types[previousType] || {};
		var $title = $row.find('.fcb-ch__title-input');
		var $value = $row.find('.fcb-ch__value-input');

		if (!$.trim($title.val()) || $title.val() === prev.label) {
			$title.val(t.label);
		}
		$value.attr('placeholder', t.placeholder || '');
		$row.find('.fcb-ch__hint').text(t.hint || '');
		$row.find('.fcb-ch__msg').prop('hidden', !t.message_label);
		$row.find('.fcb-ch__msg-label').text(t.message_label || '');
		$row.find('.fcb-ch__newtab input').prop('checked', !!parseInt(t.new_tab, 10));

		var $field = $row.find('.fcb-icon-field');
		if (($field.find('.fcb-icon-source:checked').val() || 'preset') === 'preset') {
			$field.find('.fcb-icon-preset[value="' + t.icon + '"]').prop('checked', true);
		}
		var $bg = $row.find('input[name$="[icon_bg]"]');
		var $color = $row.find('input[name$="[icon_color]"]');
		$bg.attr('data-default-color', t.bg);
		$color.attr('data-default-color', t.color);
		setColor($bg, t.bg);
		setColor($color, t.color);
		updateHead($row);
	}

	function toggleRow($row, open) {
		open = open === undefined ? !$row.hasClass('is-open') : open;
		$row.toggleClass('is-open', open);
		$row.find('.fcb-ch__summary').attr('aria-expanded', open ? 'true' : 'false');
	}

	$('#fcb-add-channel').on('click', function () {
		var type = $('#fcb-add-type').val();
		var html = $('#tmpl-fcb-channel').html().replace(/__INDEX__/g, 'n' + counter++);
		var $row = $($.parseHTML($.trim(html), document, false));
		$list.append($row);
		initColors($row);
		$row.find('.fcb-ch__type-select').val(type).data('prev', type);
		applyType($row, type, 'custom');
		refreshEmpty();
		$row.find('.fcb-ch__value-input').trigger('focus');
	});

	$list.on('focus', '.fcb-ch__type-select', function () {
		$(this).data('prev', this.value);
	});
	$list.on('change', '.fcb-ch__type-select', function () {
		var $row = $(this).closest('.fcb-ch');
		applyType($row, this.value, $(this).data('prev'));
		$(this).data('prev', this.value);
	});

	$list.on('click', '.fcb-ch__summary, .fcb-ch__toggle', function () {
		toggleRow($(this).closest('.fcb-ch'));
	});

	$list.on('click', '.fcb-ch__remove', function () {
		if (window.confirm(data.i18n.confirmRemove)) {
			$(this).closest('.fcb-ch').remove();
			refreshEmpty();
			renderPreview();
		}
	});

	$list.on('input change fcb:color fcb:icon', '.fcb-ch__title-input, .fcb-ch__value-input, .fcb-ch__enabled input, input[name*="[subtitle]"], .fcb-color, .fcb-icon-field', function () {
		updateHead($(this).closest('.fcb-ch'));
	});

	if ($.fn.sortable) {
		$list.sortable({
			handle: '.fcb-ch__handle',
			axis: 'y',
			placeholder: 'fcb-ch fcb-ch--placeholder',
			forcePlaceholderSize: true,
			update: renderPreview
		});
	}

	/* ---------------- Live preview (appearance tab) ---------------- */
	function val(id) {
		return $('#' + id).val();
	}

	function renderPreview() {
		var $btn = $('#fcb-preview-btn');
		if (!$btn.length) {
			return;
		}
		var bg = val('fcb-bg-color') || '#F5B301';
		var bg2 = val('fcb-bg-color2') || bg;
		$('#fcb-preview').css({
			'--p-bg': bg,
			'--p-bg2': bg2,
			'--p-color': val('fcb-icon-color') || '#2B2118',
			'--p-card-bg': val('fcb-card-bg') || '#fff',
			'--p-card-txt': val('fcb-card-text') || '#2B2118',
			'--p-card-sub': val('fcb-card-subtext') || '#8a7d70',
			'--p-card-bd': val('fcb-card-border') || '#f5ead0'
		}).toggleClass('is-pulse', $('input[name="fcb_settings[pulse]"]').is(':checked'));

		$btn.html(iconHtml($('.fcb-tab[data-tab="appearance"] .fcb-icon-field').first()));
		previewPosition();

		var items = '';
		$list.children('.fcb-ch').each(function () {
			var $row = $(this);
			if (!$row.find('.fcb-ch__enabled input').is(':checked')) {
				return;
			}
			var sub = $.trim($row.find('input[name*="[subtitle]"]').val());
			items += '<div class="fcb-preview__opt"><span class="fcb-preview__ic" style="--ic-bg:' + escapeHtml($row.find('input[name$="[icon_bg]"]').val()) + ';--ic-color:' + escapeHtml($row.find('input[name$="[icon_color]"]').val()) + '">' +
				iconHtml($row.find('.fcb-icon-field')) + '</span><span><b>' + escapeHtml($.trim($row.find('.fcb-ch__title-input').val()) || data.i18n.untitled) + '</b>' +
				(sub ? '<small>' + escapeHtml(sub) + '</small>' : '') + '</span></div>';
		});
		$('#fcb-preview-menu').html(items);
	}

	/** هماهنگ با FCB_Frontend::menu_layout(). */
	function previewPosition() {
		var pos = $('input[name="fcb_settings[position]"]:checked').val() || 'bottom-right';
		var $preview = $('#fcb-preview');
		var $anchor = $('#fcb-preview-anchor');
		var open, align;

		if (pos === 'custom') {
			var x = clamp($('#fcb-custom_x').val());
			var y = clamp($('#fcb-custom_y').val());
			if (y > 35 && y < 65) {
				open = x > 50 ? 'left' : 'right';
				align = 'center';
			} else {
				open = y <= 35 ? 'down' : 'up';
				align = x > 66 ? 'right' : (x < 34 ? 'left' : 'center');
			}
			$anchor.css({ left: x + '%', top: y + '%', transform: 'translate(-' + x + '%, -' + y + '%)' });
		} else {
			var parts = pos.split('-');
			$anchor.css({ left: '', top: '', transform: '' });
			if (parts[0] === 'middle') {
				open = parts[1] === 'right' ? 'left' : 'right';
				align = 'center';
			} else {
				open = parts[0] === 'top' ? 'down' : 'up';
				align = parts[1];
			}
		}
		$preview.attr({ 'data-pos': pos, 'data-open': open, 'data-align': align });
	}

	$('.fcb-tab[data-tab="appearance"]').on('input change fcb:color fcb:icon', '.fcb-color, .fcb-icon-field, input[type="checkbox"]', renderPreview);

	/* ---------------- Position ---------------- */
	function currentPosition() {
		return $('input[name="fcb_settings[position]"]:checked').val();
	}

	function refreshPosition() {
		var pos = currentPosition();
		$('[data-show-when]').prop('hidden', pos !== 'custom');
		$('[data-hide-when]').prop('hidden', pos === 'custom');
		moveDot();
		previewPosition();
	}

	function clamp(n) {
		n = parseFloat(n);
		return isNaN(n) ? 0 : Math.max(0, Math.min(100, n));
	}

	function moveDot() {
		var x = clamp($('#fcb-custom_x').val());
		var y = clamp($('#fcb-custom_y').val());
		$('#fcb-custom-dot').css({ left: x + '%', top: y + '%', transform: 'translate(-' + x + '%, -' + y + '%)' });
		previewPosition();
	}

	$form.on('change', 'input[name="fcb_settings[position]"]', refreshPosition);
	$('#fcb-custom_x, #fcb-custom_y').on('input change', moveDot);

	var dragging = false;
	function setFromPointer(e) {
		var $screen = $('#fcb-custom-screen');
		var rect = $screen[0].getBoundingClientRect();
		var point = e.originalEvent && e.originalEvent.touches ? e.originalEvent.touches[0] : e;
		var x = Math.round(clamp(((point.clientX - rect.left) / rect.width) * 100) * 2) / 2;
		var y = Math.round(clamp(((point.clientY - rect.top) / rect.height) * 100) * 2) / 2;
		$('#fcb-custom_x').val(x);
		$('#fcb-custom_y').val(y);
		moveDot();
	}
	$('#fcb-custom-screen').on('mousedown touchstart', function (e) {
		dragging = true;
		setFromPointer(e);
		e.preventDefault();
	});
	$(document).on('mousemove touchmove', function (e) {
		if (dragging) {
			setFromPointer(e);
		}
	}).on('mouseup touchend', function () {
		dragging = false;
	});

	/* ---------------- Display targets ---------------- */
	function refreshMode() {
		var mode = $('input[name="fcb_settings[display_mode]"]:checked').val();
		$('[data-hide-mode]').prop('hidden', mode === 'all');
	}
	$form.on('change', 'input[name="fcb_settings[display_mode]"]', refreshMode);

	$('.fcb-page-search').on('input', function () {
		var q = $.trim(this.value).toLowerCase();
		$('.fcb-pages label').each(function () {
			$(this).toggle(!q || $(this).text().toLowerCase().indexOf(q) !== -1);
		});
	});

	/* ---------------- Init ---------------- */
	initColors($form);
	$list.children('.fcb-ch').each(function () {
		$(this).find('.fcb-ch__type-select').data('prev', $(this).find('.fcb-ch__type-select').val());
	});
	refreshEmpty();
	refreshPosition();
	refreshMode();
	renderPreview();
})(jQuery);
