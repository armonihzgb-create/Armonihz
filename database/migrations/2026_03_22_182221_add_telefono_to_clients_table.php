<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            // Agregamos el campo teléfono, permitiendo que sea nulo por si el usuario no lo ingresa
            $table->string('telefono', 20)->nullable()->after('email');
        });
    }

    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('telefono');
        });
    }
};
