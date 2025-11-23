<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visitations', function (Blueprint $table) {
            $table->id();

            // relasi ke sekolah
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();

            // tanggal & jam visitasi
            $table->date('tanggal_visitasi');         // contoh: 2025-04-12
            $table->time('jam_mulai');                // contoh: 09:00:00
            $table->time('jam_selesai');              // contoh: 12:00:00

            // status untuk badge: Tuntas/Rencana/Tidak Valid
            $table->enum('status', ['rencana','tuntas','tidak_valid'])->default('rencana');

            // opsional
            $table->text('catatan')->nullable();      // catatan pengawas
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // pengawas yang menjadwalkan

            $table->timestamps();

            $table->index(['tanggal_visitasi','status']);
            $table->unique(['school_id','tanggal_visitasi','jam_mulai','jam_selesai'], 'visitations_unique_slot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitations');
    }
};
