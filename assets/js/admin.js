/* Floating Call Button – admin – https://tavoosweb.ir/ */
(function ($) {
	'use strict';

	var data = window.tavoosFcbAdmin || { option: 'tavoos_fcb_options', icons: {}, types: {}, i18n: {} };
	var opt = data.option || 'tavoos_fcb_options';

	function field(key) {
		return 'input[name="' + opt + '[' + key + ']"]';
	}
	var $form = $('.tavoos-fcb-form');
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
		if (!$('.tavoos-fcb-tab[data-tab="' + tab + '"]').length) {
			tab = 'channels';
		}
		$('.tavoos-fcb-tabs .nav-tab').removeClass('nav-tab-active').filter('[data-tab="' + tab + '"]').addClass('nav-tab-active');
		$('.tavoos-fcb-tab').removeClass('is-active').filter('[data-tab="' + tab + '"]').addClass('is-active');
		storage('tavoosFcbTab', tab);
	}

	$('.tavoos-fcb-tabs').on('click', '.nav-tab', function (e) {
		e.preventDefault();
		var tab = $(this).data('tab');
		showTab(tab);
		if (window.history && window.history.replaceState) {
			window.history.replaceState(null, '', '#' + tab);
		}
	});
	showTab((window.location.hash || '').replace('#', '') || storage('tavoosFcbTab') || 'channels');

	/* ---------------- Helpers ---------------- */
	function initColors($scope) {
		$scope.find('.tavoos-fcb-color').each(function () {
			var $input = $(this);
			if ($input.closest('.wp-picker-container').length) {
				return;
			}
			$input.wpColorPicker({
				change: function () {
					// مقدار جدید بعد از این رویداد در input قرار می‌گیرد.
					setTimeout(function () {
						$input.trigger('tavoos-fcb:color');
					}, 0);
				},
				clear: function () {
					setTimeout(function () {
						$input.trigger('tavoos-fcb:color');
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
		$input.trigger('tavoos-fcb:color');
	}

	function escapeHtml(str) {
		return String(str).replace(/[&<>"']/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
		});
	}

	/** HTML آیکون فعلی یک فیلد آیکون (برای پیش‌نمایش). */
	function iconHtml($field) {
		var source = $field.find('.tavoos-fcb-icon-source:checked').val() || 'preset';
		if (source === 'svg') {
			var svg = $.trim($field.find('.tavoos-fcb-icon-svg').val());
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
			var src = $.trim($field.find('.tavoos-fcb-icon-img').val());
			if (src) {
				return '<img src="' + escapeHtml(src) + '" alt="">';
			}
		}
		var key = $field.find('.tavoos-fcb-icon-preset:checked').val() || 'phone';
		return data.icons[key] || data.icons.phone || '';
	}

	/* ---------------- Icon fields ---------------- */
	$form.on('change', '.tavoos-fcb-icon-source', function () {
		$(this).closest('.tavoos-fcb-icon-field').attr('data-source', this.value).trigger('tavoos-fcb:icon');
	});
	$form.on('change input', '.tavoos-fcb-icon-preset, .tavoos-fcb-icon-svg, .tavoos-fcb-icon-img', function () {
		$(this).closest('.tavoos-fcb-icon-field').trigger('tavoos-fcb:icon');
	});

	var mediaFrame;
	$form.on('click', '.tavoos-fcb-upload', function (e) {
		e.preventDefault();
		var $input = $(this).siblings('.tavoos-fcb-icon-img');
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
	var $list = $('#tavoos-fcb-channels');
	var counter = Date.now();

	function refreshEmpty() {
		$('.tavoos-fcb-empty').prop('hidden', $list.children('.tavoos-fcb-ch').length > 0);
	}

	function updateHead($row) {
		var type = $row.find('.tavoos-fcb-ch__type-select').val();
		var t = data.types[type] || {};
		var $field = $row.find('.tavoos-fcb-icon-field');
		$row.find('.tavoos-fcb-ch__title').text($.trim($row.find('.tavoos-fcb-ch__title-input').val()) || data.i18n.untitled);
		$row.find('.tavoos-fcb-ch__type').text(t.label || type);
		$row.find('.tavoos-fcb-ch__value').text($.trim($row.find('.tavoos-fcb-ch__value-input').val()) || data.i18n.noValue);
		$row.find('.tavoos-fcb-ch__preview')
			.html(iconHtml($field))
			.css({
				'--ic-bg': $row.find('input[name$="[icon_bg]"]').val(),
				'--ic-color': $row.find('input[name$="[icon_color]"]').val()
			});
		$row.toggleClass('is-disabled', !$row.find('.tavoos-fcb-ch__enabled input').is(':checked'));
		renderPreview();
	}

	/** اعمال پیش‌فرض‌های نوع کانال (آیکون، رنگ، راهنما و ...). */
	function applyType($row, type, previousType) {
		var t = data.types[type];
		if (!t) {
			return;
		}
		var prev = data.types[previousType] || {};
		var $title = $row.find('.tavoos-fcb-ch__title-input');
		var $value = $row.find('.tavoos-fcb-ch__value-input');

		if (!$.trim($title.val()) || $title.val() === prev.label) {
			$title.val(t.label);
		}
		$value.attr('placeholder', t.placeholder || '');
		$row.find('.tavoos-fcb-ch__hint').text(t.hint || '');
		$row.find('.tavoos-fcb-ch__msg').prop('hidden', !t.message_label);
		$row.find('.tavoos-fcb-ch__msg-label').text(t.message_label || '');
		$row.find('.tavoos-fcb-ch__newtab input').prop('checked', !!parseInt(t.new_tab, 10));

		var $field = $row.find('.tavoos-fcb-icon-field');
		if (($field.find('.tavoos-fcb-icon-source:checked').val() || 'preset') === 'preset') {
			$field.find('.tavoos-fcb-icon-preset[value="' + t.icon + '"]').prop('checked', true);
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
		$row.find('.tavoos-fcb-ch__summary').attr('aria-expanded', open ? 'true' : 'false');
	}

	$('#tavoos-fcb-add-channel').on('click', function () {
		var type = $('#tavoos-fcb-add-type').val();
		var html = $('#tmpl-tavoos-fcb-channel').html().replace(/__INDEX__/g, 'n' + counter++);
		var $row = $($.parseHTML($.trim(html), document, false));
		$list.append($row);
		initColors($row);
		$row.find('.tavoos-fcb-ch__type-select').val(type).data('prev', type);
		applyType($row, type, 'custom');
		refreshEmpty();
		$row.find('.tavoos-fcb-ch__value-input').trigger('focus');
	});

	$list.on('focus', '.tavoos-fcb-ch__type-select', function () {
		$(this).data('prev', this.value);
	});
	$list.on('change', '.tavoos-fcb-ch__type-select', function () {
		var $row = $(this).closest('.tavoos-fcb-ch');
		applyType($row, this.value, $(this).data('prev'));
		$(this).data('prev', this.value);
	});

	$list.on('click', '.tavoos-fcb-ch__summary, .tavoos-fcb-ch__toggle', function () {
		toggleRow($(this).closest('.tavoos-fcb-ch'));
	});

	$list.on('click', '.tavoos-fcb-ch__remove', function () {
		if (window.confirm(data.i18n.confirmRemove)) {
			$(this).closest('.tavoos-fcb-ch').remove();
			refreshEmpty();
			renderPreview();
		}
	});

	$list.on('input change tavoos-fcb:color tavoos-fcb:icon', '.tavoos-fcb-ch__title-input, .tavoos-fcb-ch__value-input, .tavoos-fcb-ch__enabled input, input[name*="[subtitle]"], .tavoos-fcb-color, .tavoos-fcb-icon-field', function () {
		updateHead($(this).closest('.tavoos-fcb-ch'));
	});

	if ($.fn.sortable) {
		$list.sortable({
			handle: '.tavoos-fcb-ch__handle',
			axis: 'y',
			placeholder: 'tavoos-fcb-ch tavoos-fcb-ch--placeholder',
			forcePlaceholderSize: true,
			update: renderPreview
		});
	}

	/* ---------------- Live preview (appearance tab) ---------------- */
	function val(id) {
		return $('#' + id).val();
	}

	function renderPreview() {
		var $btn = $('#tavoos-fcb-preview-btn');
		if (!$btn.length) {
			return;
		}
		var bg = val('tavoos-fcb-bg-color') || '#F5B301';
		var bg2 = val('tavoos-fcb-bg-color2') || bg;
		$('#tavoos-fcb-preview').css({
			'--p-bg': bg,
			'--p-bg2': bg2,
			'--p-color': val('tavoos-fcb-icon-color') || '#2B2118',
			'--p-card-bg': val('tavoos-fcb-card-bg') || '#fff',
			'--p-card-txt': val('tavoos-fcb-card-text') || '#2B2118',
			'--p-card-sub': val('tavoos-fcb-card-subtext') || '#8a7d70',
			'--p-card-bd': val('tavoos-fcb-card-border') || '#f5ead0'
		}).toggleClass('is-pulse', $(field('pulse')).is(':checked'));

		$btn.html(iconHtml($('.tavoos-fcb-tab[data-tab="appearance"] .tavoos-fcb-icon-field').first()));
		previewPosition();

		var items = '';
		$list.children('.tavoos-fcb-ch').each(function () {
			var $row = $(this);
			if (!$row.find('.tavoos-fcb-ch__enabled input').is(':checked')) {
				return;
			}
			var sub = $.trim($row.find('input[name*="[subtitle]"]').val());
			items += '<div class="tavoos-fcb-preview__opt"><span class="tavoos-fcb-preview__ic" style="--ic-bg:' + escapeHtml($row.find('input[name$="[icon_bg]"]').val()) + ';--ic-color:' + escapeHtml($row.find('input[name$="[icon_color]"]').val()) + '">' +
				iconHtml($row.find('.tavoos-fcb-icon-field')) + '</span><span><b>' + escapeHtml($.trim($row.find('.tavoos-fcb-ch__title-input').val()) || data.i18n.untitled) + '</b>' +
				(sub ? '<small>' + escapeHtml(sub) + '</small>' : '') + '</span></div>';
		});
		$('#tavoos-fcb-preview-menu').html(items);
	}

	/** هماهنگ با FCB_Frontend::menu_layout(). */
	function previewPosition() {
		var pos = $(field('position') + ':checked').val() || 'bottom-right';
		var $preview = $('#tavoos-fcb-preview');
		var $anchor = $('#tavoos-fcb-preview-anchor');
		var open, align;

		if (pos === 'custom') {
			var x = clamp($('#tavoos-fcb-custom_x').val());
			var y = clamp($('#tavoos-fcb-custom_y').val());
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

	$('.tavoos-fcb-tab[data-tab="appearance"]').on('input change tavoos-fcb:color tavoos-fcb:icon', '.tavoos-fcb-color, .tavoos-fcb-icon-field, input[type="checkbox"]', renderPreview);

	/* ---------------- Position ---------------- */
	function currentPosition() {
		return $(field('position') + ':checked').val();
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
		var x = clamp($('#tavoos-fcb-custom_x').val());
		var y = clamp($('#tavoos-fcb-custom_y').val());
		$('#tavoos-fcb-custom-dot').css({ left: x + '%', top: y + '%', transform: 'translate(-' + x + '%, -' + y + '%)' });
		previewPosition();
	}

	$form.on('change', field('position'), refreshPosition);
	$('#tavoos-fcb-custom_x, #tavoos-fcb-custom_y').on('input change', moveDot);

	var dragging = false;
	function setFromPointer(e) {
		var $screen = $('#tavoos-fcb-custom-screen');
		var rect = $screen[0].getBoundingClientRect();
		var point = e.originalEvent && e.originalEvent.touches ? e.originalEvent.touches[0] : e;
		var x = Math.round(clamp(((point.clientX - rect.left) / rect.width) * 100) * 2) / 2;
		var y = Math.round(clamp(((point.clientY - rect.top) / rect.height) * 100) * 2) / 2;
		$('#tavoos-fcb-custom_x').val(x);
		$('#tavoos-fcb-custom_y').val(y);
		moveDot();
	}
	$('#tavoos-fcb-custom-screen').on('mousedown touchstart', function (e) {
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
		var mode = $(field('display_mode') + ':checked').val();
		$('[data-hide-mode]').prop('hidden', mode === 'all');
	}
	$form.on('change', field('display_mode'), refreshMode);

	$('.tavoos-fcb-page-search').on('input', function () {
		var q = $.trim(this.value).toLowerCase();
		$('.tavoos-fcb-pages label').each(function () {
			$(this).toggle(!q || $(this).text().toLowerCase().indexOf(q) !== -1);
		});
	});

	/* ---------------- Init ---------------- */
	initColors($form);
	$list.children('.tavoos-fcb-ch').each(function () {
		$(this).find('.tavoos-fcb-ch__type-select').data('prev', $(this).find('.tavoos-fcb-ch__type-select').val());
	});
	refreshEmpty();
	refreshPosition();
	refreshMode();
	renderPreview();
})(jQuery);
