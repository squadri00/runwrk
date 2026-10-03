<?php

namespace App\Models;

use App\Domain\Tenancy\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'created_by', 'title', 'body', 'url', 'image_path', 'status', 'scheduled_at'])]
class PushMessage extends Model
{
    use BelongsToBusiness;

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime', 'started_at' => 'datetime', 'finished_at' => 'datetime', 'locked_until' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['scheduled', 'sending'], true);
    }

    public function processed(): int
    {
        return $this->success_count + $this->failure_count + $this->expired_count;
    }
}
