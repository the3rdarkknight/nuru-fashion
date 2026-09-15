<footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div class="footer-logo">Nuru<span>Fashion</span></div>
                    <p>Everyday and statement women's fashion, picked for Nairobi. New drops weekly, deals daily, and delivery arranged straight over WhatsApp.</p>
                </div>
                <div class="footer-col">
                    <h4>Shop</h4>
                    <a href="index.php">All products</a>
                    <a href="index.php#deals">Today's deals</a>
                    <a href="category.php?slug=dresses">Dresses</a>
                    <a href="category.php?slug=sets">Sets</a>
                </div>
                <div class="footer-col">
                    <h4>Help</h4>
                    <a href="<?= e(whatsAppGeneralLink()) ?>" target="_blank" rel="noopener">Order on WhatsApp</a>
                    <a href="<?= e(whatsAppGeneralLink()) ?>" target="_blank" rel="noopener">Delivery &amp; returns</a>
                    <a href="<?= e(whatsAppGeneralLink()) ?>" target="_blank" rel="noopener">Size guide</a>
                </div>
                <div class="footer-col">
                    <h4>Nuru Fashion</h4>
                    <a href="admin/login.php">Admin</a>
                </div>
            </div>
            <div class="footer-bottom">
                <span>&copy; <?= date('Y') ?> Nuru Fashion. All rights reserved.</span>
                <span>Nairobi, Kenya</span>
            </div>
        </div>
    </footer>

    <a href="<?= e(whatsAppGeneralLink()) ?>" target="_blank" rel="noopener" class="float-whatsapp" aria-label="Chat on WhatsApp">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="#fff"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.2h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2Zm4.51 11.98c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.13-.17.24-.64.8-.78.97-.14.16-.29.18-.54.06-.25-.12-1.04-.38-1.98-1.22-.73-.65-1.22-1.45-1.37-1.7-.14-.24-.02-.37.11-.5.11-.11.25-.29.37-.43.12-.15.16-.25.24-.41.08-.16.04-.31-.02-.43-.06-.12-.56-1.35-.77-1.85-.2-.48-.41-.42-.56-.42-.14-.01-.31-.01-.48-.01-.16 0-.43.06-.66.31-.23.24-.86.85-.86 2.06s.89 2.39 1.01 2.56c.12.16 1.75 2.67 4.24 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.08.14-1.18-.06-.11-.22-.17-.47-.29Z"/></svg>
    </a>

    <script src="js/main.js"></script>
</body>
</html>