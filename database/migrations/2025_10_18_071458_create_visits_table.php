?<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->date('visit_date');
            $table->time('visit_time');
            $table->enum('status', ['requested','scheduled','rejected','done'])->default('requested');
            $table->foreignId('pengawas_id')->nullable()->constrained('users')->nullOnDelete(); // user role pengawas
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete(); // user role admin
            $table->text('note')->nullable(); // alasan tolak / catatan
            $table->timestamps();
            $table->unique(['school_id','visit_date','visit_time']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('visits');
    }
};
