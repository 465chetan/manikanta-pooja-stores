<?php
// ============================================================
// SRI MANIKANTA POOJA STORES â€” NOTIFICATIONS
// Handles email + WhatsApp + in-app notifications
// ============================================================

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// â”€â”€ Email via PHPMailer (inline, no Composer required) â”€â”€â”€â”€â”€â”€â”€â”€
// Downloads PHPMailer as a single file alternative
function sendEmail(string $toEmail, string $toName, string $subject, string $htmlBody): bool {
    if (!MAIL_HOST || !MAIL_USERNAME) return false;

    // Use PHP's built-in mail() as fallback if PHPMailer not available
    // In production, set up PHPMailer via cPanel â†’ File Manager â†’ upload
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=utf-8',
        "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_EMAIL . ">",
        "Reply-To: " . MAIL_FROM_EMAIL,
        "X-Mailer: PHP/" . phpversion(),
    ];

    // Try PHPMailer if available (recommended)
    if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        return sendViaPhpMailer($toEmail, $toName, $subject, $htmlBody);
    }

    // Fallback: native mail() function (works on cPanel)
    return mail($toEmail, $subject, $htmlBody, implode("\r\n", $headers));
}

function sendViaPhpMailer(string $toEmail, string $toName, string $subject, string $htmlBody): bool {
    try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION;
        $mail->Port       = MAIL_PORT;
        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->addAddress($toEmail, $toName);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->send();
        return true;
    } catch (\Exception $e) {
        error_log('PHPMailer Error: ' . $e->getMessage());
        return false;
    }
}

// ── SMS OTP via Fast2SMS ────────────────────────────────────────
// Register free at https://www.fast2sms.com — get API key from Dashboard → Dev API
function sendSmsOTP(string $mobile, string $otp, string $name = ''): bool {
    if (!defined('SMS_API_KEY') || !SMS_API_KEY) {
        error_log('SMS_API_KEY not configured. OTP: ' . $otp . ' for ' . $mobile);
        return false;
    }

    // Clean mobile — only 10 digits
    $mobile = preg_replace('/\D/', '', $mobile);
    if (strlen($mobile) === 12) $mobile = substr($mobile, 2); // strip 91
    if (strlen($mobile) !== 10) return false;

    $message = "Your OTP for Sri Manikanta Pooja Stores password reset is: {$otp}. Valid for 10 minutes. Do not share this OTP with anyone.";

    $ch = curl_init('https://www.fast2sms.com/dev/bulkV2');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query([
            'authorization' => SMS_API_KEY,
            'route'         => 'q',      // quick SMS (plain text)
            'message'       => $message,
            'language'      => 'english',
            'flash'         => 0,
            'numbers'       => $mobile,
        ]),
        CURLOPT_HTTPHEADER => ['cache-control: no-cache'],
        CURLOPT_TIMEOUT    => 10,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $response = curl_exec($ch);
    $err      = curl_error($ch);
    curl_close($ch);

    if ($err) { error_log('SMS curl error: ' . $err); return false; }

    $res = json_decode($response, true);
    if (isset($res['return']) && $res['return'] === true) return true;

    error_log('Fast2SMS error: ' . $response);
    return false;
}



// â”€â”€ WhatsApp via Interakt API â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function sendWhatsApp(string $mobile, string $templateName, array $params = []): bool {
    if (!WHATSAPP_API_ENABLED || !WHATSAPP_API_KEY) return false;

    // Ensure mobile has country code
    $mobile = preg_replace('/\D/', '', $mobile);
    if (strlen($mobile) === 10) $mobile = '91' . $mobile;

    $payload = json_encode([
        'countryCode' => '+91',
        'phoneNumber' => substr($mobile, 2),
        'callbackData' => 'smps_notification',
        'type' => 'Template',
        'template' => [
            'name' => $templateName,
            'languageCode' => 'en',
            'bodyValues' => $params,
        ],
    ]);

    $ch = curl_init(WHATSAPP_API_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Basic ' . WHATSAPP_API_KEY,
        ],
        CURLOPT_TIMEOUT => 10,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $httpCode === 200;
}

