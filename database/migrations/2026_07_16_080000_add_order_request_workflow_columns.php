<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'cancel_requested_at')) {
                $table->timestamp('cancel_requested_at')->nullable()->after('shipped_at');
            }

            if (! Schema::hasColumn('orders', 'cancel_request_reason')) {
                $table->string('cancel_request_reason')->nullable()->after('cancel_requested_at');
            }

            if (! Schema::hasColumn('orders', 'cancel_request_note')) {
                $table->text('cancel_request_note')->nullable()->after('cancel_request_reason');
            }

            if (! Schema::hasColumn('orders', 'cancel_approved_at')) {
                $table->timestamp('cancel_approved_at')->nullable()->after('cancel_request_note');
            }

            if (! Schema::hasColumn('orders', 'cancel_rejected_at')) {
                $table->timestamp('cancel_rejected_at')->nullable()->after('cancel_approved_at');
            }

            if (! Schema::hasColumn('orders', 'cancel_decision_note')) {
                $table->text('cancel_decision_note')->nullable()->after('cancel_rejected_at');
            }

            if (! Schema::hasColumn('orders', 'return_requested_at')) {
                $table->timestamp('return_requested_at')->nullable()->after('cancel_decision_note');
            }

            if (! Schema::hasColumn('orders', 'return_request_type')) {
                $table->string('return_request_type')->nullable()->after('return_requested_at');
            }

            if (! Schema::hasColumn('orders', 'return_reason')) {
                $table->string('return_reason')->nullable()->after('return_request_type');
            }

            if (! Schema::hasColumn('orders', 'return_request_note')) {
                $table->text('return_request_note')->nullable()->after('return_reason');
            }

            if (! Schema::hasColumn('orders', 'return_approved_at')) {
                $table->timestamp('return_approved_at')->nullable()->after('return_request_note');
            }

            if (! Schema::hasColumn('orders', 'return_rejected_at')) {
                $table->timestamp('return_rejected_at')->nullable()->after('return_approved_at');
            }

            if (! Schema::hasColumn('orders', 'return_decision_note')) {
                $table->text('return_decision_note')->nullable()->after('return_rejected_at');
            }

            if (! Schema::hasColumn('orders', 'returnable_until')) {
                $table->date('returnable_until')->nullable()->after('return_decision_note');
            }

            if (! Schema::hasColumn('orders', 'pickup_date')) {
                $table->date('pickup_date')->nullable()->after('returnable_until');
            }

            if (! Schema::hasColumn('orders', 'returned_at')) {
                $table->timestamp('returned_at')->nullable()->after('pickup_date');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $columns = [
                'cancel_requested_at',
                'cancel_request_reason',
                'cancel_request_note',
                'cancel_approved_at',
                'cancel_rejected_at',
                'cancel_decision_note',
                'return_requested_at',
                'return_request_type',
                'return_reason',
                'return_request_note',
                'return_approved_at',
                'return_rejected_at',
                'return_decision_note',
                'returnable_until',
                'pickup_date',
                'returned_at',
            ];

            $existingColumns = array_values(array_filter($columns, fn ($column) => Schema::hasColumn('orders', $column)));

            if ($existingColumns !== []) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};
