-- Fee payments: QR-based online fee payment feature.
-- Public Pay Fees page stores submissions here; admin manages them at /admin/payments.

CREATE TABLE IF NOT EXISTS payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(150) NOT NULL,
    class VARCHAR(60) NOT NULL,
    roll_no VARCHAR(30) NOT NULL,
    phone VARCHAR(40) NOT NULL,
    payment_for VARCHAR(120) NULL,
    amount DECIMAL(10,2) NULL,
    payment_method VARCHAR(30) NOT NULL DEFAULT 'qr',
    transaction_ref VARCHAR(80) NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings (`key`, `value`) VALUES
('payment_qr_image', ''),
('payment_instructions', 'Scan the QR code with any UPI app (GPay, PhonePe, Paytm), pay the amount, and enter the transaction reference number in the form so we can verify your payment.'),
('payment_qr_label', 'School UPI QR Code')
ON DUPLICATE KEY UPDATE `key` = VALUES(`key`);
