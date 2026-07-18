<?php
// ============================================================
// ONE-TIME MIGRATION: Import all products.js data into database
// DELETE this file after running it once!
// Access: https://manikantapoojastore.com/migrate_products.php
// ============================================================
require_once __DIR__ . '/api/config/config.php';
require_once __DIR__ . '/api/config/database.php';
require_once __DIR__ . '/api/helpers/response.php';

$pdo = db();
$log = [];

// --- STEP 0: Un-delete and activate ALL products so admin can see them ---
$restore = $pdo->prepare("UPDATE products SET is_deleted = 0, is_active = 1");
$restore->execute();
$log[] = "Restored " . $restore->rowCount() . " products to active state";

$restoreCat = $pdo->prepare("UPDATE categories SET is_active = 1");
$restoreCat->execute();
$log[] = "Restored " . $restoreCat->rowCount() . " categories to active state";

// --- STEP 1: Ensure all categories exist ---
$categories = [
    ['agarbatti',    'Agarbatti & Incense',   'అగర్బత్తి',       'images/cat_agarbatti.webp',  1],
    ['camphor',      'Camphor (Kapoor)',        'కర్పూరం',          'images/cat_camphor.webp',    2],
    ['kumkum',       'Kumkum & Haldi',          'కుంకుమ పసుపు',    'images/cat_kumkum.webp',     3],
    ['oils',         'Pooja Oils & Ghee',       'నూనె & నెయ్యి',   'images/cat_oils.webp',       4],
    ['diyas',        'Diyas & Lamps',           'దీపాలు',           'images/cat_diyas.webp',      5],
    ['photos',       'God Photos & Frames',     'దేవుడి ఫోటోలు',   'images/cat_photos.webp',     6],
    ['idols',        'Idols (Vigrahas)',         'విగ్రహాలు',        'images/cat_idols.webp',      7],
    ['thali',        'Puja Thali & Vessels',    'పూజా పాత్రలు',    'images/cat_thali.webp',      8],
    ['malas',        'Malas & Rosaries',        'మాలలు',            'images/cat_malas.webp',      9],
    ['havan',        'Havan Samagri',           'హవన్ సామగ్రి',    'images/cat_havan.webp',      10],
    ['festivals',    'Festival Kits',           'పండుగ కిట్లు',    'images/cat_festival.webp',   11],
    ['wedding',      'Wedding Items',           'వివాహ సామగ్రి',   'images/cat_wedding.webp',    12],
    ['dhoop-sticks', 'Dhoop Sticks',            '',                  'images/cat_dhoop.webp',      13],
    ['more-and-other','More and other',         '',                  'images/cat_other.webp',      14],
];

$catMap = []; // slug => id
foreach ($categories as [$slug, $name, $telugu, $img, $order]) {
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
    $stmt->execute([$slug]);
    $row = $stmt->fetch();
    if ($row) {
        $catMap[$slug] = (int)$row['id'];
        $log[] = "Category exists: $name (id={$row['id']})";
    } else {
        $ins = $pdo->prepare("INSERT INTO categories (name, telugu, slug, image, sort_order, is_active) VALUES (?,?,?,?,?,1)");
        $ins->execute([$name, $telugu, $slug, $img, $order]);
        $id = (int)$pdo->lastInsertId();
        $catMap[$slug] = $id;
        $log[] = "Category CREATED: $name (id=$id)";
    }
}

