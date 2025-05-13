var chart = undefined;
var keywordsCount = 0;
var headersCount = 0;
var linksCount = 0;
var imagesCount = 0;


var keywords = [];
var headers = [];
var links = [];
var images = [];
const filters = ['Keywords', 'Headers', 'Links', 'Images'];

$( document ).ready( function () {
	'use strict';
	// var colorStops = [];
	// colorStops = ['#FF0000', '#FF0000']; // Red for 0-30%
	// renderChart(0, colorStops);

	function newArticle() {
		$.ajax( {
			type: 'post',
			url: '/dashboard/user/openai/articlewizard/clear',
			success: function ( data ) {
				location.href = '/dashboard/user/openai/articlewizard/new';
			},
			error: function ( data ) {

			}
		} );
	}

	$( '#new_article' ).on( 'click', newArticle );

	document.querySelector('#btn_add_new' ).addEventListener( 'click', function () {
		let wizardData = { ...CUR_STATE };
		if ( wizardData.current_step == 0 ) {
			if ( $( '#new_keyword' ).val().trim() == '' ) {
				toastr.warning( 'Please input new keyword' );
				return;
			} else {
				addKeyword();
				$( '#new_keyword' ).val( '' );
			}
		}
		if ( wizardData.current_step == 1 ) {
			if ( $( '#new_title' ).val().trim() == '' ) {
				toastr.warning( 'Please input new title' );
				return;
			} else {
				addTitle();
				$( '#new_title' ).val( '' );
			}
		}
		if ( wizardData.current_step == 2 ) {
			if ( $( '#new_outline' ).val().trim() == '' ) {
				toastr.warning( 'Please input new outline' );
				return;
			} else {
				addOutline();
				$( '#new_outline' ).val( '' );
			}
		}
		if ( wizardData.current_step == 3 ) {
			if ( $( '#new_file' ).val().trim() == '' ) {
				toastr.warning( 'Please select image' );
				return;
			}
			else {
				addImage();
				$( '#new_image' )
					.attr( 'src', 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==' )
					.addClass( 'hidden' );
				$( '#new_file' ).val( '' );
			}
		}
		// $( '.popover__back' ).addClass( 'hidden' );
		// $( '#popover' ).removeClass( 'popover__wrapper' );
	} );

	$( '#select_all_keyword' ).on( 'click', function () {
		let wizardData = { ...CUR_STATE };
		let keyarr = wizardData.extra_keywords;
		if ( keyarr != '' ) {
			wizardData.keywords = JSON.parse( wizardData.extra_keywords ).join( ',' );
		}
		CUR_STATE = { ...wizardData };
		updateData();

	} );

	$( '#unselect_all_keyword' ).on( 'click', function () {
		let wizardData = { ...CUR_STATE };
		wizardData.keywords = '';
		CUR_STATE = { ...wizardData };
		updateData();

	} );
} );

function renderChart(percent, colorStops) {
	try {
		var options = {
			series: [percent],
			chart: {
				type: 'radialBar',
				offsetY: -20,
				sparkline: {
					enabled: true
				}
			},
			plotOptions: {
				radialBar: {
					startAngle: -90,
					endAngle: 90,
					track: {
						background: "#e7e7e7",
						strokeWidth: '97%',
						margin: 5, // margin is in pixels
						dropShadow: {
							enabled: true,
							top: 2,
							left: 0,
							color: '#999',
							opacity: 1,
							blur: 2
						}
					},
					dataLabels: {
						enabled: false,
					}
				}
			},
			grid: {
				padding: {
					top: 10
				}
			},
			fill: {
				type: 'gradient',
				gradient: {
					shade: 'light',
					shadeIntensity: 0.4,
					inverseColors: false,
					opacityFrom: 1,
					opacityTo: 1,
					stops: [0, 30, 60, 100],
					colorStops: [{
							offset: 0,
							color: colorStops[0],
							opacity: 1
						},
						{
							offset: 30,
							color: colorStops[1],
							opacity: 1
						},
						{
							offset: 60,
							color: colorStops[2] || colorStops[1],
							opacity: 1
						},
						{
							offset: 100,
							color: colorStops[2] || colorStops[1],
							opacity: 1
						}
					]
				},
			},
			labels: ['Seo Percent'],
			colors: ['#000000'], // This color won't matter much as we are using gradient
		};

		if (chart) {
			// remove the old chart
			chart.destroy();
			chart = new ApexCharts(document.getElementById('chart-credit'), options);
			chart.render();
		} else {
			chart = new ApexCharts(document.getElementById('chart-credit'), options);
			chart.render();
		}
		document.querySelector('.total_percent span').innerText = percent;
	} catch (e) {
		console.log(e);
	}
}

$( '#new_file' ).change( function () {
	var file = this.files[ 0 ];
	var reader = new FileReader();
	reader.onloadend = function () {
		$( '#new_image' )
			.attr( 'src', reader.result )
			.removeClass('hidden');
	};
	if ( file ) {
		reader.readAsDataURL( file );
	} else {
		$( '#new_image' )
			.attr( 'src', 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==' )
			.addClass('hidden');
	}
} );

$( '#add_btn' ).on( 'click', function ( e ) {
	$( '#popover' ).addClass( 'popover__wrapper' );
	$( '.popover__back' ).removeClass( 'hidden' );
	e.stopPropagation();
} );

$( '.popover__back' ).on( 'click', function () {
	$( this ).addClass( 'hidden' );
	$( '#popover' ).removeClass( 'popover__wrapper' );
} );

const EXTRA_KEYWORDS = 'EXTRA_KEYWORDS';
const EXTRA_TITLES = 'EXTRA_TITLES';
const EXTRA_OUTLINES = 'EXTRA_OUTLINES';
const EXTRA_IMAGES = 'EXTRA_IMAGES';
const KEYWORDS = 'KEYWORDS';
const TITLE = 'TITLE';
const OUTLINE = 'OUTLINE';
const IMAGE = 'IMAGE';
const RESULT = 'RESULT';
const STEP = 'STEP';
const UPDATE_STEP = 'UPDATE_STEP';
const TOKENS = 'TOKENS';
let intervalId = 0;

let CUR_STATE;
let isGenerating = false;

$( '#article_wizard_setting_form' ).submit( function ( event ) {
	event.preventDefault();
	generateData();
} );

$( '#skip_image' ).on( 'click', () => {
	let wizardData = { ...CUR_STATE };
	wizardData.image = '';
	CUR_STATE = { ...wizardData };
	goNextStep();
} );

$( document ).on( 'click', '.keyword', function () {
	let wizardData = { ...CUR_STATE };
	let keywords = wizardData.keywords;
	let curtxt = $( this ).text().trim();
	let keyArr = keywords.split( ',' );
	if ( keyArr.includes( curtxt ) ) {
		let index = keyArr.indexOf( curtxt );
		if ( index !== -1 ) {
			keyArr.splice( index, 1 );
		}
		keywords = keyArr.join( ',' );
	} else {
		keywords += ',' + curtxt;
	}
	wizardData.keywords = keywords.split( ',' ).map( item => item.trim() ).filter( item => item !== '' ).join( ',' );
	CUR_STATE = { ...wizardData };
	updateData();
} );

$( document ).on( 'click', '.step', function () {
	if ( isGenerating ) return;
	let index = $( '.step' ).index( this );
	if ( index < CUR_STATE.current_step ) {
		CUR_STATE.current_step = index;
		if ( CUR_STATE.current_step <= 0 ) {
			CUR_STATE.extra_titles = '';
			CUR_STATE.title = '';
		}
		if ( CUR_STATE.current_step <= 1 ) {
			CUR_STATE.extra_outlines = '';
			CUR_STATE.outline = '';
		}
		if ( CUR_STATE.current_step <= 2 ) {
			CUR_STATE.extra_images = '';
			CUR_STATE.image = '';
		}
		uploadDatabase( UPDATE_STEP );
	}
} );

$( document ).on( 'click', '.title', function () {
	let wizardData = { ...CUR_STATE };
	let curtxt = subElement = $( this ).find( '.title_text' ).text().trim();
	wizardData.title = curtxt;
	CUR_STATE = { ...wizardData };
	updateData();
} );

$( document ).on( 'click', '.outline_', function () {
	let wizardData = { ...CUR_STATE };
	let curtxt = Number( $( this ).attr( 'data' ) );
	wizardData.outline = JSON.stringify( JSON.parse( wizardData.extra_outlines )[ curtxt ] );
	CUR_STATE = { ...wizardData };
	updateData();
} );

$( document ).on( 'click', '.image_', function () {
	let wizardData = { ...CUR_STATE };
	let curtxt = $( this ).find( 'img' ).attr( 'src' );
	wizardData.image = curtxt;
	CUR_STATE = { ...wizardData };
	updateData();
} );

function generateData() {
	if ( isGenerating ) {
		return;
	}
	let wizardData = { ...CUR_STATE };
	if ( wizardData.current_step == 0 ) {
		if ( $( '#txtforkeyword' ).val() == '' ) {
			toastr.warning( 'Please input topic' );
			return;
		}
		isGenerating = true;
		Alpine.store('appLoadingIndicator').show();
		let wizardData = { ...CUR_STATE };
		wizardData.topic_keywords = $( '#txtforkeyword' ).val();
		CUR_STATE = { ...wizardData };
		updateData();
		generateKeywords();
	} else if ( wizardData.current_step == 1 ) {
		isGenerating = true;
		Alpine.store('appLoadingIndicator').show();
		let wizardData = { ...CUR_STATE };
		wizardData.topic_title = $( '#txtfortitle' ).val();
		CUR_STATE = { ...wizardData };
		updateData();
		generateTitles();
	} else if ( wizardData.current_step == 2 ) {
		isGenerating = true;
		Alpine.store('appLoadingIndicator').show();
		let wizardData = { ...CUR_STATE };
		wizardData.topic_outline = $( '#txtforoutline' ).val();
		CUR_STATE = { ...wizardData };
		updateData();
		generateOutlines();
	} else if ( wizardData.current_step == 3 ) {
		isGenerating = true;
		Alpine.store('appLoadingIndicator').show();
		let wizardData = { ...CUR_STATE };
		wizardData.topic_image = $( '#txtforimage' ).val();
		CUR_STATE = { ...wizardData };
		updateData();
		generateImages();
	}
}

function goNextStep() {
	if ( isGenerating ) return;
	let wizardData = { ...CUR_STATE };
	if ( wizardData.current_step == 0 ) {
		if ( wizardData.keywords.trim() == '' ) {
			toastr.error( 'Please select more than 1 keywords' );
		} else {
			uploadDatabase( KEYWORDS );
		}
	}
	if ( wizardData.current_step == 1 ) {
		if ( wizardData.title.trim() == '' ) {
			toastr.error( 'Please select title' );
		} else {
			uploadDatabase( TITLE );
		}
	}
	if ( wizardData.current_step == 2 ) {
		if ( wizardData.outline.trim() == '' ) {
			toastr.error( 'Please select outline' );
		} else {
			uploadDatabase( OUTLINE );
		}
	}
	if ( wizardData.current_step == 3 ) {
		uploadDatabase( IMAGE );
	}
}

function generateKeywords() {
	let useSeo = $('#use_seo_aw_keyword');
	let language = $( '#language option:selected' ).text();
	const keywords_topic = $( '#txtforkeyword' ).val();
	const keywords_count = Number( $( '#number_of_keywords' ).val() );

	let success_function = ( data ) => {
		if ( stream_type == 'backend' ) {
			updateRemainingBar();
		} else {
			uploadDatabase( TOKENS, data.result.trim().split( /\s+/ ).length );
		}
		data = data.result;
		isGenerating = false;
		const wizardData = { ...CUR_STATE };
		wizardData.topic_keywords = keywords_topic;

		try {
			extra_keywords = JSON.parse( data ).map( item => item.toLowerCase() );
			let temp;
			if ( wizardData.extra_keywords == '' ) {
				temp = [];
			} else {
				temp = JSON.parse( wizardData.extra_keywords ).map( item => item.toLowerCase() );
			}
			wizardData.extra_keywords = JSON.stringify( Array.from( new Set( [ ...temp, ...extra_keywords ] ) ) );

			CUR_STATE = { ...wizardData };

			updateData();
			uploadDatabase( EXTRA_KEYWORDS );
		} catch ( e ) {
			console.log( e );
			isGenerating = false;
			updateData();
		}
		Alpine.store('appLoadingIndicator').hide();
	};

	let error_function = ( data ) => {
		isGenerating = false;
		Alpine.store('appLoadingIndicator').hide();
		updateData();
		console.log( data );
		toastr.error( data.responseJSON.message );
	};

	if(useSeo?.is(":checked")){

		$.ajax( {
			type: 'post',
			url: '/dashboard/user/seo/genkeywords',
			data: {
				id: ( { ...CUR_STATE } ).id,
				topic: keywords_topic,
				count: keywords_count,
				language: language,
			},
			success: success_function,
			error: error_function
		} );
		
	}else{
		if ( stream_type == 'backend' ) {
			$.ajax({
				type: 'post',
				url: '/dashboard/user/openai/articlewizard/genkeywords',
				data: {
					id: ( { ...CUR_STATE } ).id,
					topic: keywords_topic,
					count: keywords_count,
					language: language,
				},
				success: success_function,
				error: error_function
			});
		} else {
			$.ajax({
				type: 'post',
				url: atob( guest_id ),
				headers: {
					'Authorization': 'Bearer ' + atob( guest_event_id ) + atob( guest_look_id ) + atob( guest_product_id ),
					'Content-Type': 'application/json'
				},
				data: JSON.stringify( {
					messages: [ {
						role: 'user',
						content: `Generate ${ keywords_count } keywords(simple words or 2 words, not phrase, not person name) about '${ keywords_topic }'. in ${ language } language. Result JSON format is [keyword1, keyword2, ..., keywordn] without any additional formatting or characters.`
					} ],
					model: openai_model
				} ),
				success: function ( data ) {
					data = data[ 'choices' ][ 0 ][ 'message' ][ 'content' ];
					success_function( { result: data } );
				},
				error: error_function
			});
		}
	}
}

function generateTitles() {
	let language = $( '#language option:selected' ).text();
	const title_topic = $( '#txtfortitle' ).val();
	const titles_count = Number( $( '#number_of_titles' ).val() );
	const title_length = Number( $( '#title_length' ).val() );

	let success_function = ( data ) => {
		if ( stream_type == 'backend' ) {
			updateRemainingBar();
		} else {
			uploadDatabase( TOKENS, data.result.trim().split( /\s+/ ).length );
		}
		data = data.result;

		isGenerating = false;
		const wizardData = { ...CUR_STATE };
		wizardData.topic_title = title_topic;

		try {
			let extra_titles = JSON.parse( data );
			let temp;
			if ( wizardData.extra_titles == '' ) {
				temp = [];
			} else {
				temp = JSON.parse( wizardData.extra_titles );

			}
			if ( temp.length >= 1 && temp[ 0 ] == 'title1' || typeof ( temp[ 0 ] ) == 'object' ) {
				isGenerating = true;
				updateData();
				generateTitles();
			} else {
				wizardData.extra_titles = JSON.stringify( Array.from( new Set( [ ...temp, ...extra_titles ] ) ) );

				CUR_STATE = { ...wizardData };

				updateData();
				uploadDatabase( EXTRA_TITLES );
			}
		} catch ( e ) {
			console.log( e );
			isGenerating = false;
			updateData();
		}
		Alpine.store('appLoadingIndicator').hide();
	};

	let error_function = ( data ) => {
		isGenerating = false;
		Alpine.store('appLoadingIndicator').hide();
		updateData();
		console.log( data );
		toastr.error( data.responseJSON.message );
	};

	if ( stream_type == 'backend' ) {
		$.ajax( {
			type: 'post',
			url: '/dashboard/user/openai/articlewizard/gentitles',
			data: {
				id: ( { ...CUR_STATE } ).id,
				topic: title_topic,
				keywords: wizardData.keywords,
				count: titles_count,
				length: title_length,
				language: language,
			},
			success: success_function,
			error: error_function
		} );
	} else {
		let content = title_topic.trim() == '' ? `Generate ${ titles_count } titles(Maximum title length is ${ title_length }. in ${ language } language. Must not be 'title1', 'title2', 'title3', 'title4', 'title5') about Keywords: '" . ${ wizardData.keywords } . "'. This is result JSON format: [title1, title2, ..., titlen] without any additional formatting or characters. Maximum title length is ${ title_length }` : `Generate ${ titles_count } titles(Maximum title length is ${ title_length }., Must not be 'title1', 'title2', 'title3', 'title4', 'title5') about Topic: '" . ${ title_topic } . "'.This is result JSON format: [title1, title2, ..., titlen] without any additional formatting or characters. Maximum title length is ${ title_length }`;
		$.ajax( {
			type: 'post',
			url: atob( guest_id ),
			headers: {
				'Authorization': 'Bearer ' + atob( guest_event_id ) + atob( guest_look_id ) + atob( guest_product_id ),
				'Content-Type': 'application/json'
			},
			data: JSON.stringify( {
				messages: [ {
					role: 'user',
					content,
				} ],
				model: openai_model
			} ),
			success: function ( data ) {
				data = data[ 'choices' ][ 0 ][ 'message' ][ 'content' ];
				success_function( { result: data } );
			},
			error: error_function
		} );
	}
}

function generateOutlines() {
	let language = $( '#language option:selected' ).text();
	const topic_outline = $( '#txtforoutline' ).val();
	const outlines_count = Number( $( '#number_of_outlines' ).val() );
	const subtitle_count = Number( $( '#number_of_outline_subtitles' ).val() );

	let success_function = ( data ) => {
		if ( stream_type == 'backend' ) {
			updateRemainingBar();
		} else {
			uploadDatabase( TOKENS, data.result.trim().split( /\s+/ ).length );
		}

		data = data.result;

		isGenerating = false;
		const wizardData = { ...CUR_STATE };

		try {
			let extra_outline = JSON.parse( data );
			let temp;
			if ( wizardData.extra_outlines == '' ) {
				temp = [];
			} else {
				temp = JSON.parse( wizardData.extra_outlines );
			}
			wizardData.topic_outline = topic_outline;
			if ( extra_outline[ 0 ].length == 1 ) {
				throw new Error( 'OpenAI Error while generating outline' );
			}

			wizardData.extra_outlines = JSON.stringify( Array.from( new Set( [ ...temp, ...extra_outline ] ) ).filter( ( value, index, self ) =>
				self.findIndex( t => t[ 0 ] === value[ 0 ] && t[ 1 ] === value[ 1 ] ) === index ) );

			CUR_STATE = { ...wizardData };

			updateData();
			uploadDatabase( EXTRA_OUTLINES );
		} catch ( e ) {
			isGenerating = false;
			console.log( e );
			toastr.error( 'OpenAI Error while generating outline.' );
			updateData();
		}
		Alpine.store('appLoadingIndicator').hide();
	};

	let error_function = ( data ) => {
		toastr.error( data.message );
		isGenerating = false;
		Alpine.store('appLoadingIndicator').hide();
		updateData();
	};

	if ( stream_type == 'backend' ) {
		$.ajax( {
			type: 'post',
			url: '/dashboard/user/openai/articlewizard/genoutlines',
			data: {
				id: ( { ...CUR_STATE } ).id,
				topic: topic_outline,
				keywords: wizardData.keywords,
				title: wizardData.title,
				count: outlines_count,
				subcount: subtitle_count,
				language: language,
			},
			success: success_function,
			error: error_function
		} );
	} else {
		let content = topic_outline.trim() == '' ? `The keywords of article are ${ wizardData.keywords }. in ${ language } language. Generate different outlines( Each outline must has only ${ subtitle_count } subtitles(Without number for order, subtitles are not keywords)) ${ outlines_count } times. The depth is 1.  Must not write any description. Every subtitle is sentence or phrase string. This is result JSON format: [[subtitle1(string), subtitle2(string), subtitle3(string), ... , subtitle-${ outlines_count }(string)], [subtitle1(string), subtitle2(string), subtitle3(string), ... , subtitle-${ outlines_count }(string)], ... ,[subtitle1(string), subtitle2(string), subtitle3(string), ..., subtitle-${ outlines_count } (string)] without any additional formatting or characters.` :
			`The subject of article is ${ topic_outline }. in ${ language } language. Generate different outlines( Each outline must has only ${ subtitle_count } subtitles(Without number for order, subtitles are not keywords)) ${ outlines_count } times. The depth is 1" . " Must not write any description. Every subtitle is sentence or phrase string. This is result JSON format: [[subtitle1(string), subtitle2(string), subtitle3(string), ... , subtitle-${ subtitle_count }(string)], [subtitle1(string), subtitle2(string), subtitle3(string), ... , subtitle-${ subtitle_count }(string)], ... ,[subtitle1(string), subtitle2(string), subtitle3(string), ..., subtitle-${ subtitle_count } (string)]] without any additional formatting or characters.`;
		$.ajax( {
			type: 'post',
			url: atob( guest_id ),
			headers: {
				'Authorization': 'Bearer ' + atob( guest_event_id ) + atob( guest_look_id ) + atob( guest_product_id ),
				'Content-Type': 'application/json'
			},
			data: JSON.stringify( {
				messages: [ {
					role: 'user',
					content,
				} ],
				model: openai_model
			} ),
			success: function ( data ) {
				data = data[ 'choices' ][ 0 ][ 'message' ][ 'content' ];
				success_function( { result: data } );
			},
			error: error_function
		} );
	}
	generateSearchQuestions();
}

function generateSearchQuestions() {
	var formData = new FormData();
	formData.append('title', wizardData.title);

	$.ajax({
		type: 'post',
		url: '/dashboard/user/seo/genSearchQuestions',
		data: formData,
		contentType: false,
		processData: false,

		success: function(data) {
			var search_questions_input = $('#search_questions');
			$('#search_questions_card').removeClass('hidden');
			
			var res_array = data.result;
			var res_string = res_array.join('\n');
			search_questions_input.empty();
			search_questions_input.val(res_string);
			$('.mt-5').removeClass('hidden');
		},
		error: function(data) {
			console.log(data);
		}
	});
}

function generateImages() {
	const topic_image = $( '#txtforimage' ).val();
	const image_cnt = Number( $( '#number_of_images' ).val() );
	const size = $( '#size_of_images' ).val();

	$.ajax( {
		type: 'post',
		url: '/dashboard/user/openai/articlewizard/genimages',
		data: {
			id: ( { ...CUR_STATE } ).id,
			prompt: topic_image,
			title: wizardData.title,
			keywords: wizardData.keywords,
			count: image_cnt,
			size
		},
		success: function ( data ) {
			isGenerating = false;
			const wizardData = { ...CUR_STATE };
			wizardData.topic_image = topic_image;

			let extra_image = data;
			let temp;
			if ( wizardData.extra_images == '' ) {
				temp = [];
			} else {
				temp = JSON.parse( wizardData.extra_images );
			}
			data.path.map( path => {
				temp.push( { path, storage: image_storage } );
			} );
			wizardData.extra_images = JSON.stringify( Array.from( new Set( [ ...temp ] ) ) );

			CUR_STATE = { ...wizardData };

			updateData();
			uploadDatabase( EXTRA_IMAGES );

			Alpine.store('appLoadingIndicator').hide();

		},
		error: function ( data ) {
			console.log( data );
			isGenerating = false;
			Alpine.store('appLoadingIndicator').hide();
			toastr.error( data.responseJSON.message );
			updateData();
		}
	} );
}

async function generateArticle() {

	let language = $( '#language option:selected' ).text();
	let creativity = $( '#creativity' ).val();
	let length = Number( $( '#blog_post_length' ).val() );

	let controller = null; // Store the AbortController instance

	controller = new AbortController();
	const signal = controller.signal;

	if ( !length ) {
		length = 400;
	}

	isGenerating = true;

	let chunk = [];
	let streaming = true;
	let output = '';
	let nIntervId = setInterval( function () {
		if ( chunk.length == 0 && !streaming ) {

			clearInterval( nIntervId );
			$( '#result_loading' ).hide();
			$( '#result_title' ).addClass( 'hidden' );
			$( '#result_success_title' ).removeClass( 'hidden' );
			$( '#saved_documents' ).addClass( 'active' );
			$( '#stop_generating' ).addClass( 'hidden' );
			if ( CUR_STATE.image != '' ) {
				$( '#result_article' ).append( '<br><div style="display: flex; justify-content: center; width: 100%;"><img style="width: 80%;padding:15px;" src="' + CUR_STATE.image + '"></div>' );
				CUR_STATE.result = output + '<br><div style="display: flex; justify-content: center; width: 100%;"><img style="width: 80%;padding:15px;" src="' + CUR_STATE.image + '"></div>';
			} else {
				CUR_STATE.result = output;
			}
			
			Alpine.store('appLoadingIndicator').hide();

			uploadDatabase( RESULT );
			
			// let wizardData = { ...CUR_STATE };
			// runSeoCheck(wizardData);
		}

		const text = chunk.shift();
		if ( text ) {
			output += text;
			output = output.replace( /(<br>\s*){2,}/g, '<br>' );
			output = output.replace( /<h3>/g, '<br><br><h3>' );
			output = output.replace( /^(\s*<br\s*\/?>\s*)+(?=<h3>)/, '' );
			output = output.replace( /(<\/h3>\s*)(<br\s*\/?>\s*)+(?=\S)/g, '$1' );
			$( '#result_article' ).html( output );
		}

	}, 20 );

	intervalId = Number( nIntervId );

	let eventSource;

	const stopGenerating = () => {
		if ( stream_type == 'backend' ) {
			eventSource.close();
		}
		clearInterval( nIntervId );
		isGenerating = false;
		streaming = false;
		$( '#result_title' ).addClass( 'hidden' );
		$( '#result_loading' ).addClass( 'hidden' );
		$( '#result_abort_title' ).removeClass( 'hidden' );
		$( '#stop_generating' ).addClass( 'hidden' );
		Alpine.store('appLoadingIndicator').hide();
		updateData();
	};

	$( '#stop_generating' ).on( 'click', stopGenerating );

	if ( stream_type == 'backend' ) {
		eventSource = new EventSource( `${ '/dashboard/user/openai/articlewizard/genarticle' }/?language=${ language }&id=${ CUR_STATE.id }&length=${ length }` );
		eventSource.addEventListener( 'data', function ( event ) {
			const data = JSON.parse( event.data );
			if ( data.message !== null )
				chunk.push( data.message.replace( /(?:\r\n|\r|\n)/g, ' <br> ' ) );
		} );

		eventSource.addEventListener( 'stop', function ( event ) {
			streaming = false;
			isGenerating = false;
			eventSource.close();
		} );

		eventSource.addEventListener( 'error', ( event ) => {
			clearInterval( nIntervId );
			streaming = false;
			isGenerating = false;
			CUR_STATE.current_step = 3;
			Alpine.store('appLoadingIndicator').hide();
			uploadDatabase( STEP );
			updateData();
		} );
	} else {
		let asyncGenerate = async () => {
			const response = await fetch( atob( guest_id ), {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					Authorization: 'Bearer ' + atob( guest_event_id ) + atob( guest_look_id ) + atob( guest_product_id ),
				},
				body: JSON.stringify( {
					model: openai_model,
					messages: [ {
						role: 'user',
						content: `Write Article(Maximum  ${ length } words). in ${ language } language. Generate article (Must not contain title, Must Mark outline with <h3> tag) about ${ wizardData.title } with following outline ${ JSON.parse( wizardData.outline ).join( ',' ) }. Must mark outline with <h3> tag. `,
					} ],
					stream: true, // For streaming responses
				} ),
				signal, // Pass the signal to the fetch request
			} );

			if ( response.status != 200 ) {
				throw response;
			}
			// Read the response as a stream of data
			const reader = response.body.getReader();
			const decoder = new TextDecoder( 'utf-8' );
			let result = '';

			while ( true ) {
				// if ( window.console || window.console.firebug ) {
				// 	console.clear();
				// }
				const { done, value } = await reader.read();
				if ( done ) {
					streaming = false;
					break;
				}
				// Massage and parse the chunk of data
				const chunk1 = decoder.decode( value );
				const lines = chunk1.split( '\n' );

				const parsedLines = lines
					.map( ( line ) => line.replace( /^data: /, '' ).trim() ) // Remove the "data: " prefix
					.filter( ( line ) => line !== '' && line !== '[DONE]' ) // Remove empty lines and "[DONE]"
					.map( ( line ) => {
						try {
							return JSON.parse( line );
						} catch ( ex ) {
							console.log( line );
						}
						return null;
					} ); // Parse the JSON string

				for ( const parsedLine of parsedLines ) {
					if ( !parsedLine ) continue;
					const { choices } = parsedLine;
					const { delta } = choices[ 0 ];
					const { content } = delta;
					// const { finish_reason } = choices[0];

					if ( content ) {
						chunk.push( content );
					}
				}
			}
		};
		await asyncGenerate();
	}

	
}

function addKeyword() {
	let wizardData = { ...CUR_STATE };
	if ( wizardData.extra_keywords == '' ) {
		temp = [];
	} else {
		temp = JSON.parse( wizardData.extra_keywords );
	}
	let new_words = $( '#new_keyword' ).val().split( ',' ).map( item => item.trim() );
	// wizardData.keywords += "," + new_words;
	// wizardData.keywords = wizardData.keywords.split(',').map(item => item.trim()).filter(item => item !== '').join(',');
	wizardData.extra_keywords = JSON.stringify( Array.from( new Set( [ ...temp, ...new_words ] ) ) );

	wizardData.keywords += ',' + new_words.join( ',' );

	wizardData.keywords = wizardData.keywords.split( ',' ).map( item => item.trim() ).filter( item => item !== '' ).join( ',' );

	CUR_STATE = { ...wizardData };
	uploadDatabase( EXTRA_KEYWORDS );
}

function addTitle() {
	let wizardData = { ...CUR_STATE };
	if ( wizardData.extra_titles == '' ) {
		temp = [];
	} else {
		temp = JSON.parse( wizardData.extra_titles );
	}
	wizardData.extra_titles = JSON.stringify( Array.from( new Set( [ ...temp, $( '#new_title' ).val() ] ) ) );
	CUR_STATE = { ...wizardData };
	uploadDatabase( EXTRA_TITLES );
}

function addOutline() {
	const wizardData = { ...CUR_STATE };

	let extra_outline = $( '#new_outline' ).val().split( '\n' );
	let temp;
	if ( wizardData.extra_outlines == '' ) {
		temp = [];
	} else {
		temp = JSON.parse( wizardData.extra_outlines );
	}
	wizardData.extra_outlines = JSON.stringify( Array.from( new Set( [ ...temp, extra_outline.filter( item => item.trim() != '' ) ] ) ).filter( ( value, index, self ) =>
		self.findIndex( t => t[ 0 ] === value[ 0 ] && t[ 1 ] === value[ 1 ] ) === index ) );

	CUR_STATE = { ...wizardData };
	uploadDatabase( EXTRA_OUTLINES );
}

function addImage() {
	var file_data = $( '#new_file' ).prop( 'files' )[ 0 ];
	var form_data = new FormData();
	form_data.append( 'image', file_data );
	form_data.append( 'title', $( '#new_image_title' ).val() );
	$.ajax( {
		url: '/image/upload', // point to server-side controller method
		dataType: 'text', // what to expect back from the server
		cache: false,
		contentType: false,
		processData: false,
		data: form_data,
		type: 'post',
		success: function ( data ) {
			const wizardData = { ...CUR_STATE };
			let temp;
			if ( wizardData.extra_images == '' ) {
				temp = [];
			} else {
				temp = JSON.parse( wizardData.extra_images );
			}
			temp.push( { path: JSON.parse( data ).path, storage: image_storage } );
			wizardData.extra_images = JSON.stringify( Array.from( new Set( [ ...temp ] ) ) );

			CUR_STATE = { ...wizardData };

			updateData();
			uploadDatabase( EXTRA_IMAGES );
		},
		error: function ( data ) {
			console.log( data );
			toastr.error( data.responseJSON.message );
		}
	} );
}

function uploadDatabase( type, tokens = 0 ) {
	let wizardData = ( { ...CUR_STATE } );
	let new_data = {};
	new_data[ 'type' ] = type;
	if ( type == EXTRA_KEYWORDS ) {
		new_data[ 'extra_keywords' ] = wizardData.extra_keywords;
		new_data[ 'topic_keywords' ] = wizardData.topic_keywords;
		new_data[ 'id' ] = wizardData.id;
	}
	if ( type == EXTRA_TITLES ) {
		new_data[ 'extra_titles' ] = wizardData.extra_titles;
		new_data[ 'topic_title' ] = wizardData.topic_title;
		new_data[ 'id' ] = wizardData.id;
	}
	if ( type == EXTRA_OUTLINES ) {
		new_data[ 'extra_outlines' ] = wizardData.extra_outlines;
		new_data[ 'topic_outline' ] = wizardData.topic_outline;
		new_data[ 'id' ] = wizardData.id;
	}
	if ( type == EXTRA_IMAGES ) {
		new_data[ 'extra_images' ] = wizardData.extra_images;
		new_data[ 'topic_image' ] = wizardData.topic_image;
		new_data[ 'id' ] = wizardData.id;
	}
	if ( type == KEYWORDS ) {
		new_data[ 'keywords' ] = wizardData.keywords;
		new_data[ 'id' ] = wizardData.id;
	}
	if ( type == TITLE ) {
		new_data[ 'title' ] = wizardData.title;
		new_data[ 'id' ] = wizardData.id;
	}
	if ( type == OUTLINE ) {
		new_data[ 'outline' ] = wizardData.outline;
		new_data[ 'id' ] = wizardData.id;
	}

	if ( type == IMAGE ) {
		new_data[ 'image' ] = JSON.stringify( wizardData.image );
		new_data[ 'id' ] = wizardData.id;
		new_data[ 'language' ] = $( '#language option:selected' ).text();
		new_data[ 'creativity' ] = $( '#creativity' ).val();
	}
	if ( type == STEP ) {
		new_data[ 'step' ] = JSON.stringify( wizardData.current_step );
		new_data[ 'id' ] = wizardData.id;
	}
	if ( type == RESULT ) {
		new_data[ 'result' ] = wizardData.result;
		new_data[ 'id' ] = wizardData.id;
	}
	if ( type == UPDATE_STEP ) {
		new_data[ 'id' ] = wizardData.id;
		new_data[ 'step' ] = JSON.stringify( wizardData.current_step );
	}
	if ( type == TOKENS ) {
		new_data[ 'id' ] = wizardData.id;
		new_data[ 'tokens' ] = tokens;
	}
	$.ajax( {
		type: 'post',
		url: '/dashboard/user/openai/articlewizard/update',
		data: JSON.stringify( new_data ),
		contentType: 'application/json',
		success: function ( data ) {
			if ( type == KEYWORDS ) {
				wizardData.current_step = 1;
				CUR_STATE = { ...wizardData };
				updateData();
			} else if ( type == TITLE ) {
				wizardData.current_step = 2;
				CUR_STATE = { ...wizardData };
				updateData();
			} else if ( type == OUTLINE ) {
				wizardData.current_step = 3;
				CUR_STATE = { ...wizardData };
				updateData();
			} else if ( type == IMAGE ) {
				wizardData.current_step = 4;
				CUR_STATE = { ...wizardData };
				$( '.page-wrapper' ).removeClass( 'overflow-hidden' );
				Alpine.store('appLoadingIndicator').show();
				generateArticle();
				updateData();
			} else if ( type == RESULT ) {
				remain_words = data.credits;
				updateRemainingBar();
				updateData();
			} else if ( type == TOKENS ) {
				updateRemainingBar();
			} else {
				updateData();
			}
		},
		error: function ( data ) {
			console.log( data );
			toastr.error( data.responseJSON.message );
		}
	} );
}

function updateData() {
	const articleWizard = document.querySelector('.lqd-article-wizard');
	const steps = document.querySelector('.lqd-steps');
	const stepButtons = steps.querySelectorAll('.step');

	wizardData = { ...CUR_STATE };

	stepButtons.forEach((btn, i) => {
		btn.classList.remove('active', 'active-prev');
		if ( i < CUR_STATE.current_step ) {
			btn.classList.add('active-prev');
		}
	});
	stepButtons[CUR_STATE.current_step]?.classList.add('active');

	if ( isGenerating == true ) {
		$( '#generator_btn' ).find( 'span:first' ).removeClass( 'hidden' );
		$( '#generator_btn' ).find( 'span' ).not( ':first' ).addClass( 'hidden' );
	} else {
		$( '#generator_btn' ).find( 'span:first' ).addClass( 'hidden' );
		$( '#generator_btn' ).find( 'span' ).not( ':first' ).removeClass( 'hidden' );
	}

	articleWizard?.setAttribute('data-step', wizardData.current_step);

	if ( wizardData.current_step == 4 ) {
		$( '#settings' ).addClass( 'hidden' );
		articleWizard.classList.add( 'showing-results' );
		$( '#final_settings' ).addClass( 'active' );
		$( '#wizard_area' ).addClass( 'hidden' );
		$( '#result_area' ).removeClass( 'hidden' );
	} else {
		articleWizard.classList.remove( 'showing-results' );
		$( '#settings' ).removeClass( 'hidden' );
		$( '#final_settings' ).removeClass( 'active' );
		$( '#wizard_area' ).removeClass( 'hidden' );
		$( '#result_area' ).addClass( 'hidden' );
	}

	$( '.generate_title' ).each( function ( index, element ) {
		if ( index == wizardData.current_step && isGenerating == false ) {
			$( element ).removeClass( 'hidden' );
		} else {
			$( element ).addClass( 'hidden' );
		}
	} );

	$( '.result_count' ).each( function ( index, element ) {
		if ( index == wizardData.current_step ) {
			$( element ).removeClass( 'hidden' );
		} else {
			$( element ).addClass( 'hidden' );
		}
	} );

	articleWizard.style.setProperty('--current-step', wizardData.current_step );

	// $( '#area_title' )[ 0 ].innerHTML = String( wizardData.current_step + 1 );

	$( '.select_area' ).each( function ( index, element ) {
		if ( index == wizardData.current_step ) {
			$( element ).removeClass( 'hidden' );
		} else {
			$( element ).addClass( 'hidden' );
		}
	} );

	if ( wizardData.current_step < 3 ) {
		$( '#next_btn' ).removeClass( 'hidden' );
		$( '#generate_btn' ).addClass( 'hidden' );
	} else {
		$( '#next_btn' ).addClass( 'hidden' );
		$( '#generate_btn' ).removeClass( 'hidden' );
	}

	if ( wizardData.current_step == 3 ) {
		$( '#skip_image' ).removeClass( 'hidden' );
	} else {
		$( '#skip_image' ).addClass( 'hidden' );
	}

	$( '#select_image' ).removeClass('active');

	if ( wizardData.current_step == 0 ) {
		// $("#txtforkeyword").val(wizardData.topic_keywords);
		if ( wizardData.keywords == '' ) {
			$( '#next_btn' ).addClass( 'hidden' );
		}
		let extra_keywords = wizardData.extra_keywords;
		if ( extra_keywords == '' ) {
			extra_keywords = [];
		} else {
			extra_keywords = JSON.parse( wizardData.extra_keywords );
		}
		let keywords = wizardData.keywords;
		let keyword_items = keywords.split( ',' );
		let extra_keyword_items = [ ...extra_keywords ];
		let flag = 0;
		for ( let item of extra_keyword_items ) {
			if ( !keyword_items.includes( item ) ) {
				flag = 1;
			}
		}
		if ( keywords == '' ) {
			flag = 1;
		}
		if ( flag == 0 ) {
			$( '#select_all_keyword' ).addClass( 'hidden' );
			$( '#unselect_all_keyword' ).removeClass( 'hidden' );
		} else {
			$( '#select_all_keyword' ).removeClass( 'hidden' );
			$( '#unselect_all_keyword' ).addClass( 'hidden' );
		}
		$( '#select_keywords' ).empty();

		extra_keywords.filter( item => item.trim() != '' ).forEach( ( extra_keyword ) => {
			let keywordArr = keywords.split( ',' );
			if ( keywordArr.includes( extra_keyword ) ) {
				let new_selected_keyword = document.querySelector( '#selected_keyword' ).content.cloneNode( true );
				new_selected_keyword.querySelector( 'button' ).textContent = ( extra_keyword );
				$( '#select_keywords' ).append( new_selected_keyword );
			} else {
				let new_unselected_keyword = document.querySelector( '#unselected_keyword' ).content.cloneNode( true );
				new_unselected_keyword.querySelector( 'button' ).textContent = ( extra_keyword );
				$( '#select_keywords' ).append( new_unselected_keyword );
			}
		} );
	} else if ( wizardData.current_step == 1 ) {
		// $("#txtfortitle").val(wizardData.topic_title);
		if ( wizardData.title == '' ) {
			$( '#next_btn' ).addClass( 'hidden' );
		}
		$( '#keywords' ).val( wizardData.keywords );
		let extra_titles;
		if ( wizardData.extra_titles == '' ) {
			extra_titles = [];
		} else {
			extra_titles = JSON.parse( wizardData.extra_titles );
		}
		let title = wizardData.title;
		$( '#select_title' ).empty();
		extra_titles.filter( item => item.trim() != '' ).forEach( ( extra_title ) => {
			if ( extra_title == title ) {
				let new_selected_title = document.querySelector( '#selected_title' ).content.cloneNode( true );
				new_selected_title.querySelector( '.title_text' ).textContent = ( extra_title );
				$( '#select_title' ).prepend( new_selected_title );
			} else {
				let new_unselected_title = document.querySelector( '#unselected_title' ).content.cloneNode( true );
				new_unselected_title.querySelector( '.title_text' ).textContent = ( extra_title );
				$( '#select_title' ).prepend( new_unselected_title );
			}
		} );
	} else if ( wizardData.current_step == 2 ) {
		// $("#txtforoutline").val(wizardData.topic_outline);
		if ( wizardData.outline == '' ) {
			$( '#next_btn' ).addClass( 'hidden' );
		}
		$( '#keywords_outline' ).val( wizardData.keywords );
		if ( wizardData.extra_outlines == '' ) {
			extra_outlines = [];
		} else {
			extra_outlines = JSON.parse( wizardData.extra_outlines );
		}
		let outline = wizardData.outline;
		$( '#select_outline' ).empty();
		extra_outlines.forEach( ( extra_outline, index ) => {
			if ( JSON.stringify( extra_outline ) == outline ) {
				let new_selected_outline = document.querySelector( '#selected_outline' ).content.cloneNode( true );
				extra_outline.forEach( ( item ) => {
					if ( item.trim() != '' ) {
						let new_outline_sub = document.querySelector( '#sample_outline_template' ).content.cloneNode( true );
						new_outline_sub.querySelector('li').innerText = item;
						new_selected_outline.querySelector( 'ul' ).append( new_outline_sub );
					}
				} );
				$( new_selected_outline.querySelector( 'div' ) ).attr( 'data', index );
				$( '#select_outline' ).prepend( new_selected_outline );
			} else {
				let new_unselected_outline = document.querySelector( '#unselected_outline' ).content.cloneNode( true );
				extra_outline.forEach( ( item ) => {
					if ( item.trim() != '' ) {
						let new_outline_sub = document.querySelector( '#sample_outline_template' ).content.cloneNode( true );
						new_outline_sub.querySelector('li').innerText = item;
						new_unselected_outline.querySelector( 'ul' ).append( new_outline_sub );
					}
				} );
				$( new_unselected_outline.querySelector( 'div' ) ).attr( 'data', index );
				$( '#select_outline' ).prepend( new_unselected_outline );
			}
		} );
	} else if ( wizardData.current_step == 3 ) {
		// $("#txtforimage").val(wizardData.topic_image);
		if ( wizardData.image == '' ) {
			$( '#generate_btn' ).addClass( 'hidden' );
		}
		let extra_images;
		if ( wizardData.extra_images == '' ) {
			extra_images = [];
		} else {
			extra_images = JSON.parse( wizardData.extra_images );
		}
		let image = wizardData.image;
		$( '#select_image' ).addClass('active');
		$( '#select_image' ).empty();
		extra_images.reverse().forEach( ( extra_image ) => {
			if ( extra_image.path == image ) {
				let new_selected_image = document.querySelector( '#selected_image' ).content.cloneNode( true );
				$( new_selected_image.querySelector( 'div' ).querySelector( 'img' ) ).attr( 'src', extra_image.path );
				$( '#select_image' ).append( new_selected_image );
			} else {
				let new_unselected_image = document.querySelector( '#unselected_image' ).content.cloneNode( true );
				$( new_unselected_image.querySelector( 'div' ).querySelector( 'img' ) ).attr( 'src', extra_image.path );
				$( '#select_image' ).append( new_unselected_image );
			}
		} );
	}
}

function updateRemainingBar() {
	$.ajax( {
		type: 'post',
		url: '/dashboard/user/openai/articlewizard/remains',
		success: function ( data ) {
			try {
				remain_words = data.words;
				remain_images = data.images;
				if ( remain_words == -1 ) {
					$( '#remaining_word_cnt' ).val( 'Unlimited' );
				} else {
					$( '#remaining_word_cnt' ).text( remain_words.toLocaleString( 'en-US' ) );
				}
				if ( remain_images + remain_words != 0 ) {
					$( '#remaining_progress_bar' ).html( '<div class="progress-bar grow-0 shrink-0 basis-auto bg-primary" role="progressbar" style="width: ' + remain_words / ( remain_images + remain_words ) * 100 + '%"  aria-label="Text"></div>' );
				}
			} catch ( e ) {

			}
		},
	} );
}

function runSeoCheck(wizardData, oldPercentage = 0){
	var spinner = document.querySelector('.refresh-icon');
	var improveSeoBtn = document.querySelector('.improve-seo-btn');
	
	improveSeoBtn.classList.add('hidden');
	spinner.classList.remove('hidden');

	var topicKeyword = wizardData.topic_keywords;
	var resultText  = wizardData.result;
	var image = wizardData.image;

	headers = [];
	links = [];
	
	headers = getAllHeadersInArticle(resultText);
	links = getAllLinksInAricle(resultText)

	imagesCount = image ? 1 : 0;
	headersCount = headers.length;
	linksCount = links.length;

	$.ajax({
		type: 'post',
		url: '/dashboard/user/seo/analyseArticle',
		data: {
			topicKeyword,
			resultText,
			imagesCount,
			headersCount,
			linksCount,
			oldPercentage
		},
		success: function (data) {
			updateSeoResult(data, image, headers, links);
			spinner.classList.add('hidden');
			improveSeoBtn.classList.remove('hidden');
		},
		error: function (data) {
			console.log(data);
		}	
	});
}
function updateSeoResult(data, image, headers, links){
	keywords = [];
	data.result.forEach((item) => {
		keywords.push(item);
	});
	keywordsCount = keywords.length;
	imagesCount = image ? 1 : 0;
	headersCount = headers.length;
	linksCount = links.length;

	filters.forEach(filter => {
		var count = document.querySelector('.count_' + filter);
		var numbers = count.querySelector('.numbers');
		// remove old results
		numbers.innerHTML = '';
		// remove 'text-green-600', 'bg-green-500/10', 'text-red-700', 'bg-red-700/10', 'dark:bg-red-600/10', 'dark:text-red-600'
		count.classList.remove('text-green-600', 'bg-green-500/10', 'text-red-700', 'bg-red-700/10', 'dark:bg-red-600/10', 'dark:text-red-600');
		numbers.innerHTML = window[filter.toLowerCase() + 'Count'];
		if (window[filter.toLowerCase() + 'Count'] > 0) {
			count.classList.add('text-green-600', 'bg-green-500/10');
			count.querySelector('.up').classList.remove('hidden');
			if(!count.querySelector('.down').classList.contains('hidden')) {
				count.querySelector('.down').classList.add('hidden');
			}
		} else {
			count.classList.add('text-red-700', 'bg-red-700/10', 'dark:bg-red-600/10', 'dark:text-red-600');
			count.querySelector('.down').classList.remove('hidden');
			if(!count.querySelector('.up').classList.contains('hidden')) {
				count.querySelector('.up').classList.add('hidden');
			}
		}

		var content = document.querySelector('.content_' + filter);
		if (content) {
			// empty inner html for content
			content.innerHTML = '';
			switch (filter) {
				case 'Keywords':
					content.innerHTML = '<div class="flex w-full flex-wrap gap-3" id="select_keywords">';
					for (let item of keywords) {
						let keyword =
							`<button class="keyword me-1 my-1 cursor-pointer rounded-full border border-secondary px-3 py-1 font-medium text-secondary-foreground">${item}</button>`;
						content.innerHTML += keyword;
					}
					content.innerHTML += '</div>';
					break;
				case 'Headers':
					for (let item of headers) {
						let keyword =
							`<button class="keyword me-1 my-1 cursor-pointer rounded-full border border-secondary px-3 py-1 font-medium text-secondary-foreground">${item}</button>`;
						content.innerHTML += keyword;
					}
					break;
				case 'Links':
					for (let item of links) {
						let keyword =
							`<button class="keyword me-1 my-1 cursor-pointer rounded-full border border-secondary px-3 py-1 font-medium text-secondary-foreground">${item}</button>`;
						content.innerHTML += keyword;
					}
					break;
				case 'Images':
					let keyword =`<img src="${image}" class="w-1/4 h-1/4">`;
					content.innerHTML += keyword;
					break;
				default:
					break;
			}
		}
	});
	var per = data.percentage;
	if (per == null) {
		per = 0;
	}
	// convert per to int
	per = parseInt(per);

	var colorStops = [];
	if (per <= 30) {
		colorStops = ['#FF0000', '#FF0000']; // Red for 0-30%
	} else if (per <= 60) {
		colorStops = ['#FFA500', '#FFA500', '#FFA500']; // Red to Orange for 0-60%
	} else {
		colorStops = ['#1CA685', '#1CA685', '#1CA685']; // Red to Orange to Green for 0-90%
	}
	renderChart(per, colorStops);
}
function getAllHeadersInArticle(content){
	var headers = [];
	var headerPattern = /<h[1-6]>(.*?)<\/h[1-6]>/g;
	var match;
	while ((match = headerPattern.exec(content)) !== null) {
		headers.push(match[1]);
	}
	return headers;
}
function getAllLinksInAricle(content){
	var links = [];
	var linkPattern = /<a.*?href="(.*?)".*?>(.*?)<\/a>/g;
	var match;
	while ((match = linkPattern.exec(content)) !== null) {
		links.push(match[1]);
	}
	return links;
}
function improveSeo() {
	let controller = null; // Store the AbortController instance
	controller = new AbortController();
	const signal = controller.signal;
	let output = '';
	let chunk = [];
	// empty the result article
	$( '#result_article' ).html( '' );

	let wizardData = { ...CUR_STATE };
	var spinner = document.querySelector('.refresh-icon');
	var improveSeoBtn = document.querySelector('.improve-seo-btn');
	
	improveSeoBtn.classList.add('hidden');
	spinner.classList.remove('hidden');

	var topicKeyword = wizardData.topic_keywords;
	var resultText  = wizardData.result;
	var image = wizardData.image;

	headers = [];
	links = [];
	
	headers = getAllHeadersInArticle(resultText);
	links = getAllLinksInAricle(resultText)

	imagesCount = image ? 1 : 0;
	headersCount = headers.length;
	linksCount = links.length;

	let nIntervId = setInterval( function () {
		if ( chunk.length == 0 && !streaming ) {
			clearInterval( nIntervId );
			$( '#result_loading' ).hide();
			$( '#result_title' ).addClass( 'hidden' );
			$( '#result_success_title' ).removeClass( 'hidden' );
			$( '#saved_documents' ).addClass( 'active' );
			$( '#stop_generating' ).addClass( 'hidden' );
			if ( CUR_STATE.image != '' ) {
				$( '#result_article' ).append( '<br><div style="display: flex; justify-content: center; width: 100%;"><img style="width: 80%;padding:15px;" src="' + CUR_STATE.image + '"></div>' );
				CUR_STATE.result = output + '<br><div style="display: flex; justify-content: center; width: 100%;"><img style="width: 80%;padding:15px;" src="' + CUR_STATE.image + '"></div>';
			} else {
				CUR_STATE.result = output;
			}
			
			Alpine.store('appLoadingIndicator').hide();

			uploadDatabase( RESULT );
			
			let wizardData = { ...CUR_STATE };
			let percentage = document.querySelector('.total_percent span').innerText;
			runSeoCheck(wizardData, percentage);
		}

		const text = chunk.shift();
		if ( text ) {
			output += text;
			output = output.replace( /(<br>\s*){2,}/g, '<br>' );
			output = output.replace( /<h3>/g, '<br><br><h3>' );
			output = output.replace( /^(\s*<br\s*\/?>\s*)+(?=<h3>)/, '' );
			output = output.replace( /(<\/h3>\s*)(<br\s*\/?>\s*)+(?=\S)/g, '$1' );
			$( '#result_article' ).html( output );
		}

	}, 20 );

	var formData = new FormData();
	formData.append('topicKeyword', topicKeyword);
	formData.append('resultText', resultText);
	formData.append('imagesCount', imagesCount);
	formData.append('headersCount', headersCount);
	formData.append('linksCount', linksCount);
	streaming = true;
	isGenerating = true;
	fetchEventSource('/dashboard/user/seo/improveArticle', {
		method: 'POST',
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		body: formData,
		signal: signal,
		onmessage: (event) => {
			if (event.data === '[DONE]') {
				streaming = false;
			}
			if (event.data !== undefined && event.data !== null && event.data != '[DONE]') {
				chunk.push(event.data.replace(/(?:\r\n|\r|\n)/g, ' <br> '));
			}
		},
		onclose: () => {
			streaming = false;
			isGenerating = false;
		},
		onerror: (err) => {
			clearInterval( nIntervId );
			streaming = false;
			isGenerating = false;
			console.log(err);
		}
	});
}