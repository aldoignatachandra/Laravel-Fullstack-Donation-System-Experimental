@props([
    'isOpen' => false,
    'title' => null,
    'createdAt' => null,
    'author' => null,
    'content' => null,
    'closeAction' => 'closeArticle',
])

<div class="fixed inset-0 z-50" style="{{ $isOpen ? 'display:block;' : 'display:none;' }}">
    <div class="absolute inset-0 bg-black/40" wire:click="{{ $closeAction }}"></div>
    <div class="relative mx-auto max-w-2xl bg-white rounded-3xl shadow-xl border border-gray-200 mt-20 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-emerald-600">
            <h3 class="text-white font-semibold">{{ $title ?? 'Detail Update' }}</h3>
            <button type="button" wire:click="{{ $closeAction }}" class="text-white hover:text-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="px-6 py-5 max-h-[70vh] overflow-y-auto">
            <p class="text-sm text-gray-500 mb-3">
                {{ $createdAt ?? '' }}
                @if(!empty($author))
                    • oleh {{ $author }}
                @endif
            </p>
            <div class="prose prose-sm max-w-none">
                {!! isset($content) ? nl2br(e($content)) : '' !!}
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            <button type="button" wire:click="{{ $closeAction }}" class="px-4 py-2 rounded-2xl border border-gray-300 text-gray-700 hover:bg-gray-100 font-medium">Tutup</button>
        </div>
    </div>
    
</div>

