<?php
include '../includes/db.php';

$stmt = $pdo->query("SELECT p.*, c.COMPANY_NAME, c.IMG_URL 
    FROM POST p
    INNER JOIN COMPANY c ON p.COMPANY_ID = c.COMPANY_ID 
    WHERE p.POST_STATUS = 'A'
    ORDER BY p.CREATED_AT DESC 
    LIMIT 10
");
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="news-carousel-container">
    <div class="news-carousel" id="news-carousel">
        <?php foreach ($news as $index => $item): ?>
            <div class="news-slide<?= $index === 0 ? ' active' : '' ?>"
                data-title="<?= htmlspecialchars($item['COMPANY_NAME']) ?>"
                data-img="<?= htmlspecialchars($item['IMG_URL']) ?>"
                data-content="<?= htmlspecialchars($item['POST_CONTENT']) ?>"
                data-date="<?= date('d/m/Y', strtotime($item['CREATED_AT'])) ?>"
                style="background-image: url('<?= htmlspecialchars($item['IMG_URL']) ?>');">
                <div class="news-overlay">
                    <div class="news-content">
                        <h3><?= htmlspecialchars($item['COMPANY_NAME']) ?></h3>
                        <!-- TODO esta a dar erro banner noticias -->
                        <p><?= nl2br(htmlspecialchars(mb_strimwidth($item['POST_CONTENT'], 0, 140, '...'))) ?></p>
                        <span class="news-date">🕒 <?= date('d/m/Y', strtotime($item['CREATED_AT'])) ?></span><br>
                        <button class="read-more-btn">Ler mais</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <button class="news-nav prev">❮</button>
        <button class="news-nav next">❯</button>
    </div>
</div>

<div id="post-modal" class="news-modal" style="display: none;">
    <div class="news-modal-content">
        <span class="news-modal-close">&times;</span>
        <img id="modal-img" src="" alt="News Image" class="modal-img">
        <h3 id="modal-title"></h3>
        <p id="modal-text"></p>
        <span class="news-date" id="modal-date"></span>
    </div>
</div>

<style>
    .news-carousel-container {
        width: 80%;
        margin: 40px auto;
        border-radius: 15px;
        border: 2px solid #be3144;
        background-color: #fff;
        position: relative;
        overflow: hidden;
    }

    .news-carousel {
        height: 200px;
        position: relative;
    }

    .news-slide {
        position: absolute;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        opacity: 0;
        transition: opacity 0.8s ease-in-out;
    }

    .news-slide.active {
        opacity: 1;
        z-index: 1;
    }

    .news-overlay {
        background: rgba(255, 255, 255, 0.95);
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        justify-content: center;
        text-align: center;
        padding: 20px;
    }

    .news-content h3 {
        color: #be3144;
        margin-bottom: 10px;
    }

    .news-content p {
        max-width: 600px;
        color: #333;
        margin: 0 auto;
    }

    .news-date {
        margin-top: 10px;
        font-size: 0.85rem;
        color: #666;
    }

    .read-more-btn {
        background-color: #be3144;
        color: white;
        border: none;
        padding: 6px 16px;
        border-radius: 6px;
        margin-top: 10px;
        cursor: pointer;
    }

    .read-more-btn:hover {
        background-color: #a42939;
    }

    .news-nav {
        position: absolute;
        top: 10px;
        background: #be3144;
        border: none;
        color: white;
        font-size: 18px;
        padding: 6px 10px;
        border-radius: 4px;
        cursor: pointer;
        z-index: 3;
    }

    .news-nav.prev {
        right: 45px;
    }

    .news-nav.next {
        right: 10px;
    }

    .news-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999;
    }

    .news-modal-content {
        background: #fff;
        padding: 40px 60px;
        border-radius: 16px;
        text-align: center;
        width: 95%;
        max-width: 900px;
        position: relative;
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .news-modal-close {
        position: absolute;
        top: 10px;
        right: 20px;
        font-size: 24px;
        color: #be3144;
        cursor: pointer;
    }

    .modal-img {
        width: 140px;
        height: 140px;
        object-fit: cover;
        border-radius: 50%;
        margin-bottom: 20px;
        border: 4px solid #be3144;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        let slides = document.querySelectorAll('.news-slide');
        let current = 0;
        const modal = document.getElementById("post-modal");
        const title = document.getElementById("modal-title");
        const text = document.getElementById("modal-text");
        const img = document.getElementById("modal-img");
        const date = document.getElementById("modal-date");

        const showSlide = i => {
            slides.forEach(s => s.classList.remove("active"));
            slides[i].classList.add("active");
        };

        document.querySelector(".news-nav.next")?.addEventListener("click", () => {
            current = (current + 1) % slides.length;
            showSlide(current);
        });

        document.querySelector(".news-nav.prev")?.addEventListener("click", () => {
            current = (current - 1 + slides.length) % slides.length;
            showSlide(current);
        });

        document.querySelectorAll(".read-more-btn").forEach(btn => {
            btn.addEventListener("click", () => {
                const slide = btn.closest(".news-slide");
                title.innerText = slide.dataset.title;
                text.innerText = slide.dataset.content;
                img.src = slide.dataset.img;
                date.innerText = '🕒 ' + slide.dataset.date;
                modal.style.display = "flex";
            });
        });

        document.querySelector(".news-modal-close").addEventListener("click", () => {
            modal.style.display = "none";
        });

        modal.addEventListener("click", e => {
            if (e.target === modal) {
                modal.style.display = "none";
            }
        });

        setInterval(() => {
            current = (current + 1) % slides.length;
            showSlide(current);
        }, 7000);
    });
</script>