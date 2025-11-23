<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('visits', function (Blueprint $table) {
      $table->timestamp('declined_at')->nullable()->after('accepted_at');
      $table->string('decline_reason', 500)->nullable()->after('declined_at');
    });
  }
  public function down(): void {
    Schema::table('visits', function (Blueprint $table) {
      $table->dropColumn(['declined_at','decline_reason']);
    });
  }
};
