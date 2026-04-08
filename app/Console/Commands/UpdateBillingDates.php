<?php

namespace App\Console\Commands;

use App\Models\Account;
use Illuminate\Console\Command;

class UpdateBillingDates extends Command
{
    protected $signature = 'app:update-billing-dates';
    protected $description = 'Actualizar fechas de facturacion de cuentas';

    public function handle(): int
    {
        $accounts = Account::where('status', 'active')
            ->where('next_billing_date', '<=', now())
            ->get();

        $updated = 0;

        foreach ($accounts as $account) {
            $nextDate = $account->next_billing_date->copy()->addMonth();
            $account->update(['next_billing_date' => $nextDate]);
            $updated++;
            $this->line("  -> {$account->label}: nueva fecha {$nextDate->format('d/m/Y')}");
        }

        $this->info("Cuentas actualizadas: {$updated}");

        return Command::SUCCESS;
    }
}
