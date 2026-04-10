<?php

namespace App\Livewire\Campaign;

use App\Models\Campaign;
use App\Models\CampaignArticle;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ShowCampaign extends Component
{
    public Campaign $campaign;

    public string $slug;

    public bool $isArticleModalOpen = false;
    public ?array $articleModal = null;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
        $this->loadCampaign();
    }

    public function loadCampaign(): void
    {
        $this->campaign = Campaign::query()
            ->where('slug', '=', $this->slug)
            ->with(['category', 'donations'])
            ->with(['articles' => function ($q) {
                $q->latest()->take(5)->with('author');
            }])
            ->withCount('donations')
            ->where('status', Campaign::STATUS_ACTIVE)
            ->firstOrFail();
    }

    public function showArticle($articleId): void
    {
        $id = (int) $articleId;
        $article = $this->campaign->articles()
            ->where('id', $id)
            ->firstOrFail();

        $this->articleModal = [
            'title' => $article->title,
            'content' => $article->content,
            'created_at' => optional($article->created_at)->format('d M Y, H:i'),
            'author' => optional($article->author)->name,
        ];
        $this->isArticleModalOpen = true;
    }

    public function closeArticle(): void
    {
        $this->isArticleModalOpen = false;
        $this->articleModal = null;
    }

    #[Layout('components.layouts.beramal')]
    #[Title('Beramal')]
    public function render()
    {
        return view('livewire.campaign.show-campaign');
    }
}
