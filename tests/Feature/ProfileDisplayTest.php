<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileDisplayTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that user profile displays correct data.
     */
    public function test_user_profile_displays_correct_data(): void
    {
        // Create a test user with all the new fields
        $user = User::factory()->create([
            'first_name' => 'John',
            'middle_name' => 'Michael',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'gender' => 'male',
            'mobile_number' => '+1234567890',
            'address' => '123 Main St, City, State 12345',
            'birth_date' => '1990-01-15',
            'role' => 'client',
        ]);

        // Test the name accessor
        $this->assertEquals('John Michael Doe', $user->name);
        $this->assertEquals('John Doe', $user->full_name);

        // Test that the client profile page loads and shows user data
        $response = $this->actingAs($user)->get(route('client.profile'));
        
        $response->assertStatus(200)
                 ->assertSee('John')
                 ->assertSee('Michael')
                 ->assertSee('Doe')
                 ->assertSee('john.doe@example.com')
                 ->assertSee('+1234567890')
                 ->assertSee('123 Main St, City, State 12345')
                 ->assertSee('male');
    }

    /**
     * Test that user profile can be updated.
     */
    public function test_user_profile_can_be_updated(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'gender' => 'female',
            'mobile_number' => '+0987654321',
            'address' => '456 Oak St',
            'birth_date' => '1985-05-20',
        ]);

        $response = $this->actingAs($user)
                         ->patch(route('client.profile.update'), [
                             'first_name' => 'Jane',
                             'middle_name' => 'Marie',
                             'last_name' => 'Johnson',
                             'email' => 'jane.johnson@example.com',
                             'gender' => 'female',
                             'mobile_number' => '+1122334455',
                             'address' => '789 Pine St, New City',
                             'birth_date' => '1985-05-20',
                         ]);

        $response->assertRedirect(route('client.profile'))
                 ->assertSessionHas('status', 'profile-updated');

        // Verify the user data was updated
        $user->refresh();
        $this->assertEquals('Jane Marie Johnson', $user->name);
        $this->assertEquals('jane.johnson@example.com', $user->email);
        $this->assertEquals('+1122334455', $user->mobile_number);
        $this->assertEquals('789 Pine St, New City', $user->address);
    }
}
