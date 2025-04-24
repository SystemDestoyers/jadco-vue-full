<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Section;
use App\Models\SectionImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContentControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $page;
    protected $section;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->user = User::factory()->create([
            'role' => 'admin'
        ]);
        
        // Create a page
        $this->page = Page::create([
            'name' => 'Test Page',
            'slug' => 'test-page',
            'template' => 'default'
        ]);
        
        // Create a section
        $this->section = Section::create([
            'page_id' => $this->page->id,
            'name' => 'Test Section',
            'order' => 1,
            'content' => json_encode([
                'title' => 'Test Title',
                'description' => 'Test Description'
            ])
        ]);
    }

    /** @test */
    public function it_can_get_page_content_by_slug()
    {
        $response = $this->actingAs($this->user)
            ->getJson("/api/admin/content/{$this->page->slug}");
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'template',
                    'sections'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Page content retrieved successfully'
            ]);
    }
    
    /** @test */
    public function it_returns_404_if_page_not_found()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/admin/content/non-existent-page');
        
        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Page not found',
                'data' => null
            ]);
    }
    
    /** @test */
    public function it_can_update_section_content()
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/admin/content/section/{$this->section->id}/update", [
                'key' => 'title',
                'value' => 'Updated Title'
            ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'page_id',
                    'name',
                    'order',
                    'content',
                    'images'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Section content updated successfully'
            ]);
        
        // Check the content was updated
        $updatedSection = Section::find($this->section->id);
        $content = json_decode($updatedSection->content, true);
        $this->assertEquals('Updated Title', $content['title']);
    }
    
    /** @test */
    public function it_can_update_section_image()
    {
        Storage::fake('public');
        
        $file = UploadedFile::fake()->image('test.jpg');
        
        $response = $this->actingAs($this->user)
            ->postJson("/api/admin/content/section/{$this->section->id}/image", [
                'image' => $file,
                'alt' => 'Test Alt Text'
            ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'page_id',
                    'name',
                    'order',
                    'content',
                    'images'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Section image updated successfully'
            ]);
        
        // Check the image was created
        $this->assertCount(1, SectionImage::where('section_id', $this->section->id)->get());
    }
    
    /** @test */
    public function it_validates_content_update_request()
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/admin/content/section/{$this->section->id}/update", [
                // Missing key and value
            ]);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['key', 'value']);
    }
    
    /** @test */
    public function it_validates_image_update_request()
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/admin/content/section/{$this->section->id}/image", [
                // Missing image
            ]);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['image']);
    }
} 