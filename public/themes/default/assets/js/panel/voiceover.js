function formatTime( time ) {
	var minutes = Math.floor( time / 60 );
	var seconds = Math.floor( time % 60 );
	return minutes + ':' + ( seconds < 10 ? '0' : '' ) + seconds;
}

/**
 *
 * @param {HTMLElement} el vanilla js element. not jquery
 */
function generateWaveForm( el ) {

	var audioUrl = el.getAttribute( 'data-audio' );
	var container = el.querySelector( '.audio-preview' );
	var playButton = el.querySelector( 'button' );
	var timeData = el.querySelector( 'span' );

	if ( !audioUrl || audioUrl === '' ) {
		return;
	}

	// empty the element first
	while ( container.firstElementChild ) {
		container.firstElementChild.remove();
	}

	var waveform = WaveSurfer.create( {
		container: container,
		waveColor: '#bcbac8',
		progressColor: '#320580',
		cursorWidth: 0,
		barWidth: 1,
		interact: true,
		autoCenter: false,
		hideScrollbar: true,
		height: 22,
	} );

	waveform.load( audioUrl );

	waveform.on( 'ready', function () {
		var duration = waveform.getDuration();
		timeData.textContent = formatTime( duration );
	} );

	waveform.on( 'audioprocess', function () {
		var currentTime = waveform.getCurrentTime();
		timeData.textContent = formatTime( currentTime );
	} );
	waveform.on( 'play', function () {
		playButton.classList.add( 'is-playing' );
	} );
	waveform.on( 'pause', function () {
		playButton.classList.remove( 'is-playing' );
	} );

	playButton.addEventListener( 'click', function () {
		waveform.playPause();
	} );

}

document.addEventListener( 'DOMContentLoaded', function () {
	var audioElements = document.querySelectorAll( '.data-audio' );
	audioElements.forEach( generateWaveForm );
} );