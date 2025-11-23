<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('evaluation_items', function (Blueprint $t) {
      $t->id();
      $t->foreignId('evaluation_id')->constrained('evaluations')->cascadeOnDelete();
      $t->string('aspect', 64);     // manajemen_sekolah, kurikulum, dst.
      $t->string('indicator', 64);  // mis: kepemimpinan, perencanaan, dst.
      $t->enum('score', ['A','B','C','D'])->nullable();
      $t->string('evidence_path')->nullable(); // file bukti
      $t->text('notes')->nullable();           // catatan per indikator (opsional)
      $t->timestamps();

      $t->unique(['evaluation_id','aspect','indicator']);
      $t->index(['aspect','indicator']);
    });
  }
  public function down(): void { Schema::dropIfExists('evaluation_items'); }
};
