<?php require_once '../includes/header.php'; ?>

<style>
.about-section {
    max-width: 1100px;
    margin: 40px auto 0 auto;
    padding: 0 16px;
}
.about-row {
    display: flex;
    flex-wrap: wrap;
    gap: 32px;
    margin-bottom: 40px;
    align-items: center;
}
.about-col {
    flex: 1 1 340px;
    min-width: 280px;
}
.about-img {
    width: 100%;
    max-width: 320px;
    border-radius: 14px;
    box-shadow: 0 4px 18px rgba(61,82,160,0.10);
    display: block;
    margin: 0 auto;
    background: #f6f7fb;
    object-fit: cover;
    aspect-ratio: 1/1;
}
.about-title {
    font-size: 2.1rem;
    font-weight: 800;
    color: #3D52A0;
    margin-bottom: 14px;
    letter-spacing: 1px;
}
.about-subtitle {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2a2a2a;
    margin-bottom: 10px;
    letter-spacing: 0.5px;
}
.about-text {
    font-size: 1.08rem;
    color: #444;
    line-height: 1.7;
    margin-bottom: 0;
}
.about-thankyou {
    text-align: center;
    font-size: 1.15rem;
    margin: 36px 0 0 0;
    color: #222;
    font-weight: 500;
    letter-spacing: 0.2px;
}
@media (max-width: 900px) {
    .about-row { flex-direction: column; gap: 20px; }
    .about-img { max-width: 100%; }
    .about-section { padding: 0 4vw; }
}
</style>

<div class="about-section">

    <div class="about-row">
        <div class="about-col">
            <div class="about-title">ABOUT US</div>
            <div class="about-text">
                Welcome to AllGoodsStation.com, your one-stop destination for high-quality home and kitchen, office supplies, and arts and notions products! Operated by AG Station LLC, we take pride in offering an extensive range of products designed to enhance your everyday life.
            </div>
        </div>
        <div class="about-col" style="text-align:center;">
            <img src="https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?auto=format&fit=crop&w=400&q=80" alt="About Us" class="about-img">
        </div>
    </div>

    <div class="about-row">
        <div class="about-col" style="text-align:center;">
            <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80" alt="Our Mission" class="about-img">
        </div>
        <div class="about-col">
            <div class="about-subtitle">OUR MISSION</div>
            <div class="about-text">
                Our mission at AllGoodsStation.com is to simplify and enhance your shopping experience by offering a carefully curated selection of high-quality products that cater to your home, office, and creative needs. We aim to be a reliable and trusted platform where customers can find exceptional products that inspire and improve their daily lives.
            </div>
        </div>
    </div>

    <div class="about-row">
        <div class="about-col">
            <div class="about-subtitle">WHY CHOOSE US?</div>
            <div class="about-text">
                At AllGoodsStation.com, your satisfaction is our foremost priority. Choose us for your shopping needs because we offer a diverse range of products, spanning from innovative kitchen gadgets to essential office supplies and inspiring arts and notions, catering to everyone's preferences. Rest assured, we prioritize quality by sourcing only from trusted suppliers, ensuring your confidence in every purchase.<br><br>
                Additionally, our dedicated customer support team stands ready to assist you at every turn, providing exceptional service to address any inquiries or assistance required throughout your shopping journey.
            </div>
        </div>
        <div class="about-col" style="text-align:center;">
            <img src="https://images.unsplash.com/photo-1464983953574-0892a716854b?auto=format&fit=crop&w=400&q=80" alt="Why Choose Us" class="about-img">
        </div>
    </div>

    <div class="about-thankyou">
        Thank you for choosing AllGoodsStation.com. We look forward to serving you and being a part of your journey in discovering exceptional products that complement your lifestyle. Happy shopping!
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>