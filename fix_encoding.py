import os

replacements = {
    'â‚¹': '₹',
    'â ³': '⏳',
    'ðŸ”': '🔍',
    'âœ…': '✅',
    'ðŸ“¦': '📦',
    'ðŸšš': '🚚',
    'ðŸŽ‰': '🎉',
    'â Œ': '❌',
    'âœ•': '✕',
    'ðŸ‘¤': '👤',
    'ðŸ“ ': '📍',
    'ðŸ“': '📌',
    'ðŸ’³': '💳',
    'ðŸ™ ': '🙏',
    'ðŸ™': '🙏',
    'ðŸ’°': '💰',
    'ðŸ’µ': '💵',
    'â °': '⏰',
    'â€”': '—',
    'ðŸ›’': '🛒',
    'ðŸ“ž': '📞',
    'ðŸŒ ': '🌐',
    'ðŸ’¬': '💬',
    'â­ ': '⭐',
    'â€¢': '•',
    'â€“': '–',
    'Ã—': '×',
    'à°…à°—à°°à± à°¬à°¤à± à°¤à°¿': 'అగర్బత్తి',
    'à°¤à±†à°²à± à°—à±  à°ªà±‡à°°à± ': 'తెలుగు పేరు',
    'âœ': '✔',
    'ðŸ“Œ': '📌'
}

def fix_file(filepath):
    if not os.path.exists(filepath):
        return
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    for bad, good in replacements.items():
        content = content.replace(bad, good)
        
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Fixed {filepath}")

for f in ['admin/orders.html', 'admin/products.html', 'admin/dashboard.html', 'admin/customers.html']:
    fix_file(f)
