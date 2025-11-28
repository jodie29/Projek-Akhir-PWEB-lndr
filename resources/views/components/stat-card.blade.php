<div class="p-6 {{ $bgClass ?? 'bg-white' }} rounded-2xl shadow-lg text-center transform hover:scale-105 transition duration-300">
    <p class="text-sm text-gray-500">{{ $label }}</p>
    <p class="text-3xl font-extrabold {{ $textClass ?? 'text-gray-900' }} mt-2">{{ $value }}</p>
    @if(isset($hint))
        <p class="text-xs text-gray-400 mt-1">{{ $hint }}</p>
    @endif
</div>