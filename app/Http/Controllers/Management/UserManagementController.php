<?php

namespace App\Http\Controllers\Management;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('facility')->latest()->paginate(20);

        return view('users.index', compact('users'));
    }

    public function edit(User $user)
    {
        $facilities = Facility::where('is_active', true)->orderBy('name')->get();
        $roles = UserRole::cases();

        return view('users.edit', compact('user', 'facilities', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate(['name' => 'required|string|max:255', 'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)], 'role' => ['required', Rule::in(array_map(fn ($r) => $r->value, UserRole::cases()))], 'facility_id' => 'nullable|exists:facilities,id', 'is_active' => 'nullable|boolean', 'password' => 'nullable|string|min:8|confirmed']);
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->facility_id = $data['facility_id'] ?? null;
        $user->is_active = $request->boolean('is_active');
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }$user->save();

        return redirect()->route('users.index')->with('success', 'تم تحديث المستخدم والصلاحيات.');
    }
}
