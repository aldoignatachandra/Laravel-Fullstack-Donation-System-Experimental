<?php

namespace App\Livewire\Campaign;

use App\Models\Campaign;
use App\Services\DonationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

class DonationForm extends Component
{
    public Campaign $campaign;

    #[Validate('required|numeric|min:10000')]
    public $selectedAmount = 0;

    #[Validate('nullable|string|max:20')]
    public $customAmount = '';

    #[Validate('nullable|string|max:500')]
    public $message = '';

    #[Validate('boolean')]
    public $isAnonymous = false;

    public function mount($slug)
    {
        $this->campaign = Campaign::query()
            ->where('slug', '=', $slug)
            ->where('status', '=', Campaign::STATUS_ACTIVE)
            ->firstOrFail();
    }

    public function selectAmount($amount)
    {
        $this->selectedAmount = $amount;
        $this->customAmount = number_format($amount, 0, ',', '.');
    }

    public function setCustomAmount()
    {
        if ($this->customAmount) {
            // Remove formatting and convert to integer
            $cleanAmount = (int) str_replace(['.', ','], '', $this->customAmount);
            $this->selectedAmount = $cleanAmount;
            // Update customAmount with formatted value
            $this->customAmount = number_format($cleanAmount, 0, ',', '.');
        }
    }

    public function updatedCustomAmount()
    {
        $this->setCustomAmount();
    }

    public function proceedToPayment()
    {
        $this->validate();

        // Process the donation
        try {
            $service = app(DonationService::class);
            $result = $service->recordDonation($this->campaign, [
                'amount' => $this->selectedAmount,
                'is_anonymous' => $this->isAnonymous,
                'message' => $this->message,
            ]);

            if ($result['success']) {
                // Redirect externally to payment page (Snap)
                return redirect()->away($result['snap_url']);
            } else {
                $this->addError('donation', 'Failed to process donation. Please try again.');

                return;
            }

        } catch (ValidationException $e) {
            // Handle validation errors from service
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        } catch (\Exception $e) {
            // Handle other errors
            $this->addError('donation', 'An error occurred while processing your donation. Please try again later.');
            Log::error('Donation form error', [
                'campaign_id' => $this->campaign->id,
                'user_id' => Auth::id(),
                'amount' => $this->selectedAmount,
                'error' => $e->getMessage(),
            ]);
        }
    }

    #[Layout('components.layouts.beramal')]
    #[Title('Donasi')]
    public function render()
    {
        $predefinedAmounts = [100000, 200000, 300000, 400000, 500000, 1000000];

        return view('livewire.campaign.donation-form', [
            'predefinedAmounts' => $predefinedAmounts,
        ]);
    }
}
