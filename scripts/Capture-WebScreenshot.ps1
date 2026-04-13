param(
    [Parameter(Mandatory = $true)]
    [string]$Url,

    [Parameter(Mandatory = $true)]
    [string]$OutputPath,

    [int]$Width = 1440,

    [int]$Height = 1800,

    [int]$DelaySeconds = 2
)

Add-Type -AssemblyName System.Windows.Forms
Add-Type -AssemblyName System.Drawing

$browser = New-Object System.Windows.Forms.WebBrowser
$browser.ScrollBarsEnabled = $false
$browser.ScriptErrorsSuppressed = $true
$browser.Width = $Width
$browser.Height = $Height

$script:isLoaded = $false

$browser.add_DocumentCompleted({
    if ($browser.ReadyState -eq [System.Windows.Forms.WebBrowserReadyState]::Complete) {
        Start-Sleep -Seconds $DelaySeconds
        $script:isLoaded = $true
    }
})

$browser.Navigate($Url)

while (-not $script:isLoaded) {
    [System.Windows.Forms.Application]::DoEvents()
    Start-Sleep -Milliseconds 100
}

$bitmap = New-Object System.Drawing.Bitmap $Width, $Height
$rectangle = New-Object System.Drawing.Rectangle 0, 0, $Width, $Height
$browser.DrawToBitmap($bitmap, $rectangle)
$bitmap.Save($OutputPath, [System.Drawing.Imaging.ImageFormat]::Png)

$bitmap.Dispose()
$browser.Dispose()
