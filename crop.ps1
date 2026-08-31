Add-Type -AssemblyName System.Drawing
function CropImage($src, $dest) {
    if (Test-Path $src) {
        $bmp = [System.Drawing.Image]::FromFile($src)
        $rect = New-Object System.Drawing.Rectangle 350, 0, 162, 100
        $cropped = $bmp.Clone($rect, $bmp.PixelFormat)
        $cropped.Save($dest, [System.Drawing.Imaging.ImageFormat]::Png)
        $bmp.Dispose()
        $cropped.Dispose()
        Write-Output "Saved to $dest"
    } else {
        Write-Output "Not found: $src"
    }
}
CropImage "C:/Users/Alejandro Lo Presti/.gemini/antigravity/brain/aac3a4fa-eaeb-47cf-82bc-12822638e5f1/.user_uploaded/media_1788188244094.png" "C:/Users/Alejandro Lo Presti/.gemini/antigravity/brain/aac3a4fa-eaeb-47cf-82bc-12822638e5f1/crop1.png"
CropImage "C:/Users/Alejandro Lo Presti/.gemini/antigravity/brain/aac3a4fa-eaeb-47cf-82bc-12822638e5f1/.user_uploaded/media_1788191211834.png" "C:/Users/Alejandro Lo Presti/.gemini/antigravity/brain/aac3a4fa-eaeb-47cf-82bc-12822638e5f1/crop2.png"
CropImage "C:/Users/Alejandro Lo Presti/.gemini/antigravity/brain/aac3a4fa-eaeb-47cf-82bc-12822638e5f1/.user_uploaded/media_1788191780991.png" "C:/Users/Alejandro Lo Presti/.gemini/antigravity/brain/aac3a4fa-eaeb-47cf-82bc-12822638e5f1/crop3.png"
