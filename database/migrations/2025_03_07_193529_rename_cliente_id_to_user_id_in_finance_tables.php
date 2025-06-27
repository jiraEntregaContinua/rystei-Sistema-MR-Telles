<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('controle_financeiro', 'cliente_id')) {
            try {
                Schema::table('controle_financeiro', function (Blueprint $table) {
                    // Aqui droppa a foreign key pelo nome dela (ajuste para o nome real)
                    $table->dropForeign('controle_financeiro_cliente_id_foreign');
                });
            } catch (\Exception $e) {
                // Constraint não existe, ignora o erro
            }

            Schema::table('controle_financeiro', function (Blueprint $table) {
                $table->renameColumn('cliente_id', 'user_id');
            });

            Schema::table('controle_financeiro', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // Repetir o mesmo para parcelas
        if (Schema::hasColumn('parcelas', 'cliente_id')) {
            try {
                Schema::table('parcelas', function (Blueprint $table) {
                    $table->dropForeign('parcelas_cliente_id_foreign');
                });
            } catch (\Exception $e) {
                // Constraint não existe, ignora o erro
            }

            Schema::table('parcelas', function (Blueprint $table) {
                $table->renameColumn('cliente_id', 'user_id');
            });

            Schema::table('parcelas', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        // Idem para o down, com try/catch na remoção das foreign keys
        if (Schema::hasColumn('controle_financeiro', 'user_id')) {
            try {
                Schema::table('controle_financeiro', function (Blueprint $table) {
                    $table->dropForeign('controle_financeiro_user_id_foreign');
                });
            } catch (\Exception $e) {
            }

            Schema::table('controle_financeiro', function (Blueprint $table) {
                $table->renameColumn('user_id', 'cliente_id');
            });

            Schema::table('controle_financeiro', function (Blueprint $table) {
                $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            });
        }

        if (Schema::hasColumn('parcelas', 'user_id')) {
            try {
                Schema::table('parcelas', function (Blueprint $table) {
                    $table->dropForeign('parcelas_user_id_foreign');
                });
            } catch (\Exception $e) {
            }

            Schema::table('parcelas', function (Blueprint $table) {
                $table->renameColumn('user_id', 'cliente_id');
            });

            Schema::table('parcelas', function (Blueprint $table) {
                $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            });
        }
    }
};
