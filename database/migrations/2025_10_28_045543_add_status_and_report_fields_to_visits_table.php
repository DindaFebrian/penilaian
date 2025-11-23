<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_status_and_report_fields_to_visits_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('visits', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pengawas_id')->nullable()->constrained('users')->nullOnDelete();

            $table->date('visit_date')->nullable();
            $table->time('visit_time')->nullable();

            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->string('decline_reason')->nullable();

            $table->string('status')->default('scheduled'); // scheduled|accepted|rejected|done
            $table->timestamp('completed_at')->nullable();

            $table->string('report_file')->nullable();
            $table->text('report_summary')->nullable();
        });
    }

    public function down(): void {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('school_id');
            $table->dropConstrainedForeignId('pengawas_id');
            $table->dropColumn(['visit_date','visit_time','accepted_at','declined_at','decline_reason','status','completed_at','report_file','report_summary']);
        });
    }
};