// â”€â”€ In-App Notification (stored in DB) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function createNotification(int $userId, string $type, string $title, string $message, array $data = []): void {
    try {
        db()->prepare('INSERT INTO notifications (user_id, type, title, message, data) VALUES (?, ?, ?, ?, ?)')
           ->execute([$userId, $type, $title, $message, json_encode($data)]);
    } catch (\Throwable $e) {
        error_log('Notification error: ' . $e->getMessage());
    }
}

// â”€â”€ Order Notification Templates â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

function notifyNewOrder(array $order, array $customer, array $items): void {
    $orderNum = $order['order_number'];
    $total    = 'â‚¹' . number_format($order['total'], 2);

    // 1. Email to OWNER
    $itemsHtml = '';
    foreach ($items as $item) {
        $itemsHtml .= "<tr>
            <td style='padding:8px;border-bottom:1px solid #eee'>{$item['name']}" . ($item['variant'] ? " ({$item['variant']})" : '') . "</td>
            <td style='padding:8px;border-bottom:1px solid #eee;text-align:center'>{$item['qty']}</td>
            <td style='padding:8px;border-bottom:1px solid #eee;text-align:right'>â‚¹" . number_format($item['price'] * $item['qty'], 2) . "</td>
        </tr>";
    }

    $ownerEmail = getEmailTemplate('new_order_owner', [
        '{{ORDER_NUMBER}}' => $orderNum,
        '{{CUSTOMER_NAME}}' => htmlspecialchars($customer['full_name']),
        '{{CUSTOMER_MOBILE}}' => $customer['mobile'],
        '{{CUSTOMER_EMAIL}}' => $customer['email'] ?? 'N/A',
        '{{ITEMS_HTML}}'    => $itemsHtml,
        '{{SUBTOTAL}}'      => 'â‚¹' . number_format($order['subtotal'], 2),
        '{{DELIVERY}}'      => 'â‚¹' . number_format($order['delivery_charge'], 2),
        '{{TOTAL}}'         => $total,
        '{{PAYMENT_METHOD}}' => strtoupper($order['payment_method']),
        '{{ADMIN_URL}}'     => APP_URL . '/admin/orders.html?order=' . $order['id'],
    ]);
    sendEmail(OWNER_EMAIL, OWNER_NAME, "New Order $orderNum â€” " . APP_NAME, $ownerEmail);

    // 2. Email to CUSTOMER
    if (!empty($customer['email'])) {
        $customerEmail = getEmailTemplate('order_confirmed_customer', [
            '{{ORDER_NUMBER}}' => $orderNum,
            '{{CUSTOMER_NAME}}' => htmlspecialchars($customer['full_name']),
            '{{ITEMS_HTML}}'   => $itemsHtml,
            '{{TOTAL}}'        => $total,
            '{{PAYMENT_METHOD}}' => strtoupper($order['payment_method']),
            '{{DASHBOARD_URL}}' => APP_URL . '/dashboard/orders.html',
        ]);
        sendEmail($customer['email'], $customer['full_name'], "âœ… Order Confirmed: $orderNum", $customerEmail);
    }

    // 3. WhatsApp to owner (free wa.me link format stored, full API if enabled)
    sendWhatsApp(OWNER_WHATSAPP, 'new_order_owner', [
        $customer['full_name'],
        $orderNum,
        $total,
    ]);

    // 4. In-app notification to customer
    createNotification(
        $customer['id'],
        'order_placed',
        'Order Placed Successfully!',
        "Your order $orderNum has been placed. We'll confirm it shortly.",
        ['order_id' => $order['id'], 'order_number' => $orderNum]
    );
}

