<div class="no-scrollbar -mx-1 flex snap-x items-center gap-2 overflow-x-auto py-1">
    <button
        wire:click="$set('selectedCategory', null)"
        :class="`snap-start rounded-2xl border px-3 py-1.5 text-sm transition ${
            @this.selectedCategory === null
                ? 'border-gray-900 dark:border-emerald-500 bg-gray-900 dark:bg-emerald-600 text-white'
                : 'border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700'
        }`"
        :aria-pressed="@this.selectedCategory === null"
    >
        Semua
    </button>
    @foreach($categories as $category)
        <button
            wire:click="$set('selectedCategory', {{ $category->id }})"
            :class="`snap-start rounded-2xl border px-3 py-1.5 text-sm transition ${
                @this.selectedCategory === {{ $category->id }}
                    ? 'border-gray-900 dark:border-emerald-500 bg-gray-900 dark:bg-emerald-600 text-white'
                    : 'border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700'
            }`"
            :aria-pressed="@this.selectedCategory === {{ $category->id }}"
        >
            {{ $category->name }}
        </button>
    @endforeach
</div>
