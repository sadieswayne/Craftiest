let chatbot_selectedPrompt = -1;
let chatbot_promptsData = [];
let chatbot_searchString = '';
let chatbot_filterType = 'all';
let chatbot_imagePath = [];
let chatbot_chat_id = '';
let chatbot_chat_category_id = '';

function chatbot_updateFav(id) {
    $.ajax({
        type: 'post',
        url: '/dashboard/user/openai/chat/update-prompt',
        data: {
            id: id,
        },
        success: function (data) {
            favData = data;
            chatbot_updatePrompts(chatbot_promptsData);
        },
        error: function () {
        },
    });
}

function chatbot_updatePrompts(data) {
    const $prompts = $('#prompts');

    $prompts.empty();

    if (data.length == 0) {
        $('#no_prompt').removeClass('hidden');
    } else {
        $('#no_prompt').addClass('hidden');
    }

    for (let i = 0; i < data.length; i++) {
        let isFav = favData.filter(item => item.item_id == data[i].id).length;

        let title = data[i].title.toLowerCase();
        let prompt = data[i].prompt.toLowerCase();
        let searchStr = chatbot_searchString.toLowerCase();

        if (searchStr.replace(/\s/g, '') === '') {
        }

        if (data[i].id == chatbot_selectedPrompt) {
            if (title.includes(searchStr) || prompt.includes(searchStr)) {
                if (
                    (chatbot_filterType == 'fav' && isFav != 0) ||
                    chatbot_filterType != 'fav'
                ) {
                    let prompt = document
                        .querySelector('#selected_prompt')
                        .content.cloneNode(true);
                    const favbtn = prompt.querySelector('.favbtn');
                    prompt.querySelector('.prompt_title').innerHTML =
                        data[i].title;
                    prompt.querySelector('.prompt_text').innerHTML =
                        data[i].prompt;
                    favbtn.setAttribute('id', data[i].id);

                    if (isFav != 0) {
                        favbtn.classList.add('active');
                    } else {
                        favbtn.classList.remove('active');
                    }

                    $prompts.append(prompt);
                } else {
                    chatbot_selectedPrompt = -1;
                }
            } else {
                chatbot_selectedPrompt = -1;
            }
        } else {
            if (title.includes(searchStr) || prompt.includes(searchStr)) {
                if (
                    (chatbot_filterType == 'fav' && isFav != 0) ||
                    chatbot_filterType != 'fav'
                ) {
                    let prompt = document
                        .querySelector('#unselected_prompt')
                        .content.cloneNode(true);
                    const favbtn = prompt.querySelector('.favbtn');
                    prompt.querySelector('.prompt_title').innerHTML =
                        data[i].title;
                    prompt.querySelector('.prompt_text').innerHTML =
                        data[i].prompt;
                    favbtn.setAttribute('id', data[i].id);

                    if (isFav != 0) {
                        favbtn.classList.add('active');
                    } else {
                        favbtn.classList.remove('active');
                    }

                    $prompts.append(prompt);
                }
            }
        }
    }
    let favCnt = favData.length;
    let perCnt = data.length;

    if (favCnt == 0) {
        $('#fav_count')[0].innerHTML = '';
    } else {
        $('#fav_count')[0].innerHTML = favCnt;
    }

    if (perCnt == 0 || perCnt == undefined) {
        $('#per_count')[0].innerHTML = '';
    } else {
        $('#per_count')[0].innerHTML = perCnt;
    }
}

function chatbot_searchStringChange(e) {
    chatbot_searchString = $('#search_str').val();
    console.log(chatbot_searchString);
    chatbot_updatePrompts(chatbot_promptsData);
}

