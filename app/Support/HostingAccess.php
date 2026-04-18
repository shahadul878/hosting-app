<?php

namespace App\Support;

use App\Enums\MvpRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class HostingAccess
{
    public static function userMayBypass(?User $user): bool
    {
        return $user !== null && $user->isSuperAdmin();
    }

    /** @return Collection<int, int> */
    public static function subtreeUserIds(User $user): Collection
    {
        return $user->subtreeUserIds();
    }

    public static function constrainOwnedService(Builder $builder, User $user, string $alias = 'services'): void
    {
        if (self::userMayBypass($user)) {
            return;
        }

        if ($user->mvp_role === MvpRole::Client) {
            $builder->where($alias.'.client_user_id', $user->id);

            return;
        }

        if ($user->mvp_role === MvpRole::SubReseller) {
            $subIds = self::subtreeUserIds($user)->push($user->id)->unique()->values();
            $builder->where(function (Builder $q) use ($alias, $user, $subIds): void {
                $q->where($alias.'.sub_reseller_user_id', $user->id)
                    ->orWhereIn($alias.'.client_user_id', $subIds);
            });

            return;
        }

        if ($user->mvp_role === MvpRole::Reseller) {
            $subIds = self::subtreeUserIds($user)->push($user->id)->unique()->values();
            $builder->where(function (Builder $q) use ($alias, $user, $subIds): void {
                $q->where($alias.'.reseller_user_id', $user->id)
                    ->orWhereIn($alias.'.client_user_id', $subIds)
                    ->orWhereIn($alias.'.sub_reseller_user_id', $subIds);
            });

            return;
        }

        $builder->whereRaw('0 = 1');
    }

    public static function constrainInvoice(Builder $builder, User $user, string $alias = 'invoices'): void
    {
        if (self::userMayBypass($user)) {
            return;
        }

        if ($user->mvp_role === MvpRole::Client) {
            $builder->where($alias.'.client_user_id', $user->id);

            return;
        }

        if ($user->mvp_role === MvpRole::SubReseller) {
            $subIds = self::subtreeUserIds($user)->push($user->id)->unique()->values();
            $builder->where(function (Builder $q) use ($alias, $subIds): void {
                $q->whereIn($alias.'.client_user_id', $subIds);
            });

            return;
        }

        if ($user->mvp_role === MvpRole::Reseller) {
            $subIds = self::subtreeUserIds($user)->push($user->id)->unique()->values();
            $builder->where(function (Builder $q) use ($alias, $user, $subIds): void {
                $q->where($alias.'.reseller_user_id', $user->id)
                    ->orWhereIn($alias.'.client_user_id', $subIds);
            });

            return;
        }

        $builder->whereRaw('0 = 1');
    }

    public static function constrainTicket(Builder $builder, User $user, string $alias = 'tickets'): void
    {
        if (self::userMayBypass($user)) {
            return;
        }

        if ($user->mvp_role === MvpRole::Client) {
            $builder->where($alias.'.client_user_id', $user->id);

            return;
        }

        $subIds = self::subtreeUserIds($user)->push($user->id)->unique()->values();
        $builder->whereIn($alias.'.client_user_id', $subIds);
    }

    public static function constrainResellerProductPrice(Builder $builder, User $user, string $alias = 'reseller_product_prices'): void
    {
        if (self::userMayBypass($user)) {
            return;
        }

        if ($user->mvp_role === MvpRole::Reseller) {
            $builder->where($alias.'.reseller_user_id', $user->id);

            return;
        }

        if ($user->mvp_role === MvpRole::SubReseller) {
            $parent = $user->parent;
            if ($parent && $parent->mvp_role === MvpRole::Reseller) {
                $builder->where($alias.'.reseller_user_id', $parent->id);

                return;
            }
        }

        $builder->whereRaw('0 = 1');
    }

    public static function constrainServiceRequest(Builder $builder, User $user, string $alias = 'service_requests'): void
    {
        if (self::userMayBypass($user)) {
            return;
        }

        if ($user->mvp_role === MvpRole::Client) {
            $builder->where($alias.'.client_user_id', $user->id);

            return;
        }

        if ($user->mvp_role === MvpRole::SubReseller) {
            $subIds = self::subtreeUserIds($user)->push($user->id)->unique()->values();
            $builder->where(function (Builder $q) use ($alias, $user, $subIds): void {
                $q->whereIn($alias.'.client_user_id', $subIds)
                    ->orWhere($alias.'.reseller_user_id', $user->id);
            });

            return;
        }

        if ($user->mvp_role === MvpRole::Reseller) {
            $subIds = self::subtreeUserIds($user)->push($user->id)->unique()->values();
            $builder->where(function (Builder $q) use ($alias, $user, $subIds): void {
                $q->where($alias.'.reseller_user_id', $user->id)
                    ->orWhereIn($alias.'.client_user_id', $subIds);
            });

            return;
        }

        $builder->whereRaw('0 = 1');
    }
}
