<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('matricule')->unique()->after('id');
            $table->string('first_name')->after('matricule');
            $table->string('last_name')->after('first_name');
            $table->enum('gender', ['M', 'F'])->after('last_name');
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE')->after('gender');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'matricule',
                'first_name',
                'last_name',
                'gender',
                'status'
            ]);
        });
    }
};