function chatbot_updatePrompt() {
    $('#chat_images').empty();
    if (prompt_images.length == 0) {
        $('#chat_images').removeClass('active');
        $('.split_line').addClass('hidden');
        return;
    }
    $('#chat_images').addClass('active');
    $('.split_line').removeClass('hidden');
    for (let i = 0; i < prompt_images.length; i++) {
        let new_image = document
            .querySelector('#prompt_image')
            .content.cloneNode(true);
        $(new_image.querySelector('img')).attr('src', prompt_images[i]);
        $(new_image.querySelector('.prompt_image_close')).attr('index', i);
        $(document.querySelector('#chat_images')).append(new_image);
    }
    let new_image_btn = document
        .querySelector('#prompt_image_add_btn')
        .content.cloneNode(true);
    document.querySelector('#chat_images').append(new_image_btn);
    $('.promt_image_btn').on('click', function (e) {
        e.preventDefault();
        $('#chat_add_image').click();
    });
    $('.prompt_image_close').on('click', function (e) {
        prompt_images.splice($(this).attr('index'), 1);
        chatbot_updatePrompt();
    });
}

function initChatBot() {
    var mediaRecorder;
    let audioBlob;
    var chunks = [];
    var stream_;

    prompt_images = [];

    $('#scrollable_content').animate({scrollTop: 1000}, 200);
    // Start recording when the button is pressed
    $('#voice_record_button').click(function (e) {
        chunks = [];
        navigator.mediaDevices
            .getUserMedia({audio: true})
            .then(function (stream) {
                stream_ = stream;
                mediaRecorder = new MediaRecorder(stream);
                $('#voice_record_button').addClass('inactive');
                $('#voice_record_stop_button').addClass('active');
                isRecord = true;
                console.log(mediaRecorder);
                mediaRecorder.ondataavailable = function (e) {
                    console.log(e.data);
                    chunks.push(e.data);
                };
                mediaRecorder.start();
            })
            .catch(function (err) {
                console.log('The following error occurred: ' + err);
                toastr.warning('Audio is not allowed');
            });

        $('#voice_record_stop_button').click(function (e) {
            e.preventDefault();
            $('#voice_record_button').removeClass('inactive');
            $('#voice_record_stop_button').removeClass('active');
            isRecord = false;
            mediaRecorder.onstop = function (e) {
                var blob = new Blob(chunks, {type: 'audio/mp3'});

                var formData = new FormData();
                var fileOfBlob = new File([blob], 'audio.mp3');
                formData.append('file', fileOfBlob);

                chunks = [];

                $.ajax({
                    url: '/dashboard/user/openai/chat/transaudio',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.length >= 4) {
                            $('#prompt').val(response);
                        }
                    },
                    error: function (error) {
                        // Handle the error response
                    },
                });
            };
            mediaRecorder.stop();
            stream_
                .getTracks() // get all tracks from the MediaStream
                .forEach(track => track.stop()); // stop each of them
        });
    });
    $('#btn_add_new_prompt').on('click', function (e) {
        prompt_title = $('#new_prompt_title').val();
        prompt = $('#new_prompt').val();

        if (prompt_title.trim() == '') {
            toastr.warning('Please input title');
            return;
        }

        if (prompt.trim() == '') {
            toastr.warning('Please input prompt');
            return;
        }

        $.ajax({
            type: 'post',
            url: '/dashboard/user/openai/chat/add-prompt',
            data: {
                title: prompt_title,
                prompt: prompt,
            },
            success: function (data) {
                chatbot_promptsData = data;
                chatbot_updatePrompts(data);
                $('.custom__popover__back').addClass('hidden');
                $('#custom__popover').removeClass('custom__popover__wrapper');
            },
            error: function () {
            },
        });
    });

    $('#add_btn').on('click', function (e) {
        $('#custom__popover').addClass('custom__popover__wrapper');
        $('.custom__popover__back').removeClass('hidden');
        e.stopPropagation();
    });

    $('.custom__popover__back').on('click', function () {
        $(this).addClass('hidden');
        $('#custom__popover').removeClass('custom__popover__wrapper');
    });

    $('#prompt_library').on('click', function (e) {
        e.preventDefault();

        $('#prompts').empty();

        $.ajax({
            type: 'post',
            url: '/dashboard/user/openai/chat/prompts',
            success: function (data) {
                chatbot_filterType = 'all';
                chatbot_promptsData = data.promptData;
                console.log('Update');
                favData = data.favData;
                chatbot_updatePrompts(data.promptData);
                $('#modal').addClass('lqd-is-active');
                $('.modal__back').removeClass('hidden');
            },
            error: function () {
            },
        });
        e.stopPropagation();
    });

    $('.modal__back').on('click', function () {
        $(this).addClass('hidden');
        $('#modal').removeClass('lqd-is-active');
    });

    $(document).on('click', '.prompt', function () {
        const $promptInput = $('#prompt');
        chatbot_selectedPrompt = Number($(this.querySelector('.favbtn')).attr('id'));
        $promptInput.val(
            chatbot_promptsData.filter(item => item.id == chatbot_selectedPrompt)[0].prompt
        );
        $('.modal__back').addClass('hidden');
        $('#modal').removeClass('lqd-is-active');
        chatbot_selectedPrompt = -1;
        // chatbot_updatePrompts(chatbot_promptsData);
        console.log($(this.querySelector('.favbtn')).attr('id'));
        $promptInput.css('height', '5px');
        $promptInput.css('height', $promptInput[0].scrollHeight + 'px');
    });

    $(document).on('click', '.filter_btn', function () {
        $('.filter_btn').removeClass('active');
        $(this).addClass('active');
        chatbot_filterType = $(this).attr('filter');
        console.log(chatbot_filterType);
        chatbot_updatePrompts(chatbot_promptsData);
    });

    $(document).on('click', '.favbtn', function (e) {
        console.log($(this).attr('id'));
        chatbot_updateFav(Number($(this).attr('id')));
        e.stopPropagation();
    });
}

