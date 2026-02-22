<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shield_csp_reports', function (Blueprint $table): void {
            $table->id();
            $table->json('payload')->nullable();
            $table->text('document_uri')->nullable();
            $table->text('blocked_uri')->nullable();
            $table->string('violated_directive')->nullable()->index();
            $table->string('effective_directive')->nullable()->index();
            $table->text('original_policy')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('received_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shield_csp_reports');
    }
};