function notifyOrderStatusUpdate(array $order, array $customer): void {
    $orderNum = $order['order_number'];
    $status   = ucfirst($order['order_status']);

    $statusMessages = [
        'confirmed'  => "Great news! Your order $orderNum has been confirmed. We're preparing it for dispatch.",
        'processing' => "Your order $orderNum is now being processed and packed.",
        'shipped'    => "Your order $orderNum has been shipped! You'll receive it in 2-3 days.",
        'delivered'  => "Your order $orderNum has been delivered. Thank you for shopping with us!",
        'cancelled'  => "Your order $orderNum has been cancelled. If you paid online, refund will be processed in 3-5 days.",
    ];

    $msg = $statusMessages[$order['order_status']] ?? "Your order $orderNum status updated to: $status.";

    // Email to customer
    if (!empty($customer['email'])) {
        $html = getEmailTemplate('order_status_update', [
            '{{ORDER_NUMBER}}'  => $orderNum,
            '{{CUSTOMER_NAME}}' => htmlspecialchars($customer['full_name']),
            '{{STATUS}}'        => $status,
            '{{STATUS_MSG}}'    => $msg,
            '{{DASHBOARD_URL}}' => APP_URL . '/dashboard/orders.html',
        ]);
        sendEmail($customer['email'], $customer['full_name'], "Order $orderNum â€” Status: $status", $html);
    }

    // WhatsApp to customer
    sendWhatsApp($customer['mobile'], 'order_status_update', [
        $customer['full_name'],
        $orderNum,
        $status,
    ]);

    // In-app notification
    createNotification(
        $customer['id'],
        'order_status',
        "Order Status: $status",
        $msg,
        ['order_id' => $order['id'], 'order_number' => $orderNum, 'status' => $order['order_status']]
    );
}

