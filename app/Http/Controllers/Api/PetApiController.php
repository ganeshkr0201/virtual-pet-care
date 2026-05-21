<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Repositories\PetRepository;
use App\Services\PetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PetApiController extends Controller
{
    public function __construct(
        private PetService $petService,
        private PetRepository $petRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pets = $this->petRepository->getAllForUser(
            auth()->id(),
            $request->only(['species', 'search'])
        );
        return response()->json($pets);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'species'        => 'required|string|max:50',
            'breed'          => 'nullable|string|max:100',
            'gender'         => 'required|in:male,female,unknown',
            'date_of_birth'  => 'nullable|date|before:today',
            'weight'         => 'nullable|numeric|min:0|max:999',
            'activity_level' => 'required|in:low,moderate,high',
        ]);

        $pet = $this->petService->createPet(auth()->id(), $validated);
        return response()->json($pet, 201);
    }

    public function show(Pet $pet): JsonResponse
    {
        $this->authorize('view', $pet);
        return response()->json($pet->load(['vaccinations', 'medicalRecords', 'reminders']));
    }

    public function update(Request $request, Pet $pet): JsonResponse
    {
        $this->authorize('update', $pet);

        $validated = $request->validate([
            'name'           => 'sometimes|string|max:100',
            'species'        => 'sometimes|string|max:50',
            'breed'          => 'nullable|string|max:100',
            'gender'         => 'sometimes|in:male,female,unknown',
            'date_of_birth'  => 'nullable|date|before:today',
            'weight'         => 'nullable|numeric|min:0|max:999',
            'activity_level' => 'sometimes|in:low,moderate,high',
        ]);

        $pet = $this->petService->updatePet($pet, $validated);
        return response()->json($pet);
    }

    public function destroy(Pet $pet): JsonResponse
    {
        $this->authorize('delete', $pet);
        $this->petService->deletePet($pet);
        return response()->json(['message' => 'Pet deleted successfully.']);
    }
}
