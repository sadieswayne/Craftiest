<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Traits\EnumTo;
use App\Enums\Traits\StringBackedEnumTrait;

enum Introduction: string implements Contracts\WithStringBackedEnum
{
    use EnumTo;
    use StringBackedEnumTrait;

    case INITIALIZE = 'initialize';

    case LAST = 'last';
    case AFFILIATE_SEND = 'affiliate_send';
    case SELECT_PLAN = 'select_plan';
    case AI_WRITER = 'ai_writer';
    case AI_EDITOR = 'ai_editor';
    case AI_DOCUMENT = 'documents';
    case AI_EXT_CHATBOT = 'ext_chat_bot';
    case AI_EXT_CHATBOT_AGENT = 'ext_chat_bot_agent';
    case AI_VIDEO = 'ai_video';

    case AI_VIDEO_TO_VIDEO = 'ai_video_to_video';
    case AI_ARTICLE_WIZARD = 'ai_article_wizard';
    case AI_VISION = 'ai_vision';
    case AI_REWRITER = 'ai_rewriter';
    case AI_CHAT_IMAGE = 'ai_chat_image';
    case AI_CHAT = 'ai_chat_all';
    case AI_YOUTUBE = 'ai_youtube';
    case AI_RSS = 'ai_rss';
    case AI_SPEECH_TO_TEXT = 'ai_speech_to_text';
    case AI_VOICEOVER = 'ai_voiceover';
    case AI_VOICEOVER_CLONE = 'ai_voiceover_clone';
    case TEAM_MENU = 'team_menu';
    case BRAND_VOICE = 'brand_voice';
    case ADVANCED_IMAGE = 'advanced_image';
    case AI_VOICE_ISOLATOR = 'ai_voice_isolator';
    case AI_AVATAR = 'ai_avatar';
    case AI_AVATAR_PRO = 'ai_persona';
    case AI_FALL_VIDEO = 'ai_video_pro';
    case AI_REPLICA = 'ai_replica';
    case AI_MUSIC = 'ai_music';
    case AI_PRODUCT_SHOT = 'ai_product_shot';
    case USER_API_KEYS = 'user_api_keys';
    case AFFILIATES = 'affiliates';
    case SUPPORT = 'support';
    case INTEGRATION = 'integration';
    case AI_IMAGE = 'ai_image';
    case AI_PDF = 'ai_pdf';
    case AI_CODE = 'ai_code';
    case ADMIN_DASHBOARD = 'admin_dashboard';
    case ADMIN_MARKETPLACE = 'marketplace';
    case ADMIN_THEMES = 'themes';
    case ADMIN_USER_MANAGEMENT = 'user_management';
    case ADMIN_ANNOUNCEMENTS = 'announcements';
    case ADMIN_GOOGLE_ADSENSE = 'site_promo';
    case ADMIN_SUPPORT_REQUEST = 'support_requests';
    case ADMIN_TEMPLATES = 'templates';
    case ADMIN_CHAT_SETTINGS = 'chat_settings';
    case ADMIN_FRONTEND = 'frontend';
    case ADMIN_FINANCE = 'finance';
    case ADMIN_PAGES = 'pages';
    case ADMIN_BLOG = 'blog';
    case ADMIN_AFFILIATES = 'affiliates_admin';
    case ADMIN_COUPONS = 'coupons_admin';
    case ADMIN_EMAIL_TEMPLATES = 'email_templates';
    case ADMIN_ONBOARDING_PRO = 'onboarding_pro_extension';
    case ADMIN_ONBOARDING = 'onboarding';
    case ADMIN_MAILCHIMP_NEWSLETTER = 'mailchimp_newsletter';
    case ADMIN_HUBSPOT_NEWSLETTER = 'hubspot';
    case ADMIN_API_INTEGRATION = 'api_integration';
    case ADMIN_SETTINGS = 'settings';
    case ADMIN_SITE_HEALTH = 'site_health';
    case ADMIN_LICENSE = 'license';
    case ADMIN_UPDATE = 'update';
    case ADMIN_MENU_SETTINGS = 'menu_setting';
    case SIDEBAR = 'sidebar';
    case DASHBOARD_FIRST = 'dashboard_first';
    case DASHBOARD_TWO = 'dashboard_two';
    case DASHBOARD_THREE = 'dashboard_three';
    case DASHBOARD_CHATBOT_ICON = 'dashboard_chatbot_icon';

    case SOCIAL_MEDIA = 'social_media';
    case AI_REALTIME_VOICE_CHAT = 'ai_realtime_voice_chat';

