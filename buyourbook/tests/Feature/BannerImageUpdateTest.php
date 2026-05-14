<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BannerImageUpdateTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);
    }

    public function test_banner_image_can_be_updated(): void
    {
        Storage::fake('public');

        // Créer une bannière initiale avec une image
        $originalImage = UploadedFile::fake()->image('original.jpg');
        $originalPath = $originalImage->store('banners', 'public');

        $banner = Banner::create([
            'title'       => 'Bannière Test',
            'image'       => $originalPath,
            'position'    => 'home_top',
            'target_type' => 'all',
            'is_active'   => true,
        ]);

        Storage::disk('public')->assertExists($originalPath);

        // Uploader une nouvelle image via le formulaire d'édition
        $newImage = UploadedFile::fake()->image('new_banner.jpg', 800, 200);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.banners.update', $banner), [
                'title'       => $banner->title,
                'image'       => $newImage,
                'position'    => 'home_top',
                'target_type' => 'all',
                'is_active'   => '1',
            ]);

        $response->assertRedirect(route('admin.banners.index'));
        $response->assertSessionHas('success');

        $banner->refresh();

        // La nouvelle image doit être différente de l'ancienne
        $this->assertNotEquals($originalPath, $banner->image);

        // La nouvelle image doit exister sur le disque
        Storage::disk('public')->assertExists($banner->image);

        // L'ancienne image doit être supprimée
        Storage::disk('public')->assertMissing($originalPath);
    }

    public function test_banner_update_without_new_image_keeps_old_image(): void
    {
        Storage::fake('public');

        $originalImage = UploadedFile::fake()->image('original.jpg');
        $originalPath = $originalImage->store('banners', 'public');

        $banner = Banner::create([
            'title'       => 'Bannière Test',
            'image'       => $originalPath,
            'position'    => 'home_top',
            'target_type' => 'all',
            'is_active'   => true,
        ]);

        // Mettre à jour sans changer l'image
        $response = $this->actingAs($this->admin)
            ->put(route('admin.banners.update', $banner), [
                'title'       => 'Nouveau titre',
                'position'    => 'home_top',
                'target_type' => 'all',
                'is_active'   => '1',
                // pas de 'image' => ...
            ]);

        $response->assertRedirect(route('admin.banners.index'));

        $banner->refresh();
        $this->assertEquals($originalPath, $banner->image);
        $this->assertEquals('Nouveau titre', $banner->title);
        Storage::disk('public')->assertExists($originalPath);
    }

    public function test_banner_image_validation_rejects_non_image(): void
    {
        Storage::fake('public');

        $originalImage = UploadedFile::fake()->image('original.jpg');
        $originalPath = $originalImage->store('banners', 'public');

        $banner = Banner::create([
            'title'       => 'Bannière Test',
            'image'       => $originalPath,
            'position'    => 'home_top',
            'target_type' => 'all',
            'is_active'   => true,
        ]);

        // Tenter d'uploader un fichier PDF
        $pdfFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->admin)
            ->put(route('admin.banners.update', $banner), [
                'title'       => $banner->title,
                'image'       => $pdfFile,
                'position'    => 'home_top',
                'target_type' => 'all',
                'is_active'   => '1',
            ]);

        $response->assertSessionHasErrors('image');

        // L'image originale doit être conservée
        $banner->refresh();
        $this->assertEquals($originalPath, $banner->image);
    }
}
