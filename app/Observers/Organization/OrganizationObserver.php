<?php

namespace App\Observers\Organization;

use App\Models\Organization\Organization;
use App\Models\User;
use App\Notifications\ActionHandledOnModel;

class OrganizationObserver
{
    /**
     * Handle the Organization "created" event.
     */
    public function created(Organization $organization): void
    {
        session()->flash('message', [
            'message' => "{$organization->rif} ({$organization->name})",
            'title' => __('SAVED!'),
            'type'  => 'success',
        ]);

        $users = User::permission('create new organizations')->get()->filter(
            fn (User $user) => $user->id != auth()->user()->id
        )->all();

        foreach ($users as $user)
        {
            $user->notify(new ActionHandledOnModel(
                auth()->user(),
                [
                    'id' => $organization->id,
                    'type' => __('organization'),
                    'name' => "{$organization->rif} ({$organization->name})",
                    'timestamp' => $organization->created_at,
                ],
                'created',
                ['routeName' => 'organizations', 'routeParam' => 'organization']
            ));
        }
    }

    /**
     * Handle the Organization "updated" event.
     */
    public function updated(Organization $organization): void
    {
        session()->flash('message', [
            'message' => "{$organization->rif} ({$organization->name})",
            'title' => __('SAVED!'),
            'type'  => 'success',
        ]);

        $users = User::permission('update organizations')->get()->filter(
            fn (User $user) => $user->id != auth()->user()->id
        )->all();

        foreach ($users as $user)
        {
            $user->notify(new ActionHandledOnModel(
                auth()->user(),
                [
                    'id' => $organization->id,
                    'type' => __('organization'),
                    'name' => "{$organization->rif} ({$organization->name})",
                    'timestamp' => $organization->updated_at,
                ],
                'updated',
                ['routeName' => 'organizations', 'routeParam' => 'organization']
            ));
        }
    }

    /**
     * Handle the Organization "deleted" event.
     */
    public function deleted(Organization $organization): void
    {
        session()->flash('message', [
            'message' => "{$organization->rif} ({$organization->name})",
            'title' => __('DELETED!'),
            'type'  => 'danger',
        ]);

        $users = User::permission('delete organizations')->get()->filter(
            fn (User $user) => $user->id != auth()->user()->id
        )->all();

        foreach ($users as $user)
        {
            $user->notify(new ActionHandledOnModel(
                auth()->user(),
                [
                    'type' => __('organization'),
                    'name' => "{$organization->rif} ({$organization->name})",
                    'timestamp' => now()->toISOString(),
                ],
                'deleted',
                ['routeName' => 'organizations', 'routeParam' => 'organization']
            ));
        }
    }

    /**
     * Handle the Organization "restored" event.
     */
    public function restored(Organization $organization): void
    {
        //
    }

    /**
     * Handle the Organization "force deleted" event.
     */
    public function forceDeleted(Organization $organization): void
    {
        //
    }
}
