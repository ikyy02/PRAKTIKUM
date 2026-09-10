<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationDomainTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_rejects_email_outside_mhs_domain(): void
    {
        $this->post(route('register'), [
            'name' => 'Orang Luar',
            'email' => 'orangluar@gmail.com',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertDatabaseMissing('users', ['email' => 'orangluar@gmail.com']);
    }

    public function test_registration_accepts_mhs_domain_email(): void
    {
        $this->post(route('register'), [
            'name' => 'Mahasiswa Politala',
            'email' => 'Mahasiswa@mhs.politala.ac.id',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', ['email' => 'mahasiswa@mhs.politala.ac.id']);
    }

    public function test_admin_original_email_can_still_login(): void
    {
        User::factory()->create([
            'email' => 'admin@labkom.test',
            'role' => 'admin',
        ]);

        $this->post(route('login'), [
            'email' => 'admin@labkom.test',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }
}
