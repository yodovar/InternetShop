<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - AirShop</title>
    <link rel="stylesheet" href="/Airshop/internetShop/public/css/aboutStyle.css">
    <script src="/Airshop/public/js/script.js" defer></script>
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <a href="/" class="logo">AirShop</a>
            <ul class="nav-links">
                <li><a href="/">Home</a></li>
                <li><a href="/about.php" class="active">About Us</a></li>
                <li><a href="/contact.php">Contact</a></li>
            </ul>
        </nav>
        <div class="hero">
            <h1>About Us</h1>
            <p>Discover the story behind AirShop and our mission to deliver excellence.</p>
        </div>
    </header>
    <main>
        <section class="about-section">
            <h2>Who We Are</h2>
            <p>AirShop is a leading online marketplace offering the best products with fast delivery and excellent customer support.</p>
        </section>
        <section class="mission-section">
            <h2>Our Mission</h2>
            <p>Our goal is to connect customers with the best stores, ensuring quality and affordability in every purchase.</p>
        </section>
        <section class="team-section">
            <h2>Meet Our Team</h2>
            <div class="team-gallery">
                <div class="team-member">
                    <img src="/Airshop/public/images/team1.jpg" alt="Team Member">
                    <h3>John Doe</h3>
                    <p>CEO & Founder</p>
                </div>
                <div class="team-member">
                    <img src="/Airshop/public/images/team2.jpg" alt="Team Member">
                    <h3>Jane Smith</h3>
                    <p>Marketing Manager</p>
                </div>
                <div class="team-member">
                    <img src="/Airshop/public/images/team3.jpg" alt="Team Member">
                    <h3>Sam Wilson</h3>
                    <p>Lead Developer</p>
                </div>
            </div>
        </section>
        <a href="/" class="btn">Go to Home</a>
    </main>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> AirShop. All rights reserved.</p>
    </footer>

    <script>
        // Добавляем интерактивные элементы
document.addEventListener("DOMContentLoaded", () => {
    const btn = document.querySelector(".btn");

    btn.addEventListener("mouseover", () => {
        btn.style.transform = "scale(1.1)";
    });

    btn.addEventListener("mouseout", () => {
        btn.style.transform = "scale(1)";
    });
});
    </script>
</body>
</html>