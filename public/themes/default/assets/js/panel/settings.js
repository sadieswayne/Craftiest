//admin.openai.custom.form
$(document).ready(function () {
    "use strict";
    if (!$.fn.select2) return;
    $('.select2').select2({
        tags: true
    });
});

function frontendSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('site_name', $("#site_name").val());
    formData.append('register_active', $("#register_active").val());
	formData.append('facebook_domain_verification', $("#facebook_domain_verification").val());
	formData.append('google_robots', $("#google_robots").val());
    formData.append('site_url', $("#site_url").val());
    formData.append('site_email', $("#site_email").val());
    formData.append('frontend_pricing_section', $("#frontend_pricing_section").val());
    formData.append('frontend_custom_templates_section', $("#frontend_custom_templates_section").val());
    formData.append('frontend_business_partners_section', $("#frontend_business_partners_section").val());
    formData.append('frontend_additional_url', $("#frontend_additional_url").val());
    formData.append('frontend_custom_css', $("#frontend_custom_css").val());
    formData.append('frontend_custom_js', $("#frontend_custom_js").val());
    formData.append('frontend_footer_facebook', $("#frontend_footer_facebook").val());
    formData.append('frontend_footer_twitter', $("#frontend_footer_twitter").val());
    formData.append('frontend_footer_instagram', $("#frontend_footer_instagram").val());

    formData.append('preheader_active', $("#preheader_active").val());
    formData.append('header_title', $("#header_title").val());
    formData.append('header_text', $("#header_text").val());
    formData.append('sign_in', $("#sign_in").val());
    formData.append('join_hub', $("#join_hub").val());

    formData.append('hero_subtitle', $("#hero_subtitle").val());
    formData.append('hero_title', $("#hero_title").val());
    formData.append('hero_title_text_rotator', $("#hero_title_text_rotator").val());
    formData.append('hero_description', $("#hero_description").val());
    formData.append('hero_scroll_text', $("#hero_scroll_text").val());
    formData.append('hero_button', $("#hero_button").val());
    formData.append('hero_button_url', $("#hero_button_url").val());
    formData.append('hero_button_type', $("#hero_button_type").val());



    formData.append('floating_button_small_text', $("#floating_button_small_text").val());
    formData.append('floating_button_bold_text', $("#floating_button_bold_text").val());
    formData.append('floating_button_link', $("#floating_button_link").val());
    formData.append('floating_button_active', $("#floating_button_active").val());

	if ($("#no_credit_cart_required") && faster_content_creation && over_5000_businesses) {
		formData.append('no_credit_cart_required', $("#no_credit_cart_required").val());
		formData.append('faster_content_creation', faster_content_creation.getValue());
		formData.append('over_5000_businesses', over_5000_businesses.getValue());
	}


    if (frontend_code_before_head) {
        formData.append('frontend_code_before_head', frontend_code_before_head.getValue());
    }
    if (frontend_code_before_body) {
        formData.append('frontend_code_before_body', frontend_code_before_body.getValue());
    }

    formData.append('footer_header', $("#footer_header").val());
    formData.append('footer_text_small', $("#footer_text_small").val());
    formData.append('footer_text', $("#footer_text").val());
    formData.append('footer_button_text', $("#footer_button_text").val());
    formData.append('footer_button_url', $("#footer_button_url").val());
    formData.append('footer_copyright', $("#footer_copyright").val());
	formData.append('footer_text_color', $("#footer_text_color").val());

	if ($('#hero_image').val() != 'undefined') {
		formData.append('hero_image', $('#hero_image').prop('files')[0]);
	}

    $.ajax({
        type: "post",
        url: "/dashboard/admin/frontend/settings-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function frontendSectionSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('features_active', $("#features_active").val());
    formData.append('features_title', $("#features_title").val());
	formData.append('features_subtitle', $("#features_subtitle").val());
	formData.append('marquee_items', $("#marquee_items").val());
    formData.append('features_description', $("#features_description").val());


    formData.append('generators_active', $("#generators_active").val());
    formData.append('generators_subtitle', $("#generators_subtitle").val());
    formData.append('generators_title', $("#generators_title").val());
    formData.append('generators_description', $("#generators_description").val());

    formData.append('advanced_features_section_title', $("#advanced_features_section_title").val());
    formData.append('advanced_features_section_description', $("#advanced_features_section_description").val());

    formData.append('plan_footer_text', $("#plan_footer_text").val());

    formData.append('who_is_for_active', $("#who_is_for_active").val());

    formData.append('custom_templates_active', $("#custom_templates_active").val());
    formData.append('custom_templates_subtitle_one', $("#custom_templates_subtitle_one").val());
    formData.append('custom_templates_subtitle_two', $("#custom_templates_subtitle_two").val());
    formData.append('custom_templates_title', $("#custom_templates_title").val());
    formData.append('custom_templates_description', $("#custom_templates_description").val());


    formData.append('tools_active', $("#tools_active").val());
    formData.append('tools_title', $("#tools_title").val());
	formData.append('tools_subtitle', $("#tools_subtitle").val());
    formData.append('tools_description', $("#tools_description").val());

	formData.append('custom_templates_learn_more_link', $("#custom_templates_learn_more_link").val());
	formData.append('custom_templates_learn_more_link_url', $("#custom_templates_learn_more_link_url").val());

    formData.append('how_it_works_active', $("#how_it_works_active").val());
    formData.append('how_it_works_title', $("#how_it_works_title").val());
	formData.append('how_it_works_subtitle', $("#how_it_works_subtitle").val());
	formData.append('how_it_works_description', $("#how_it_works_description").val());
	formData.append('how_it_works_link', $("#how_it_works_link").val());
	formData.append('how_it_works_link_label', $("#how_it_works_link_label").val());

    formData.append('testimonials_active', $("#testimonials_active").val());
    formData.append('testimonials_title', $("#testimonials_title").val());
	formData.append('testimonials_description', $("#testimonials_description").val());
    formData.append('testimonials_subtitle_one', $("#testimonials_subtitle_one").val());
    formData.append('testimonials_subtitle_two', $("#testimonials_subtitle_two").val());

    formData.append('pricing_active', $("#pricing_active").val());
    formData.append('pricing_title', $("#pricing_title").val());
	formData.append('pricing_subtitle', $("#pricing_subtitle").val());
    formData.append('pricing_description', $("#pricing_description").val());
    formData.append('pricing_save_percent', $("#pricing_save_percent").val());


    formData.append('faq_active', $("#faq_active").val());
    formData.append('faq_title', $("#faq_title").val());
    formData.append('faq_subtitle', $("#faq_subtitle").val());
    formData.append('faq_text_one', $("#faq_text_one").val());
    formData.append('faq_text_two', $("#faq_text_two").val());

	if (join_the_ranks) {
		formData.append('join_the_ranks', join_the_ranks.getValue());
	}

    formData.append('blog_active', $("#blog_active").val());
    formData.append('blog_title', $("#blog_title").val());
    formData.append('blog_subtitle', $("#blog_subtitle").val());
    formData.append('blog_posts_per_page', $("#blog_posts_per_page").val());
    formData.append('blog_button_text', $("#blog_button_text").val());
    formData.append('blog_a_title', $("#blog_a_title").val());
    formData.append('blog_a_subtitle', $("#blog_a_subtitle").val());
    formData.append('blog_a_description', $("#blog_a_description").val());
    formData.append('blog_a_posts_per_page', $("#blog_a_posts_per_page").val());


	var advancedFeaturesSection = document.querySelectorAll("[id^='advanced_features_title_']");
    advancedFeaturesSection.forEach(function (element, index) {
        var key = element.id.split('_').pop();
        formData.append('advanced_features_title_' + key, document.getElementById('advanced_features_title_' + key).value);
        formData.append('advanced_features_description_' + key, document.getElementById('advanced_features_description_' + key).value);

        var imageInput = document.getElementById('advanced_features_image_' + key);
        if (imageInput.files.length > 0) {
            formData.append('advanced_features_image_' + key, imageInput.files[0]);
        }
    });

	var comparison_section_items = document.querySelectorAll("[id^='comparison_section_item_label_']");
    comparison_section_items.forEach(function (element, index) {
        var key = element.id.split('_').pop();
        formData.append('comparison_section_item_label_' + key, document.getElementById('comparison_section_item_label_' + key).value);
        formData.append('comparison_section_item_others_' + key, document.getElementById('comparison_section_item_others_' + key).checked);
        formData.append('comparison_section_item_ours_' + key, document.getElementById('comparison_section_item_ours_' + key).checked);
    });

	var features_marquee = document.querySelectorAll("[id^='features_marquee_']");

    features_marquee.forEach(function (element, index) {
        var key = element.id.split('_').pop();
        formData.append('features_marquee_' + key, document.getElementById('features_marquee_' + key).value);
    });
	var footer_items = document.querySelectorAll("[id^='footer_item_']");

    footer_items.forEach(function (element, index) {
        var key = element.id.split('_').pop();
        formData.append('footer_item_' + key, document.getElementById('footer_item_' + key).value);
    });

	var banner_bottom_texts = document.querySelectorAll("[id^='banner_bottom_text_']");

    banner_bottom_texts.forEach(function (element, index) {
        var key = element.id.split('_').pop();
        formData.append('banner_bottom_text_' + key, document.getElementById('banner_bottom_text_' + key).value);
    });


    $.ajax({
        type: "post",
        url: "/dashboard/admin/frontend/section-settings-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function menuSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    var menuData = [];
    $("#menu-items .accordion-content").each(function () {
        var title = $(this).find(".menu-title").val();
        var url = $(this).find(".menu-url").val();
        var target = $(this).find(".menu-target").prop("checked");
		var mega_menu_id = $(this).find(".mega-menu-id option:selected").val();

        var data = {
            title: title,
            url: url,
            target: target,
			mega_menu_id: mega_menu_id,
        };

        menuData.push(data);
    });

	console.log(menuData);
    var jsonData = JSON.stringify(menuData);
    formData.append('menu_options', jsonData);

    $.ajax({
        type: "post",
        url: "/dashboard/admin/frontend/menu-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function generalSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('site_name', $("#site_name").val());
    formData.append('site_url', $("#site_url").val());
    formData.append('site_email', $("#site_email").val());
    formData.append('register_active', $("#register_active").val());
    formData.append('login_with_otp', $("#login_with_otp").val());
    formData.append('tour_seen', $("#tour_seen").val());

    collectCreditsToFormData(formData);

    formData.append('default_country', $("#default_country").val());
    formData.append('default_currency', $("#default_currency").val());
    formData.append('default_ai_engine', $("#default_ai_engine").val());
	formData.append('default_aw_image_engine', $("#default_aw_image_engine").val());
    formData.append('openai_default_stream_server', $("#openai_default_stream_server").is(":checked") ? 'frontend' : 'backend');
    formData.append('login_without_confirmation', $("#login_without_confirmation").is(":checked") ? 0 : 1);
    formData.append('daily_limit_count', $("#daily_limit_count").val());
    formData.append('daily_voice_limit_count', $("#daily_voice_limit_count").val());

    formData.append('notification_active', $("#notification_active").is(":checked") ? 1 : 0);
    formData.append('pusher_app_id', $("#pusher_app_id").val());
    formData.append('pusher_app_key', $("#pusher_app_key").val());
    formData.append('pusher_app_secret', $("#pusher_app_secret").val());
    formData.append('pusher_app_cluster', $("#pusher_app_cluster").val());

	formData.append('onetime_commission', $("#onetime_commission").is(":checked") ? 1 : 0);

    if ($('#limit').prop('checked')) {
        formData.append('limit', 1);
    } else {
        formData.append('limit', 0);
    }

    if ($('#voice_limit').prop('checked')) {
        formData.append('voice_limit', 1);
    } else {
        formData.append('voice_limit', 0);
    }

    formData.append('recaptcha_login', $("#recaptcha_login").is(":checked") ? 1 : 0);
    formData.append('recaptcha_register', $("#recaptcha_register").is(":checked") ? 1 : 0);
    formData.append('recaptcha_sitekey', $("#recaptcha_sitekey").val());
    formData.append('recaptcha_secretkey', $("#recaptcha_secretkey").val());

    formData.append('facebook_active', $("#facebook_active").is(":checked") ? 1 : 0);
    formData.append('google_active', $("#google_active").is(":checked") ? 1 : 0);
    formData.append('github_active', $("#github_active").is(":checked") ? 1 : 0);

    formData.append('metaTitleLocal', $("#metaTitleLocal").val());
    formData.append('metaDescLocal', $("#metaDescLocal").val());

    const logoFields = [
        'logo',
        'logo_dark',
        'logo_sticky',
        'logo_dashboard',
        'logo_dashboard_dark',
        'logo_collapsed',
        'logo_collapsed_dark',
        'logo_2x',
        'logo_dark_2x',
        'logo_sticky_2x',
        'logo_dashboard_2x',
        'logo_dashboard_dark_2x',
        'logo_collapsed_2x',
        'logo_collapsed_dark_2x',
        'favicon'
    ];

    logoFields.forEach(field => {
        const fileInput = $(`#${field}`);
        if (fileInput.val() !== 'undefined') {
            formData.append(field, fileInput.prop('files')[0]);
        }
    });


    formData.append('google_analytics_code', $("#google_analytics_code").val());
    formData.append('meta_title', $("#meta_title").val());
    formData.append('meta_description', $("#meta_description").val());
    formData.append('meta_keywords', $("#meta_keywords").val());

    if (dashboard_code_before_head) {
        formData.append('dashboard_code_before_head', dashboard_code_before_head.getValue());
    }
    if (dashboard_code_before_head) {
        formData.append('dashboard_code_before_body', dashboard_code_before_body.getValue());
    }

    formData.append('feature_ai_writer', $("#feature_ai_writer").is(":checked") ? 1 : 0);
    formData.append('feature_ai_image', $("#feature_ai_image").is(":checked") ? 1 : 0);
    formData.append('feature_ai_video', $("#feature_ai_video").is(":checked") ? 1 : 0);
    formData.append('feature_ai_chat', $("#feature_ai_chat").is(":checked") ? 1 : 0);
    formData.append('feature_ai_code', $("#feature_ai_code").is(":checked") ? 1 : 0);
    formData.append('feature_ai_speech_to_text', $("#feature_ai_speech_to_text").is(":checked") ? 1 : 0);
    formData.append('feature_ai_voiceover', $("#feature_ai_voiceover").is(":checked") ? 1 : 0);
    formData.append('feature_affilates', $("#feature_affilates").is(":checked") ? 1 : 0);
    formData.append('user_api_option', $("#user_api_option").is(":checked") ? 1 : 0);
    formData.append('feature_ai_article_wizard', $("#feature_ai_article_wizard").is(":checked") ? 1 : 0);
    formData.append('feature_ai_vision', $("#feature_ai_vision").is(":checked") ? 1 : 0);
    formData.append('feature_ai_pdf', $("#feature_ai_pdf").is(":checked") ? 1 : 0);
    formData.append('feature_ai_youtube', $("#feature_ai_youtube").is(":checked") ? 1 : 0);
    formData.append('feature_ai_rss', $("#feature_ai_rss").is(":checked") ? 1 : 0);
    formData.append('team_functionality', $("#team_functionality").is(":checked") ? 1 : 0);
    formData.append('feature_ai_advanced_editor', $("#feature_ai_advanced_editor").is(":checked") ? 1 : 0);
    formData.append('mobile_payment_active', $("#mobile_payment_active").is(":checked") ? 1 : 0);
    formData.append('feature_ai_chat_image', $("#feature_ai_chat_image").is(":checked") ? 1 : 0);
    formData.append('feature_ai_rewriter', $("#feature_ai_rewriter").is(":checked") ? 1 : 0);
    formData.append('feature_ai_voice_clone', $("#feature_ai_voice_clone").is(":checked") ? 1 : 0)
    formData.append('user_prompt_library', $("#user_prompt_library").is(":checked") ? 1 : 0)
    formData.append('select_model_option', $("#select_model_option").is(":checked") ? 1 : 0)
    formData.append('user_ai_image_prompt_library', $("#user_ai_image_prompt_library").is(":checked") ? 1 : 0)
	formData.append('ai_voice_isolator', $("#ai_voice_isolator").is(":checked") ? 1 : 0)
    formData.append('user_ai_writer_custom_templates', $("#user_ai_writer_custom_templates").is(":checked") ? 1 : 0)
    formData.append('chat_setting_for_customer', $("#chat_setting_for_customer").is(":checked") ? 1 : 0)
    formData.append('photo_studio', $("#photo_studio").is(":checked") ? 1 : 0)

    formData.append('mrrobot_name', $("#mrrobot_name").val());
    formData.append('mrrobot_search_words', $("#mrrobot_search_words").val());

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/general-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved_redirecting ||'Settings saved succesfully. Redirecting...')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
            setTimeout(function () {
                location.href = '/dashboard/admin/settings/general'
            }, 1000);
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function introductionSettingsSave() {
    "use strict";
    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var form = document.getElementById("settings_form");
    var formData = new FormData(form);

    $.ajax({
        type: "POST",
        url: form.action,
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved || 'Introductions saved successfully');
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function stripeSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('default_currency', $("#default_currency").val());
    formData.append('stripe_key', $("#stripe_key").val());
    formData.append('stripe_secret', $("#stripe_secret").val());
    formData.append('stripe_base_url', $("#stripe_base_url").val());

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/payment-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function xaiSettingsSave() {
	"use strict";

	document.getElementById("settings_button").disabled = true;
	document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

	const formData = new FormData();
	formData.append('xai_api_secret', $("#xai_api_secret").val());
	formData.append('xai_default_model', $("#xai_default_model").val());

	$.ajax({
		type: "post",
		url: "/dashboard/admin/settings/x-ai-save",
		data: formData,
		contentType: false,
		processData: false,
		success: function (data) {
			toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
			document.getElementById("settings_button").disabled = false;
			document.getElementById("settings_button").innerHTML = "Save";
		},
		error: function (data) {
			var err = data.responseJSON.errors;
			$.each(err, function (index, value) {
				toastr.error(value);
			});
			document.getElementById("settings_button").disabled = false;
			document.getElementById("settings_button").innerHTML = "Save";
		}
	});
	return false;
}
function openaiSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('openai_api_secret', $("#openai_api_secret").val());
    formData.append('dalle_default_model', $("#dalle_default_model").val());
    formData.append('openai_default_model', $("#openai_default_model").val());
    formData.append('openai_default_language', $("#openai_default_language").val());
    formData.append('openai_default_tone_of_voice', $("#openai_default_tone_of_voice").val());
    formData.append('openai_default_creativity', $("#openai_default_creativity").val());
    formData.append('openai_max_input_length', $("#openai_max_input_length").val());
    formData.append('openai_max_output_length', $("#openai_max_output_length").val());
    formData.append('openai_default_stream_server', $("#openai_default_stream_server").val());
	formData.append('hide_creativity_option', $("#hide_creativity_option").is(":checked") ? 1 : 0);
	formData.append('hide_tone_of_voice_option', $("#hide_tone_of_voice_option").is(":checked") ? 1 : 0);
	formData.append('hide_output_length_option', $("#hide_output_length_option").is(":checked") ? 1 : 0);
    formData.append('dalle_hidden', $("#dalle_hidden").is(":checked") ? 1 : 0);
    formData.append('realtime_voice_chat', $("#realtime_voice_chat").is(":checked") ? 1 : 0);
	formData.append('openai_file_search', $("#openai_file_search").is(":checked") ? 1 : 0)

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/openai-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function anthropicSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('anthropic_api_secret', $("#anthropic_api_secret").val());
    formData.append('anthropic_default_model', $("#anthropic_default_model").val());
    formData.append('anthropic_bedrock_model', $("#anthropic_bedrock_model").val());
    formData.append('anthropic_max_input_length', $("#anthropic_max_input_length").val());
    formData.append('anthropic_max_output_length', $("#anthropic_max_output_length").val());

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/anthropic",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function geminiSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('gemini_api_secret', $("#gemini_api_secret").val());
    formData.append('gemini_default_model', $("#gemini_default_model").val());
	formData.append('gemini_max_input_length', $("#gemini_max_input_length").val());
    formData.append('gemini_max_output_length', $("#gemini_max_output_length").val());

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/gemini",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function stablediffusionSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('stable_diffusion_api_key', $("#stable_diffusion_api_key").val());
    formData.append('stable_hidden', $("#stable_hidden").is(":checked") ? 1 : 0);
    formData.append('stablediffusion_default_language', $("#stablediffusion_default_language").val());
    formData.append('stablediffusion_default_model', $("#stablediffusion_default_model").val());
    formData.append('stablediffusion_bedrock_model', $("#stablediffusion_bedrock_model").val());

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/stablediffusion-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function unsplashSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('unsplash_api_key', $("#unsplash_api_key").val());
    // formData.append( 'stablediffusion_default_language', $( "#stablediffusion_default_language" ).val() );
    // formData.append( 'stablediffusion_default_model', $( "#stablediffusion_default_model" ).val() );

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/unsplashapi-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function pebblelySettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('pebblely_key', $("#pebblely_key").val());

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/pebblely-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function aimlapiSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('aimlapi_key', $("#aimlapi_key").val());
    formData.append('ai_music_model', $("#ai_music_model").val());

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/aimlapi-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function synthesiaSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('synthesia_secret_key', $("#synthesia_secret_key").val());

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/synthesia-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}
function pexelsSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('pexels_api_key', $("#pexels_api_key").val());

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/pexelsapi-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}
function pixabaySettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('pixabay_api_key', $("#pixabay_api_key").val());
    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/pixabayapi-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function serperSettingsSave() {
	"use strict";

	document.getElementById( "settings_button" ).disabled = true;
	document.getElementById( "settings_button" ).innerHTML = magicai_localize.please_wait;

	var formData = new FormData();
	formData.append( 'serper_api_key', $( "#serper_api_key" ).val() );

	var arrayKeysNeedToCeck = ['serper_seo_tool_improve', 'serper_seo_aw_sq', 'seo_ai_tool', 'serper_seo_aw_keyword', 'serper_seo_blog_title_desc', 'serper_seo_site_meta', 'serper_seo_aw_anlyze','serper_seo_aw_improve'];
	arrayKeysNeedToCeck.forEach(function (key) {
		if ($( "#" + key )) {
			formData.append( key, $( "#" + key ).is( ":checked" ) ? 1 : 0 );
		}
	});

	$.ajax( {
	    type: "post",
	 	url: "/dashboard/admin/settings/serperapi-save",
	 	data: formData,
	 	contentType: false,
	 	processData: false,
	 	success: function ( data ) {
	 		toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
	 		document.getElementById( "settings_button" ).disabled = false;
	 		document.getElementById( "settings_button" ).innerHTML = "Save";
	 	},
	 	error: function ( data ) {
			var err = data.responseJSON.errors;
			$.each( err, function ( index, value ) {
				toastr.error( value );
			} );
			document.getElementById( "settings_button" ).disabled = false;
			document.getElementById( "settings_button" ).innerHTML = "Save";
		}
	} );
	return false;
}
function clipdropSettingsSave() {
	"use strict";

	document.getElementById( "settings_button" ).disabled = true;
	document.getElementById( "settings_button" ).innerHTML = magicai_localize.please_wait;

	var formData = new FormData();
	formData.append( 'clipdrop_api_key', $( "#clipdrop_api_key" ).val() );
	// formData.append( 'stablediffusion_default_language', $( "#stablediffusion_default_language" ).val() );
	// formData.append( 'stablediffusion_default_model', $( "#stablediffusion_default_model" ).val() );

	$.ajax( {
	    type: "post",
	 	url: "/dashboard/admin/settings/clipdrop-save",
	 	data: formData,
	 	contentType: false,
	 	processData: false,
	 	success: function ( data ) {
	 		toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
	 		document.getElementById( "settings_button" ).disabled = false;
	 		document.getElementById( "settings_button" ).innerHTML = "Save";
	 	},
	 	error: function ( data ) {
			var err = data.responseJSON.errors;
			$.each( err, function ( index, value ) {
				toastr.error( value );
			} );
			document.getElementById( "settings_button" ).disabled = false;
			document.getElementById( "settings_button" ).innerHTML = "Save";
		}
	} );
	return false;
}

function ttsSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    if ($("#feature_tts_elevenlabs").is(":checked") && ($("#feature_tts_google").is(":checked") || $("#feature_tts_openai").is(":checked"))) {
        toastr.warning("Cannot use Elevenlabs TTS with Google TTS or OpenAI TTS");
        document.getElementById("settings_button").disabled = false;
        document.getElementById("settings_button").innerHTML = "Save";
        return false;
    }

    var formData = new FormData();
    formData.append('feature_tts_google', $("#feature_tts_google").is(":checked") ? 1 : 0);
    formData.append('feature_tts_openai', $("#feature_tts_openai").is(":checked") ? 1 : 0);
    formData.append('feature_tts_elevenlabs', $("#feature_tts_elevenlabs").is(":checked") ? 1 : 0);
    formData.append('elevenlabs_api_key', $("#elevenlabs_api_key").val());

    if ($("#azure_api_key")) {
        formData.append('feature_tts_azure', $("#feature_tts_azure").is(":checked") ? 1 : 0);
        formData.append('azure_api_key', $("#azure_api_key").val());
        formData.append('azure_region', $("#azure_region").val());
    }

	if ($("#speechify_api_key")) {
		formData.append('feature_tts_speechify', $("#feature_tts_speechify").is(":checked") ? 1 : 0);
		formData.append('speechify_api_key', $("#speechify_api_key").val());
	}

    formData.append('gcs_file', $("#gcs_file").val());
    formData.append('gcs_name', $("#gcs_name").val());

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/tts-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function apiKeysSettingsSave() {
    'use strict';
    document.getElementById('settings_button').disabled = true;
    document.getElementById('settings_button').innerHTML =
	magicai_localize.please_wait;
    var formData = new FormData();
	formData.append('api_keys', $("#openai_api_keys").val());
    formData.append('anthropic_api_keys', $('#anthropic_api_keys').val());
    formData.append('gemini_api_keys', $('#gemini_api_keys').val());
    $.ajax({
        type: 'post',
        url: '/dashboard/user/api-keys/update',
        data: formData,
        contentType: false,
        processData: false,
        success: function(data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully');
            document.getElementById('settings_button').disabled = false;
            document.getElementById('settings_button').innerHTML = 'Save';
        },
        error: function(data) {
            var err = data.responseJSON.errors;
            $.each(err, function(index, value) {
                toastr.error(value);
            });
            document.getElementById('settings_button').disabled = false;
            document.getElementById('settings_button').innerHTML = 'Save';
        }
});
    return false;
}
function smtpSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('smtp_host', $("#smtp_host").val());
    formData.append('smtp_port', $("#smtp_port").val());
    formData.append('smtp_username', $("#smtp_username").val());
    formData.append('smtp_password', $("#smtp_password").val());
    formData.append('smtp_email', $("#smtp_email").val());
    formData.append('smtp_sender_name', $("#smtp_sender_name").val());
    formData.append('smtp_encryption', $("#smtp_encryption").val());

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/smtp-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function privacySettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('privacy_enable', $("#privacy_enable").is(":checked") ? 1 : 0);
    formData.append('privacy_enable_login', $("#privacy_enable_login").is(":checked") ? 1 : 0);
    formData.append('privacy_content', tinymce.get("privacy_content").getContent());
    formData.append('terms_content', tinymce.get("terms_content").getContent());
    formData.append('termsLocal', $("#termsLocal").val());
    formData.append('privacyLocal', $("#privacyLocal").val());


    console.log(formData);

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/privacy-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function faqCreateOrUpdate(faq_id) {
    "use strict";

    document.getElementById("faq_button").disabled = true;
    document.getElementById("faq_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('question', $("#question").val());
    formData.append('answer', $("#answer").val());
    formData.append('faq_id', faq_id);

    $.ajax({
        type: "post",
        url: "/dashboard/admin/frontend/faq/action/save",
        data: formData,
        contentType: false,
        processData: false,
        success: function () {
            toastr.success(magicai_localize?.faq_saved ||'Faq saved succesfully. Redirecting')
            setTimeout(function () {
                location.href = "/dashboard/admin/frontend/faq"
            }, 750);
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("faq_button").disabled = false;
            document.getElementById("faq_button").innerHTML = "Save";
        }
    });
    return false;
}

function toolsCreateOrUpdate(item_id) {
    "use strict";

    document.getElementById("item_button").disabled = true;
    document.getElementById("item_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('title', $("#title").val());
    formData.append('description', $("#description").val());
	formData.append('buy_link', $("#buy_link").val());
	formData.append('buy_link_url', $("#buy_link_url").val());
	formData.append('learn_more_link', $("#learn_more_link").val());
	formData.append('learn_more_link_url', $("#learn_more_link_url").val());

    if ($('#image').val() != 'undefined') {
        formData.append('image', $('#image').prop('files')[0]);
    }
    formData.append('item_id', item_id);

    $.ajax({
        type: "post",
        url: "/dashboard/admin/frontend/tools/action/save",
        data: formData,
        contentType: false,
        processData: false,
        success: function () {
            toastr.success(magicai_localize?.item_saved ||'Item saved succesfully. Redirecting')
            setTimeout(function () {
                location.href = "/dashboard/admin/frontend/tools"
            }, 750);
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("item_button").disabled = false;
            document.getElementById("item_button").innerHTML = "Save";
        }
    });
    return false;
}

function futureCreateOrUpdate(item_id) {
    "use strict";

    document.getElementById("item_button").disabled = true;
    document.getElementById("item_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('title', $("#title").val());
    formData.append('description', $("#description").val());
    formData.append('image', $("#image").val());
    formData.append('item_id', item_id);

    $.ajax({
        type: "post",
        url: "/dashboard/admin/frontend/future/action/save",
        data: formData,
        contentType: false,
        processData: false,
        success: function () {
            toastr.success(magicai_localize?.item_saved ||'Item saved succesfully. Redirecting')
            setTimeout(function () {
                location.href = "/dashboard/admin/frontend/future"
            }, 750);
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("item_button").disabled = false;
            document.getElementById("item_button").innerHTML = "Save";
        }
    });
    return false;
}


function whoisCreateOrUpdate(item_id) {
    "use strict";

    document.getElementById("item_button").disabled = true;
    document.getElementById("item_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('title', $("#title").val());
    formData.append('color', $("#color").val());
    formData.append('item_id', item_id);

    $.ajax({
        type: "post",
        url: "/dashboard/admin/frontend/whois/action/save",
        data: formData,
        contentType: false,
        processData: false,
        success: function () {
            toastr.success(magicai_localize?.item_saved ||'Item saved succesfully. Redirecting')
            setTimeout(function () {
                location.href = "/dashboard/admin/frontend/whois"
            }, 750);
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("item_button").disabled = false;
            document.getElementById("item_button").innerHTML = "Save";
        }
    });
    return false;
}

function generatorlistCreateOrUpdate(item_id) {
    "use strict";

    document.getElementById("item_button").disabled = true;
    document.getElementById("item_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('menu_title', $("#menu_title").val());
    formData.append('subtitle_one', $("#subtitle_one").val());
    formData.append('subtitle_two', $("#subtitle_two").val());
    formData.append('title', $("#title").val());
    formData.append('text', $("#text").val());
    formData.append('image_title', $("#image_title").val());
    formData.append('image_subtitle', $("#image_subtitle").val());
	formData.append('icon', $("#icon").val());
    formData.append('color', $("#color").val());
    if ($('#image').val() != 'undefined') {
        formData.append('image', $('#image').prop('files')[0]);
    }
    formData.append('item_id', item_id);

    $.ajax({
        type: "post",
        url: "/dashboard/admin/frontend/generatorlist/action/save",
        data: formData,
        contentType: false,
        processData: false,
        success: function () {
            toastr.success(magicai_localize?.item_saved ||'Item saved succesfully. Redirecting')
            setTimeout(function () {
                location.href = "/dashboard/admin/frontend/generatorlist"
            }, 750);
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("item_button").disabled = false;
            document.getElementById("item_button").innerHTML = "Save";
        }
    });
    return false;
}
