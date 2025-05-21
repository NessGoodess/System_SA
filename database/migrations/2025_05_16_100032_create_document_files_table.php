<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_files', function (Blueprint $table) {
            $table->id();
            $table->uuid('document_id');
            $table->string('original_name');
            $table->string('stored_name');
            $table->string('file_path');
            $table->string('file_url')->nullable();
            $table->string('mime_type');
            $table->string('file_extension');
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('hash')->index();

            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();

            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');

            $table->index('document_id');
            $table->index('uploaded_by');
            $table->index('file_extension');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_files');
    }
};
