<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Recipe;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class RecipePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Recipe');
    }

    public function view(AuthUser $authUser, Recipe $recipe): bool
    {
        return $authUser->can('View:Recipe');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Recipe');
    }

    public function update(AuthUser $authUser, Recipe $recipe): bool
    {
        return $authUser->can('Update:Recipe');
    }

    public function delete(AuthUser $authUser, Recipe $recipe): bool
    {
        return $authUser->can('Delete:Recipe');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Recipe');
    }

    public function restore(AuthUser $authUser, Recipe $recipe): bool
    {
        return $authUser->can('Restore:Recipe');
    }

    public function forceDelete(AuthUser $authUser, Recipe $recipe): bool
    {
        return $authUser->can('ForceDelete:Recipe');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Recipe');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Recipe');
    }

    public function replicate(AuthUser $authUser, Recipe $recipe): bool
    {
        return $authUser->can('Replicate:Recipe');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Recipe');
    }
}
