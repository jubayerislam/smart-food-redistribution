param(
    [Parameter(Mandatory = $true)]
    [string]$MarkdownPath,

    [Parameter(Mandatory = $true)]
    [string]$OutputPath,

    [string[]]$ImagePaths = @()
)

$markdown = Get-Content $MarkdownPath -Raw
$lines = $markdown -split "`r?`n"

$word = New-Object -ComObject Word.Application
$word.Visible = $false
$document = $word.Documents.Add()
$selection = $word.Selection

foreach ($line in $lines) {
    if ($line -match '^# (.+)$') {
        $selection.Style = 'Heading 1'
        $selection.TypeText($matches[1])
    } elseif ($line -match '^## (.+)$') {
        $selection.Style = 'Heading 2'
        $selection.TypeText($matches[1])
    } elseif ($line -match '^### (.+)$') {
        $selection.Style = 'Heading 3'
        $selection.TypeText($matches[1])
    } elseif ($line -match '^\- (.+)$') {
        $selection.Style = 'Normal'
        $selection.TypeText([char]0x2022 + ' ' + $matches[1])
    } elseif ($line -match '^\|') {
        $selection.Style = 'Normal'
        $selection.TypeText($line)
    } else {
        $selection.Style = 'Normal'
        $selection.TypeText($line)
    }

    $selection.TypeParagraph()
}

if ($ImagePaths.Count -gt 0) {
    $selection.Style = 'Heading 2'
    $selection.TypeText('Appendix Screenshots')
    $selection.TypeParagraph()

    foreach ($imagePath in $ImagePaths) {
        if (Test-Path $imagePath) {
            $selection.Style = 'Heading 3'
            $selection.TypeText([System.IO.Path]::GetFileNameWithoutExtension($imagePath))
            $selection.TypeParagraph()
            $selection.InlineShapes.AddPicture($imagePath) | Out-Null
            $selection.TypeParagraph()
            $selection.TypeParagraph()
        }
    }
}

$document.SaveAs([ref]$OutputPath, [ref]16)
$document.Close()
$word.Quit()

[System.Runtime.Interopservices.Marshal]::ReleaseComObject($selection) | Out-Null
[System.Runtime.Interopservices.Marshal]::ReleaseComObject($document) | Out-Null
[System.Runtime.Interopservices.Marshal]::ReleaseComObject($word) | Out-Null
[GC]::Collect()
[GC]::WaitForPendingFinalizers()
