let isGenerating = false;
const CAMPAIGN_CONTENT = 'CAMPAIGN_CONTENT';
const TOKENS = 'TOKENS';

$(document).ready(function () {

	$('#generateCamContent').on('click', function() {
		generateCampaignContent('#lds-dual-ring1', '.generate');
	});
	$('#generateCamContentModal').on('click', function() {
		const cam_name = $('#cam_name').val();

		if (cam_name){
			generateCampaignContentModal('#lds-dual-ring2', '.generate2');
		} else {
			toastr.warning('Please enter the campaign name');
		}
	});
	$('#generateContentTopics').on('click', function() {
		generateContentTopics('#lds-dual-ring3', '.generate');
	});

	if ($('[name="step"]').val() == 6){
		generatePost('', '');
	}

});

function generateCampaignContent(ring, svg) {
	isGenerating = true;
	updateData(ring, svg);

	const campaign_name = $('#camp_id option:selected').text();
	var oldContent = $('#camp_target').val();
	var productIds = [];
	$('input[name="product_id[]"]').each(function() {
		productIds.push($(this).val());
	});

	let success_function = data => {
		try {
			data = JSON.parse(data.result);
			if (oldContent){
				$('#camp_target').val(oldContent + '\n' + data.join('\n'));
			} else {
				$('#camp_target').val(data.join('\n'));
			}
			isGenerating = false;

			updateData(ring, svg);
		} catch (e) {
			console.log(e);
			isGenerating = false;
			updateData(ring, svg);
		}
	};

	let error_function = data => {
		isGenerating = false;
		updateData(ring, svg);
		console.log(data);
		toastr.error(data.responseJSON.message);
	};

	// 0512014899
	let url = '/dashboard/user/automation/campaign/genContent';

	let generate_campaign_url = $('#generate_campaign_url');

	if (generate_campaign_url) {
		if (generate_campaign_url.val()) {
			url = generate_campaign_url.val();
		}
	}


	$.ajax({
		type: 'post',
		url: url,
		data: {
			campaign_name: campaign_name,
			productIds: productIds,
		},
		success: success_function,
		error: error_function
	});
}
function generateCampaignContentModal(ring, svg) {
	isGenerating = true;
	updateData(ring, svg);

	const campaign_name = $('#cam_name').val();

	var productIds = [];
	$('input[name="product_id[]"]').each(function() {
		productIds.push($(this).val());
	});

	let success_function = data => {
		try {
			data = JSON.parse(data.result);
			$('#cam_target').val(data.join('\n'));
			isGenerating = false;
			updateData(ring, svg);
		} catch (e) {
			console.log(e);
			isGenerating = false;
			updateData(ring, svg);
		}
	};

	let error_function = data => {
		isGenerating = false;
		updateData(ring, svg);
		console.log(data);
		toastr.error(data.responseJSON.message);
	};

	let url = '/dashboard/user/automation/campaign/genContent';

	let generate_campaign_url = $('#generate_campaign_url');

	if (generate_campaign_url) {
		if (generate_campaign_url.val()) {
			url = generate_campaign_url.val();
		}
	}

	$.ajax({
		type: 'post',
		url: url,
		data: {
			campaign_name: campaign_name,
			productIds: productIds,
		},
		success: success_function,
		error: error_function
	});
}


function generateContentTopics(ring, svg) {
	isGenerating = true;
	updateData(ring, svg);

	const campaign_name = $('#cam_injected_name').val();
	var productIds = [];
	$('input[name="product_id[]"]').each(function() {
		productIds.push($(this).val());
	});

	const form = document.getElementById('stepsForm');
	const ul = document.querySelector('.select_outline ul');

	let success_function = data => {
		try {
			data = JSON.parse(data.result);
			data.forEach(function(line) {
				const trimmedLine = line.trim();
				if (trimmedLine.length > 0) {
					const li = document.createElement('li');
					li.textContent = trimmedLine;
					ul.appendChild(li);

					const input = document.createElement('input');
					input.type = 'hidden';
					input.name = 'topics[]';
					input.value = trimmedLine;
					form.appendChild(input);
				}
			});
			isGenerating = false;

			updateData(ring, svg);
		} catch (e) {
			console.log(e);
			isGenerating = false;
			updateData(ring, svg);
		}
	};

	let error_function = data => {
		isGenerating = false;
		updateData(ring, svg);
		console.log(data);
		toastr.error(data.responseJSON.message);
	};
	$.ajax({
		type: 'post',
		url: '/dashboard/user/automation/campaign/genTopics',
		data: {
			campaign_name: campaign_name,
			productIds: productIds,
		},
		success: success_function,
		error: error_function
	});
}


