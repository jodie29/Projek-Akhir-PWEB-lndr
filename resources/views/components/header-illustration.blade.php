<div class="flex flex-col md:flex-row items-center gap-8 mb-6">
    <img src="{{ $image ?? 'https://static.vecteezy.com/system/resources/previews/026/721/193/non_2x/washing-machine-and-laundry-laundry-sticker-png.png' }}" alt="{{ $alt ?? 'Illustration' }}" class="w-32 h-32 object-contain drop-shadow-xl rounded-2xl bg-white hover:scale-[1.025] transition duration-300">
    <div>
        <h1 class="text-3xl font-bold {{ $titleClass ?? 'text-gray-800' }}">{{ $title }}</h1>
        @if(isset($subtitle))
            <div class="text-sm text-gray-600">{!! $subtitle !!}</div>
        @endif
    </div>
</div>