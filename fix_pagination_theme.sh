#!/bin/bash

# Script to replace <x-front.pagination> with direct {{ $variable->links() }} calls

# Array of files to update with their respective variable names
declare -A files_vars
files_vars[resources/views/livewire/passager/passagers-data-table.blade.php]="passagers"
files_vars[resources/views/livewire/pilote/pilote-stat-year.blade.php]="pilotes"
files_vars[resources/views/livewire/pilote/pilote-data-table.blade.php]="pilotes"
files_vars[resources/views/livewire/pilote/pilote-stat-month.blade.php]="pilotes"
files_vars[resources/views/livewire/front/invoice/invoice-data-table.blade.php]="factures"
files_vars[resources/views/livewire/front/invoice/invoice-reservation-data-table.blade.php]="reservations"
files_vars[resources/views/livewire/front/passager/passager-data-table.blade.php]="passagers"
files_vars[resources/views/livewire/front/account/account-data-table.blade.php]="accounts"
files_vars[resources/views/livewire/front/address/address-data-table.blade.php]="addresses"
files_vars[resources/views/livewire/front/reservation/reservation-data-table.blade.php]="reservations"
files_vars[resources/views/livewire/front/cost-center/cost-center-data-table.blade.php]="items"
files_vars[resources/views/livewire/type-facturation/type-facturation-data-table.blade.php]="typefacturations"
files_vars[resources/views/livewire/reservation/reservation-data-table.blade.php]="reservations"
files_vars[resources/views/livewire/reservation/adresses-reservation-data-table.blade.php]="adresses"
files_vars[resources/views/livewire/localisation/localisation-data-table.blade.php]="localisations"
files_vars[resources/views/livewire/cost-center/cost-center-data-table.blade.php]="costcenters"
files_vars[resources/views/livewire/facturation/facturation-data-table.blade.php]="facturations"
files_vars[resources/views/livewire/facturation/export.blade.php]="facturations"
files_vars[resources/views/livewire/entreprise/entreprises-data-table.blade.php]="entreprises"
files_vars[resources/views/livewire/entreprise/recap-reservation-entreprise.blade.php]="reservations"
files_vars[resources/views/livewire/entreprise/users-entreprise-data-table.blade.php]="users"

echo "Replacing <x-front.pagination> with direct links() calls..."

for file in "${!files_vars[@]}"; do
    if [[ -f "$file" ]]; then
        variable="${files_vars[$file]}"
        echo "Processing: $file (variable: \$$variable)"
        
        # Replace <x-front.pagination> with direct links() call
        sed -i '' "s|<x-front\.pagination :pagination=\"\\\$$variable\" :perPage=\"\\\$perPage\" />|{{ \\\$$variable->links() }}|g" "$file"
    else
        echo "File not found: $file"
    fi
done

echo "Pagination replacement completed!"