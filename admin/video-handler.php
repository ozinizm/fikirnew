<?php
require_once __DIR__ . '/../includes/db.php';
/**
 * video-handler.php - BunnyCDN Stream Upload & Orientation Detection
 * ─────────────────────────────────────────────────────────────────
 * Handles the direct upload to BunnyCDN Stream and determines
 * if the video should be md:row-span-1 (Landscape) or md:row-span-2 (Portrait).
 * ─────────────────────────────────────────────────────────────────
 */

function uploadToBunnyStream($filePath, $title)
{
    if (!defined('BUNNY_STREAM_LIBRARY_ID') || !defined('BUNNY_STREAM_API_KEY')) {
        return false;
    }

    $libraryId = BUNNY_STREAM_LIBRARY_ID;
    $apiKey = BUNNY_STREAM_API_KEY;
    $hostname = BUNNY_CDN_HOSTNAME;

    // STEP 1: Create Video Entry on BunnyCDN
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://video.bunnycdn.com/library/{$libraryId}/videos");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['title' => $title]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "AccessKey: {$apiKey}",
        "Content-Type: application/json",
        "accept: application/json"
    ]);

    $response = curl_exec($ch);
    $data = json_decode($response, true);
    curl_close($ch);

    if (!isset($data['guid'])) {
        return false;
    }

    $videoId = $data['guid'];

    // STEP 2: Upload Video File Content (PUT method)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://video.bunnycdn.com/library/{$libraryId}/videos/{$videoId}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

    $fileSize = filesize($filePath);
    $fileData = fopen($filePath, 'r');

    curl_setopt($ch, CURLOPT_INFILE, $fileData);
    curl_setopt($ch, CURLOPT_INFILESIZE, $fileSize);
    curl_setopt($ch, CURLOPT_UPLOAD, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "AccessKey: {$apiKey}",
        "accept: application/json"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);
    fclose($fileData);

    // STEP 3: Fallback orientation detection (Server-side)
    // Primary detection is done via JS in portfolio.php, but this is a safety net.
    $orientation = detectVideoOrientation($filePath);

    return [
        'video_id' => $videoId,
        'medya_url' => "https://{$hostname}/{$videoId}/playlist.m3u8",
        'gorsel_url' => "https://{$hostname}/{$videoId}/thumbnail.jpg",
        'orientation' => $orientation
    ];
}

/**
 * detectVideoOrientation
 * Reads MP4 header to find the 'tkhd' atom and extract dimensions.
 */
function detectVideoOrientation($file)
{
    if (!file_exists($file))
        return 'md:row-span-1';

    $handle = fopen($file, 'rb');
    if (!$handle)
        return 'md:row-span-1';

    // Read first 64KB - usually enough for header
    $data = fread($handle, 65536);
    fclose($handle);

    $pos = strpos($data, 'tkhd');
    if ($pos === false)
        return 'md:row-span-2'; // Default to portrait for agency Reels/Shorts

    // Dimensions are typically at offset 80 and 84 within the tkhd atom
    // Standard MP4 structure (simplified)
    try {
        $width = unpack('N', substr($data, $pos + 80, 4))[1] / 65536;
        $height = unpack('N', substr($data, $pos + 84, 4))[1] / 65536;

        if ($width > 0 && $height > 0) {
            return ($height > $width) ? 'md:row-span-2' : 'md:row-span-1';
        }
    }
    catch (Exception $e) {
    // Fallback
    }

    return 'md:row-span-2'; // Default safety for creative agencies
}
?>
