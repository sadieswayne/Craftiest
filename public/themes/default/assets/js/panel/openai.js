//Admin

$( document ).ready( function () {
	'use strict';

	const colorInput = document.querySelector( '#color' );
	const colorValue = document.querySelector( '#color_value' );
	const chatCompletionFillBtn = document.querySelector( '.chat-completions-fill-btn' );

	colorInput?.addEventListener( 'input', ev => {
		const input = ev.currentTarget;

		if ( colorValue ) {
			colorValue.value = input.value;
		}
	} );

	colorValue?.addEventListener( 'input', ev => {
		const input = ev.currentTarget;

		if ( colorInput ) {
			colorInput.value = input.value;
		}
	} );

	chatCompletionFillBtn?.addEventListener( 'click', () => {
		editor_chat_completions?.setValue( `[
	{
		"role": "system",
		"content": "You are a helpful assistant."
	},
	{
		"role": "user",
		"content": "Who won the world series in 2020?"
	},
	{
		"role": "assistant",
		"content": "The Los Angeles Dodgers won the World Series in 2020."
	},
	{
		"role": "user",
		"content": "Where was it played?"
	}
]`, -1 );
	} );

	//admin.openai.custom.form
	if ( $.fn.select2 ) {
		$( '.select2' ).select2( {
			tags: true
		} );
	}

} );

//admin.openai.list
function updateStatus( status, entry_id ) {
	'use strict';

	var formData = new FormData();
	formData.append( 'status', status );
	formData.append( 'entry_id', entry_id );

	$.ajax( {
		type: 'post',
		url: '/dashboard/admin/openai/update-status',
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			const templateRow = document.querySelector( `#template-${ entry_id }` );
			toastr.success(magicai_localize?.status_changed ||'Status changed succesfully');
			templateRow?.classList?.toggle('active', status == 1);
			templateRow?.classList?.toggle('passive', status == 0);
		},
		error: function ( data ) {
			toastr.error(magicai_localize?.something_wrong ||'Something went wrong. Please reload the page and try it again');
		}
	} );
	return false;
}

function updateChatStatus(status, entry_id) {
	'use strict';

	var formData = new FormData();
	formData.append( 'status', status );
	formData.append( 'entry_id', entry_id );

	$.ajax( {
		type: 'post',
		url: '/dashboard/admin/openai/chat/update-plan',
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success(magicai_localize?.status_changed ||'Status changed succesfully');
		},
		error: function ( data ) {
			toastr.error(magicai_localize?.something_wrong ||'Something went wrong. Please reload the page and try it again');
		}
	} );
	return false;
}

function updatePackageStatus( status, entry_id ) {
	'use strict';

	var formData = new FormData();
	formData.append( 'status', status );
	formData.append( 'entry_id', entry_id );

	$.ajax( {
		type: 'post',
		url: '/dashboard/admin/openai/update-package-status',
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success(magicai_localize?.status_changed ||'Status changed succesfully');
		},
		error: function ( data ) {
			toastr.error(magicai_localize?.something_wrong ||'Something went wrong. Please reload the page and try it again');
		}
	} );
	return false;
}

function templateSave(template_id) {
	'use strict';

	document.getElementById('custom_template_button').disabled = true;
	document.getElementById('custom_template_button').innerHTML = magicai_localize.please_wait;

	var inputDataByType = {};

	// Iterate over each input type
	$('.input_type').each(function(index) {
		var inputType = $(this).val();
		var inputName = $('.input_name').eq(index).val();
		var inputDescription = $('.input_description').eq(index).val();
		var selectListValues = [];

		// If the input type is select list, collect its values
		if (inputType === 'select') {
			$('.selectlistinputsvalues').eq(index).find('option').each(function() {
				selectListValues.push($(this).val());
			});
		}

		// Create an object for the current input type if it doesn't exist
		if (!inputDataByType[inputType]) {
			inputDataByType[inputType] = [];
		}

		// Push the input data to the corresponding type array
		inputDataByType[inputType].push({
			inputName: inputName,
			inputDescription: inputDescription,
			selectListValues: selectListValues
		});
	});

	var formData = new FormData();
	formData.append('template_id', template_id);
	formData.append('title', $('#title').val());
	formData.append('filters', $('#filters').val());
	formData.append('premium', $('#premium').val());
	formData.append('description', $('#description').val());
	formData.append('image', $('#image').val());
	formData.append('color', $('#color').val());
	formData.append('prompt', $('#prompt').val());
	formData.append('input_data_by_type', JSON.stringify(inputDataByType));

	var reqUrl = '/dashboard/admin/openai/custom/save';
	var return_url = '/dashboard/admin/openai/custom';
	if (document.getElementById('is_user')) {
		reqUrl = '/dashboard/user/openai/custom/save';
		return_url = '/dashboard/user/openai/custom';
	} else {
		reqUrl = '/dashboard/admin/openai/custom/save';
		return_url = '/dashboard/admin/openai/custom';
	}

	$.ajax({
		type: 'post',
		url: reqUrl,
		data: formData,
		contentType: false,
		processData: false,
		success: function(data) {
			toastr.success(magicai_localize?.template_saved ||'Template Saved Successfully');
			location.href = return_url;
			document.getElementById('custom_template_button').disabled = false;
			document.getElementById('custom_template_button').innerHTML = 'Save';
		},
		error: function(data) {
			var err = data.responseJSON.errors;
			$.each(err, function(index, value) {
				toastr.error(value);
			});
			document.getElementById('custom_template_button').disabled = false;
			document.getElementById('custom_template_button').innerHTML = 'Save';
		}
	});
	return false;
}

