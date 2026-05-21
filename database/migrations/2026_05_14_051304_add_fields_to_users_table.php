<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('email');
            $table->string('phone')->nullable()->after('avatar');
            $table->string('timezone')->default('UTC')->after('phone');
            $table->string('locale')->default('en')->after('timezone');
            $table->boolean('email_notifications')->default(true)->after('locale');
            $table->boolean('push_notifications')->default(true)->after('email_notifications');
            $table->boolean('dark_mode')->default(false)->after('push_notifications');
            $table->timestamp('last_login_at')->nullable()->after('dark_mode');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar', 'phone', 'timezone', 'locale',
                'email_notifications', 'push_notifications', 'dark_mode', 'last_login_at',
            ]);
            $table->dropSoftDeletes();
        });
    }
};
