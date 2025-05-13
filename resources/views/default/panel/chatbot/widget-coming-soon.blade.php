@php
    $chatbot = App\Models\Chatbot\Chatbot::query()
        ->where('id', $settings_two->chatbot_template)
        ->first();

    if (! $chatbot)
    {
        return;
    }

    $userChats = \App\Models\UserOpenaiChat::query()
        ->where('user_id', auth()->id())
        ->where('is_chatbot', true)
        ->get();

@endphp



<div id="magicai-chatbot-widget" class="{{ $settings_two->chatbot_position ?: 'bottom-left' }}">
    <div class="magicai-chatbot-widget--chat active-message-list bg-white active">
        <div class="magicai-chatbot-widget--header border-b-amber-700">
            <div class="title">MagicAI Bot<br><span>Theme Support</span>
            </div>
            <div class="message-list">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M21 17.9995V19.9995H3V17.9995H21ZM6.59619 3.90332L8.01041 5.31753L4.82843 8.49951L8.01041 11.6815L6.59619 13.0957L2 8.49951L6.59619 3.90332ZM21 10.9995V12.9995H12V10.9995H21ZM21 3.99951V5.99951H12V3.99951H21Z"></path></svg>
            </div>
        </div>
        <div class="loader">
            <div class="bar"></div>
            <div class="bar" style="width:100%"></div>
            <div class="bar" style="width:40%"></div>
        </div>
        <div class="magicai-chatbot-widget--message-list">
            @foreach($userChats as $userChat)
                <div class="magicai-chatbot-widget--message-list--item" data-id="{{ $userChat->id }}">
                    <img src="{{ $chatbot->image ? '/uploads/'.$chatbot->image : '/assets/img/chat-default.jpg' }}" class="svg" alt="ChatBot">
                    <div class="magicai-chatbot-widget--message-list--item-title">
                        You are an AI assistant providing concise,…<br>
                        <span>{{ $chatbot->title }} •  {{ $chatbot->role }}</span>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M13.1717 12.0007L8.22192 7.05093L9.63614 5.63672L16.0001 12.0007L9.63614 18.3646L8.22192 16.9504L13.1717 12.0007Z"></path></svg>
                </div>
            @endforeach
        </div>
        <button type="button" class="btn magicai-btn start-new-chat">Start New Chat</button>
        <div class="magicai-chatbot-widget--messages"></div>
        <form action="">
            <input type="text" name="prompt" id="prompt" placeholder="Type and hit..." required="" autocomplete="off">
            <button type="submit" class="start">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" width="18" height="18"><path d="M3.5 1.3457C3.58425 1.3457 3.66714 1.36699 3.74096 1.4076L22.2034 11.562C22.4454 11.695 22.5337 11.9991 22.4006 12.241C22.3549 12.3241 22.2865 12.3925 22.2034 12.4382L3.74096 22.5925C3.499 22.7256 3.19497 22.6374 3.06189 22.3954C3.02129 22.3216 3 22.2387 3 22.1544V1.8457C3 1.56956 3.22386 1.3457 3.5 1.3457ZM5 4.38261V11.0001H10V13.0001H5V19.6175L18.8499 12.0001L5 4.38261Z"></path></svg>
            </button>
        </form>
    </div>
    <div class="magicai-chatbot-widget--trigger loaded bg-white">
        <img src="http://wordpress.test/wp-content/plugins/magicai-wp/assets/img/logo.svg" class="svg"  alt="ChatBot">
    </div>
</div>