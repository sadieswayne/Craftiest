import { Player } from './player.js';
import { Recorder } from './recorder.js';
import { LowLevelRTClient } from 'rt-client';
import { Alpine } from '~vendor/livewire/livewire/dist/livewire.esm';

Alpine.store('realtimeChatStatus', {
	active: false,
	conversationStarted: false,

	setActive(value) {
		this.active = value;

		this.onActiveChange();
	},

	setConversationStarted(value) {
		this.conversationStarted = value;

		this.onConversationStartedChange();
	},

	onActiveChange() {
		document.querySelectorAll('.lqd-realtime-chat-button').forEach(button => button.classList.toggle('active', this.active));
		document.querySelector('.lqd-audio-vis-wrap')?.classList?.toggle('active', this.active);
	},

	onConversationStartedChange() {
		document.querySelectorAll('.lqd-realtime-chat-button').forEach(button => {
			button.classList.toggle('conversation-started', this.conversationStarted);
			button.classList.toggle('conversation-not-started', !this.conversationStarted);
		});
	},
});

export default (prompt1, prompt2, prompt3) => ({
	apiKey: '',
	recordingActive: false,
	buffer: new Uint8Array(),
	/** @type {LowLevelRTClient} */
	wsConnection: null,
	/** @type {Recorder} */
	audioRecorder: null,
	/** @type {Player} */
	audioPlayer: null,
	/** @type {'idle' | 'loading' | 'recording' | 'playing'} */
	activeVisulaizer: 'idle',
	/** @type {HTMLElement} */
	audioVisWrap: null,
	/** @type {HTMLElement} */
	audioVisBars: null,
	/** @type {HTMLElement} */
	audioVisDotWrap: null,
	/** @type {HTMLElement} */
	audioVisLoader: null,
	conversationArea: document.querySelector('.conversation-area'),
	chatsContainer: document.querySelector('.chats-container'),
	/** @type {HTMLTemplateElement} */
	userBubbleTemplate: document.querySelector('#chat_user_bubble'),
	/** @type {HTMLTemplateElement} */
	aiBubbleTemplate: document.querySelector('#chat_ai_bubble'),
	lastAiBubble: null,
	lastUserBubble: null,
	lastUserQuestion: '',
	lastAiResponse: '',
	lastResponseSaved: false,

	init() {
		this.audioVisWrap = document.querySelector('.lqd-audio-vis-wrap');
		this.audioVisBars = this.audioVisWrap?.querySelectorAll('.lqd-audio-vis-bar');
		this.audioVisDotWrap = this.audioVisWrap?.querySelector('.lqd-audio-vis-dot-wrap');
		this.audioVisLoader = this.audioVisWrap?.querySelector('.lqd-audio-vis-loader');

		this.processAudioRecordingBuffer = this.processAudioRecordingBuffer.bind(this);
	},
	async start() {
		// if(window.isDemo){
		if (false) {
			toastr.error('This feature is disabled in the demo version.');
			return;
		} else {
			if (Alpine.store('realtimeChatStatus').isActive) return;

			Alpine.store('realtimeChatStatus').setActive(true);

			this.switchVisualizers('waiting');

			this.wsConnection = new LowLevelRTClient(
				{ key: atob(prompt1) + atob(prompt2) + atob(prompt3) },
				{ model: 'gpt-4o-realtime-preview-2024-12-17' }
			);

			try {
				await this.wsConnection.send(this.createConfigMessage());
			} catch (error) {
				this.stop();
				console.error('Error sending initial config message:', error);
				this.appendToChatBubble('ai', '[Connection error]: Unable to send initial config message. Please check your endpoint and authentication details.');
				return;
			}

			await Promise.all([ this.startRecorder(), this.startPlayer() ])
				.then(() => {
					this.handleRealtimeMessages();

					this.startBarsVisualizer();
					this.startDotVisualizer();

					this.switchVisualizers('idle');
				})
				.catch(error => {
					this.stop();
					console.error('Error starting recorder and player:', error);
					this.appendToChatBubble('ai', '[Error]: Unable to start audio recorder and player. Please check your microphone permissions and refresh the page.');
				});
		}
	},
	stop() {
		if ( !this.lastResponseSaved && 'saveResponseAsync' in window && this.lastUserQuestion.trim() !== '' && this.lastAiResponse.trim() !== '' ) {
			saveResponseAsync(
				this.lastUserQuestion.trim(),
				this.lastAiResponse.trim(),
				document.querySelector('#chat_id').value,
				'',
				'',
				'',
				'',
				'gpt-4o-realtime-preview-2024-12-17'
			);
		}

		this.resetPlayers();
		this.wsConnection && this.wsConnection.close();

		this.switchVisualizers('');

		Alpine.store('realtimeChatStatus').setActive(false);
	},
	async startPlayer() {
		try {
			this.audioPlayer = new Player();
			await this.audioPlayer.init(24000);
		} catch (error) {
			console.error('Error starting audio player:', error);
		}
	},
	async startRecorder() {
		try {
			this.audioRecorder = new Recorder(this.processAudioRecordingBuffer);
			const stream = await navigator.mediaDevices.getUserMedia({ audio: true, video: false });

			await this.audioRecorder.start(stream);
			this.recordingActive = true;
		} catch (error) {
			console.error('Error starting audio recorder:', error);
		}
	},
	createConfigMessage() {
		let configMessage = {
			type: 'session.update',
			session: {
				turn_detection: {
					type: 'server_vad',
					silence_duration_ms: 500
				},
				input_audio_transcription: {
					model: 'whisper-1'
				},
			},
		};

		const systemMessage = this.getSystemMessage();
		const temperature = this.getTemperature();
		const voice = this.getVoice();

		if (systemMessage) {
			configMessage.session.instructions = systemMessage;
		}
		if (!isNaN(temperature)) {
			configMessage.session.temperature = temperature;
		}
		if (voice) {
			configMessage.session.voice = voice;
		}

		return configMessage;
	},
	async handleRealtimeMessages() {
		for await (const message of this.wsConnection.messages()) {
			let consoleLog = '' + message.type;

			switch (message.type) {
				case 'session.created':
					this.switchVisualizers('idle');
					break;
				case 'response.content_part.added':
					this.lastAiResponse = '';

					Alpine.store('realtimeChatStatus').setConversationStarted(true);

					this.createChatBubble('ai');
					break;
				case 'response.audio_transcript.delta':
					this.lastAiResponse += message.delta;
					this.appendToChatBubble('ai', message.delta);
					break;
				case 'response.audio.delta': {
					this.switchVisualizers('playing');

					const binary = atob(message.delta);
					const bytes = Uint8Array.from(binary, c => c.charCodeAt(0));
					const pcmData = new Int16Array(bytes.buffer);

					this.audioPlayer.play(pcmData);
					break;
				}
				case 'input_audio_buffer.speech_started': {
					this.createChatBubble('user');
					this.switchVisualizers('recording');

					this.audioPlayer.clear();

					this.lastResponseSaved = false;

					setTimeout(() => {
						this.lastUserQuestion = '';
					}, 0);
					break;
				}
				case 'conversation.item.input_audio_transcription.completed':
					this.lastUserQuestion += message.transcript;
					this.appendToChatBubble('user', message.transcript);

					break;
				case 'response.output_item.done':
					break;
				case 'conversation.item.truncated':
					this.appendToChatBubble('ai', '...');
					break;
				case 'conversation.item.deleted':
					break;
				case 'response.done':
					if ( 'saveResponseAsync' in window ) {
						saveResponseAsync(
							this.lastUserQuestion.trim(),
							this.lastAiResponse.trim(),
							document.querySelector('#chat_id').value,
							'',
							'',
							'',
							'',
							'gpt-4o-realtime-preview-2024-12-17'
						);

						this.lastResponseSaved = true;
					}

					if ( 'formatString' in window && this.lastAiBubble ) {
						this.lastAiBubble.innerHTML = formatString( this.lastAiResponse );
					}
					break;
				default:
					consoleLog = JSON.stringify(message, null, 2);
					break;
			}

			// if (consoleLog) {
			// 	console.log(consoleLog);
			// }
		}

		this.resetPlayers();
	},
	combineArray(newData) {
		const newBuffer = new Uint8Array(this.buffer.length + newData.length);
		newBuffer.set(this.buffer);
		newBuffer.set(newData, this.buffer.length);
		this.buffer = newBuffer;
	},
	processAudioRecordingBuffer(data) {
		const uint8Array = new Uint8Array(data);

		this.combineArray(uint8Array);

		if (this.buffer.length >= 4800) {
			const toSend = new Uint8Array(this.buffer.slice(0, 4800));
			this.buffer = new Uint8Array(this.buffer.slice(4800));
			const regularArray = String.fromCharCode(...toSend);
			const base64 = btoa(regularArray);

			if (this.recordingActive) {
				this.wsConnection.send({
					type: 'input_audio_buffer.append',
					audio: base64,
				});
			}
		}
	},
	async resetPlayers() {
		this.recordingActive = false;

		this.audioRecorder?.stop();
		this.audioPlayer?.clear();
	},
	getSystemMessage() {
		return '';
	},
	getTemperature() {
		return parseFloat(0.8);
	},
	getVoice() {
		// alloy, echo, or shimmer
		return 'alloy';
	},
	switchVisualizers(activeVisulaizer) {
		this.activeVisulaizer = activeVisulaizer;

		this.audioVisWrap?.setAttribute('data-state', this.activeVisulaizer);
	},
	createChatBubble(role) {
		const template = role === 'user' ? this.userBubbleTemplate : this.aiBubbleTemplate;
		const bubble = template.content.cloneNode(true);
		const bubbleContainer = bubble.querySelector('.chat-content');

		this.chatsContainer.appendChild(bubble);

		if (role === 'user') {
			this.lastUserBubble = bubbleContainer;
		} else {
			this.lastAiBubble = bubbleContainer;
		}

		this.scrollConversationAreaToBottom();
	},
	appendToChatBubble(role, text) {
		const bubble = role === 'user' ? this.lastUserBubble : this.lastAiBubble;

		if (bubble) {
			bubble.textContent += text;

			this.scrollConversationAreaToBottom();
		} else {
			this.createChatBubble(role);
			this.appendToChatBubble(role, text);
		}
	},
	scrollConversationAreaToBottom() {
		this.conversationArea.scrollTo({
			top: this.conversationArea.scrollHeight + 200,
			left: 0
		});
	},
	startBarsVisualizer() {
		if (!this.audioVisBars?.length) return;

		const audioAnalyser = this.audioPlayer.audioContext.createAnalyser();
		audioAnalyser.fftSize = 4096;

		const bufferLength = audioAnalyser.frequencyBinCount;
		const dataArray = new Uint8Array(bufferLength);
		const barCount = this.audioVisBars.length;

		this.audioPlayer.playbackNode.connect(audioAnalyser);

		// Define frequency ranges for each bar (in Hz)
		const frequencyRanges = [
			[ 85, 150 ],   // Low
			[ 150, 250 ],  // Low-mid
			[ 250, 400 ],  // Mid
			[ 400, 600 ],  // Mid-high
			[ 600, 1000 ]  // High (including some overtones)
		];

		// Create an array to store the current heights of bars
		this.barHeights = this.barHeights || new Array(barCount).fill(0);

		const animate = () => {
			audioAnalyser.getByteFrequencyData(dataArray);

			this.audioVisBars.forEach((bar, index) => {
				const [ lowFreq, highFreq ] = frequencyRanges[index];

				// Convert frequency to FFT bin index
				const lowIndex = Math.floor(lowFreq / (this.audioPlayer.audioContext.sampleRate / audioAnalyser.fftSize));
				const highIndex = Math.ceil(highFreq / (this.audioPlayer.audioContext.sampleRate / audioAnalyser.fftSize));

				// Get the maximum amplitude in this frequency range
				let maxAmplitude = 0;
				for (let i = lowIndex; i <= highIndex && i < dataArray.length; i++) {
					if (dataArray[i] > maxAmplitude) {
						maxAmplitude = dataArray[i];
					}
				}

				// Calculate target height (0-80)
				let targetHeight = (maxAmplitude / 255) * 80;

				// Smooth the movement
				this.barHeights[index] += (targetHeight - this.barHeights[index]) * 0.4;

				// Add some randomness for natural look
				this.barHeights[index] += (Math.random() - 0.5) * 2;

				// Ensure height is between 5% and 100%
				this.barHeights[index] = Math.max(5, Math.min(100, this.barHeights[index]));

				// Animate the bar height
				bar.animate(
					[
						{ height: bar.style.height },
						{ height: `${this.barHeights[index]}%` }
					],
					{
						duration: 30,
						fill: 'forwards',
						easing: 'linear'
					}
				);
			});

			requestAnimationFrame(animate);
		};

		animate();
	},
	startDotVisualizer() {
		if (!this.audioRecorder || !this.audioVisDotWrap) return;

		const analyser = this.audioRecorder.audioContext.createAnalyser();
		analyser.fftSize = 256;
		const bufferLength = analyser.frequencyBinCount;
		const dataArray = new Uint8Array(bufferLength);

		this.audioRecorder.getMediaStreamSource().connect(analyser);

		const dot = this.audioVisDotWrap.querySelector('.lqd-audio-vis-dot');

		if (!dot) return;

		const animate = () => {
			analyser.getByteFrequencyData(dataArray);

			let sum = 0;
			for (let i = 0; i < bufferLength; i++) {
				sum += dataArray[i];
			}
			const average = sum / bufferLength;

			const scale = 1 + (average / 256) * 1.5;
			const opacity = Math.max(0.2, 1 - (scale - 1) / 1.5); // Minimum opacity of 0.2

			dot.style.transform = `scale(${scale})`;
			dot.style.opacity = opacity.toFixed(2); // Limit to two decimal places

			requestAnimationFrame(animate);
		};

		animate();
	},
});
