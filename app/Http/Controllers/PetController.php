<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\PetImage;
use App\Repositories\PetRepository;
use App\Services\PetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PetController extends Controller
{
    public function __construct(
        private PetService $petService,
        private PetRepository $petRepository
    ) {}

    public function index(Request $request): View
    {
        $pets = $this->petRepository->getAllForUser(auth()->id(), $request->only(['species', 'search']));
        $speciesStats = $this->petRepository->getSpeciesStats(auth()->id());
        return view('pets.index', compact('pets', 'speciesStats'));
    }

    public function create(): View
    {
        return view('pets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'species' => 'required|string|max:50',
            'breed' => 'nullable|string|max:100',
            'gender' => 'required|in:male,female,unknown',
            'date_of_birth' => 'nullable|date|before:today',
            'weight' => 'nullable|numeric|min:0|max:999',
            'color' => 'nullable|string|max:50',
            'microchip_id' => 'nullable|string|max:50',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'emergency_notes' => 'nullable|string',
            'activity_level' => 'required|in:low,moderate,high',
            'vet_name' => 'nullable|string|max:100',
            'vet_phone' => 'nullable|string|max:20',
            'vet_email' => 'nullable|email|max:100',
            'vet_clinic' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $pet = $this->petService->createPet(auth()->id(), $validated, $request->file('avatar'));

        return redirect()->route('pets.show', $pet)->with('success', "{$pet->name} has been added successfully! 🐾");
    }

    public function show(Pet $pet): View
    {
        $this->authorize('view', $pet);
        $pet->load(['images', 'vaccinations', 'medicalRecords', 'appointments', 'reminders']);
        return view('pets.show', compact('pet'));
    }

    public function edit(Pet $pet): View
    {
        $this->authorize('update', $pet);
        return view('pets.edit', compact('pet'));
    }

    public function update(Request $request, Pet $pet): RedirectResponse
    {
        $this->authorize('update', $pet);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'species' => 'required|string|max:50',
            'breed' => 'nullable|string|max:100',
            'gender' => 'required|in:male,female,unknown',
            'date_of_birth' => 'nullable|date|before:today',
            'weight' => 'nullable|numeric|min:0|max:999',
            'color' => 'nullable|string|max:50',
            'microchip_id' => 'nullable|string|max:50',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'emergency_notes' => 'nullable|string',
            'activity_level' => 'required|in:low,moderate,high',
            'vet_name' => 'nullable|string|max:100',
            'vet_phone' => 'nullable|string|max:20',
            'vet_email' => 'nullable|email|max:100',
            'vet_clinic' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $pet = $this->petService->updatePet($pet, $validated, $request->file('avatar'));

        return redirect()->route('pets.show', $pet)->with('success', "{$pet->name} has been updated successfully!");
    }

    public function destroy(Pet $pet): RedirectResponse
    {
        $this->authorize('delete', $pet);
        $name = $pet->name;
        $this->petService->deletePet($pet);
        return redirect('/pets')->with('success', "{$name} has been removed.");
    }

    public function uploadImages(Request $request, Pet $pet): RedirectResponse
    {
        $this->authorize('update', $pet);
        $request->validate([
            'images' => 'required|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $this->petService->uploadImages($pet, $request->file('images'));

        return back()->with('success', 'Images uploaded successfully!');
    }

    public function deleteImage(Pet $pet, PetImage $image): RedirectResponse
    {
        $this->authorize('update', $pet);
        $this->petService->deleteImage($image);
        return back()->with('success', 'Image deleted.');
    }
}
