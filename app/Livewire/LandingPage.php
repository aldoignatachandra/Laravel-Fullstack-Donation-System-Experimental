<?php

namespace App\Livewire;

use App\Models\Campaign;
use App\Models\CampaignCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class LandingPage extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCategory = null;
    public $categories = [];

    public function mount(): void
    {
        $this->categories = CampaignCategory::query()
            ->select('id','name')->get();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->selectedCategory = null;
        $this->resetPage();
    }


    #[Layout('components.layouts.beramal')]
    #[Title('Beramal')]
    public function render()
    {
        $campaigns = Campaign::query()
            ->with(['category', 'donations', 'user'])
            ->where('status', Campaign::STATUS_ACTIVE)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedCategory, function ($query) {
                $query->where('campaign_category_id', $this->selectedCategory);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('livewire.landing.landing-page', [
            'campaigns' => $campaigns,
        ]);
    }
}
