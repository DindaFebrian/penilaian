<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Profil Sekolah
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('nama');                                  // TK Penwari
            $table->string('npsn', 20)->nullable()->unique();        // 69096869
            $table->string('jenjang', 20)->nullable();               // TK/SD/SMP dst.
            $table->string('status_kepemilikan', 20)->nullable();    // Negeri/Swasta
            $table->date('tanggal_sk_sekolah')->nullable();          // 1977-09-01
            $table->string('alamat', 255)->nullable();               // Alamat lengkap
            $table->string('kepala_sekolah', 120)->nullable();       // N Jubedah S.Pd
            $table->string('email', 150)->nullable();
            $table->foreignId('user_id')
                      ->nullable()
                      ->constrained('users')
                      ->nullOnDelete();

            // Status kelengkapan (panel kiri bawah)
            $table->boolean('complete_profile')->default(false);
            $table->boolean('complete_guru')->default(false);
            $table->boolean('complete_siswa')->default(false);
            $table->boolean('complete_dokumen')->default(false);
            $table->boolean('complete_sarpras')->default(false);

            // Persetujuan (Ditolak/Terima)
            $table->enum('review_status', ['pending','approved','rejected'])->default('pending');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();

            $table->timestamps();
        });

        // 2) Data Guru (tabel detail)
        Schema::create('school_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('nama', 120);
            $table->string('nip_nik', 40)->nullable();
            $table->string('pangkat_golongan', 60)->nullable();  // contoh: PENATA (III/c)
            $table->string('jabatan', 100)->nullable();          // contoh: Guru TK
            $table->boolean('sertifikasi')->default(false);      // Ya/Tidak
            $table->timestamps();

            $table->index(['school_id','nama']);
        });

        // 3) Rekap Data Siswa + file (sesuai “Jumlah Kelas, Laki-Laki, Perempuan, Jumlah Siswa”)
        Schema::create('school_student_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('tahun_ajaran', 9)->nullable();   // contoh: 2024/2025
            $table->unsignedInteger('jumlah_kelas')->default(0);
            $table->unsignedInteger('laki_laki')->default(0);
            $table->unsignedInteger('perempuan')->default(0);
            $table->unsignedInteger('jumlah_siswa')->default(0);
            $table->string('file_path')->nullable();         // lampiran “File Data Siswa”
            $table->timestamps();

            $table->unique(['school_id','tahun_ajaran']);
        });

        // 4) Dokumen Administrasi (RKS, RKAS, EDS) + file
        Schema::create('school_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->enum('jenis', ['RKS','RKAS','EDS','LAINNYA'])->default('LAINNYA');
            $table->string('nama', 120)->nullable(); // opsional: judul dokumen
            $table->string('file_path');             // path file (storage)
            $table->date('tanggal_upload')->nullable();
            $table->timestamps();

            $table->index(['school_id','jenis']);
        });

        // 5) Kondisi Sarana & Prasarana (Item, Jumlah, Kondisi, Keterangan) + file bukti
        Schema::create('school_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('item', 120);                            // Ruang Kelas / Toilet Siswa / Air Bersih
            $table->unsignedInteger('jumlah')->nullable();          // boleh null (jika "-")
            $table->enum('kondisi', ['Baik','Cukup','Rusak Ringan','Rusak Berat'])->nullable();
            $table->string('keterangan', 255)->nullable();          // contoh: Perlu renovasi
            $table->string('file_path')->nullable();                // tombol "File" per baris
            $table->timestamps();

            $table->index(['school_id','item']);
        });
         Schema::table('schools', function (Blueprint $table) {
            if (!Schema::hasColumn('schools', 'nama')) {
                $table->string('nama');
            }
            if (!Schema::hasColumn('schools', 'alamat')) {
                $table->string('alamat', 255)->nullable();
            }
        });

    }

    public function down(): void
    {
    Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('schools')) {
            Schema::table('schools', function (Blueprint $table) {
                if (Schema::hasColumn('schools', 'alamat')) {
                    $table->dropColumn('alamat');
                }
                if (Schema::hasColumn('schools', 'nama')) {
                    $table->dropColumn('nama');
                }
            });
        }

        // Drop tabel anak dulu, lalu induk
        Schema::dropIfExists('school_facilities');
        Schema::dropIfExists('school_documents');
        Schema::dropIfExists('school_student_stats');
        Schema::dropIfExists('school_teachers');
        Schema::dropIfExists('schools');

        Schema::enableForeignKeyConstraints();
    }

};
