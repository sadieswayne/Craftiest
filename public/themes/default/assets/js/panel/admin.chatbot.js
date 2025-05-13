jQuery(document).ready(function($) {
	'use strict';

	let count = $('[data-select="item"]:checked').length;

	let pages_total_count = $('#pages_total_count').val();

	if(pages_total_count != 0 && pages_total_count == count) {
		$('[data-select="all"]').addClass('has-selected');
	}

	$(document).on('click', '[data-edit="item-qa"]', function () {
		let id = $(this).data('id');
		let question = $(this).data('question');
		let answer = $(this).data('answer');

		$('#qa_id').val(id);

		$('#question').val(question);

		$('#answer').val(answer);
	});

	$(document).on('submit', '#save_form', event => {
		event.preventDefault();

		let form = $('#save_form');

		let action = form.attr('action');

		let data = form.serialize();

		$.ajax({
			type: 'POST',
			url: action,
			data: data,
			success: function (response) {
				if(response.status == 'error') {
					toastr.error(response.message);
				} else {
					toastr.success(response.message);
				}

			},
			error: function (response) {
				toastr.error(response.responseJSON.message);
			}
		});
	});

	$(document).on('click', '[data-edit="item"]', function () {
		let id = $(this).data('id');
		let title = $(this).data('title');
		let text = $(this).data('text');

		$('#text_id').val(id);

		$('#title').val(title);

		$('#content_text').val(text);

	});

	$(document).on('click', '[data-submit="addtext"]', function () {

		const btn = $(this);
		let formId = $(this).data('form');
		let form = $(formId);
		let data = form.serializeArray();
		let url = form.attr('action');

		btn.addClass('lqd-is-busy');

		$.ajax({
			type: 'POST',
			url: url,
			data: data,
			success: function (response) {
				if(response.status == 'error') {
					toastr.error(response.message);
				} else {
					toastr.success(response.message);
				}

				$('#text-list').html(response.content);

				btn.removeClass('lqd-is-busy');

				$('#text-list').removeClass('hidden');

				$('#text_id').val('');

				$('#title').val('');

				$('#content_text').val('');

				if (response.count) {
					$('#text-list-alert').removeClass('hidden');
				}
			},
			error: function (response) {
				btn.removeClass('lqd-is-busy');
				toastr.error(response.responseJSON.message);
			}
		});
	});

	$(document).on('click', '[data-submit="addqa"]', function () {

		const btn = $(this);
		let formId = $(this).data('form');
		let form = $(formId);
		let data = form.serializeArray();
		let url = form.attr('action');

		btn.addClass('lqd-is-busy');

		$.ajax({
			type: 'POST',
			url: url,
			data: data,
			success: function (response) {
				if(response.status == 'error') {
					toastr.error(response.message);
				} else {
					toastr.success(response.message);
				}

				$('#qa-list').html(response.content);

				btn.removeClass('lqd-is-busy');

				$('#qa-list').removeClass('hidden');

				$('#qa_id').val('');

				$('#question').val('');

				$('#answer').val('');

				if (response.count) {
					$('#text-list-alert').removeClass('hidden');
				}
			},
			error: function (response) {
				btn.removeClass('lqd-is-busy');
				toastr.error(response.responseJSON.message);
			}
		});
	});

	$(document).on('click','#web-site-form-submit', function () {
		const btn = $(this);
		let action = $(this).data('action');
		let url = $('[name="url"]').val();
		let typeRadio = $('[name="type"]:checked').val();

		if (!url) {
			toastr.error(magicai_localize?.please_enter_url ||'Please enter the URL');
			return;
		}

		btn.addClass('lqd-is-busy');
		$('#chatbotPages').addClass('hidden');

		$.ajax({
			type: 'POST',
			url: action,
			data: {url: url, type: typeRadio},
			success: function (response) {

				if(response.status == 'error') {
					toastr.error(response.message);
				} else {
					toastr.success(response.message);
				}

				$('#chatbotPages').html(response.content);

				btn.removeClass('lqd-is-busy');
				$('#chatbotPages').removeClass('hidden');

				$('#pages_total_count').val( $('#chatbotPages > .item').length );

			},
			error: function (response) {
				btn.removeClass('lqd-is-busy');
				toastr.error(response.responseJSON.message);
			}
		});
	});

	$(document).on('click', '[data-item="delete"]', function () {
		let parent = $(this).data('parent');

		let url = $(this).data('url');

		if(confirm('Are you sure? This is item and will delete.')) {
			$.ajax({
				type: 'DELETE',
				url: url,
				success: function(result) {
					if(result.status == 'error') {
						toastr.error(result.message);
					} else {
						toastr.success(result.message);
					}

					$(parent).remove();

				}
			});
		}
	});

	$(document).on('click','[data-submit="train"]' , function () {

		let formId = $(this).data('form');

		let list = $(this).data('list');

		let form = $(formId);

		let data = form.serializeArray();

		let url = form.attr('action');

		$('.crawler-spinner').removeClass('hidden');

		let alertTrain = $('.text-list-alert');

		if (alertTrain) {
			alertTrain.addClass('hidden');
		}

		let pages = $(list);

		if (pages) {
			pages.addClass('hidden');
		}

		$.ajax({
			type: 'POST',
			url: url,
			data: data,
			success: function(result) {
				if (pages) {
					pages.html(result.content);
					pages.removeClass('hidden');
				}

				if (alertTrain) {
					alertTrain.removeClass('hidden');
				}

				$('.crawler-spinner').addClass('hidden');

				if(result.status == 'error') {
					toastr.error(result.message);
				} else {
					toastr.success(result.message);
				}

			},
			error: function (response) {

				if (pages) {
					// pages.html(result.content);
					pages.removeClass('hidden');
				}

				if (alertTrain) {
					alertTrain.removeClass('hidden');
				}

				$('.crawler-spinner').addClass('hidden');

				// $('.crawler-spinner').addClass('hidden');

				// if (pages) {
				// pages.html(result.content);
				// 	pages.removeClass('hidden');
				// }

				toastr.error(response.responseJSON.message);
			}
		});

	});

	$(document).on('click', '[data-select="all"]', function () {

		let count = $('[data-select="item"]:checked').length;

		let pages_total_count = $('#pages_total_count').val();

		if(pages_total_count == count) {
			$('[data-select="item"]').prop('checked', false);
			$('[data-select="all"]').removeClass('has-selected');
		} else {
			$('[data-select="item"]').prop('checked', true);
			$('[data-select="all"]').addClass('has-selected');
		}

	});

	$(document).on('change', '[data-select="item"]', function () {
		let count = $('[data-select="item"]:checked').length;

		let pages_total_count = $('#pages_total_count').val();

		if(pages_total_count == count) {
			$('[data-select="all"]').removeClass('has-selected');
		}else{
			$('[data-select="all"]').addClass('has-selected');
		}
	});
});


