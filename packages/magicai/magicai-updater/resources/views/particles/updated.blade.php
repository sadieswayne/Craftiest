<div class="mx-auto">
   <div class="flex flex-col mb-4 ">
       <div class="flex justify-center">
           <x-tabler-circle-check class="size-16 text-green-600" />

       </div>

         <p class="text-xl font-bold ml-2">
             Great! Your system is up to date.
         </p>
   </div>

    <x-button
        href="{{ route('dashboard.admin.update.index') }}"
        variant="ghost-shadow"
        class="w-full"
        size="lg"
    >
        {{ trans('Back to dashboard') }}
    </x-button>
</div>

