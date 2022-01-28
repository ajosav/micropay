<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuidMorphs('from');
            $table->uuidMorphs('to');
            $table
                ->enum(
                    'status',
                    ['exchange', 'transfer', 'paid', 'refund', 'gift']
                )
                ->default('transfer')
            ;

            $table
                ->enum(
                    'status_last',
                    ['exchange', 'transfer', 'paid', 'refund', 'gift']
                )
                ->nullable()
            ;

            $table->string('deposit_id');
            $table->string('withdraw_id');

            $table->uuid('reference')->unique();
            $table->timestamps();

            $table->foreign('deposit_id')
                ->references('reference')
                ->on('transactions')
                ->onDelete('cascade')
            ;

            $table->foreign('withdraw_id')
                ->references('reference')
                ->on('transactions')
                ->onDelete('cascade')
            ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfers');
    }
}
