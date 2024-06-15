<?php

namespace App\Filters\Tenant;

use App\Filters\Core\traits\NameFilter;
use App\Filters\Core\traits\SearchFilterTrait;
use App\Filters\FilterBuilder;
use App\Helpers\Traits\UserAccessQueryHelper;
use Illuminate\Database\Eloquent\Builder;

class DocumentFilter extends FilterBuilder
{
    use NameFilter, SearchFilterTrait, UserAccessQueryHelper;

    public function userId($id = null)
    {
        if($id) {
            $this->whereClause('user_id', $id, "=");
        }
    }

    public function showAll($showAll = 'yes')
    {
        $this->builder->when($showAll == 'no', function (Builder $builder) {
            $builder->where('user_id', auth()->id());
        }, function (Builder $builder) {
            $builder->when(request()->get('access_behavior') == 'own_departments',
                fn(Builder $b) => $this->userAccessQuery($b)
            );
        });
    }

}