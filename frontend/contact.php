<?php require_once '../includes/header.php'; ?>

<style>
.contact-section {
    max-width: 1100px;
    margin: 40px auto 0 auto;
    padding: 0 16px;
    display: flex;
    flex-wrap: wrap;
    gap: 40px;
    align-items: flex-start;
}
.contact-info {
    flex: 1 1 320px;
    min-width: 260px;
}
.contact-title {
    font-size: 1.35rem;
    font-weight: 700;
    color: #222;
    margin-bottom: 18px;
    letter-spacing: 0.5px;
}
.contact-info p {
    margin: 0 0 10px 0;
    font-size: 1.07rem;
    color: #444;
}
.contact-info strong {
    font-weight: 600;
    color: #3D52A0;
}
.contact-info .contact-label {
    font-weight: 600;
    color: #222;
}
.contact-map {
    flex: 1 1 420px;
    min-width: 320px;
    max-width: 520px;
}
.contact-map iframe, .contact-map #map {
    width: 100%;
    height: 320px;
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(61,82,160,0.08);
}
@media (max-width: 900px) {
    .contact-section { flex-direction: column; gap: 24px; }
    .contact-map { max-width: 100%; }
}
</style>

<div class="contact-section">
    <div class="contact-info">
        <div class="contact-title">OUR ADDRESS</div>
        <p><span class="contact-label">Email:</span> info@allgoodsstation.com</p>
        <p><span class="contact-label">Phone:</span> +1 737 303 4981</p>
        <p style="margin-top:22px; font-size:1.13rem; font-weight:600; color:#3D52A0;">Texas</p>
        <p>
            5900 Balcones DR STE 7782<br>
            Texas Austin 78731 United States
        </p>
    </div>
    <div class="contact-map">
        <!-- OpenStreetMap with Leaflet.js -->
        <div id="map"></div>
    </div>
</div>

<!-- Leaflet.js for OpenStreetMap -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var map = L.map('map').setView([30.3531, -97.7516], 16); // Coordinates for 5900 Balcones DR, Austin, TX
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    L.marker([30.3531, -97.7516]).addTo(map)
        .bindPopup('All Goods Station<br>5900 Balcones DR STE 7782<br>Austin, TX')
        .openPopup();
});
</script>

<?php require_once '../includes/footer.php'; ?>