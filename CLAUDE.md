# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Stack

- **Laravel 10** (PHP 8.1+) with **Livewire 2** for reactive UI
- **WireUI** for UI components (notifications, dialogs, modals)
- **Spatie Laravel Settings** (`spatie/laravel-settings`) for persisted app settings
- **laraveldaily/laravel-invoices** for PDF invoice generation
- **Maatwebsite/Excel** for XLS exports
- **Laravel Sail** (Docker) as the local dev environment

## Common Commands

```bash
# Run all tests (SQLite in-memory)
./vendor/bin/sail php vendor/bin/phpunit

# Run a single test file
./vendor/bin/sail php vendor/bin/phpunit tests/Feature/FacturationTest.php

# Run a single test method
./vendor/bin/sail php vendor/bin/phpunit --filter testSendFacture

# PHPStan static analysis
make phpstan

# Regenerate IDE helpers after model changes
make helpers
```

## Facturation Module Architecture

The billing system converts confirmed reservations into invoices (`Facture`) and sends them by email.

### Data model

| Model | Table | Key fields |
|---|---|---|
| `Facture` | `factures` | `statut` (BillStatut enum), `month`, `year`, `reference` (FA2024-MM-NNN), `montant_ttc`, `is_acquitte`, `adresse_client`, `adresse_facturation` |
| `Reservation` | `reservations` | `facture_id`, `statut` (ReservationStatus enum), `tarif`, `majoration`, `complement` |

A `Facture` has many `Reservation`s. The inverse (`Reservation` belongs to `Facture`) is how billing is scoped — there is no direct `Facture`↔`Entreprise` relation; the entreprise is always resolved through `facture->reservations->first()->entreprise`.

### Statuses

**`BillStatut`**: `CREATED(1)` → `COMPLETED(2)` or `CANCEL(3)`

**`ReservationStatus`**: reservations eligible for billing must be `Confirmed(4)` or `CanceledToPay(3)` with `encaisse_pilote` null/0. Sending a bill transitions them to `Billed(5)`.

### Billing flow (admin)

1. **`admin.facturations.edition`** — `EditionFacture` Livewire component  
   - Lists eligible entreprises for the selected month/year  
   - `goToEditPage($entrepriseId)` creates or reuses a `CREATED` `Facture` and links unlinked reservations to it  
   - Each reservation tarif/majoration/complement is edited inline; `reservationUpdated` recalculates `montant_ttc`  
   - `sendFactureAction()` validates all reservations have a tarif, marks the facture `COMPLETED`, transitions reservations to `Billed`, and dispatches `BillCreated` event  
   - `BillCreatedListener` sends `App\Mail\BillCreated` to comma-separated recipients

2. **`admin.facturations.index`** — `FacturationDataTable` — lists completed/cancelled factures, allows toggling `is_acquitte`

3. **`admin.facturations.show`** — `FacturationsController::show()` streams the PDF via `InvoiceService::generateInvoice()`

4. **`admin.facturations.export`** — `Export` Livewire component, date-range export to PDF or Excel via `ExportService`

### PDF generation

`InvoiceService::generateInvoice(Facture $facture)` builds a `laraveldaily/laravel-invoices` `Invoice` object. The custom template lives at `resources/views/vendor/invoices/templates/motobleu.blade.php`. TVA is always 10%; `montant_ht` is a computed attribute (`montant_ttc / 1.10`).

### Reference format

`Facture::generateReference(year, month)` produces:
- **2024+**: `FA{YYYY}-{MM}-{NNN}` where NNN is sequential within the year
- **pre-2024**: `FA{YYYY}-{MM}-{NN}` sequential within the month

### Per-entreprise billing settings (`BillSettings`)

Stored via `app\Settings\BillSettings` (Spatie settings, group `bill`):
- `entreprises_xls_file` — entreprise IDs that get an XLS recap instead of PDF
- `entreprises_cost_center_facturation` — entreprises that need a cost center field on the recap
- `entreprise_without_command_field` — entreprises that skip the command number field
- `rib` — bank details shown on invoices

### Front-end invoice view (client-facing)

`app/Http/Livewire/Front/Invoice/` — read-only invoice listing for logged-in clients, separate from the admin billing flow.

### Tests

Feature tests use `RefreshDatabase` with `$seed = true` (runs seeders). Livewire components are tested with `Livewire::test()`. The test database is SQLite in-memory (see `phpunit.xml`).

Key test file: `tests/Feature/FacturationTest.php`