// --- STEP 2: All products from products.js ---
$products = [
    // AGARBATTI
    [5005,'agarbatti','Balaji Bindu Premium Incense Sticks','బాలాజీ బిందు ప్రీమియం అగర్బత్తి',70,90,'Balaji Bindu Premium Incense Sticks – by Balaji Since 1957. Zipper-lock pouch for extra freshness. Premium quality incense for a divine, long-lasting fragrance experience during pooja.','["images/1000090833.jpg"]','["Standard Pouch"]','popular',1,1,100],
    [5001,'agarbatti','Ambica Durbar Bathi','అంబికా దర్బార్ బత్తి',60,75,'Ambica Durbar Bathi – India\'s only Herbal Durbar Bathi. Hand rolled in India using a mix of 63 herbs. By ACP Industries Ltd., Eluru. A traditional herbal incense loved for generations.','["images/1000090829.jpg","images/1000090831.jpg","images/1000090830.jpg","images/1000090832.jpg"]','[{"name":"Herbal","price":60,"original_price":75,"stock":100,"image":"images/1000090829.jpg"},{"name":"75g","price":60,"original_price":75,"stock":100,"image":"images/1000090831.jpg"},{"name":"145g","price":70,"original_price":90,"stock":100,"image":"images/1000090830.jpg"},{"name":"New Pack","price":70,"original_price":85,"stock":100,"image":"images/1000090832.jpg"}]','popular',1,1,100],
    [5,   'agarbatti','Zed Black Luxury Fresh Pineapple Incense Sticks','జెడ్ బ్లాక్ లక్జరీ ఫ్రెష్ పైనాపిల్ అగర్బత్తి',70,90,'Zed Black Luxury Fresh Pineapple Premium Incense Sticks – charcoal-free incense with a refreshing, exotic pineapple fragrance. Premium luxury range for a truly uplifting prayer experience.','["images/1000090828.jpg"]','["Standard Pack"]',null,0,0,100],
    [4,   'agarbatti','Darshan Black Stone Incense Sticks','దర్శన్ బ్లాక్ స్టోన్ అగర్బత్తి',60,80,'Darshan Black Stone Incense Sticks – a unique, unmatched fragrance (Ek Anokhi Khushboo). Dark premium packaging with gold mandala design. Perfect for meditation and evening prayers.','["images/1000090827.jpg"]','["Standard Pack"]',null,0,1,100],
    [3,   'agarbatti','Darshan White Stone Incense Sticks','దర్శన్ వైట్ స్టోన్ అగర్బత్తి',60,80,'Darshan White Stone Incense Sticks – manufactured using enchanting perfumes in combination with natural oils to give power to your prayer and concentration for meditation.','["images/1000090826.jpg"]','["Standard Pack"]','new',0,1,100],
    [2,   'agarbatti','Balaji 100 Divine Agarbathi 4IN1','బాలాజీ 100 డివైన్ అగర్బత్తి',70,90,'Balaji 100 Divine Agarbathi – Super Strong Premium Fragrance 4IN1 blend. Since 1957, Balaji brings you the finest quality incense. Premium Gold range for a truly divine experience.','["images/1000090825.jpg"]','["Standard Pack"]','popular',0,1,100],
    [1,   'agarbatti','Zed Black 3-IN-1 Premium Agarbatti','జెడ్ బ్లాక్ 3-IN-1 అగర్బత్తి',60,80,'Zed Black Premium 3-IN-1 Agarbatti – a triple fragrance blend in one pack. Long-lasting divine scent for daily puja. Brand ambassador MS Dhoni. Comes with a free matchbox inside.','["images/1000090824.jpg"]','["Standard Pack"]','popular',0,1,100],
    // CAMPHOR
    [8,'camphor','Camphor Aarti Lamp Refill 90','కర్పూర్ ఆరతి',120,150,'Premium camphor cubes specially shaped for brass aarti lamps. Consistent size, pure grade camphor. Pack includes 24 large cubes for extended puja sessions.','["images/1000090831.jpg"]','[{"name":"Pack of 12","price":120,"original_price":150,"stock":100,"image":""},{"name":"Pack of 24","price":120,"original_price":150,"stock":100,"image":""}]','new',0,0,100],
    [7,'camphor','Camphor Powder (Loose)','కర్పూరం పొడి',55,70,'Fine camphor powder for havan and special rituals. Dissolves quickly and produces divine fragrance. Used in traditional Telugu and South Indian ceremonies.','["images/1000090830.jpg"]','["50g","100g","250g"]',null,0,0,100],
    // KUMKUM
    [10,'kumkum','Pure Haldi (Turmeric) Powder','పసుపు',30,42,'Bright yellow turmeric powder of highest purity. Used for tilak, haldi ceremony, warding off evil eye and skin care. Naturally sourced, pure grade quality.','["images/1000090833.jpg"]','["50g","100g","250g","500g","1kg"]',null,0,1,100],
    [9,'kumkum','Premium Red Kumkum','కుంకుమ',25,35,'Pure, bright red kumkum made from natural turmeric and lime. Fine texture, vibrant color, long-lasting. Essential for tilak, goddess worship and all religious ceremonies.','["images/1000090832.jpg"]','["25g","50g","100g","250g","500g"]','popular',0,1,100],
    // DIYAS
    [19,'diyas','Pancha Mukhi Deepam','పంచముఖి దీపం',450,599,'Five-faced brass deepam for special pujas and navaratri. Each face represents a different deity. Heavy antique-finish brass, premium craftsmanship.','["images/1000090832.jpg"]','["Medium","Large"]','new',0,0,100],
    [18,'diyas','Clay Diyas (Mitti Diya)','మట్టి దీపాలు',60,80,'Handmade earthen clay diyas for Diwali and all festivals. Pack of 20 small diyas. Made by traditional artisans. Eco-friendly, natural clay, auspicious orange color.','["images/1000090831.jpg"]','["Pack of 10","Pack of 20","Pack of 50","Pack of 100"]','popular',0,1,100],
    [17,'diyas','Brass Deepam (5-inch)','పితల దీపం',299,399,'Traditional 5-inch brass deepam for daily worship. Elegant design, heavy base, holds ghee or oil. Polished brass surface, tarnish resistant. Perfect for home puja room.','["images/1000090830.jpg"]','["Single","Set of 2","Set of 5"]','popular',0,1,100],
    // PHOTOS
    [24,'photos','Navgraha (9 Planets) Frame','నవగ్రహ దేవతలు',350,480,'All nine planet deities in one beautiful frame. Essential for navgraha puja and home altar. Premium print, gold border frame.','["images/1000090827.jpg"]','["8x10 inch","12x15 inch"]',null,0,0,100],
    [22,'photos','Goddess Lakshmi Photo Frame','లక్ష్మీదేవి ఫోటో',250,350,'Auspicious Goddess Lakshmi photo with golden border frame. High-resolution divine image, premium print on canvas board. Brings prosperity and blessings to your home.','["images/1000090825.jpg"]','["4x6 inch","6x8 inch","8x10 inch","12x15 inch"]',null,0,1,100],
    [21,'photos','Lord Ganesha Photo Frame','గణేశ ఫోటో ఫ్రేమ్',250,350,'Beautiful Lord Ganesha photo in premium wooden frame with glass. High-quality print with vibrant colors. Available in multiple sizes. Ideal for puja room, office and gifting.','["images/1000090824.jpg"]','["4x6 inch","6x8 inch","8x10 inch","12x15 inch"]','popular',0,1,100],
    // IDOLS
    [27,'idols','Saraswati Brass Idol','సరస్వతి విగ్రహం',899,1200,'Goddess Saraswati brass idol with Veena (musical instrument). Perfect for Saraswati Puja and Navratri. Fine craftsmanship, antique brass finish.','["images/1000090830.jpg"]','["4 inch","6 inch","8 inch"]',null,0,0,100],
    [26,'idols','Lakshmi Idol (Silver Plated)','లక్ష్మీదేవి విగ్రహం',1299,1699,'Beautiful silver-plated Goddess Lakshmi idol in standing posture. Exquisite craftsmanship, pure silver coating, elegant finish. Brings divine blessings and prosperity.','["images/1000090829.jpg"]','["3 inch","5 inch","7 inch"]','new',0,1,100],
    [25,'idols','Brass Ganesha Idol (4 inch)','గణేశ విగ్రహం',799,1099,'Hand-crafted pure brass Lord Ganesha idol, 4-inch seated Ganesha. Intricate detailing, smooth finish, auspicious idol for home temple. Ideal for gifting and installation.','["images/1000090828.jpg"]','["2 inch","4 inch","6 inch","8 inch"]','popular',0,1,100],
    // MALAS
    [33,'malas','Marigold Flower Garland','బంతి పూల మాల',50,70,'Artificial marigold flower garland for deity decoration. Premium quality silk-finish flowers, vibrant orange/yellow. Reusable, long-lasting, no maintenance required.','["images/1000090826.jpg"]','["2 ft","4 ft","6 ft"]',null,0,0,100],
    [32,'malas','Tulsi Mala (Holy Basil)','తులసి మాల',150,220,'Sacred Tulsi mala for Vishnu and Krishna japa. Made from genuine Vrindavan Tulsi beads. 108 + 1 beads, smooth finish, auspicious for daily chanting.','["images/1000090825.jpg"]','["Standard (108 beads)"]',null,0,0,100],
    [31,'malas','Rudraksha Mala (108 beads)','రుద్రాక్ష మాల',599,850,'Authentic 5-mukhi Rudraksha mala with 108 beads for japa and meditation. Energized and blessed. Genuine Indonesian rudraksha, knotted between each bead, silver guru bead.','["images/1000090824.jpg"]','["Small Bead (6mm)","Medium Bead (8mm)","Large Bead (10mm)"]','popular',0,1,100],
    // HAVAN
    [35,'havan','Copper Havan Kund','హవన్ కుండ్',899,1299,'Pure copper havan kund for performing Agni puja and homam at home. Traditional pyramid shape, durable copper, easy to clean, comes with stand.','["images/1000090828.jpg"]','[{"name":"Small (6 inch)","price":899,"original_price":1299,"stock":100,"image":""},{"name":"Medium (9 inch)","price":1199,"original_price":1699,"stock":100,"image":""},{"name":"Large (12 inch)","price":1599,"original_price":2199,"stock":100,"image":""}]',null,0,0,100],
    [34,'havan','Havan Samagri Mix (Premium)','హవన్ సామగ్రి',199,280,'Premium blend of 51 sacred herbs and materials for havan/homam. Includes sandalwood, ghee-soaked cotton wicks, guggul, camphor, and more. Rich fragrance, pure ingredients.','["images/1000090827.jpg"]','[{"name":"100g","price":199,"original_price":280,"stock":100,"image":""},{"name":"250g","price":349,"original_price":499,"stock":100,"image":""},{"name":"500g","price":599,"original_price":850,"stock":100,"image":""},{"name":"1kg","price":999,"original_price":1400,"stock":100,"image":""}]','popular',0,1,100],
    // FESTIVALS
    [38,'festivals','Ugadi Puja Kit','ఉగాది పూజా కిట్',349,499,'Complete Telugu New Year (Ugadi) kit with neem flowers, jaggery, mango pieces, tamarind, chilli and all 6 rasa (taste) items plus puja essentials.','["images/1000090831.jpg"]','["Small","Family Pack"]','new',0,0,100],
    [37,'festivals','Diwali Puja Complete Kit','దీపావళి పూజా కిట్',649,950,'Complete Diwali puja kit with: clay diyas (set of 20), camphor, Lakshmi idol, rangoli colors, kumkum, haldi, dhoop, flowers, mauli thread and puja booklet.','["images/1000090830.jpg"]','["Standard","Premium"]','popular',0,1,100],
    [36,'festivals','Vinayaka Chavithi Complete Kit','వినాయక చవితి కిట్',549,799,'All-in-one Ganesh Chaturthi kit: includes modak plate, 21-durva grass, red thread (janeu), dhoop, incense, kumkum, haldi, flowers, coconut and complete puja booklet in Telugu & English.','["images/1000090829.jpg"]','["Basic","Premium","Deluxe"]','popular',0,1,100],
    // WEDDING
    [41,'wedding','Mangalsutra Thread (Dhaarana)','మంగళసూత్రం దారం',150,220,'Traditional yellow mangalsutra dhaaranu thread with turmeric powder and kumkum. Pure cotton, sacred yellow thread for wedding and Varalakshmi puja.','["images/1000090826.jpg"]','["Standard","With Pendant Space"]',null,0,0,100],
    [40,'wedding','Kankana Dhara Thread Set','కంకణ ధారణ సెట్',299,420,'Sacred kankanam wrist thread for bride and groom. Yellow silk thread with turmeric, janeu sacred thread included. Traditional Telugu wedding ritual item.','["images/1000090825.jpg"]','["Standard Pack (2 sets)","Family Pack (5 sets)"]',null,0,0,100],
    [39,'wedding','Telugu Wedding Samagri Kit','వివాహ సామగ్రి కిట్',1499,2200,'Complete Telugu Brahmin wedding puja kit. Includes: coconut, beetle leaves & nuts, mango leaves, sacred thread, turmeric pieces, bangles, sacred cloth, kankanam thread, akshat, and all ritual items as per Telugu Shastras.','["images/1000090824.jpg"]','["Basic","Complete","Premium Deluxe"]','popular',0,1,100],
];

