INSERT INTO `openai` (`title`, `description`, `slug`, `active`, `questions`, `image`, `premium`, `type`, `prompt`,
                      `custom_template`, `tone_of_voice`, `color`, `filters`, `updated_at`, `created_at`)
values ('AI Voiceover',
        'The AI app that turns text into audio speech with ease. Get ready to generate custom audios from texts quickly and accurately.',
        'ai_voiceover', '1',
        '[{"name":"file","type":"file","question":"Upload an Audio File (mp3, mp4, mpeg, mpga, m4a, wav, and webm)(Max: 25Mb)","select":""}]',
        '<svg xmlns="http://www.w3.org/2000/svg" height="48" viewBox="0 96 960 960" width="48"><path d="M140 976q-24.75 0-42.375-17.625T80 916V236q0-24.75 17.625-42.375T140 176h380l-60 60H140v680h480V776h60v140q0 24.75-17.625 42.375T620 976H140Zm100-170v-60h280v60H240Zm0-120v-60h200v60H240Zm380 10L460 536H320V336h140l160-160v520Zm60-92V258q56 21 88 74t32 104q0 51-35 101t-85 67Zm0 142v-62q70-25 125-90t55-158q0-93-55-158t-125-90v-62q102 27 171 112.5T920 436q0 112-69 197.5T680 746Z"/></svg>',
        '0', 'voiceover', '', '0', '0', '#DEFF81', 'voiceover', '2024-03-01 11:35:52', '2024-03-01 11:35:52'
       );
