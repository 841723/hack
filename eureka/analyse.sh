#!/bin/bash

LOG_FILE=log
STATUS_CODES=("200:0") # Indexed array: "code:count" pairs


while IFS= read -r line; do
        code=$(echo "$line" | grep -oP 'Status: \K.*')
        found=0

	echo "code: $code"

        # Check if code exists in STATUS_CODES array
        for i in "${!STATUS_CODES[@]}"; do
            existing_entry="${STATUS_CODES[$i]}"
            existing_code=$(echo "$existing_entry" | cut -d':' -f1)
            existing_count=$(echo "$existing_entry" | cut -d':' -f2)
            if [[ "$existing_code" -eq "$code" ]]; then
                new_count=$((existing_count + 1))
                STATUS_CODES[$i]="${existing_code}:${new_count}"
                break
            fi
	done
done < <(grep "HTTP.*Status: " "$LOG_FILE")