// â”€â”€ Email HTML Templates â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function getEmailTemplate(string $name, array $vars = []): string {
    $baseStyle = 'font-family:Arial,sans-serif;max-width:600px;margin:0 auto;background:#fff';
    $headerBg  = '#7B1A1A'; // maroon
    $accentColor = '#FF7722'; // saffron

    $templates = [
        'new_order_owner' => '
        <div style="' . $baseStyle . '">
          <div style="background:' . $headerBg . ';padding:24px;text-align:center">
            <h1 style="color:#FFD700;margin:0;font-size:22px">New Order Received!</h1>
            <p style="color:#fff;margin:8px 0 0">{{ORDER_NUMBER}}</p>
          </div>
          <div style="padding:24px">
            <p style="font-size:16px">A new order has been placed on your website.</p>
            <table style="width:100%;border-collapse:collapse;margin:16px 0">
              <tr><td style="padding:8px;font-weight:bold;width:140px">Customer:</td><td style="padding:8px">{{CUSTOMER_NAME}}</td></tr>
              <tr style="background:#f9f9f9"><td style="padding:8px;font-weight:bold">Mobile:</td><td style="padding:8px">{{CUSTOMER_MOBILE}}</td></tr>
              <tr><td style="padding:8px;font-weight:bold">Email:</td><td style="padding:8px">{{CUSTOMER_EMAIL}}</td></tr>
              <tr style="background:#f9f9f9"><td style="padding:8px;font-weight:bold">Payment:</td><td style="padding:8px">{{PAYMENT_METHOD}}</td></tr>
            </table>
            <h3 style="color:' . $headerBg . '">Order Items</h3>
            <table style="width:100%;border-collapse:collapse">
              <thead><tr style="background:' . $headerBg . ';color:#fff">
                <th style="padding:10px;text-align:left">Product</th>
                <th style="padding:10px;text-align:center">Qty</th>
                <th style="padding:10px;text-align:right">Amount</th>
              </tr></thead>
              <tbody>{{ITEMS_HTML}}</tbody>
              <tfoot>
                <tr><td colspan="2" style="padding:8px;text-align:right">Subtotal:</td><td style="padding:8px;text-align:right">{{SUBTOTAL}}</td></tr>
                <tr><td colspan="2" style="padding:8px;text-align:right">Delivery:</td><td style="padding:8px;text-align:right">{{DELIVERY}}</td></tr>
                <tr style="font-weight:bold;font-size:18px;color:' . $headerBg . '"><td colspan="2" style="padding:8px;text-align:right">TOTAL:</td><td style="padding:8px;text-align:right">{{TOTAL}}</td></tr>
              </tfoot>
            </table>
            <div style="text-align:center;margin-top:24px">
              <a href="{{ADMIN_URL}}" style="background:' . $accentColor . ';color:#fff;padding:12px 32px;border-radius:6px;text-decoration:none;font-weight:bold">View in Admin Panel</a>
            </div>
          </div>
          <div style="background:#f5f5f5;padding:16px;text-align:center;font-size:12px;color:#777">
            Sri Manikanta Pooja Stores | Dilsukhnagar, Hyderabad
          </div>
        </div>',

        'order_confirmed_customer' => '
        <div style="' . $baseStyle . '">
          <div style="background:' . $headerBg . ';padding:24px;text-align:center">
            <h1 style="color:#FFD700;margin:0">Order Confirmed!</h1>
            <p style="color:#fff;margin:8px 0 0">{{ORDER_NUMBER}}</p>
          </div>
          <div style="padding:24px">
            <p style="font-size:16px">Dear <strong>{{CUSTOMER_NAME}}</strong>,</p>
            <p>Thank you for your order! We have received it and will confirm delivery details with you shortly.</p>
            <table style="width:100%;border-collapse:collapse;margin:16px 0">
              <thead><tr style="background:' . $headerBg . ';color:#fff">
                <th style="padding:10px;text-align:left">Product</th>
                <th style="padding:10px;text-align:center">Qty</th>
                <th style="padding:10px;text-align:right">Amount</th>
              </tr></thead>
              <tbody>{{ITEMS_HTML}}</tbody>
              <tfoot>
                <tr style="font-weight:bold;font-size:18px;color:' . $headerBg . '"><td colspan="2" style="padding:8px;text-align:right">TOTAL:</td><td style="padding:8px;text-align:right">{{TOTAL}}</td></tr>
              </tfoot>
            </table>
            <p style="font-size:14px">Payment Method: <strong>{{PAYMENT_METHOD}}</strong></p>
            <p style="font-size:14px">Need help? Call us: <strong>+91 91105 82086</strong></p>
            <div style="text-align:center;margin-top:24px">
              <a href="{{DASHBOARD_URL}}" style="background:' . $accentColor . ';color:#fff;padding:12px 32px;border-radius:6px;text-decoration:none;font-weight:bold">Track Your Order</a>
            </div>
          </div>
          <div style="background:#f5f5f5;padding:16px;text-align:center;font-size:12px;color:#777">
            Sri Manikanta Pooja Stores | Dilsukhnagar, Hyderabad | +91 91105 82086
          </div>
        </div>',

        'order_status_update' => '
        <div style="' . $baseStyle . '">
          <div style="background:' . $headerBg . ';padding:24px;text-align:center">
            <h1 style="color:#FFD700;margin:0">Order Update</h1>
            <p style="color:#fff;margin:8px 0 0">{{ORDER_NUMBER}} â€” {{STATUS}}</p>
          </div>
          <div style="padding:24px">
            <p style="font-size:16px">Dear <strong>{{CUSTOMER_NAME}}</strong>,</p>
            <p style="font-size:16px">{{STATUS_MSG}}</p>
            <div style="text-align:center;margin-top:24px">
              <a href="{{DASHBOARD_URL}}" style="background:' . $accentColor . ';color:#fff;padding:12px 32px;border-radius:6px;text-decoration:none;font-weight:bold">View Order Details</a>
            </div>
          </div>
          <div style="background:#f5f5f5;padding:16px;text-align:center;font-size:12px;color:#777">
            Sri Manikanta Pooja Stores | +91 91105 82086
          </div>
        </div>',

        'password_reset_otp' => '
        <div style="' . $baseStyle . '">
          <div style="background:' . $headerBg . ';padding:24px;text-align:center">
            <h1 style="color:#FFD700;margin:0">Password Reset OTP</h1>
          </div>
          <div style="padding:32px;text-align:center">
            <p style="font-size:16px">Dear <strong>{{CUSTOMER_NAME}}</strong>,</p>
            <p>Your OTP for password reset is:</p>
            <div style="font-size:48px;font-weight:bold;color:' . $headerBg . ';letter-spacing:12px;margin:24px 0">{{OTP}}</div>
            <p style="color:#777;font-size:14px">This OTP expires in 10 minutes.</p>
            <p style="color:#777;font-size:14px">If you did not request a password reset, please ignore this email.</p>
          </div>
        </div>',
    ];

    $template = $templates[$name] ?? '<p>{{STATUS_MSG}}</p>';
    foreach ($vars as $placeholder => $value) {
        $template = str_replace($placeholder, $value, $template);
    }
    return $template;
}

