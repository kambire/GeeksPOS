<?php
$key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI1N2I1ZTVjMC1kMGExLTQ5YTktOWY4Yy01ZjViNjA1ZTRkNDkiLCJpc3MiOiJuOG4iLCJhdWQiOiJwdWJsaWMtYXBpIiwianRpIjoiYTA4N2I2YTgtODVlYy00M2YxLWJmOGItODc5MGZkMDEyZDI4IiwiaWF0IjoxNzkyNzI1OTY5fQ.CIcgpaIFf2yaYCMz0i5y3OxdsFkG9dytcO-6peAbDU0';
$ch = curl_init('http://geekspos_n8n:5678/api/v1/credentials');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-N8N-API-KEY: ' . $key]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
echo "Credentials: $res\n";
