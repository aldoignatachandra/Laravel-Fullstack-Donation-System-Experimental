<div class="bg-slate-50">

    @include('livewire.landing._hero')

    <section class="border-t border-emerald-100 bg-emerald-50">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
            @include('livewire.landing._category-filter', ['categories' => $categories])
        </div>
    </section>

    @include('livewire.landing._campaigns', ['campaigns' => $campaigns])

    @include('livewire.landing._how-it-works')
    @include('livewire.landing._cta')
    @include('livewire.landing._footer')

</div>

