<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        // Alterar controle_financeiro
        if (Schema::hasColumn('controle_financeiro', 'cliente_id')) {
            Schema::table('controle_financeiro', function (Blueprint $table) {
                $table->dropForeign(['cliente_id']);
                $table->renameColumn('cliente_id', 'user_id');
            });

            Schema::table('controle_financeiro', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // Alterar parcelas
        if (Schema::hasColumn('parcelas', 'cliente_id')) {
            Schema::table('parcelas', function (Blueprint $table) {
                $table->dropForeign(['cliente_id']);
                $table->renameColumn('cliente_id', 'user_id');
            });

            Schema::table('parcelas', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        // Reverter controle_financeiro
        if (Schema::hasColumn('controle_financeiro', 'user_id')) {
            Schema::table('controle_financeiro', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->renameColumn('user_id', 'cliente_id');
            });

            Schema::table('controle_financeiro', function (Blueprint $table) {
                $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            });
        }

        // Reverter parcelas
        if (Schema::hasColumn('parcelas', 'user_id')) {
            Schema::table('parcelas', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->renameColumn('user_id', 'cliente_id');
            });

            Schema::table('parcelas', function (Blueprint $table) {
                $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            });
        }
    }
};

