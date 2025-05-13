@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Brand Voice'))
@section('titlebar_subtitle',
    __('Generate AI content exclusive to your brand and eliminate the need for repetitive
    introductions of your company.'))
@section('titlebar_actions')
	<x-button href="{{ route('dashboard.user.brand.create') }}">
		<x-tabler-plus class="size-4" />
		{{ __('New Company') }}
	</x-button>
@endsection

@section('content')
	<div class="py-10">
		<x-table>
			<x-slot:head>
				<tr>
					<th>
						{{ __('Name') }}
					</th>
					<th class="text-end">
						{{ __('Action') }}
					</th>
				</tr>
			</x-slot:head>

			<x-slot:body>
				@foreach ($list as $entry)
					<tr>
						<td>
							{{ $entry->name }}
						</td>

						<td class="whitespace-nowrap text-end">
							<x-button
								class="size-9"
								variant="ghost-shadow"
								size="none"
								href="{{  (route('dashboard.user.brand.edit', $entry->id)) }}"
								title="{{ __('Edit') }}"
							>
								<x-tabler-pencil class="size-4" />
							</x-button>
							@if ($app_is_demo)
								<x-button
									class="size-9"
									variant="ghost-shadow"
									hover-variant="danger"
									size="none"
									onclick="return toastr.info('This feature is disabled in Demo version.')"
									title="{{ __('Delete') }}"
								>
									<x-tabler-x class="size-4" />
								</x-button>
							@else
								<x-button
									class="size-9"
									variant="ghost-shadow"
									hover-variant="danger"
									size="none"
									onclick="return confirm('{{ __('Are you sure? This is permanent and will delete all documents related to user.') }}')"
									href="{{  (route('dashboard.user.brand.delete', $entry->id)) }}"
									title="{{ __('Delete') }}"
								>
									<x-tabler-x class="size-4" />
								</x-button>
							@endif
						</td>
					</tr>
				@endforeach
			</x-slot:body>
		</x-table>
	</div>
@endsection
