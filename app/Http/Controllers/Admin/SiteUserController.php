<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SiteUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'banned') {
                $query->where('is_banned', true);
            } elseif ($request->status === 'active') {
                $query->where('is_banned', false);
            }
        }

        $users = $query->orderByDesc('created_at')->paginate(15);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($users);
        }

        return view('admin.site-users.index', compact('users'));
    }

    public function ban(Request $request, User $user)
    {
        $validated = $request->validate([
            'is_banned' => ['required', 'boolean'],
        ]);

        $user->is_banned = $validated['is_banned'];
        $user->save();

        return back()->with('success', 'User status updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }
}

