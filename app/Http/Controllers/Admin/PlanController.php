<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Ops\Audit;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlanController extends Controller
{
    public function index()
    {
        return view('admin.plans.index', ['plans' => Plan::withCount('businesses')->orderBy('sort_order')->orderBy('price_cents')->get()]);
    }

    public function create()
    {
        return view('admin.plans.form', ['plan' => new Plan(['interval' => 'month', 'currency' => 'usd', 'is_public' => true])]);
    }

    public function store(Request $request)
    {
        $plan = Plan::create($this->data($request));
        Audit::log('plan.create', $plan, ['code' => $plan->code]);

        return redirect()->route('admin.plans.index')->with('status', 'Plan created.');
    }

    public function edit(Plan $plan)
    {
        return view('admin.plans.form', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $plan->update($this->data($request, $plan));
        Audit::log('plan.update', $plan, $plan->getChanges());

        return redirect()->route('admin.plans.index')->with('status', 'Plan saved.');
    }

    public function archive(Plan $plan)
    {
        $plan->update(['archived_at' => $plan->archived_at ? null : now()]);
        Audit::log($plan->archived_at ? 'plan.archive' : 'plan.restore', $plan);

        return back()->with('status', $plan->archived_at ? 'Plan archived. Existing customers keep it; it is hidden from signup.' : 'Plan restored.');
    }

    private function data(Request $request, ?Plan $plan = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:80',
            'code' => ['required', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/', 'max:40', Rule::unique('plans', 'code')->ignore($plan?->id)],
            'description' => 'nullable|string|max:300',
            'price' => 'required|numeric|min:0|max:100000',
            'interval' => 'required|in:month,year',
            'stripe_price_id' => 'nullable|string|max:120',
            'features' => 'nullable|string|max:2000',
            'sort_order' => 'nullable|integer|min:0|max:1000',
        ]);

        return [
            'name' => $data['name'],
            'code' => $data['code'],
            'description' => $data['description'] ?? null,
            'price_cents' => (int) round($data['price'] * 100),
            'currency' => 'usd',
            'interval' => $data['interval'],
            'stripe_price_id' => $data['stripe_price_id'] ?? null,
            'features' => array_values(array_filter(array_map('trim', preg_split('/\R/', $data['features'] ?? '')))),
            'is_public' => $request->boolean('is_public'),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }
}
