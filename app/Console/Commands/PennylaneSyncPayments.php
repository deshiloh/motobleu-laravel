<?php

namespace App\Console\Commands;

use App\Models\Facture;
use App\Services\PennylaneService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PennylaneSyncPayments extends Command
{
    protected $signature = 'pennylane:sync-payments';
    protected $description = 'Synchronise le statut de paiement des factures depuis Pennylane';

    public function handle(PennylaneService $pennylane): void
    {
        // Use start_date checkpoint (processed_at of last seen item) — not cursor-based.
        // Pennylane recommends storing processed_at and using start_date on the next poll.
        $startDate = Cache::get('pennylane_changes_start_date');
        $cursor = null;
        $updatedCount = 0;
        $lastProcessedAt = null;
        $result = [];

        do {
            // First page: pass start_date. Subsequent pages: pass cursor only.
            $result = $pennylane->getInvoiceChanges($cursor, $cursor ? null : $startDate);
            $items = $result['items'] ?? [];

            foreach ($items as $change) {
                if (!empty($change['processed_at'])) {
                    $lastProcessedAt = $change['processed_at'];
                }

                if ($change['operation'] !== 'update') {
                    continue;
                }

                $pennylaneId = (string) $change['id'];

                $facture = Facture::where('pennylane_invoice_id', $pennylaneId)
                    ->where('is_acquitte', false)
                    ->first();

                if (!$facture) {
                    continue;
                }

                try {
                    if ($pennylane->isInvoicePaid($pennylaneId)) {
                        $facture->updateQuietly(['is_acquitte' => true]);
                        $updatedCount++;
                        $this->line("Facture #{$facture->id} ({$facture->invoice_number}) marquée acquittée.");
                    }
                } catch (\Throwable $e) {
                    Log::error('Pennylane payment check failed', [
                        'pennylane_invoice_id' => $pennylaneId,
                        'message' => $e->getMessage(),
                    ]);
                }
            }

            $cursor = $result['next_cursor'] ?? null;

        } while (!empty($result['has_more']) && $cursor);

        // Update checkpoint only once all pages exhausted (has_more: false).
        // This ensures we resume from the right position if we crash mid-pagination.
        if ($lastProcessedAt && empty($result['has_more'])) {
            Cache::forever('pennylane_changes_start_date', $lastProcessedAt);
        }

        $this->info("Sync terminée — {$updatedCount} facture(s) acquittée(s).");
    }
}