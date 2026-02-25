$filePath = "app\Http\Controllers\DashboardController.php"
$content = Get-Content $filePath -Raw
# The garbled string likely contains specific bytes. 
# Since I can't be sure of the exact encoding in the tool, 
# I'll replace the line by matching the part before and after.

$pattern = 'return \$show \? ''Rp '' \. number_format\(\$value, 0, '','', ''\.'' evaluations\) : ''Rp .*'';'
# Wait, let's keep it simpler. Match the unique function structure.

$line1 = '    protected function maskNominal($value, $show)'
$line2 = '    {'
$line3 = '        return $show ? ''Rp '' . number_format($value, 0, '','', ''.'') : ''Rp ********'';'
$line4 = '    }'

$newFunc = "$line1`r`n$line2`r`n$line3`r`n$line4"

# Let's just find the function and replace it.
$content = $content -replace '(?s)protected function maskNominal\(\$value, \$show\)\s*\{.*?\}(?=\s*private function ratioStatus)', $newFunc

# Actually, let's just do a direct string replacement for the garbled line if possible.
# But PowerShell might mangle the garbled chars too.
# Safer to replace the whole block.

Set-Content -Path $filePath -Value $content -Encoding utf8
