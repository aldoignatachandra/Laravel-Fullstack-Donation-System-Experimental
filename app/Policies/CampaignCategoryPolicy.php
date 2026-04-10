<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CampaignCategory;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CampaignCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CampaignCategory');
    }

    public function view(AuthUser $authUser, CampaignCategory $campaignCategory): bool
    {
        return $authUser->can('View:CampaignCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CampaignCategory');
    }

    public function update(AuthUser $authUser, CampaignCategory $campaignCategory): bool
    {
        return $authUser->can('Update:CampaignCategory');
    }

    public function delete(AuthUser $authUser, CampaignCategory $campaignCategory): bool
    {
        return $authUser->can('Delete:CampaignCategory');
    }

    public function restore(AuthUser $authUser, CampaignCategory $campaignCategory): bool
    {
        return $authUser->can('Restore:CampaignCategory');
    }

    public function forceDelete(AuthUser $authUser, CampaignCategory $campaignCategory): bool
    {
        return $authUser->can('ForceDelete:CampaignCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CampaignCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CampaignCategory');
    }

    public function replicate(AuthUser $authUser, CampaignCategory $campaignCategory): bool
    {
        return $authUser->can('Replicate:CampaignCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CampaignCategory');
    }
}
