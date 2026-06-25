<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ArPayment;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ArPaymentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ArPayment');
    }

    public function view(AuthUser $authUser, ArPayment $arPayment): bool
    {
        return $authUser->can('View:ArPayment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ArPayment');
    }

    public function update(AuthUser $authUser, ArPayment $arPayment): bool
    {
        return $authUser->can('Update:ArPayment');
    }

    public function delete(AuthUser $authUser, ArPayment $arPayment): bool
    {
        return $authUser->can('Delete:ArPayment');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ArPayment');
    }

    public function restore(AuthUser $authUser, ArPayment $arPayment): bool
    {
        return $authUser->can('Restore:ArPayment');
    }

    public function forceDelete(AuthUser $authUser, ArPayment $arPayment): bool
    {
        return $authUser->can('ForceDelete:ArPayment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ArPayment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ArPayment');
    }

    public function replicate(AuthUser $authUser, ArPayment $arPayment): bool
    {
        return $authUser->can('Replicate:ArPayment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ArPayment');
    }
}
