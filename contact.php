<?php
$page_title = 'Contact Us | Shopping Zone';
$nav_active = 'contact';
$load_contact_page_css = true;
$contact_css_path = __DIR__ . '/assets/css/contact.css';
$contact_css_version = is_file($contact_css_path) ? filemtime($contact_css_path) : time();
include('layouts/header.php');

// Pakistan: 0331 6551 524 → international digits for WhatsApp (wa.me)
$whatsapp_e164 = '923316551524';
$whatsapp_default_text = rawurlencode('Hi Shopping Zone, I would like to know more about your products.');
$whatsapp_url = 'https://wa.me/' . $whatsapp_e164 . '?text=' . $whatsapp_default_text;

$mailto_subject = rawurlencode('Question from Shopping Zone website');
$mailto_body = rawurlencode("Hello,\n\n");
$mailto_href = 'mailto:shoppingzone@gmail.com?subject=' . $mailto_subject . '&body=' . $mailto_body;
?>

<section id="sz-contact" class="contact-page">
    <div class="contact-banner">
        <div class="container">
            <p class="contact-banner-kicker text-uppercase">Shopping Zone</p>
            <h1 class="contact-banner-title">Contact <span>us</span></h1>
            <p class="contact-banner-lead">We are here for orders, sizing, delivery, and anything else you need.</p>
        </div>
    </div>

    <div class="container contact-cards-shell">
        <div class="text-center pt-2 pb-1">
            <h3 class="contact-section-title">Get in touch</h3>
            <hr class="fancy-hr contact-fancy-hr">
        </div>
        <div class="row g-4 contact-cards-row">
            <div class="col-md-4">
                <div class="contact-card h-100">
                    <div class="contact-card-icon"><i class="fa-solid fa-phone"></i></div>
                    <h2 class="contact-card-title">Phone</h2>
                    <p class="contact-card-text">Speak with us directly for orders and support.</p>
                    <a class="contact-action contact-action--brand" href="tel:+923316551524">0331 6551 524</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contact-card h-100">
                    <div class="contact-card-icon"><i class="fa-solid fa-envelope"></i></div>
                    <h2 class="contact-card-title">Email</h2>
                    <p class="contact-card-text">Send a detailed message — we reply as soon as we can.</p>
                    <a class="contact-action contact-action--brand" href="<?php echo htmlspecialchars($mailto_href); ?>">shoppingzone@gmail.com</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contact-card contact-card--whatsapp h-100">
                    <div class="contact-card-icon contact-card-icon--whatsapp"><i class="fa-brands fa-whatsapp"></i></div>
                    <h2 class="contact-card-title">WhatsApp</h2>
                    <p class="contact-card-text">Quick answers on your phone — tap to start chatting.</p>
                    <a class="contact-btn contact-btn--whatsapp contact-btn--in-card" href="<?php echo htmlspecialchars($whatsapp_url); ?>" target="_blank" rel="noopener noreferrer">
                        <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Chat on WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="contact-lower">
        <div class="container py-5">
            <div class="text-center mb-4">
                <h3 class="contact-section-title">WhatsApp message</h3>
                <hr class="fancy-hr contact-fancy-hr">
                <p class="contact-section-sub text-muted mx-auto">Type below, then open WhatsApp — your text is filled in for our number.</p>
            </div>
            <div class="row g-4 align-items-stretch justify-content-center">
                <div class="col-lg-7">
                    <div class="contact-panel h-100">
                        <form class="contact-whatsapp-form" id="contactWhatsappForm" action="#" method="get" onsubmit="return false;">
                            <label for="waMessage" class="form-label contact-form-label">Your message</label>
                            <textarea class="form-control contact-textarea" id="waMessage" rows="5" placeholder="Ask about orders, sizes, delivery, or anything else..."></textarea>
                            <button type="button" class="contact-btn contact-btn--whatsapp contact-btn--block mt-3" id="waSendBtn">
                                <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Open WhatsApp with this message
                            </button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="contact-hours h-100">
                        <h3 class="contact-panel-heading">Support hours</h3>
                        <hr class="contact-hours-accent">
                        <p class="contact-hours-highlight">We work <strong>24/7</strong> to answer your questions.</p>
                        <ul class="contact-hours-list list-unstyled mb-0">
                            <li><i class="fa-solid fa-clock"></i><span>WhatsApp &amp; email: anytime</span></li>
                            <li><i class="fa-solid fa-phone"></i><span>Phone: during business hours</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
(function () {
    var base = 'https://wa.me/<?php echo htmlspecialchars($whatsapp_e164, ENT_QUOTES, 'UTF-8'); ?>';
    var defaultMsg = <?php echo json_encode('Hi Shopping Zone, I would like to know more about your products.', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    var btn = document.getElementById('waSendBtn');
    var ta = document.getElementById('waMessage');
    if (!btn || !ta) return;
    btn.addEventListener('click', function () {
        var text = (ta.value || '').trim();
        var msg = text || defaultMsg;
        var url = base + '?text=' + encodeURIComponent(msg);
        window.open(url, '_blank', 'noopener,noreferrer');
    });
})();
</script>

<?php include('layouts/footer.php'); ?>
