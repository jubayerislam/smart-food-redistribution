<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('suspended_at')->nullable()->after('organization_name');
            $table->text('suspension_reason')->nullable()->after('suspended_at');
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->boolean('is_hidden')->default(false)->after('status');
            $table->foreignId('moderated_by')->nullable()->after('receiver_id')->constrained('users')->nullOnDelete();
            $table->timestamp('moderated_at')->nullable()->after('moderated_by');
            $table->text('moderation_reason')->nullable()->after('moderated_at');
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('moderated_by');
            $table->dropColumn(['is_hidden', 'moderated_at', 'moderation_reason']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['suspended_at', 'suspension_reason']);
        });
    }
};
