<?php
require_once '../config/config.php';
include_once '../src/includes/header.php';

$contact_error = $_GET['error'] ?? '';
$contact_success = $_GET['success'] ?? '';
?>

    <section class="contact-section">
        <h2>Contact Us</h2>
        <?php if ($contact_error !== ''): ?>
            <p style="background:#f8d7da;color:#721c24;padding:10px;border-radius:6px;margin-bottom:12px;"><?php echo htmlspecialchars($contact_error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php elseif ($contact_success !== ''): ?>
            <p style="background:#d4edda;color:#155724;padding:10px;border-radius:6px;margin-bottom:12px;"><?php echo htmlspecialchars($contact_success, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <div class="contact-container">
            <div class="contact-info">
                <h3>Get in Touch</h3>
                <div class="info-item">
                    <strong>Email:</strong>
                    <p>info@nameeshopping.com</p>
                </div>
                <div class="info-item">
                    <strong>Phone:</strong>
                    <p>+1 (555) 123-4567</p>
                </div>
                <div class="info-item">
                    <strong>Address:</strong>
                    <p>123 Shopping Street, Commerce City, CC 12345</p>
                </div>
                <div class="info-item">
                    <strong>Business Hours:</strong>
                    <p>Monday - Friday: 9:00 AM - 6:00 PM</p>
                    <p>Saturday: 10:00 AM - 4:00 PM</p>
                    <p>Sunday: Closed</p>
                </div>
            </div>
            
            <form class="contact-form" id="contactForm" method="POST" action="../src/includes/process_contact.php">
                <h3>Send us a Message</h3>
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="subject">Subject:</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                <div class="form-group">
                    <label for="message">Message:</label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>
    </section>

<?php include_once '../src/includes/footer.php'; ?>
