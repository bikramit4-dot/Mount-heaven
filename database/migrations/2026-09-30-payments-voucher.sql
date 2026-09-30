-- Payment vouchers: parents can attach a screenshot/PDF of their payment receipt.

ALTER TABLE payments ADD COLUMN voucher VARCHAR(255) NULL AFTER transaction_ref;
