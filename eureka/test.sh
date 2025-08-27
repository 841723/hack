#!/bin/bash

code=0
existing_code="$(touch pwn)"

echo -n "code"
echo $code

echo -n "existing_code"
echo $existing_code

if [[ "$existing_code" -eq "$code" ]]; then
	echo "true"
else
	echo "false"
fi
