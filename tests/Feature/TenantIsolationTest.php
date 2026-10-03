<?php

namespace Tests\Feature;

use App\Domain\Tenancy\CurrentBusiness;
use App\Models\Business;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\Support\TenantNote;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    private Business $a;

    private Business $b;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('tenant_notes', function ($t) {
            $t->id();
            $t->unsignedBigInteger('business_id')->index();
            $t->string('body');
            $t->timestamps();
        });

        $this->a = Business::factory()->create();
        $this->b = Business::factory()->create();

        app(CurrentBusiness::class)->run($this->a, fn () => TenantNote::create(['body' => 'a1']));
        app(CurrentBusiness::class)->run($this->b, fn () => TenantNote::create(['body' => 'b1']));
        app(CurrentBusiness::class)->run($this->b, fn () => TenantNote::create(['body' => 'b2']));
        app(CurrentBusiness::class)->forget();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('tenant_notes');
        parent::tearDown();
    }

    public function test_no_business_resolved_returns_nothing(): void
    {
        $this->assertSame(0, TenantNote::count());
    }

    public function test_each_business_sees_only_its_own_rows(): void
    {
        $current = app(CurrentBusiness::class);

        $this->assertSame(['a1'], $current->run($this->a, fn () => TenantNote::pluck('body')->all()));
        $this->assertEqualsCanonicalizing(['b1', 'b2'], $current->run($this->b, fn () => TenantNote::pluck('body')->all()));
        $this->assertSame(2, $current->run($this->b, fn () => TenantNote::count()));
    }

    public function test_other_tenants_row_cannot_be_found_by_id(): void
    {
        $bRow = TenantNote::withoutBusinessScope()->where('business_id', $this->b->id)->first();

        $found = app(CurrentBusiness::class)->run($this->a, fn () => TenantNote::find($bRow->id));

        $this->assertNull($found);
    }

    public function test_other_tenants_row_cannot_be_updated_or_deleted(): void
    {
        $current = app(CurrentBusiness::class);

        $current->run($this->a, function () {
            $this->assertSame(1, TenantNote::query()->update(['body' => 'edited']));
            $this->assertSame(1, TenantNote::query()->delete());
        });

        $this->assertSame(2, TenantNote::withoutBusinessScope()->where('business_id', $this->b->id)->count());
    }

    public function test_business_id_is_filled_from_current_business(): void
    {
        $note = app(CurrentBusiness::class)->run($this->a, fn () => TenantNote::create(['body' => 'x']));

        $this->assertSame($this->a->id, $note->business_id);
    }

    public function test_run_restores_previous_business(): void
    {
        $current = app(CurrentBusiness::class);
        $current->set($this->a);

        $current->run($this->b, fn () => $this->assertSame($this->b->id, $current->id()));

        $this->assertSame($this->a->id, $current->id());
    }

    public function test_withoutBusinessScope_is_the_only_way_across(): void
    {
        $this->assertSame(3, TenantNote::withoutBusinessScope()->count());
    }
}
