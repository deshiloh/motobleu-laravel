#!/bin/bash

# Script to revert pagination changes back to <x-front.pagination>

# Array of files to update
files=(
    "resources/views/livewire/passager/passagers-data-table.blade.php"
    "resources/views/livewire/pilote/pilote-stat-year.blade.php"
    "resources/views/livewire/pilote/pilote-data-table.blade.php"
    "resources/views/livewire/pilote/pilote-stat-month.blade.php"
    "resources/views/livewire/front/invoice/invoice-data-table.blade.php"
    "resources/views/livewire/front/invoice/invoice-reservation-data-table.blade.php"
    "resources/views/livewire/front/passager/passager-data-table.blade.php"
    "resources/views/livewire/front/account/account-data-table.blade.php"
    "resources/views/livewire/front/address/address-data-table.blade.php"
    "resources/views/livewire/front/reservation/reservation-data-table.blade.php"
    "resources/views/livewire/front/cost-center/cost-center-data-table.blade.php"
    "resources/views/livewire/type-facturation/type-facturation-data-table.blade.php"
    "resources/views/livewire/reservation/reservation-data-table.blade.php"
    "resources/views/livewire/reservation/adresses-reservation-data-table.blade.php"
    "resources/views/livewire/localisation/localisation-data-table.blade.php"
    "resources/views/livewire/cost-center/cost-center-data-table.blade.php"
    "resources/views/livewire/facturation/facturation-data-table.blade.php"
    "resources/views/livewire/facturation/export.blade.php"
    "resources/views/livewire/entreprise/entreprises-data-table.blade.php"
    "resources/views/livewire/entreprise/recap-reservation-entreprise.blade.php"
    "resources/views/livewire/entreprise/users-entreprise-data-table.blade.php"
)

echo "Reverting pagination changes..."

for file in "${files[@]}"; do
    if [[ -f "$file" ]]; then
        echo "Processing: $file"
        # Try to detect the variable name and replace accordingly
        if grep -q '{{ \$passagers->links()' "$file"; then
            sed -i '' 's/{{ \$passagers->links() }}/<x-front.pagination :pagination="\$passagers" :perPage="\$perPage" \/>/g' "$file"
        elif grep -q '{{ \$pilotes->links()' "$file"; then
            sed -i '' 's/{{ \$pilotes->links() }}/<x-front.pagination :pagination="\$pilotes" :perPage="\$perPage" \/>/g' "$file"
        elif grep -q '{{ \$factures->links()' "$file"; then
            sed -i '' 's/{{ \$factures->links() }}/<x-front.pagination :pagination="\$factures" :perPage="\$perPage" \/>/g' "$file"
        elif grep -q '{{ \$accounts->links()' "$file"; then
            sed -i '' 's/{{ \$accounts->links() }}/<x-front.pagination :pagination="\$accounts" :perPage="\$perPage" \/>/g' "$file"
        elif grep -q '{{ \$addresses->links()' "$file"; then
            sed -i '' 's/{{ \$addresses->links() }}/<x-front.pagination :pagination="\$addresses" :perPage="\$perPage" \/>/g' "$file"
        elif grep -q '{{ \$reservations->links()' "$file"; then
            sed -i '' 's/{{ \$reservations->links() }}/<x-front.pagination :pagination="\$reservations" :perPage="\$perPage" \/>/g' "$file"
        elif grep -q '{{ \$costCenters->links()' "$file"; then
            sed -i '' 's/{{ \$costCenters->links() }}/<x-front.pagination :pagination="\$costCenters" :perPage="\$perPage" \/>/g' "$file"
        elif grep -q '{{ \$typeFacturations->links()' "$file"; then
            sed -i '' 's/{{ \$typeFacturations->links() }}/<x-front.pagination :pagination="\$typeFacturations" :perPage="\$perPage" \/>/g' "$file"
        elif grep -q '{{ \$adresses->links()' "$file"; then
            sed -i '' 's/{{ \$adresses->links() }}/<x-front.pagination :pagination="\$adresses" :perPage="\$perPage" \/>/g' "$file"
        elif grep -q '{{ \$localisations->links()' "$file"; then
            sed -i '' 's/{{ \$localisations->links() }}/<x-front.pagination :pagination="\$localisations" :perPage="\$perPage" \/>/g' "$file"
        elif grep -q '{{ \$entreprises->links()' "$file"; then
            sed -i '' 's/{{ \$entreprises->links() }}/<x-front.pagination :pagination="\$entreprises" :perPage="\$perPage" \/>/g' "$file"
        elif grep -q '{{ \$users->links()' "$file"; then
            sed -i '' 's/{{ \$users->links() }}/<x-front.pagination :pagination="\$users" :perPage="\$perPage" \/>/g' "$file"
        else
            echo "No matching pagination pattern found in $file"
        fi
    else
        echo "File not found: $file"
    fi
done

echo "Pagination revert completed!"