<?php

namespace App\Livewire\Dashboard;

use App\Filament\Resources\Donations\Helpers\DonationHelper;
use App\Models\Donation;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Daftar Donasi Saya')]
class Donations extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        // Initialize component
    }

    /**
     * Get the donations for the current user.
     */
    public function getDonations()
    {
        $query = Donation::with(['campaign'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if ($this->search) {
            $query->whereHas('campaign', function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%');
            });
        }

        // Apply status filter
        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        return $query->paginate(5);
    }

    /**
     * Get status label for display.
     */

    /**
     * Get status color for display.
     */
    public function getStatusColor($status)
    {
        return DonationHelper::getDonationStatusColor($status);
    }

    /**
     * Format date for display.
     */
    public function formatDate($date)
    {
        return $date ? $date->format('d M Y, H:i') : '-';
    }

    /**
     * Clear search and filters.
     */
    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    /**
     * Update search and reset pagination.
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * Update status filter and reset pagination.
     */
    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.dashboard.donation.donations', [
            'donations' => $this->getDonations(),
        ]);
    }
}
