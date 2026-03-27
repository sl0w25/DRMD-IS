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
        Schema::create('sitreps', function (Blueprint $table) {
            $table->id();
            $table->string('incident_type');
            $table->string('incident_date');
            $table->string('incident_time');
            $table->string('province');
            $table->string('municipality');
            $table->string('barangay');
            $table->longText('overview');
            $table->string('affected_families');
            $table->string('affected_individuals');
            $table->string('partially_damaged')->nullable();
            $table->string('totally_damaged')->nullable();
            $table->boolean('open_ec')->default(false);
            $table->boolean('casulaties')->default(false);
            $table->string('displaced_family')->nullable();
            $table->string('displaced_individual')->nullable();
            $table->string('dead');
            $table->string('injured');
            $table->string('missing');
            $table->string('dswd_cost')->nullable();
            $table->string('lgu_cost')->nullable();
            $table->string('ngo_cost')->nullable();
            $table->string('partners_cost')->nullable();
            $table->string('status')->default('For Review');
            $table->string('submitted_by')->default(false);
            $table->string('created_by');
            $table->string('reviewed_by')->nullable();
            $table->text('sitrep_file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sitreps');
    }
};
