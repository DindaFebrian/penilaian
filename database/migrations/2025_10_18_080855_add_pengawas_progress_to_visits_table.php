?<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('visits', function (Blueprint $table) {
      $table->timestamp('accepted_at')->nullable()->after('approved_by');   // pengawas klik Terima
      $table->timestamp('completed_at')->nullable()->after('accepted_at');  // selesai visit
      $table->string('report_file')->nullable()->after('completed_at');     // file laporan (opsional)
      $table->text('report_summary')->nullable()->after('report_file');     // ringkasan hasil (opsional)
    });
  }
  public function down(): void {
    Schema::table('visits', function (Blueprint $table) {
      $table->dropColumn(['accepted_at','completed_at','report_file','report_summary']);
    });
  }
};
