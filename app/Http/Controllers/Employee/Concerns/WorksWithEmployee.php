<?php

namespace App\Http\Controllers\Employee\Concerns;

use App\Models\AssignedMember;
use App\Models\User;
use Illuminate\Support\Collection;

trait WorksWithEmployee
{
    private function employee(): User
    {
        return auth()->user();
    }

    /**
     * @return Collection<int, int>
     */
    private function assignedIds(User $employee, string $type): Collection
    {
        return AssignedMember::query()
            ->where('member_id', $employee->id)
            ->where('assignable_type', $type)
            ->pluck('assignable_id');
    }
}
