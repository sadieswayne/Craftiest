@php
    use App\Domains\Entity\Enums\EntityEnum;
    $dalle_select_options = [
        'size' => [
            '256x256' => '256 x 256',
            '512x512' => '512 x 512',
            '1024x1024' => '1024 x 1024',
        ],
        'image_style' => [
            '' => 'None',
            '3d_render' => '3D Render',
            'anime' => 'Anime',
            'ballpoint_pen' => 'Ballpoint Pen Drawing',
            'bauhaus' => 'Bauhaus',
            'cartoon' => 'Cartoon',
            'clay' => 'Clay',
            'contemporary' => 'Contemporary',
            'cubism' => 'Cubism',
            'cyberpunk' => 'Cyberpunk',
            'glitchcore' => 'Glitchcore',
            'impressionism' => 'Impressionism',
            'isometric' => 'Isometric',
            'line' => 'Line Art',
            'low_poly' => 'Low Poly',
            'minimalism' => 'Minimalism',
            'modern' => 'Modern',
            'origami' => 'Origami',
            'pencil' => 'Pencil Drawing',
            'pixel' => 'Pixel',
            'pointillism' => 'Pointillism',
            'pop' => 'Pop',
            'realistic' => 'Realistic',
            'renaissance' => 'Renaissance',
            'retro' => 'Retro',
            'steampunk' => 'Steampunk',
            'sticker' => 'Sticker',
            'ukiyo' => 'Ukiyo',
            'vaporwave' => 'Vaporwave',
            'vector' => 'Vector',
            'watercolor' => 'Watercolor',
        ],
        'image_lighting' => [
            '' => 'None',
            'ambient' => 'Ambient',
            'backlight' => 'Backlight',
            'blue_hour' => 'Blue Hour',
            'cinematic' => 'Cinematic',
            'cold' => 'Cold',
            'dramatic' => 'Dramatic',
            'foggy' => 'Foggy',
            'golden_hour' => 'Golden Hour',
            'hard' => 'Hard',
            'natural' => 'Natural',
            'neon' => 'Neon',
            'studio' => 'Studio',
            'warm' => 'Warm',
        ],
        'image_mood' => [
            '' => 'None',
            'aggressive' => 'Aggressive',
            'angry' => 'Angry',
            'boring' => 'Boring',
            'bright' => 'Bright',
            'calm' => 'Calm',
            'cheerful' => 'Cheerful',
            'chilling' => 'Chilling',
            'colorful' => 'Colorful',
            'dark' => 'Dark',
            'neutral' => 'Neutral',
        ],
        'image_number_of_images' => [
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
        ],
        'image_quality' => [
            'standard' => 'Standard',
            'hd' => 'HD',
        ],
    ];
    if ($settings_two->dalle === EntityEnum::DALL_E_3->value) {
        $dalle_select_options['size'] = [
            '1024x1024' => '1024 x 1024',
            '1024x1792' => '1024 x 1792',
            '1792x1024' => '1792 x 1024',
        ];
        $dalle_select_options['image_number_of_images'] = [
            '1' => '1',
        ];
    }
@endphp

<x-forms.input
    class:label="text-heading-foreground font-medium"
    id="size"
    container-class="grow"
    label="{{ __('Image resolution') }}"
    @class([
        'bg-background focus:ring-foreground/10',
        EntityEnum::DALL_E_2->value =>
            $settings_two->dalle === EntityEnum::DALL_E_2->value,
        EntityEnum::DALL_E_3->value =>
            $settings_two->dalle === EntityEnum::DALL_E_3->value,
    ])
    type="select"
    name="size"
    size="lg"
    @change="if ( $app_is_demo && {{ $settings_two->dalle === EntityEnum::DALL_E_3 ? 1 : 0 }} && $event.target.value !== '1024x1024' ) {
				toastr.info('{{ __('This feature is disabled in Demo version.') }}')
				return $event.target.value = '1024x1024';
			}"
>
    @foreach ($dalle_select_options['size'] as $value => $label)
        <option
            value="{{ $value }}"
            @selected($loop->first)
        >
            {{ __($label) }}
        </option>
    @endforeach
</x-forms.input>

<x-forms.input
    class="bg-background focus:ring-foreground/10"
    class:label="text-heading-foreground font-medium"
    id="image_style"
    label="{{ __('Art Style') }}"
    name="image_style"
    container-class="grow"
    size="lg"
    type="select"
>
    @foreach ($dalle_select_options['image_style'] as $value => $label)
        <option
            value="{{ $value }}"
            @selected($loop->first)
        >
            {{ __($label) }}
        </option>
    @endforeach
</x-forms.input>

<x-forms.input
    class="bg-background focus:ring-foreground/10"
    class:label="text-heading-foreground font-medium"
    id="image_lighting"
    label="{{ __('Lightning Style') }}"
    name="image_lighting"
    container-class="grow"
    size="lg"
    type="select"
>
    @foreach ($dalle_select_options['image_lighting'] as $value => $label)
        <option
            value="{{ $value }}"
            @selected($loop->first)
        >
            {{ __($label) }}
        </option>
    @endforeach
</x-forms.input>

<x-forms.input
    class="bg-background focus:ring-foreground/10"
    class:label="text-heading-foreground font-medium"
    id="image_mood"
    label="{{ __('Mood') }}"
    name="image_mood"
    container-class="grow"
    size="lg"
    type="select"
>
    @foreach ($dalle_select_options['image_mood'] as $value => $label)
        <option
            value="{{ $value }}"
            @selected($loop->first)
        >
            {{ __($label) }}
        </option>
    @endforeach
</x-forms.input>

<x-forms.input
    class:label="text-heading-foreground font-medium"
    id="image_number_of_images"
    @class([
        'bg-background focus:ring-foreground/10',
        EntityEnum::DALL_E_2->value =>
            $settings_two->dalle === EntityEnum::DALL_E_2->value,
        EntityEnum::DALL_E_3->value =>
            $settings_two->dalle === EntityEnum::DALL_E_3->value,
    ])
    label="{{ __('Number of Images') }}"
    name="image_number_of_images"
    container-class="grow"
    size="lg"
    type="select"
>
    @foreach ($dalle_select_options['image_number_of_images'] as $value => $label)
        <option
            value="{{ $value }}"
            @selected($loop->first)
        >
            {{ __($label) }}
        </option>
    @endforeach
</x-forms.input>

<x-forms.input
    class:label="text-heading-foreground font-medium"
    class="dall-e-2 bg-background focus:ring-foreground/10"
    id="image_quality"
    label="{{ __('Quality of Images') }}"
    name="image_quality"
    container-class="grow"
    size="lg"
    type="select"
>
    @foreach ($dalle_select_options['image_quality'] as $value => $label)
        <option
            value="{{ $value }}"
            @selected($loop->first)
        >
            {{ __($label) }}
        </option>
    @endforeach
</x-forms.input>
