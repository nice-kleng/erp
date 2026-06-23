<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ApPayment;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ApPaymentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ApPayment');
    }

    public function view(AuthUser $authUser, ApPayment $apPayment): bool
    {
        return $authUser->can('View:ApPayment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ApPayment');
    }

    public function update(AuthUser $authUser, ApPayment $apPayment): bool
    {
        return $authUser->can('Update:ApPayment');
    }

    public function delete(AuthUser $authUser, ApPayment $apPayment): bool
    {
        return $authUser->can('Delete:ApPayment');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ApPayment');
    }

    public function restore(AuthUser $authUser, ApPayment $apPayment): bool
    {
        return $authUser->can('Restore:ApPayment');
    }

    public function forceDelete(AuthUser $authUser, ApPayment $apPayment): bool
    {
        return $authUser->can('ForceDelete:ApPayment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ApPayment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ApPayment');
    }

    public function replicate(AuthUser $authUser, ApPayment $apPayment): bool
    {
        return $authUser->can('Replicate:ApPayment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ApPayment');
    }
}
