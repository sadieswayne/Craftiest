<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Traits\EnumTo;
use App\Enums\Traits\StringBackedEnumTrait;

enum Permissions: string implements Contracts\WithStringBackedEnum
{
    use EnumTo;
    use StringBackedEnumTrait;

    case MARKETPLACE = 'marketplace';
    case THEMES = 'themes';
    case USER_MANAGEMENT = 'user_management';
    case ANNOUNCEMENTS = 'announcements';
    case GOOGLE_ADSENSE = 'google_adsense';
    case SUPPORT_REQUESTS = 'support_requests';
    case TEMPLATES = 'templates';
    case CHAT_SETTINGS = 'chat_settings';
    case FRONTEND = 'frontend';
    case FINANCE = 'finance';
    case PAGES = 'pages';
    case BLOG = 'blog';
    case AFFILIATES_ADMIN = 'affiliates_admin';
    case COUPONS_ADMIN = 'coupons_admin';
    case EMAIL_TEMPLATES = 'email_templates';
    case INTRODUCTIONS = 'introductions';
    case MAILCHIMP_NEWSLETTER = 'mailchimp_newsletter';
    case HUBSPOT = 'hubspot';
    case API_INTEGRATION = 'api_integration';
    case SETTINGS = 'settings';
    case SITE_HEALTH = 'site_health';
    case LICENCE = 'license';
    case UPDATE = 'update';
    case MENU_SETTINGS = 'menu_setting';
    case VIP_CHAT_WIDGET = 'VIP_CHAT_WIDGET';

    public function label(): string
    {
        return match ($this) {
            self::MARKETPLACE             => __('Marketplace'),
            self::THEMES                  => __('Themes'),
            self::USER_MANAGEMENT         => __('User Management'),
            self::ANNOUNCEMENTS           => __('Announcements'),
            self::GOOGLE_ADSENSE          => __('Google Adsense'),
            self::SUPPORT_REQUESTS        => __('Support Requests'),
            self::TEMPLATES               => __('Templates'),
            self::CHAT_SETTINGS           => __('Chat Settings'),
            self::FRONTEND                => __('Frontend'),
            self::FINANCE                 => __('Finance'),
            self::PAGES                   => __('Pages'),
            self::BLOG                    => __('Blog'),
            self::AFFILIATES_ADMIN        => __('Affiliates Admin'),
            self::COUPONS_ADMIN           => __('Coupons Admin'),
            self::EMAIL_TEMPLATES         => __('Email Templates'),
            self::INTRODUCTIONS           => __('Introductions'),
            self::MAILCHIMP_NEWSLETTER    => __('Mailchimp Newsletter'),
            self::HUBSPOT                 => __('Hubspot'),
            self::API_INTEGRATION         => __('Api Integration'),
            self::SETTINGS                => __('Settings'),
            self::SITE_HEALTH             => __('Site Health'),
            self::LICENCE                 => __('Licence'),
            self::UPDATE                  => __('Update'),
            self::MENU_SETTINGS           => __('Menu Settings'),
            self::VIP_CHAT_WIDGET         => __('VIP Chat Widget'),
        };
    }
}
