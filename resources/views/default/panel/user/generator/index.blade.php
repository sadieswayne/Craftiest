@extends('panel.layout.app', [
    'disable_header' => true,
    'disable_titlebar' => true,
    'disable_navbar' => true,
    'disable_footer' => true,
    'disable_floating_menu' => true,
    'disable_mobile_bottom_menu' => true,
    'disable_tblr' => true,
])

@section('content')
    <div
        class="lqd-generator-v2 group/generator [--editor-bb-h:40px] [--editor-tb-h:50px] [--sidebar-w:min(440px,90vw)]"
        :class="{ 'lqd-generator-sidebar-collapsed': sideNavCollapsed }"
        x-data="generatorV2"
    >
        @include('panel.user.generator.components.sidebar')
        @include('panel.user.generator.components.editor')
    </div>
@endsection
