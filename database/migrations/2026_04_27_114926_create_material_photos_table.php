<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            // Kolom untuk menyimpan jumlah foto (disinkronkan otomatis oleh BarangController)
            if (!Schema::hasColumn('materials', 'Jumlah Foto')) {
                $table->unsignedTinyInteger('Jumlah Foto')->default(0)->after('updated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            if (Schema::hasColumn('materials', 'Jumlah Foto')) {
                $table->dropColumn('Jumlah Foto');
            }
        });
    }
};