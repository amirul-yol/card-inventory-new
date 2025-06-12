<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Inventory</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Include your CSS file -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px; /* Adjust padding as needed */
        }
        #live-clock-container {
            font-size: 16px;
            font-weight: bold;
            color: white;
        }
    </style>
</head>
<body onload="showLiveTime()">
    <header>
        <h1>Card Inventory Management System</h1>
        <div id="live-clock-container"></div>
    </header>

<script>
function showLiveTime() {
    const clock = document.getElementById('live-clock-container');
    if (!clock) return;

    function updateClock() {
        const now = new Date();
        const day = String(now.getDate()).padStart(2, '0');
        const month = String(now.getMonth() + 1).padStart(2, '0'); // Months are 0-based
        const year = now.getFullYear();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');

        clock.innerHTML = `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
    }

    updateClock(); // Initial call to display immediately
    setInterval(updateClock, 1000); // Update every second
}
</script>
