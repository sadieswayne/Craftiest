import { YoutubeTranscript } from 'youtube-transcript';

export function fetchAndLogTranscript(videoIdOrURL) {
    return YoutubeTranscript.fetchTranscript(videoIdOrURL)
        .then(transcript => {
            console.log(transcript);
            return transcript; 
        })
        .catch(error => {
            console.error('Error fetching transcript:', error);
            throw error;
        });
}
