<!-- AJAX CALLS -->
<script src="{{ custom_theme_url('/assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ custom_theme_url('/assets/libs/toastr/toastr.min.js') }}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>

@if (in_array($settings_two->chatbot_status, ['dashboard', 'both']) &&
        !activeRoute('dashboard.user.openai.chat.chat') &&
        !(route('dashboard.user.openai.generator.workbook', 'ai_vision') == url()->current()) &&
        !(route('dashboard.user.openai.generator.workbook', 'ai_chat_image') == url()->current()) &&
        !(route('dashboard.user.openai.generator.workbook', 'ai_pdf') == url()->current()))
    @if (
        !Route::has('dashboard.user.openai.webchat.workbook') ||
            (Route::has('dashboard.user.openai.webchat.workbook') && route('dashboard.user.openai.webchat.workbook') !== url()->current()))
        <script src="{{ custom_theme_url('/assets/js/panel/openai_chatbot.js') }}"></script>
    @endif
@endif

<script>
    var magicai_localize = {
        @foreach (json_decode(file_get_contents(base_path('lang/en.json')), true) as $key => $value)
            @php
                $safeKey = preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($key));
                if (is_numeric(substr($safeKey, 0, 1))) {
                    $safeKey = '_' . $safeKey;
                }
            @endphp
            {{ $safeKey }}: @json($value),
        @endforeach
    };
    var magicai_localize_second_part = {
        signup: @json(__('Sign Up')),
        please_wait: @json(__('Please Wait...')),
        sign_in: @json(__('Sign In')),
        login_redirect: @json(__('Login Successful, Redirecting...')),
        register_redirect: @json(__('Registration is complete. Redirecting...')),
        password_reset_link: @json(__('Password reset link sent succesfully. Please also check your spam folder.')),
        password_reset_done: @json(__('Password succesfully changed.')),
        password_reset: @json(__('Reset Password')),
        missing_email: @json(__('Please enter your email address.')),
        missing_password: @json(__('Please enter your password.')),
        content_copied_to_clipboard: @json(__('Content copied to clipboard.')),
        text_content_copied_to_clipboard: @json(__('Plain text content copied to clipboard.')),
        md_content_copied_to_clipboard: @json(__('Markdown content copied to clipboard.')),
        html_content_copied_to_clipboard: @json(__('HTML content copied to clipboard.')),
        new_chat_conversation_successfully: @json(__('New conversation created successfully.')),
        conversation_deleted_successfully: @json(__('Conversation deleted successfully.')),
        analyze_file_begin: @json(__('Analyzing uploaded file.')),
        analyze_file_finish: @json(__('Analyzing file is done. You can start the conversation.')),
        please_active_magicai: @json(__('Please Active The MagicAI')),
        please_enter_url: @json(__('Please enter the URL')),
        you_cannot_withdrawal: @json(__('You cannot withdrawal with this amount. Please check')),
        error_while_sending: @json(__('Error while sending information. Please contact us.')),
        please_fill_message: @json(__('Please fill the message field')),
        api_connection_error: @json(__('Api Connection Error. You hit the rate limites of openai requests. Please check your Openai API Key')),
        api_connection_error_admin: @json(__('Api Connection Error. Please contact system administrator via Support Ticket. Error is: API Connection failed due to API keys')),
        file_size_exceed: @json(__('This file exceed the limit of file upload')),
        something_wrong: @json(__('Something went wrong. Please reload the page and try it again')),
        fill_all_fields: @json(__('Please fill all fields in User Group Input areas')),
        at_least_one_group: @json(__('You must have at least one group of inputs')),
        workbook_error: @json(__('Workbook Error')),
        settings_saved: @json(__('Settings saved successfully. Redirecting...')),
        request_sent: @json(__('Request Sent Succesfully')),
        invitation_sent: @json(__('Invitation Sent Succesfully!')),
        page_saved: @json(__('Page Saved Succesfully')),
        template_saved: @json(__('Template Saved Succesfully')),
        saved: @json(__('Saved Succesfully')),
        client_saved: @json(__('Client Saved Succesfully. Redirecting...')),
        plan_saved: @json(__('Plan Saved Succesfully. Redirecting...')),
        how_it_works_step_saved: @json(__('How it Works Step Saved Succesfully. Redirecting...')),
        how_it_works_bottom_line_saved: @json(__('How it Works Bottom Line updated successfully. Redirecting...')),
        addon_installed: @json(__('Add-on installed succesfully.')),
        addon_uninstalled: @json(__('Add-on uninstalled succesfully.')),
        status_changed: @json(__('Status changed succesfully')),
        chat_template_saved: @json(__('Chat Template Saved Succesfully')),
        settings_saved: @json(__('Settings saved succesfully')),
        settings_saved_redirecting: @json(__('Settings saved succesfully. Redirecting...')),
        faq_saved: @json(__('Faq saved succesfully. Redirecting')),
        item_saved: @json(__('Item saved succesfully. Redirecting')),
        support_ticket_created: @json(__('Support Ticket Created Succesfully. Redirecting...')),
        message_sent: @json(__('Message sent succesfully. Please Wait')),
        testimonial_saved: @json(__('Testimonial Saved Succesfully. Redirecting...')),
        user_saved: @json(__('User saved succesfully')),
        workbook_saved: @json(__('Workbook saved succesfully')),
        code_copied: @json(__('Code copied to clipboard')),
        content_copied: @json(__('Content copied to clipboard')),
        search: @json(__('Search...')),
        what_would_you_like_to_do: @json(__('What would you like to do?')),
        rewrite: @json(__('Rewrite')),
        summarize: @json(__('Summarize')),
        make_it_longer: @json(__('Make it Longer')),
        make_it_shorter: @json(__('Make it Shorter')),
        improve_writing: @json(__('Improve Writing')),
        translate_to: @json(__('Translate to')),
        search: @json(__('Search')),
        simplify: @json(__('Simplify')),
        change_style_to: @json(__('Change Style to')),
        change_tone_to: @json(__('Change Tone to')),
        fix_grammatical_mistakes: @json(__('Fix Grammatical Mistakes')),
		conversation_unpinned: @json(__('Chat unpinned successfully.')),
		conversation_pinned: @json(__('Chat pinned successfully.')),
		conversation_pin_error: @json(__('An error occurred while updating the pin status.')),
    }
    Object.assign(magicai_localize, magicai_localize_second_part);
</script>

<!-- PAGES JS-->
@guest()
    <script src="{{ custom_theme_url('/assets/js/panel/login_register.js') }}"></script>
@endguest

@auth
    <script src="{{ custom_theme_url('/assets/js/tabler.min.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/js/panel/search.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/list.js/dist/list.js') }}"></script>
@endauth
