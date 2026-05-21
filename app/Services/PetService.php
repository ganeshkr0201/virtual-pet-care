<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Pet;
use App\Models\PetImage;
use App\Repositories\PetRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PetService
{
    public function __construct(private PetRepository $petRepository) {}

    public function createPet(int $userId, array $data, ?UploadedFile $avatar = null): Pet
    {
        $data['user_id'] = $userId;

        if ($avatar) {
            $data['avatar'] = $avatar->store('pets/avatars', 'public');
        }

        $pet = $this->petRepository->create($data);

        ActivityLog::log('created', "Added new pet: {$pet->name}", $pet);

        return $pet;
    }

    public function updatePet(Pet $pet, array $data, ?UploadedFile $avatar = null): Pet
    {
        $oldValues = $pet->toArray();

        if ($avatar) {
            if ($pet->avatar) {
                Storage::disk('public')->delete($pet->avatar);
            }
            $data['avatar'] = $avatar->store('pets/avatars', 'public');
        }

        $updated = $this->petRepository->update($pet, $data);

        ActivityLog::log('updated', "Updated pet: {$pet->name}", $pet, $oldValues, $data);

        return $updated;
    }

    public function deletePet(Pet $pet): bool
    {
        ActivityLog::log('deleted', "Deleted pet: {$pet->name}", $pet);
        return $this->petRepository->delete($pet);
    }

    public function uploadImages(Pet $pet, array $files): void
    {
        foreach ($files as $file) {
            $path = $file->store('pets/images', 'public');
            PetImage::create([
                'pet_id' => $pet->id,
                'path' => $path,
                'is_primary' => $pet->images()->count() === 0,
            ]);
        }
    }

    public function deleteImage(PetImage $image): void
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();
    }
}
