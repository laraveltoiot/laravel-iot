<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $secret_key
 * @property string|null $wifi_ssid
 * @property string|null $wifi_password
 * @property \Illuminate\Support\Carbon|null $last_seen_at
 * @property bool $is_active
 * @property string|null $firmware_version
 * @property string|null $ip_last
 * @property string|null $mac_address
 * @property array|null $metadata
 * @property array|null $settings
 */
final class Device extends Model
{
    protected $guarded = ['id'];

    protected $hidden = ['secret_key', 'wifi_password'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_seen_at' => 'datetime',
            'metadata' => 'array',
            'settings' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