$(document).ready(initChatBot);

($ => {
    'use strict';

    function chatbot_submitForm(ev) {
        ev?.preventDefault();
        let chatbot = $(this).find('#chatbot').val() ?? 0;
        document.getElementById('mainupscale_src') &&
        (document.getElementById('mainupscale_src').style.display = 'none');
        document.getElementById('sugg') &&
        (document.getElementById('sugg').style.display = 'none');

        const prompt = chatbot ? document.getElementById('chatbot_prompt') : document.getElementById('prompt');
        const constvalue = prompt.value;

        prompt.value = '';
        prompt.style.height = '';

        let chatbot_scrollLocked = false;
        let chatbot_chatsContainer = chatbot ? $('#chatbot-messages .chats-container') : $('.chats-container');
        let chatbot_chatsScroller = chatbot ? $('#chatbot-messages .chats-container') : $('.conversation-area');

        if (
            !constvalue ||
            constvalue.length === 0 ||
            constvalue.replace(/\s/g, '') === ''
        ) {
            return toastr.error('Please fill the message field');
        }

        const category_id = document.getElementById('chatbot_category_id');
        const chat_id = document.getElementById('chatbot_chat_id');
        const submitBtn = chatbot ? document.getElementById('send_chatbot_message_button') : document.getElementById('send_message_button');

        if (submitBtn.classList.contains('submitting')) return;

        submitBtn.disabled = true;
        submitBtn.classList.add('submitting');

        var formData = new FormData();
        formData.append('prompt', constvalue);
        formData.append('chat_id', chat_id.value);
        formData.append('category_id', category_id.value);
        if (!chatbot) {
            if (document.getElementById('realtime').checked) {
                formData.append('realtime', 1);
            } else {
                formData.append('realtime', 0);
            }
        }

        function onBeforePageUnload(e) {
            e.preventDefault();
            e.returnValue = '';
        }

        function onWindowScroll() {
            if (
                chatbot_chatsScroller[0].scrollTop + chatbot_chatsScroller[0].offsetHeight >=
                chatbot_chatsScroller[0].scrollHeight
            ) {
                chatbot_scrollLocked = true;
            } else {
                chatbot_scrollLocked = false;
            }
        }

        // to prevent from reloading when generating respond
        window.addEventListener('beforeunload', onBeforePageUnload);

        $.ajax({
            type: 'post',
            url: chatbot ? '/dashboard/user/openai/chat/chatbot-send' : '/dashboard/user/openai/chat/chat-send',
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                chatbot_chatsContainer = chatbot ? $('#chatbot-messages .chats-container') : $('.chats-container');
                const userBubbleTemplate = document
                    .querySelector('#chat_user_bubble')
                    .content.cloneNode(true);
                const aiBubbleTemplate = document
                    .querySelector('#chat_ai_bubble')
                    .content.cloneNode(true);
                const {chat_id, message_id} = data;

                //Here you can append user input to the chat area
                userBubbleTemplate.querySelector('.chat-content').innerHTML =
                    constvalue;
                chatbot_chatsContainer.append(userBubbleTemplate);

                for (let i = 0; i < prompt_images.length; i++) {
                    const chatImageBubbleTemplate = document
                        .querySelector('#chat_user_image_bubble')
                        .content.cloneNode(true);
                    chatImageBubbleTemplate.querySelector('.img-content').src =
                        prompt_images[i];
                    chatbot_chatsContainer.append(chatImageBubbleTemplate);
                }

                function implementChat(type, images) {
                    if (images == undefined) images = '';
                    let chat_send_url = chatbot ? '/dashboard/user/openai/chat/chatbot-send?chat_id=' : '/dashboard/user/openai/chat/chat-send?chat_id=';
                    const eventSource = new EventSource(
                        chat_send_url +
                        chat_id +
                        '&message_id=' +
                        message_id +
                        '&type=' +
                        type +
                        '&images=' +
                        images
                    );

                    //This is the div which the text will append continuously.
                    let responseText = '';

                    const aiBubbleWrapper = aiBubbleTemplate.firstElementChild;
                    aiBubbleWrapper.classList.add('loading');
                    aiBubbleTemplate.querySelector('.chat-content').innerHTML =
                        responseText;
                    chatbot_chatsContainer.append(aiBubbleTemplate);

                    aiBubbleWrapper.setAttribute('data-message-id', message_id);

                    chatbot_chatsScroller[0].scrollTo(0, chatbot_chatsScroller[0].scrollHeight);

                    chatbot_chatsScroller[0].addEventListener('scroll', onWindowScroll);

                    eventSource.onmessage = function (e) {
                        aiBubbleWrapper.classList.remove('loading');

                        if (e.data === '[DONE]') {
                            //This is the area when the chat ends.
                            eventSource.close();
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('submitting');
                            document.getElementById('chatbot_form').reset();

                            window.removeEventListener(
                                'beforeunload',
                                onBeforePageUnload
                            );
                        }
                        let txt = e.data;
                        if (txt !== undefined && e.data != '[DONE]') {
                            //This is the area which the text will append to the div
                            responseText += txt.split('/**')[0];
                            aiBubbleWrapper.querySelector(
                                '.chat-content'
                            ).innerHTML = responseText;

                            chatbot_scrollLocked && chatbot_chatsScroller[0].scrollTo(0, chatbot_chatsScroller[0].scrollHeight);
                        }
                    };

                    eventSource.onerror = function (e) {
                        //If error from the openai.
                        eventSource.close();
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('submitting');
                        aiBubbleWrapper.classList.remove('loading');
                        document.getElementById('chatbot_form').reset();

                        window.removeEventListener(
                            'beforeunload',
                            onBeforePageUnload
                        );
                        chatbot_chatsScroller[0].removeEventListener(
                            'scroll',
                            onWindowScroll
                        );
                    };
                }

                if (prompt_images.length == 0) {
                    implementChat('chat');
                } else {
                    let temp = [...prompt_images];
                    prompt_images = [];
                    chatbot_updatePrompt();
                    $.ajax({
                        type: 'POST',
                        url: '/images/upload',
                        data: {
                            images: temp,
                        },
                        success: function (result) {
                            implementChat('vision', result.path);
                        },
                    });
                }

                // if (prompt_images.length == 0) {
                // 	implementChat('chat');
                // } else {
                // 	var temp = [...prompt_images];
                // 	prompt_images = [];
                // 	chatbot_updatePrompts();
                // 	$.ajax({
                // 		type: "POST",
                // 		url: '/images/upload',
                // 		data: {
                // 			images: temp
                // 		},
                // 		success: function (result) {
                // 			implementChat('vision', result.path);
                // 		}
                // 	});
                // }
            },
            error: function (data) {
                var err = data.responseJSON.errors;
                if (err) {
                    $.each(err, function (index, value) {
                        toastr.error(value);
                    });
                } else {
                    toastr.error(data.responseJSON.message);
                }
                submitBtn.disabled = false;
                submitBtn.classList.remove('submitting');

                window.removeEventListener('beforeunload', onBeforePageUnload);
                chatbot_chatsScroller[0].removeEventListener('scroll', onWindowScroll);
            },
        });

        return false;
    }

    $('body').on('submit', '#chatbot_form', chatbot_submitForm);

    const chatbot_prompt = document.getElementById('chatbot_prompt');

    chatbot_prompt?.addEventListener('keypress', ev => {
        if (ev.code === 'Enter') {
            ev.preventDefault();
            $('#chatbot_form').submit();
        }
    });
})(jQuery);

