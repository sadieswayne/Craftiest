$(document).ready(function () {
	'use strict';

	var addonFilter = 'All';
	var strFilter = '';
	var isInstalling = false;

	$('[data-toogle="cart"]').on('click', function () {
		var url = $(this).data('url');

		$.get(url, function (data) {


			var icon = $('#' + data.iconId);

			if (icon.hasClass('text-gray-500')) {
				icon.removeClass('text-gray-500');
				icon.addClass('text-green-500');
			} else {
				icon.removeClass('text-green-500');
				icon.addClass('text-gray-500');
			}

			$('#itemCount').html(data.itemCount);
			toastr.success(data.message);
		});
	});

	function updateList() {
		$('.lqd-extension').each((index, element) => {
			if (addonFilter == 'All') {
				$(element).removeClass('hidden');
			} else if (addonFilter == 'Installed') {
				if ($(element).attr('data-installed') == '1') {
					$(element).removeClass('hidden');
				} else {
					$(element).addClass('hidden');
				}
			} else if (addonFilter == 'Free') {
				if ($(element).attr('data-price') == '0') {
					$(element).removeClass('hidden');
				} else {
					$(element).addClass('hidden');
				}
			} else if (addonFilter == 'Paid') {
				if ($(element).attr('data-price') != '0') {
					$(element).removeClass('hidden');
				} else {
					$(element).addClass('hidden');
				}
			}

			if (!$(element).attr('data-name').toLowerCase().includes(strFilter)) {
				$(element).addClass('hidden');
			}
		});
	}

	$('.addons_filter').on('click', function () {
		$('.addons_filter').removeClass('active');
		$(this).addClass('active');
		var filter = $(this).attr('data-filter');
		addonFilter = filter;
		updateList();
	});

	$('#search_str').on('keydown', function (e) {
		if (e.key == 'Enter') {
			strFilter = $('#search_str').val();
			updateList();
		}
	});

	$('#btn_confirm_method').on('click', function () {

		const btn = $(this);
		let formData = new FormData();

		btn.prop('disabled', true);
		btn.addClass('lqd-is-busy');
		Alpine.store('appLoadingIndicator').show();

		$.ajax({
			type: 'post',
			url: '/dashboard/admin/marketplace/buy/' + btn.attr('data-name'),
			data: formData,
			contentType: false,
			processData: false,
			success: function (data) {
				window.location.href = data.url;
			},
			error: function (data) {
			},
			complete: function () {
				btn.prop('disabled', false);
				btn.removeClass('lqd-is-busy');
				Alpine.store('appLoadingIndicator').hide();
			}
		});
	});

	$('.btn_install').on('click', function () {
		if(isInstalling) return;
		isInstalling = true;
		let btn = $(this);

		btn.addClass('lqd-is-busy');

		btn.attr('disable', true);

		let formData = new FormData();

		Alpine.store('appLoadingIndicator').show();

		$.ajax({
			type: 'post',
			url: '/install-extension/' + btn.attr('data-name'),
			data: formData,
			contentType: false,
			processData: false,
			success: function (data) {
				toastr.success(magicai_localize?.addon_installed ||'Add-on installed succesfully.');
				setTimeout(() => {
					window.location.reload();
				}, 2000);
			},
			error: function (data) {
				if ( data?.responseJSON?.message ){
					toastr.error( data?.responseJSON?.message);
				}
			},
			complete: function () {
				isInstalling = false;
				btn.removeClass('lqd-is-busy');
				btn.attr('disable', false);
				Alpine.store('appLoadingIndicator').hide();
			}
		});
	});

	$('.btn_installed').on('click', function () {
		if(isInstalling) return;
		isInstalling = true;
		let btn = $(this);

		btn.addClass('lqd-is-busy');
		btn.attr('disable', true);

		let formData = new FormData();

		Alpine.store('appLoadingIndicator').show();

		$.ajax({
			type: 'post',
			url: '/uninstall-extension/' + btn.attr('data-name'),
			data: formData,
			contentType: false,
			processData: false,
			success: function (data) {
				toastr.success(magicai_localize?.addon_uninstalled ||'Add-on uninstalled succesfully.');
				setTimeout(() => {
					window.location.reload();
				}, 2000);
			},
			error: function (data) {
				if ( data?.responseJSON?.message ){
					toastr.error( data?.responseJSON?.message);
				}
			},
			complete: function () {
				isInstalling = false;
				btn.removeClass('lqd-is-busy');
				btn.attr('disable', false);
				Alpine.store('appLoadingIndicator').hide();
			}
		});

	});

	const accordionItems = document.querySelectorAll('.custom-accordion-item');

	$('.custom-accordion-header').on('click', function () {

		const accordionItem = $($(this).closest('.custom-accordion-item'));

		const accordionIcon = $(accordionItem.find('.custom-accordion-icon'));

		const accordionBody = $(accordionItem.find('.custom-accordion-body'));

		if (accordionItem.hasClass('active')) {
			accordionIcon.removeClass('rotate-180');
			accordionItem.removeClass('active');
			accordionBody.animate({
				height: 0
			}, 200, 'linear', function () {
				accordionBody.hide();
			});
		} else {
			accordionIcon.addClass('rotate-180');
			accordionItem.addClass('active');
			accordionBody.css('height', '0px');
			accordionBody.css('display', 'block');
			accordionBody.css('overflow', 'hidden');
			// accordionBody.removeClass('hidden');
			accordionBody.animate({
				height: '100%'
			}, 200, 'linear', function () {
				// accordionBody.hide();
			});
		}
	});


});

