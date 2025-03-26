<div class="footer-carousel">
    <div class="carousel-track">
        <?php
        $logos = ['logo1.png', 'logo2.png', 'logo3.png', 'logo4.png', 'logo5.png' ,'logo1.png', 'logo2.png', 'logo3.png', 'logo4.png', 'logo5.png'];
        // Repetir 2x para fazer loop visual contínuo
        for ($i = 0; $i < 2; $i++) {
            foreach ($logos as $index => $logo) {
                echo '<img src="rodape/' . htmlspecialchars($logo) . '" alt="Logo ' . ($index + 1) . '">';
            }
        }
        ?>
    </div>
</div>

<style>
.footer-carousel {
    width: 100%;
    overflow: hidden;
    border-top: 2px solid #be3144;
    border-bottom: 2px solid #be3144;
    background-color: #fff;
    padding: 10px 0;
}

.carousel-track {
    display: flex;
    width: max-content;
    animation: scroll-carousel 15s linear infinite;
}

.carousel-track img {
    height: 60px;
    margin: 0 40px;
    flex-shrink: 0;
    object-fit: contain;
}

@keyframes scroll-carousel {
    from {
        transform: translateX(50);
    }
    to {
        transform: translateX(-50%);
    }
}
</style>