function openChatBotArea(chat_id) {
    'use strict';

    document.getElementById('mainupscale_src') &&
    (document.getElementById('mainupscale_src').style.display = 'none');
    document.getElementById('sugg') &&
    (document.getElementById('sugg').style.display = 'none');

    var formData = new FormData();
    formData.append('chat_id', chat_id);

    $.ajax({
        type: 'post',
        url: '/dashboard/user/openai/chat/open-chatbot-area',
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            console.log(data);
            $('#chatbot-messages .chats-container').html(data.html);
            initChatBot();
            if (data.lastThreeMessage != '') {
                document.getElementById('mainupscale_src') &&
                (document.getElementById('mainupscale_src').style.display =
                    'none');
                document.getElementById('sugg') &&
                (document.getElementById('sugg').style.display = 'none');
            }
            setTimeout(function () {
                $('#chatbot-messages .chats-container').stop().animate({scrollTop: $('#chatbot-messages  .chats-container')[0].scrollHeight}, 200);
            }, 750);
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            if (err) {
                $.each(err, function (index, value) {
                    toastr.error(value);
                });
            } else {
                toastr.error(data.responseJSON.message);
            }
        },
    });
    return false;
}

function startNewChatBot() {
    'use strict';
    var formData = new FormData();
    let chatid = '';

    $.ajax({
        type: 'post',
        url: '/dashboard/user/openai/chat/start-new-chatbot',
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            chatid = data.id;
            $('#chatbot-trigger').attr('data-chatbot', data.chat.id);
            $('#chatbot_chat_id').val(data.chat.id);
            $('#chatbot_category_id').val(data.chat.openai_chat_category_id);
            $('#load_chat_area_container').html(data.html);
            $('#chat_sidebar_container').html(data.html2);
            // makeDocumentReadyAgain();

            initChatBot();

            openChatBotArea(data.chat.id);

            setTimeout(function () {
                $('.conversation-area').stop().animate({scrollTop: $('.conversation-area').outerHeight()}, 200);
            }, 750);
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            if (err) {
                $.each(err, function (index, value) {
                    toastr.error(value);
                });
            } else {
                toastr.error(data.responseJSON.message);
            }
        },
    });
    return false;
}

