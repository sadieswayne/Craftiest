INSERT INTO openai (user_id, title, description, slug, active, questions, image, premium, type, created_at, updated_at, prompt, custom_template, tone_of_voice, color, filters, package)
VALUES
-- AMA Post
(NULL, 'AMA Post', 'Create engaging Ask Me Anything (AMA) posts to connect with your audience and share valuable insights.', 'ama_post', 1, '[{"name":"topic","type":"textarea","question":"Topic of the AMA","select":""},{"name":"your_name","type":"text","question":"Your Name","select":""},{"name":"key_points","type":"textarea","question":"Key Points to Discuss","select":""}]', '<svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M6.25 11.3333C5.50832 11.3333 4.7833 11.1134 4.16661 10.7013C3.54993 10.2893 3.06928 9.70362 2.78545 9.0184C2.50162 8.33318 2.42736 7.57918 2.57206 6.85175C2.71675 6.12432 3.0739 5.45613 3.59835 4.93168C4.1228 4.40724 4.79098 4.05009 5.51841 3.90539C6.24584 3.7607 6.99984 3.83496 7.68506 4.11879C8.37029 4.40262 8.95596 4.88326 9.36801 5.49995C9.78007 6.11663 10 6.84166 10 7.58334C9.9989 8.57756 9.60345 9.53075 8.90043 10.2338C8.19741 10.9368 7.24422 11.3322 6.25 11.3333ZM11.6667 20.5H0.833333C0.61232 20.5 0.400358 20.4122 0.244078 20.2559C0.0877974 20.0996 0 19.8877 0 19.6667V19.25C0 17.5924 0.65848 16.0027 1.83058 14.8306C3.00268 13.6585 4.5924 13 6.25 13C7.9076 13 9.49731 13.6585 10.6694 14.8306C11.8415 16.0027 12.5 17.5924 12.5 19.25V19.6667C12.5 19.8877 12.4122 20.0996 12.2559 20.2559C12.0996 20.4122 11.8877 20.5 11.6667 20.5ZM14.5833 8C13.8417 8 13.1166 7.78007 12.4999 7.36801C11.8833 6.95596 11.4026 6.37029 11.1188 5.68507C10.835 4.99984 10.7607 4.24584 10.9054 3.51841C11.0501 2.79098 11.4072 2.1228 11.9317 1.59835C12.4561 1.0739 13.1243 0.716751 13.8517 0.572057C14.5792 0.427362 15.3332 0.501625 16.0184 0.785453C16.7036 1.06928 17.2893 1.54993 17.7013 2.16661C18.1134 2.7833 18.3333 3.50832 18.3333 4.25C18.3322 5.24423 17.9368 6.19741 17.2338 6.90043C16.5307 7.60346 15.5776 7.9989 14.5833 8ZM13.3992 9.68417C12.623 9.7883 11.8767 10.0516 11.2071 10.4575C10.5374 10.8635 9.95882 11.4034 9.5075 12.0433C11.3749 12.8914 12.8422 14.4285 13.6025 16.3333H19.1667C19.3877 16.3333 19.5996 16.2455 19.7559 16.0893C19.9122 15.933 20 15.721 20 15.5V15.4683C19.9991 14.6377 19.8211 13.8168 19.4777 13.0604C19.1344 12.304 18.6336 11.6296 18.0088 11.0821C17.3841 10.5347 16.6498 10.1268 15.8549 9.88574C15.0599 9.64467 14.2227 9.57595 13.3992 9.68417Z" fill="url(#paint0_linear_0_3061)"/>
<defs>
<linearGradient id="paint0_linear_0_3061" x1="0" y1="10.5" x2="20" y2="10.5" gradientUnits="userSpaceOnUse">
<stop stop-color="#EB6434"/>
<stop offset="0.545" stop-color="#BB2D9F"/>
<stop offset="0.98" stop-color="#BB802D"/>
</linearGradient>
</defs>
</svg>', 0, 'text', '2024-05-15 10:00:00', '2024-05-15 10:00:00', 'Write an engaging AMA post about **topic**. My name is **your_name**, and I will focus on discussing **key_points**.', 1, 0, '#FFB4B4', 'social_media', NULL),

-- Newsletter
(NULL, 'Newsletter', 'Craft professional newsletters to keep your audience informed and engaged.', 'newsletter', 1, '[{"name":"subject","type":"text","question":"Subject Line","select":""},{"name":"content","type":"textarea","question":"Main Content","select":""},{"name":"call_to_action","type":"text","question":"Call to Action","select":""}]', '<svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M11 0.5C8.08359 0.503154 5.28753 1.66309 3.22531 3.72531C1.16309 5.78753 0.00315432 8.58359 0 11.5C0.55 26.075 21.45 26.0686 22 11.5C21.9968 8.58359 20.8369 5.78753 18.7747 3.72531C16.7125 1.66309 13.9164 0.503154 11 0.5ZM14.2083 7.83333C14.573 7.83333 14.9227 7.9782 15.1806 8.23606C15.4385 8.49392 15.5833 8.84366 15.5833 9.20833C15.5833 9.57301 15.4385 9.92274 15.1806 10.1806C14.9227 10.4385 14.573 10.5833 14.2083 10.5833C13.8437 10.5833 13.4939 10.4385 13.2361 10.1806C12.9782 9.92274 12.8333 9.57301 12.8333 9.20833C12.8333 8.84366 12.9782 8.49392 13.2361 8.23606C13.4939 7.9782 13.8437 7.83333 14.2083 7.83333ZM7.79167 7.83333C8.15634 7.83333 8.50608 7.9782 8.76394 8.23606C9.0218 8.49392 9.16667 8.84366 9.16667 9.20833C9.16667 9.57301 9.0218 9.92274 8.76394 10.1806C8.50608 10.4385 8.15634 10.5833 7.79167 10.5833C7.42699 10.5833 7.07726 10.4385 6.81939 10.1806C6.56153 9.92274 6.41667 9.57301 6.41667 9.20833C6.41667 8.84366 6.56153 8.49392 6.81939 8.23606C7.07726 7.9782 7.42699 7.83333 7.79167 7.83333ZM16.3552 14.7468C15.8166 15.6964 15.0389 16.4885 14.0995 17.0446C13.1601 17.6006 12.0916 17.9013 11 17.9167C9.90769 17.9014 8.83833 17.6009 7.89801 17.0448C6.95768 16.4888 6.17905 15.6966 5.63933 14.7468C5.54188 14.5947 5.49154 14.4172 5.49466 14.2365C5.49777 14.0559 5.55419 13.8802 5.65683 13.7316C5.75947 13.5829 5.90376 13.4678 6.07156 13.4009C6.23937 13.334 6.42321 13.3181 6.6 13.3553C8.00954 13.8864 9.495 14.1884 11 14.25C12.5025 14.1877 13.9854 13.8856 15.3927 13.3553C15.5696 13.3177 15.7537 13.3332 15.9218 13.3999C16.09 13.4667 16.2346 13.5817 16.3375 13.7304C16.4404 13.8792 16.497 14.0551 16.5001 14.236C16.5033 14.4168 16.4528 14.5946 16.3552 14.7468Z" fill="url(#paint0_linear_0_3065)"/>
<defs>
<linearGradient id="paint0_linear_0_3065" x1="0" y1="11.4644" x2="22" y2="11.4644" gradientUnits="userSpaceOnUse">
<stop stop-color="#EB6434"/>
<stop offset="0.545" stop-color="#BB2D9F"/>
<stop offset="0.98" stop-color="#BB802D"/>
</linearGradient>
</defs>
</svg>', 0, 'text', '2024-05-15 10:00:00', '2024-05-15 10:00:00', 'Create a newsletter with the subject line **subject**. The main content is **content**, and include a call to action: **call_to_action**.', 1, 0, '#C2D6D6', 'business', NULL),

-- Storytelling
(NULL, 'Storytelling', 'Engage your audience with compelling storytelling that captivates and inspires.', 'storytelling', 1, '[{"name":"theme","type":"text","question":"Theme of the Story","select":""},{"name":"characters","type":"textarea","question":"Characters in the Story","select":""},{"name":"plot","type":"textarea","question":"Plot Summary","select":""}]', '<svg width="22" height="21" viewBox="0 0 22 21" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M4.7829 0.981566C5.06819 -0.321342 6.9239 -0.329451 7.22057 0.970914L7.23531 1.03577C7.24542 1.08036 7.25484 1.122 7.26496 1.16507C7.63483 2.73994 8.90971 3.94329 10.5056 4.22084C11.8637 4.45705 11.8637 6.40607 10.5056 6.64228C8.90121 6.92131 7.62125 8.13604 7.25912 9.72329L7.22057 9.89221C6.9239 11.1926 5.06819 11.1845 4.7829 9.88156L4.75117 9.73661C4.40228 8.14334 3.12388 6.91944 1.51655 6.63988C0.161155 6.40415 0.161145 4.45897 1.51655 4.22324C3.11829 3.94466 4.39338 2.72828 4.74747 1.14315L4.77106 1.03583L4.7829 0.981566ZM16.4546 5.42472C16.731 5.30847 17.0278 5.24857 17.3276 5.24857C17.6273 5.24857 17.9241 5.30847 18.2005 5.42472C18.4758 5.54058 18.7254 5.71008 18.9345 5.92337L18.9368 5.92556L20.8367 7.83985C21.0464 8.04851 21.2131 8.29649 21.3268 8.56962C21.4412 8.8438 21.5 9.1379 21.5 9.43493C21.5 9.73196 21.4412 10.0261 21.3268 10.3002C21.213 10.5734 21.0464 10.8215 20.8365 11.0302L20.8343 11.0324L11.5353 20.3886C11.4103 20.5145 11.2447 20.5919 11.0679 20.6072L6.56657 20.9972C6.34586 21.0164 6.12796 20.937 5.9713 20.7804C5.81465 20.6237 5.73525 20.4059 5.75436 20.1852L6.14448 15.6852C6.1598 15.5085 6.23727 15.343 6.36313 15.2179L15.7225 5.92131C15.9312 5.709 16.18 5.54021 16.4546 5.42472Z" fill="url(#paint0_linear_0_3068)"/>
<defs>
<linearGradient id="paint0_linear_0_3068" x1="0.5" y1="10.5" x2="21.5" y2="10.5" gradientUnits="userSpaceOnUse">
<stop stop-color="#EB6434"/>
<stop offset="0.545" stop-color="#BB2D9F"/>
<stop offset="0.98" stop-color="#BB802D"/>
</linearGradient>
</defs>
</svg>', 0, 'text', '2024-05-15 10:00:00', '2024-05-15 10:00:00', 'Tell a captivating story with the theme **theme**. The characters are **characters**, and the plot summary is **plot**.', 1, 0, '#D6A3A3', 'creative', NULL),

-- Trending Post
(NULL, 'Trending Post', 'Create viral-worthy posts that resonate with current trends and capture attention.', 'trending_post', 1, '[{"name":"trend","type":"text","question":"Current Trend","select":""},{"name":"angle","type":"textarea","question":"Unique Angle","select":""},{"name":"hashtags","type":"text","question":"Relevant Hashtags","select":""}]', '<svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M19.505 2.02008L19.8958 0.604248L18.48 0.995081C18.3175 1.04008 14.7858 2.02758 10.8333 4.09508V0.659248L9.37583 1.60008C4.98167 4.43592 0 7.64925 0 13.4167C0 15.3084 0.736667 17.0868 2.07417 18.4259C3.4125 19.7634 5.19167 20.5001 7.08333 20.5001C12.8508 20.5001 16.0642 15.5184 18.9 11.1242L19.8408 9.66675H16.4058C18.4725 5.71425 19.46 2.18341 19.505 2.02008ZM7.5 17.1667C5.2025 17.1667 3.33333 15.2976 3.33333 13.0001C3.33333 10.7026 5.2025 8.83342 7.5 8.83342C9.7975 8.83342 11.6667 10.7026 11.6667 13.0001C11.6667 15.2976 9.7975 17.1667 7.5 17.1667ZM10 13.0001C10 14.3784 8.87833 15.5001 7.5 15.5001C6.12167 15.5001 5 14.3784 5 13.0001C5 11.6217 6.12167 10.5001 7.5 10.5001C8.87833 10.5001 10 11.6217 10 13.0001Z" fill="url(#paint0_linear_0_3072)"/>
<defs>
<linearGradient id="paint0_linear_0_3072" x1="0" y1="10.5522" x2="19.8958" y2="10.5522" gradientUnits="userSpaceOnUse">
<stop stop-color="#EB6434"/>
<stop offset="0.545" stop-color="#BB2D9F"/>
<stop offset="0.98" stop-color="#BB802D"/>
</linearGradient>
</defs>
</svg>', 0, 'text', '2024-05-15 10:00:00', '2024-05-15 10:00:00', 'Write a trending post about **trend**. Use the unique angle **angle** and include hashtags: **hashtags**.', 1, 0, '#F1C40F', 'social_media', NULL),

-- Strategy
(NULL, 'Strategy', 'Develop actionable strategies to achieve specific goals or solve problems effectively.', 'strategy', 1, '[{"name":"goal","type":"text","question":"Goal","select":""},{"name":"challenges","type":"textarea","question":"Challenges","select":""},{"name":"approach","type":"textarea","question":"Proposed Approach","select":""}]', '<svg width="16" height="21" viewBox="0 0 16 21" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M5.52167 4.66659C5.52167 3.76909 6.5775 2.06909 7.31667 0.889086C7.39178 0.770145 7.49579 0.672156 7.61899 0.60425C7.74219 0.536345 7.88058 0.500732 8.02125 0.500732C8.16193 0.500732 8.30031 0.536345 8.42351 0.60425C8.54671 0.672156 8.65072 0.770145 8.72583 0.889086C9.465 2.06909 10.5217 3.76909 10.5217 4.66659C10.5217 5.32963 10.2583 5.96551 9.78943 6.43435C9.32059 6.90319 8.68471 7.16659 8.02167 7.16659C7.35863 7.16659 6.72274 6.90319 6.2539 6.43435C5.78506 5.96551 5.52167 5.32963 5.52167 4.66659ZM13.855 18.8549V17.1883H2.18833V18.8549H0.521667V20.5216H15.5217V18.8549H13.855ZM10.5325 8.85492H12.1883V7.18825H3.855V8.85492H5.51083C5.4575 11.9991 4.67333 13.8658 2.70833 15.5216H13.3333C11.3692 13.8658 10.5842 11.9999 10.5325 8.85492Z" fill="url(#paint0_linear_0_3076)"/>
<defs>
<linearGradient id="paint0_linear_0_3076" x1="0.521667" y1="10.5112" x2="15.5217" y2="10.5112" gradientUnits="userSpaceOnUse">
<stop stop-color="#EB6434"/>
<stop offset="0.545" stop-color="#BB2D9F"/>
<stop offset="0.98" stop-color="#BB802D"/>
</linearGradient>
</defs>
</svg>', 0, 'text', '2024-05-15 10:00:00', '2024-05-15 10:00:00', 'Develop a strategy to achieve the goal **goal**. Address the challenges **challenges** and propose the approach **approach**.', 1, 0, '#C2D6D6', 'business', NULL),

-- Viral Ideas
(NULL, 'Viral Ideas', 'Generate creative ideas designed to go viral and capture widespread attention.', 'viral_ideas', 1, '[{"name":"audience","type":"text","question":"Target Audience","select":""},{"name":"platform","type":"text","question":"Platform","select":""},{"name":"idea","type":"textarea","question":"Idea Description","select":""}]', '<svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M17.5139 11.691C16.8389 11.6913 16.1932 11.9677 15.7264 12.4563L13.8312 11.6495C13.9608 11.2613 14.029 10.8552 14.0334 10.4459C14.0289 8.24731 12.2507 6.4661 10.0557 6.46156C9.72389 6.4657 9.39393 6.51199 9.07373 6.59936L7.984 4.65616C8.90666 3.64327 8.83486 2.07291 7.82365 1.14872C6.81244 0.22452 5.2447 0.296437 4.32204 1.30934C3.39939 2.32224 3.47118 3.89259 4.4824 4.81678C4.94103 5.23596 5.54002 5.46744 6.16091 5.46548C6.28009 5.46208 6.39892 5.45014 6.51642 5.4298L7.58791 7.33896C5.97853 8.59425 5.5965 10.8748 6.70865 12.5875L3.5729 15.6264C2.28981 15.0562 0.78821 15.6359 0.218991 16.9211C-0.350229 18.2064 0.228483 19.7105 1.51157 20.2806C2.79466 20.8508 4.29626 20.2711 4.86548 18.9859C5.18209 18.2711 5.15314 17.4501 4.78693 16.7595L7.87049 13.7712C9.50873 14.8631 11.7001 14.5764 13.0034 13.0997L15.0486 13.9696C15.0428 14.0401 15.0279 14.1082 15.0279 14.1796C15.0279 15.5549 16.1409 16.6698 17.5139 16.6698C18.887 16.6698 20 15.5548 20 14.1795C20 12.8042 18.887 11.6893 17.5139 11.6893V11.691Z" fill="url(#paint0_linear_0_3079)"/>
<defs>
<linearGradient id="paint0_linear_0_3079" x1="0" y1="10.5" x2="20" y2="10.5" gradientUnits="userSpaceOnUse">
<stop stop-color="#EB6434"/>
<stop offset="0.545" stop-color="#BB2D9F"/>
<stop offset="0.98" stop-color="#BB802D"/>
</linearGradient>
</defs>
</svg>', 0, 'text', '2024-05-15 10:00:00', '2024-05-15 10:00:00', 'Generate a viral idea for the target audience **audience** on the platform **platform**. The idea description is **idea**.', 1, 0, '#FFB4B4', 'creative', NULL),

-- Ad Script
(NULL, 'Ad Script', 'Create compelling ad scripts to engage your audience and boost conversions.', 'ad_script', 1, '[{"name":"product_name","type":"text","question":"Product Name","select":""},{"name":"target_audience","type":"text","question":"Target Audience","select":""},{"name":"key_message","type":"textarea","question":"Key Message","select":""}]', '<svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M11 0.5C8.08359 0.503154 5.28753 1.66309 3.22531 3.72531C1.16309 5.78753 0.00315432 8.58359 0 11.5C0.55 26.075 21.45 26.0686 22 11.5C21.9968 8.58359 20.8369 5.78753 18.7747 3.72531C16.7125 1.66309 13.9164 0.503154 11 0.5Z" fill="url(#paint0_linear)"/>
<defs><linearGradient id="paint0_linear" x1="0" y1="10.5" x2="20" y2="10.5" gradientUnits="userSpaceOnUse"><stop stop-color="#EB6434"/><stop offset="0.545" stop-color="#BB2D9F"/><stop offset="0.98" stop-color="#BB802D"/></linearGradient></defs>
</svg>', 0, 'text', NOW(), NOW(), 'Write an engaging ad script for **product_name** targeting **target_audience**. The key message to highlight is **key_message**.', 1, 0, '#FF5733', 'advertising', NULL),

-- Marketing Plan
(NULL, 'Marketing Plan', 'Develop a structured marketing plan to grow your business and reach your target audience.', 'marketing_plan', 1, '[{"name":"business_name","type":"text","question":"Business Name","select":""},{"name":"target_market","type":"text","question":"Target Market","select":""},{"name":"marketing_goals","type":"textarea","question":"Marketing Goals","select":""}]', '<svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M11 0.5C8.08359 0.503154 5.28753 1.66309 3.22531 3.72531C1.16309 5.78753 0.00315432 8.58359 0 11.5C0.55 26.075 21.45 26.0686 22 11.5C21.9968 8.58359 20.8369 5.78753 18.7747 3.72531C16.7125 1.66309 13.9164 0.503154 11 0.5Z" fill="url(#paint0_linear)"/>
<defs><linearGradient id="paint0_linear" x1="0" y1="10.5" x2="20" y2="10.5" gradientUnits="userSpaceOnUse"><stop stop-color="#3498DB"/><stop offset="0.545" stop-color="#2ECC71"/><stop offset="0.98" stop-color="#F1C40F"/></linearGradient></defs>
</svg>', 0, 'text', NOW(), NOW(), 'Create a marketing plan for **business_name** targeting **target_market**. The primary marketing goals are **marketing_goals**.', 1, 0, '#3498DB', 'business', NULL),

-- Video Script
(NULL, 'Video Script', 'Write compelling video scripts for promotional, educational, or storytelling purposes.', 'video_script', 1, '[{"name":"video_topic","type":"text","question":"Video Topic","select":""},{"name":"intended_audience","type":"text","question":"Intended Audience","select":""},{"name":"key_talking_points","type":"textarea","question":"Key Talking Points","select":""}]', '<svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M11 0.5C8.08359 0.503154 5.28753 1.66309 3.22531 3.72531C1.16309 5.78753 0.00315432 8.58359 0 11.5C0.55 26.075 21.45 26.0686 22 11.5C21.9968 8.58359 20.8369 5.78753 18.7747 3.72531C16.7125 1.66309 13.9164 0.503154 11 0.5Z" fill="url(#paint0_linear)"/>
<defs><linearGradient id="paint0_linear" x1="0" y1="10.5" x2="20" y2="10.5" gradientUnits="userSpaceOnUse"><stop stop-color="#8E44AD"/><stop offset="0.545" stop-color="#3498DB"/><stop offset="0.98" stop-color="#E74C3C"/></linearGradient></defs>
</svg>', 0, 'text', NOW(), NOW(), 'Write a video script about **video_topic** for **intended_audience**. Key talking points to include: **key_talking_points**.', 1, 0, '#8E44AD', 'video', NULL);
