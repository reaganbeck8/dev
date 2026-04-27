<!-- ─── CONTACT ────────────────────────────────────────────── -->
<section class="contact-page fade-in">
  <div class="container container-sm">
    <p class="section-label">Reach Out</p>
    <h2 class="section-title">Contact Us</h2>

    <form method="POST" action="<?= url('/contact/submit') ?>" class="contact-form">
      <?= \Core\Csrf::field() ?>

      <input type="text" name="name" placeholder="Your Name *" required>
      <input type="email" name="email" placeholder="Your Email *" required>
      <input type="tel" name="phone" placeholder="Phone (optional)">
      <textarea name="message" rows="6" placeholder="Your Message *" required></textarea>

      <button type="submit" class="btn-primary form-submit">Send Message</button>
    </form>
  </div>
</section>
