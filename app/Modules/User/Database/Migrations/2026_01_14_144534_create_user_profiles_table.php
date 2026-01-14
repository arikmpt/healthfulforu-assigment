<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->text('avatar_url')->nullable();
            $table->char('country', 2);
            $table->string('language', 10);
            $table->string('timezone', 50)->nullable();
            $table->string('phone', 30)->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('phone');
            $table->index('country');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
