<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class BlogCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_blog_category');
    }

    public function view(User $user): bool
    {
        return $user->can('view_blog_category');
    }

    public function create(User $user): bool
    {
        return $user->can('create_blog_category');
    }

    public function update(User $user): bool
    {
        return $user->can('update_blog_category');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_blog_category');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_blog_category');
    }
}