$( document ).ready( function () {
	'use strict';

	const slugify = str => {
		// Convert the string to lowercase and remove leading/trailing whitespaces
		let slug = str.toLowerCase().trim();

		// Replace special characters with empty string
		slug = slug.replace(/[^\w\s-]/g, '');

		// Replace spaces, underscores, or hyphens with a single hyphen
		slug = slug.replace(/[\s_-]+/g, '-');

		// Remove any leading or trailing hyphens
		slug = slug.replace(/^-+|-+$/g, '');

		// Add asterisks to the beginning and end of the slug
		slug = `**${slug}**`;

		return slug;
	};

	/** @type {HTMLTemplateElement} */
	const userInputTemplate = document.querySelector( '#user-input-template' );
	const afterAddMoreButton = document.querySelector( '.after-add-more-button' );
	const addMorePlaceholder = document.querySelector( '.add-more-placeholder' );
	let currentInputGroupts = document.querySelectorAll( '.user-input-group' );
	let lastInputsParent = [ ...currentInputGroupts ].at( -1 );
	let lastInpusGroupId = lastInputsParent ? parseInt( lastInputsParent.getAttribute( 'data-inputs-id' ), 10 ) : 0;

	if ( currentInputGroupts.length > 1 ) {
		document.querySelectorAll( '.remove-inputs-group' ).forEach( el => el.removeAttribute( 'disabled' ) );
	} else {
		document.querySelectorAll( '.remove-inputs-group' ).forEach( el => el.setAttribute( 'disabled', true ));
	}

	$( '.add-more' ).click( function () {
		const currentInputs = document.querySelectorAll( '.input_name, .input_description, .input_type' );
		let anInputIsEmpty = false;

		currentInputs.forEach( input => {
			const { value } = input;
			if ( !value || value.length === 0 || value.replace( /\s/g, '' ) === '' ) {
				return anInputIsEmpty = true;
			}
		} );

		if ( anInputIsEmpty ) {
			return toastr.error(magicai_localize?.fill_all_fields ||'Please fill all fields in User Group Input areas');
		}

		const newInputsMarkup = userInputTemplate.content.cloneNode( true );
		const newInputsWrapper = newInputsMarkup.firstElementChild;

		newInputsWrapper.dataset.inputsId = lastInpusGroupId + 1;

		addMorePlaceholder.before( newInputsMarkup );

		currentInputGroupts = document.querySelectorAll( '.user-input-group' );
		lastInputsParent = [ ...currentInputGroupts ].at( -1 );

		if ( currentInputGroupts.length > 1 ) {
			document.querySelectorAll( '.remove-inputs-group' ).forEach( el => el.removeAttribute( 'disabled' ) );
		} else {
			document.querySelectorAll( '.remove-inputs-group' ).forEach( el => el.setAttribute( 'disabled', true ));
		}

		lastInpusGroupId++;

		const timeout = setTimeout( () => {
			newInputsWrapper.querySelector( '.input_name' )?.focus();
			clearTimeout( timeout );
		}, 100 );

		return;

	} );

	$( 'body' ).on( 'click', '.remove-inputs-group', function () {
		const button = $( this );
		const parent = button.closest( '.user-input-group' );
		const inputsId = parent.attr( 'data-inputs-id' );
		const prompt = $( '#prompt' );
		const currentPromptVal = prompt.val();

		if ( currentInputGroupts.length <= 1 ) {
			return toastr.error(magicai_localize?.at_least_one_group ||'You must have at least one group of inputs');
		}

		prompt.val( currentPromptVal.replaceAll( slugify( parent.attr( 'data-input-name' ) ), '' ) );

		$( `[data-inputs-id=${ inputsId }]` ).remove();

		currentInputGroupts = document.querySelectorAll( '.user-input-group' );
		lastInputsParent = [ ...currentInputGroupts ].at( -1 );

		if ( currentInputGroupts.length > 1 ) {
			document.querySelectorAll( '.remove-inputs-group' ).forEach( el => el.removeAttribute( 'disabled' ) );
		} else {
			document.querySelectorAll( '.remove-inputs-group' ).forEach( el => el.setAttribute( 'disabled', true ) );
		}
	} );

	$( 'body' ).on( 'click', 'button[data-input-name]', function () {
		const prompt = $( '#prompt' );
		const currentPromptVal = prompt.val();
		prompt.val( currentPromptVal + slugify( $( this ).attr( 'data-input-name' ) ) );
	} );

	$( 'body' ).on( 'input', '.input_name', ev => {
		const input = ev.currentTarget;
		const parent = input.closest( '.user-input-group' );
		const parentId = parent.getAttribute( 'data-inputs-id' );
		const inputName = slugify( input.value );
		let button = document.querySelector( `button[data-inputs-id="${ parentId }"]` );

		if ( !button ) {
			button = document.createElement( 'button' );
			button.className = 'bg-[#EFEFEF] text-black cursor-pointer py-[0.15rem] px-[0.5rem] border-none rounded-full transition-all duration-300 hover:bg-black hover:!text-white';
			button.dataset.inputsId = parentId;
			button.type = 'button';
			afterAddMoreButton.append( button );
		}

		parent.dataset.inputName = inputName;
		button.dataset.inputName = inputName;
		button.innerText = inputName;
	} );

} );
