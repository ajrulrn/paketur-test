<?php

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;

class CompanyRepository
{
    public function getWithPagination($keyword = null, $direction = 'asc', $perPage = 10)
    {
        return Company::when(isset($keyword), function ($query) use ($keyword) {
                $query->where('name', 'ilike', "%{$keyword}%");
            })
            ->when(in_array($direction, ['asc', 'desc']), function (Builder $query) use ($direction) {
                $query->orderBy('name', $direction);
            }, function (Builder $query) {
                $query->orderBy('name');
            })
            ->paginate($perPage);
    }

    public function create($data)
    {
        return Company::create($data);
    }

    public function delete($id)
    {
        Company::destroy($id);
    }
}