function closeChatbot(event) {
    if (event && event.code && event.code !== 'Escape') return;

    $('#chatbot-trigger').removeClass('lqd-is-active');
    $('#chatbot-wrapper').removeClass('lqd-is-active');

    document.removeEventListener('keydown', closeChatbot);
}

// check if clicked outside #chatbot-trigger and #chatbot-wrapper
$(document).on('click', function (e) {
    if (!$(e.target).closest('#chatbot-trigger, #chatbot-wrapper').length) {
        closeChatbot();
    }
});

$(document).on('click', '#chatbot-close', closeChatbot);

$(document).on('click', '#chatbot-trigger', function (e) {
    'use strict';
    e.preventDefault();

    let wrapper = $('#chatbot-wrapper');
    let chatbot = $(this).attr('data-chatbot');

    $(e.currentTarget).toggleClass('lqd-is-active');
    wrapper.toggleClass('lqd-is-active');

    if (wrapper.hasClass('lqd-is-active')) {
        const timeout = setTimeout(() => {
            document.querySelector('#chatbot_prompt')?.focus();
            clearTimeout(timeout);
        }, 50);

        document.addEventListener('keydown', closeChatbot);

        if (!chatbot) {
            startNewChatBot();
        } else {
            openChatBotArea(chatbot);
        }
    }
});