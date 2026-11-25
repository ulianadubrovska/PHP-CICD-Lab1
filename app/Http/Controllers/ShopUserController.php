<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ShopUserController extends Controller
{
    public function index(): View
    {
        $users = User::withCount(['orders', 'reviews'])
            ->orderBy('name')
            ->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.form', ['user' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()
            ->route('shop.users.index')
            ->with('success', 'Покупця успішно створено!');
    }

    public function edit(int $id): View
    {
        return view('users.form', [
            'user' => User::findOrFail($id),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($id),
            ],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('shop.users.index')
            ->with('success', 'Дані покупця оновлено!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if (
            $user->orders()->exists()
            || $user->cart()->exists()
            || $user->reviews()->exists()
        ) {
            return redirect()
                ->route('shop.users.index')
                ->with('error', 'Покупця з пов’язаними записами видаляти не можна.');
        }

        $user->delete();

        return redirect()
            ->route('shop.users.index')
            ->with('success', 'Покупця успішно видалено!');
    }
}
