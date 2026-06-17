<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class WorkflowAction extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'workflow_actions';

    protected $fillable = [
        'tenant_id',
        'workflow_id',
        'action_type',
        'config',
        'order_index',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(AutomationWorkflow::class, 'workflow_id');
    }

}
