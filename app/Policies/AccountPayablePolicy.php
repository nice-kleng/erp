<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AccountPayable;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AccountPayablePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AccountPayable');
    }

    public function view(AuthUser $authUser, AccountPayable $accountPayable): bool
    {
        return $authUser->can('View:AccountPayable');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AccountPayable');
    }

    public function update(AuthUser $authUser, AccountPayable $accountPayable): bool
    {
        return $authUser->can('Update:AccountPayable');
    }

    public function delete(AuthUser $authUser, AccountPayable $accountPayable): bool
    {
        return $authUser->can('Delete:AccountPayable');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AccountPayable');
    }

    public function restore(AuthUser $authUser, AccountPayable $accountPayable): bool
    {
        return $authUser->can('Restore:AccountPayable');
    }

    public function forceDelete(AuthUser $authUser, AccountPayable $accountPayable): bool
    {
        return $authUser->can('ForceDelete:AccountPayable');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AccountPayable');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AccountPayable');
    }

    public function replicate(AuthUser $authUser, AccountPayable $accountPayable): bool
    {
        return $authUser->can('Replicate:AccountPayable');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AccountPayable');
    }
}
