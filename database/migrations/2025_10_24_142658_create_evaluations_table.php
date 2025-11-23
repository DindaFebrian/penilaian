<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('evaluations', function (Blueprint $t) {
      $t->id();
      $t->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
      $t->foreignId('pengawas_id')->constrained('users')->cascadeOnDelete(); // user role pengawas
      $t->date('tanggal')->default(now());
      $t->enum('status', ['draft','submitted'])->default('draft');
      $t->text('overall_notes')->nullable(); // catatan & rekomendasi manajemen
      $t->timestamps();
      $t->unique(['school_id','pengawas_id','tanggal']); // 1 penilaian/hari/pengawas
    });
  }
  public function down(): void { Schema::dropIfExists('evaluations'); }
};