function dropHandler(ev, id) {
	ev.preventDefault();
	$('#' + id)[0].files = ev.dataTransfer.files;
	$('#' + id).prev().find('.file-name').text(ev.dataTransfer.files[0].name);

	for (let i = 0; i < ev.dataTransfer.files.length; i++) {

		let reader = new FileReader();
		// Existing image handling code
		reader.onload = function(e) {
			var img = new Image();
			img.src = e.target.result;
			img.onload = function() {
				var canvas = document.createElement('canvas');
				var ctx = canvas.getContext('2d');
				canvas.height = img.height * 200 / img.width;
				canvas.width = 200;
				ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
				var base64 = canvas.toDataURL('image/png');
				addImagetoChat(base64);
			};
		};

		handleFileSelect(id);

		reader.readAsDataURL(ev.dataTransfer.files[id]);
	}
	document.getElementById('mainupscale_src').style.display = 'none';


}

function dragOverHandler(ev) {
	// Prevent default behavior (Prevent file from being opened)
	ev.preventDefault();
}

function handleFileSelect(id) {

	let input = $('#' + id);

	let action = input.data('action');

	var formData = new FormData();

	input
		.prev()
		.find('.file-name')
		.text($('#' + id)[0].files[0].name);

	$('.pdf-list').addClass('hidden');

	// add assoc key values, this will be posts values
	formData.append('file', input[0].files[0]);

	input.closest('label').addClass('lqd-is-busy');

	$.ajax({
		url: action,
		type: 'POST',
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		success: function (data) {
			$('#' + id).prev().find('.file-name').text('Upload a PDF File (Max: 25Mb)');
			$('.pdf-list').html(data.content);
			input.closest('label').removeClass('lqd-is-busy');
			$('.pdf-list').removeClass('hidden');

			if(data.status == 'error') {
				toastr.error(data.message);
			} else {
				toastr.success(data.message);
			}

		},
		error: function (data) {
			input.closest('label').removeClass('lqd-is-busy');

			$('.pdf-list').removeClass('hidden');

			toastr.error(data.responseJSON.message);
		}
	});
}
