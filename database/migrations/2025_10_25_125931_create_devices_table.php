<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('secret_key');
            $table->string('wifi_ssid')->nullable();
            $table->text('wifi_password')->nullable();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->boolean('is_active')->default(false)->index();
            $table->string('firmware_version')->nullable();
            $table->string('ip_last')->nullable();
            $table->string('mac_address')->nullable();
            $table->json('metadata')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
