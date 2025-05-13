@extends('layout.app')

@section('content')
    @include('landing-page.banner.section')

    @includeWhen($fSectSettings->features_active == 1, 'landing-page.features.section')

    @includeWhen($fSectSettings->generators_active == 1, 'landing-page.generators.section')

    @includeWhen($fSectSettings->who_is_for_active == 1, 'landing-page.who-is-for.section')

    @includeWhen($fSectSettings->custom_templates_active == 1, 'landing-page.custom-templates.section')

    @includeWhen($fSectSettings->tools_active == 1, 'landing-page.tools.section')

    @includeWhen($fSectSettings->how_it_works_active == 1, 'landing-page.how-it-works.section')

    @includeWhen($fSectSettings->testimonials_active == 1, 'landing-page.testimonials.section')

    @includeWhen($fSectSettings->pricing_active == 1, 'landing-page.pricing.section')

    @includeWhen($fSectSettings->faq_active == 1, 'landing-page.faq.section')

    @includeWhen($fSectSettings->blog_active == 1, 'landing-page.blog.section')

    @includeWhen($setting->gdpr_status == 1, 'landing-page.gdpr')
@endsection

@if ($app_is_demo)
    <x-demo-switcher themes-type="Frontend" />
@endif
