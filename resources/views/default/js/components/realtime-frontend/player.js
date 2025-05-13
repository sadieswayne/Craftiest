export class Player {
	constructor() {
		this.playbackNode = null;
	}

	async init(sampleRate) {
		this.audioContext = new AudioContext({ sampleRate });
		await this.audioContext.audioWorklet.addModule('/themes/default/assets/js/audio/playback-worklet.js');

		this.playbackNode = new AudioWorkletNode(this.audioContext, 'playback-worklet');
		this.playbackNode.connect(this.audioContext.destination);
	}

	play(buffer) {
		if (this.playbackNode) {
			this.playbackNode.port.postMessage(buffer);
		}
	}

	clear() {
		if (this.playbackNode) {
			this.playbackNode.port.postMessage(null);
		}
	}
}
