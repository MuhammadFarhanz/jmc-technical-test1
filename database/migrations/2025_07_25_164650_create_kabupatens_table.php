<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kabupatens', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->unsignedBigInteger('provinsi_id');
            $table->integer('jumlah_penduduk')->default(0);
            $table->foreign('provinsi_id')->references('id')->on('provinsis')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kabupatens');
    }
};