<?php

namespace App\Livewire\Dashboard;

use App\Models\Donation;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
class Dashboard extends Component
{
    public function render()
    {

        // Total donasi dalam rupiah (hanya yang sudah dibayar)
        $totalDonationAmount = Donation::query()->where('user_id', '=', Auth::user()->id)
            ->where('status', Donation::STATUS_PAID)
            ->sum('amount');

        // Total jumlah donasi yang sudah dilakukan
        $totalDonationCount = Donation::query()->where('user_id', '=', Auth::user()->id)
            ->where('status', Donation::STATUS_PAID)
            ->count();

        // 5 donasi terbaru
        $recentDonations = Donation::query()->where('user_id', '=', Auth::user()->id)
            ->with('campaign')
            ->latest()
            ->limit(5)
            ->get();

        return view('livewire.dashboard.dashboard', [
            'totalDonationAmount' => $totalDonationAmount,
            'totalDonationCount' => $totalDonationCount,
            'recentDonations' => $recentDonations,
        ]);
    }
}
