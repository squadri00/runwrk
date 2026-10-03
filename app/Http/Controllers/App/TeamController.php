<?php

namespace App\Http\Controllers\App;

use App\Domain\Ops\Audit;
use App\Domain\Tenancy\CurrentBusiness;
use App\Domain\Tenancy\Invites;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    public function index(CurrentBusiness $current)
    {
        return view('app.team', ['users' => $current->getOrFail()->users()->orderBy('role')->orderBy('name')->get()]);
    }

    public function store(Request $request, CurrentBusiness $current, Invites $invites)
    {
        $business = $current->getOrFail();

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')],
            'role' => ['required', Rule::in(['owner', 'staff'])],
        ]);

        [$user] = $invites->createUser($data + ['business_id' => $business->id]);
        $user->setRelation('business', $business);
        Audit::log('team.invite', $user, ['email' => $user->email, 'role' => $user->role]);

        return back()->with('status', 'Invitation sent to '.$user->email.'. They choose their own password.');
    }

    public function resend(int $id, CurrentBusiness $current, Invites $invites)
    {
        $business = $current->getOrFail();
        $user = $business->users()->findOrFail($id);

        $invites->send($user->setRelation('business', $business));

        return back()->with('status', 'Password link sent to '.$user->email.'.');
    }

    public function destroy(int $id, CurrentBusiness $current)
    {
        $business = $current->getOrFail();
        $user = $business->users()->findOrFail($id);

        if ($user->id === Auth::guard('web')->id()) {
            return back()->with('error', "You can't remove yourself.");
        }

        if ($user->role === 'owner' && $business->users()->where('role', 'owner')->count() <= 1) {
            return back()->with('error', 'A business needs at least one owner.');
        }

        Audit::log('team.remove', $user, ['email' => $user->email]);
        $user->forceDelete();

        return back()->with('status', 'Removed.');
    }
}