    public function label(): string
    {
        return match ($this) {
            self::AI_REALTIME_VOICE_CHAT          => __('Realtime Voice Chat'),
            self::INITIALIZE         	            => __('Onboarding'),
            self::LAST         		                 => __('Onboarding End'),
            self::AFFILIATE_SEND     	            => __('Affiliate'),
            self::SELECT_PLAN        	            => __('Select Plan'),
            self::AI_WRITER          	            => __('AI Writer'),
            self::AI_IMAGE           	            => __('AI Image'),
            self::AI_PDF             	            => __('AI File Chat'),
            self::AI_CODE            	            => __('AI Code'),
            self::AI_EDITOR          	            => __('AI Editor'),
            self::AI_DOCUMENT        	            => __('Documents'),
            self::AI_EXT_CHATBOT     	            => __('AI Bots'),
            self::AI_VIDEO     		                 => __('AI Video'),
            self::AI_ARTICLE_WIZARD  	            => __('AI Article Wizard'),
            self::AI_VISION          	            => __('AI Vision'),
            self::AI_REWRITER        	            => __('AI ReWriter'),
            self::AI_CHAT_IMAGE      	            => __('AI Chat Image'),
            self::AI_CHAT            	            => __('AI Chat'),
            self::AI_YOUTUBE        	             => __('AI YouTube'),
            self::AI_RSS             	            => __('AI RSS'),
            self::AI_SPEECH_TO_TEXT   	           => __('AI Speech to Text'),
            self::AI_VOICEOVER        	           => __('AI Voiceover'),
            self::AI_VOICEOVER_CLONE  	           => __('AI Voice Isolator'),
            self::AI_AVATAR  		                   => __('AI Avatar'),
            self::AI_AVATAR_PRO  	                => __('AI Persona'),
            self::ADVANCED_IMAGE  	               => __('Advanced Image Editor'),
            self::BRAND_VOICE  	                  => __('Brand Voice'),
            self::TEAM_MENU  	                    => __('Team'),
            self::AI_FALL_VIDEO                   => __('Fall Video'),
            self::AI_REPLICA  		                  => __('AI Replica'),
            self::AI_MUSIC  	  	                  => __('AI Music'),
            self::AI_PRODUCT_SHOT  	              => __('AI Product Photography'),
            self::USER_API_KEYS  	                => __('API Keys'),
            self::AFFILIATES  	  	                => __('Affiliates'),
            self::SUPPORT  	  	                   => __('Support'),
            self::INTEGRATION  	  	               => __('Integration'),
            self::ADMIN_DASHBOARD  	  	           => __('Dashboard'),
            self::ADMIN_MARKETPLACE  	            => __('Admin Marketplace'),
            self::ADMIN_THEMES  	  	              => __('Admin Themes'),
            self::ADMIN_USER_MANAGEMENT           => __('Admin User Management'),
            self::ADMIN_ANNOUNCEMENTS  	          => __('Admin Announcements'),
            self::ADMIN_GOOGLE_ADSENSE            => __('Admin Google Adsense'),
            self::ADMIN_SUPPORT_REQUEST           => __('Admin Suport Request'),
            self::ADMIN_TEMPLATES  		             => __('Admin Templates'),
            self::ADMIN_CHAT_SETTINGS  	          => __('Admin Chat Settings'),
            self::ADMIN_FRONTEND  		              => __('Admin Frontend'),
            self::ADMIN_FINANCE  		               => __('Admin Finance'),
            self::ADMIN_PAGES  		                 => __('Admin Pages'),
            self::ADMIN_BLOG  		                  => __('Admin Blog'),
            self::ADMIN_AFFILIATES                => __('Admin Affiliates'),
            self::ADMIN_COUPONS  		               => __('Admin Coupons'),
            self::ADMIN_EMAIL_TEMPLATES           => __('Admin Email Templates'),
            self::ADMIN_ONBOARDING_PRO            => __('Admin Onboarding Pro'),
            self::ADMIN_ONBOARDING  	             => __('Admin Onboarding'),
            self::ADMIN_MAILCHIMP_NEWSLETTER  	   => __('Admin Mailchimp Newsletter'),
            self::ADMIN_HUBSPOT_NEWSLETTER  	     => __('Admin Hubspot Newsletter'),
            self::ADMIN_API_INTEGRATION  		       => __('Admin Api Integration'),
            self::ADMIN_SETTINGS  		              => __('Admin Settings'),
            self::ADMIN_SITE_HEALTH  	            => __('Admin Site Health'),
            self::ADMIN_LICENSE  		               => __('Admin License'),
            self::ADMIN_UPDATE  		                => __('Admin Update'),
            self::ADMIN_MENU_SETTINGS  	          => __('Admin Menu Settings'),
            self::SIDEBAR  	      			             => __('Sidebar'),
            self::DASHBOARD_FIRST  	      			     => __('Dashboard First'),
            self::DASHBOARD_TWO  	      			       => __('Dashboard Two'),
            self::DASHBOARD_THREE  	      			     => __('Dashboard Three'),
            self::DASHBOARD_CHATBOT_ICON  	      	=> __('Dashboard Chatbot Icon'),
            self::SOCIAL_MEDIA  	      	          => __('Social Media'),
        };
    }
}
