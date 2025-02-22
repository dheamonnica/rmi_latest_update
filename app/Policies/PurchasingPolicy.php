<?php

namespace App\Policies;

use App\Helpers\Authorize;
use App\Models\Purchasing;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchasingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view purchasing.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function index(User $user)
    {
        return (new Authorize($user, 'view_purchasing'))->check();
    }

    /**
     * Determine whether the user can view the purchasing.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\purchasing  $purchasing
     * @return mixed
     */
    public function view(User $user, Purchasing $purchasing)
    {
        return (new Authorize($user, 'view_purchasing', $purchasing))->check();
    }

    /**
     * Determine whether the user can create purchasing.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return (new Authorize($user, 'add_purchasing'))->check();
    }

    /**
     * Determine whether the user can update the purchasing.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\purchasing  $purchasing
     * @return mixed
     */
    public function update(User $user, Purchasing $purchasing)
    {
        return (new Authorize($user, 'edit_purchasing', $purchasing))->check();
    }

    /**
     * Determine whether the user can delete the purchasing.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\purchasing  $purchasing
     * @return mixed
     */
    public function delete(User $user, Purchasing $purchasing)
    {
        return (new Authorize($user, 'delete_purchasing', $purchasing))->check();
    }

    /**
     * Determine whether the user can delete the Product.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function massDelete(User $user)
    {
        return (new Authorize($user, 'delete_purchasing'))->check();
    }
}
