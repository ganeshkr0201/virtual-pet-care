<?php

namespace App\Repositories;

use App\Models\Pet;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PetRepository
{
    public function getAllForUser(int $userId, array $filters = []): LengthAwarePaginator
    {
        $query = Pet::where('user_id', $userId)->with(['images', 'reminders']);

        if (!empty($filters['species'])) {
            $query->where('species', $filters['species']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('breed', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->latest()->paginate(12);
    }

    public function findForUser(int $petId, int $userId): ?Pet
    {
        return Pet::where('id', $petId)->where('user_id', $userId)->firstOrFail();
    }

    public function create(array $data): Pet
    {
        return Pet::create($data);
    }

    public function update(Pet $pet, array $data): Pet
    {
        $pet->update($data);
        return $pet->fresh();
    }

    public function delete(Pet $pet): bool
    {
        return $pet->delete();
    }

    public function getSpeciesStats(int $userId): Collection
    {
        return Pet::where('user_id', $userId)
            ->selectRaw('species, count(*) as count')
            ->groupBy('species')
            ->get();
    }
}