// --- STEP 3: Insert each product (skip if ID already exists) ---
$inserted = 0; $skipped = 0;
foreach ($products as $p) {
    [$id, $catSlug, $name, $telugu, $price, $origPrice, $desc, $images, $sizes, $badge, $isFeatured, $isActive, $stock] = $p;

    // Check if already exists by ID or name
    $chk = $pdo->prepare("SELECT id FROM products WHERE id = ? OR (name = ? AND is_deleted = 0)");
    $chk->execute([$id, $name]);
    if ($chk->fetch()) {
        $log[] = "SKIP (exists): $name";
        $skipped++;
        continue;
    }

    $catId = $catMap[$catSlug] ?? null;
    if (!$catId) { $log[] = "ERROR: No category for $catSlug"; continue; }

    $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
    $slug = trim($slug, '-') . '-' . $id;

    $stmt = $pdo->prepare("
        INSERT INTO products (id, category_id, name, telugu_name, slug, description, price, original_price, stock_qty, images, sizes, badge, is_featured, is_active, is_deleted, rating, review_count)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, 0)
        ON DUPLICATE KEY UPDATE
            category_id=VALUES(category_id), name=VALUES(name), telugu_name=VALUES(telugu_name),
            description=VALUES(description), price=VALUES(price), original_price=VALUES(original_price),
            stock_qty=VALUES(stock_qty), images=VALUES(images), sizes=VALUES(sizes),
            badge=VALUES(badge), is_featured=VALUES(is_featured), is_active=VALUES(is_active), is_deleted=0
    ");
    $stmt->execute([$id, $catId, $name, $telugu, $slug, $desc, $price, $origPrice, $stock, $images, $sizes, $badge, $isFeatured, $isActive]);
    $log[] = "INSERTED: $name (id=$id)";
    $inserted++;
}

// --- STEP 4: Regenerate products.js from DB ---
require_once __DIR__ . '/api/admin/helpers/regenerate_js.php';
regenerate_products_js();
$log[] = "products.js regenerated from DB!";

echo '<pre style="font-family:monospace;font-size:13px;background:#1a1a2e;color:#00ff88;padding:20px;border-radius:8px">';
echo "=== MIGRATION COMPLETE ===\n";
echo "Inserted: $inserted | Skipped: $skipped\n\n";
echo implode("\n", $log);
echo "\n\n✅ DONE! You can now delete this file.\n";
echo '</pre>';
