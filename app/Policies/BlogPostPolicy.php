<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class BlogPostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_blog_post');
    }

    public function view(User $user): bool
    {
        return $user->can('view_blog_post');
    }

    public function create(User $user): bool
    {
        return $user->can('create_blog_post');
    }

    public function update(User $user): bool
    {
        return $user->can('update_blog_post');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_blog_post');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_blog_post');
    }
}
