<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Ops\Audit;
use App\Domain\Tenancy\Invites;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Business;
use App\Models\Plan;
use App\Models\User;
use App\Rules\AvailableSlug;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BusinessController extends Controller
{
    public function home()
    {
        return view('admin.home', [
            'counts' => Business::selectRaw("status, count(*) c")->groupBy('status')->pluck('c', 'status'),
            'recent' => AuditLog::latest('id')->limit(10)->get(),
        ]);
    }

    public function index(Request $request)
    {
        $businesses = Business::with('plan')->withCount('users')
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('slug', 'like', "%$s%")))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest('id')->paginate(20)->withQueryString();

        return view('admin.businesses.index', compact('businesses'));
    }

    public function create()
    {
        return view('admin.businesses.create', ['plans' => Plan::available()->orderBy('sort_order')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => ['required', new AvailableSlug],
            'owner_name' => 'required|string|max:120',
            'owner_email' => ['required', 'email', Rule::unique('users', 'email')],
            'plan_id' => 'nullable|exists:plans,id',
            'status' => ['required', Rule::in(Business::STATUSES)],
            'domains' => ['nullable', 'string', 'max:2000', $this->domainRule()],
        ]);

        $business = DB::transaction(function () use ($data) {
            $business = Business::create(Arr::only($data, ['name', 'slug', 'plan_id', 'status']) + ['short_name' => Str::limit($data['name'], 30, '')]);
            $business->syncDomains(preg_split('/[\s,]+/', $data['domains'] ?? '', -1, PREG_SPLIT_NO_EMPTY));

            return $business;
        });

        [, $url] = app(Invites::class)->createUser([
            'business_id' => $business->id, 'name' => $data['owner_name'], 'email' => $data['owner_email'], 'role' => 'owner',
        ]);

        Audit::log('business.create', $business, ['slug' => $business->slug], $business->id);

        return redirect()->route('admin.businesses.show', $business)->with('invite_url', $url);
    }

    public function resendInvite(Business $business, User $user, Invites $invites)
    {
        abort_unless($user->business_id === $business->id, 404);

        $url = $invites->send($user->setRelation('business', $business));
        Audit::log('user.invite_sent', $user, ['email' => $user->email], $business->id);

        return back()->with('invite_url', $url)->with('status', 'Password link sent to '.$user->email.'.');
    }

    public function show(Business $business)
    {
        return view('admin.businesses.show', [
            'business' => $business->load('plan', 'domains', 'users'),
            'logs' => AuditLog::where('business_id', $business->id)->latest('id')->limit(15)->get(),
        ]);
    }

    public function edit(Business $business)
    {
        return view('admin.businesses.edit', ['business' => $business->load('domains'), 'plans' => Plan::orderBy('sort_order')->get()]);
    }

    public function update(Request $request, Business $business)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => ['required', new AvailableSlug($business->id)],
            'plan_id' => 'nullable|exists:plans,id',
            'status' => ['required', Rule::in(Business::STATUSES)],
            'short_name' => 'nullable|string|max:30',
            'theme_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'background_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'phone' => 'nullable|string|max:40',
            'address' => 'nullable|string|max:255',
            'website_url' => 'nullable|url|max:255',
            'timezone' => 'required|timezone',
            'domains' => ['nullable', 'string', 'max:2000', $this->domainRule()],
        ]);

        $before = $business->only(array_keys($data)) + ['domains' => $business->domains->pluck('domain')->all()];
        $business->update(Arr::except($data, 'domains'));
        $business->syncDomains(preg_split('/[\s,]+/', $data['domains'] ?? '', -1, PREG_SPLIT_NO_EMPTY));

        $after = $business->fresh()->only(array_keys($data)) + ['domains' => $business->domains()->pluck('domain')->all()];
        $changes = collect($after)->reject(fn ($v, $k) => $before[$k] == $v)->map(fn ($v, $k) => ['from' => $before[$k], 'to' => $v])->all();
        Audit::log('business.update', $business, $changes, $business->id);

        return redirect()->route('admin.businesses.show', $business)->with('status', 'Saved.');
    }

    private function domainRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) {
            $origins = app(\App\Domain\Api\OriginChecker::class);

            foreach (preg_split('/[\s,]+/', (string) $value, -1, PREG_SPLIT_NO_EMPTY) as $domain) {
                if (! preg_match('/^(\*\.)?([a-z0-9-]+\.)+[a-z]{2,}$/', $origins->normalize($domain))) {
                    $fail("\"$domain\" is not a valid domain.");
                }
            }
        };
    }

    public function regenerateKey(Business $business)
    {
        $business->forceFill(['public_key' => Business::newPublicKey()])->save();
        Audit::log('business.key_regenerated', $business, [], $business->id);

        return back()->with('status', 'New key generated. The old key stopped working immediately.');
    }
}
