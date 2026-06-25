<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AccountReceivable;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AccountReceivablePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AccountReceivable');
    }

    public function view(AuthUser $authUser, AccountReceivable $accountReceivable): bool
    {
        return $authUser->can('View:AccountReceivable');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AccountReceivable');
    }

    public function update(AuthUser $authUser, AccountReceivable $accountReceivable): bool
    {
        return $authUser->can('Update:AccountReceivable');
    }

    public function delete(AuthUser $authUser, AccountReceivable $accountReceivable): bool
    {
        return $authUser->can('Delete:AccountReceivable');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AccountReceivable');
    }

    public function restore(AuthUser $authUser, AccountReceivable $accountReceivable): bool
    {
        return $authUser->can('Restore:AccountReceivable');
    }

    public function forceDelete(AuthUser $authUser, AccountReceivable $accountReceivable): bool
    {
        return $authUser->can('ForceDelete:AccountReceivable');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AccountReceivable');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AccountReceivable');
    }

    public function replicate(AuthUser $authUser, AccountReceivable $accountReceivable): bool
    {
        return $authUser->can('Replicate:AccountReceivable');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AccountReceivable');
    }
}
