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

    /**
     * Real-time validation for message field with debounce.
     */
    public function updatedMessage($value): void
    {
        if (strlen($value) > 500) {
            $this->addError('message', 'Pesan tidak boleh lebih dari 500 karakter.');
        } else {
            $this->resetErrorBag('message');
        }
    }

    public $showErrorModal = false;

    public $errorModalMessage = '';

    public $errorModalTitle = '';

    public function proceedToPayment()
    {
        // Custom validation before proceeding
        $cleanAmount = (int) str_replace(['.', ','], '', $this->customAmount);

        // Check if amount is valid integer
        if (! is_numeric($cleanAmount) || $cleanAmount <= 0) {
            $this->errorModalTitle = 'Nominal Tidak Valid';
            $this->errorModalMessage = 'Masukkan nominal donasi yang valid dalam bentuk angka.';
            $this->showErrorModal = true;

            return;
        }

        // Check if amount is below minimum
        if ($cleanAmount < DonationService::MIN_DONATION_AMOUNT) {
            $this->errorModalTitle = 'Nominal Terlalu Kecil';
            $this->errorModalMessage = 'Minimal donasi adalah Rp 10.000. Silakan masukkan nominal yang lebih besar.';
            $this->showErrorModal = true;

            return;
        }

        // Check if amount exceeds maximum
        if ($cleanAmount > DonationService::MAX_DONATION_AMOUNT) {
            $this->errorModalTitle = 'Nominal Terlalu Besar';
            $this->errorModalMessage = 'Maksimal donasi per transaksi adalah Rp 100.000.000. Silakan masukkan nominal yang lebih kecil atau hubungi admin untuk donasi besar.';
            $this->showErrorModal = true;

            return;
        }

        $this->selectedAmount = $cleanAmount;
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
                $this->errorModalTitle = 'Terjadi Kesalahan';
                $this->errorModalMessage = 'Gagal memproses donasi. Silakan coba lagi.';
                $this->showErrorModal = true;

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
            $this->errorModalTitle = 'Terjadi Kesalahan';
            $this->errorModalMessage = 'Terjadi kesalahan saat memproses donasi Anda. Silakan coba lagi nanti.';
            $this->showErrorModal = true;
            Log::error('Donation form error', [
                'campaign_id' => $this->campaign->id,
                'user_id' => Auth::id(),
                'amount' => $this->selectedAmount,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function closeErrorModal()
    {
        $this->showErrorModal = false;
        $this->errorModalMessage = '';
        $this->errorModalTitle = '';
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
