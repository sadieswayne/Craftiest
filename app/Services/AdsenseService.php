<?php

use App\Models\Ad;

function adsense($type)
{
    $adsenses = Ad::getCache(function () {
        return Ad::query()->where('status', true)->get();
    });

    return $adsenses->where('type', $type)->first();
}

function adsense_header()
{
    if (adsense('landing-header-section')) {
        return adsense('landing-header-section')->code;
    }
}

function adsense_features_728x90()
{
    if (adsense('landing-features-section-728x90')) {
        return '<center>
                    <div class="google-ads-728 mb-6">' . adsense('landing-features-section-728x90')->code . '</div>
                </center>';
    }
}
function adsense_templates_728x90()
{
    if (adsense('landing-templates-section-728x90')) {
        return '<center>
                    <div class="google-ads-728 mb-6">' . adsense('landing-templates-section-728x90')->code . '</div>
                </center>';
    }
}
function adsense_tools_728x90()
{
    if (adsense('landing-tools-section-728x90')) {
        return '<center>
                    <div class="google-ads-728 mb-6">' . adsense('landing-tools-section-728x90')->code . '</div>
                </center>';
    }
}
function adsense_how_it_works_728x90()
{
    if (adsense('landing-how-it-works-section-728x90')) {
        return '<center>
                    <div class="google-ads-728 mb-6">' . adsense('landing-how-it-works-section-728x90')->code . '</div>
                </center>';
    }
}
function adsense_testimonials_728x90()
{
    if (adsense('landing-testimonials-section-728x90')) {
        return '<center>
                    <div class="google-ads-728 mb-6">' . adsense('landing-testimonials-section-728x90')->code . '</div>
                </center>';
    }
}
function adsense_pricing_728x90()
{
    if (adsense('landing-pricing-section-728x90')) {
        return '<center>
                    <div class="google-ads-728 mb-6">' . adsense('landing-pricing-section-728x90')->code . '</div>
                </center>';
    }
}
function adsense_faq_728x90()
{
    if (adsense('landing-faq-section-728x90')) {
        return '<center>
                    <div class="google-ads-728 mb-6">' . adsense('landing-faq-section-728x90')->code . '</div>
                </center>';
    }
}
