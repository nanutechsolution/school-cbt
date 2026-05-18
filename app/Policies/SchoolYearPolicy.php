<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SchoolYear;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchoolYearPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SchoolYear');
    }

    public function view(AuthUser $authUser, SchoolYear $schoolYear): bool
    {
        return $authUser->can('View:SchoolYear');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SchoolYear');
    }

    public function update(AuthUser $authUser, SchoolYear $schoolYear): bool
    {
        return $authUser->can('Update:SchoolYear');
    }

    public function delete(AuthUser $authUser, SchoolYear $schoolYear): bool
    {
        return $authUser->can('Delete:SchoolYear');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SchoolYear');
    }

    public function restore(AuthUser $authUser, SchoolYear $schoolYear): bool
    {
        return $authUser->can('Restore:SchoolYear');
    }

    public function forceDelete(AuthUser $authUser, SchoolYear $schoolYear): bool
    {
        return $authUser->can('ForceDelete:SchoolYear');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SchoolYear');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SchoolYear');
    }

    public function replicate(AuthUser $authUser, SchoolYear $schoolYear): bool
    {
        return $authUser->can('Replicate:SchoolYear');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SchoolYear');
    }

}