<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /** Daftar user + search + pagination + statistik role (FR-002). */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $role   = $request->input('role');            // '' | admin | waiters | ...
        $status = $request->input('status');          // '' | active | inactive

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, fn ($query, $role) => $query->where('role', $role))
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total'   => User::count(),
            'admin'   => User::where('role', 'admin')->count(),
            'kasir'   => User::where('role', 'kasir')->count(),
            'waiters' => User::where('role', 'waiters')->count(),
            'new'     => User::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('admin.users.index', compact('users', 'search', 'role', 'status', 'stats'));
    }

    public function create()
    {
        return view('admin.users.create', [
            'roles' => $this->roles(),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        // password otomatis di-hash oleh cast 'hashed' di User model.
        User::create($request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User baru berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user'  => $user,
            'roles' => $this->roles(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        // BR-006: admin tidak boleh menonaktifkan dirinya sendiri (lewat form edit).
        if ($user->id === auth()->id() && ! $data['is_active']) {
            return back()
                ->withInput()
                ->with('error', 'Anda tidak bisa menonaktifkan akun sendiri.');
        }

        // Password kosong = jangan diubah.
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // BR-006: tidak boleh hapus diri sendiri.
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        // BR-006: admin terakhir tidak boleh dihapus.
        if ($user->isRole('admin') && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Admin terakhir tidak bisa dihapus.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /** Toggle aktif/nonaktif (FR-002). */
    public function toggleStatus(User $user)
    {
        // BR-006: tidak boleh menonaktifkan diri sendiri.
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with(
            'success',
            $user->is_active ? 'User diaktifkan.' : 'User dinonaktifkan.'
        );
    }

    /** Daftar role valid (dipakai form create & edit). */
    private function roles(): array
    {
        return ['admin', 'waiters', 'kitchen', 'barista', 'kasir'];
    }
}
