import sys, re

# Fix ALL html files for encoding corruption
# Pattern analysis:
# U+251C U+00F3 U+0393 U+00E9 U+00BC U+0393 U+00C7 U+00A3 = "â€œ" type -> em dash variation
# U+251C U+00F3 U+0393 U+00E9 U+00BC U+0393 U+00C7 U+00A5 = another variation
# U+251C U+00F3 U+0393 U+00DC U+252C U+2563 = â‚¹ (rupee)

# The corruption pattern is consistent: these come from
# UTF-8 bytes being misread as Latin1 then re-encoded
# Mapping table (exact unicode char sequences -> correct replacement):
FIXES = [
    # Rupee sign ₹ corrupted variants
    ('\u251c\u00f3\u0393\u00dc\u252c\u2563', '&#8377;'),  # ├óΓÜ┬╣ = ₹
    ('\u251c\u00f3\u0393\u00c7\u00dc\u252c\u2563', '&#8377;'),  # ├óΓÇÜ┬╣ = ₹ (with Ç)
    # Em dash — corrupted variants  
    ('\u251c\u00f3\u0393\u00e9\u00bc\u0393\u00c7\u00a3', '&mdash;'),  # ├óΓé¼ΓÇ£ = "
    ('\u251c\u00f3\u0393\u00e9\u00bc\u0393\u00c7\u00a5', '&mdash;'),  # ├óΓé¼ΓÇ¥ = —
    # Ellipsis … corrupted
    ('\u251c\u00f3\u0393\u00e9\u00bc\u0393\u00c7\u00a6', '&hellip;'),
    # Left double quote "
    ('\u251c\u00f3\u0393\u00e9\u00bc\u0393\u00c7\u0093', '&ldquo;'),
    # Right double quote "
    ('\u251c\u00f3\u0393\u00e9\u00bc\u0393\u00c7\u0094', '&rdquo;'),
    # Single right quote '
    ('\u251c\u00f3\u0393\u00e9\u00bc\u0393\u00c7\u0099', '&rsquo;'),
    # Generic fallback for any remaining ├óΓ sequences -> mdash
    ('\u251c\u00f3\u0393\u00e9\u00bc\u0393\u00c7', '&mdash;'),
    # Box drawing chars that shouldn't be in HTML
    ('\u253c', ' '),  # ┼
    # Also fix shop.html patterns (already fixed but double check)
    ('\u00e2\u20ac\u201d', '&mdash;'),   # â€" = —
    ('\u00e2\u20ac\u2122', '&rsquo;'),   # â€™ = '
    ('\u00e2\u20ac\u0153', '&ldquo;'),   # â€œ = "
    ('\u00e2\u20ac\u009d', '&rdquo;'),   # â€ = "
    ('\u00e2\u201a\u00b9', '&#8377;'),   # â‚¹ = ₹
    ('\u00e2\u20ac\u00a6', '&hellip;'),  # â€¦ = …
    ('\u00e2\u20ac\u02dc', '&lsquo;'),   # â€˜ = '
]

files = ['index.html', 'shop.html', 'product.html', 'cart.html']

for fname in files:
    try:
        with open(fname, 'r', encoding='utf-8') as f:
            content = f.read()
        original = content
        for bad, good in FIXES:
            content = content.replace(bad, good)
        if content != original:
            with open(fname, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f'FIXED: {fname}')
        else:
            print(f'OK (no changes): {fname}')
    except Exception as e:
        print(f'ERROR {fname}: {e}')
