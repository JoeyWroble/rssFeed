<?php /* Template Name: joeytest */ ?>

<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the XML data sent via POST request
    $rssXml = file_get_contents('php://input');
    
    // Save XML to a file
    $filename = 'rss_feed.xml'; // Choose your desired filename
    $result = file_put_contents($filename, $rssXml, FILE_APPEND);
}

?>

<form id="podcastForm" method="post" enctype="multipart/form-data">
    <div id="form" class="center">
        <label for="podcastTitle">Title:</label><br>
        <input type="text" id="podcastTitle" name="podcastTitle" ><br><br>

        <label for="author">Author:</label><br>
        <input type="text" id="author" name="author" ><br><br>

        <label for="summary">Description:</label><br>
        <input type="text" id="podcastDescription" name="podcastDescription" ><br><br>

        <label for="series">Series:</label><br>
        <input type="text" id="series" name="series" ><br><br>

        <label for="podcastFile">Audio File:</label><br>
        <input type="file" class="center" id="podcastFile" name="podcastFile" accept=".mp3" ><br><br>

        <label for="coverImage">Cover Image File:</label><br>
        <input type="file" class="center" id="coverImage" name="coverImage" accept="image/png, image/jpeg" ><br><br>
        
        <input type="submit" value="Submit">
    </div>
</form>

<script>

document.getElementById("podcastForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent the default form submission
    
    // Get form data
    const podcastTitle = document.getElementById('podcastTitle').value;
    const author = document.getElementById('author').value;
    const podcastDescription = document.getElementById('podcastDescription').value;
    const podcastFile = document.getElementById('podcastFile').files[0]; // For file input, use .files to access the selected file(s)
    const coverImage = document.getElementById('coverImage').value;
    const series = document.getElementById('series').value;

    // Create an audio element to get the duration
    var audio = document.createElement('audio');
    var duration;

    // Add event listener to audio element to get duration
    audio.addEventListener('loadedmetadata', function() {
        duration = audio.duration; // Get duration
        const rssXml = generateRssXml(podcastTitle, author, podcastDescription, podcastFile.name, coverImage, duration);
        console.log(rssXml);

        // Send XML data to server using AJAX
        const xhr = new XMLHttpRequest();
        xhr.open('POST', window.location.href); // Point to the current URL
        xhr.onload = function() {
            if (xhr.status === 200) {
                console.log('RSS XML saved successfully');
            } else {
                console.error('Failed to save RSS XML');
            }
        };
        xhr.send(rssXml);
    });

    // Set the audio source to the podcast file
    audio.src = URL.createObjectURL(podcastFile);
});

function generateRssXml(podcastTitle, author, podcastDescription, podcastFile, coverImage, duration, cove) {
    const guid = Math.random().toString(16).slice(2);
    const rssTemplate = `<xml version="1.0" encoding="UTF-8">
<rss version="2.0" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd">
    <channel>
        <title>${podcastTitle}</title>
        <author>${author}</author>
        <description>${podcastDescription}</description>
        <audio>${podcastFile}</audio>
        <coverImage>${coverImage}</coverImage>
        <series>${series}</series>
        <duration>${duration}</series>
        <pubDate>${new Date().toUTCString()}</pubDate>
        <guid>${guid}</guid>
    </channel>
</rss>`;

document.forms['podcastForm'].reset()
    return rssTemplate;
}
</script>
