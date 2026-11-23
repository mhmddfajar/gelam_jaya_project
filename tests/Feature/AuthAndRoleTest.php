<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Hamlet;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthAndRoleTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $stafUser;
    protected User $wargaUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@gelamjaya.test',
            'password' => Hash::make('password'),
            'role' => Role::ADMIN,
            'is_active' => true,
        ]);

        $this->stafUser = User::create([
            'name' => 'Staf Test',
            'email' => 'staf@gelamjaya.test',
            'password' => Hash::make('password'),
            'role' => Role::STAF,
            'is_active' => true,
        ]);

        $this->wargaUser = User::create([
            'name' => 'Warga Test',
            'email' => 'warga@gelamjaya.test',
            'password' => Hash::make('password'),
            'role' => Role::WARGA,
            'is_active' => true,
        ]);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_user_can_authenticate_using_login_screen_and_redirect_by_role(): void
    {
        // 1. Admin redirect
        $responseAdmin = $this->post('/login', [
            'email' => 'admin@gelamjaya.test',
            'password' => 'password',
        ]);
        $this->assertAuthenticatedAs($this->adminUser);
        $responseAdmin->assertRedirect(route('admin.dashboard'));
        $this->post('/logout');

        // 2. Staf redirect
        $responseStaf = $this->post('/login', [
            'email' => 'staf@gelamjaya.test',
            'password' => 'password',
        ]);
        $this->assertAuthenticatedAs($this->stafUser);
        $responseStaf->assertRedirect(route('staf.residents.index'));
        $this->post('/logout');

        // 3. Warga redirect
        $responseWarga = $this->post('/login', [
            'email' => 'warga@gelamjaya.test',
            'password' => 'password',
        ]);
        $this->assertAuthenticatedAs($this->wargaUser);
        $responseWarga->assertRedirect(route('warga.requests.index'));
    }

    public function test_authenticated_user_visiting_home_or_login_is_redirected_by_role(): void
    {
        // Admin visiting / and /login
        $this->actingAs($this->adminUser)->get('/')->assertRedirect(route('admin.dashboard'));
        $this->actingAs($this->adminUser)->get('/login')->assertRedirect(route('admin.dashboard'));

        // Staf visiting / and /login
        $this->actingAs($this->stafUser)->get('/')->assertRedirect(route('staf.residents.index'));
        $this->actingAs($this->stafUser)->get('/login')->assertRedirect(route('staf.residents.index'));

        // Warga visiting / and /login
        $this->actingAs($this->wargaUser)->get('/')->assertRedirect(route('warga.requests.index'));
        $this->actingAs($this->wargaUser)->get('/login')->assertRedirect(route('warga.requests.index'));
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::create([
            'name' => 'Inactive User',
            'email' => 'inactive@gelamjaya.test',
            'password' => Hash::make('password'),
            'role' => Role::WARGA,
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'inactive@gelamjaya.test',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_logout(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_role_authorization_staf_and_warga_cannot_access_admin_dashboard(): void
    {
        // Warga cannot access admin dashboard
        $responseWarga = $this->actingAs($this->wargaUser)->get('/admin/dashboard');
        $responseWarga->assertStatus(403);

        // Staf cannot access admin dashboard
        $responseStaf = $this->actingAs($this->stafUser)->get('/admin/dashboard');
        $responseStaf->assertStatus(403);

        // Admin can access admin dashboard
        $responseAdmin = $this->actingAs($this->adminUser)->get('/admin/dashboard');
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertInertia(fn ($page) => $page
            ->component('Admin/Dashboard')
            ->has('metrics')
            ->has('requestStats')
            ->has('hamletDistribution')
            ->has('recentRequests')
        );
    }

    public function test_role_authorization_warga_cannot_access_staf_routes(): void
    {
        $response = $this->actingAs($this->wargaUser)->get('/staf/pengajuan');
        $response->assertStatus(403);
    }

    public function test_user_can_change_password_with_valid_current_password(): void
    {
        $response = $this->actingAs($this->wargaUser)->put('/akun/ganti-password', [
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('newpassword123', $this->wargaUser->fresh()->password));
    }

    public function test_user_cannot_change_password_with_invalid_current_password(): void
    {
        $response = $this->actingAs($this->wargaUser)->put('/akun/ganti-password', [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    public function test_admin_can_toggle_user_active_status(): void
    {
        $this->assertTrue($this->stafUser->is_active);

        $response = $this->actingAs($this->adminUser)->patch("/admin/pengguna/{$this->stafUser->id}/toggle-active");
        $response->assertSessionHasNoErrors();

        $this->assertFalse($this->stafUser->fresh()->is_active);
    }

    public function test_admin_cannot_deactivate_own_account(): void
    {
        $response = $this->actingAs($this->adminUser)->patch("/admin/pengguna/{$this->adminUser->id}/toggle-active");
        $response->assertSessionHas('error');

        $this->assertTrue($this->adminUser->fresh()->is_active);
    }

    public function test_admin_can_reset_user_password(): void
    {
        $response = $this->actingAs($this->adminUser)->post("/admin/pengguna/{$this->stafUser->id}/reset-password");
        $response->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('password123', $this->stafUser->fresh()->password));
    }

    public function test_warga_can_register_via_two_step_registration(): void
    {
        $hamlet = Hamlet::create(['name' => 'Dusun Test', 'head_name' => 'Kepala Test']);

        $response = $this->post('/register', [
            'email' => 'warga.baru@gelamjaya.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nik' => '3201123456789012',
            'family_card_no' => '3201123456789013',
            'name' => 'Budi Santoso',
            'birth_place' => 'Tangerang',
            'birth_date' => '1990-05-15',
            'gender' => 'L',
            'religion' => 'Islam',
            'marital_status' => 'Kawin',
            'address' => 'Jl. Gelam No. 12',
            'rt' => '001',
            'rw' => '002',
            'hamlet_id' => $hamlet->id,
            'occupation' => 'Karyawan',
            'education' => 'S1',
            'citizenship' => 'WNI',
        ]);

        $response->assertRedirect(route('warga.requests.index'));
        $this->assertAuthenticated();

        $newUser = User::where('email', 'warga.baru@gelamjaya.test')->first();
        $this->assertNotNull($newUser);
        $this->assertEquals(Role::WARGA, $newUser->role);
        $this->assertNotNull($newUser->resident_id);

        $resident = Resident::find($newUser->resident_id);
        $this->assertNotNull($resident);
        $this->assertEquals('3201123456789012', $resident->nik);
        $this->assertEquals('Budi Santoso', $resident->name);
        $this->assertFalse($resident->is_verified);
    }
}

