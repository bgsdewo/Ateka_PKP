$ppt = New-Object -ComObject PowerPoint.Application
$pptPath = (Resolve-Path "Presentasi_Gabungan_ATEKA_Minggu_3_dan_4.pptx").Path
$pres = $ppt.Presentations.Open($pptPath, [Microsoft.Office.Core.MsoTriState]::msoTrue, [Microsoft.Office.Core.MsoTriState]::msoFalse, [Microsoft.Office.Core.MsoTriState]::msoFalse)

$outDir = (Resolve-Path ".").Path + "\slide_previews"
if (-not (Test-Path $outDir)) {
    New-Item -ItemType Directory -Path $outDir | Out-Null
}

$count = $pres.Slides.Count
Write-Output "Total slides to export: $count"

for ($i = 1; $i -le $count; $i++) {
    $outFile = "$outDir\slide_$($i.ToString('00')).png"
    $pres.Slides.Item($i).Export($outFile, "PNG", 1920, 1080)
    Write-Output "Exported slide $i -> $outFile"
}

$pres.Close()
$ppt.Quit()
[System.Runtime.Interopservices.Marshal]::ReleaseComObject($pres) | Out-Null
[System.Runtime.Interopservices.Marshal]::ReleaseComObject($ppt) | Out-Null
Write-Output "All slides exported successfully!"