function uploadDatabase(type, tokens = 0) {
	let new_data = {};
	new_data['type'] = type;
	if (type == TOKENS) {
		new_data['tokens'] = tokens;
	}

	$.ajax({
		type: 'post',
		url: '/dashboard/user/automation/update',
		data: JSON.stringify(new_data),
		contentType: 'application/json',
		success: function (data) {

		},
		error: function (data) {
			console.log(data);
			toastr.error(data.responseJSON.message);
		}
	});
}
function updateData(ring, svg) {
	console.log(ring, svg);

	if (isGenerating == true) {
		$(ring).removeClass('hidden');
		$(svg).addClass('hidden');
		document.querySelector('#app-loading-indicator')?.classList?.remove('opacity-0');
	} else {
		$(ring).addClass('hidden');
		$(svg).removeClass('hidden');
		document.querySelector('#app-loading-indicator')?.classList?.add('opacity-0');
	}
}
function generatePost(ring, svg){
	$('#reviweNextBtn').prop('disabled', true);
	isGenerating = true;
	updateData(ring, svg);

	var platform = 'Any';
	var platform_id = $('[name="platform_id"]').val();
	switch (platform_id) {
		case '1':
			platform = 'Twitter.com/X.com';
			break;
		case '2':
			platform = 'Linkedin.com';
			break;
		default:
			platform = 'Any';
			break;
	}

	var company_id = $('[name="company_id"]').val();
	var cam_injected_name = $('[name="cam_injected_name"]').val();
	var camp_target = $('[name="camp_target"]').val();
	var productIds = $('input[name="product_id[]"]').map(function() {return $(this).val();}).get();
	var topics = $('input[name="topics[]"]').map(function() {return $(this).val();}).get();

	var tone = $('[name="tone"]').val();
	var num_res = $('[name="num_res"]').val();
	var seo = $('[name="seo"]').val();

	// var is_img = $('[name="is_img"]').val();
	// var vis_format = $('[name="vis_format"]').val();
	// var vis_ratio = $('[name="vis_ratio"]').val();

	let success_function = data => {
		try {
			data = data.result;
			$('#thePost').text(data);
			if ($('#content')) {
				$('#content').val(data);
			}
			isGenerating = false;
			$('#reviweNextBtn').prop('disabled', false);
			updateData(ring, svg);
		} catch (e) {
			console.log(e);
			isGenerating = false;
			$('#reviweNextBtn').prop('disabled', false);
			updateData(ring, svg);
		}
	};
	let error_function = data => {
		isGenerating = false;
		$('#reviweNextBtn').prop('disabled', false);
		updateData(ring, svg);
		console.log(data);
		toastr.error(data.responseJSON.message);
	};

	$.ajax({
		type: 'post',
		url: '/dashboard/user/automation/genPost',
		data: {
			company_id: company_id,
			platform_id: platform_id,
			productIds: productIds,
			platform: platform,
			cam_injected_name: cam_injected_name,
			camp_target: camp_target,
			topics: topics,
			tone: tone,
			num_res: num_res,
			seo: seo,
		},
		success: success_function,
		error: error_function
	});
}
function getCompany(id)
{
	var company;
	$.ajax({
		url: '/dashboard/user/automation/getCompany',
		method: 'POST',
		data: { id: id },
		dataType: 'json',
		async: false,
		success: function(data) {
			company = data;
		},
		error: function(error) {
			console.error('Error:', error);
		}
	});
	return company;
}
function getProducts(ids_array){
	var products;
	$.ajax({
		url: '/dashboard/user/automation/getSelectedProducts',
		method: 'POST',
		data: { ids_array: ids_array },
		dataType: 'json',
		async: false,
		success: function(data) {
			products = data;
		},
		error: function(error) {
			console.error('Error:', error);
		}
	});
	return products;
}
